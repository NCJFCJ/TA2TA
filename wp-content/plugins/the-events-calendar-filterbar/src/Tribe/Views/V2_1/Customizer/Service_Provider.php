<?php
/**
 * The main service provider for the version 2 of the Views.
 *
 * @package Tribe\Events\Filterbar\Views\V2_1\Customizer
 * @since   5.1.4
 */

namespace Tribe\Events\Filterbar\Views\V2_1\Customizer;

use Tribe\Events\Filterbar\Views\V2_1\Customizer\Section\Events_Bar;
use Tribe\Events\Filterbar\Views\V2_1\Customizer\Section\Global_Elements;
use TEC\Common\Contracts\Service_Provider as Provider_Contract;

/**
 * Class Service_Provider
 *
 * @since   5.1.4
 *
 * @package Tribe\Events\Filterbar\Views\V2_1\Customizer
 */
class Service_Provider extends Provider_Contract {
	/**
	 * Binds and sets up implementations.
	 *
	 * @since 5.1.4
	 */
	public function register() {
		$this->container->singleton( 'filterbar.views.v2_1.customizer.provider', $this );

		$this->register_hooks();

		tribe_register( 'filterbar.views.v2_1.customizer.events-bar', Events_Bar::class );
		tribe_register( 'filterbar.views.v2_1.customizer.global-elements', Global_Elements::class );
	}

	/**
	 * Register the hooks for Tribe_Customizer integration.
	 *
	 * @since 5.1.4
	 */
	public function register_hooks() {
		$hooks = new Hooks( $this->container );
		$hooks->register();

		// Allow Hooks to be removed, by having the them registered to the container
		$this->container->singleton( Hooks::class, $hooks );
		$this->container->singleton( 'filterbar.views.v2_1.customizer.hooks', $hooks );
	}
}
