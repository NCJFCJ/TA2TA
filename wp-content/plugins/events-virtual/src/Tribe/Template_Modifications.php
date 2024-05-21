<?php
/**
 * Handles template modifications.
 *
 * @since 1.0.0
 *
 * @package Tribe\Events\Virtual
 */

namespace Tribe\Events\Virtual;

use Tribe\Events\Virtual\Meetings\Facebook\Event_Meta as Facebook_Meta;
use Tribe__Events__Main as Events_Plugin;
use WP_Post;

/**
 * Class Template_Modifications.
 *
 * @since 1.0.0
 *
 * @package Tribe\Events\Virtual
 */
class Template_Modifications {
	/**
	 * Stores the template class used.
	 *
	 * @since 1.0.0
	 *
	 * @var Template
	 */
	protected $template;

	/**
	 * An instance of the admin template handler.
	 *
	 * @since 1.9.0
	 *
	 * @var Admin_Template
	 */
	protected $admin_template;

	/**
	 * Template_Modifications constructor.
	 *
	 * @since 1.0.0
	 * @since 1.9.0 - Add Admin_Template handler.
	 *
	 * @param Template       $template An instance of the front-end template handler.
	 * @param Admin_Template $template An instance of the backend template handler.
	 */
	public function __construct( Template $template, Admin_Template $admin_template ) {
		$this->template       = $template;
		$this->admin_template = $admin_template;
	}

	/**
	 * Determines if the virtual content should be shown
	 * based on the `virtual_show_embed_to` setting of the event.
	 *
	 * @since 1.0.4
	 * @since 1.9.0 - Only use tribe_get_event if an integer and handle no show to setting checked.
	 *
	 * @param int|WP_Post $event Post ID or post object.
	 *
	 * @return boolean
	 */
	public function should_show_virtual_content( $event ) {
		if ( is_integer( $event ) ) {
		     $event = tribe_get_event( $event );
		}

		if ( ! $event instanceof \WP_Post) {
			return false;
		}

		$show_to_arr = array_flip( $event->virtual_show_embed_to );
		$show =  isset( $show_to_arr[ Event_Meta::$value_show_embed_to_all ] )
		 || ( isset(  $show_to_arr[ Event_Meta::$value_show_embed_to_logged_in ] ) && is_user_logged_in() );

		/**
		 * Filters whether the virtual content should show or not.
		 *
		 * @since 1.0.4
		 *
		 * @param boolean     $show  If the virtual content should show or not.
		 * @param int|WP_Post $event The post object or ID of the viewed event.
		 */
		return apply_filters( 'tribe_events_virtual_show_virtual_content', $show, $event );
	}

	/**
	 * Add the control classes for the views v2 elements
	 *
	 * @since 1.0.0
	 *
	 * @param int|WP_Post $event Post ID or post object.
	 *
	 * @return string[]
	 */
	public function get_body_classes( $event ) {
		$classes = [];
		if ( ! tribe_is_event( $event ) ) {
			return $classes;
		}

		$event = tribe_get_event( $event );

		if ( ! $event instanceof \WP_Post) {
			return $classes;
		}

		if ( $event->virtual ) {
			$classes[] = 'tribe-events-virtual-event';
		}

		if ( Event_Meta::$value_hybrid_event_type === $event->virtual_event_type ) {
			$classes[] = 'tribe-events-hybrid-event';
		}

		return $classes;
	}

	/**
	 * Add the control classes for the views v2 elements
	 *
	 * @since 1.0.0
	 *
	 * @param int|WP_Post $event Post ID or post object.
	 *
	 * @return string[]
	 */
	public function get_post_classes( $event ) {
		$classes = [];
		if ( ! tribe_is_event( $event ) ) {
			return $classes;
		}

		/**
		 * We're specifically forcing here (last param) as otherwise
		 * this runs into issues with the event list table in the admin.
		 */
		$event = tribe_get_event( $event, OBJECT, 'raw', true );

		if ( ! $event instanceof \WP_Post) {
			return $classes;
		}

		if ( $event->virtual ) {
			$classes[] = 'tribe-events-virtual-event';
		}

		if ( Event_Meta::$value_hybrid_event_type === $event->virtual_event_type ) {
			$classes[] = 'tribe-events-hybrid-event';
		}

		return $classes;
	}

