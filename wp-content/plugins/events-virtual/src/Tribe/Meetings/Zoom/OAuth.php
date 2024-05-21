<?php
/**
 * Handles OAuth-based authentication requests for the Zoom API.
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual\Meetings\Zoom
 */

namespace Tribe\Events\Virtual\Meetings\Zoom;

/**
 * Class OAuth
 *
 * @since   1.0.0
 * @deprecated 1.13.0 - Functionality moved to API and Account_API Classes.
 *
 * @package Tribe\Events\Virtual\Meetings\Zoom
 */
class OAuth {
	/**
	 * The name of the action used to generate the OAuth authentication URL.
	 *
	 * @since 1.0.0
	 * @deprecated 1.13.0 - Use API::$authorize_nonce_action
	 *
	 * @var string
	 */
	public static $authorize_nonce_action = 'events-virtual-meetings-zoom-oauth-authorize';

	/**
	 * The name of the action used to generate the OAuth deauthorization URL.
	 *
	 * @since 1.0.0
	 * @deprecated 1.13.0 - No replacement.
	 *
	 * @var string
	 */
	public static $deauthorize_nonce_action = 'events-virtual-meetings-zoom-oauth-deauthorize';

	/**
	 * The base URL to request an access token to Zoom API.
	 *
	 * @since 1.0.0
	 * @since 1.4.0 - update to use new Zoom App endpoint.
	 * @deprecated 1.13.0 - Use URL->request_url;
	 *
	 * @var string
	 *
	 * @link  https://marketplace.zoom.us/docs/guides/auth/oauth
	 */
	public static $token_request_url = 'https://whodat.theeventscalendar.com/oauth/zoom/v2/token';

	/**
	 * The base URL to request an access token to Zoom API.
	 *
	 * @since 1.0.0
	 * @deprecated 1.13.0 - No replacement.
	 *
	 * @var string
	 *
	 * @link  https://marketplace.zoom.us/docs/guides/auth/oauth
	 */
	public static $legacy_token_request_url = 'https://zoom.us/oauth/token';

	/**
	 * an instance of the Zoom API handler.
	 *
	 * @deprecated 1.13.0 - Functionality moved to API and Account_API Classes.
	 *
	 * @var Api
	 */
	protected $api;

	/**
	 * OAuth constructor.
	 *
	 * @deprecated 1.13.0 - Functionality moved to API and Account_API Classes.
	 *
	 * @param Api $api An instance of the Zoom API handler.
	 */
	public function __construct( Api $api ) {
		$this->api = $api;
	}

	/**
	 * Handles an OAuth authorization return request.
	 *
	 * The method will `wp_die` if the nonce is not valid.
	 *
	 * @since 1.0.0
	 * @since 1.9.0 - Remove legacy support.
	 * @deprecated 1.13.0 - Use API->handle_auth_request( $nonce )
	 *
	 * @param string|null $nonce The nonce string to authorize the authorization request.
	 *
	 * @return boolean Whether the authorization request was handled.
	 */
	public function handle_auth_request( $nonce = null ) {
		_deprecated_function( __METHOD__, '1.13.1', 'Use API->handle_auth_request( $nonce )' );

		if ( ! wp_verify_nonce( $nonce, self::$authorize_nonce_action ) ) {
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
			$this->api->save_account( [ 'body' => base64_decode( $service_response_body ) ] );

			$handled = true;
		}

		wp_safe_redirect( Settings::admin_url() );

		return $handled;
	}

	/**
	 * Returns the full OAuth URL to authorize the application.
	 *
	 * @since 1.0.0
	 * @deprecated 1.13.0 - Use API->authorize_url()
	 *
	 * @return string The full OAuth URL to authorize the application.
	 */
	public function authorize_url() {
		_deprecated_function( __METHOD__, '1.13.1', 'Use API->authorize_url()' );

		// Use the `state` query arg as described in Zoom API documentation.
		$authorize_url = add_query_arg(
			[
				'state' => wp_create_nonce( self::$authorize_nonce_action ),
			],
			admin_url()
		);

		return $authorize_url;
	}
}
