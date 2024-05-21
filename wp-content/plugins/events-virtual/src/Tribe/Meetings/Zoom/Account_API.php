<?php
/**
 * Manages all the Account Connection to Zoom
 *
 * @since   1.5.0
 *
 * @package Tribe\Events\Virtual\Meetings\Zoom
 */

namespace Tribe\Events\Virtual\Meetings\Zoom;

use Tribe\Events\Virtual\Encryption;
use Tribe\Events\Virtual\Event_Meta as Virtual_Events_Meta;
use Tribe\Events\Virtual\Integrations\Abstract_Account_Api;
use Tribe\Events\Virtual\Meetings\Zoom\Event_Meta as Zoom_Meta;
use Tribe__Utils__Array as Arr;

/**
 * Class Account_API
 *
 * @since   1.5.0
 * @since   1.9.0 - Use Base_Account_Api to enable elements to be shared across API classes.
 *
 * @package Tribe\Events\Virtual\Meetings\Zoom
 */
abstract class Account_API extends Abstract_Account_Api {

	/**
	 * The name of the action used to generate the OAuth authentication URL.
	 *
	 * @since 1.13.0
	 * @deprecated 1.13.0 - Use Actions::$authorize_nonce_action.
	 *
	 * @var string
	 */
	public static $authorize_nonce_action = 'events-virtual-meetings-zoom-oauth-authorize';

	/**
	 * Whether a Zoom account supports webinars.
	 *
	 * @since 1.5.0
	 *
	 * @var boolean
	 */
	protected $supports_webinars = false;

	/**
	 * {@inheritDoc}
	 */
	protected $all_account_key = 'tec_zoom_accounts';

	/**
	 * {@inheritDoc}
	 */
	protected $single_account_prefix = 'tec_zoom_account_';

	/**
	 * An array of fields to encrypt, using names from Zoom API.
	 *
	 * @since 1.5.0
	 *
	 * @var array<string|boolean> An array of field names and whether the field is an array.
	 */
	protected $encrypted_fields = [
		'name'          => false,
		'email'         => false,
		'access_token'  => false,
		'refresh_token' => false,
	];

	/**
	 * {@inheritDoc}
	 */
	protected $account_id_meta_field_name = '_tribe_events_zoom_account_id';

	/**
	 * The Encryption handler.
	 *
	 * @since 1.9.0
	 *
	 * @var Encryption
	 */
	public $encryption;

	/**
	 * {@inheritDoc}
	 * @deprecated 1.13.0 - Use Actions::$select_action.
	 */
	public static $select_action = 'events-virtual-zoom-account-setup';

	/**
	 * {@inheritDoc}
	 * @deprecated 1.13.0 - Use Actions::$status_action.
	 */
	public static $status_action = 'events-virtual-meetings-zoom-settings-status';

	/**
	 * {@inheritDoc}
	 * @deprecated 1.13.0 - Use Actions::$delete_action.
	 */
	public static $delete_action = 'events-virtual-meetings-zoom-settings-delete';