	/**
	 * Include the control markers to the single page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $notices_html Previously set HTML.
	 *
	 * @return string New notices with the control markers appended.
	 */
	public function add_single_control_mobile_markers( $notices_html ) {
		if ( ! is_singular( Events_Plugin::POSTTYPE ) ) {
			return $notices_html;
		}

		$args = [
			'event' => tribe_get_event( get_the_ID() ),
		];

		return $this->template->template( 'single/virtual-marker-mobile', $args, false ) . $notices_html;
	}

	/**
	 * Include the hybrid control markers to the single page.
	 *
	 * @since 1.4.0
	 *
	 * @param string $notices_html Previously set HTML.
	 *
	 * @return string New notices with the control markers appended.
	 */
	public function add_single_hybrid_control_mobile_markers( $notices_html ) {
		if ( ! is_singular( Events_Plugin::POSTTYPE ) ) {
			return $notices_html;
		}

		$args = [
			'event' => tribe_get_event( get_the_ID() ),
		];

		return $this->template->template( 'single/hybrid-marker-mobile', $args, false ) . $notices_html;
	}

	/**
	 * Get the event object of the event on which to show control markers.
	 *
	 * @since 1.4.1
	 *
	 * @param string $schedule  The output HTML.
	 * @param int    $event_id  The post ID of the event we are interested in.
	 *
	 * @return bool|\WP_Post Event on which to show control markers.
	 */
	public function get_control_marker_event( $event_id ) {
		$should_show = true;

		if ( ! is_singular( Events_Plugin::POSTTYPE ) ) {
			$should_show = false;
		}

		/**
		 * Filter whether we should show control markers.
		 *
		 * @since 1.4.1
		 *
		 * @param bool $should_show Whether to show the markers or not.
		 * @param int $event_id The post ID of the event we are interested in.
		 */
		$should_show = apply_filters( 'tribe_events_virtual_should_show_control_markers', $should_show, $event_id );

		if ( ! $should_show ) {
			return false;
		}

		// The following checks are mandatory for bailing, so we'll constrain the filtering to up above.

		// Bail if this action was already introduced.
		if ( $should_show && did_action( 'tribe_tickets_ticket_email_top' ) ) {
			return false;
		}

		$event = tribe_get_event( $event_id );

		if ( ! $event instanceof \WP_Post) {
			return false;
		}

		if ( ! $event->virtual ) {
			return false;
		}

		return $event;
	}

	/**
	 * Include the control markers to the single page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $schedule  The output HTML.
	 * @param int    $event_id  The post ID of the event we are interested in.
	 *
	 * @return string  New details with the control markers appended.
	 */
	public function add_single_control_markers( $schedule, $event_id ) {
		if ( ! $event = $this->get_control_marker_event( $event_id ) ) {
			return $schedule;
		}

		$args = [ 'event' => $event ];

		return $schedule . $this->template->template( 'single/virtual-marker', $args, false );
	}

	/**
	 * Include the control markers to the single page.
	 *
	 * @since 1.4.0
	 *
	 * @param string $schedule  The output HTML.
	 * @param int    $event_id  The post ID of the event we are interested in.
	 *
	 * @return string  New details with the control markers appended.
	 */
	public function add_single_hybrid_control_markers( $schedule, $event_id ) {
		if ( ! $event = $this->get_control_marker_event( $event_id ) ) {
			return $schedule;
		}

		$args = [ 'event' => $event ];

		return $schedule . $this->template->template( 'single/hybrid-marker', $args, false );
	}

	/**
	 * Adds Template for Virtual Event marker.
	 *
	 * @since 1.0.0
	 *
	 * @param string   $file      Complete path to include the PHP File.
	 * @param array    $name      Template name.
	 * @param Template $template  Current instance of the Template.
	 */
	public function add_virtual_event_marker( $file, $name, $template ) {
		$context = $template->get_values();
		$template->template( 'components/virtual-event', $context );
	}

	/**
	 * Adds Template for Hybrid Event marker.
	 *
	 * @since 1.4.0
	 *
	 * @param string   $file      Complete path to include the PHP File.
	 * @param array    $name      Template name.
	 * @param Template $template  Current instance of the Template.
	 */
	public function add_hybrid_event_marker( $file, $name, $template ) {
		$context = $template->get_values();
		$template->template( 'components/hybrid-event', $context );
	}

