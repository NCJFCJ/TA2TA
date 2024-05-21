<?php
/**
 * The Provider class for registering community event routes.
 *
 * @since 4.10.9
 */

namespace TEC\Events_Community\Routes;

use WP_Router;
use TEC\Events_Community\Routes\Routes_Factory;
use TEC\Events_Community\Routes\Event\Route_Add;
use TEC\Events_Community\Routes\Event\Route_Edit;
use TEC\Events_Community\Routes\Listing\Route_Listing;
use TEC\Events_Community\Routes\Organizer\Route_Edit as Organizer_Route_Edit;
use TEC\Events_Community\Routes\Venue\Route_Edit as Venue_Route_Edit;

class Provider extends \tad_DI52_ServiceProvider {
	private array $routes;

	/**
	 * Register the provider.
	 *
	 * @since 4.10.9
	 *
	 * @return void
	 */
	public function register(): void {
		$this->container->singleton( Routes_Factory::class, Routes_Factory::class );
		$this->container->singleton( Route_Add::class, Route_Add::class );
		$this->container->singleton( Route_Edit::class, Route_Edit::class );
		$this->container->singleton( Organizer_Route_Edit::class, Organizer_Route_Edit::class );
		$this->container->singleton( Venue_Route_Edit::class, Venue_Route_Edit::class );
		$this->container->singleton( Route_Listing::class, Route_Listing::class );
		$this->register_hooks();
	}

	/**
	 * Register the provider hooks.
	 *
	 * @since 4.10.9
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'wp_router_generate_routes', [ $this, 'generate_default_routes' ] );
	}

	/**
	 * Generate the community event routes.
	 *
	 * @since 4.10.9
	 *
	 * @param WP_Router $router The WP_Router object.
	 *
	 * @return void
	 */
	public function generate_default_routes( \WP_Router $router ): array {
		$routes_factory = tribe( Routes_Factory::class );
		return $routes_factory->generate_default_routes( $router );
	}
}
