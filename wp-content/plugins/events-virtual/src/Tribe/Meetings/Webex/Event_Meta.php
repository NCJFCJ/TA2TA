<?php
/**
 * Handles the post meta related to Webex Meetings.
 *
 * @since 1.9.0
 *
 * @package Tribe\Events\Virtual\Meetings\Webex
 */

namespace Tribe\Events\Virtual\Meetings\Webex;

use Tribe\Events\Virtual\Event_Meta as Virtual_Event_Meta;
use Tribe\Events\Virtual\Integrations\Abstract_Event_Meta;
use Tribe__Utils__Array as Arr;
use WP_Post;

/**
 * Class Event_Meta
 *
 * @since 1.9.0
 * @since 1.11.0 - Change to use an abstract class for shared methods.
 *
 * @package Tribe\Events\Virtual\Meetings\Webex
 */
class Event_Meta extends Abstract_Event_Meta {

	/**
	 * {@inheritDoc}
	 */
	public static $key_source_id = 'webex';

	/**
	 * {@inheritDoc}
	 */
	protected static $create_actions = [
		'ev_webex_meetings_create',
	];

	/**
	 * {@inheritDoc}
	 */
	protected static function get_meeting_id( WP_Post $event ) {
		return $event->webex_meeting_id;
	}


	/**
	 * {@inheritDoc}
	 */
	protected static function get_meeting_join_url( WP_Post $event ) {
		return $event->webex_join_url;
	}

	/**
	 * {@inheritDoc}
	 */
	protected static function get_meeting_data_for_rest_api( WP_Post $event ) {
		return [
			'id'         => $event->webex_meeting_id,
			'url'        => $event->webex_join_url,
			'host_email' => $event->webex_host_email,
			'password'   => self::get_password( $event ),
			'type'       => $event->webex_meeting_type,
		];
	}

	/**
	 * {@inheritDoc}
	 */
	protected static function get_api_properties( WP_Post $event, $prefix, $is_new_event ) {
		$event->webex_meeting_type              = $is_new_event ? '' : get_post_meta( $event->ID, $prefix . 'webex_meeting_type', true );
		$event->webex_meeting_id                = $is_new_event ? '' : get_post_meta( $event->ID, $prefix . 'webex_meeting_id', true );
		$event->webex_join_url                  = $is_new_event ? '' : get_post_meta( $event->ID, $prefix . 'webex_join_url', true );
		$event->virtual_meeting_display_details = $is_new_event ? '' : get_post_meta( $event->ID, $prefix . 'webex_display_details', true );
		$event->webex_host_email                = $is_new_event ? '' : get_post_meta( $event->ID, $prefix . 'webex_host_email', true );
		$event->webex_password                  = self::get_password( $event );

		return $event;
	}

	/**
	 * Determines if the password should be shown
	 * based on the `virtual_show_embed_to` setting of the event.
	 *
	 * @since 1.9.0
	 *
	 * @param WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
	 *
	 * @return boolean
	 */
	public static function should_show_password( $event ) {
		if ( ! $event instanceof WP_Post ) {
			return false;
		}

		$show = ! in_array( Virtual_Event_Meta::$value_show_embed_to_logged_in, $event->virtual_show_embed_to, true ) || is_user_logged_in();

		/**
		 * Filters whether the virtual content should show or not.
		 *
		 * @since 1.0.4
		 *
		 * @param boolean $show  If the virtual content should show or not.
		 * @param WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
		 */
		return apply_filters( 'tec_events_virtual_show_virtual_content', $show, $event );
	}

	/**
	 * Get the Webex password if it should be shown.
	 *
	 * @since 1.9.0
	 *
	 * @param WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
	 *
	 * @return string|null The password or null if it should not be shown.
	 */
	public static function get_password( WP_Post $event ) {
		$should_show = static::should_show_password( $event );

		/**
		 * Filters whether the Webex password should be shown.
		 *
		 * @since 1.9.0
		 *
		 * @param boolean $should_show Whether the password should be shown.
		 * @param WP_Post $event       The event post object, as decorated by the `tribe_get_event` function.
		 */
		$should_show = apply_filters( 'tec_events_virtual_meetings_webex_meeting_show_password', $should_show, $event );
		if ( ! $should_show ) {
			return null;
		}

		$prefix   = Virtual_Event_Meta::$prefix;
		$password = get_post_meta( $event->ID, $prefix . 'webex_password', true );

		if ( $password ) {
			return $password;
		}

		$all_webex_details = get_post_meta( $event->ID, $prefix . 'webex_meeting_data', true );

		return Arr::get( $all_webex_details, 'password', null );
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
			'webex_meeting_type'            => $event->webex_meeting_type,
			'webex_meeting_id'              => $event->webex_meeting_id,
			'webex_join_url'                => $event->webex_join_url,
			'webex_meeting_display_details' => $event->virtual_meeting_display_details,
			'webex_host_email'              => $event->webex_host_email,
			'webex_password'                => $event->webex_password,
		];

		return $next_event;
	}
}
