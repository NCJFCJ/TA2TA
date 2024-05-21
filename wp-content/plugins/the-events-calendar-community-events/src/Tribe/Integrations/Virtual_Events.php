<?php

/**
 * Handle integration with the Virtual Events add-on.
 *
 * @since 4.9.2 Updated to only allow specific video sources when not an admin role.
 * @since 4.8.0
 */

use Tribe\Events\Virtual\Assets;
use Tribe\Events\Virtual\Meetings\Zoom_Provider;

/**
 * Handle integration of Community Events and the Virtual Events add-on.
 *
 * Does not run unless Virtual Events add-on is active.
 *
 * @see \Tribe__Events__Community__Integrations__Manager::load_virtual_events_integration()
 *
 * @since 4.8.0
 */
class Tribe__Events__Community__Integrations__Virtual_Events {

	/**
	 * Setup the hooks for Virtual Events integration.
	 *
	 * @since 4.8.0
	 */
	public function hooks() {
		add_filter( 'tribe_events_virtual_link_allow_generation', [ $this, 'maybe_allow_meeting_link_generation' ] );
		add_action( 'tribe_community_events_enqueue_resources', [ $this, 'enqueue_assets' ] );
		add_action( 'tribe_events_community_form_before_linked_posts', [ $this, 'render_meta_box' ] );
	  	add_filter( 'tribe_events_virtual_video_source_placeholder_text', [ $this, 'generate_video_source_placeholder_text' ] );
		add_action( 'tribe_events_virtual_video_sources', [ $this, 'manage_possible_video_sources' ] , 100 , 1);
		add_action( 'tec_events_virtual_autodetect_video_sources', [ $this, 'manage_possible_autodetect_video_sources' ] , 100 , 1);
		add_filter( 'user_has_cap', [ $this, 'enable_for_visitors' ], 10, 3 );
		add_action( 'init', [ $this, 'maybe_remove_share_section' ], 50 );

	}

	/**
	 * Maybe hide the share section for Virtual Events. The share section is hidden if
	 * Community Tickets is not enabled.
	 *
	 * @since 4.10.6
	 *
	 * @return void
	 */
	public function maybe_remove_share_section() {
		// If we are on the backend, return.
		if ( is_admin() ) {
			return;
		}

		$is_ct_active = tribe_check_plugin( 'Tribe__Events__Community__Tickets__Main' );

		// If CT is active, return.
		if ( $is_ct_active ) {
			return;
		}

		remove_action( 'tribe_template_entry_point:events-virtual/admin-views/virtual-metabox/container/compatibility/event-tickets/share:before_share_list_end', [
			tribe( Tribe\Events\Virtual\Compatibility\Event_Tickets\Service_Provider::class ),
			'share_ticket_controls'
		] );
		remove_action( 'tribe_template_before_include:events-virtual/admin-views/virtual-metabox/container/label', [
			tribe( Tribe\Events\Virtual\Compatibility\Event_Tickets\Service_Provider::class ),
			'share_rsvp_controls'
		] );
	}

	/**
	 * When Anonymous Submissions is enabled for CE, make sure it is also enabled for VE.
	 *
	 * @since 4.10.0
	 *
	 * @param array $user_caps      The capabilities the user has
	 * @param array $requested_caps The capabilities the user needs
	 * @param array $args           [0] = The specific cap requested, [1] = The user ID, [2] = Post ID
	 *
	 * @return array mixed
	 */
	public function enable_for_visitors( $user_caps, $requested_caps, $args ) {
		$current_user_id = $args[1];

		// Bail if the Post ID args[2] isn't sent, or the Current user isn't a visitor.
		if ( ! array_key_exists( 2, $args )
		     || $current_user_id !== 0
		) {
			return $user_caps;
		}

		$edit_event_cap = get_post_type_object( Tribe__Events__Main::POSTTYPE )->cap->edit_posts;

		// Make sure our post type object comes back with data, or the requested cap isn't 'edit_tribe_events'.
		if ( empty( $edit_event_cap )
		     || $requested_caps[0] !== $edit_event_cap
		) {
			return $user_caps;
		}

		$community_events     = tribe( 'community.main' );
		$anonymousSubmissions = $community_events->allowAnonymousSubmissions;
		$editPage             = $community_events->isEditPage;
		$event_id             = $args[2];
		$event                = get_post( $event_id );


		// Bail if the $event is not of type WP_Post, or if the post type is not an event.
		if ( ! $event instanceof \WP_Post || $event->post_type !== Tribe__Events__Main::POSTTYPE ) {
			return $user_caps;
		}
		// If Anonymous submissions is disabled, or you are not on the edit page, bail.
		if ( ! $anonymousSubmissions || ! $editPage ) {
			return $user_caps;
		}

		// If you aren't a visitor, or the post author, bail.
		if ( $current_user_id !==  (int) $event->post_author  ) {
			return $user_caps;
		}

		// If you have passed all checks grant the `edit_tribe_events` permission.
		$user_caps['edit_tribe_events'] = true;

		return $user_caps;
	}

