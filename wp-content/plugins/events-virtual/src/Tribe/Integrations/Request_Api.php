<?php
/**
 * Abstract Class to Manage API calls.
 *
 * @since 1.9.0
 *
 * @package Tribe\Events\Virtual\Integrations
 */

namespace Tribe\Events\Virtual\Integrations;

use WP_Error;

/**
 * Abstract Class Request_Api
 *
 * @since 1.9.0
 *
 * @package Tribe\Events\Virtual\Integrations
 */
abstract class Request_Api {

	/**
	 * The name of the API
	 *
	 * @since 1.9.0
	 *
	 * @var string
	 */
	public static $api_name;

	/**
	 * The id of the API
	 *
	 * @since 1.9.0
	 *
	 * @var string
	 */
	public static $api_id;

	/**
	 * The base URL of the API Call.
	 *
	 * @since 1.9.0
	 *
	 * @var string
	 */
	public static $api_base;

	/**
	 * The current API access token.
	 *
	 * @since 1.9.0
	 *
	 * @var string
	 */
	protected $token;

	/**
	 * The current API refresh token.
	 *
	 * @since 1.9.0
	 *
	 * @var string
	 */
	protected $refresh_token;

	/**
	 * Expected response code for GET requests.
	 *
	 * @since 1.9.0
	 *
	 * @var integer
	 */
	const GET_RESPONSE_CODE = 200;

	/**
	 * Expected response code for POST requests.
	 *
	 * @since 1.9.0
	 *
	 * @var integer
	 */
	const POST_RESPONSE_CODE = 200;

	/**
	 * Expected response code for POST OAuth requests.
	 *
	 * @since 1.9.0
	 *
	 * @var integer
	 */
	const OAUTH_POST_RESPONSE_CODE = 200;

	/**
	 * Expected response code for PATCH requests.
	 *
	 * @since 1.9.0
	 *
	 * @var integer
	 */
	const PATCH_RESPONSE_CODE = 204;

	/**
	 * Expected response code for PUT requests.
	 *
	 * @since 1.9.0
	 *
	 * @var integer
	 */
	const PUT_RESPONSE_CODE = 200;

	/**
	 * Makes a request to the an API.
	 *
	 * @since 1.9.0
	 *
	 * @param string $url         The URL to make the request to.
	 * @param array  $args        An array of arguments for the request. Should include 'method' (POST/GET/PATCH, etc).
	 * @param int    $expect_code The expected response code, if not met, then the request will be considered a failure.
	 *                            Set to `null` to avoid checking the status code.
	 *
	 * @return Api_Response An API response to act upon the response result.
	 */
	protected function request( $url, array $args, $expect_code = self::GET_RESPONSE_CODE ) {
		$app_id = static::$api_id;

		/**
		 * Filters the response for an API request to prevent the response from actually happening.
		 *
		 * @since 1.9.0
		 *
		 * @param null|Api_Response|WP_Error|mixed $response    The response that will be returned. A non `null` value
		 *                                                       here will short-circuit the response.
		 * @param string                            $url         The full URL this request is being made to.
		 * @param array<string,mixed>               $args        The request arguments.
		 * @param int                               $expect_code The HTTP response code expected for this request.
		 */
		$response = apply_filters( 'tec_events_virtual_meetings_api_post_response', null, $url, $args, $expect_code );

		/**
		 * Filters the response for an API request by API id to prevent the response from actually happening.
		 *
		 * @since 1.9.0
		 *
		 * @param null|Api_Response|WP_Error|mixed $response    The response that will be returned. A non `null` value
		 *                                                       here will short-circuit the response.
		 * @param string                            $url         The full URL this request is being made to.
		 * @param array<string,mixed>               $args        The request arguments.
		 * @param int                               $expect_code The HTTP response code expected for this request.
		 */
		$response = apply_filters( "tec_events_virtual_meetings_{$app_id}_api_post_response", $response, $url, $args, $expect_code );

		if ( null !== $response ) {
			return Api_Response::ensure_response( $response );
		}

		$response = wp_remote_request( $url, $args );

		if ( $response instanceof WP_Error ) {
			$error_message = $response->get_error_message();

			do_action(
				'tribe_log',
				'error',
				__CLASS__,
				[
					'action'  => __METHOD__,
					'code'    => $response->get_error_code(),
					'message' => $error_message,
					'method'  => $args['method'],
				]
			);

			$user_message = sprintf(
				// translators: %1$s: the API name, %2$s: the error as returned from the API.
				_x(
					'Error while trying to communicate with %1$s API: %2$s. Please try again in a minute.',
					'The prefix of a message reporting a %1$s API communication error, the placeholder is for the error.',
					'events-virtual'
				),
				static::$api_name,
				$error_message
			);
			tribe_transient_notice(
				"events-virtual-{$app_id}-request-error",
				'<p>' . esc_html( $user_message ) . '</p>',
				[ 'type' => 'error' ],
				60
			);

			return new Api_Response( $response );
		}

		$response_code = wp_remote_retrieve_response_code( $response );

		if ( null !== $expect_code && $expect_code !== $response_code ) {

			// Add error message from the API if available.
			$api_message = '';
			$body        = json_decode( wp_remote_retrieve_body( $response ), true );
			$body_set    = $this->has_proper_response_body( $response );
			if ( $body_set ) {
				$api_message = isset( $body['message'] ) ? ' API Message: ' . $body['message'] : '';

				/**
				 * Filters the API error message.
				 *
				 * @since 1.11.0
				 *
				 * @param string              $url        The full URL this request is being made to.
				 * @param array<string,mixed> $body       The json_decoded request body.
				 * @param Api_Response        $response   The response that will be returned. A non `null` value
				 *                                        here will short-circuit the response.
				 */
				$api_message = apply_filters( 'tec_events_virtual_meetings_api_error_message', $api_message, $body, $response );
			}

			$data = [
				'action'        => __METHOD__,
				'message'       => 'Response code is not the expected one.' . $api_message ,
				'expected_code' => $expect_code,
				'response_code' => $response_code,
				'api_method'    => $args['method'],
				'api_response'  => json_decode( wp_remote_retrieve_body( $response ), true ),
			];
			do_action( 'tribe_log', 'error', __CLASS__, $data );

			$user_message = sprintf(
				// translators: the placeholders are, %1$s: the API name, %2$s: the expected code, and %3$s: the actual response code.
				_x(
					'%1$s API response is not the expected one, expected %2$s, received %3$s. Please, try again in a minute.',
					'The message reporting an API unexpected response code, placeholders are the AP name and the codes.',
					'events-virtual'
				),
				static::$api_name,
				$expect_code,
				$response_code
			);
			tribe_transient_notice(
				"events-virtual-{$app_id}-response-error",
				'<p>' . esc_html( $user_message ) . '</p>',
				[ 'type' => 'error' ],
				60
			);

			return new Api_Response( new WP_Error( $response_code, 'Response code is not the expected one.', $data ) );
		}

		return new Api_Response( $response );
	}

