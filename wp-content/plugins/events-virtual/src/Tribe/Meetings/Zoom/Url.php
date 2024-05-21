<?php
/**
 * Manages the Zoom URLs for the plugin.
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual\Meetings\Zoom
 */

namespace Tribe\Events\Virtual\Meetings\Zoom;

use Tribe\Events\Virtual\Context\Context_Provider;
use Tribe\Events\Virtual\Integrations\Abstract_Url;
use Tribe\Events\Virtual\Plugin;
use Tribe\Events\Virtual\Meetings\Zoom\Event_Meta as Zoom_Event_Meta;

/**
 * Class Url
 *
 * @since   1.0.0
 * @since   1.13.0 - Utilize an abstract class.
 *
 * @package Tribe\Events\Virtual\Meetings\Zoom
 */
class Url extends Abstract_Url {

	/**
	 * An instance of the API OAuth handler.
	 *
	 * @since      1.0.0
	 * @deprecated 1.13.0 - No replacement;
	 *
	 * @var OAuth
	 */
	protected $oauth;

	/**
	 * Url constructor.
	 *
	 * @since 1.13.0
	 * @since 1.13.2 - Remove API dependency to prevent maximum function nesting level.
	 * @since 1.13.3 - Remove OAuth dependency to prevent maximum function nesting level.
	 *
	 * @param Actions $actions An instance of the Zoom Actions handler.
	 */
	public function __construct( Actions $actions ) {
		self::$api_id        = Zoom_Event_Meta::$key_source_id;
		self::$authorize_url = 'https://whodat.theeventscalendar.com/oauth/zoom/v2/authorize';
		self::$refresh_url   = 'https://whodat.theeventscalendar.com/oauth/zoom/v2/token';
		self::$revoke_url    = 'https://whodat.theeventscalendar.com/oauth/zoom/v2/revoke';
		$this->actions       = $actions;
	}

	/**
	 * Returns the URL to authorize the use of the Zoom API.
	 * -Zoom utilizes its own method as it takes different parameters on whodat.
	 *
	 * @since 1.0.0
	 * @since 1.4.0 Add a constant to be able to change the authorize url.
	 * @since 1.13.0 Changed the source of the redirect URI to API class.
	 *
	 * @return string The request URL.
	 *
	 * @link  https://marketplace.zoom.us/docs/guides/auth/oauth
	 */
	public function to_authorize() {
		$license       = get_option( 'pue_install_key_events_virtual' );
		$authorize_url = static::$authorize_url;
		$constant_id   = strtoupper( static::$api_id );

		if ( defined( "TEC_VIRTUAL_EVENTS_{$constant_id}_API_AUTHORIZE_URL" ) ) {
			$authorize_url = constant( "TEC_VIRTUAL_EVENTS_{$constant_id}_API_AUTHORIZE_URL" );
		}

		$real_url = add_query_arg( [
			'key'          => $license ? $license : 'no-license',
			'redirect_uri' => esc_url( $this->get_authorize_url() ),
		], $authorize_url );

		return $real_url;
	}

	/**
	 * Returns the full OAuth URL to authorize the application.
	 *
	 * @since 1.13.2
	 * @since 1.14.0 - Use a constant for 'state' query variable.
	 *
	 * @return string The full OAuth URL to authorize the application.
	 */
	public function get_authorize_url() {
		// Use the `state` query arg as described in Zoom API documentation.
		$authorize_url = add_query_arg(
			[
				Context_Provider::AUTH_STATE_QUERY_VAR => wp_create_nonce( $this->actions::$authorize_nonce_action ),
			],
			admin_url()
		);

		return $authorize_url;
	}

	/**
	 * Returns the URL that should be used to generate a Zoom API webinar link.
	 *
	 * @since 1.1.1
	 *
	 * @param \WP_Post|null $post A post object to generate the webinar for.
	 *
	 * @return string The URL to generate the Zoom Webinar.
	 */
	public function to_generate_webinar_link( \WP_Post $post ) {
		$nonce = wp_create_nonce( $this->actions::$webinar_create_action );

		return add_query_arg( [
				'action'              => 'ev_zoom_webinars_create',
				Plugin::$request_slug => $nonce,
				'post_id'             => $post->ID,
				'_ajax_nonce'         => $nonce,
			], admin_url( 'admin-ajax.php' ) );
	}

