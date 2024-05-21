<?php
/**
 * Handles the post meta related to Microsoft Meet.
 *
 * @since 1.13.0
 *
 * @package Tribe\Events\Virtual\Meetings\Microsoft
 */

namespace Tribe\Events\Virtual\Meetings\Microsoft;

use Tribe\Events\Virtual\Integrations\Abstract_Event_Meta;
use WP_Post;

/**
 * Class Event_Meta
 *
 * @since 1.13.0
 *
 * @package Tribe\Events\Virtual\Meetings\Microsoft
 */
class Event_Meta extends Abstract_Event_Meta {

	/**
	 * {@inheritDoc}
	 */
	public static $key_source_id = 'microsoft';

	/**
	 * {@inheritDoc}
	 */
	protected static $create_actions = [
		'ev_microsoft_meetings_create',
	];

	/**
	 * {@inheritDoc}
	 */
	protected static function get_meeting_id( WP_Post $event ) {
		return $event->microsoft_meeting_id;
	}

	/**
	 * {@inheritDoc}
	 */
	protected static function get_meeting_join_url( WP_Post $event ) {
		return $event->microsoft_join_url;
	}

	/**
	 * {@inheritDoc}
	 */
	protected static function get_meeting_data_for_rest_api( WP_Post $event ) {
		return [
			'id'         => $event->microsoft_meeting_id,
			'url'        => $event->microsoft_join_url,
			'host_email' => $event->microsoft_host_email,
			'type'       => $event->microsoft_provider,
		];
	}

	/**
	 * {@inheritDoc}
	 */
	protected static function get_api_properties( WP_Post $event, $prefix, $is_new_event ) {
		$event->microsoft_provider              = $is_new_event ? '' : get_post_meta( $event->ID, $prefix . 'microsoft_provider', true );
		$event->microsoft_meeting_id            = $is_new_event ? '' : get_post_meta( $event->ID, $prefix . 'microsoft_meeting_id', true );
		$event->microsoft_conference_id         = $is_new_event ? '' : get_post_meta( $event->ID, $prefix . 'microsoft_conference_id', true );
		$event->microsoft_join_url              = $is_new_event ? '' : get_post_meta( $event->ID, $prefix . 'microsoft_join_url', true );
		$event->virtual_meeting_display_details = $is_new_event ? '' : get_post_meta( $event->ID, $prefix . 'microsoft_display_details', true );
		$event->microsoft_host_email            = $is_new_event ? '' : get_post_meta( $event->ID, $prefix . 'microsoft_host_email', true );

		return $event;
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
			'microsoft_provider'        => $event->microsoft_provider,
			'microsoft_meeting_id'      => $event->microsoft_meeting_id,
			'microsoft_conference_id'   => $event->microsoft_conference_id,
			'microsoft_join_url'        => $event->microsoft_join_url,
			'microsoft_display_details' => $event->virtual_meeting_display_details,
			'microsoft_host_email'      => $event->microsoft_host_email,
		];

		return $next_event;
	}
}
