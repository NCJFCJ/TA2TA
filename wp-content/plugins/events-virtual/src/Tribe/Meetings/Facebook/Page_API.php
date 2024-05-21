<?php
/**
 * Manages the Connection to Facebook
 *
 * @since   1.7.0
 *
 * @package Tribe\Events\Virtual\Meetings\Facebok
 */

namespace Tribe\Events\Virtual\Meetings\Facebook;

use Tribe\Events\Virtual\Meetings\Facebook\Event_Meta as Facebook_Meta;
use Tribe\Events\Virtual\Traits\With_AJAX;
use Tribe__Utils__Array as Arr;
use Tribe__Events__Main as TEC;

/**
 * Class Page_API
 *
 * @since   1.7.0
 *
 * @package Tribe\Events\Virtual\Meetings\Facebook
 */
class Page_API extends Video_API {

	use With_AJAX;

	/**
	 * Whether a Facebook Page has been loaded for the API to use.
	 *
	 * @since 1.7.0
	 *
	 * @var boolean
	 */
	protected $page_loaded = false;

	/**
	 * The name of the loaded page.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	public $loaded_page_name = '';

	/**
	 * The key to get the option with a list of all Facebook Pages.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	protected $all_page_key = 'tec_facebook_pages';

	/**
	 * The prefix to save all Facebook pages with.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	protected $single_page_prefix = 'tec_facebook_page_';

	/**
	 * The current Facebook App ID.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	protected $add_id;

	/**
	 * The current Facebook App Secret.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	protected $app_secret;

	/**
	 * The current Facebook Page's local id.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	protected $local_id;

	/**
	 * The current Facebook Page's ID.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	public $page_id;

	/**
	 * The current Facebook Page's Access Token.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	public $access_token;

	/**
	 * The meta field name to save the page id to for single posts.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	protected $page_local_id_meta_field_name = '_tribe_events_facebook_page_local_id';

	/**
	 * The name of the action used to get a page id when selecting a Facebook Live page.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	public static $select_action = 'events-virtual-facebook-page-selection';

	/**
	 * The url to connect to the Facebook API to get an access token's expiration.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	public static $facebook_page_access_expiration_url = 'https://graph.facebook.com/v12.0/debug_token';

	/**
	 * Get the Facebook API url to get an access token's expiration.
	 *
	 * @since 1.7.0
	 *
	 * @return string The api expiration url..
	 */
	protected static function get_access_expiration_url() {
		/**
		 * Allow filtering of the Facebook API url to get an access token's expiration.
		 *
		 * @since 1.7.0
		 *
		 * @param string The url to get a Facebook access token's expiration.
		 */
		return apply_filters( 'tribe_events_virtual_facebook_page_expiration_url', static::$facebook_page_access_expiration_url );
	}

	/**
	 * Get the Facebook API url to get an access token's expiration with query strings.
	 *
	 * @since 1.7.0
	 *
	 * @param string $access_token The Facebook api access token.
	 *
	 * @return string The url to access the Facebook API url with query strings.
	 */
	protected function get_access_expiration_url_with_query_strings( $access_token ) {
		return add_query_arg( [
			'input_token' => $access_token,
			'access_token' => $access_token,
		], $this->get_access_expiration_url() );
	}

	/**
	 * Checks whether the current Facebook Page API is ready to use.
	 *
	 * @since 1.7.0
	 *
	 * @return bool Whether the current Facebook Page API has a loaded page.
	 */
	public function is_ready() {
		return ! empty( $this->page_loaded );
	}