	/**
	 * Adds Block Template for Virtual Event Marker.
	 *
	 * @since 1.0.1
	 */
	public function add_single_block_virtual_event_marker() {
		$event = tribe_get_event( get_the_ID() );

		if ( ! $event instanceof \WP_Post) {
			return;
		}

		if ( ! $event->virtual ) {
			return;
		}

		$args = [ 'event' => $event ];

		$this->template->template( 'single/virtual-marker', $args );
	}

	/**
	 * Adds Block Template for Hybrid Event Marker.
	 *
	 * @since 1.4.0
	 */
	public function add_single_block_hybrid_event_marker() {
		$event = tribe_get_event( get_the_ID() );

		if ( ! $event instanceof \WP_Post ) {
			return;
		}

		if ( ! $event->virtual ) {
			return;
		}

		$args = [ 'event' => $event ];

		$this->template->template( 'single/hybrid-marker', $args );
	}

	/**
	 * Adds video embed to event single.
	 *
	 * @since 1.0.0
	 * @since 1.0.4 Added possible login requirement.
	 * @since 1.6.0 - Add video source support.
	 */
	public function add_event_single_video_embed() {
		// don't show on password protected posts.
		if ( post_password_required() ) {
			return;
		}

		$event = tribe_get_event( get_the_ID() );

		if ( ! $event instanceof \WP_Post) {
			return;
		}

		// Only embed when the source is video.
		if ( Event_Meta::$key_video_source_id !== $event->virtual_video_source ) {
			return;
		}

		// Don't show if requires log in and user isn't logged in.
		if ( ! $this->should_show_virtual_content( $event ) ) {
			return;
		}

		$context = [
			'event' => $event,
		];

		if ( Facebook_Meta::$autodetect_fb_video_id === $event->virtual_autodetect_source ) {
			$this->template->template( 'facebook/single/facebook-video-embed', $context );

			return;
		}

		$this->template->template( 'single/video-embed', $context );
	}

	/**
	 * Adds link button to event single outside of details block.
	 *
	 * @since 1.0.0
	 */
	public function add_event_single_non_block_link_button() {
		$block_slug = tribe( 'events.editor.blocks.classic-event-details' )->slug();
		// If we're also rendering the event details block, we don't want to inject this block.
		// @see action_add_event_single_details_block_link_button().
		if ( has_block( "tribe/{$block_slug}", get_the_ID() ) ) {
			return;
		}

		return $this->add_event_single_link_button();
	}

	/**
	 * Adds link button to event single.
	 *
	 * @since 1.0.0
	 * @since 1.0.4 Added possible login requirement.
	 */
	public function add_event_single_link_button() {
		// don't show on password protected posts.
		if ( post_password_required() ) {
			return;
		}

		$event = tribe_get_event( get_the_ID() );

		if ( ! $event instanceof \WP_Post) {
			return;
		}

		if ( empty( $event->virtual ) ){
			return;
		}

		if ( empty( $event->virtual_should_show_embed ) ) {
			return;
		}

		if ( empty( $event->virtual_linked_button ) ) {
			return;
		}

		if ( empty( $event->virtual_url ) ) {
			return;
		}

		// Don't show if requires log in and user isn't logged in.
		if ( ! $this->should_show_virtual_content( $event ) ) {
			return;
		}

		/**
		 * Filters whether the link button should open in a new window or not.
		 *
		 * @since 1.0.0
		 *
		 * @param boolean $new_window If link button should open in new window.
		 */
		$new_window = apply_filters( 'tribe_events_virtual_link_button_new_window', false );

		$attrs = [];
		if ( ! empty( $new_window ) ) {
			$attrs['target'] = '_blank';
		}

		$context = [
			'url'   => $event->virtual_url,
			'label' => $event->virtual_linked_button_text,
			'attrs' => $attrs,
		];

		$this->template->template( 'components/link-button', $context );
	}

	/**
	 * The message template to display on user account changes.
	 *
	 * @since 1.5.0
	 *
	 * @param string $message The message to display.
	 * @param string $type    The type of message, either updated or error.
	 *
	 * @return string The message with html to display
	 */
	public function get_settings_message_template( $message, $type = 'updated' ) {
		return $this->admin_template->template( 'components/message', [
			'message' => $message,
			'type'    => $type,
		] );
	}
}
