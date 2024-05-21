<?php
/**
 * Service Provider for interfacing with TEC\Common\Telemetry.
 *
 * @since 6.1.0
 *
 * @package TEC\Events_Pro\Telemetry
 */

namespace TEC\Events_Pro\Telemetry;

use TEC\Common\Contracts\Service_Provider;
use TEC\Common\Telemetry\Telemetry as Common_Telemetry;

 /**
  * Class Provider
  *
  * @since 6.1.0

  * @package TEC\Events_Pro\Telemetry
  */
class Provider extends Service_Provider {
	/**
	 * Register the service provider.
	 *
	 * @since 6.1.0
	 */
	public function register() {
		$this->add_filters();
		$this->add_actions();
	}

	/**
	 * Add the action hooks.
	 *
	 * @since 6.1.0
	 */
	public function add_actions() {
		add_action( 'activate' . EVENTS_CALENDAR_PRO_FILE, [ $this, 'register_tec_ecp_telemetry_on_activation' ] );
	}

	/**
	 * Add the action hooks.
	 *
	 * @since 6.1.0
	 */
	public function add_filters() {
		add_filter( 'tec_telemetry_slugs', [ $this, 'filter_tec_telemetry_slugs' ] );
	}

	/**
	 * Let Events Calendar Pro add itself to the list of registered plugins for Telemetry.
	 *
	 * @since 6.1.0
	 *
	 * @param array<string,string> $slugs The existing array of slugs.
	 *
	 * @return array<string,string> $slugs The modified array of slugs.
	 */
	public function filter_tec_telemetry_slugs( $slugs ) {
		return $this->container->get( Telemetry::class )->filter_tec_telemetry_slugs( $slugs );
	}

	/**
	 * Registers the plugin with Telemetry on plugin activation.
	 *
	 * @since 6.2.1.1
	 */
	public function register_tec_ecp_telemetry_on_activation() {
		// Activate plugin in Telemetry. We do this on 5 to make sure it triggers before the library does.
	add_action(
		'shutdown',
		[ $this, 'register_tec_telemetry_child_plugins' ], 5 );
	}

	public function register_tec_telemetry_child_plugins() {
		$common_telemetry = self::$container->get( Common_Telemetry::class );
		$status = $common_telemetry->calculate_optin_status();
		$common_telemetry->register_tec_telemetry_plugins( $status );
	}
}
