<?php
/**
 * Handles the registration of Microsoft as a Meetings provider.
 *
 * @since 1.13.0
 *
 * @package Tribe\Events\Virtual\Meetings
 */

namespace Tribe\Events\Virtual\Meetings;

use Tribe\Events\Virtual\Meetings\Microsoft\Actions;
use Tribe\Events\Virtual\Meetings\Microsoft\Api;
use Tribe\Events\Virtual\Meetings\Microsoft\Classic_Editor;
use Tribe\Events\Virtual\Meetings\Microsoft\Email;
use Tribe\Events\Virtual\Meetings\Microsoft\Event_Meta as Microsoft_Meta;
use Tribe\Events\Virtual\Meetings\Microsoft\Event_Export as Microsoft_Event_Export;
use Tribe\Events\Virtual\Meetings\Microsoft\Meetings;
use Tribe\Events\Virtual\Traits\With_Nonce_Routes;
use Tribe\Events\Virtual\Meetings\Microsoft\Template_Modifications;
use WP_Post;

/**
 * Class Microsoft_Provider
 *
 * @since 1.13.0
 *
 * @package Tribe\Events\Virtual\Meetings
 */
class Microsoft_Provider extends Meeting_Provider {
	use With_Nonce_Routes;

	/**
	 * The slug of this provider.
	 *
	 * @since 1.13.0
	 */
	const SLUG = 'microsoft';

	/**
	 * {@inheritDoc}
	 */
	public function get_slug() {
		return self::SLUG;
	}

	/**
	 * Registers the bindings, actions and filters required by the Microsoft API meetings provider to work.
	 *
	 * @since 1.13.0
	 */
	public function register() {
		// Register this provider in the container to allow calls on it, e.g. to check if enabled.
		$this->container->singleton( 'events-virtual.meetings.microsoft', self::class );
		$this->container->singleton( self::class, self::class );

		if ( ! $this->is_enabled() ) {
			return;
		}

		$this->add_actions();
		$this->add_filters();
		$this->hook_templates();
		$this->route_admin_by_nonce( $this->admin_routes(), 'manage_options' );
	}

	/**
	 * Hooks the filters required for the Microsoft API integration to work correctly.
	 *
	 * @since 1.13.0
	 */
	protected function add_filters() {
		add_filter( 'tribe_addons_tab_fields', [ $this, 'filter_addons_tab_fields' ] );
		add_filter( 'tec_events_virtual_meetings_api_error_message', [ $this, 'filter_api_error_message' ], 10, 3 );
		add_filter( 'tribe_events_virtual_video_sources', [ $this, 'add_video_source' ], 20, 2 );
		add_filter( 'tec_events_virtual_autodetect_video_sources', [ $this, 'add_autodetect_source' ], 20, 3 );
		add_filter( 'tec_events_virtual_video_source_autodetect_field_all', [ $this, 'filter_virtual_autodetect_field_accounts' ], 20, 5 );
		add_filter( 'tec_events_virtual_video_source_autodetect_field_microsoft-accounts', [ $this, 'filter_virtual_autodetect_field_accounts' ], 20, 5 );

		add_filter(
			'tribe_rest_event_data',
			$this->container->callback( Microsoft_Meta::class, 'attach_rest_properties' ),
			10,
			2
		);

		add_filter( 'tribe_events_virtual_display_embed_video_hidden', [ $this, 'filter_display_embed_video_hidden' ], 10, 2 );
		add_filter( 'tec_events_virtual_export_fields', [ $this, 'filter_microsoft_source_microsoft_calendar_parameters' ], 10, 5 );
		add_filter( 'tec_events_virtual_export_fields', [ $this, 'filter_microsoft_source_ical_feed_items' ], 10, 5 );
		add_filter( 'tec_events_virtual_outlook_single_event_export_url', [ $this, 'filter_outlook_single_event_export_url_by_api' ], 10, 6 );
		add_action( 'tec_virtual_automator_map_event_details', [ $this, 'add_event_automator_properties' ], 10, 2 );
	}

	/**
	 * Filters the fields in the Events > Settings > APIs tab to add the ones provided by the extension.
	 *
	 * @since 1.13.0
	 *
	 * @param array<string,array> $fields The current fields.
	 *
	 * @return array<string,array> The fields, as updated by the settings.
	 */
	public function filter_addons_tab_fields( $fields ) {
		if ( ! is_array( $fields ) ) {
			return $fields;
		}

		return tribe( Microsoft\Settings::class )->add_fields( $fields );
	}

	/**
	 * Filters the API error message.
	 *
	 * @since 1.13.0
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
	 * Add the Microsoft Team Video Source.
	 *
	 * @since 1.13.0
	 *
	 * @param array<string|string> An array of video sources.
	 * @param WP_Post $post       The current event post object, as decorated by the `tribe_get_event` function.
	 *
	 * @return array<string|mixed> An array of video sources.
	 */
	public function add_video_source( $video_sources, $post ) {

		$video_sources[] = [
			'text'     => _x( 'Microsoft (Teams and Skype)', 'The name of the video source.', 'events-virtual' ),
			'id'       => Microsoft_Meta::$key_source_id,
			'value'    => Microsoft_Meta::$key_source_id,
			'selected' => Microsoft_Meta::$key_source_id === $post->virtual_video_source,
		];

		return $video_sources;
	}

