<?php
/**
 * The Event Status Labels.
 *
 * @since   1.7.3
 * @package Tribe\Events\Virtual\Event_Status
 */

namespace Tribe\Events\Virtual\Event_Status;

/**
 * Class Statuses
 *
 * @since   1.7.3
 *
 * @package Tribe\Events\Virtual\Event_Status
 */
class Status_Labels {

	/**
	 * Add the event statuses to select for an event.
	 *
	 * @since 1.7.3
	 *
	 * @param array<string|mixed> $statuses       The event status options for an event.
	 * @param string              $current_status The current event status for the event or empty string if none.
	 *
	 * @return array<string|mixed> The event status options for an event.
	 */
	public function filter_event_statuses( $statuses, $current_status ) {
		$default_statuses = [
			[
				'text'     => $this->get_moved_online_label(),
				'id'       => 'moved-online',
				'value'    => 'moved-online',
				'selected' => 'moved-online' === $current_status ? true : false,
			]
		];

		$statuses = array_merge( $statuses, $default_statuses );

		return $statuses;
	}

	/**
	 * Get the moved online status label.
	 *
	 * @since 1.7.3
	 *
	 * @return string The label for the moved online status.
	 */
	public function get_moved_online_label() {

		/**
		 * Filter the moved online label for event status.
		 *
		 * @since 1.7.3
		 *
		 * @param string The default translated label for the moved online status.
		 */
		return apply_filters( 'tec_events_virtual_event_status_moved_online_label', _x( 'Moved Online', 'Moved Online event status label', 'events-virtual' ) );
	}
}
