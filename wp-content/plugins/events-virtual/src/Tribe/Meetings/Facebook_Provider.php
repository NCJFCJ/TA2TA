<?php
/**
 * Handles the registration of Facebook Live as a meetings provider.
 *
 * @since   1.7.0
 *
 * @package Tribe\Events\Virtual\Meetings
 */

namespace Tribe\Events\Virtual\Meetings;

use Tribe\Events\Virtual\Assets;
use Tribe\Events\Virtual\Meetings\Facebook\Classic_Editor;
use Tribe\Events\Virtual\Meetings\Facebook\Page_API;
use Tribe\Events\Virtual\Meetings\Facebook\Settings;
use Tribe\Events\Virtual\Meetings\Facebook\Event_Export as Facebook_Event_Export;
use Tribe\Events\Virtual\Meetings\Facebook\Event_Meta as Facebook_Meta;
use Tribe\Events\Virtual\Meetings\Facebook\Template_Modifications;
use Tribe\Events\Virtual\Plugin;
use Tribe\Events\Virtual\Traits\With_Nonce_Routes;
use Tribe__Admin__Helpers as Admin_Helpers;
use WP_Post;

/**
 * Class Facebook_Provider
 *
 * @since   1.7.0
 *
 * @package Tribe\Events\Virtual\Meetings
 */
class Facebook_Provider extends Meeting_Provider {

	use With_Nonce_Routes;

	/**
	 * The slug of this provider.
	 *
	 * @since 1.7.0
	 */
	const SLUG = 'facebook-live';

	/**
	 * {@inheritDoc}
	 */
	public function get_slug() {
		return self::SLUG;
	}

	/**
	 * Registers the bindings, actions and filters required by the Facebook Live API meetings provider to work.
	 *
	 * @since 1.7.0
	 */
	public function register() {
		// Register this providers in the container to allow calls on it, e.g. to check if enabled.
		$this->container->singleton( 'events-virtual.meetings.facebook', self::class );
		$this->container->singleton( self::class, self::class );

		if ( ! $this->is_enabled() ) {
			return;
		}

		$this->add_actions();
		$this->hook_templates();
		$this->add_filters();
		$this->enqueue_admin_assets();
		$this->enqueue_frontend_assets();

		/**
		 * Allows filtering of the capability required to use the Facebook integration ajax features.
		 *
		 * @since 1.7.0
		 *
		 * @param string $ajax_capability The capability required to use the ajax features, default manage_options.
		 */
		$ajax_capability = apply_filters( 'tribe_events_virtual_facebook_admin_ajax_capability', 'manage_options' );

		$this->route_admin_by_nonce( $this->admin_routes(), $ajax_capability );
	}

	/**
	 * Hooks the actions required for the Facebook Live API integration to work correctly.
	 *
	 * @since 1.7.0
	 */
	protected function add_actions() {
		add_action( 'tribe_events_virtual_add_event_properties', [ $this, 'add_event_properties' ] );
		add_action( 'tribe_events_virtual_metabox_save', [ $this, 'on_metabox_save' ], 10, 2 );
	}

	/**
	 * Hooks the actions required for the Facebook Live API integration to work correctly.
	 *
	 * @since 1.7.0
	 */
	protected function hook_templates() {
		// Metabox.
		add_action(
			'tribe_template_entry_point:events-virtual/admin-views/virtual-metabox/container/video-source:video_sources',
			[ $this, 'render_classic_setup_options' ],
			10,
			3
		);

		// Single non-block Event FE.
		add_action(
			'tribe_events_single_event_after_the_content',
			[ $this, 'action_add_event_single_facebook_embed' ],
			15,
			0
		);

		// Single Event Block.
		add_action( 'wp', [ $this, 'hook_block_template' ] );
	}