	/**
	 * Load a specific page into the API.
	 *
	 * @since 1.7.0
	 *
	 * @param array<string|string> $page A Facebook Page with the fields to access the API.
	 *
	 * @return bool Whether the page is loaded into the class to use for the API, default is false.
	 */
	public function load_page( array $page = [] ) {
		if ( $this->is_valid_page( $page ) ) {
			$this->init_page( $page );

			return true;
		}

		// Check for single events first.
		$loaded_page = '';
		if ( is_singular( TEC::POSTTYPE ) ) {
			$post_id = get_the_ID();

			// Get the local Facebook Page id and if found, use to get the page.
			if ( $local_id = get_post_meta( $post_id, $this->page_local_id_meta_field_name, true ) ) {
				$loaded_page = $this->get_page_by_id( $local_id );
			}

			if ( ! $loaded_page ) {
				return false;
			}

			if ( $this->is_valid_page( $loaded_page ) ) {
				$this->init_page( $loaded_page );

				return true;
			}
		}

		// If nothing loaded so far and this is the frontend, return false.
		if ( ! is_admin() ) {
			return false;
		}

		$local_id = $this->get_local_id_in_admin();

		// Get the page id and if found, use to get the page.
		if ( $local_id ) {
			$loaded_page = $this->get_page_by_id( $local_id );
		}

		if ( ! $loaded_page ) {
			return false;
		}

		if ( $this->is_valid_page( $loaded_page ) ) {
			$this->init_page( $loaded_page );

			return true;
		}

		return false;
	}

	/**
	 * Get the Facebook Page id in the WordPress admin.
	 *
	 * @since 1.7.0
	 *
	 * @param int $post_id The optional post id.
	 *
	 * @return string The page id or empty string if not found.
	 */
	public function get_local_id_in_admin( $post_id = 0 ) {
		// If there is a post id, check if it is a post and if so use to get the page id.
		$post = $post_id ? get_post( $post_id ) : '';
		if ( $post instanceof \WP_Post ) {
			return get_post_meta( $post_id, $this->page_local_id_meta_field_name, true );
		}

		// Attempt to load through ajax requested variables.
		$nonce             = tribe_get_request_var( '_ajax_nonce' );
		$page_id           = tribe_get_request_var( 'page_id' );
		$requested_post_id = tribe_get_request_var( 'post_id' );
		if ( $page_id && $requested_post_id && $nonce ) {

			// Verify the nonce is valid.
			$valid_nonce = $this->is_valid_nonce( $nonce );
			if ( ! $valid_nonce ) {
				return '';
			}
			// Verify there is a real post.
			$post = get_post( $post_id );
			if ( $post instanceof \WP_Post ) {
				return esc_html( $page_id );
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
				$post_id = $post->ID;
			}
		}

		if ( ! $post_id ) {
			return '';
		}

		return esc_html( get_post_meta( $post_id, $this->page_local_id_meta_field_name, true ) );
	}

	/**
	 * Load a specific page by the id.
	 *
	 * @since 1.7.0
	 *
	 * @param string $local_id The local Facebook Page id to get and load for use with the API.
	 *
	 * @return bool|string Whether the page is loaded or an error code. False or code means the page did not load.
	 */
	public function load_page_by_id( $local_id ) {
		$page = $this->get_page_by_id( $local_id );

		// Return not-found if no page.
		if ( empty( $page ) ) {
			return 'not-found';
		}

		// Return disabled if no access token.
		if ( empty( $page['access_token'] ) ) {
			return 'disabled';
		}

		return $this->load_page( $page );
	}

