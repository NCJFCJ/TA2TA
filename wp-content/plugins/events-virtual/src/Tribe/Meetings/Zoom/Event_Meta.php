<?php
/**
 * Handles the post meta related to Zoom Meetings.
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual\Meetings\Zoom
 */

namespace Tribe\Events\Virtual\Meetings\Zoom;

use Tribe\Events\Virtual\Encryption;
use Tribe\Events\Virtual\Event_Meta as Virtual_Event_Meta;
use Tribe\Events\Virtual\Integrations\Abstract_Event_Meta;
use Tribe__Utils__Array as Arr;
use WP_Post;

/**
 * Class Event_Meta
 *
 * @since   1.0.0
 * @since 1.11.0 - Change to use an abstract class for shared methods.
 *
 * @package Tribe\Events\Virtual\Meetings\Zoom
 */
class Event_Meta extends Abstract_Event_Meta {

	/**
	 * {@inheritDoc}
	 */
	public static $key_source_id = 'zoom';

	/**
	 * {@inheritDoc}
	 */
	public static $encrypted_fields = [
		'meeting_data'      => true,
		'host_email'        => false,
		'alternative_hosts' => false,
	];

	/**
	 * {@inheritDoc}
	 */
	protected static $create_actions = [
		'ev_zoom_meetings_create',
		'ev_zoom_webinars_create',
	];

	/**
	 * {@inheritDoc}
	 */
	protected static function get_meeting_id( WP_Post $event ) {
		return $event->zoom_meeting_id;
	}

	/**
	 * {@inheritDoc}
	 */
	protected static function get_meeting_join_url( WP_Post $event ) {
		return $event->zoom_join_url;
	}

	/**
	 * {@inheritDoc}
	 */
	protected static function get_meeting_data_for_rest_api( WP_Post $event ) {
		return [
			'id'           => $event->zoom_meeting_id,
			'url'          => $event->zoom_join_url,
			'numbers'      => $event->zoom_global_dial_in_numbers,
			'password'     => get_post_meta( $event->ID, Virtual_Event_Meta::$prefix . 'zoom_password', true ),
			'type'         => $event->zoom_meeting_type,
			'instructions' => $event->zoom_join_instructions,
		];
	}

	/**
	 * {@inheritDoc}
	 */
	protected static function get_api_properties( WP_Post $event, $prefix, $is_new_event ) {
		$event->zoom_meeting_type               = $is_new_event ? '' : get_post_meta( $event->ID, $prefix . 'zoom_meeting_type', true );
		$event->zoom_meeting_id                 = $is_new_event ? '' : get_post_meta( $event->ID, $prefix . 'zoom_meeting_id', true );
		$event->zoom_join_url                   = $is_new_event ? '' : tribe( Password::class )->get_zoom_meeting_link( $event );
		$event->zoom_join_instructions          = $is_new_event ? '' : get_post_meta( $event->ID, $prefix . 'zoom_join_instructions', true );
		$event->virtual_meeting_display_details = $is_new_event ? '' : get_post_meta( $event->ID, $prefix . 'zoom_display_details', true );
		$event->zoom_host_email                 = $is_new_event ? '' : self::get_host_email( $event );
		$event->zoom_alternative_hosts          = $is_new_event ? '' : self::get_alt_host_emails( $event );

		$dial_in_numbers = $is_new_event ? [] : array_filter(
			(array) get_post_meta( $event->ID, $prefix . 'zoom_global_dial_in_numbers', true )
		);

		$compact_phone_number = static function ( $phone_number ) {
			return trim( str_replace( ' ', '', $phone_number ) );
		};

		$event->zoom_global_dial_in_number = count( $dial_in_numbers )
			? array_keys( $dial_in_numbers )[0]
			: '';

		$event->zoom_global_dial_in_numbers = [];
		foreach ( $dial_in_numbers as $phone_number => $country ) {
			$event->zoom_global_dial_in_numbers[] = [
				'country' => $country,
				'compact' => $compact_phone_number( $phone_number ),
				'visual'  => $phone_number,
			];
		}

		return $event;
	}

	/**
	 * Get the host email from the meta or saved Zoom data.
	 *
	 * @since 1.4.0
	 *
	 * @param WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
	 *
	 * @return string|null The found host email or null for the meeting.
	 */
	public static function get_host_email( WP_Post $event ) {
		$encryption = tribe( Encryption::class );
		$prefix     = Virtual_Event_Meta::$prefix;
		$host_email = $encryption->decrypt( get_post_meta( $event->ID, $prefix . 'zoom_host_email', true ) );

		if ( $host_email ) {
			return $host_email;
		}

		$all_zoom_details = $encryption->decrypt( get_post_meta( $event->ID, $prefix . 'zoom_meeting_data', true ) );

		return Arr::get( $all_zoom_details, 'host_email', null );
	}

	/**
	 * Get the alternative host emails from the meta or saved Zoom data.
	 *
	 * @since 1.4.0
	 *
	 * @param WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
	 *
	 * @return string|null The found host email or null for the meeting.
	 */
	public static function get_alt_host_emails( WP_Post $event ) {
		$encryption        = tribe( Encryption::class );
		$prefix            = Virtual_Event_Meta::$prefix;
		$alternative_hosts = $encryption->decrypt( get_post_meta( $event->ID, $prefix . 'zoom_alternative_hosts', true ) );

		if ( $alternative_hosts ) {
			return $alternative_hosts;
		}

		$all_zoom_details = $encryption->decrypt( get_post_meta( $event->ID, $prefix . 'zoom_meeting_data', true ) );
		$settings = Arr::get( $all_zoom_details, 'settings', [] );

		return Arr::get( $settings, 'alternative_hosts', '' );
	}

	/**
	 * Adds related properties to an Event Automator event details map.
	 *
	 * @since 1.13.5
	 *
	 * @param array<string|mixed> $next_event An array of event details.
	 * @param WP_Post             $event      An instance of the event WP_Post object.
	 *
	 * @return array<string|mixed> An array of event details.
	 */
	public static function add_event_automator_properties( array $next_event, WP_Post $event ) {
		if ( $event->virtual_video_source !== static::$key_source_id ) {
			return $next_event;
		}

		$next_event['virtual_url']              = $event->virtual_meeting_url;
		$next_event['virtual_provider_details'] = [
			'zoom_meeting_type'            => $event->zoom_meeting_type,
			'zoom_meeting_id'              => $event->zoom_meeting_id,
			'zoom_join_url'                => $event->zoom_join_url,
			'zoom_join_instructions'       => $event->zoom_join_instructions,
			'zoom_meeting_display_details' => $event->virtual_meeting_display_details,
			'zoom_host_email'              => $event->zoom_host_email,
			'zoom_alternative_hosts'       => $event->zoom_alternative_hosts,
			'zoom_global_dial_in_numbers'  => $event->zoom_global_dial_in_numbers,
		];

		return $next_event;
	}
}
