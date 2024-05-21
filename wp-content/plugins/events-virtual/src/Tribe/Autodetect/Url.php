<?php
/**
 * Manages the Autodetect URLs for the plugin.
 *
 * @since   1.8.0
 *
 * @package Tribe\Events\Virtual\Autodetect
 */

namespace Tribe\Events\Virtual\Autodetect;

use Tribe\Events\Virtual\Plugin;

/**
 * Class Url
 *
 * @since   1.8.0
 *
 * @package Tribe\Events\Virtual\Autodetect
 */
class Url {

	/**
	 * Returns the URL that should be used to autodetect a video source.
	 *
	 * @since 1.8.0
	 *
	 * @param \WP_Post|null $post The event post object, as decorated by the `tribe_get_event` function.
	 *
	 * @return string The URL to autodetect a video source.
	 */
	public function to_autodetect_video_source( \WP_Post $post ) {
		$nonce = wp_create_nonce( AJAX::$autodetect_action );

		return add_query_arg( [
			'action'              => 'ev_autodetect_video_source',
			Plugin::$request_slug => $nonce,
			'post_id'             => $post->ID,
			'_ajax_nonce'         => $nonce,
		], admin_url( 'admin-ajax.php' ) );
	}
}
