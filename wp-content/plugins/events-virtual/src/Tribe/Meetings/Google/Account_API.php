<?php
/**
 * Manages all the Account Connections to Google.
 *
 * @since 1.11.0
 *
 * @package Tribe\Events\Virtual\Meetings\Google
 */

namespace Tribe\Events\Virtual\Meetings\Google;

use Tribe\Events\Virtual\Integrations\Abstract_Account_Api;

/**
 * Class Account_API
 *
 * @since 1.11.0
 *
 * @package Tribe\Events\Virtual\Meetings\Google
 */
abstract class Account_API extends Abstract_Account_Api {

	/**
	 * {@inheritDoc}
	 */
	protected $all_account_key = 'tec_google_accounts';

	/**
	 * {@inheritDoc}
	 */
	protected $single_account_prefix = 'tec_google_account_';

	/**
	 * The name of the action used to generate the OAuth authentication URL.
	 *
	 * @since 1.11.0
	 * @deprecated 1.13.0 - Use Actions::$authorize_nonce_action.
	 *
	 * @var string
	 */
	public static $authorize_nonce_action = 'events-virtual-meetings-google-oauth-authorize';

	/**
	 * {@inheritDoc}
	 */
	protected $account_id_meta_field_name = '_tribe_events_google_account_id';

	/**
	 * {@inheritDoc}
	 * @deprecated 1.13.0 - Use Actions::$select_action.
	 */
	public static $select_action = 'events-virtual-google-account-setup';

	/**
	 * {@inheritDoc}
	 * @deprecated 1.13.0 - Use Actions::$status_action.
	 */
	public static $status_action = 'tec-events-virtual-meetings-google-settings-status';

	/**
	 * {@inheritDoc}
	 * @deprecated 1.13.0 - Use Actions::$delete_action.
	 */
	public static $delete_action = 'tec-events-virtual-meetings-google-settings-delete';

	/**
	 * {@inheritDoc}
	 */
	protected function revoke_account_by_id( $account_id ) {
		$revoked = false;

		// Load account and get a valid not expired token as only those can be revoked.
		$account_loaded = $this->load_account_by_id( $account_id );
		if ( empty( $account_loaded ) ) {
			return $revoked;
		}

		$revoke_url = Url::$revoke_url;
		if ( defined( 'TEC_VIRTUAL_EVENTS_GOOGLE_API_REVOKE_URL' ) ) {
			$revoke_url = TEC_VIRTUAL_EVENTS_GOOGLE_API_REVOKE_URL;
		}

		$this->post(
			$revoke_url,
			[
				'headers' => [
					'Authorization' => $this->get_token_authorization_header(),
				],
				'body'    => [
					'token' => $this->access_token,
				],
			],
			Api::OAUTH_POST_RESPONSE_CODE
		)->then(
			function ( array $response ) use ( &$revoked ) {
				$body     = json_decode( wp_remote_retrieve_body( $response ), true );
				$body_set = $this->has_proper_response_body( $body, [ 'status' ] );
				if (
					! (
						$body_set
						&& 'success' === $body['status']
					)
				) {
					do_action( 'tribe_log', 'error', __CLASS__, [
						'action'   => __METHOD__,
						'message'  => 'Google Account Revoke Failed.',
						'response' => $body,
					] );

					return $revoked;
				}

				$revoked = true;

				return $revoked;
			}
		);

		return $revoked;
	}

	/**
	 * Handles an OAuth authorization return request.
	 *
	 * The method will `wp_die` if the nonce is not valid.
	 *
	 * @since 1.11.0
	 *
	 * @param string|null $nonce The nonce string to authorize the authorization request.
	 *
	 * @return bool Whether the authorization request is valid and was handled or not.
	 */
	public function handle_auth_request( $nonce = null ) {
		if ( ! wp_verify_nonce( $nonce, $this->actions::$authorize_nonce_action ) ) {
			wp_die( _x(
					'You are not authorized to do this.',
					'The message shown to a user providing a wrong Google API OAuth authorization nonce.',
					'events-virtual'
				)
			);
		}
		$handled = false;

		// This is response from our OAuth proxy service.
		$service_response_body = tribe_get_request_var( 'response_body', false );
		if ( $service_response_body ) {
			$this->save_account( [ 'body' => base64_decode( $service_response_body ) ] );

			$handled = true;
		}

		wp_safe_redirect( Settings::admin_url() );

		return $handled;
	}