	/**
	 * Maybe allow meeting link generation.
	 *
	 * @since 4.8.0
	 *
	 * @param boolean $allow_generation Whether to allow meeting link generation.
	 *
	 * @return boolean Whether to allow meeting link generation.
	 */
	public function maybe_allow_meeting_link_generation( $allow_generation ) {
		if ( false === $allow_generation ) {
			return $allow_generation;
		}

		// Don't allow meeting link generation from the frontend or if they are not a site admin.
		if ( ! is_admin() || ( ! is_super_admin() && ! current_user_can( 'manage_options' ) ) ) {
			return false;
		}

		return $allow_generation;
	}

	/**
	 * Method to determine whether or not Virtual Event assets need to be enqueued.
	 *
	 * @since 4.8.9
	 * @return boolean
	 */
	private function should_enqueue_assets() {
		// If on the CE event page, return true.
		if ( tribe_is_community_edit_event_page() ) {
			return true;
		}

		// If current page is not a page or post, return false.
		$post = get_queried_object();
		if ( ! $post instanceof WP_Post ) {
			return false;
		}

		// Return if current page/post has the [tribe_community_events] shortcode.
		return has_shortcode( $post->post_content, 'tribe_community_events' );
	}

	/**
	 * Handle enqueuing the assets for Virtual Events.
	 *
	 * @since 4.8.0
	 * @since 4.8.3 Enqueue Zoom Admin CSS and JS, if appropriate.
	 */
	public function enqueue_assets() {
		if ( ! $this->should_enqueue_assets() ) {
			return;
		}

		// VE's 'admin' CSS and JS are required for CE's front-end.
		$assets_to_enqueue = [
			'tribe-events-virtual-admin-css',
			'tribe-events-virtual-admin-js',
		];

		/** @var Zoom_Provider $zoom */
		$zoom = tribe( Zoom_Provider::class );

		/**
		 * @see \Tribe\Events\Virtual\Meetings\Zoom_Provider::register() Replicate that logic.
		 */
		if ( $zoom->is_enabled() ) {
			$assets_to_enqueue[] = 'tribe-events-virtual-zoom-admin-style';
			$assets_to_enqueue[] = 'tribe-events-virtual-zoom-admin-js';
		}

		tribe_asset_enqueue( $assets_to_enqueue );

		tribe_asset_enqueue_group( Assets::$group_key );
	}

	/**
	 * Handle rendering the Virtual Events meta box.
	 *
	 * @since 4.8.0
	 *
	 * @param int|WP_Post $event Event object or ID.
	 */
	public function render_meta_box( $event ) {
		$data = [
			'event' => $event,
		];

		tribe_get_template_part( 'community/modules/virtual', null, $data );
	}

	/**
	 * Overwrite placeholder text for video source to correspond with
	 * only allowing Youtube and Vimeo (oembed).
	 *
	 * @since 4.9.2
	 *
	 * @return string|void
	 */
	public function generate_video_source_placeholder_text() {
		return _x( 'Enter URL for hosted video (YouTube, Vimeo, etc.)',
			'Default placeholder text for the virtual event smart URL input.',
			'tribe-events-community' );
	}

	/**
	 * Filters the $video_sources variable against the $allowed_sources.
	 *
	 * @since 4.9.2
	 *
	 * @param array $video_sources An array of video sources.
	 * @param array $allowed_sources An array of allowed video sources.
	 *
	 * @return array Filtered list of video sources.
	 */
	public function modify_video_sources( array $video_sources, array $allowed_sources ) {
		return array_values( array_filter( $video_sources, function ( $source ) use ( &$allowed_sources ) {
			if ( in_array( $source[ 'id' ], $allowed_sources ) ) {
				return $source;
			}
		} ) );
	}

	/**
	 * Manage possible video sources for virtual Events.
	 * Administrators have access to all.
	 * All other roles default to `oembed`.
	 *
	 * @since 4.9.2
	 *
	 * @param array $video_sources An array of video sources.
	 *
	 * @return array An array of video sources.
	 */
	public function manage_possible_autodetect_video_sources( array $video_sources ) {

		// If an administrator, bail as you have access to all video sources.
		if ( is_admin() ) {
			return $video_sources;
		}

		$allowed_sources = [ 'oembed' ];

		return $this->modify_video_sources( $video_sources, $allowed_sources );

	}

	/**
	 * Manage possible video sources for virtual Events.
	 * Administrators have access to all.
	 * All other roles default to `video` and `youtube`.
	 *
	 * @since 4.9.2
	 *
	 * @param array $video_sources An array of video sources.
	 *
	 * @return array An array of video sources.
	 */
	public function manage_possible_video_sources( array $video_sources ) {

		// If an administrator, bail as you have access to all video sources.
		if ( is_admin() ) {
			return $video_sources;
		}

		$allowed_sources = [ 'video', 'youtube' ];

		return $this->modify_video_sources( $video_sources, $allowed_sources );

	}

}