	/**
	 * Hooks the filters required for the Facebook Live API integration to work correctly.
	 *
	 * @since 1.7.0
	 */
	protected function add_filters() {
		add_filter( 'tribe_addons_tab_fields', [ $this, 'filter_addons_tab_fields' ], 20 );
		add_filter( 'tribe_events_virtual_video_sources', [ $this, 'add_video_source' ], 10, 2 );
		add_filter( 'tec_events_virtual_export_fields', [ $this, 'filter_facebook_source_google_calendar_parameters' ], 10, 5 );
		add_filter( 'tec_events_virtual_export_fields', [ $this, 'filter_facebook_source_ical_feed_items' ], 10, 5 );
		add_filter( 'tec_events_virtual_autodetect_video_sources', [ $this, 'add_autodetect_source' ], 20, 3 );
		add_filter( 'tec_events_virtual_video_source_autodetect_field_all', [ $this, 'filter_virtual_autodetect_field_accounts' ], 20, 5 );
		add_filter( 'tec_events_virtual_video_source_autodetect_field_zoom-accounts', [ $this, 'filter_virtual_autodetect_field_accounts' ], 20, 5 );
		add_filter( 'tribe_rest_event_data', [ $this, 'attach_rest_properties' ], 10, 2 );

		// Filter event object properties to add Facebook Live Status
		add_filter( 'tribe_get_event_after', [ $this, 'add_dynamic_properties' ], 15 );

		// Filter the ticket email virtual url.
		add_filter( 'tribe_events_virtual_ticket_email_url', [ $this, 'filter_ticket_email_url' ], 15, 2 );
		add_filter( 'tec_events_virtual_ticket_email_url', [ $this, 'filter_ticket_email_url' ], 15, 2 );
		add_action( 'tec_virtual_automator_map_event_details', [ $this, 'add_event_automator_properties' ], 10, 2 );
	}

	/**
	 * Filters the object returned by the `tribe_get_event` function to add to it properties related to Facebook.
	 *
	 * @since 1.7.0
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

		return $this->container->make( Facebook_Meta::class )->add_event_properties( $event );
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

		return $this->container->make( Facebook_Meta::class )->add_event_automator_properties( $next_event, $event );
	}

	/**
	 * Handles the save operations of the Classic Editor VE Metabox.
	 *
	 * @since 1.7.0
	 *
	 * @param int                 $post_id The post ID of the event currently being saved.
	 * @param array<string,mixed> $data    The data currently being saved.
	 */
	public function on_metabox_save( $post_id, $data ) {
		$post = get_post( $post_id );
		if ( ! $post instanceof \WP_Post && is_array( $data ) ) {
			return;
		}

		$this->container->make( Facebook_Meta::class )->save_metabox_data( $post_id, $data );
	}

	/**
	 * Renders the Facebook Live Integration Fields.
	 *
	 * @since 1.7.0
	 *
	 * @param string           $file        The path to the template file, unused.
	 * @param string           $entry_point The name of the template entry point, unused.
	 * @param \Tribe__Template $template    The current template instance.
	 */
	public function render_classic_setup_options( $file, $entry_point, \Tribe__Template $template ) {
		$this->container->make( Classic_Editor::class )
		                ->render_setup_options( $template->get( 'post' ) );
	}

	/**
	 * Include the Facebook embed for event single.
	 *
	 * @since 1.7.0
	 */
	public function action_add_event_single_facebook_embed() {
		$this->container->make( Template_Modifications::class )
						->add_facebook_video_embed();
	}

	/**
	 * Filters the fields in the Events > Settings > APIs tab to add the ones provided by the extension.
	 *
	 * @since 1.7.0
	 *
	 * @param array<string,array> $fields The current fields.
	 *
	 * @return array<string,array> The fields, as updated by the settings.
	 */
	public function filter_addons_tab_fields( $fields ) {
		if ( ! is_array( $fields ) ) {
			return $fields;
		}

		return tribe( Facebook\Settings::class )->add_fields( $fields );
	}

	/**
	 * Add the Facebook Live Video Source.
	 *
	 * @since 1.7.0
	 *
	 * @param array<string|string> An array of video sources.
	 * @param \WP_Post $post       The current event post object, as decorated by the `tribe_get_event` function.
	 *
	 * @return array<string|mixed> An array of video sources.
	 */
	public function add_video_source( $video_sources, $post ) {

		$video_sources[] = [
			'text'     => _x( 'Facebook Live', 'The name of the video source.', 'events-virtual' ),
			'id'       => Facebook_Meta::$video_source_fb_id,
			'value'    => Facebook_Meta::$video_source_fb_id,
			'selected' => Facebook_Meta::$video_source_fb_id === $post->virtual_video_source,
		];

		return $video_sources;
	}

