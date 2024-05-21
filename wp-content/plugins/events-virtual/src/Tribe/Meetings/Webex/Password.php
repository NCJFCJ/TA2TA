<?php
/**
 * Manages the Webex Meeting Password
 *
 * @since 1.9.0
 *
 * @package Tribe\Events\Virtual\Meetings\Webex
 */

namespace Tribe\Events\Virtual\Meetings\Webex;

use Tribe\Events\Virtual\Event_Meta as Virtual_Event_Meta;
use Tribe__Events__Main as Events_Plugin;
use WP_Post;

/**
 * Class Password
 *
 * @since 1.9.0
 *
 * @package Tribe\Events\Virtual\Meetings\Webex
 */
class Password {

	/**
	 * The Account API instance.
	 *
	 * @since 1.9.0
	 *
	 * @var Api
	 */
	public $api;

	/**
	 * Password constructor.
	 *
	 * @since 1.9.0
	 *
	 * @param Api $api An instance of the Webex API handler.
	 */
	public function __construct( Api $api ) {
		$this->api = $api;
	}

	/**
	 * Update the Webex Password Related Meta Fields
	 *
	 * @since 1.9.0
	 *
	 * @param \WP_Post $event The event post object.
	 *
	 * @return bool|void Whether the update completed.
	 */
	public function update_password( $event ) {

		$event = tribe_get_event( $event->ID );

		if ( ! $event instanceof \WP_Post ) {
			return;
		}

		if ( empty( $event->virtual ) ) {
			return;
		}

		if ( empty( $event->webex_join_url ) ) {
			return;
		}

		$this->api->load_account();
		if ( empty( $this->api->is_ready() ) ) {
			return;
		}

		$meeting = $this->api->fetch_meeting_data( $event->webex_join_url );

		if ( empty( $meeting['password'] ) ) {
			return;
		}

		$prefix = Virtual_Event_Meta::$prefix;
		update_post_meta( $event->ID, $prefix . 'webex_password', esc_html( $meeting['password'] ) );

		return true;
	}

	/**
	 * Check Webex Meeting in the admin.
	 *
	 * @since 1.9.0
	 *
	 * @param WP_Post $event The event post object.
	 *
	 * @return bool|void Whether the update completed.
	 */
	public function check_admin_webex_meeting( $event ) {
		if ( ! $event instanceof WP_Post ) {
			// We should only act on event posts, else bail.
			return;
		}

		/** @var \Tribe__Cache $cache */
		$cache    = tribe( 'cache' );
		$transient_name = $event->ID . '_webex_pw__admin_last_check';

		$last_check = (string) $cache->get_transient( $transient_name );
		if ( $last_check ) {
			return;
		}

		$cache->set_transient( $transient_name, true, MINUTE_IN_SECONDS * 10 );

		return $this->update_password( $event );
	}

	/**
	 * Check Webex Meeting on Front End..
	 *
	 * @since 1.9.0
	 *
	 * @return bool|void Whether the update completed.
	 */
	public function check_webex_meeting() {
		if ( ! is_singular( Events_Plugin::POSTTYPE ) ) {
			return;
		}

		global $post;

		/** @var \Tribe__Cache $cache */
		$cache    = tribe( 'cache' );
		$transient_name = $post->ID . '_webex_pw_last_check';

		$last_check = (string) get_transient( $transient_name );
		if ( $last_check ) {
			return;
		}

		$cache->set_transient( $transient_name, true, HOUR_IN_SECONDS );

		return $this->update_password( $post );
	}
}
