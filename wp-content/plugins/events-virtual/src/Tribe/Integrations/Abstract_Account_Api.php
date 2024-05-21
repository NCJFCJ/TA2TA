<?php
/**
 * Abstract Class to Manage Multiple Accounts Accessing an API.
 *
 * @since 1.9.0
 *
 * @package Tribe\Events\Virtual\Integrations
 */

namespace Tribe\Events\Virtual\Integrations;

use Tribe\Events\Virtual\Template_Modifications;
use Tribe\Events\Virtual\Traits\With_AJAX;
use Tribe__Utils__Array as Arr;
use Tribe__Events__Main as TEC;

/**
 * Class Account_Api
 *
 * @since 1.9.0
 *
 * @package Tribe\Events\Virtual\Integrations
 */
abstract class Abstract_Account_Api extends Request_Api {
	use With_AJAX;

	/**
	 * Whether an account has been loaded for the API to use.
	 *
	 * @since 1.9.0
	 *
	 * @var boolean
	 */
	protected $account_loaded = false;

	/**
	 * The name of the loaded account.
	 *
	 * @since 1.9.0
	 *
	 * @var string
	 */
	public $loaded_account_name = '';

	/**
	 * The current Account API access token.
	 *
	 * @since 1.9.0
	 *
	 * @var string
	 */
	protected $access_token;

	/**
	 * The current Account API refresh token.
	 *
	 * @since 1.9.0
	 *
	 * @var string
	 */
	protected $refresh_token;

	/**
	 * The key to get the option with a list of all accounts.
	 *
	 * @since 1.9.0
	 *
	 * @var string
	 */
	protected $all_account_key = 'tec_api_accounts';

	/**
	 * The prefix to save all single accounts with.
	 *
	 * @since 1.9.0
	 *
	 * @var string
	 */
	protected $single_account_prefix = 'tec_api_account_';

	/**
	 * The meta field name to save the account id to for single posts.
	 *
	 * @since 1.9.0
	 *
	 * @var string
	 */
	protected $account_id_meta_field_name = '_tribe_events_tec_account_id';

	/**
	 * The name of the action used to get an account setup to generate use an API.
	 *
	 * @since 1.9.0
	 * @deprecated 1.13.0 - Use Abstract_Actions::$select_action
	 *
	 * @var string
	 */
	public static $select_action = 'events-virtual-tec-account-setup';

	/**
	 * The name of the action used to change the status of an account to enabled or disabled.
	 *
	 * @since 1.9.0
	 * @deprecated 1.13.0 - Use Abstract_Actions::$select_action
	 *
	 * @var string
	 */
	public static $status_action;

	/**
	 * The name of the action used to delete an account.
	 *
	 * @since 1.9.0
	 * @deprecated 1.13.0 - Use Abstract_Actions::$select_action
	 *
	 * @var string
	 */
	public static $delete_action;

	/**
	 * An instance of the Template_Modifications.
	 *
	 * @since 1.9.0
	 *
	 * @var Template_Modifications
	 */
	protected $template_modifications;

	/**
	 * The Actions name handler.
	 *
	 * @since 1.13.0
	 *
	 * @var Abstract_Actions
	 */
	protected $actions;

	/**
	 * The URL handler.
	 *
	 * @since 1.13.3
	 *
	 * @var Abstract_Url
	 */
	protected $url;

	/**
	 * Checks whether the current API is ready to use.
	 *
	 * @since 1.9.0
	 *
	 * @return bool Whether the current API has a loaded account.
	 */
	public function is_ready() {
		return ! empty( $this->account_loaded );
	}