	/**
	 * Makes a POST request to an API.
	 *
	 * @since 1.9.0
	 *
	 * @param string $url         The URL to make the request to.
	 * @param array  $args        An array of arguments for the request.
	 * @param int    $expect_code The expected response code, if not met, then the request will be considered a failure.
	 *                            Set to `null` to avoid checking the status code.
	 *
	 * @return Api_Response An API response to act upon the response result.
	 */
	public function post( $url, array $args, $expect_code = self::POST_RESPONSE_CODE ) {
		$args['method'] = 'POST';

		return $this->request( $url, $args, $expect_code );
	}

	/**
	 * Makes a PATCH request to an API.
	 *
	 * @since 1.9.0
	 *
	 * @param string $url         The URL to make the request to.
	 * @param array  $args        An array of arguments for the request.
	 * @param int    $expect_code The expected response code, if not met, then the request will be considered a failure.
	 *                            Set to `null` to avoid checking the status code.
	 *
	 * @return Api_Response An API response to act upon the response result.
	 */
	public function patch( $url, array $args, $expect_code = self::PATCH_RESPONSE_CODE ) {
		$args['method'] = 'PATCH';

		return $this->request( $url, $args, $expect_code );
	}

	/**
	 * Makes a PUT request to an API.
	 *
	 * @since 1.9.0
	 *
	 * @param string $url         The URL to make the request to.
	 * @param array  $args        An array of arguments for the request.
	 * @param int    $expect_code The expected response code, if not met, then the request will be considered a failure.
	 *                            Set to `null` to avoid checking the status code.
	 *
	 * @return Api_Response An API response to act upon the response result.
	 */
	public function put( $url, array $args, $expect_code = self::PUT_RESPONSE_CODE ) {
		$args['method'] = 'PUT';

		return $this->request( $url, $args, $expect_code );
	}

	/**
	 * Makes a GET request to an API.
	 *
	 * @since 1.9.0
	 *
	 * @param string $url         The URL to make the request to.
	 * @param array  $args        An array of arguments for the request.
	 * @param int    $expect_code The expected response code, if not met, then the request will be considered a failure.
	 *                            Set to `null` to avoid checking the status code.
	 *
	 * @return Api_Response An API response to act upon the response result.
	 */
	public function get( $url, array $args, $expect_code = self::GET_RESPONSE_CODE ) {
		$args['method'] = 'GET';

		return $this->request( $url, $args, $expect_code );
	}

	/**
	 * Check if a response body has proper attributes.
	 *
	 * @since 1.13.0
	 *
	 * @param array<string|mixed>  $body              A response body array.
	 * @param array<string|string> $additional_checks An array of keys to check for in the body array.
	 *
	 * @return boolean Whether the response body has the proper attributes.
	 */
	public static function has_proper_response_body( $body, $additional_checks = [] ) {
		if ( empty( $body ) || ! is_array( $body ) ) {
			return false;
		}

		if ( empty( $additional_checks ) ) {
			return true;
		}

		// Additional array keys to check for in the body response.
		if ( array_diff_key( array_flip( $additional_checks ), $body ) ) {
			return false;
		}

		return true;
	}
}
