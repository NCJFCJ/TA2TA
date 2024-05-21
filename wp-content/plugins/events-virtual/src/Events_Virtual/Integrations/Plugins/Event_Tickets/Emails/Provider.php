<?php
namespace TEC\Events_Virtual\Integrations\Plugins\Event_Tickets\Emails;

use TEC\Events_Virtual\Integrations\Integration_Abstract;
use TEC\Common\Integrations\Traits\Module_Integration;

/**
 * Class Provider
 *
 * @since 1.15.0
 *
 * @package TEC\Events_Virtual\Integrations\Plugins\Event_Tickets\Emails
 */
class Provider extends Integration_Abstract {
	use Module_Integration;

	/**
	 * @inheritDoc
	 */
	public static function get_slug(): string {
		return 'event-tickets-emails';
	}

	/**
	 * @inheritDoc
	 */
	public function load_conditionals(): bool {
		// We want users to always be able to preview Tickets Emails functionality.
		return true;
	}

	/**
	 * @inheritDoc
	 */
	protected function load(): void {
		// Register the Service Provider for Hooks.
		$this->register_hooks();

		$this->container->singleton( Template::class, Template::class );
	}

	/**
	 * Registers the provider handling all the 1st level filters and actions for this Service Provider.
	 *
	 * @since 1.15.0
	 */
	protected function register_hooks(): void {
		$hooks = new Hooks( $this->container );
		$hooks->register();

		// Allow Hooks to be removed, by having the them registered to the container.
		$this->container->singleton( Hooks::class, $hooks );
	}

}
