<?php
/**
 * The Virtual Event Integration with Zapier service provider.
 *
 * @since 1.13.5
 * @package TEC\Events_Virtual\Compatibility\Event_Automator\Zapier
 */

namespace TEC\Events_Virtual\Compatibility\Event_Automator\Zapier;

use TEC\Events_Virtual\Compatibility\Event_Automator\Zapier\Maps\Event;
use TEC\Common\Contracts\Service_Provider;
use WP_Post;

/**
 * Class Zapier_Provider
 *
 * @since 1.13.5
 *
 * @package TEC\Events_Virtual\Compatibility\Event_Automator\Zapier
 */
class Zapier_Provider extends Service_Provider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 1.13.5
	 */
	public function register() {
		// Register the SP on the container
		$this->container->singleton( Zapier_Provider::class, $this );

		$this->add_filters();
	}

	/**
	 * Adds the filters for Event Automator integration.
	 *
	 * @since 1.13.5
	 */
	protected function add_filters() {
		add_filter( 'tec_automator_map_event_details', [ $this, 'add_virtual_fields' ], 10, 3 );
	}

	/**
	 * Filters the event details sent to a 3rd party.
	 *
	 * @since 1.13.5
	 * @since 1.15.3 - Add Service ID.
	 *
	 * @param array<string|mixed> $next_event An array of event details.
	 * @param WP_Post             $event      An instance of the event WP_Post object.
	 * @param string              $service_id The service id used to modify the mapped event details.
	 *
	 * @return array<string|mixed> An array of event details.
	 */
	public function add_virtual_fields( array $next_event, WP_Post $event, $service_id ) {
		return $this->container->make( Event::class )->add_virtual_fields( $next_event, $event, $service_id );
	}
}
