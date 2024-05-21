<?php
/**
 * Handles Event Schedule Manager Admin Assets.
 *
 * @since 1.0.0
 *
 * @package TEC\Conference\Admin
 */

namespace TEC\Conference\Admin;

use TEC\Conference\Plugin;
use TEC\Conference\Vendor\StellarWP\Assets\Asset;
use TEC\Conference\Vendor\StellarWP\Assets\Assets as Stellar_Assets;

/**
 * Class Assets
 *
 * @since 1.0.0
 *
 * @package TEC\Conference\Admin
 */
class Assets {

	/**
	 * Registers the admin assets.
	 *
	 * @since 1.0.0
	 */
	public function register_admin_assets() {
		Asset::add(
			'event-schedule-manager-admin-css',
			'event-schedule-manager-admin.css'
		)
		->add_to_group( 'event-schedule-manager-admin' )
		->register();

		Asset::add(
			'event-schedule-manager-jquery-ui-css',
			'jquery-ui.css'
		)
		->add_to_group( 'event-schedule-manager-admin' )
		->register();

		Asset::add(
			'event-schedule-manager-admin-js',
			'event-schedule-manager-admin.js'
		)
		->set_dependencies( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable' )
		->add_to_group( 'event-schedule-manager-admin' )
		->register();

		Asset::add(
			'event-schedule-manager-js',
			'event-schedule-manager.js'
		)
		->add_to_group( 'event-schedule-manager-admin' )
		->register();
	}

	/**
	 * Enqueues the admin assets for custom post types.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_admin_posttype_assets() {
		$screen = get_current_screen();

		if ( empty( $screen->post_type ) ) {
			return;
		}

		$post_types = array(
			Plugin::SESSION_POSTTYPE,
			Plugin::SPEAKER_POSTTYPE,
			Plugin::SPONSOR_POSTTYPE,
		);

		if (
			'post' !== $screen->base
			|| ! in_array( $screen->post_type, $post_types )
		) {
			return;
		}

		Stellar_Assets::instance()->enqueue_group( 'event-schedule-manager-admin' );
	}
}