	/**
	 * Load a specific account into the API.
	 *
	 * @since 1.9.0
	 *
	 * @param array<string|string> $account An account with the fields to access the API.
	 *
	 * @return boolean Whether the account is loaded into the class to use for the API, default is false.
	 */
	public function load_account( array $account = [] ) {
		if ( $this->is_valid_account( $account ) ) {
			$this->init_account( $account );

			return true;
		}

		// Check for single events first.
		$loaded_account = '';
		if ( is_singular( TEC::POSTTYPE ) ){
			$post_id = get_the_ID();

			// Get the account id and if found, use to get the account.
			if ( $account_id = get_post_meta( $post_id, $this->account_id_meta_field_name, true ) ) {
				$loaded_account = $this->get_account_by_id( $account_id );
			}

			if ( ! $loaded_account ) {
				return false;
			}

			if ( $this->is_valid_account( $loaded_account ) ) {
				$this->init_account( $loaded_account );

				return true;
			}
		}

		// If nothing loaded so far and this is not the admin, then return false.
		if ( ! is_admin() ) {
			return false;
		}

		$account_id = $this->get_account_id_in_admin();

		// Get the account id and if found, use to get the account.
		if ( $account_id ) {
			$loaded_account = $this->get_account_by_id( $account_id );
		}

		if ( ! $loaded_account ) {
			return false;
		}

		if ( $this->is_valid_account( $loaded_account ) ) {
			$this->init_account( $loaded_account );

			return true;
		}

		return false;
	}

	/**
	 * Get the account id in the WordPress admin.
	 *
	 * @since 1.9.0
	 *
	 * @param int $post_id The optional post id.
	 *
	 * @return string The account id or empty string if not found.
	 */
	public function get_account_id_in_admin( $post_id = 0 ) {
		// If there is a post id, check if it is a post and if so use to get the account id.
		$post = $post_id ? get_post( $post_id ) : '';
		if ( $post instanceof \WP_Post ) {
			return get_post_meta( $post_id, $this->account_id_meta_field_name, true );
		}

		// Attempt to load through ajax requested variables.
		$nonce             = tribe_get_request_var( '_ajax_nonce' );
		$account_id        = tribe_get_request_var( 'account_id' );
		$requested_post_id = tribe_get_request_var( 'post_id' );
		if ( $account_id && $requested_post_id && $nonce ) {

			// Verify the nonce is valid.
			$valid_nonce = $this->is_valid_nonce( $nonce );
			if ( ! $valid_nonce ) {
				return '';
			}
			// Verify there is a real post.
			$post = get_post( $post_id );
			if ( $post instanceof \WP_Post ) {
				return esc_html( $account_id );
			}
		}

		// Safety check.
		if ( ! function_exists( 'get_current_screen' ) ) {
			return '';
		}

		// Set the ID if on the single event editor.
		if ( ! $post_id ) {
			$screen = get_current_screen();
			if ( ! empty( $screen->id ) && $screen->id == TEC::POSTTYPE ) {
				global $post;
				// Add a safety check for minimum supported versions of PHP(5.6) and WP(4.9.x).
				$post_id = empty( $post->ID ) ? 0 : $post->ID;
			}
		}

		if ( ! $post_id ) {
			return '';
		}

		return esc_html( get_post_meta( $post_id, $this->account_id_meta_field_name, true ) );
	}

	/**
	 * Load a specific account by the id.
	 *
	 * @since 1.9.0
	 *
	 * @param string $account_id The account id to get and load for use with the API.
	 *
	 * @return bool|string Whether the page is loaded or an error code. False or code means the page did not load.
	 */
	public function load_account_by_id( $account_id ) {
		$account = $this->get_account_by_id( $account_id );

		// Return not-found if no account.
		if ( empty( $account ) ) {
			return 'not-found';
		}

		// Return disabled if the is disabled.
		if ( empty( $account['status'] ) ) {
			return 'disabled';
		}

		return $this->load_account( $account );
	}

