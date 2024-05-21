<?php
/**
 * Handles the interaction w/ Zoom API.
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual\Meetings\Zoom
 */

namespace Tribe\Events\Virtual\Meetings\Zoom;

use Tribe\Events\Virtual\Encryption;
use Tribe\Events\Virtual\Event_Meta as Virtual_Events_Meta;
use Tribe\Events\Virtual\Meetings\Zoom\Event_Meta as Zoom_Meta;
use Tribe\Events\Virtual\Template_Modifications;

/**
 * Class Api
 *
 * @since  1.0.0
 * @since  1.5.0 - Extends Account_API Class to support multiple accounts.
 * @since  1.9.0 - Extends Request_Api Class a shared class for connections to an API.
 *
 * @package Tribe\Events\Virtual\Meetings\Zoom
 */
class Api extends Account_API {

	/**
	 * {@inheritDoc}
	 */
	public static $api_name = 'Zoom';

	/**
	 * {@inheritDoc}
	 */
	public static $api_id = 'zoom';

	/**
	 * The base URL of the Zoom REST API, v2.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public static $api_base = 'https://api.zoom.us/v2/';

	/**
	 * The current Zoom API refresh token.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $refresh_token;

	/**
	 * {@inheritDoc}
	 */
	const POST_RESPONSE_CODE = 201;

	/**
	 * Regex to get the Meeting/Webinar ID from Zoom url.
	 *
	 * @since 1.8.0
	 *
	 * @var string
	 */
	protected $regex_meeting_id_url = '|(\bzoom\b).+?\/(?<id>[^\D]+)|';

	/**
	 * Api constructor.
	 *
	 * @since 1.0.0
	 * @since 1.4.0  - Add encryption handler.
	 * @since 1.5.0 - Add Account_API to support multiple accounts.
	 * @since 1.9.0 - Move request methods to a new class and load through construct.
	 * @since 1.13.0 - Add the Action class.
	 *
	 * @param Encryption             $encryption             An instance of the Encryption handler.
	 * @param Template_Modifications $template_modifications An instance of the Template_Modifications handler.
	 * @param Actions                $actions                An instance of the Actions name handler.
	 * @param URL                    $url                    An instance of the URL handler.
	 */
	public function __construct( Encryption $encryption, Template_Modifications $template_modifications, Actions $actions, Url $url ) {
		$this->encryption             = ( ! empty( $encryption ) ? $encryption : tribe( Encryption::class ) );
		$this->template_modifications = $template_modifications;
		$this->actions                = $actions;
		$this->url                    = $url;

		// Attempt to load an account.
		$this->load_account();
	}

	/**
	 * {@inheritDoc}
	 */
	public function refresh_access_token( $id, $refresh_token ) {
		$refreshed = false;

		$this->post(
			$this->url::to_refresh(),
			[
				'body'    => [
					'grant_type'    => 'refresh_token',
					'refresh_token' => $refresh_token,
				],
			],
			200
		)->then(
			function ( array $response ) use ( &$id, &$refreshed ) {

				$body     = json_decode( wp_remote_retrieve_body( $response ), true );
				$body_set = $this->has_proper_response_body( $body, [ 'access_token', 'refresh_token', 'expires_in' ] );
				if ( ! $body_set ) {
					do_action( 'tribe_log', 'error', __CLASS__, [
						'action'   => __METHOD__,
						'message'  => 'Zoom API access token refresh response is malformed.',
						'response' => $body,
					] );

					return false;
				}

				$refreshed = $this->save_access_and_expiration( $id, $response );

				return $refreshed;
			}
		);

		return $refreshed;
	}

