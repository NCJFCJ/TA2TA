<?php
/**
 * Handles The Community Events integration.
 *
 * @since   4.10.13
 *
 * @package TEC\Events_Community\Integrations
 */

namespace TEC\Events_Community\Integrations;

use TEC\Common\Contracts\Service_Provider;


/**
 * Class Provider
 *
 * @since   4.10.13
 *
 * @package TEC\Events_Community\Integrations
 */
class Provider extends Service_Provider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 4.10.13
	 */
	public function register() {
		$this->container->singleton( static::class, $this );

		$this->container->register( Themes\Divi\Provider::class );
	}
}
