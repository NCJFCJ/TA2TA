<?php

namespace TEC\Events_Community\Routes;

use WP_Router;
use TEC\Events_Community\Routes\Event\Route_Add;
use TEC\Events_Community\Routes\Event\Route_Edit;
use TEC\Events_Community\Routes\Listing\Route_Listing;
use TEC\Events_Community\Routes\Organizer\Route_Edit as Organizer_Route_Edit;
use TEC\Events_Community\Routes\Venue\Route_Edit as Venue_Route_Edit;

/**
 * Factory for generating community event routes.
 *
 * @since 4.10.9
 */
class Routes_Factory {

	/**
	 * An array of generated routes.
	 *
	 * @var array
	 */
	protected static $routes = [];

	/**
	 * The single instance of the class.
	 *
	 * @var Abstract_Route|null
	 */
	protected static $instance;

	/**
	 * An array of default route classes to generate.
	 *
	 * @var array
	 */
	protected static array $default_routes = [
		'event_add'      => Route_Add::class,
		'event_edit'     => Route_Edit::class,
		'event-list'     => Route_Listing::class,
		'venue-edit'     => Venue_Route_Edit::class,
		'organizer-edit' => Organizer_Route_Edit::class,
	];

	/**
	 * Get the single instance of the class.
	 *
	 * @since 4.10.9
	 *
	 * @return Abstract_Route The single instance of the class.
	 */
	public static function getInstance(): Abstract_Route {
		if ( static::$instance === null ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Generate the default community event routes.
	 *
	 * @since 4.10.9
	 *
	 * @param WP_Router $router The WP_Router object.
	 *
	 * @return array An array of generated routes.
	 */
	public static function generate_default_routes( \WP_Router $router ): array {
		$routes = [];

		// Generate each default route.
		foreach ( self::$default_routes as $name => $route_class ) {
			$route = tribe( $route_class );
			$route->set_router( $router );
			$route->setup();
			self::add_route( $name, $route );
			$routes[ $name ] = $route;
		}

		return $routes;
	}

	/**
	 * Add a route to the static routes property.
	 *
	 * @since 4.10.9
	 *
	 * @param string $name The name of the route.
	 * @param mixed $route The route to add.
	 *
	 * @return void
	 */
	public static function add_route( string $name, $route ): void {
		self::$routes[ $name ] = $route;
	}

	/**
	 * Get the static routes property.
	 *
	 * @since 4.10.9
	 *
	 * @return array The static routes property.
	 */
	public static function get_routes(): array {
		return self::$routes;
	}
}
