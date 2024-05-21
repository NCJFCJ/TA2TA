<?php
/**
 * Service Provider for interfacing with TEC\Conference\Vendor\StellarWP\Telemetry.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Telemetry
 */

namespace TEC\Conference\Telemetry;

use TEC\Conference\Contracts\Service_Provider;
use TEC\Conference\Plugin;

/**
 * Class Provider
 *
 * @since   1.0.0
 * @package TEC\Conference\Telemetry
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
		add_filter( 'stellarwp/telemetry/' . Plugin::SLUG . '/optin_args', [ $this, 'filter_telemetry_optin_args' ] );
	}

	/**
	 * Handles the action hooks for this module.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'render_esm_telemetry_setting', [ $this, 'render_esm_telemetry_setting' ] );
		add_action( 'admin_init', [ $this, 'action_save_opt_in_setting_field' ] );
	}

	/**
	 * Filter the telemetry opt-in arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param array $optin_args Previous set of args we are changing.
	 *
	 * @return array
	 */
	public function filter_telemetry_optin_args( $optin_args ) {
		return $this->container->get( Telemetry::class )->filter_telemetry_optin_args( $optin_args );
	}

	/**
	 * Render the checkbox, label, and description.
	 * Hooked to the `render_esm_telemetry_setting` action in `src/Conference/Admin/Settings.php`
	 *
	 * @since 1.0.0
	 */
	public function render_esm_telemetry_setting() {
		$this->container->get( Telemetry::class )->render_esm_telemetry_setting();
	}

	public function action_save_opt_in_setting_field() {
		$this->container->get( Telemetry::class )->action_save_opt_in_setting_field();
	}
}
