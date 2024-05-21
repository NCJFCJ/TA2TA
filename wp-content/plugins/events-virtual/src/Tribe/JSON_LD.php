<?php
/**
 * Handles updates to events JSON-LD schema to mark them as virtual (online) events.
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual
 */

namespace Tribe\Events\Virtual;

use WP_Post;

/**
 * Class JSON_LD.
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual
 */
class JSON_LD {

	/**
	 * The reference schema URL for an online/virtual event attendance mode.
	 *
	 * @since 1.0.0
	 */
	const ONLINE_EVENT_ATTENDANCE_MODE = 'https://schema.org/OnlineEventAttendanceMode';

	/**
	 * The reference schema URL for an event that has both virtual and physical attendance mode.
	 *
	 * @since 1.0.0
	 */
	const MIXED_EVENT_ATTENDANCE_MODE= 'https://schema.org/MixedEventAttendanceMode';

	/**
	 * The reference schema URL for a moved online event status mode.
	 *
	 * @since 1.7.3
	 */
	const MOVEDONLINE_SCHEMA = 'https://schema.org/EventMovedOnline';

	/**
	 * Modifiers to the JSON LD event object for virtual attendance events.
	 *
	 * @since 1.0.0
	 * @since 1.7.3 - Add support for eventStatus MOVEDONLINE_SCHEMA.
	 *
	 * @param object  $data The JSON-LD object.
	 * @param array   $args The arguments used to get data.
	 * @param WP_Post $post The post object.
	 *
	 * @return object JSON LD object after modifications.
	 */
	public function modify_virtual_event( $data, $args, $post ) {
		// Skip any events without proper data.
		if ( empty( $data->startDate ) || empty( $data->endDate ) ) {
			return $data;
		}

		$event = tribe_get_event( $post );

		if ( ! $event instanceof \WP_Post) {
			return $data;
		}

		/**
		 * Filters if an Event is Considered "Online" in JSON-LD context.
		 *
		 * @since 1.0.0
		 *
		 * @param boolean $virtual If an event is considered virtual.
		 * @param object  $data    The JSON-LD object.
		 * @param array   $args    The arguments used to get data.
		 * @param WP_Post $post    The post object.
		 */
		$virtual = apply_filters( 'tribe_events_virtual_single_event_online_status', $event->virtual, $data, $args, $post );

		// Bail on modifications for non canceled events.
		if ( ! $virtual ) {
			return $data;
		}

		// Set as online by default, but if hybrid set to mixed.
		$data->eventAttendanceMode = static::ONLINE_EVENT_ATTENDANCE_MODE;
		if ( Event_Meta::$value_hybrid_event_type === $event->virtual_event_type ) {
			$data->eventAttendanceMode = static::MIXED_EVENT_ATTENDANCE_MODE;
		}

		// Update event status schema.
		if ( 'moved-online' === $event->event_status ) {
			$data->eventStatus = static::MOVEDONLINE_SCHEMA;
		}

		if (
			empty( $data->location ) ||
			! is_object( $data->location )
		) {
			// If the physical location is not present, then mark this event as online only.
			$data->location            = (object) [
				'@type' => 'VirtualLocation',
				'url'   => esc_url( $this->get_virtual_url( $post ) ),
			];
		} else {
			// If a physical location is set for the event, then assign both locations.
			// Override the event attendance when there is a location.
			$data->eventAttendanceMode = static::MIXED_EVENT_ATTENDANCE_MODE;
			$data->location            = [
				(object) [
					'@type' => 'VirtualLocation',
					'url'   => esc_url( $this->get_virtual_url( $post ) ),
				],
				$data->location
			];
		}

		return $data;
	}

	/**
	 * Get the virtual URL for an event trying the virtual URL, the website URL, and using the permalink if nothing found.
	 * A URL is required when using virtualLocation.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post The post object to use to get the virtual url for an event.
	 *
	 * @return string The string of the virtual url for an event if available.
	 */
	protected function get_virtual_url( $post ) {
		$event = tribe_get_event( $post );

		if ( ! $event instanceof \WP_Post) {
			return '';
		}

		$virtual_url = $event->virtual_url;

		// If empty get website URL.
		if ( empty( $virtual_url ) ) {
			$virtual_url = get_post_meta( $post->ID, '_EventURL', true );
		}

		// If both are empty then get the permalink.
		if ( empty( $virtual_url ) ) {
			$virtual_url = $event->permalink;
		}

		return $virtual_url;
	}
}
