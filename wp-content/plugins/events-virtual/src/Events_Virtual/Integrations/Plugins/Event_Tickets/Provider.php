<?php

namespace TEC\Events_Virtual\Integrations\Plugins\Event_Tickets;

use TEC\Common\Integrations\Traits\Plugin_Integration;
use TEC\Events_Virtual\Integrations\Integration_Abstract;

/**
 * Class Provider
 *
 * @since   1.15.0
 *
 * @package TEC\Events_Virtual\Integrations\Plugins\Event_Tickets
 */
class Provider extends Integration_Abstract {
	use Plugin_Integration;

	/**
	 * @inheritDoc
	 */
	public static function get_slug(): string {
		return 'event-tickets';
	}

	/**
	 * @inheritDoc
	 */
	public function load_conditionals(): bool {
		return function_exists( 'tribe_tickets' );
	}

	/**
	 * @inheritDoc
	 */
	protected function load(): void {
		// Loads Tickets Emails.
		$this->container->register( Emails\Provider::class );
	}
}
