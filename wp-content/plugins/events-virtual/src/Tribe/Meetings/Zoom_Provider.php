<?php
/**
 * Handles the registration of Zoom as a meetings provider.
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual\Meetings
 */

namespace Tribe\Events\Virtual\Meetings;

use Tribe\Events\Virtual\Integrations\Api_Response;
use Tribe\Events\Virtual\Meetings\Zoom\Actions;
use Tribe\Events\Virtual\Meetings\Zoom\Email;
use Tribe\Events\Virtual\Meetings\Zoom\Event_Export as Zoom_Event_Export;
use Tribe\Events\Virtual\Meetings\Zoom\Classic_Editor;
use Tribe\Events\Virtual\Meetings\Zoom\Migration;
use Tribe\Events\Virtual\Meetings\Zoom\Migration_Notice;
use Tribe\Events\Virtual\Event_Meta;
use Tribe\Events\Virtual\Meetings\Zoom\Event_Meta as Zoom_Meta;
use Tribe\Events\Virtual\Meetings\Zoom\Api;
use Tribe\Events\Virtual\Meetings\Zoom\Meetings;
use Tribe\Events\Virtual\Meetings\Zoom\Password;
use Tribe\Events\Virtual\Meetings\Zoom\Template_Modifications;
use Tribe\Events\Virtual\Meetings\Zoom\Users;
use Tribe\Events\Virtual\Meetings\Zoom\Webinars;
use Tribe\Events\Virtual\Plugin;
use Tribe\Events\Virtual\Traits\With_Nonce_Routes;
use Tribe__Events__Main as Events_Plugin;
use Tribe__Admin__Helpers as Admin_Helpers;
use WP_Post;

/**
 * Class Zoom_Provider
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual\Meetings
 */
class Zoom_Provider extends Meeting_Provider {
	use With_Nonce_Routes;

	/**
	 * The slug of this provider.
	 *
	 * @since 1.0.0
	 */
	const SLUG = 'zoom';

	/**
	 * {@inheritDoc}
	 */
	public function get_slug() {
		return self::SLUG;
	}

	/**
	 * Registers the bindings, actions and filters required by the Zoom API meetings provider to work.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		// Register this providers in the container to allow calls on it, e.g. to check if enabled.
		$this->container->singleton( 'events-virtual.meetings.zoom', self::class );
		$this->container->singleton( self::class, self::class );

		if ( ! $this->is_enabled() ) {
			return;
		}

		$this->add_actions();
		$this->add_filters();

		add_filter(
			'tribe_rest_event_data',
			$this->container->callback( Zoom_Meta::class, 'attach_rest_properties' ),
			10,
			2
		);

		$this->hook_templates();
		$this->enqueue_assets();

		/**
		 * Allows filtering of the capability required to use the Zoom integration ajax features.
		 *
		 * @since 1.6.0
		 *
		 * @param string $ajax_capability The capability required to use the ajax features, default manage_options.
		 */
		$ajax_capability = apply_filters( 'tribe_events_virtual_zoom_admin_ajax_capability', 'manage_options' );