	/**
	 * Check if an account has all the information to be valid.
	 *
	 * It will attempt to refresh the access token if it has expired.
	 *
	 * @since 1.9.0
	 *
	 * @param array<string|string> $account An account with the fields to access the API.
	 *
	 * @return bool
	 */
	protected function is_valid_account( $account ) {
		if ( empty( $account['id'] ) ) {
			return false;
		}
		if ( empty( $account['refresh_token'] ) ) {
			return false;
		}
		if ( empty( $account['expiration'] ) ) {
			return false;
		}

		// Attempt to refresh the token.
		$access_token = $this->maybe_refresh_access_token( $account );
		if ( empty( $access_token ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Initialize an Account to use for the API.
	 *
	 * @since 1.9.0
	 *
	 * @param array<string|string> $account An account with the fields to access the API.
	 */
	protected function init_account( $account ) {
		$this->access_token        = $account['access_token'];
		$this->refresh_token       = $account['refresh_token'];
		$this->id                  = $account['id'];
		$this->email               = $account['email'];
		$this->supports_webinars   = isset( $account['webinars'] ) ? tribe_is_truthy( $account['webinars'] ) : false;
		$this->account_loaded      = true;
		$this->loaded_account_name = $account['name'];
		$this->domain              = isset( $account['domain'] ) ? $account['domain'] : '';
	}

	/**
	 * Get the listing of Accounts.
	 *
	 * @since 1.9.0
	 *
	 * @param boolean $all_data Whether to return all account data, default is only name and status.
	 *
	 * @return array<string|string> $list_of_accounts An array of all the accounts.
	 */
	public function get_list_of_accounts( $all_data = false ) {
		$list_of_accounts = get_option( $this->all_account_key, [] );
		foreach ( $list_of_accounts as $account_id => $account ) {
			if ( empty( $account['name'] ) ) {
				continue;
			}
			$list_of_accounts[ $account_id ]['name'] = $account['name'];

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
	 * Get list of accounts formatted for options dropdown.
	 *
	 * @since 1.9.0
	 * @since 1.11.0 - Add email to the account list.
	 *
	 * @param boolean $all_data Whether to return only active accounts or not.
	 *
	 * @return array<string,mixed>  An array of Accounts formatted for options dropdown.
	 */
	public function get_formatted_account_list( $active_only = false ) {
		$available_accounts = $this->get_list_of_accounts( true );
		if ( empty( $available_accounts ) ) {
			return [];
		}

		$accounts = [];
		foreach ( $available_accounts as $account ) {
			$name  = Arr::get( $account, 'name', '' );
			$value = Arr::get( $account, 'id', '' );
			$email = Arr::get( $account, 'email', '' );
			$status = Arr::get( $account, 'status', false );

			if ( empty( $name ) || empty( $value ) ) {
				continue;
			}

			if ( $active_only && ! $status ) {
				continue;
			}

			$accounts[] = [
				'text'  => (string) $name,
				'id'    => (string) $value,
				'value' => (string) $value,
				'email' => (string) $email,
			];
		}

		return $accounts;
	}

	/**
	 * Update the list of accounts with provided account.
	 *
	 * @since 1.9.0
	 *
	 * @param array<string|string> $account_data The array of data for an account to add to the list.
	 */
	protected function update_list_of_accounts( $account_data ) {
		$accounts                        = $this->get_list_of_accounts();

		/**
		 * Fires after before the account list is updated for an API.
		 *
		 * @since 1.9.0
		 *
		 * @param array<string,mixed>  An array of Accounts formatted for options dropdown.
		 * @param array<string|string> $account_data The array of data for an account to add to the list.
		 * @param string               $api_id       The id of the API in use.
		 */
		do_action( 'tec_events_virtual_before_update_api_accounts', $accounts, $account_data, static::$api_id );

		$accounts[ esc_attr( $account_data['id'] ) ] = [
			'name'   => esc_attr( $account_data['name'] ),
			'status' => esc_attr( $account_data['status'] ),
		];

		update_option( $this->all_account_key, $accounts );
	}

	/**
	 * Delete from the list of accounts the provided account.
	 *
	 * @since 1.9.0
	 *
	 * @param string $account_id The id of the single account to save.
	 */
	protected function delete_from_list_of_accounts( $account_id ) {
		$accounts                        = $this->get_list_of_accounts();
		unset( $accounts[ $account_id ] );

		update_option( $this->all_account_key, $accounts );
	}

	/**
	 * Get a Single Account by id.
	 *
	 * @since 1.9.0
	 *
	 * @param string $account_id The id of the single account.
	 *
	 * @return array<string|string> $account The account data or empty array if no account.
	 */
	public function get_account_by_id( $account_id ) {
		return get_option( $this->single_account_prefix . $account_id, [] );
	}

	/**
	 * Set an Account with the provided id.
	 *
	 * @since 1.9.0
	 *
	 * @param array<string|string> $account_data A specific account data to save.
	 */
	public function set_account_by_id( array $account_data ) {
		update_option( $this->single_account_prefix . $account_data['id'], $account_data, false );

		$this->update_list_of_accounts( $account_data );
	}

	/**
	 * Delete an account by ID.
	 *
	 * @since 1.9.0
	 *
	 * @param string $account_id The id of the single account.
	 *
	 * @return bool Whether the account has been deleted and the access token revoked.
	 */
	public function delete_account_by_id( $account_id ) {
		delete_option( $this->single_account_prefix . $account_id );

		$this->delete_from_list_of_accounts( $account_id );

		return true;
	}

	/**
	 * Revoke the accounts access token with the API.
	 *
	 * @since 1.9.0
	 *
	 * @param string $account_id The id of the single account.
	 *
	 * @return bool Whether the account access token is revoked.
	 */
	abstract protected function revoke_account_by_id( $account_id );

	/**
	 * Save the account id to the post|event.
	 *
	 * @since 1.9.0
	 *
	 * @param int $post_id The id to save the meta field too.
	 * @param string $account_id The id of the single account to save.
	 *
	 * @return bool|int
	 */
	public function save_account_id_to_post( $post_id, $account_id ) {
		return update_post_meta( $post_id, $this->account_id_meta_field_name, $account_id );
	}

	/**
	 * Set an Account Access Data with the provided id.
	 *
	 * @since 1.9.0
	 *
	 * @param string $account_id    The id of the single account to save.
	 * @param string $access_token  The Account API access token.
	 * @param string $refresh_token The Account API refresh token.
	 * @param string $expiration    The expiration in seconds as provided by the server.
	 */
	public function set_account_access_by_id( $account_id, $access_token, $refresh_token, $expiration ) {
		$account_data                  = $this->get_account_by_id( $account_id );
		$account_data['access_token']  = $access_token;
		$account_data['refresh_token'] = $refresh_token;
		$account_data['expiration']    = $expiration;

		$this->set_account_by_id( $account_data );
	}

	/**
	 * Check if the access token response has the proper credentials.
	 *
	 * @since 1.11.0
	 *
	 * @param array<string,array> $response      An array representing the access token request response, in the format
	 *                                           returned by WordPress `wp_remote_` functions.
	 * @param boolean             $check_refresh Whether to check for a refresh token, default true.
	 *
	 * @return bool Whether the proper credentials are found.
	 */
	protected function has_proper_credentials( array $response, $check_refresh = true ) {
		if ( ! isset( $response['body'] ) ) {
			return false;
		}

		$credentials = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( false === $credentials ) {
			return false;
		}

		if ( ! isset( $credentials['access_token'], $credentials['expires_in'] ) ) {
			return false;
		}

		if ( $check_refresh && ! isset( $credentials['refresh_token'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Save an Account.
	 *
	 * @since 1.9.0
	 *
	 * @param array<string,array> $response An array representing the access token request response, in the format
	 *                                      returned by WordPress `wp_remote_` functions.
	 *
	 * @return bool|mixed The access token for an account.
	 */
	abstract public function save_account( array $response );

	/**
	 * Save an Access Token and Expiration information for an Account.
	 *
	 * @since 1.9.0
	 *
	 * @param array<string,array> $response An array representing the access token request response, in the format
	 *                                      returned by WordPress `wp_remote_` functions.
	 *
	 * @return bool Whether the access token has been updated.
	 */
	abstract public function save_access_and_expiration( $id, array $response );

	/**
	 * Prepare a single account's data to save.
	 *
	 * @since 1.9.0
	 * @since 1.8.0 - Use a method to detect webinar support.
	 *
	 * @param array<string|string> $user          The user information from the API.
	 * @param string               $access_token  The Account API access token.
	 * @param string               $refresh_token The Account API refresh token.
	 * @param string               $expiration    The expiration in seconds as provided by the server.
	 * @param array<string|mixed>  $settings      The user settings from the API.
	 * @param boolean              $status        The status of the account, whether active or not.
	 *
	 * @return array<string|string> The account information prepared for saving.
	 */
	abstract protected function prepare_account_data( $user, $access_token, $refresh_token, $expiration, $settings, $status );

	/**
	 * Returns the access token based authorization header to send requests to the API.
	 *
	 * @since 1.9.0
	 *
	 * @return string|boolean The authorization header, to be used in the `headers` section of a request to the API or false if not available.
	 */
	public function get_token_authorization_header( $access_token = '' ) {
		if ( $access_token ) {
			return 'Bearer ' . $access_token;
		}

		if ( $this->access_token ) {
			return 'Bearer ' . $this->access_token;
		}

		return false;
	}

	/**
	 * Get the expiration time stamp.
	 *
	 * @since 1.9.0
	 *
	 * @param string The amount of time in seconds until the access token expires.
	 *
	 * @return string The timestamp when the access token expires.
	 */
	public function get_expiration_time_stamp( $expires_in ) {
		// Take the expiration in seconds as provided by the server and remove a minute to pad for save delays.
		return ( (int) $expires_in ) - MINUTE_IN_SECONDS + current_time( 'timestamp' );
	}

	/**
	 * Get the refresh token.
	 *
	 * @since 1.9.0
	 *
	 * @return string The refresh token.
	 */
	public function get_refresh_token() {
		return $this->refresh_token;
	}

	/**
	 * Returns the current API access token.
	 *
	 * If not available, then a new token will be fetched.
	 *
	 * @since 1.9.0
	 *
	 * @param string $id
	 * @param string $refresh_token The API refresh token for the account.
	 *
	 * @return string The API access token, or an empty string if the token cannot be fetched.
	 */
	abstract function refresh_access_token( $id, $refresh_token );

	/**
	 * Maybe refresh the access token or use the saved one.
	 *
	 * @since 1.9.0
	 *
	 * @param array<string|string> $account An account with the fields to access the API.
	 *
	 * @return bool|mixed|string
	 */
	protected function maybe_refresh_access_token( $account ) {
		// If token is valid, return it to start using it.
		if (
			current_time( 'timestamp' ) <= $account['expiration'] &&
			! empty( $account['access_token'] )
		) {
			return $account['access_token'];
		}

		// Attempt to refresh the token.
		$access_token = $this->refresh_access_token( $account['id'], $account['refresh_token'] );
		if ( empty( $access_token ) ) {
			return false;
		}

		return $access_token;
	}

	/**
	 * Checks whether the current API integration is authorized or not.
	 *
	 * The check is made on the existence of the refresh token, with it the token can be fetched on demand when
	 * required.
	 *
	 * @since 1.11.0
	 *
	 * @return bool Whether the current API integration is authorized or not.
	 */
	public function is_authorized() {
		return ! empty( $this->refresh_token );
	}

	/**
	 * Get a User's information or settings.
	 *
	 * @since 1.9.0
	 *
	 * @param string  $user_id      A user id for an API.
	 * @param boolean $settings     Whether to fetch the users settings.
	 * @param string  $access_token A provided access token to use to access the API.
	 *
	 * @return array<string|mixed> An array of data from the an API.
	 */
	abstract function fetch_user( $user_id = '', $settings = false, $access_token = '' );

	/**
	 * Check if a nonce is valid from a list of actions.
	 *
	 * @since 1.9.0
	 *
	 * @param string $nonce  The nonce to check.
	 *
	 * @return bool Whether the nonce is valid or not.
	 */
	protected function is_valid_nonce( $nonce ) {
		$app_id = static::$api_id;

		/**
		 * Filters a list of account api ajax nonce actions.
		 *
		 * @since 1.9.0
		 *
		 * @param array<string,callable> A map from the nonce actions to the corresponding handlers.
		 */
		$actions = apply_filters( "tribe_events_virtual_meetings_{$app_id}_actions", [] );

		foreach ( $actions as $action => $callback ) {
			if ( $this->check_ajax_nonce( $action, $nonce ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * The message template to display on user account changes.
	 *
	 * @since 1.9.0
	 *
	 * @param string $message The message to display.
	 * @param string $type    The type of message, either standard or error.
	 *
	 * @return string The message with html to display
	 */
	public function get_settings_message_template( $message, $type = 'standard' ) {
		return $this->template_modifications->get_settings_message_template( $message, $type );
	}

	/**
	 * Handles the request to change the status of an API account.
	 *
	 * @since 1.9.0
	 *
	 * @param string|null $nonce The nonce that should accompany the request.
	 *
	 * @return bool Whether the request was handled or not.
	 */
	public function ajax_status( $nonce = null ) {
		if ( ! $this->check_ajax_nonce( $this->actions::$status_action, $nonce ) ) {
			return false;
		}

		$account_id = tribe_get_request_var( 'account_id' );
		$account    = $this->get_account_by_id( $account_id );
		// If no account id found, fail the request.
		if ( empty( $account_id ) || empty( $account ) ) {
			$error_message = sprintf(
				// translators: the placeholders is for the API name.
				_x(
					'The %1$s Account ID or Account is missing to change the status.',
					'Account ID is missing on status change error message.',
					'events-virtual'
				),
				static::$api_name
			);

			$this->get_settings_message_template( $error_message, 'error' );

			wp_die();
		}

		// Set the status to the opposite of what is saved.
		$new_status        = tribe_is_truthy( $account['status'] ) ? false : true;
		$account['status'] = $new_status;
		$this->set_account_by_id( $account );

		// Attempt to load the account when status is changed to enabled and on failure display a message.
		$loaded = $new_status ? $this->load_account_by_id( $account['id'] ) : true;
		if ( empty( $loaded ) ) {
			$error_message = sprintf(
				// translators: the placeholders are for the API name.
				_x(
					'There seems to be a problem with the connection to this %1$s account. Please refresh the connection.',
					'Message to display when the %1$s account could not be loaded after being enabled.',
					'events-virtual'
				),
				static::$api_name
			);

			$this->get_settings_message_template( $error_message, 'error' );

			wp_die();
		}

		$status_msg = $new_status
				? _x(
					'%1$s connection enabled for %2$s',
					'Enables the API Account for the Website.',
					'events-virtual'
				)
				: _x(
					'%1$s connection disabled for %2$s',
					'Disables the API Account for the Website.',
					'events-virtual'
				);

		$message = sprintf(
			/* Translators: %1$s is the name of the API, %2$s: the name of the account that has the status change. */
			$status_msg,
			static::$api_name,
			$account['name']
		);

		$this->get_settings_message_template( $message );

		wp_die();
	}

	/**
	 * Get the confirmation text for refreshing an account.
	 *
	 * @since 1.9.0
	 *
	 * @return string The confirmation text.
	 */
	public static function get_confirmation_to_refresh_account() {
		return sprintf(
			// translators: the placeholders are for the API name.
			_x(
				'Before refreshing the connection, make sure you are logged into the %1$s account in this browser.',
				'The message to display before a user attempts to refresh a %1$s account connection.',
				'events-virtual'
			),
			static::$api_name
		);
	}

	/**
	 * Handles the request to delete a an API account.
	 *
	 * @since 1.9.0
	 *
	 * @param string|null $nonce The nonce that should accompany the request.
	 *
	 * @return bool Whether the request was handled or not.
	 */
	public function ajax_delete( $nonce = null ) {
		if ( ! $this->check_ajax_nonce( $this->actions::$delete_action, $nonce ) ) {
			return false;
		}

		$account_id = tribe_get_request_var( 'account_id' );
		$account    = $this->get_account_by_id( $account_id );
		// If no account id found, fail the request.
		if ( empty( $account_id ) || empty( $account ) ) {
			$error_message = sprintf(
				// translators: the placeholders is for the API name.
				_x(
					'The %1$s Account ID or Account is missing to change the status.',
					'Account ID is missing on status change error message.',
					'events-virtual'
				),
				static::$api_name
			);

			$this->get_settings_message_template( $error_message, 'error' );

			wp_die();
		}

		$success = $this->delete_account_by_id( $account_id );
		if ( $success ) {
			$message = sprintf(
				/* Translators: %1$s: the name of the account that has been deleted. */
				_x(
					'%1$s was successfully deleted',
					'Account ID is missing on status change error message.',
					'events-virtual'
				),
				static::$api_name
			);

			$this->get_settings_message_template( $message );

			wp_die();
		}

		$error_message = sprintf(
		/* Translators: %1$s: the name of the account that has been deleted. */
			_x(
				'The %1$s Account access token could not be revoked.',
				'Message to display when the %1$s account could not be loaded after being enabled.',
				'events-virtual'
			),
			static::$api_name
		);

		$this->get_settings_message_template( $error_message, 'error' );

		wp_die();
	}

	/**
	 * Get the confirmation text for deleting an account.
	 *
	 * @since 1.9.0
	 *
	 * @return string The confirmation text.
	 */
	public static function get_confirmation_to_delete_account() {
		return sprintf(
			// translators: the placeholders are for the API name.
			_x(
				'Are you sure you want to delete this %1$s connection? This operation cannot be undone. Existing meetings tied to this account will not be impacted.',
				'The message to display to confirm a user would like to delete a %1$s account.',
				'events-virtual'
			),
			static::$api_name
		);
	}

}