	/**
	 * Filter the Google Calendar export fields for a Facebook Live source event.
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
	public function filter_facebook_source_google_calendar_parameters( $fields, $event, $key_name, $type, $should_show ) {

		return $this->container->make( Facebook_Event_Export::class )->modify_video_source_export_output( $fields, $event, $key_name, $type, $should_show );
	}

	/**
	 * Filter the iCal export fields for a Facebook Live source event.
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
	public function filter_facebook_source_ical_feed_items( $fields, $event, $key_name, $type, $should_show ) {
		return $this->container->make( Facebook_Event_Export::class )->modify_video_source_export_output( $fields, $event, $key_name, $type, $should_show );
	}

	/**
	 * Add Facebook Video to Autodetect Source.
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
			'text'     => _x( 'Facebook Video', 'The name of the autodetect source.', 'events-virtual' ),
			'id'       => Facebook_Meta::$autodetect_fb_video_id,
			'value'    => Facebook_Meta::$autodetect_fb_video_id,
			'selected' => Facebook_Meta::$autodetect_fb_video_id === $autodetect_source,
		];

		return $autodetect_sources;
	}

	/**
	 * Add the Facebook video message field to the autodetect fields.
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
		                ->classic_autodetect_video_source_message( $autodetect_fields, $video_url, $autodetect_source, $event, $ajax_data );
	}

	/**
	 * Add information about the Facebook live stream if available via the REST Api.
	 *
	 * @since 1.7.0
	 *
	 * @param array<string,mixed> $data  The current data of the event.
	 * @param \WP_Post            $event The event being updated.
	 *
	 * @return array<string,mixed> An array with the data of the event on the endpoint.
	 */
	public function attach_rest_properties( array $data, \WP_Post $event ) {
		return tribe( Facebook_Meta::class )->attach_rest_properties( $data, $event );
	}

	/**
	 * Adds dynamic, time-related, properties to the event object.
	 *
	 * This method deals with properties we set, for convenience, on the event object that should not
	 * be cached as they are time-dependent; i.e. the time the properties are computed at matters and
	 * caching their values would be incorrect.
	 *
	 * @since 1.7.0
	 *
	 * @param mixed|WP_Post $post The event post object, as read from the cache, if any.
	 *
	 * @return WP_Post The decorated event post object; its dynamic and time-dependent properties correctly set up.
	 */
	public function add_dynamic_properties( $post ) {
		if ( ! $post instanceof WP_Post ) {
			// We should only act on event posts, else bail.
			return $post;
		}

		return $this->container->make( Facebook_Meta::class )->add_dynamic_properties( $post );
	}

	/**
	 * Filter the ticket email url.
	 *
	 * @since 1.7.2
	 *
	 * @param string  $virtual_url The virtual url for the ticket and rsvp emails.
	 * @param WP_Post $event       The event post object with properties added by the `tribe_get_event` function.
	 *
	 * @return string The Facebook Live virtual url for the ticket and rsvp emails.
	 */
	public function filter_ticket_email_url( $virtual_url, WP_Post $event ) {
		return $this->container->make( Facebook_Meta::class )->filter_ticket_email_url( $virtual_url, $event );
	}

	/**
	 * Enqueues the assets required by the integration.
	 *
	 * @since 1.7.0
	 */
	protected function enqueue_admin_assets() {
		$admin_helpers = Admin_Helpers::instance();

		tribe_asset(
			tribe( Plugin::class ),
			'tec-virtual-fb-sdk-admin',
			'https://connect.facebook.net/en_US/sdk.js',
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
			'tribe-events-virtual-facebook-settings-js',
			'events-virtual-facebook-settings.js',
			[ 'jquery' ],
			'admin_enqueue_scripts',
			[
				'conditionals' => [
					'operator' => 'OR',
					[ $admin_helpers, 'is_screen' ],
				],
				'localize' => [
					'name' => 'tribe_events_virtual_facebook_settings_strings',
					'data' => [
						'localIdFailure'              => static::get_local_id_failure_text(),
						'pageWrapFailure'             => static::get_facebook_page_wrap_failure_text(),
						'connectionFailure'           => static::get_facebook_connection_failure_text(),
						'userTokenFailure'            => static::get_facebook_user_extended_token_failure_text(),
						'pageTokenFailure'            => static::get_facebook_page_token_failure_text(),
						'pageDeleteConfirmation'      => static::get_facebook_page_delete_confirmation_text(),
						'pageClearAccessConfirmation' => static::get_facebook_page_clear_access_confirmation_text(),
						'facebookAppId'               => static::get_facebook_app_id(),
					],
				],
			]
		);
	}

	/**
	 * Enqueues the frontend assets required by the integration.
	 *
	 * @since 1.8.0
	 */
	protected function enqueue_frontend_assets() {
		// If disable setting is checked, do not add the asset.
		if ( tribe_get_option( tribe( Settings::class )->get_prefix( 'disable_fb_js_sdk' ), false ) ) {
			return;
		}

		tribe_asset(
			tribe( Plugin::class ),
			'tec-virtual-fb-sdk',
			'https://connect.facebook.net/en_US/sdk.js',
			[],
			'wp_enqueue_scripts',
			[
				'priority'     => 1,
				'conditionals' => [ tribe( Assets::class ), 'should_enqueue_single_event' ],
				'groups'       => [ Assets::$group_key ],
			]
		);
	}

