<?php
/**
 * Manages the Users API Integrations.
 *
 * @since   1.13.0
 *
 * @package Tribe\Events\Virtual\Integrations
 */

namespace Tribe\Events\Virtual\Integrations;

use Tribe\Events\Virtual\Traits\With_AJAX;
use Tribe__Utils__Array as Arr;

/**
 * Class Abstract_Users
 *
 * @since   1.13.0
 *
 * @package Tribe\Events\Virtual\Integrations
 */
abstract class Abstract_Users {
	use With_AJAX;

	/**
	 * The internal id of the API integration.
	 *
	 * @since 1.13.0
	 *
	 * @var string
	 */
	public static $api_id = '';

	/**
	 * An instance of the an API handler.
	 *
	 * @since 1.13.0
	 *
	 * @var Api
	 */
	protected $api;

	/**
	 * The template handler instance.
	 *
	 * @since 1.13.0
	 *
	 * @var Admin_Template
	 */
	public $admin_template;

	/**
	 * The current Actions handler instance.
	 *
	 * @since 1.13.0
	 *
	 * @var Abstract_Actions
	 */
	protected $actions;

	/**
	 * The current instance of the Encryption handler.
	 *
	 * @since 1.13.0
	 *
	 * @var \Tribe\Events\Virtual\Encryption
	 */
	protected $encryption;

	/**
	 * Get list of users from an API.
	 *
	 * @since 1.13.0
	 *
	 * @param null|string $account_id The account id to use to get the users with.
	 *
	 * @return array<string,mixed> An array of users from an API.
	 */
	public function get_users( $account_id = null ) {
		$api = $this->api;
		if ( $account_id ) {
			$api->load_account_by_id( $account_id );
		} else {
			$api->load_account();
		}

		if ( empty( $this->api->is_ready() ) ) {
			return [];
		}

		$api_id = static::$api_id;
		/** @var \Tribe__Cache $cache */
		$cache    = tribe( 'cache' );
		$cache_id = "events_virtual_meetings_{$api_id}_users_" . md5( $this->api->id );

		/**
		 * Filters the time in seconds until an API user cache expires.
		 *
		 * @since 1.13.0
		 *
		 * @param int     The time in seconds until the user cache expires, default 1 hour.
		 */
		$expiration = apply_filters( "tribe_events_virtual_meetings_{$api_id}_user_cache", HOUR_IN_SECONDS );
		$users      = $cache->get_transient( $cache_id );

		if ( ! empty( $users ) ) {
			if ( empty( $this->encryption ) ) {
				return $users;
			}

			return $this->encryption->decrypt( $users, true );
		}

		$available_hosts = $api->fetch_users();

		$cache_available_hosts = $available_hosts;
		if ( ! empty( $this->encryption ) ) {
			$cache_available_hosts = $this->encryption->encrypt( $available_hosts, true );
		}
		$cache->set_transient( $cache_id, $cache_available_hosts, $expiration );

		return $available_hosts;
	}

	/**
	 * Get the users from an API users response.
	 *
	 * @since 1.13.0
	 *
	 * @param array<string|mixed> $available_hosts The response from an API of available hosts.
	 *
	 * @return array<string|mixed> An array of users or an empty array if none available.
	 */
	abstract protected function get_users_array( $available_hosts );

	/**
	 * Get a user's information formatted for internal use.
	 *
	 * @since 1.13.0
	 *
	 * @param array<string|mixed> $user Information for a user from an API,
	 *
	 * @return array<string|mixed> An array of a user's information formatted for internal use.
	 */
	abstract protected function get_formatted_user_info( $user );

	/**
	 * Get list of hosts formatted for options dropdown.
	 *
	 * @since 1.13.0
	 *
	 * @param null|string $account_id The account id to use to get the users with.
	 *
	 * @return array<string,mixed>  An array of Zoom Users to use as the host
	 */
	public function get_formatted_hosts_list( $account_id = null ) {
		$available_hosts = $this->get_users( $account_id );
		$active_users    = $this->get_users_array( $available_hosts );
		if ( empty( $active_users ) ) {
			return [];
		}

		$hosts = [];
		foreach ( $active_users as $user ) {
			$user_info = $this->get_formatted_user_info( $user );

			if (
				empty( $user_info['name'] ) ||
				empty( $user_info['id'] ) ||
				empty( $user_info['value'] ) ||
				empty( $user_info['email'] )
			) {
				continue;
			}

			$hosts[] = [
				'text'             => (string) trim( $user_info['name'] ),
				'sort'             => (string) trim( $user_info['last_name'] ),
				'id'               => (string) $user_info['id'],
				'value'            => (string) $user_info['value'],
				'selected'         => $account_id === $user_info['value'] ? true : false,
				'email'            => (string) $user_info['email'],
				// Zoom Alternative Host.
				'alternative_host' => isset( $user_info['type'] ) && $user_info['type'] > 1 ? true : false,
			];
		}

		// Sort the hosts array by text(email).
		$sort_arr = array_column( $hosts, 'sort' );
		array_multisort( $sort_arr, SORT_ASC, $hosts );

		return $hosts;
	}
}