	/**
	 * Handles an OAuth authorization return request.
	 *
	 * The method will `wp_die` if the nonce is not valid.
	 *
	 * @since 1.13.0
	 *
	 * @param string|null $nonce The nonce string to authorize the authorization request.
	 *
	 * @return boolean Whether the authorization request was handled.
	 */
	public function handle_auth_request( $nonce = null ) {
		if ( ! wp_verify_nonce( $nonce, $this->actions::$authorize_nonce_action ) ) {
			wp_die( _x(
					'You are not authorized to do this',
					'The message shown to a user providing a wrong Zoom API OAuth authorization nonce.',
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
	 * Get the listing of Zoom Accounts.
	 *
	 * @since 1.5.0
	 *
	 * @param boolean $all_data Whether to return all account data, default is only name and status.
	 *
	 * @return array<string|string> $list_of_accounts An array of all the Zoom accounts.
	 */
	public function get_list_of_accounts( $all_data = false ) {
		// Get list of accounts and decrypt the PII
		$list_of_accounts = get_option( $this->all_account_key, [] );
		foreach ( $list_of_accounts as $account_id => $account ) {
			if ( empty( $account['name'] ) ) {
				continue;
			}
			$list_of_accounts[ $account_id ]['name'] = $this->encryption->decrypt( $account['name'] );

			// If false (default ) skip getting all the account data.
			if ( empty( $all_data ) ) {
				continue;
			}
			$account_data = $this->get_account_by_id( $account_id );

			$list_of_accounts[ $account_id ] = $account_data;
		}

		return $list_of_accounts;
	}

	/**
	 * Get a Single Zoom Account by id.
	 *
	 * @since 1.5.0
	 *
	 * @param string $account_id The id of the single account.
	 *
	 * @return array<string|string> $account The Zoom account data.
	 */
	public function get_account_by_id( $account_id ) {
		// Get an account and decrypt the PII.
		$account = get_option( $this->single_account_prefix . $account_id, [] );
		foreach ( $account as $field_key => $value ) {
			if ( ! array_key_exists( $field_key, $this->encrypted_fields ) ) {
				continue;
			}

			$account[ $field_key ] = $this->encryption->decrypt( $value, $this->encrypted_fields[ $field_key ] );
		}

		return $account;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete_account_by_id( $account_id ) {
		$revoked = $this->revoke_account_by_id( $account_id );

		delete_option( $this->single_account_prefix . $account_id );

		$this->delete_from_list_of_accounts( $account_id );

		return $revoked;
	}

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

		$revoke_url = tribe( Url::class )::$revoke_url;
		if ( defined( 'TEC_VIRTUAL_EVENTS_ZOOM_API_REVOKE_URL' ) ) {
			$revoke_url = TEC_VIRTUAL_EVENTS_ZOOM_API_REVOKE_URL;
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
						'message'  => 'Zoom Account Revoke Failed.',
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
	 * Set an Account Access Data with the provided id.
	 *
	 * @since 1.5.0
	 *
	 * @param string $account_id    The id of the single account to save.
	 * @param string $access_token  The Zoom Account API access token.
	 * @param string $refresh_token The Zoom Account API refresh token.
	 * @param string $expiration    The expiration in seconds as provided by the server.
	 */
	public function set_account_access_by_id( $account_id, $access_token, $refresh_token, $expiration ) {
		$account_data                 = $this->get_account_by_id( $account_id );
		$account_data['access_token'] = $this->encryption->encrypt( $access_token );
		$account_data['refresh_token'] = $this->encryption->encrypt( $refresh_token );
		$account_data['expiration']   = $expiration;

		$this->set_account_by_id( $account_data );
	}

	/**
	 * {@inheritDoc}
	 */
	public function save_account( array $response ) {
		if ( ! $this->has_proper_credentials( $response ) ) {
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
		$refresh_token = $credentials['refresh_token'];
		$expiration    = $this->get_expiration_time_stamp( $credentials['expires_in'] );

		// Get the user who authorized the account.
		$user         = $this->fetch_user( 'me', false, $access_token);
		if ( empty( $user['id'] ) ) {
			return false;
		}

		$settings         = $this->fetch_user( $user['id'], true, $access_token );
		$account_data     = $this->prepare_account_data( $user, $access_token, $refresh_token, $expiration, $settings, true );
		$existing_account = $this->get_account_by_id( $account_data['id'] );
		$this->set_account_by_id( $account_data );

		$account_msg = $existing_account ?
			_x( 'Zoom connection refreshed for %1$s', 'The refresh message if the account exists.', 'events-virtual' )
			: _x( 'Zoom Account added for %1$s', 'The refresh message if the account exists.', 'events-virtual' );
		$message = sprintf(
			/* Translators: %1$s: the name of the account that has been added or refreshed from Zoom . */
			$account_msg,
			$this->encryption->decrypt( $account_data['name'] )
		);

		set_transient( Settings::$option_prefix . 'account_message', $message, MINUTE_IN_SECONDS );

		return $access_token;
	}

	/**
	 * {@inheritDoc}
	 */
	public function save_access_and_expiration( $id, array $response ) {
		if ( ! $this->has_proper_credentials( $response ) ) {
			do_action( 'tribe_log', 'error', __CLASS__, [
				'action'  => __METHOD__,
				'code'    => wp_remote_retrieve_response_code( $response ),
				'message' => 'Response body missing or malformed',
			] );

			return false;
		}

		$credentials   = json_decode( wp_remote_retrieve_body( $response ), true );
		$access_token  = $credentials['access_token'];
		$refresh_token = $credentials['refresh_token'];
		$expiration    = $this->get_expiration_time_stamp( $credentials['expires_in'] );

		$this->set_account_access_by_id( $id, $access_token, $refresh_token, $expiration );

		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function prepare_account_data( $user, $access_token, $refresh_token, $expiration, $settings, $status ) {
		return [
			'id'            => $user['id'],
			'name'          => $this->encryption->encrypt( $user['first_name'] . ' ' . $user['last_name'] ),
			'email'         => $this->encryption->encrypt( $user['email'] ),
			'access_token'  => $this->encryption->encrypt( $access_token ),
			'refresh_token' => $this->encryption->encrypt( $refresh_token ),
			'expiration'    => $expiration,
			'webinars'      => $this->get_webinars_support( $settings ),
			'status'        => $status,
		];
	}

	/**
	 * Checks whether the current Zoom account supports webinars.
	 *
	 * @since 1.5.0
	 *
	 * @return boolean Whether the current Zoom account supports webinars.
	 */
	public function supports_webinars() {
		return ! empty( $this->supports_webinars );
	}

	/**
	 * Get whether the account supports webinars.
	 *
	 * @since 1.8.0
	 *
	 * @param array<string|mixed> $settings The user settings from Zoom.
	 *
	 * @return boolean Whether the account supports webinars.
	 */
	public function get_webinars_support( $settings ) {
		if ( empty( $settings['feature'] ) ) {
			return false;
		}

		$webinar_values = [
			'webinar',
			'zoom_events',
		];

		/**
		 * Filters the values to look for when detecting if a user has webinar support.
		 *
		 * @since 1.8.0
		 *
		 * @param array<string|string> $webinar_values The default webinar values to detect.
		 */
		$webinar_values = (array) apply_filters( 'tec_events_virtual_zoom_webinar_support_values', $webinar_values );

		$supports = false;
		foreach ( $webinar_values as $webinar_value ) {
			if ( empty( $settings['feature'][ $webinar_value ] ) ) {
				continue;
			}

			$supports = true;
			break;
		}

		return $supports;
	}

	/**
	 * Get password requirements for a user.
	 *
	 * @since 1.11.0
	 *
	 * @param array<string|mixed> $settings The user settings from Zoom.
	 *
	 * @return array<string|mixed> The password requirements for a user.
	 */
	public function get_password_requirements( $settings ) {
		if ( empty( $settings['schedule_meeting']['meeting_password_requirement'] ) ) {
			$settings['schedule_meeting']['meeting_password_requirement'] = [];
		}

		$password_requirements = $settings['schedule_meeting']['meeting_password_requirement'];

		return [
			'password_length'                 => (int) Arr::get( $password_requirements, 'length', 6 ),
			'password_have_special_character' => (bool) Arr::get( $password_requirements, 'have_special_character', false ),
			'password_only_allow_numeric'     => (bool) Arr::get( $password_requirements, 'only_allow_numeric', false ),
		];
	}

	/**
	 * Update the Zoom account on existing events before Multiple Account Support.
	 *
	 * @since 1.5.0
	 *
	 * @param \WP_Post $event The event post object.
	 *
	 * @return bool|void Whether the account has been added.
	 */
	public function update_event_for_multiple_accounts_support( $event ) {

		$event = tribe_get_event( $event->ID );

		if ( ! $event instanceof \WP_Post ) {
			return;
		}

		if ( empty( $event->virtual ) ) {
			return;
		}

		if ( empty( $event->zoom_meeting_id ) ) {
			return;
		}

		$account_id = get_post_meta( $event->ID, $this->account_id_meta_field_name, true );
		if ( $account_id ) {
			return;
		}

		$account_id = tribe_get_option( Settings::$option_prefix . 'original_account', '' );
		if ( empty( $account_id ) ) {
			return;
		}

		$this->save_account_id_to_post( $event->ID, $account_id );

		return true;
	}

	/**
	 * Returns the full OAuth URL to authorize the application.
	 *
	 * @since 1.13.0
	 * @deprecated 1.13.2
	 *
	 * @return string The full OAuth URL to authorize the application.
	 */
	public function authorize_url() {
		_deprecated_function( __FUNCTION__, '1.13.2', 'Zoom/Url->get_authorize_url()' );

		// Use the `state` query arg as described in Zoom API documentation.
		$authorize_url = add_query_arg(
			[
				'state' => wp_create_nonce( $this->actions::$authorize_nonce_action ),
			],
			admin_url()
		);

		return $authorize_url;
	}
}
