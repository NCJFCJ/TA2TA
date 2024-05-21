<?php
/**
 * Handles the registration of YouTube as a meetings provider.
 *
 * @since   1.6.0
 *
 * @package Tribe\Events\Virtual\Meetings
 */

namespace Tribe\Events\Virtual\Meetings;

use Tribe\Events\Virtual\Meetings\YouTube\Event_Export as YouTube_Event_Export;
use Tribe\Events\Virtual\Meetings\YouTube\Event_Meta as YouTube_Meta;
use Tribe\Events\Virtual\Meetings\YouTube\Settings;
use Tribe\Events\Virtual\Meetings\YouTube\Template_Modifications;
use Tribe\Events\Virtual\Plugin;
use Tribe\Events\Virtual\Traits\With_Nonce_Routes;
use Tribe__Admin__Helpers as Admin_Helpers;
use WP_Post;

/**
 * Class YouTube_Provider
 *
 * @since   1.6.0
 *
 * @package Tribe\Events\Virtual\Meetings
 */
class YouTube_Provider extends Meeting_Provider {

	use With_Nonce_Routes;

	/**
	 * The slug of this provider.
	 *
	 * @since 1.6.0
	 */
	const SLUG = 'youtube';

	/**
	 * {@inheritDoc}
	 */
	public function get_slug() {
		return self::SLUG;
	}

	/**
	 * Registers the bindings, actions and filters required by the YouTube API meetings provider to work.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		// Register this providers in the container to allow calls on it, e.g. to check if enabled.
		$this->container->singleton( 'events-virtual.meetings.youtube', self::class );
		$this->container->singleton( self::class, self::class );

		if ( ! $this->is_enabled() ) {
			return;
		}

		$this->add_actions();
		$this->add_filters();
		$this->enqueue_assets();
		$this->hook_templates();

		/**
		 * Allows filtering of the capability required to use the YouTube integration ajax features.
		 *
		 * @since 1.6.0
		 *
		 * @param string $ajax_capability The capability required to use the ajax features, default manage_options.
		 */
		$ajax_capability = apply_filters( 'tribe_events_virtual_youtube_admin_ajax_capability', 'manage_options' );

