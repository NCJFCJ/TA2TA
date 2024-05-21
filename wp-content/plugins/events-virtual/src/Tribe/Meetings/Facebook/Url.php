<?php
/**
 * Manages the Facebook URLs for the plugin.
 *
 * @since 1.7.0
 *
 * @package Tribe\Events\Virtual\Meetings\Facebook
 */

namespace Tribe\Events\Virtual\Meetings\Facebook;

use Tribe\Events\Virtual\Plugin;

/**
 * Class Url
 *
 * @since 1.7.0
 *
 * @package Tribe\Events\Virtual\Meetings\Facebook
 */
class Url {

	/**
	 * Returns the URL that should be used to save a Facebook app id and secret.
	 *
	 * @since 1.7.0
	 *
	 * @return string The URL to save a Facebook app id and secret.
	 */
	public function to_save_facebook_app_link() {
		$nonce = wp_create_nonce( Settings::$save_app_action );

		return add_query_arg( [
			'action'              => 'ev_facebook_settings_save_app',
			Plugin::$request_slug => $nonce,
			'_ajax_nonce'         => $nonce,
		], admin_url( 'admin-ajax.php' ) );
	}

	/**
	 * Returns the URL that should be used to add a Facebook Page.
	 *
	 * @since 1.7.0
	 *
	 * @return string The URL to add a page.
	 */
	public function add_link() {
		$nonce = wp_create_nonce( Settings::$add_action );

		return add_query_arg( [
			'action'              => 'ev_facebook_settings_add_page',
			Plugin::$request_slug => $nonce,
			'_ajax_nonce'         => $nonce,
		], admin_url( 'admin-ajax.php' ) );
	}

	/**
	 * Returns the URL that should be used to delete a Facebook Page by local id..
	 *
	 * @since 1.7.0
	 *
	 * @param string $local_id The local id of the Facebook Page Id to delete.
	 *
	 * @return string The URL to delete a page.
	 */
	public function to_delete_page_link( $local_id ) {
		$nonce = wp_create_nonce( Settings::$delete_action );

		return add_query_arg( [
			'action'              => 'ev_facebook_settings_delete_page',
			Plugin::$request_slug => $nonce,
			'local_id'            => $local_id,
			'_ajax_nonce'         => $nonce,
		], admin_url( 'admin-ajax.php' ) );
	}

	/**
	 * Returns the URL that should be used to save a Facebook Page.
	 *
	 * @since 1.7.0
	 *
	 * @return string The URL to save a page.
	 */
	public function to_save_page_link() {
		$nonce = wp_create_nonce( Settings::$save_action );

		return add_query_arg( [
			'action'              => 'ev_facebook_settings_save_page',
			Plugin::$request_slug => $nonce,
			'_ajax_nonce'         => $nonce,
		], admin_url( 'admin-ajax.php' ) );
	}

	/**
	 * Returns the URL that should be used to save a Facebook Page's Access Token.
	 *
	 * @since 1.7.0
	 *
	 * @return string The URL to save a page's access token.
	 */
	public function to_save_access_page_link() {
		$nonce = wp_create_nonce( Settings::$save_access_action );

		return add_query_arg( [
			'action'              => 'ev_facebook_settings_save_access_page',
			Plugin::$request_slug => $nonce,
			'_ajax_nonce'         => $nonce,
		], admin_url( 'admin-ajax.php' ) );
	}

	/**
	 * Returns the URL that should be used to clear a Facebook Page's Access Token.
	 *
	 * @since 1.7.0
	 *
	 * @return string The URL to clear a page's access token.
	 */
	public function to_clear_access_page_link() {
		$nonce = wp_create_nonce( Settings::$clear_access_action );

		return add_query_arg( [
			'action'              => 'ev_facebook_settings_clear_access_token',
			Plugin::$request_slug => $nonce,
			'_ajax_nonce'         => $nonce,
		], admin_url( 'admin-ajax.php' ) );
	}
}
