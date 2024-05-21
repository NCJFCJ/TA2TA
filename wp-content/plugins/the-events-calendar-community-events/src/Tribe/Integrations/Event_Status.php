<?php

namespace Tribe\Events\Community\Integrations;

/**
 * Handle integration of Event Status add-on.
 *
 *
 * @see   \Tribe__Events__Community__Integrations__Manager::load_event_status()
 *
 * @since 4.10.13 Added the correct capabilities to use Event Status.
 *
 * @since 4.8.11
 */

use Tribe__Events__Main;

class Event_Status {

	/**
	 * Setup the hooks for Event Status integration.
	 *
	 * @since 4.8.11
	 */
	public function hooks() {
		add_action( 'tribe_events_community_form_before_linked_posts', [ $this, 'render_meta_box' ] );
		add_filter( 'user_has_cap', [ $this, 'enable_event_status_caps' ], 10, 3 );
	}

	/**
	 * Handle rendering the event status meta box.
	 *
	 * @since 4.8.11
	 *
	 * @param int|WP_Post $event Event object or ID.
	 */
	public function render_meta_box( $event ) {
		$data = [
			'event' => $event,
		];

		tribe_get_template_part( 'community/modules/event_status', null, $data );
	}

	/**
	 * Enable Event_Status for all roles who are able to use it.
	 *
	 * @since 4.10.13
	 *
	 * @param array $user_caps      The capabilities the user has
	 * @param array $requested_caps The capabilities the user needs
	 * @param array $args           [0] = The specific cap requested, [1] = The user ID, [2] = Post ID
	 *
	 * @return array mixed
	 */
	public function enable_event_status_caps( array $user_caps, array $requested_caps, array $args ): array {
		// Validate the event ID
		if ( ! isset( $args[2] ) || ! is_int( $args[2] ) ) {
			return $user_caps;
		}
		$event_id = $args[2];

		// Retrieve the event post
		$event = get_post( $event_id );

		// Validate the event post
		if ( ! $event instanceof \WP_Post || $event->post_type !== Tribe__Events__Main::POSTTYPE ) {
			return $user_caps;
		}

		// Get the current user ID
		$current_user_id = get_current_user_id();

		// Validate the current user against the post author
		if ( $current_user_id !== (int) $event->post_author ) {
			return $user_caps;
		}

		// Retrieve the post type object for events
		$post_type_object = get_post_type_object( Tribe__Events__Main::POSTTYPE );

		// Check if the post type object is valid
		if ( ! is_object( $post_type_object ) || ! isset( $post_type_object->cap->edit_posts ) ) {
			return $user_caps;
		}

		// Retrieve the edit capability for events
		$edit_event_cap = $post_type_object->cap->edit_posts;

		// Validate the edit capability and requested capability
		if ( empty( $edit_event_cap ) || ! isset( $requested_caps[0] ) || $requested_caps[0] !== $edit_event_cap ) {
			return $user_caps;
		}

		// Retrieve community events settings
		$community_events     = tribe( 'community.main' );
		$editPage             = $community_events->isEditPage;

		if ( ! $editPage ) {
			return $user_caps;
		}

		// Grant the `edit_tribe_events` permission if all checks pass
		$user_caps['edit_tribe_events'] = true;

		return $user_caps;
	}

}