	/**
	 * Get the local id failure text.
	 *
	 * @since 1.7.0
	 *
	 * @return string The failure text.
	 */
	public static function get_local_id_failure_text() {
		return _x(
			'The local id for the Facebook is not set.',
			'The message to display if no local id is found when trying to authorize a facebook page.',
			'events-virtual'
		);
	}

	/**
	 * Get the Facebook page wrap failure text.
	 *
	 * @since 1.7.0
	 *
	 * @return string The Facebook page wrap text.
	 */
	public static function get_facebook_page_wrap_failure_text() {
		return _x(
			'No Facebook Page data found.',
			'The message to display if no Facebook page wrap found.',
			'events-virtual'
		);
	}

	/**
	 * Get the Facebook connection failure text.
	 *
	 * @since 1.7.0
	 *
	 * @return string The Facebook connection failure text.
	 */
	public static function get_facebook_connection_failure_text() {
		return _x(
			'The Facebook Page could not be connected to your site, please try again.',
			'The message to display if no connection is established to the Facebook sdk.',
			'events-virtual'
		);
	}

	/**
	 * Get the Facebook user extended token failure text.
	 *
	 * @since 1.7.0
	 *
	 * @return string The Facebook user extended token failure text.
	 */
	public static function get_facebook_user_extended_token_failure_text() {
		return _x(
			'The attempt to get an extended Facebook user access token failed with error',
			'The message to display if a Facebook user could not obtain an extended access token.',
			'events-virtual'
		);
	}

	/**
	 * Get the Facebook user extended token failure text.
	 *
	 * @since 1.7.0
	 *
	 * @return string The Facebook user extended token failure text.
	 */
	public static function get_facebook_page_token_failure_text() {
		return _x(
			'Unable to capture the Facebook page’s access token. Please verify your Facebook app credentials. The attempt failed with error',
			'The message to display if a Facebook Page could not obtain an access token.',
			'events-virtual'
		);
	}

	/**
	 * Get the Facebook Page delete confirmation.
	 *
	 * @since 1.7.0
	 *
	 * @return string The Facebook Page delete confirmation text.
	 */
	public static function get_facebook_page_delete_confirmation_text() {
		return _x(
			'Are you sure you want to delete the Facebook Page? Deleting it will disconnect any upcoming virtual events using this Facebook Page.',
			'The message to display to confirm when deleting a Facebook Page.',
			'events-virtual'
		);
	}

	/**
	 * Get the Facebook Page clear access confirmation.
	 *
	 * @since 1.7.0
	 *
	 * @return string The Facebook Page clear access confirmation text.
	 */
	public static function get_facebook_page_clear_access_confirmation_text() {
		return _x(
			'Are you sure you want to clear the access token? Clearing it will disconnect any upcoming virtual events using this Facebook Page until you authorize the page again.',
			'The message to display to confirm clear Facebook Page\'s access token.',
			'events-virtual'
		);
	}

	/**
	 * Get the Facebook app id from the options.
	 *
	 * @since 1.8.0
	 *
	 * @return string The Facebook app id or empty string if not found.
	 */
	public static function get_facebook_app_id() {
		return tribe_get_option( tribe( Settings::class )->get_prefix( 'app_id' ), '' );
	}

	/**
	 * Provides the routes that should be used to handle Facebook API requests.
	 *
	 * The map returned by this method will be used by the `Tribe\Events\Virtual\Traits\With_Nonce_Routes` trait.
	 *
	 * @since 1.7.0
	 *
	 * @return array<string,callable> A map from the nonce actions to the corresponding handlers.
	 */
	public function admin_routes() {
		return [
			Settings::$save_app_action     => $this->container->callback( Settings::class, 'save_app' ),
			Settings::$add_action          => $this->container->callback( Page_API::class, 'add_page' ),
			Settings::$delete_action       => $this->container->callback( Page_API::class, 'delete_page' ),
			Settings::$save_action         => $this->container->callback( Page_API::class, 'save_page' ),
			Settings::$save_access_action  => $this->container->callback( Page_API::class, 'save_access_token' ),
			Settings::$clear_access_action => $this->container->callback( Page_API::class, 'clear_access_token' ),
		];
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
			[ $this, 'action_add_event_single_facebook_embed' ],
			20,
			0
		);
	}
}