		$this->route_admin_by_nonce( $this->admin_routes(), $ajax_capability );
	}

	/**
	 * Hooks the filters required for the YouTube API integration to work correctly.
	 *
	 * @since 1.6.0
	 */
	protected function add_filters() {
		add_filter( 'tribe_rest_event_data', [ $this, 'attach_rest_properties' ], 10, 2 );
		add_filter( 'tribe_addons_tab_fields', [ $this, 'filter_addons_tab_fields' ], 20 );
		add_filter( 'tribe_field_div_end', [ $this, 'setup_channel_trash_icon' ], 10, 2 );
		add_filter( 'tribe_events_virtual_video_sources', [ $this, 'add_video_source' ], 15, 2 );
		add_filter( 'tec_events_virtual_export_fields', [ $this, 'filter_youtube_source_google_calendar_parameters' ], 10, 5 );
		add_filter( 'tec_events_virtual_export_fields', [ $this, 'filter_youtube_source_ical_feed_items' ], 10, 5 );

		// Filter event object properties to add YouTube live status.
		add_filter( 'tribe_get_event_after', [ $this, 'add_dynamic_properties' ], 15 );

		// Filter the ticket email virtual url.
		add_filter( 'tribe_events_virtual_ticket_email_url', [ $this, 'filter_ticket_email_url' ], 15, 2 );
		add_filter( 'tec_events_virtual_ticket_email_url', [ $this, 'filter_ticket_email_url' ], 15, 2 );
		add_action( 'tec_virtual_automator_map_event_details', [ $this, 'add_event_automator_properties' ], 10, 2 );
	}

	/**
	 * Hooks the actions required for the YouTube API integration to work correctly.
	 *
	 * @since 1.6.0
	 */
	protected function add_actions() {
		add_action( 'tribe_events_virtual_add_event_properties', [ $this, 'add_event_properties' ] );
		add_action( 'tribe_events_virtual_metabox_save', [ $this, 'on_metabox_save' ], 10, 2 );
	}

	/**
	 * Hooks the template required for the integration to work.
	 *
	 * @since 1.6.0
	 */
	protected function hook_templates() {
		// Metabox.
		add_action(
			'tribe_template_entry_point:events-virtual/admin-views/virtual-metabox/container/video-source:video_sources',
			[ $this, 'render_classic_setup_options' ],
			10,
			3
		);

		// "Classic" Single
		add_action(
			'tribe_events_single_event_after_the_content',
			[ $this, 'action_add_event_single_youtube_embed' ],
			15,
			0
		);

		// Single Block
		add_action( 'wp', [ $this, 'hook_block_template' ] );
	}

	/**
	 * Add the YouTube Video Source.
	 *
	 * @since 1.6.0
	 *
	 * @param array<string|string> An array of video sources.
	 * @param \WP_Post $post       The current event post object, as decorated by the `tribe_get_event` function.
	 *
	 * @return array<string|string> An array of video sources.
	 */
	public function add_video_source( $video_sources, $post ) {

		$video_sources[] = [
			'text'     => _x( 'YouTube Live', 'The name of the video source.', 'events-virtual' ),
			'id'       => 'youtube',
			'value'    => 'youtube',
			'selected' => 'youtube' === $post->virtual_video_source,
		];

		return $video_sources;
	}

	/**
	 * Filter the Google Calendar export fields for a YouTube Live source event.
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
	public function filter_youtube_source_google_calendar_parameters( $fields, $event, $key_name, $type, $should_show ) {

		return $this->container->make( YouTube_Event_Export::class )->modify_video_source_export_output( $fields, $event, $key_name, $type, $should_show );
	}

	/**
	 * Filter the iCal export fields for a YouTube Live source event.
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
	public function filter_youtube_source_ical_feed_items( $fields, $event, $key_name, $type, $should_show ) {
		return $this->container->make( YouTube_Event_Export::class )->modify_video_source_export_output( $fields, $event, $key_name, $type, $should_show );
	}

	/**
	 * Enqueues the assets required by the integration.
	 *
	 * @since 1.6.0
	 */
	protected function enqueue_assets() {
		$admin_helpers = Admin_Helpers::instance();

		tribe_asset(
			tribe( Plugin::class ),
			'tribe-events-virtual-youtube-settings-js',
			'events-virtual-youtube-settings.js',
			[ 'jquery', 'tribe-events-views-v2-accordion' ],
			'admin_enqueue_scripts',
			[
				'conditionals' => [
					'operator' => 'OR',
					[ $admin_helpers, 'is_screen' ],
				],
				'localize' => [
					'name' => 'tribe_events_virtual_youtube_settings_strings',
					'data' => [
						'deleteConfirm'  => static::get_youtube_confirmation_to_delete_account(),
					],
				],
			]
		);
	}

	/**
	 * Add information about the YouTube live stream if available via the REST Api.
	 *
	 * @since 1.6.0
	 *
	 * @param array<string,mixed> $data  The current data of the event.
	 * @param \WP_Post            $event The event being updated.
	 *
	 * @return array<string,mixed> An array with the data of the event on the endpoint.
	 */
	public function attach_rest_properties( array $data, \WP_Post $event ) {
		return tribe( YouTube_Meta::class )->attach_rest_properties( $data, $event );
	}

	/**
	 * Filters the fields in the Events > Settings > APIs tab to add the ones provided by the extension.
	 *
	 * @since 1.6.0
	 *
	 * @param array<string,array> $fields The current fields.
	 *
	 * @return array<string,array> The fields, as updated by the settings.
	 */
	public function filter_addons_tab_fields( $fields ) {
		if ( ! is_array( $fields ) ) {
			return $fields;
		}

		return tribe( YouTube\Settings::class )->add_fields( $fields );
	}

	/**
	 * Add the channel trash icon with AJAX url.
	 *
	 * @since 1.6.0
	 *
	 * @param string              $html  The html for the end of the field.
	 * @param array<string|mixed> $field An array of the field attributes.
	 *
	 * @return string The html for the trash icon along with remaining field html.
	 */
	public function setup_channel_trash_icon( $html, $field ) {
		return tribe( YouTube\Template_Modifications::class )->setup_channel_trash_icon( $html, $field );
	}

	/**
	 * Provides the routes that should be used to handle YouTube API requests.
	 *
	 * The map returned by this method will be used by the `Tribe\Events\Virtual\Traits\With_Nonce_Routes` trait.
	 *
	 * @since 1.6.0
	 *
	 * @return array<string,callable> A map from the nonce actions to the corresponding handlers.
	 */
	public function admin_routes() {
		return [
			Settings::$delete_action => $this->container->callback( Settings::class, 'ajax_delete' ),
		];
	}

	/**
	 * Get the confirmation text for deleting a YouTube channel ID.
	 *
	 * @since 1.6.0
	 *
	 * @return string The confirmation text.
	 */
	public static function get_youtube_confirmation_to_delete_account() {
		return _x(
			'Are you sure you want to delete your default YouTube channel ID?',
			'The message to display to confirm a user would like to delete a YouTube channel ID.',
			'events-virtual'
		);
	}

	/**
	 * Renders the YouTube Live Integration Fields.
	 *
	 * @since 1.6.0
	 *
	 * @param string           $file        The path to the template file, unused.
	 * @param string           $entry_point The name of the template entry point, unused.
	 * @param \Tribe__Template $template    The current template instance.
	 */
	public function render_classic_setup_options( $file, $entry_point, \Tribe__Template $template ) {
		$this->container->make( YouTube\Classic_Editor::class )
		                ->render_setup_options( $template->get( 'post' ) );
	}

	/**
	 * Include the YouTube embed for event single.
	 *
	 * @since 1.6.0
	 */
	public function action_add_event_single_youtube_embed() {
		$this->container->make( Template_Modifications::class )
						->add_youtube_video_embed();
	}

	/**
	 * Filters the object returned by the `tribe_get_event` function to add to it properties related to YouTube.
	 *
	 * @since 1.6.0
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

		return $this->container->make( YouTube_Meta::class )->add_event_properties( $event );
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

		return $this->container->make( YouTube_Meta::class )->add_event_automator_properties( $next_event, $event );
	}

	/**
	 * Handles the save operations of the Classic Editor VE Metabox.
	 *
	 * @since 1.6.0
	 *
	 * @param int                 $post_id The post ID of the event currently being saved.
	 * @param array<string,mixed> $data    The data currently being saved.
	 */
	public function on_metabox_save( $post_id, $data ) {
		$post = get_post( $post_id );
		if ( ! $post instanceof \WP_Post && is_array( $data ) ) {
			return;
		}

		$this->container->make( YouTube_Meta::class )->save_metabox_data( $post_id, $data );
	}

	/**
	 * Adds dynamic, time-related, properties to the event object.
	 *
	 * This method deals with properties we set, for convenience, on the event object that should not
	 * be cached as they are time-dependent; i.e. the time the properties are computed at matters and
	 * caching their values would be incorrect.
	 *
	 * @since 1.6.0
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

		return $this->container->make( YouTube_Meta::class )->add_dynamic_properties( $post );
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
			[
				$this,
				'action_add_event_single_youtube_embed',
			],
			12
		);
	}

	/**
	 * Filter the ticket email url.
	 *
	 * @since 1.7.2
	 *
	 * @param string  $virtual_url The virtual url for the ticket and rsvp emails.
	 * @param WP_Post $event       The event post object with properties added by the `tribe_get_event` function.
	 *
	 * @return string The YouTube virtual url for the ticket and rsvp emails.
	 */
	public function filter_ticket_email_url( $virtual_url, WP_Post $event ) {
		return $this->container->make( YouTube_Meta::class )->filter_ticket_email_url( $virtual_url, $event );
	}
}