	/**
	 * Add Microsoft Teams to Autodetect Source.
	 *
	 * @since 1.13.0
	 *
	 * @param array<string|string>        An array of autodetect sources.
	 * @param string  $autodetect_source The ID of the current selected video source.
	 * @param WP_Post $post              The current event post object, as decorated by the `tribe_get_event` function.
	 *
	 * @return array<string|string> An array of video sources.
	 */
	public function add_autodetect_source( $autodetect_sources, $autodetect_source, $post ) {

		$autodetect_sources[] = [
			'text'     => _x( 'Microsoft (Teams and Skype)', 'The name of the autodetect source.', 'events-virtual' ),
			'id'       => Microsoft_Meta::$key_source_id,
			'value'    => Microsoft_Meta::$key_source_id,
			'selected' => Microsoft_Meta::$key_source_id === $autodetect_source,
		];

		return $autodetect_sources;
	}

	/**
	 * Add the Microsoft accounts dropdown field to the autodetect fields.
	 *
	 * @since 1.13.0
	 *
	 * @param array<string|mixed> $autodetect        An array of the autodetect resukts.
	 * @param string              $video_url         The url to use to autodetect the video source.
	 * @param string              $autodetect_source The optional name of the video source to attempt to autodetect.
	 * @param WP_Post|null        $event             The event post object, as decorated by the `tribe_get_event` function.
	 * @param array<string|mixed> $ajax_data         An array of extra values that were sent by the ajax script.
	 *
	 * @return array<string|mixed> An array of the autodetect results.
	 */
	public function filter_virtual_autodetect_field_accounts( $autodetect_fields, $video_url, $autodetect_source, $event, $ajax_data ) {
		return $this->container->make( Classic_Editor::class )->classic_autodetect_video_source_accounts( $autodetect_fields, $video_url, $autodetect_source, $event, $ajax_data );
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
			! $event->virtual_meeting ||
			tribe( self::class )->get_slug() !== $event->virtual_meeting_provider
		) {
			return $is_hidden;
		}

