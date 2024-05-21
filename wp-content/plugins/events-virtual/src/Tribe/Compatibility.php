<?php
/**
 * Handles the compatibility and integration of the plugin with other Tribe and third-party plugins.
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual
 */

namespace Tribe\Events\Virtual;

use TEC\Common\Contracts\Service_Provider;

/**
 * Class Compatibility
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual
 */
class Compatibility extends Service_Provider {

	/**
	 * Conditionally registers the Service Providers that handle the compatibility and integrations w/ other plugins.
	 *
	 * @since 1.0.0
	 * @since 1.0.4 Added Filter Bar.
	 */
	public function register() {
		$this->container->singleton( self::class, $this );
		$this->container->singleton( 'events-virtual.compatibility', $this );

		if ( ! class_exists( 'Tribe__Events__Main' ) ) {
			// No point in moving forward if The Events Calendar is not activated.
			return;
		}

		// Ensure we can call WordPress plugin-related functions.
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		add_action( 'tribe_common_loaded', [ $this, 'handle_events_control_extension' ], 99 );
		add_action( 'tribe_common_loaded', [ $this, 'handle_online_event_extension' ], 99 );
		add_action( 'tribe_common_loaded', [ $this, 'handle_filter_bar' ], 99 );
		add_action( 'tribe_common_loaded', [ $this, 'handle_event_tickets' ], 99 );
	}

	/**
	 * Handles the compatibility with the "The Events Calendar Extension: Events Control" plugin.
	 *
	 * @since 1.0.0
	 */
	public function handle_events_control_extension() {
		if ( ! is_plugin_active( 'tribe-ext-events-control/tribe-ext-events-control.php' ) ) {
			return;
		}

		$this->container->register( Compatibility\Events_Control_Extension\Service_Provider::class );
	}

	/**
	 * Handles the compatibility with the "Events Tickets Extension: Virtual / Online Event Tickets" plugin.
	 *
	 * @since 1.0.0
	 */
	public function handle_online_event_extension() {
		if ( ! is_plugin_active( 'tribe-ext-online-event/index.php' ) ) {
			return;
		}

		$this->container->register( Compatibility\Online_Event_Extension\Service_Provider::class );
	}

	/**
	 * Handles the compatibility with the Filter Bar plugin.
	 *
	 * @since 1.0.4
	 */
	public function handle_filter_bar() {
		if ( ! class_exists( 'Tribe__Events__Filterbar__View' ) ) {
			return;
		}

		$this->container->register( Compatibility\Filter_Bar\Service_Provider::class );
	}

	/**
	 * Handles the compatibility with the Event Tickets plugin.
	 *
	 * @since 1.0.4
	 */
	public function handle_event_tickets() {
		if ( ! class_exists( 'Tribe__Tickets__Main' ) ) {
			return;
		}

		$this->container->register( Compatibility\Event_Tickets\Service_Provider::class );
	}
}
