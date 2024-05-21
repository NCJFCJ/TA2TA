<?php
/**
 * Service Provider for interfacing with TEC\Conference\Vendor\StellarWP\License.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\License
 */

namespace TEC\Conference\License;

use TEC\Conference\Contracts\Service_Provider;
use TEC\Conference\Plugin;

/**
 * Class Provider
 *
 * @since   1.0.0
 * @package TEC\Conference\License
 */
class Provider extends Service_Provider {

	/**
	 * Handles the registering of the provider.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		$this->add_filters();
		$this->add_actions();
	}

	/**
	 * Handles the inclusion of the Filters for this module.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'stellarwp/uplink/' . Plugin::SLUG . '/admin_js_source', [ $this, 'filter_license_admin_js_source' ] );
		add_filter( 'stellarwp/uplink/' . Plugin::SLUG . '/admin_css_source', [ $this, 'filter_license_admin_css_source' ] );
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
		return $this->container->get( License::class )->filter_license_admin_js_source( $path );
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
		return $this->container->get( License::class )->filter_license_admin_css_source( $path );
	}

	/**
	 * Handles the action hooks for this module.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
	}
}