	/**
	 * Get the Meeting by ID from Zoom and Return the Data.
	 *
	 * @since 1.0.4
	 *
	 * @param int $zoom_meeting_id The Zoom meeting id.
	 * @param string $meeting_type The type of meeting (Meeting or Webinar) to fetch the information for.
	 *
	 * @return array An array of data from the Zoom API.
	 */
	public function fetch_meeting_data( $zoom_meeting_id, $meeting_type ) {
		if ( ! $this->get_token_authorization_header() ) {
			return [];
		}

		$data = [];

		$api_endpoint = Meetings::$meeting_type === $meeting_type
			? Meetings::$api_endpoint
			: Webinars::$api_endpoint;

		$this->get(
			self::$api_base . "{$api_endpoint}/{$zoom_meeting_id}",
			[
				'headers' => [
					'Authorization' => $this->get_token_authorization_header(),
					'Content-Type'  => 'application/json; charset=utf-8',
					'accept'        => 'application/json;',
				],
				'body'    => null,
			],
			200
		)->then(
			function ( array $response ) use ( &$data ) {

				$body     = json_decode( wp_remote_retrieve_body( $response ), true );
				$body_set = $this->has_proper_response_body( $body, [ 'join_url' ] );
				if ( ! $body_set ) {
					do_action( 'tribe_log', 'error', __CLASS__, [
						'action'   => __METHOD__,
						'message'  => 'Zoom API meetings settings response is malformed.',
						'response' => $body,
					] );

					return [];
				}
				$data = $body;
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

		return $data;
	}

	/**
	 * {@inheritDoc}
	 */
	public function fetch_user( $user_id = 'me', $settings = false, $access_token = '' ) {
		if ( ! $this->get_token_authorization_header( $access_token ) ) {
			return [];
		}

		// If both user id and settings, add settings to detect webinar support.
		if ( $user_id && $settings ) {
			$user_id = $user_id . '/settings';
		}

		$this->get(
			self::$api_base . 'users/' . $user_id,
			[
				'headers' => [
					'Authorization' => $this->get_token_authorization_header( $access_token ),
					'Content-Type'  => 'application/json; charset=utf-8',
					'accept'        => 'application/json;',
				],
				'body'    => null,
			],
			200
		)->then(
			static function ( array $response ) use ( &$data ) {
				$body     = json_decode( wp_remote_retrieve_body( $response ), true );
				$body_set = self::has_proper_response_body( $body );

				if ( ! $body_set ) {
					do_action( 'tribe_log', 'error', __CLASS__, [
						'action'   => __METHOD__,
						'message'  => 'Zoom API user response is malformed.',
						'response' => $body,
					] );

					return [];
				}
				$data = $body;
			}
		)->or_catch(
			static function ( \WP_Error $error ) {
				do_action( 'tribe_log', 'error', __CLASS__, [
					'action'  => __METHOD__,
					'code'    => $error->get_error_code(),
					'message' => $error->get_error_message(),
				] );
			}
		);

		return $data;
	}

	/**
	 * Get the List of all Users
	 *
	 * @since 1.4.0
	 * @since 1.8.2 - Add pagination support.
	 *
	 * @return array An array of users from the Zoom API.
	 */
	public function fetch_users() {
		if ( ! $this->get_token_authorization_header() ) {
			return [];
		}

		$args = [
			'page_size'   => 300,
			'page_number' => 1,
		];

		/**
		 * Filters the arguments for fetching users.
		 *
		 * @since 1.8.0
		 * @since 1.8.2 Correct duplicated hook name.
		 *
		 * @param array<string|string> $args The default arguments to fetch users.
		 */
		$args = (array) apply_filters( 'tec_events_virtual_zoom_user_get_arguments', $args );

		$page_query_atts = [
			'start' => 1,
			'limit' => 20,
		];
		/**
		 * Filters the attributes for getting all of an account's users with pagination support.
		 *
		 * @since 1.8.2
		 *
		 * @param array<string|string> $args The default attributes to fetch users through pagination.
		 */
		$page_query_atts = (array) apply_filters( 'tec_events_virtual_zoom_user_pagination_attributes', $page_query_atts );

		// Get the initial page of users.
		$users = $this->fetch_users_with_args( $args );

		// Support Pagination of users for accounts with over 300 users.
		if ( isset( $users['page_count'] ) && $users['page_count'] > $page_query_atts['start'] ) {
			// Use the filtered default arguments for the base of pagination queries.
			$page_args = $args;

			// Number of pages to get. If no limit, do the total number of pages.
			// If there is a limit, do the smaller amount between the total number of pages and the limit.
			$pages = $page_query_atts['limit'] ? min( $page_query_atts['start'] + $page_query_atts['limit'], $users['page_count'] + 1 ) : $users['page_count'] + 1;

			for ( $i = $page_query_atts['start'] + 1; $i < $pages; $i ++ ) {
				$page_args['page_number'] = $i;

				$page_of_users = $this->fetch_users_with_args( $page_args );

				if ( ! isset( $page_of_users['users'] ) ) {
					continue;
				}

				// merge in the current page of users to the previous.
				$users['users'] = array_merge( $users['users'], $page_of_users['users'] );
			}
		}

		return $users;
	}

	/**
	 * Get the List of Users by arguments.
	 *
	 * @since 1.8.2
	 *
	 * @return array An array of data from the Zoom API.
	 */
	public function fetch_users_with_args( $args ) {
		if ( ! $this->get_token_authorization_header() ) {
			return [];
		}

		$data = '';

		$this->get(
			self::$api_base . 'users',
			[
				'headers' => [
					'Authorization' => $this->get_token_authorization_header(),
					'Content-Type'  => 'application/json; charset=utf-8',
					'accept'        => 'application/json;',
				],
				'body'    => ! empty( $args ) ? $args : null,
			],
			200
		)->then(
			static function ( array $response ) use ( &$data ) {
				$body     = json_decode( wp_remote_retrieve_body( $response ), true );
				$body_set = self::has_proper_response_body( $body, [ 'users' ] );

				if ( ! $body_set ) {
					do_action( 'tribe_log', 'error', __CLASS__, [
						'action'   => __METHOD__,
						'message'  => 'Zoom API users response is malformed.',
						'response' => $body,
					] );

					return [];
				}
				$data = $body;
			}
		)->or_catch(
			static function ( \WP_Error $error ) {
				do_action( 'tribe_log', 'error', __CLASS__, [
					'action'  => __METHOD__,
					'code'    => $error->get_error_code(),
					'message' => $error->get_error_message(),
				] );
			}
		);

		return $data;
	}

	/**
	 * Get the regex to get the Zoom meeting/webinar id from a url.
	 *
	 * @since 1.8.0
	 *
	 * @return string The regex to get Zoom meeting/webinar id from a url from the filter if a string or the default.
	 */
	public function get_regex_meeting_id_url() {
		/**
		 * Allow filtering of the regex to get Zoom meeting/webinar id from a url.
		 *
		 * @since 1.8.0
		 *
		 * @param string The regex to get Zoom meeting/webinar id from a url.
		 */
		$regex_meeting_id_url = apply_filters( 'tec_events_virtual_zoom_regex_meeting_id_url', $this->regex_meeting_id_url );

		return is_string( $regex_meeting_id_url ) ? $regex_meeting_id_url : $this->regex_meeting_id_url;
	}

	/**
	 * Filter the autodetect source to detect if a Zoom link.
	 *
	 * @since 1.8.0
	 *
	 * @param array<string|mixed> $autodetect   An array of the autodetect defaults.
	 * @param string              $video_url    The url to use to autodetect the video source.
	 * @param string              $video_source The optional name of the video source to attempt to autodetect.
	 * @param \WP_Post|null       $event        The event post object, as decorated by the `tribe_get_event` function.
	 * @param array<string|mixed> $ajax_data    An array of extra values that were sent by the ajax script.
	 *
	 * @return array<string|mixed> An array of the autodetect results.
	 */
	public function filter_virtual_autodetect_zoom( $autodetect, $video_url, $video_source, $event, $ajax_data ) {
		if ( $autodetect['detected'] || $autodetect['guess'] ) {
			return $autodetect;
		}

		// All video sources are checked on the first autodetect run, only prevent checking of this source if it is set.
		if ( ! empty( $video_source ) && Zoom_Meta::$key_source_id !== $video_source ) {
			return $autodetect;
		}

		// If virtual url, fail the request.
		if ( empty( $video_url ) ) {
			$autodetect['message'] = _x( 'No url found. Please enter a Zoom meeting URL or change the selected source.', 'Zoom autodetect missing video url error message.', 'events-virtual' );

			return $autodetect;
		}

		// Attempt to find the Zoom meeting/webinar id from the url.
		preg_match( $this->get_regex_meeting_id_url(), $video_url, $matches );
		$zoom_meeting_id     = isset( $matches['id'] ) ? $matches['id'] : false;
		if ( ! $zoom_meeting_id ) {
			$error_message = _x( 'No Zoom ID found. Please check your meeting URL.', 'No Zoom meeting/webinar ID found for autodetect error message.', 'events-virtual' );
			$autodetect['message'] = $error_message;

			return $autodetect;
		}

		$autodetect['guess'] = Zoom_Meta::$key_source_id;

		// Use the zoom-account if available, otherwise try with the first account stored in the site.
		$accounts = $this->get_list_of_accounts();
		$account_id = empty( $ajax_data['zoom-account'] ) ? key( array_slice( $accounts, 0, 1 ) ) : esc_attr( $ajax_data['zoom-account'] );

		if ( empty( $account_id ) ) {
			$autodetect['message'] = $this->get_no_account_message();

			return $autodetect;
		}

		$this->load_account_by_id( $account_id );
		if ( ! $this->is_ready() ) {
			$autodetect['message'] = $this->get_no_account_message();

			return $autodetect;
		}

		$data = $this->fetch_meeting_data( $zoom_meeting_id, 'meeting' );

		// If not meeting found, test with webinar.
		if ( empty( $data ) ) {
			$data = $this->fetch_meeting_data( $zoom_meeting_id, 'webinar' );
		}

		// If no meeting or webinar found it is because the account is not authorized or does not exist.
		if ( empty( $data ) ) {
			$autodetect['message'] = _x( 'This Zoom meeting could not be found in the selected account. Please select the associated account below and try again.', 'No Zoom meeting or webinar found for autodetect error message.', 'events-virtual' );

			return $autodetect;
		}

		// Set as virtual event and video source to zoom.
		update_post_meta( $event->ID, Virtual_Events_Meta::$key_virtual, true );
		update_post_meta( $event->ID, Virtual_Events_Meta::$key_video_source, Zoom_Meta::$key_source_id );
		$event->virtual_video_source = Zoom_Meta::$key_source_id;

		// Save Zoom data.
		$new_response['body'] = json_encode( $data );
		tribe( Meetings::class )->process_meeting_connection_response( $new_response, $event->ID );

		// Set Zoom as the autodetect source and set up success data and send back to smart url ui.
		update_post_meta( $event->ID, Virtual_Events_Meta::$key_autodetect_source, Zoom_Meta::$key_source_id );
		$autodetect['detected']          = true;
		$autodetect['autodetect-source'] = Zoom_Meta::$key_source_id;
		$autodetect['message']           = _x( 'Zoom meeting successfully connected!', 'Zoom meeting/webinar connected success message.', 'events-virtual' );
		$autodetect['html'] = tribe( Classic_Editor::class )->get_meeting_details( $event, false, $account_id, false );

		return $autodetect;
	}

	/**
	 * Get the no Zoom account found message.
	 *
	 * @since 1.8.0
	 *
	 * @return string The message returned when no account found.
	 */
	protected function get_no_account_message() {
		return sprintf(
			'%1$s <a href="%2$s" target="_blank">%3$s</a>',
			esc_html_x(
				'No Zoom account found. Please check',
			'The start of the message for smart url/autodetect when there is no Zoom account found.',
			'events-virtual'
			),
			Settings::admin_url(),
			esc_html_x(
				'check your account connection.',
			'The link in of the message for smart url/autodetect when no Zoom account is found.',
			'events-virtual'
			)
		);
	}

	/**
	 * Filters the API error message.
	 *
	 * @since 1.11.0
	 *
	 * @param string              $api_message The API error message.
	 * @param array<string,mixed> $body        The json_decoded request body.
	 * @param Api_Response        $response    The response that will be returned. A non `null` value
	 *                                         here will short-circuit the response.
	 *
	 * @return string              $api_message        The API error message.
	 */
	public function filter_api_error_message( $api_message, $body, $response ) {
		if ( ! isset( $body['errors'][0]['message'] ) ) {
			return $api_message;
		}

		$api_message .=  ' API Error: ' . $body['errors'][0]['message'];

		return $api_message;
	}
}