	/**
	 * Check if an page has all the information to be valid.
	 *
	 * @since 1.7.0
	 *
	 * @param array<string|string> $page A Facebook Page page with the fields to access the API.
	 *
	 * @return bool
	 */
	private function is_valid_page( $page ) {
		if ( empty( $page['local_id'] ) ) {
			return false;
		}
		if ( empty( $page['access_token'] ) ) {
			return false;
		}
		if ( empty( $page['expiration'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Initialize a Facebook Page to use for the API.
	 *
	 * @since 1.7.0
	 *
	 * @param array<string|string> $page A Facebook Page with the fields to access the API.
	 */
	private function init_page( $page ) {
		$this->access_token     = $page['access_token'];
		$this->local_id         = $page['local_id'];
		$this->page_id          = $page['page_id'];
		$this->page_loaded      = true;
		$this->loaded_page_name = $page['name'];
	}

	/**
	 * Get the listing of Facebook Pages.
	 *
	 * @since 1.7.0
	 *
	 * @param boolean $all_data Whether to return all page data, default is only name and status.
	 *
	 * @return array<string|string> An array of all the Facebook Pages.
	 */
	public function get_list_of_pages( $all_data = false ) {
		// Get list of pages.
		$list_of_pages = get_option( $this->all_page_key, [] );

		if ( empty( $all_data ) ) {
			return $list_of_pages;
		}

		// Add all the data to the list.
		foreach ( $list_of_pages as $local_id => $page ) {
			$page_data = $this->get_page_by_id( $local_id );

			$list_of_pages[ $local_id ] = $page_data;
		}

		return $list_of_pages;
	}

	/**
	 * Get list of pages formatted for options dropdown.
	 *
	 * @since 1.7.0
	 *
	 * @param boolean $all_data Whether to return only active pages or not.
	 * @param string  $selected The selected local id.
	 *
	 * @return array<string,mixed>  An array of Facebook Pages formatted for options dropdown.
	 */
	public function get_formatted_page_list( $active_only = false, $selected = '' ) {
		$available_pages = $this->get_list_of_pages( true );
		if ( empty( $available_pages ) ) {
			return [];
		}

		$pages = [];
		foreach ( $available_pages as $page ) {
			$name   = Arr::get( $page, 'name', '' );
			$value  = Arr::get( $page, 'local_id', '' );
			$status = Arr::get( $page, 'access_token', '' );

			if ( empty( $name ) || empty( $value ) ) {
				continue;
			}

			if ( $active_only && ! $status ) {
				continue;
			}

			$pages[] = [
				'text'  => (string) $name,
				'id'    => (string) $value,
				'value' => (string) $value,
				'selected' => $value === $selected ? true : false,
			];
		}

		return $pages;
	}

	/**
	 * Update the list of pages with provided page.
	 *
	 * @since 1.7.0
	 *
	 * @param array<string|string> $page_data The array of data for an page to add to the list.
	 */
	protected function update_list_of_pages( $page_data ) {
		$pages = $this->get_list_of_pages();
		$pages[ esc_attr( $page_data['local_id'] ) ] = [
			'name'   => esc_attr( $page_data['name'] ),
			'status' => empty( $page['access_token'] ) ? false : true,
		];

		update_option( $this->all_page_key, $pages );
	}

	/**
	 * Delete from the list of pages the provided page.
	 *
	 * @since 1.7.0
	 *
	 * @param string $local_id The local id of the Facebook page to delete.
	 */
	protected function delete_from_list_of_pages( $local_id ) {
		$pages = $this->get_list_of_pages();
		unset( $pages[ $local_id ] );

		update_option( $this->all_page_key, $pages );
	}

	/**
	 * Get a Single Facebook Page by id.
	 *
	 * @since 1.7.0
	 *
	 * @param string $local_id The local id of the Facebook Page.
	 *
	 * @return array<string|string> $page The Facebook Page data.
	 */
	public function get_page_by_id( $local_id ) {

		return get_option( $this->single_page_prefix . $local_id, [] );
	}

	/**
	 * Set a Facebook Page with the provided id.
	 *
	 * @since 1.7.0
	 *
	 * @param array<string|string> $page_data A specific Facebook Page data to save.
	 */
	public function set_page_by_id( array $page_data ) {
		if ( empty( $page_data['local_id'] ) ) {
			return false;
		}

		update_option( $this->single_page_prefix . $page_data['local_id'], $page_data, false );

		$this->update_list_of_pages( $page_data );
	}

	/**
	 * Delete a Facebook Page by ID.
	 *
	 * @since 1.7.0
	 *
	 * @param string $local_id The Facebook Page local id.
	 */
	public function delete_page_by_id( $local_id ) {
		delete_option( $this->single_page_prefix . $local_id );

		$this->delete_from_list_of_pages( $local_id );
	}

	/**
	 * Save the page id to the event.
	 *
	 * @since 1.7.0
	 *
	 * @param int    $post_id  The id to save the meta field too.
	 * @param string $local_id The local id of the Facebook Page to save.
	 *
	 * @return boolean Whether the Facebook Page local id was saved to the event.
	 */
	public function save_page_local_id_to_post( $post_id, $local_id ) {
		return update_post_meta( $post_id, $this->page_local_id_meta_field_name, $local_id );
	}

	/**
	 * Set an page Access Data with the provided id.
	 *
	 * @since 1.7.0
	 *
	 * @param string $local_id     The local id of the Facebook Page to save.
	 * @param string $access_token The Facebook Page access token.
	 * @param string $expiration   The expiration in seconds as provided by the server.
	 */
	public function set_page_access_by_id( $local_id, $access_token, $expiration ) {
		$page_data                 = $this->get_page_by_id( $local_id );
		$page_data['access_token'] = esc_attr( $access_token );
		$page_data['expiration']   = esc_attr( $expiration );

		$this->set_page_by_id( $page_data );
	}

	/**
	 * Save a Facebook Page using ajax.
	 *
	 * @since 1.7.0
	 *
	 * @param string $nonce The save action nonce to check.
	 *
	 * @return string An html message for success or failure when saving.
	 */
	public function save_page( $nonce ) {
		if ( ! $this->check_ajax_nonce( Settings::$save_action, $nonce ) ) {
			return false;
		}

		$local_id  = tribe_get_request_var( 'local_id' );
		// If there is no local id fail the request.
		if ( empty( $local_id ) ) {
			$error_message = _x(
				'The local id to save the Facebook Page is missing.',
				'The local id for the facebook page is missing error message.',
				'events-virtual'
			);
			tribe( Template_Modifications::class )->get_settings_message_template( $error_message, 'error' );

			wp_die();
		}

		$page_name = tribe_get_request_var( 'page_name' );
		$page_id   = tribe_get_request_var( 'page_id' );
		// If there is no page name or page id fail the request.
		if ( empty( $page_name ) || empty( $page_id ) ) {
			$error_message = _x(
				'The Facebook Page Name or ID is missing.',
				'Facebook Page Name or ID is missing error message.',
				'events-virtual'
			);
			tribe( Template_Modifications::class )->get_settings_message_template( $error_message, 'error' );

			wp_die();
		}

		// If no page, setup initial fields
		$page_data = $this->get_page_by_id( $local_id );
		if ( empty( $page_data['local_id'] ) ) {
			$page_data = [
				'local_id'     => esc_attr( $local_id ),
				'name'         => esc_attr( $page_name ),
				'page_id'      => esc_attr( $page_id ),
				'access_token' => '',
				'expiration'   => '',
			];
		} else {
			// Otherwise update an existing page.
			$page_data['name']    = esc_attr( $page_name );
			$page_data['page_id'] = esc_attr( $page_id );
		}

		$this->set_page_by_id( $page_data );

		$message = _x(
			'Facebook Page Saved.',
			'Facebook Page is saved to the options.',
			'events-virtual'
		);
		tribe( Template_Modifications::class )->get_settings_message_template( $message );

		$page = $this->get_page_by_id( $local_id );
		tribe( Template_Modifications::class )->get_page_fields( $local_id, $page );

		wp_die();
	}

	/**
	 * Get the video live stream permalink.
	 *
	 * @since 1.7.0
	 *
	 * @param int $video_id The Facebook video id.
	 *
	 * @return string The expiration, either never or a timestamp.
	 */
	public function get_access_expiration( $access_token ) {
		$expiration = '';

		$this->get(
			$this->get_access_expiration_url_with_query_strings( $access_token ),
			[]
	    )->then(
			function ( array $response ) use ( &$expiration ) {

				$body = json_decode( wp_remote_retrieve_body( $response ) );
				if ( empty( $body ) ) {
				   do_action( 'tribe_log', 'error', __CLASS__, [
				       'action'   => __METHOD__,
				       'message'  => 'Facebook API response is missing the required information to get the video permalink.',
				       'response' => $body,
				   ] );

				   return false;
				}

				  if ( ! isset( $body->data->expires_at ) ) {
				      return false;
				  }

				  $expiration = $body->data->expires_at;

				  return true;
			}
		)->or_catch(
			function ( \WP_Error $error ) {
			  do_action( 'tribe_log', 'error', __CLASS__, [
			      'action'  => __METHOD__,
			      'code'    => $error->get_error_code(),
			      'message' => $error->get_error_message(),
			  ] );
			}
		);

		if( 0 === $expiration) {
			return 'never';
		}


		return $expiration;
	}

	/**
	 * Save a Facebook Page access token using ajax.
	 *
	 * @since 1.7.0
	 *
	 * @param string $nonce The save access action nonce to check.
	 *
	 * @return string An html message for success or failure when saving.
	 */
	public function save_access_token( $nonce ) {
		if ( ! $this->check_ajax_nonce( Settings::$save_access_action, $nonce ) ) {
			return false;
		}

		$local_id     = tribe_get_request_var( 'local_id' );
		$page_id      = tribe_get_request_var( 'page_id' );
		$access_token = tribe_get_request_var( 'access_token' );
		// If missing information fail the request.
		if ( empty( $local_id ) || empty( $page_id ) || empty( $access_token ) ) {
			$error_message = _x(
				'The Facebook Page ID, local ID, or access token is missing.',
				'Facebook Page ID, local ID, or access token is missing error message.',
				'events-virtual'
			);
			tribe( Template_Modifications::class )->get_settings_message_template( $error_message, 'error' );

			wp_die();
		}

		// Check if the page exists.
		$page_data = $this->get_page_by_id( $local_id );
		if ( empty( $page_data ) ) {
			$message = _x(
				'No Facebook Page found to update.',
				'Error message if the Facebook Page was not found in the options.',
				'events-virtual'
			);
			tribe( Template_Modifications::class )->get_settings_message_template( $message, 'error' );

			wp_die();
		}

		// Get the access token expiration.
		$expiration = $this->get_access_expiration( $access_token );

		$this->set_page_access_by_id( $local_id, $access_token, $expiration );

		$message = _x(
			'Facebook Page access token saved.',
			'Facebook Page is saved to the options.',
			'events-virtual'
		);
		tribe( Template_Modifications::class )->get_settings_message_template( $message );

		$page = $this->get_page_by_id( $local_id );
		tribe( Template_Modifications::class )->get_page_fields( $local_id, $page );

		wp_die();
	}

	/**
	 * Clear a Facebook Page access token using ajax.
	 *
	 * @since 1.7.0
	 *
	 * @param string $nonce The clear access action nonce to check.
	 *
	 * @return string An html message for success or failure when clearing.
	 */
	public function clear_access_token( $nonce ) {
		if ( ! $this->check_ajax_nonce( Settings::$clear_access_action, $nonce ) ) {
			return false;
		}

		$local_id     = tribe_get_request_var( 'local_id' );
		// If missing information fail the request.
		if ( empty( $local_id ) ) {
			$error_message = _x(
				'No access token to clear as the the Facebook Page local ID is missing.',
				'Facebook Page ID local ID is missing error message.',
				'events-virtual'
			);
			tribe( Template_Modifications::class )->get_settings_message_template( $error_message, 'error' );

			wp_die();
		}

		// Check if the page exists.
		$page_data = $this->get_page_by_id( $local_id );
		if ( empty( $page_data ) ) {
			$message = _x(
				'No access token is cleared as no Facebook Page was found to update.',
				'Error message if the Facebook Page was not found in the options.',
				'events-virtual'
			);
			tribe( Template_Modifications::class )->get_settings_message_template( $message, 'error' );

			wp_die();
		}

		$this->set_page_access_by_id( $local_id, '', '' );

		$message = _x(
			'Facebook Page access token cleared.',
			'Facebook Page access token is cleared message.',
			'events-virtual'
		);
		tribe( Template_Modifications::class )->get_settings_message_template( $message );

		$page = $this->get_page_by_id( $local_id );
		tribe( Template_Modifications::class )->get_page_fields( $local_id, $page );

		wp_die();
	}

	/**
	 * Add a Facebook Page fields using ajax.
	 *
	 * @since 1.7.0
	 *
	 * @param string $nonce The add action nonce to check.
	 *
	 * @return string An html message for success or failure when adding.
	 */
	public function add_page( $nonce ) {
		if ( ! $this->check_ajax_nonce( Settings::$add_action, $nonce ) ) {
			return false;
		}

		$message = _x(
			'Facebook Page fields added.',
			'Facebook Page new fields are added message.',
			'events-virtual'
		);
		tribe( Template_Modifications::class )->get_settings_message_template( $message );

		// Add empty fields template
		tribe( Template_Modifications::class )->get_page_fields(
			$this->get_unique_id(),
			[
				'name'         => '',
				'page_id'      => '',
				'access_token' => '',
				'expiration'   => '',
			]
		);

		wp_die();
	}

	/**
	 * Delete a Facebook Page using ajax.
	 *
	 * @since 1.7.0
	 *
	 * @param string $nonce The delete action nonce to check.
	 *
	 * @return string An html message for success or failure when deleting.
	 */
	public function delete_page( $nonce ) {
		if ( ! $this->check_ajax_nonce( Settings::$delete_action, $nonce ) ) {
			return false;
		}

		$local_id = tribe_get_request_var( 'local_id' );
		// If local id fail the request.
		if ( empty( $local_id )) {
			$error_message = _x(
				'The Facebook Page local id is missing and cannot be deleted.',
				'Facebook Page local id missing when trying to delete error message.',
				'events-virtual'
			);
			tribe( Template_Modifications::class )->get_settings_message_template( $error_message, 'error' );

			wp_die();
		}

		$page_data = $this->get_page_by_id( $local_id );
		if ( empty( $page_data ) ) {
			$message = _x(
				'No Facebook Page Found to Delete.',
				'Error message if the Facebook Page was not found in the options.',
				'events-virtual'
			);
			tribe( Template_Modifications::class )->get_settings_message_template( $message, 'error' );

			wp_die();
		}

		$this->delete_page_by_id( $local_id );

		$message = _x(
			'Facebook Page Deleted.',
			'Message when a Facebook Page is deleted from the options table.',
			'events-virtual'
		);
		tribe( Template_Modifications::class )->get_settings_message_template( $message );

		wp_die();
	}

	/**
	 * Get a unique Id.
	 *
	 * @since 1.7.0
	 *
	 * @return string A unique id to use as the local id.
	 */
	public function get_unique_id() {
		return uniqid();
	}

	/**
	 * Check if a nonce is valid from a list of actions.
	 *
	 * @since 1.7.0
	 *
	 * @param string $nonce The nonce to check.
	 *
	 * @return bool Whether the nonce is valid or not.
	 */
	protected function is_valid_nonce( $nonce ) {
		/**
		 * Filters a list of Facebook ajax nonce actions.
		 *
		 * @since 1.7.0
		 *
		 * @param array<string,callable> A map from the nonce actions to the corresponding handlers.
		 */
		$actions = apply_filters( 'tribe_events_virtual_meetings_facebook_actions', [] );

		foreach ( $actions as $action => $callback ) {
			if ( $this->check_ajax_nonce( $action, $nonce ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the message to content for no Facebook App ID.
	 *
	 * @since 1.8.0
	 *
	 * @return array<string|mixed> An array of the content for the template to display no Facebook App ID.
	 */
	public function get_no_facebook_app_id_message_content() {
		return [
			'path'  => 'virtual-metabox/facebook/autodetect-message',
			'field' => [
				'classes_wrap' => [ 'tribe-dependent', 'tribe-events-virtual-meetings-autodetect-facebook-video__message-wrap', 'error', 'inline' ],
				'message'      => sprintf(
					'%1$s <a href="%2$s" target="_blank">%3$s</a> %4$s',
					esc_html_x(
						'No connected Facebook Pages found. You must',
					'The start of the message for smart url/autodetect when there is no Facebook App ID.',
					'events-virtual'
					),
					Settings::admin_url(),
					esc_html_x(
						'connect a Facebook App',
						'The link text in message for smart url/autodetect when there is no Facebook App ID.',
						'events-virtual'
					),
					esc_html_x(
						'to your site before you can add a Facebook video to an event.',
						'The end of the message for smart url/autodetect when there is no Facebook App ID.',
						'events-virtual'
					)
				),
				'wrap_attrs'   => [
					'data-depends'   => '#tribe-events-virtual-autodetect-source',
					'data-condition' => Facebook_Meta::$autodetect_fb_video_id,
				],
			]
		];
	}
}