	/**
	 * {@inheritDoc}
	 */
	public function save_account( array $response ) {
		if ( ! $this->has_proper_credentials( $response, false ) ) {
			do_action( 'tribe_log', 'error', __CLASS__, [
				'action'  => __METHOD__,
				'code'    => wp_remote_retrieve_response_code( $response ),
				'message' => 'Response body missing or malformed',
			] );

			return false;
		}

		// Set the access token here as we have to call fetch_user immediately, to get the user information.
		$credentials   = json_decode( wp_remote_retrieve_body( $response ), true );
		$access_token  = $credentials['access_token'];
		$refresh_token = isset( $credentials['refresh_token'] ) ? $credentials['refresh_token'] : '';
		$expiration    = $this->get_expiration_time_stamp( $credentials['expires_in'] );

		// Get the user who authorized the account.
		$user_fields = $this->fetch_user( 'me', false, $access_token );

		// The API can return as sub or id for the user id.
		$id = ! empty( $user_fields['id'] ) ? $user_fields['id'] : '';
		$id = empty( $id ) && ! empty( $user_fields['sub'] ) ? $user_fields['sub'] : '';
		if ( empty( $id ) ) {
			return false;
		}

		$user = [
			'id'     => $id,
			'name'   => $user_fields['name'] . ' (' . $user_fields['email'] .')',
			'email'  => $user_fields['email'],
			'domain' => isset( $user_fields['hd'] ) ? $user_fields['hd'] : '',
		];

		$account_data     = $this->prepare_account_data( $user, $access_token, $refresh_token, $expiration, [], true );
		$existing_account = $this->get_account_by_id( $account_data['id'] );
		$this->set_account_by_id( $account_data );

		$message = $existing_account ?
			sprintf(
				/* Translators: %1$s: the name of the account that has been refreshed from Google . */
				_x(
					'Google connection refreshed for %1$s',
					'The refresh message for a Google account.',
					'events-virtual'
				),
				$account_data['name']
			)
			: sprintf(
					/* Translators: %1$s: the name of the account that has been added from Google . */
					_x(
						'Google Account added for %1$s',
						'The Google account added message.',
						'events-virtual'
					),
					$account_data['name']
			);

		/** @var \Tribe__Cache $cache */
		$cache = tribe( 'cache' );
		$cache->set_transient( Settings::$option_prefix . 'account_message', $message, MINUTE_IN_SECONDS );

		return $access_token;
	}

	/**
	 * {@inheritDoc}
	 */
	public function save_access_and_expiration( $id, array $response ) {
		if ( ! $this->has_proper_credentials( $response, false ) ) {
			do_action( 'tribe_log', 'error', __CLASS__, [
				'action'  => __METHOD__,
				'code'    => wp_remote_retrieve_response_code( $response ),
				'message' => 'Response body missing or malformed when trying to refresh the access_token.',
			] );

			return false;
		}

		$credentials   = json_decode( wp_remote_retrieve_body( $response ), true );
		$access_token  = $credentials['access_token'];
		$refresh_token = isset( $credentials['refresh_token'] ) ? $credentials['refresh_token'] : '';

		/**
		 * Google API does not return a new refresh token unless it is the initial authorization.
		 * Check here for an existing refresh token and add that if the sent refresh_token is empty.
		 */
		$existing_account = $this->get_account_by_id( $id );
		if ( ! empty( $existing_account ) && empty( $refresh_token ) ) {
			$refresh_token = $existing_account['refresh_token'];
		}

		$expiration = $this->get_expiration_time_stamp( $credentials['expires_in'] );

		$this->set_account_access_by_id( $id, $access_token, $refresh_token, $expiration );

		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function prepare_account_data( $user, $access_token, $refresh_token, $expiration, $settings, $status ) {
		/**
		 * Google API does not return a new refresh token unless it is the initial authorization.
		 * Check here for an existing refresh token and add that if the sent refresh_token is empty.
		 */
		$existing_account = $this->get_account_by_id( $user['id'] );
		if ( ! empty( $existing_account ) && empty( $refresh_token ) ) {
			$refresh_token = $existing_account['refresh_token'];
		}

		return [
			'id'            => $user['id'],
			'name'          => $user['name'],
			'email'         => $user['email'],
			'domain'        => $user['domain'],
			'access_token'  => $access_token,
			'refresh_token' => $refresh_token,
			'expiration'    => $expiration,
			'status'        => $status,
		];
	}
}
