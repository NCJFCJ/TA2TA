<?php
/**
 * Manages the Google Meet Phone Numbers.
 *
 * @since   1.11.0
 *
 * @package Tribe\Events\Virtual\Meetings\Google
 */

namespace Tribe\Events\Virtual\Meetings\Google;

use Tribe\Events\Virtual\Event_Meta as Virtual_Event_Meta;

/**
 * Class Phone_Number
 *
 * @since   1.11.0
 *
 * @package Tribe\Events\Virtual\Meetings\Google
 */
class Phone_Number {

	/**
	 * Get the Google Meet
	 *
	 * @since 1.11.0
	 *
	 * @param \WP_Post $event    The event post object, as decorated by the `tribe_get_event` function.
	 * @param bool     $is_email Optional. Whether to skip the filters and return the full join url with password.
	 *                           Default false.
	 *
	 * @return array<string|string> An array of Google Meet numbers with or without the pin.
	 */
	public function get_google_meet_number( \WP_Post $event, $is_email = false ) {
		/*
		 * If the ID is empty, return a blank string to prevent remove_query_arg() using current URL.
		 *
		 * This happens because get_post_meta() will always return false when the ID is empty.
		 */
		if ( empty( $event->ID ) ) {
			return [];
		}

		$prefix       = Virtual_Event_Meta::$prefix;
		$entry_points = array_filter( (array) get_post_meta( $event->ID, $prefix . 'google_entry_points', true ) );

		$compact_phone_number = static function ( $phone_number ) {
			return trim( str_replace( ' ', '', $phone_number ) );
		};

		$compact_url = static function ( $uri ) {
			return implode( '', array_intersect_key( wp_parse_url( $uri ), array_flip( [ 'host', 'path' ] ) ) );
		};

		/**
		 * Filters the Google Meet phone pin to show with the pin for all site visitors.
		 *
		 * @since 1.11.0
		 *
		 * @param boolean  $include_pin Whether a user is logged in.
		 * @param \WP_Post $event       The event post object, as decorated by the `tribe_get_event` function.
		 */
		$include_pin = apply_filters( 'tec_events_virtual_meetings_google_meet_include_pin', is_user_logged_in(), $event );

		// If is_email always return the pin.
		if ( $is_email ) {
			$include_pin = true;
		}

		$global_dial_in_numbers = [];

		foreach ( $entry_points as $access ) {
			if ( 'phone' === $access['entryPointType'] ) {
				$global_dial_in_numbers[ $access['label'] ] = [
					'country' => "({$access['regionCode']})",
					'compact' => $compact_phone_number( $access['label'] ),
					'uri'     => $access['uri'],
					'pin'     => $include_pin ? $access['pin'] : '',
					'visual'  => $access['label'],
				];
			} elseif ( 'more' === $access['entryPointType'] && $include_pin ) {
				$global_dial_in_numbers[ $compact_url( $access['uri'] ) ] = [
					'country' => '',
					'compact' => $compact_url( $access['uri'] ),
					'uri'     => $access['uri'],
					'pin'     => $include_pin ? $access['pin'] : '',
					'visual'  => $compact_url( $access['uri'] ),
				];
			}
		}

		return $global_dial_in_numbers;
	}
}
