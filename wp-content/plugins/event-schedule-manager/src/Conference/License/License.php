<?php
/**
 * License for Event Schedule Manager.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\License
 */

namespace TEC\Conference\License;

use TEC\Conference\Plugin;
use TEC\Conference\Uplink\Helper;
use TEC\Conference\Vendor\StellarWP\Uplink\Config as Uplink_Config;
use TEC\Conference\Vendor\StellarWP\Uplink\Register;
use TEC\Conference\Vendor\StellarWP\Uplink\Uplink;

/**
 * Class License
 *
 * Handles interfacing with TEC\Conference\Vendor\StellarWP\License.
 *
 * @since   1.0.0
 * @package TEC\Conference\License
 */
class License {

	/**
	 * Initialize the license component.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed  $container The service container.
	 * @param string $path      The path for the plugin.
	 * @param object $plugin    The plugin instance.
	 *
	 * @return void
	 */
	public static function boot( $container, $path, $plugin ) {
		Uplink_Config::set_container( $container );
		Uplink_Config::set_hook_prefix( Plugin::SLUG );
		Uplink::init();

		Register::plugin(
			Plugin::SLUG,
			'Event Schedule Manager',
			Plugin::VERSION,
			$path,
			get_class( $plugin ),
			Helper::class
		);
	}

	/**
	 * Filter the license JavaScript source URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path The path to the js file.
	 *
	 * @return string The filtered js source URL.
	 */
	public function filter_license_admin_js_source( $path ) {
		$filename = basename( $path );

		return Plugin::get_vendor_url() . 'vendor-prefixed/stellarwp/uplink/src/assets/js/' . $filename;
	}

	/**
	 * Filter the license CSS source URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path The path to the css file.
	 *
	 * @return string The filtered css source URL.
	 */
	public function filter_license_admin_css_source( $path ) {
		$filename = basename( $path );

		return Plugin::get_vendor_url() . 'vendor-prefixed/stellarwp/uplink/src/assets/css/' . $filename;
	}
}
