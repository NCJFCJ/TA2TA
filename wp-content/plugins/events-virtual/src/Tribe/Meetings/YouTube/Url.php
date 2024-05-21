<?php
/**
 * Manages the YouTube URLs for the plugin.
 *
 * @since1.6.0
 *
 * @package Tribe\Events\Virtual\Meetings\YouTube
 */

namespace Tribe\Events\Virtual\Meetings\YouTube;

use Tribe\Events\Virtual\Plugin;

/**
 * Class Url
 *
 * @since 1.6.0
 *
 * @package Tribe\Events\Virtual\Meetings\YouTube
 */
class Url {

	/**
	 * Returns the URL that should be used to delete a YouTube channel id.
	 *
	 * @since 1.6.0
	 *
	 * @return string The URL to delete a YouTube channel id.
	 */
	public function to_delete_channel_id() {
		$nonce = wp_create_nonce( Settings::$delete_action );

		return add_query_arg( [
			'action'              => 'ev_youtube_settings_delete_channel_id',
			Plugin::$request_slug => $nonce,
			'channel_field_id'    => Settings::$option_prefix . 'channel_id',
			'_ajax_nonce'         => $nonce,
		], admin_url( 'admin-ajax.php' ) );
	}
}
