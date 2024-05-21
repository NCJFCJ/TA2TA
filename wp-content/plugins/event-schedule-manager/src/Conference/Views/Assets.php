<?php
/**
 * Handles Event Schedule Manager View Assets.
 *
 * @since 1.0.0
 *
 * @package TEC\Conference\Views
 */

namespace TEC\Conference\Views;

use TEC\Conference\Plugin;
use TEC\Conference\Vendor\StellarWP\Assets\Asset;
use TEC\Conference\Vendor\StellarWP\Assets\Assets as Stellar_Assets;

/**
 * Class Assets
 *
 * @since 1.0.0
 *
 * @package TEC\Conference\Views
 */
class Assets {

	/**
	 * Registers the view assets.
	 *
	 * @since 1.0.0
	 */
	public function register_views_assets() {
		Asset::add(
			'event-schedule-manager-views-css',
			'event-schedule-manager-views.css'
		)
		->set_dependencies( 'event-schedule-manager-font-awesome', 'dashicons' )
		->add_to_group( 'event-schedule-manager-views' )
		->register();

		Asset::add(
			'event-schedule-manager-font-awesome',
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css'
		)
			->add_to_group( 'event-schedule-manager-views' )
		->register();

		Asset::add(
			'event-schedule-manager-js',
			'event-schedule-manager.js'
		)
		->set_dependencies( 'jquery' )
		->add_to_group( 'event-schedule-manager-views' )
		->register();
	}

	/**
	 * Checks for specified custom post types on single post pages and enqueues assets if true.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_views_posttype_assets() {
		if ( ! is_single() ) {
			return;
		}

		$post_types = array(
			Plugin::SESSION_POSTTYPE,
			Plugin::SPEAKER_POSTTYPE,
			Plugin::SPONSOR_POSTTYPE,
		);

		if ( ! in_array( get_post_type(), $post_types ) ) {
			return;
		}

		Stellar_Assets::instance()->enqueue_group( 'event-schedule-manager-views' );
	}
}
