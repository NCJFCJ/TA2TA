<?php
/**
 * Handles The Virtual Events integration.
 *
 * @since   1.15.0
 *
 * @package TEC\Events_Virtual\Integrations
 */
namespace TEC\Events_Virtual\Integrations;

use TEC\Common\Contracts\Service_Provider;
/**
 * Class Provider
 *
 * @since   1.15.0
 *
 * @package TEC\Events_Virtual\Integrations
 */
class Provider extends Service_Provider {
	/**
	 * Binds and sets up implementations.
	 *
	 * @since 1.15.0
	 */
	public function register() {
		$this->container->singleton( static::class, $this );

		$this->container->register( Plugins\Event_Tickets\Provider::class );
		$this->container->register_on_action( 'tec_container_registered_provider_TEC\Tickets_Wallet_Plus\Controller', Plugins\Tickets_Wallet_Plus\Controller::class );
	}
}
