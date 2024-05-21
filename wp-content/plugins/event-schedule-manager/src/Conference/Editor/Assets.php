<?php
/**
 * Handles Event Schedule Manager Editor Assets.
 *
 * @since 1.0.0
 *
 * @package TEC\Conference\Editor
 */

namespace TEC\Conference\Editor;

use TEC\Conference\Vendor\StellarWP\Assets\Asset;
use TEC\Conference\Vendor\StellarWP\Assets\Assets as Stellar_Assets;

/**
 * Class Assets
 *
 * @since 1.0.0
 *
 * @package TEC\Conference\Editor
 */
class Assets {

	/**
	 * Registers the editor assets.
	 *
	 * @since 1.0.0
	 */
	public function register_editor_assets() {
		Asset::add(
			'event-schedule-manager-editor-css',
			'event-schedule-manager-editor.css'
		)
		->set_dependencies( 'event-schedule-manager-flatpickr-style', 'event-schedule-manager-font-awesome', 'dashicons' )
		->add_to_group( 'event-schedule-manager-editor' )
		->register();

		Asset::add(
			'event-schedule-manager-font-awesome',
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css'
		)
			->add_to_group( 'event-schedule-manager-editor' )
		->register();

		Asset::add(
			'event-schedule-manager-flatpickr-style',
			'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css'
		)
			->add_to_group( 'event-schedule-manager-editor' )
		->register();

		Asset::add(
			'event-schedule-manager-flatpickr-script',
			'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js'
		)
			->add_to_group( 'event-schedule-manager-editor' )
		->register();

		Asset::add(
			'event-schedule-manager-schedule-block-js',
			'event-schedule-manager-block.js'
		)
		->set_dependencies( 'event-schedule-manager-flatpickr-script', 'event-schedule-manager-js', 'wp-blocks', 'wp-i18n', 'wp-editor' )
		->add_to_group( 'event-schedule-manager-editor' )
		->register();

		Stellar_Assets::instance()->enqueue_group( 'event-schedule-manager-editor' );
	}
}