		$this->route_admin_by_nonce( $this->admin_routes(), $ajax_capability );
	}

	/**
	 * Filters the fields in the Events > Settings > APIs tab to add the ones provided by the extension.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string,array> $fields The current fields.
	 *
	 * @return array<string,array> The fields, as updated by the settings.
	 */
	public function filter_addons_tab_fields( $fields ) {
		if ( ! is_array( $fields ) ) {
			return $fields;
		}

		return tribe( Zoom\Settings::class )->add_fields( $fields );
	}

	/**
	 * Renders the Zoom API link generation UI and controls, depending on the current state.
	 *
	 * @since 1.0.0
	 *
	 * @param string           $file        The path to the template file, unused.
	 * @param string           $entry_point The name of the template entry point, unused.
	 * @param \Tribe__Template $template    The current template instance.
	 */
	public function render_classic_meeting_link_ui( $file, $entry_point, \Tribe__Template $template ) {
		$this->container->make( Zoom\Classic_Editor::class )
						->render_initial_setup_options( $template->get( 'post' ) );
	}

	/**
	 * Filters the object returned by the `tribe_get_event` function to add to it properties related to Zoom meetings.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Post $event The events post object to be modified.
	 *
	 * @return \WP_Post The original event object decorated with properties related to virtual events.
	 */
	public function add_event_properties( $event ) {
		if ( ! $event instanceof \WP_Post ) {
			// We should only act on event posts, else bail.
			return $event;
		}

		return $this->container->make( Zoom_Meta::class )->add_event_properties( $event );
	}

	/**
	 * Filters the array returned for the event details map in the Event Automator integration.
	 *
	 * @since 1.13.5
	 *
	 * @param array<string|mixed> $next_event An array of event details.
	 * @param WP_Post             $event      An instance of the event WP_Post object.
	 *
	 * @return array<string|mixed> An array of event details.
	 */
	public function add_event_automator_properties( array $next_event, WP_Post $event ) {
		if ( ! $event instanceof WP_Post ) {
			return $next_event;
		}

		return $this->container->make( Zoom_Meta::class )->add_event_automator_properties( $next_event, $event );
	}

	/**
	 * Check Zoom Meeting in the admin.
	 *
	 * @since 1.0.4
	 * @since 1.9.0 - moved logic to Password->check_admin_zoom_meeting().
	 *
	 * @param WP_Post $event The event post object.
	 *
	 * @return bool|void Whether the update completed.
	 */
	public function check_admin_zoom_meeting( $event ) {
		return $this->container->make( Password::class )->check_admin_zoom_meeting( $event );
	}

	/**
	 * Check Zoom Meeting Account in the admin on every event for compatibility with multiple accounts.
	 *
	 * @since 1.5.0
	 *
	 * @param WP_Post $event The event post object.
	 *
	 * @return bool|WP_Post Whether the update completed or the WP Post instance if not an event.
	 */
	public function update_event_for_multiple_accounts_support( $event ) {
		if ( ! $event instanceof WP_Post ) {
			// We should only act on event posts, else bail.
			return $event;
		}

		return $this->container->make( Api::class )->update_event_for_multiple_accounts_support( $event );
	}

	/**
	 * Check Zoom Meeting on Front End.
	 *
	 * @since 1.0.4
	 * @since 1.9.0 - moved logic to Password->check_zoom_meeting().
	 *
	 * @return bool|void Whether the update completed.
	 */
	public function check_zoom_meeting() {
		return $this->container->make( Password::class )->check_zoom_meeting();
	}

	/**
	 * If there is no original account for Zoom, save the first one to use to update individual events.
	 *
	 * @since 1.9.0
	 *
	 * @param array<string,mixed>  An array of Accounts formatted for options dropdown.
	 * @param array<string|string> $account_data The array of data for an account to add to the list.
	 * @param string               $api_id       The id of the API in use.
	 */
	public function update_original_account( $accounts, $account_data, $app_id ) {
		$this->container->make( Migration::class )->update_original_account( $accounts, $account_data, $app_id );
	}

	/**
	 * Hooks the template required for the integration to work.
	 *
	 * @since 1.0.0
	 */
	protected function hook_templates() {
		// Metabox.
		add_action(
			'tribe_template_entry_point:events-virtual/admin-views/virtual-metabox/container/video-source:video_sources',
			[ $this, 'render_classic_meeting_link_ui' ],
			10,
			3
		);

		// Email Templates.
		add_filter(
			'tribe_events_virtual_ticket_email_template',
			[
				$this,
				'maybe_change_email_template',
			],
			10,
			2
		);

		// Event Single.
		add_action(
			'tribe_events_single_event_after_the_content',
			[ $this, 'action_add_event_single_zoom_details' ],
			15,
			0
		);

		// Event Single - Blocks.
		add_action( 'wp', [ $this, 'hook_block_template' ] );

		add_action(
			'tribe_events_virtual_block_content',
			[ $this, 'action_add_event_single_zoom_details' ],
			20,
			0
		);
	}

	/**
	 * Enqueues the assets required by the integration.
	 *
	 * @since 1.0.0
	 */
	protected function enqueue_assets() {
		$admin_helpers = Admin_Helpers::instance();

		tribe_asset(
			tribe( Plugin::class ),
			'tribe-events-virtual-api-admin-js',
			'events-virtual-api-admin.js',
			[ 'jquery', 'tribe-dropdowns' ],
			'admin_enqueue_scripts',
			[
				'conditionals' => [
					'operator' => 'OR',
					[ $admin_helpers, 'is_screen' ],
				],
				'localize' => [
					'name' => 'tribe_events_virtual_placeholder_strings',
					'data' => [
						'video'         => Event_Meta::get_video_source_text(),
					],
				],
			]
		);

		tribe_asset(
			tribe( Plugin::class ),
			'tribe-events-virtual-zoom-admin-style',
			'events-virtual-zoom-admin.css',
			[],
			'admin_enqueue_scripts',
			[
				'conditionals' => [
					'operator' => 'OR',
					[ $admin_helpers, 'is_screen' ],
				],
			]
		);

		tribe_asset(
			tribe( Plugin::class ),
			'tribe-events-virtual-api-settings-js',
			'events-virtual-api-settings.js',
			[ 'jquery' ],
			'admin_enqueue_scripts',
			[
				'conditionals' => [
					'operator' => 'OR',
					[ $admin_helpers, 'is_screen' ],
				],
			]
		);
	}

	/**
	 * Handles the save operations of the Classic Editor VE Metabox.
	 *
	 * @since 1.0.0
	 *
	 * @param int                 $post_id The post ID of the event currently being saved.
	 * @param array<string,mixed> $data    The data currently being saved.
	 */
	public function on_metabox_save( $post_id, $data ) {
		$post = get_post( $post_id );
		if ( ! $post instanceof \WP_Post && is_array( $data ) ) {
			return;
		}

		$this->container->make( Zoom_Meta::class )->save_metabox_data( $post_id, $data );
	}

	/**
	 * Handles updating Zoom meetings on post save.
	 *
	 * @since 1.0.2
	 *
	 * @param int     $post_id     The post ID.
	 * @param WP_Post $unused_post The post object.
	 * @param bool    $update      Whether this is an existing post being updated or not.
	 */
	public function on_post_save( $post_id, $unused_post, $update ) {
		if ( ! $update ) {
			return;
		}

		$event = tribe_get_event( $post_id );

		if ( ! $event instanceof \WP_Post || empty( $event->duration ) ) {
			// Hook for the Event meta save to try later in the save request, data might be there then.
			if ( ! doing_action( 'tribe_events_update_meta' ) ) {
				// But do no re-hook if we're acting on it.
				add_action( 'tribe_events_update_meta', [ $this, 'on_post_save' ], 100, 3 );
			}

			return;
		}

		// Handle the update with the correct handler, depending on the meeting type.
		if ( empty( $event->zoom_meeting_type ) || Webinars::$meeting_type !== $event->zoom_meeting_type ) {
			$meeting_handler = $this->container->make( Meetings::class );
		} else {
			$meeting_handler = $this->container->make( Webinars::class );
		}

		$meeting_handler->update( $event );
	}

	/**
	 * Conditionally inject content into ticket email templates.
	 *
	 * @since 1.0.0
	 * @since 1.13.0 - Moved functionality to Emall::Class.
	 *
	 * @param string $template The template path, relative to src/views.
	 * @param array  $args     The template arguments.
	 *
	 * @return string
	 */
	public function maybe_change_email_template( $template, $args ) {
		return $this->container->make( Email::class )->maybe_change_email_template( $template, $args );
	}

	/**
	 * Include the zoom details for event single.
	 *
	 * @since 1.0.0
	 */
	public function action_add_event_single_zoom_details() {
		// Don't show if requires log in and user isn't logged in.
		$base_modifications = $this->container->make( 'Tribe\Events\Virtual\Template_Modifications' );
		$should_show        = $base_modifications->should_show_virtual_content( tribe_get_Event( get_the_ID() ) );

		if ( ! $should_show ) {
			return;
		}

		$template_modifications = $this->container->make( Template_Modifications::class );
		$template_modifications->add_event_single_api_details();
	}

	/**
	 * Filters the password for the Zoom Meeting.
	 *
	 * @since 1.0.2
	 *
	 * @param null|string|int $password     The password for the Zoom meeting.
	 * @param array           $requirements An array of password requirements from Zoom.
	 */
	public function filter_zoom_password( $password, $requirements ) {
		return $this->container->make( Password::class )->filter_zoom_password( $password, $requirements );
	}

	/**
	 * Filters whether embed video control is hidden.
	 *
	 * @param boolean $is_hidden Whether the embed video control is hidden.
	 * @param WP_Post $event     The event object.
	 *
	 * @return boolean Whether the embed video control is hidden.
	 */
	public function filter_display_embed_video_hidden( $is_hidden, $event ) {
		if (
			! $event->virtual_meeting
			|| tribe( self::class )->get_slug() !== $event->virtual_meeting_provider
		) {
			return $is_hidden;
		}

		return true;
	}

	/**
	 * Get the list of Zoom ajax nonce actions.
	 *
	 * @since 1.5.0
	 *
	 * @return array<string,callable> A map from the nonce actions to the corresponding handlers.
	 */
	public function filter_virtual_meetings_zoom_ajax_actions() {
		return $this->admin_routes();
	}

	/**
	 * Provides the routes that should be used to handle Zoom API requests.
	 *
	 * The map returned by this method will be used by the `Tribe\Events\Virtual\Traits\With_Nonce_Routes` trait.
	 *
	 * @since 1.0.2.1 Added the method
	 * @since 1.0.4 Renamed the method to `admin_routes`.
	 *
	 * @return array<string,callable> A map from the nonce actions to the corresponding handlers.
	 */
	public function admin_routes() {
		$actions = tribe( Actions::class );

		return [
			$actions::$create_action          => $this->container->callback( Meetings::class, 'ajax_create' ),
			$actions::$remove_action          => $this->container->callback( Meetings::class, 'ajax_remove' ),
			$actions::$webinar_create_action  => $this->container->callback( Webinars::class, 'ajax_create' ),
			$actions::$webinar_remove_action  => $this->container->callback( Webinars::class, 'ajax_remove' ),
			$actions::$validate_user_action   => $this->container->callback( Users::class, 'validate_user' ),
			$actions::$authorize_nonce_action => $this->container->callback( API::class, 'handle_auth_request' ),
			$actions::$select_action          => $this->container->callback( Classic_Editor::class, 'ajax_selection' ),
			$actions::$status_action          => $this->container->callback( API::class, 'ajax_status' ),
			$actions::$delete_action          => $this->container->callback( API::class, 'ajax_delete' ),
		];
	}

	/**
	 * Add the Zoom Video Source.
	 *
	 * @since 1.6.0
	 *
	 * @param array<string|string> An array of video sources.
	 * @param \WP_Post $post       The current event post object, as decorated by the `tribe_get_event` function.
	 *
	 * @return array<string|mixed> An array of video sources.
	 */
	public function add_video_source( $video_sources, $post ) {

		$video_sources[] = [
			'text'     => _x( 'Zoom', 'The name of the video source.', 'events-virtual' ),
			'id'       => Zoom_Meta::$key_source_id,
			'value'    => Zoom_Meta::$key_source_id,
			'selected' => Zoom_Meta::$key_source_id === $post->virtual_video_source,
		];

		return $video_sources;
	}

	/**
	 * Filter the Google Calendar export fields for a Zoom source event.
	 *
	 * @since 1.7.3
	 * @since 1.8.0 add should_show parameter.
	 *
	 * @param array<string|string> $fields      The various file format components for this specific event.
	 * @param \WP_Post             $event       The WP_Post of this event.
	 * @param string               $key_name    The name of the array key to modify.
	 * @param string               $type        The name of the export type.
	 * @param boolean              $should_show Whether to modify the export fields for the current user, default to false.
	 *
	 * @return  array<string|string> Google Calendar Link params.
	 */
	public function filter_zoom_source_google_calendar_parameters( $fields, $event, $key_name, $type, $should_show ) {

		return $this->container->make( Zoom_Event_Export::class )->modify_video_source_export_output( $fields, $event, $key_name, $type, $should_show );
	}

	/**
	 * Filter the iCal export fields for a Zoom source event.
	 *
	 * @since 1.7.3
	 * @since 1.8.0 add should_show parameter.
	 *
	 * @param array<string|string> $fields      The various file format components for this specific event.
	 * @param \WP_Post             $event       The WP_Post of this event.
	 * @param string               $key_name    The name of the array key to modify.
	 * @param string               $type        The name of the export type.
	 * @param boolean              $should_show Whether to modify the export fields for the current user, default to false.
	 *
	 * @return array<string|string>  The various iCal file format components of this specific event item.
	 */
	public function filter_zoom_source_ical_feed_items( $fields, $event, $key_name, $type, $should_show ) {
		return $this->container->make( Zoom_Event_Export::class )->modify_video_source_export_output( $fields, $event, $key_name, $type, $should_show );
	}

	/**
	 * Filter the Outlook single event export url for a Zoom source event.
	 *
	 * @since 1.13.0
	 *
	 * @param string               $url             The url used to subscribe to a calendar in Outlook.
	 * @param string               $base_url        The base url used to subscribe in Outlook.
	 * @param array<string|string> $params          An array of parameters added to the base url.
	 * @param Outlook_Methods      $outlook_methods An instance of the link abstract.
	 * @param \WP_Post             $event           The WP_Post of this event.
	 * @param boolean              $should_show     Whether to modify the export fields for the current user, default to false.
	 *
	 * @return string The export url used to generate an Outlook event for the single event.
	 */
	public function filter_outlook_single_event_export_url_by_api( $url, $base_url, $params, $outlook_methods, $event, $should_show ) {
		return $this->container->make( Zoom_Event_Export::class )->filter_outlook_single_event_export_url_by_api( $url, $base_url, $params, $outlook_methods, $event, $should_show );
	}

	/**
	 * Add Zoom to Autodetect Source.
	 *
	 * @since 1.8.0
	 *
	 * @param array<string|string>        An array of autodetect sources.
	 * @param string   $autodetect_source The ID of the current selected video source.
	 * @param \WP_Post $post              The current event post object, as decorated by the `tribe_get_event` function.
	 *
	 * @return array<string|string> An array of video sources.
	 */
	public function add_autodetect_source( $autodetect_sources, $autodetect_source, $post ) {

		$autodetect_sources[] = [
			'text'     => _x( 'Zoom', 'The name of the autodetect source.', 'events-virtual' ),
			'id'       => Zoom_Meta::$key_source_id,
			'value'    => Zoom_Meta::$key_source_id,
			'selected' => Zoom_Meta::$key_source_id === $autodetect_source,
		];

		return $autodetect_sources;
	}

	/**
	 * Add the Zoom accounts dropdown field to the autodetect fields.
	 *
	 * @since 1.8.0
	 *
	 * @param array<string|mixed> $autodetect        An array of the autodetect resukts.
	 * @param string              $video_url         The url to use to autodetect the video source.
	 * @param string              $autodetect_source The optional name of the video source to attempt to autodetect.
	 * @param \WP_Post|null       $event             The event post object, as decorated by the `tribe_get_event` function.
	 * @param array<string|mixed> $ajax_data         An array of extra values that were sent by the ajax script.
	 *
	 * @return array<string|mixed> An array of the autodetect results.
	 */
	public function filter_virtual_autodetect_field_accounts( $autodetect_fields, $video_url, $autodetect_source, $event, $ajax_data ) {
		return $this->container->make( Classic_Editor::class )
		                ->classic_autodetect_video_source_accounts( $autodetect_fields, $video_url, $autodetect_source, $event, $ajax_data );
	}

	/**
	 * Filters the API error message.
	 *
	 * @since 1.11.0
	 *
	 * @param string              $api_message The API error message.
	 * @param array<string,mixed> $body        The json_decoded request body.
	 * @param Api_Response        $response    The response that will be returned. A non `null` value
	 *                                         here will short-circuit the response.
	 *
	 * @return string              $api_message        The API error message.
	 */
	public function filter_api_error_message( $api_message, $body, $response ) {
		return $this->container->make( Api::class )->filter_api_error_message( $api_message, $body, $response );
	}

	/**
	 * Hooks the filters required for the Zoom API integration to work correctly.
	 *
	 * @since 1.1.1
	 */
	protected function add_filters() {
		add_filter( 'tribe_addons_tab_fields', [ $this, 'filter_addons_tab_fields' ] );

		foreach ( [ Meetings::$meeting_type, Webinars::$meeting_type ] as $meeting_type ) {
			add_filter(
				"tribe_events_virtual_meetings_zoom_{$meeting_type}_password",
				[ $this, 'filter_zoom_password' ],
				10,
				2
			);
		}

		add_filter(
			'tribe_events_virtual_display_embed_video_hidden',
			[ $this, 'filter_display_embed_video_hidden' ],
			10,
			2
		);
		add_filter(
			'tribe_events_virtual_meetings_zoom_ajax_actions',
			[ $this, 'filter_virtual_meetings_zoom_ajax_actions' ]
		);
		add_filter( 'tribe_events_virtual_video_sources', [ $this, 'add_video_source' ], 20, 2 );
		add_filter( 'tec_events_virtual_export_fields', [ $this, 'filter_zoom_source_google_calendar_parameters' ], 10, 5 );
		add_filter( 'tec_events_virtual_export_fields', [ $this, 'filter_zoom_source_ical_feed_items' ], 10, 5 );
		add_filter( 'tec_events_virtual_outlook_single_event_export_url', [ $this, 'filter_outlook_single_event_export_url_by_api' ], 10, 6 );
		add_filter( 'tec_events_virtual_autodetect_video_sources', [ $this, 'add_autodetect_source' ], 20, 3 );
		add_filter( 'tec_events_virtual_video_source_autodetect_field_all', [ $this, 'filter_virtual_autodetect_field_accounts' ], 20, 5 );
		add_filter( 'tec_events_virtual_video_source_autodetect_field_zoom-accounts', [ $this, 'filter_virtual_autodetect_field_accounts' ], 20, 5 );
		add_filter( 'tec_events_virtual_meetings_api_error_message', [ $this, 'filter_api_error_message' ], 20, 3 );
	}

	/**
	 * Hooks the actions required for the Zoom API integration to work correctly.
	 *
	 * @since 1.1.1
	 */
	protected function add_actions() {
		// Filter event object properties to add the ones related to Zoom meetings for virtual events.
		add_action( 'tribe_events_virtual_add_event_properties', [ $this, 'add_event_properties' ] );
		add_action( 'add_meta_boxes_' . Events_Plugin::POSTTYPE, [ $this, 'check_admin_zoom_meeting' ] );
		add_action( 'add_meta_boxes_' . Events_Plugin::POSTTYPE, [ $this, 'update_event_for_multiple_accounts_support' ], 0 );
		add_action( 'wp', [ $this, 'check_zoom_meeting' ], 50 );
		add_action( 'tribe_events_virtual_metabox_save', [ $this, 'on_metabox_save' ], 10, 2 );
		add_action( 'save_post_tribe_events', [ $this, 'on_post_save' ], 100, 3 );
		add_action( 'tec_events_virtual_before_update_api_accounts', [ $this, 'update_original_account' ], 10, 3 );
		add_action( 'tec_virtual_automator_map_event_details', [ $this, 'add_event_automator_properties' ], 10, 2 );
	}

	/**
	 * Hook block templates - legacy or new VE block.
	 * Has to be postponed to `wp` action or later so global $post is available.
	 *
	 * @since 1.7.1
	 */
	public function hook_block_template() {
		/* The action/location which the template is injected depends on whether or not V2 is enabled
		 * and whether the virtual event block is present in the post content.
		 */
		$embed_inject_action = tribe( 'events-virtual.hooks' )->get_virtual_embed_action();

		add_action(
			$embed_inject_action,
			[ $this, 'action_add_event_single_zoom_details' ],
			20,
			0
		);
	}

	/**
	 * Render the Migration Notice to the New Zoom App.
	 *
	 * @since 1.4.0
	 * @deprecated 1.15.3 - No replacement.
	 */
	public function render_migration_notice() {
		_deprecated_function( __METHOD__, '1.15.3', 'No replacement.' );
		$this->container->make( Migration_Notice::class )->render();
	}
}
