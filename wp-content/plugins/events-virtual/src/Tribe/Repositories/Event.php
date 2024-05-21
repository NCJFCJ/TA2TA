<?php
/**
 * An extension of The Events Calendar base repository to support Virtual functions.
 *
 * @since   1.0.1
 * @package Tribe\Events\Virtual\Repositories
 */

namespace Tribe\Events\Virtual\Repositories;

use Tribe\Events\Virtual\Event_Meta;

/**
 * Class Event
 *
 * @package Tribe\Events\Virtual\Repositories
 */
class Event extends \Tribe__Repository__Decorator {

	/**
	 * Event constructor.
	 *
	 * @since 1.0.1
	 */
	public function __construct() {
		$this->decorated = self::get_undecorated_repository();
		$this->decorated->add_schema_entry( 'virtual', array( $this, 'filter_by_virtual' ) );
	}

	/**
	 * Returns the current Event repository implementation, suspending the decoration applied by the plugin.
	 *
	 * @since 1.1.1
	 *
	 * @return \Tribe__Repository__Interface The current Event repository implementation.
	 *
	 * @see   Tribe\Events\Virtual\ORM\ORM_Provider for the details of the repository map filtering.
	 */
	public static function get_undecorated_repository() {
		$provider = tribe( 'events-virtual.orm' );

		remove_filter( 'tribe_events_event_repository_map', [ $provider, 'filter_event_repository_map' ], 12 );
		$undecorated_repository = tribe_events();
		add_filter( 'tribe_events_event_repository_map', [ $provider, 'filter_event_repository_map' ], 12 );

		return $undecorated_repository;
	}

	/**
	 * Filters events to include only those that match the provided virtual state.
	 *
	 * @since 1.0.1
	 *
	 * @param bool $virtual Whether the events should be virtual or not.
	 */
	public function filter_by_virtual( $virtual = true ) {
		$this->decorated->by( (bool) $virtual ? 'meta_exists' : 'meta_not_exists', Event_Meta::$key_virtual, '#' );
	}
}