	/**
	 * Returns the URL that should be used to remove an event Zoom Webinar URL.
	 *
	 * @since 1.1.1
	 *
	 * @param \WP_Post $post A post object to remove the webinar from.
	 *
	 * @return string The URL to remove the Zoom Webinars.
	 */
	public function to_remove_webinar_link( \WP_Post $post ) {
		$nonce = wp_create_nonce( $this->actions::$webinar_remove_action );

		return add_query_arg( [
				'action'              => 'ev_zoom_webinars_remove',
				Plugin::$request_slug => $nonce,
				'post_id'             => $post->ID,
				'_ajax_nonce'         => $nonce,
			], admin_url( 'admin-ajax.php' ) );
	}

	/**
	 * Returns the URL that should be used to check a Zoom users account type.
	 *
	 * @since 1.8.2
	 *
	 * @return string The URL to validate the Zoom user type.
	 */
	public function to_validate_user_type( \WP_Post $post ) {
		$nonce = wp_create_nonce( $this->actions::$validate_user_action );

		return add_query_arg( [
			'action'              => 'ev_zoom_validate_user_type',
			Plugin::$request_slug => $nonce,
			'post_id'             => $post->ID,
			'_ajax_nonce'         => $nonce,
		], admin_url( 'admin-ajax.php' ) );
	}

	/**
	 * Returns the URL to disconnect from the Zoom API.
	 *
	 * The current version (2.0) of Zoom API does not provide a de-authorization endpoint, as such the best way to
	 * disconnect the application is to de-authorize its access token.
	 * @link       https://marketplace.zoom.us/docs/guides/auth/oauth#revoking
	 *
	 * @since      1.0.0
	 * @deprecated 1.13.0 - No replacement.
	 *
	 * @param string $current_url The URL to return to after a successful disconnection.
	 *
	 * @return string The URL to disconnect from the Zoom API.s
	 */
	public function to_disconnect( $current_url = null ) {
		_deprecated_function( __METHOD__, '1.13.1', 'No replacement, see Account_API class.' );

		return add_query_arg( [
			Plugin::$request_slug => wp_create_nonce( OAuth::$deauthorize_nonce_action ),
		], Settings::admin_url() );
	}

	/**
	 * Returns the URL that should be used to update a Zoom API meeting link.
	 *
	 * @since      1.4.0
	 * @deprecated 1.13.0 - No replacement.
	 *
	 * @param \WP_Post|null $post A post object to update the meeting for.
	 *
	 * @return string The URL to update the Zoom Meeting.
	 */
	public function to_update_meeting_link( \WP_Post $post ) {
		_deprecated_function( __METHOD__, '1.13.1', 'No replacement.' );

		$nonce = wp_create_nonce( Meetings::$update_action );

		return add_query_arg( [
			'action'              => 'ev_zoom_meetings_update',
			Plugin::$request_slug => $nonce,
			'post_id'             => $post->ID,
			'_ajax_nonce'         => $nonce,
		], admin_url( 'admin-ajax.php' ) );
	}

	/**
	 * Returns the URL that should be used to update a Zoom API webinar link.
	 *
	 * @since      1.4.0
	 * @deprecated 1.13.0 - No replacement.
	 *
	 * @param \WP_Post|null $post A post object to update the webinar for.
	 *
	 * @return string The URL to update the Zoom Webinar.
	 */
	public function to_update_webinar_link( \WP_Post $post ) {
		_deprecated_function( __METHOD__, '1.13.1', 'No replacement.' );

		$nonce = wp_create_nonce( Webinars::$update_action );

		return add_query_arg( [
				'action'              => 'ev_zoom_webinars_update',
				Plugin::$request_slug => $nonce,
				'post_id'             => $post->ID,
				'_ajax_nonce'         => $nonce,
			], admin_url( 'admin-ajax.php' ) );
	}
}
