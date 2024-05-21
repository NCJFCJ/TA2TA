<?php

namespace TEC\Events_Community\Routes;

/**
 * Interface for adding custom routes to the WordPress router.
 *
 *
 * @since 4.10.9
 */
interface Route_Interface {

	/**
	 * Sets up the method.
	 *
	 * @since 4.10.9
	 *
	 * @return void
	 */
	public function setup(): void;

	/**
	 * Abstract method for adding the route to the WordPress router.
	 *
	 * @since 4.10.9
	 */
	public function add(): void;

	/**
	 * Sets the title for the route.
	 *
	 * @since 4.10.9
	 *
	 * @return void
	 */
	public function set_title(): void;

}