		return true;
	}

	/**
	 * Filter the Microsoft Calendar export fields for a Microsoft source event.
	 *
	 * @since 1.13.0
	 *
	 * @param array<string|string> $fields      The various file format components for this specific event.
	 * @param WP_Post             $event       The WP_Post of this event.
	 * @param string               $key_name    The name of the array key to modify.
	 * @param string               $type        The name of the export type.
	 * @param boolean              $should_show Whether to modify the export fields for the current user, default to false.
	 *
	 * @return  array<string|string> Microsoft Calendar Link params.
	 */
	public function filter_microsoft_source_microsoft_calendar_parameters( $fields, $event, $key_name, $type, $should_show ) {
		return $this->container->make( Microsoft_Event_Export::class )->modify_video_source_export_output( $fields, $event, $key_name, $type, $should_show );
	}

	/**
	 * Filter the iCal export fields for a Microsoft source event.
	 *
	 * @since 1.13.0
	 *
	 * @param array<string|string> $fields      The various file format components for this specific event.
	 * @param WP_Post             $event       The WP_Post of this event.
	 * @param string               $key_name    The name of the array key to modify.
	 * @param string               $type        The name of the export type.
	 * @param boolean              $should_show Whether to modify the export fields for the current user, default to false.
	 *
	 * @return array<string|string>  The various iCal file format components of this specific event item.
	 */
	public function filter_microsoft_source_ical_feed_items( $fields, $event, $key_name, $type, $should_show ) {
		return $this->container->make( Microsoft_Event_Export::class )->modify_video_source_export_output( $fields, $event, $key_name, $type, $should_show );
	}

	/**
	 * Filter the Outlook single event export url for a Webex source event.
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
	 * @return array<string|string>  The various iCal file format components of this specific event item.
	 */
	public function filter_outlook_single_event_export_url_by_api( $url, $base_url, $params, $outlook_methods, $event, $should_show ) {
		return $this->container->make( Microsoft_Event_Export::class )->filter_outlook_single_event_export_url_by_api( $url, $base_url, $params, $outlook_methods, $event, $should_show );
	}

	/**
	 * Provides the routes that should be used to handle Microsoft API requests.
	 *
	 * The map returned by this method will be used by the `Tribe\Events\Virtual\Traits\With_Nonce_Routes` trait.
	 *
	 * @since 1.13.0
	 *
	 * @return array<string,callable> A map from the nonce actions to the corresponding handlers.
	 */

	public function admin_routes() {
		$actions = tribe( Actions::class );

		return [
			$actions::$authorize_nonce_action => $this->container->callback( Api::class, 'handle_auth_request' ),
			$actions::$status_action          => $this->container->callback( Api::class, 'ajax_status' ),
			$actions::$delete_action          => $this->container->callback( Api::class, 'ajax_delete' ),
			$actions::$select_action          => $this->container->callback( Classic_Editor::class, 'ajax_selection' ),
			$actions::$create_action          => $this->container->callback( Meetings::class, 'ajax_create' ),
			$actions::$remove_action          => $this->container->callback( Meetings::class, 'ajax_remove' ),
		];
	}

	/**
	 * Hooks the actions required for the Microsoft API integration to work correctly.
	 *
	 * @since 1.13.0
	 */
	protected function add_actions() {
		// Filter event object properties to add the ones related to Microsoft Meet for virtual events.
		add_action( 'tribe_events_virtual_add_event_properties', [ $this, 'add_event_properties' ] );
		add_action( 'tribe_events_virtual_metabox_save', [ $this, 'on_metabox_save' ], 10, 2 );
		add_action( 'save_post_tribe_events', [ $this, 'on_post_save' ], 100, 3 );
	}

	/**
	 * Filters the object returned by the `tribe_get_event` function to add to it properties related to Microsoft Meet.
	 *
	 * @since 1.13.0
	 *
	 * @param WP_Post $event The events post object to be modified.
	 *
	 * @return WP_Post The original event object decorated with properties related to virtual events.
	 */
	public function add_event_properties( $event ) {
		if ( ! $event instanceof WP_Post ) {
			// We should only act on event posts, else bail.
			return $event;
		}

		return $this->container->make( Microsoft_Meta::class )->add_event_properties( $event );
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

		return $this->container->make( Microsoft_Meta::class )->add_event_automator_properties( $next_event, $event );
	}

	/**
	 * Handles the save operations of the Classic Editor VE Metabox.
	 *
	 * @since 1.13.0
	 *
	 * @param int                 $post_id The post ID of the event currently being saved.
	 * @param array<string,mixed> $data    The data currently being saved.
	 */
	public function on_metabox_save( $post_id, $data ) {
		$post = get_post( $post_id );
		if ( ! $post instanceof WP_Post && is_array( $data ) ) {
			return;
		}

		$this->container->make( Microsoft_Meta::class )->save_metabox_data( $post_id, $data );
	}

	/**
	 * Handles updating Microsoft Meet on post save.
	 *
	 * @since 1.13.0
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

		if ( ! $event instanceof WP_Post || empty( $event->duration ) ) {
			// Hook for the Event meta save to try later in the save request, data might be there then.
			if ( ! doing_action( 'tribe_events_update_meta' ) ) {
				// But do no re-hook if we're acting on it.
				add_action( 'tribe_events_update_meta', [ $this, 'on_post_save' ], 100, 3 );
			}

			return;
		}

		$meeting_handler = $this->container->make( Meetings::class );

		$meeting_handler->update( $event );
	}


	/**
	 * Hooks the template required for the integration to work.
	 *
	 * @since 1.13.0
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
			[ $this, 'action_add_event_single_microsoft_details' ],
			15,
			0
		);

		// Event Single - Blocks.
		add_action( 'wp', [ $this, 'hook_block_template' ] );

		// The location which the template is injected depends on whether or not V2 is enabled.
		$microsoft_details_inject_action = tribe_events_single_view_v2_is_enabled() ? 'tribe_events_virtual_block_content' : 'tribe_template_after_include:events/blocks/event-datetime';

		add_action(
			$microsoft_details_inject_action,
			[ $this, 'action_add_event_single_microsoft_details' ],
			20,
			0
		);
	}

	/**
	 * Renders the Microsoft API link generation UI and controls, depending on the current state.
	 *
	 * @since 1.13.0
	 *
	 * @param string           $file        The path to the template file, unused.
	 * @param string           $entry_point The name of the template entry point, unused.
	 * @param \Tribe__Template $template    The current template instance.
	 */
	public function render_classic_meeting_link_ui( $file, $entry_point, \Tribe__Template $template ) {
		$this->container->make( Microsoft\Classic_Editor::class )
						->render_initial_setup_options( $template->get( 'post' ) );
	}

	/**
	 * Conditionally inject content into ticket email templates.
	 *
	 * @since 1.13.0
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
	 * Hook block templates - legacy or new VE block.
	 * Has to be postponed to `wp` action or later so global $post is available.
	 *
	 * @since 1.13.0
	 */
	public function hook_block_template() {
		/* The action/location which the template is injected depends on whether or not V2 is enabled
		 * and whether the virtual event block is present in the post content.
		 */
		$embed_inject_action = tribe( 'events-virtual.hooks' )->get_virtual_embed_action();

		add_action(
			$embed_inject_action,
			[ $this, 'action_add_event_single_microsoft_details' ],
			20,
			0
		);
	}

	/**
	 * Include the Microsoft details for event single.
	 *
	 * @since 1.13.0
	 */
	public function action_add_event_single_microsoft_details() {
		// Don't show if requires log in and user isn't logged in.
		$base_modifications = $this->container->make( 'Tribe\Events\Virtual\Template_Modifications' );
		$should_show        = $base_modifications->should_show_virtual_content( tribe_get_Event( get_the_ID() ) );

		if ( ! $should_show ) {
			return;
		}

		$template_modifications = $this->container->make( Template_Modifications::class );
		$template_modifications->add_event_single_api_details();
	}
}
