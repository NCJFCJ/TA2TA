<?php
/**
 * Handles the interaction w/ Google API.
 *
 * @since 1.11.0
 *
 * @package Tribe\Events\Virtual\Meetings\Google
 */

namespace Tribe\Events\Virtual\Meetings\Google;

use Tribe\Events\Virtual\Encryption;
use Tribe\Events\Virtual\Template_Modifications;

/**
 * Class Api
 *
 * @since 1.11.0
 *
 * @package Tribe\Events\Virtual\Meetings\Google
 */
class Api extends Account_API {

	/**
	 * {@inheritDoc}
	 */
	public static $api_name = 'Google';

	/**
	 * {@inheritDoc}
	 */
	public static $api_id = 'google';

	/**
	 * The base URL of the Google REST API, v3.
	 *
	 * @since 1.11.0
	 *
	 * @var string
	 */
	public static $api_base = 'https://www.googleapis.com/calendar/v3/calendars/';

	/**
	 * The url to connect to the Google Calendar API for events.
	 *
	 * @since 1.11.0
	 *
	 * @var string
	 */
	public static $api_calendar_url_with_placeholders = 'https://www.googleapis.com/calendar/v3/calendars/%%CALENDARID%%/events/%%EVENTID%%';

	/**
	 * The Google Calendar ID.
	 *
	 * @since 1.11.0
	 *
	 * @var string
	 */
	public static $api_calendar_id = 'primary';

	/**
	 * The User URL of the Google REST API, v3.
	 *
	 * @since 1.11.0
	 *
	 * @var string
	 */
	public static $user_base = 'https://www.googleapis.com/oauth2/v3/userinfo';

	/**
	 * The Encryption provider.
	 *
	 * @since 1.11.0
	 *
	 * @var Encryption
	 */
	public $encryption;

