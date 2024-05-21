<?php
/**
 * Manages the API URLs for the plugin.
 *
 * @since   1.13.0
 *
 * @package Tribe\Events\Virtual\Integrations
 */

namespace Tribe\Events\Virtual\Integrations;

use Tribe\Events\Virtual\Context\Context_Provider;
use Tribe\Events\Virtual\Plugin;

/**
 * Class Abstract_Url
 *
 * @since   1.13.0
 *
 * @package Tribe\Events\Virtual\Integrations
 */
abstract class Abstract_Url {

	/**
	 * The internal id of the API integration.
	 *
	 * @since 1.13.0
	 *
	 * @var string
	 */
	public static $api_id = '';

	/**
	 * The base URL that should be used to authorize an API's App.
	 *
	 * @since 1.13.0
	 *
	 * @var string
	 */
	public static $authorize_url = '';

	/**
	 * The base URL to request an access token from an API.
	 *
	 * @since 1.13.0
	 *
	 * @var string
	 *
	 */
	public static $refresh_url = '';

	/**
	 * The base URL to revoke an authorized account.
	 *
	 * @since 1.13.0
	 *
	 * @var string
	 */
	public static $revoke_url = '';

	/**
	 * The current API handler instance.
	 *
	 * @since 1.13.0
	 *
	 * @var Request_Api
	 */
	protected $api;

	/**
	 * The current Actions handler instance.
	 *
	 * @since 1.13.0
	 *
	 * @var Abstract_Actions
	 */
	protected $actions;

	/**
	 * Returns the URL to authorize the use of an API.
	 *
	 * @since 1.13.0
	 * @since 1.14.0 - Use a constant for 'state' query variable.
	 *
	 * @return string The request URL.
	 */
	public function to_authorize() {
		$authorize_url = static::$authorize_url;
		$constant_id   = strtoupper( static::$api_id );

		if ( defined( "TEC_VIRTUAL_EVENTS_{$constant_id}_API_AUTHORIZE_URL" ) ) {
			$authorize_url = constant( "TEC_VIRTUAL_EVENTS_{$constant_id}_API_AUTHORIZE_URL" );
		}

		$real_url = add_query_arg( [
			'redirect_uri'                         => esc_url( admin_url() ),
			Context_Provider::AUTH_STATE_QUERY_VAR => wp_create_nonce( $this->actions::$authorize_nonce_action ),
		], $authorize_url );

		return $real_url;
	}

	/**
	 * Returns the URL to refresh a token.
	 *
	 * @since 1.13.0
	 *
	 * @return string The request URL.
	 */
	public static function to_refresh() {
		$refresh_url = static::$refresh_url;
		$constant_id = strtoupper( static::$api_id );

		if ( defined( "TEC_VIRTUAL_EVENTS_{$constant_id}_API_REFRESH_URL" ) ) {
			$refresh_url = constant( "TEC_VIRTUAL_EVENTS_{$constant_id}_API_REFRESH_URL" );
		}

		return $refresh_url;
	}

	/**
	 * Get the admin ajax url with parameters to enable an API action.
	 *
	 * @since 1.13.0
	 *
	 * @param string               $action         The name of the action to add to the url.
	 * @param string               $nonce          The nonce to verify for the action.
	 * @param array<string|string> $additional_arg An array of arugments to add to the query string of the admin ajax url.
	 *
	 * @return string
	 */
	public function get_admin_ajax_url_with_parameters( string $action, string $nonce, array $additional_arg ) {
		$args = [
			'action'              => $action,
			Plugin::$request_slug => $nonce,
			'_ajax_nonce'         => $nonce,
		];

		$query_args = array_merge( $args, $additional_arg );

		return add_query_arg( $query_args, admin_url( 'admin-ajax.php' ) );
	}

	/**
	 * Returns the URL that should be used to change an account status to enabled or disabled in the settings.
	 *
	 * @since 1.13.0
	 *
	 * @param string $account_id An API Account ID to change the status.
	 *
	 * @return string The URL to change an account status.
	 */
	public function to_change_account_status_link( $account_id ) {
		$api_id = static::$api_id;
		$nonce  = wp_create_nonce( $this->actions::$status_action );

		return $this->get_admin_ajax_url_with_parameters(
			"ev_{$api_id}_settings_account_status",
			$nonce,
			[
				'account_id' => $account_id
			]
		);
	}

	/**
	 * Returns the URL that should be used to delete an API account in the settings.
	 *
	 * @since 1.13.0
	 *
	 * @param string $account_id An API Account ID to change the status.
	 *
	 * @return string The URL to delete an account.
	 */
	public function to_delete_account_link( $account_id ) {
		$api_id = static::$api_id;
		$nonce  = wp_create_nonce( $this->actions::$delete_action );

		return $this->get_admin_ajax_url_with_parameters(
			"ev_{$api_id}_settings_delete_account",
			$nonce,
			[
				'account_id' => $account_id
			]
		);
	}

	/**
	 * Returns the URL that should be used to select an account to setup for an API.
	 *
	 * @since 1.13.0
	 *
	 * @param \WP_Post|null $post A post object of the event.
	 *
	 * @return string The URL to select an API account.
	 */
	public function to_select_account_link( \WP_Post $post ) {
		$api_id = static::$api_id;
		$nonce  = wp_create_nonce( $this->actions::$select_action );

		return $this->get_admin_ajax_url_with_parameters(
			"ev_{$api_id}_account_select",
			$nonce,
			[
				'post_id' => $post->ID
			]
		);
	}

	/**
	 * Returns the URL that should be used to generate an API meeting link.
	 *
	 * @since 1.13.0
	 *
	 * @param \WP_Post|null $post A post object to generate the meeting for.
	 *
	 * @return string The URL to generate an API integration.
	 */
	public function to_generate_meeting_link( \WP_Post $post ) {
		$api_id = static::$api_id;
		$nonce  = wp_create_nonce( $this->actions::$create_action );

		return $this->get_admin_ajax_url_with_parameters(
			"ev_{$api_id}_meetings_create",
			$nonce,
			[
				'post_id' => $post->ID
			]
		);
	}

	/**
	 * Returns the URL that should be used to remove an event API integration.
	 *
	 * @since 1.13.0
	 *
	 * @param \WP_Post $post A post object to remove the meeting from.
	 *
	 * @return string The URL to remove an API integration.
	 */
	public function to_remove_meeting_link( \WP_Post $post ) {
		$api_id = static::$api_id;
		$nonce  = wp_create_nonce( $this->actions::$remove_action );

		return $this->get_admin_ajax_url_with_parameters(
			"ev_{$api_id}_meetings_remove",
			$nonce,
			[
				'post_id' => $post->ID
			]
		);
	}
}
