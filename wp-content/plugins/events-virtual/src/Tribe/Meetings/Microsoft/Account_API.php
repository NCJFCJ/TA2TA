<?php
/**
 * Manages all the Account Connection to Microsoft
 *
 * @since 1.13.0
 *
 * @package Tribe\Events\Virtual\Meetings\Microsoft
 */

namespace Tribe\Events\Virtual\Meetings\Microsoft;

use Tribe\Events\Virtual\Integrations\Abstract_Account_Api;
use Tribe__Utils__Array as Arr;

/**
 * Class Account_API
 *
 * @since 1.13.0
 *
 * @package Tribe\Events\Virtual\Meetings\Microsoft
 */
abstract class Account_API extends Abstract_Account_Api {

	/**
	 * {@inheritDoc}
	 */
	protected $all_account_key = 'tec_microsoft_accounts';

	/**
	 * {@inheritDoc}
	 */
	protected $single_account_prefix = 'tec_microsoft_account_';

	/**
	 * {@inheritDoc}
	 */
	protected $account_id_meta_field_name = '_tribe_events_microsoft_account_id';

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
		if ( defined( 'TEC_VIRTUAL_EVENTS_MICROSOFT_API_REVOKE_URL' ) ) {
			$revoke_url = TEC_VIRTUAL_EVENTS_MICROSOFT_API_REVOKE_URL;
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
						'message'  => 'Microsoft Account Revoke Failed.',
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
	 * @since 1.13.0
	 *
	 * @param string|null $nonce The nonce string to authorize the authorization request.
	 *
	 * @return bool Whether the authorization request is valid and was handled or not.
	 */
	public function handle_auth_request( $nonce = null ) {
		if ( ! wp_verify_nonce( $nonce, $this->actions::$authorize_nonce_action ) ) {
			wp_die( _x(
					'You are not authorized to do this.',
					'The message shown to a user providing a wrong Microsoft API OAuth authorization nonce.',
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
		$user_fields = $this->fetch_user( 'me', false, $access_token );
		if ( empty( $user_fields['id'] ) ) {
			return false;
		}

		$email = isset( $user_fields['mail'] ) && ! is_array( $user_fields['mail'] ) ? $user_fields['mail'] : '';
		$email = empty( $email ) && isset( $user_fields['userPrincipalName'] ) ? $user_fields['userPrincipalName'] : $email;

		$user = [
			'id'    => $user_fields['id'],
			'name'   => $user_fields['displayName'] . ' (' . $email .')',
			'email' => $email,
		];

		$account_data     = $this->prepare_account_data( $user, $access_token, $refresh_token, $expiration, [], true );
		$existing_account = $this->get_account_by_id( $account_data['id'] );
		$this->set_account_by_id( $account_data );

		$message = $existing_account ?
			sprintf(
				/* Translators: %1$s: the name of the account that has been added or refreshed from Microsoft . */
				_x(
					'Microsoft connection refreshed for %1$s',
					'The refresh message for a Microsoft account.',
					'events-virtual'
				),
				$account_data['name']
			)
			: sprintf(
					/* Translators: %1$s: the name of the account that has been added or refreshed from Microsoft . */
					_x(
						'Microsoft Account added for %1$s',
						'The Microsoft account added message.',
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
			'name'          => $user['name'],
			'email'         => $user['email'],
			'access_token'  => $access_token,
			'refresh_token' => $refresh_token,
			'expiration'    => $expiration,
			'status'        => $status,
		];
	}

	/**
	 * Get calendar settings for a user.
	 *
	 * @since 1.13.0
	 *
	 * @return array<string|mixed> $data An array of settings from the response to get calendar on Microsoft API.
	 */
	public function get_calendar_settings() {
		$data = [];

		$this->get(
			Api::$api_base . "me/calendar",
			[
				'headers' => [
					'authorization' => $this->get_token_authorization_header(),
					'content-type'  => 'application/json',
				],
			],
			200
		)->then(
			static function ( array $response ) use ( &$data ) {
				$body     = json_decode( wp_remote_retrieve_body( $response ), true );
				$body_set = self::has_proper_response_body( $body );

				if ( ! $body_set ) {
					do_action( 'tribe_log', 'error', __CLASS__, [
						'action'   => __METHOD__,
						'message'  => 'Microsoft API user response is malformed.',
						'response' => $body,
					] );

					return [];
				}
				$data = $body;
			}
		)->or_catch(
			function ( \WP_Error $error ) {
				do_action(
					'tribe_log',
					'error',
					__CLASS__,
					[
						'action'  => __METHOD__,
						'code'    => $error->get_error_code(),
						'message' => $error->get_error_message(),
					]
				);
			}
		);

		return $data;
	}

	/**
	 * Get the available meeting providers from the get calendar response.
	 *
	 * @since 1.13.0
	 *
	 * @param array<string|mixed> $settings An array of settings from the response to get calendar on Microsoft API.
	 *
	 * @return array<string|string> $online_meetings An array of allowed online meetings for the Microsoft API.
	 */
	public function get_available_meeting_providers( array $settings = [] ) {
		$allowed_online_meetings = Arr::get( $settings, 'allowedOnlineMeetingProviders', [] );
		$allowed_online_meetings = array_flip( $allowed_online_meetings );

		$online_meetings = [
			'teamsForBusiness' => [
				'available' => isset( $allowed_online_meetings['teamsForBusiness'] ),
				'label'     => _x( 'Teams', 'The name of the online meeting provider for Microsoft API.', 'events-virtual' ),
				'tooltip'   => [
					'classes_wrap'  => [ 'tec-events-virtual-meetings-api__type-options--tooltip' ],
					'message'   => _x(
						'Teams is active when you have it enabled in your work or school account. (it is not available in personal accounts)',
						'Explains when the Skype for personal is active when creating a meeting link for the Microsoft API.',
						'events-virtual'
					),
				],
			],
			'skypeForBusiness' => [
				'available' => isset( $allowed_online_meetings['skypeForBusiness'] ),
				'label'     => _x( 'Skype (business)', 'The name of the online meeting provider for Microsoft API.', 'events-virtual' ),
				'tooltip'   => [
					'classes_wrap'  => [ 'tec-events-virtual-meetings-api__type-options--tooltip' ],
					'message'   => _x(
						'Skype for business use is active when you have it enabled in your work or school account. (it is not available in personal accounts)',
						'Explains when the Skype for personal is active when creating a meeting link for the Microsoft API.',
						'events-virtual'
					),
				],
			],
			'skypeForConsumer' => [
				'available' => isset( $allowed_online_meetings['skypeForConsumer'] ),
				'label'     => _x( 'Skype (personal)', 'The name of the online meeting provider for Microsoft API.', 'events-virtual' ),
				'tooltip'   => [
					'classes_wrap'  => [ 'tec-events-virtual-meetings-api__type-options--tooltip' ],
					'message'   => _x(
						'Skype for personal use is active when you have a paid Office 365 subscription.',
						'Explains when the Skype for personal is active when creating a meeting link for the Microsoft API.',
						'events-virtual'
					),
				],
			],
		];

		/**
		 * Filters the allowed online meetings for a Microsoft account.
		 *
		 * @since 1.13.0
		 *
		 * @param array<string|string> $online_meetings An array of allowed online meetings for the Microsoft API.
		 */
		$online_meetings = apply_filters( 'tribe_events_virtual_meetings_zoom_password_requirements', $online_meetings );

		return $online_meetings;
	}

	/**
	 * Get the default Microsoft Meeting Provider.
	 *
	 * @since 1.13.0
	 *
	 * @param array<string|string> $online_meetings An array of allowed online meetings for the Microsoft API.
	 *
	 * @return string The key to the default Microsoft meeting provider.
	 */
	public function get_default_meeting_provider( array $available_meetings = [] ) {
		$active_providers = wp_list_filter(
			$available_meetings,
		    [ 'available' => true ],
		);

		if ( empty( $active_providers ) || ! is_array( $active_providers ) ) {
			return '';
		}

		// If multiple providers then default to Teams of Skype for Business.
		if ( count( $active_providers ) > 1 ) {
			if ( isset( $active_providers['teamsForBusiness'] ) ) {
				return 'teamsForBusiness';
			} elseif ( isset( $active_providers['skypeForBusiness'] ) ) {
				return 'skypeForBusiness';
			}
		}

		return key($active_providers);
	}
}