	/**
	 * Api constructor.
	 *
	 * @since 1.11.0
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
				$body_set = $this->has_proper_response_body( $body, [ 'access_token', 'expires_in' ] );
				if ( ! $body_set ) {
					do_action( 'tribe_log', 'error', __CLASS__, [
						'action'   => __METHOD__,
						'message'  => 'Google API access token refresh response is malformed.',
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
	 * Get the Google Event by ID and Return the Data.
	 *
	 * @since 1.11.0
	 *
	 * @param string $google_event_id The Google Event ID.
	 *
	 * @return array An array of data from the Google API.
	 */
	public function fetch_event_data( $google_event_id ) {
		if ( ! $this->get_token_authorization_header() ) {
			return [];
		}

		$data = [];

		$this->get(
			$this->get_calendar_api_url( $google_event_id, true ),
			[
				'headers' => [
					'Authorization' => $this->get_token_authorization_header(),
					'Content-Type'  => 'application/json; charset=utf-8',
				],
				'body'    => null,
			],
			200
		)->then(
			function ( array $response ) use ( &$data ) {

				$body     = json_decode( wp_remote_retrieve_body( $response ), true );
				$body_set = $this->has_proper_response_body( $body );
				if ( ! $body_set ) {
					do_action( 'tribe_log', 'error', __CLASS__, [
						'action'   => __METHOD__,
						'message'  => 'Google API meetings is response is malformed.',
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

		$this->get(
			self::$user_base,
			[
				'headers' => [
					'Authorization' => $this->get_token_authorization_header( $access_token ),
					'Content-Type'  => 'application/json; charset=utf-8',
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
						'message'  => 'Google API user response is malformed.',
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
	 * @since 1.11.0
	 *
	 * @return array An array of users from the Google API.
	 */
	public function fetch_users() {
		if ( ! $this->get_token_authorization_header() ) {
			return [];
		}

		$args = [
			'max' => 500,
		];

		/**
		 * Filters the arguments for fetching users.
		 *
		 * @since 1.11.0
		 *
		 * @param array<string|string> $args The default arguments to fetch users.
		 */
		$args = (array) apply_filters( 'tec_events_virtual_google_get_users_arguments', $args );

		// Get the initial page of users.
		$users = $this->fetch_users_with_args( $args );

		return $users;
	}

	/**
	 * Get the List of Users by arguments.
	 *
	 * @since 1.11.0
	 *
	 * @return array An array of data from the Google API.
	 */
	public function fetch_users_with_args( $args ) {
		if ( ! $this->get_token_authorization_header() ) {
			return [];
		}

		$data = [];

		$this->get(
			self::$user_base,
			[
				'headers' => [
					'Authorization' => $this->get_token_authorization_header(),
					'Content-Type'  => 'application/json; charset=utf-8',
				],
				'body'    => ! empty( $args ) ? $args : null,
			],
			200
		)->then(
			static function ( array $response ) use ( &$data ) {
				$body     = json_decode( wp_remote_retrieve_body( $response ), true );
				$body_set = self::has_proper_response_body( $body );

				if ( ! $body_set ) {
					do_action( 'tribe_log', 'error', __CLASS__, [
						'action'   => __METHOD__,
						'message'  => 'Google API users response is malformed.',
						'response' => $body,
					] );

					return [];
				}

				if ( $body['sub'] ) {
					$data[] = $body;
				} else {
					$data = $body;
				}
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
	 * Get the Google Calendar API url.
	 *
	 * @since 1.11.0
	 *
	 * @param string  $google_event_id         The optional Google Event ID.
	 * @param boolean $include_conference_data Whether to add the query string to generate Meet link.
	 *
	 * @return string The Google Calendar API url to connect with.
	 */
	public static function get_calendar_api_url( $google_event_id = '', $include_conference_data = false ) {
		/**
		 * Allow filtering of the Google Calendar's ID.
		 *
		 * @since 1.11.0
		 *
		 * @param string The Google Calendar ID, default is primary.
		 */
		$calendar_id = apply_filters( 'tec_events_virtual_google_calendar_calendar_id', static::$api_calendar_id );
		if ( empty( $calendar_id ) ) {
			// Calendar ID cannot be empty, add back default if filter removes it.
			$calendar_id = API::$api_calendar_id;
		}

		$api_url = static::get_event_api_url_with_calendar_and_event_id( $calendar_id, $google_event_id );

		if ( empty( $include_conference_data ) ) {
			return $api_url;
		}

		return add_query_arg( [
			'conferenceDataVersion' => 1,
		], $api_url );
	}

	/**
	 * Get the Google Calendar Event API url with the provided calendar id and optional Google event id.
	 *
	 * @since 1.11.0
	 *
	 * @param string $calendar_id     The Google Calendar ID, default is primary.
	 * @param string $google_event_id The optional Google Event ID.
	 *
	 * @return string The Google Calendar Event API url with filled in placeholders.
	 */
	protected static function get_event_api_url_with_calendar_and_event_id( $calendar_id, $google_event_id = '' ) {
		/**
		 * Allow filtering of the Google Calendar's Event API url.
		 *
		 * @since 1.11.0
		 *
		 * @param string The url for Google Calendar's Event API.
		 */
		$api_url = apply_filters( 'tec_events_virtual_google_calendar_api_url_with_placeholder', static::$api_calendar_url_with_placeholders );

		$search = [
			'%%CALENDARID%%',
			'%%EVENTID%%',
		];

		$replace = [
			$calendar_id,
			$google_event_id,
		];

		return str_replace( $search, $replace, $api_url );
	}

	/**
	 * Get the no Google account found message.
	 *
	 * @since 1.11.0
	 *
	 * @return string The message returned when no account found.
	 */
	public function get_no_account_message() {
		return sprintf(
			'%1$s <a href="%2$s" target="_blank">%3$s</a>',
			esc_html_x(
				'No Google account found.',
			'The start of the message for smart url/autodetect when there is no Google account found.',
			'events-virtual'
			),
			Settings::admin_url(),
			esc_html_x(
				'Please check your account connection.',
				'The link in of the message for smart url/autodetect when no Google account is found.',
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
		if ( ! isset( $body['error']['message'] ) ) {
			return $api_message;
		}

		$api_message .=  ' API Error: ' . $body['error']['message'];

		return $api_message;
	}
}
