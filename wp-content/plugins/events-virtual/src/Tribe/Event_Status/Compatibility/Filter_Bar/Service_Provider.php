<?php
/**
 * Handles the compatibility with the Filter Bar plugin for event status.
 *
 * @since   1.7.3
 *
 * @package Tribe\Events\Virtual\Event_Status\Compatibility\Filter_Bar
 */

namespace Tribe\Events\Virtual\Event_Status\Compatibility\Filter_Bar;

use Tribe\Events\Event_Status\Compatibility\Filter_Bar\Detect;
use TEC\Common\Contracts\Service_Provider as Provider_Contract;

/**
 * Class Service_Provider
 *
 * @since   1.7.3
 *
 * @package Tribe\Events\Virtual\Event_Status\Compatibility\Filter_Bar
 */
class Service_Provider extends Provider_Contract {

	/**
	 * Register the bindings and filters required to ensure compatibility w/Filter Bar.
	 *
	 * @since 1.7.3
	 */
	public function register() {
		$this->container->singleton( static::class, $this );
		$this->container->singleton( 'events-virtual.event.status.compatibility.tribe-filter-bar', $this );

		if ( ! tribe( Detect::class )::is_active() ) {
			// For whatever reason the plugin is not active but we still got here, bail.
			return;
		}

		add_filter( 'tec_event_status_filterbar_values', [ $this, 'add_filterbar_values' ], 15 );
		add_filter( 'tec_event_status_filterbar_where_clause', [ $this, 'filter_where_clause' ], 10, 5 );
	}

	/**
	 * Adds the moved online filter for event status.
	 *
	 * @since 1.7.3
	 *
	 * @param array<string|string> $filter_values An array of filter values.
	 *
	 * @return array<string|string> An array of filter values.
	 */
	public function add_filterbar_values( $filter_values ) {
		return $this->container->make( Events_Status_Virtual_Filter::class )->add_filterbar_values( $filter_values );
	}

	/**
	 * Filter the event statuses where clause to only show moved online events.
	 *
	 * @since 1.7.3
	 *
	 * @param string                      $where_clause  The where clause to filter.
	 * @param string|array<string|string> $current_value A string or array of the current values selected for the filter.
	 * @param string                      $alias         The table alias that will be used for the postmeta table.
	 * @param array<string|string>        $hide_clauses  The hide clauses on whether to hide canceled and postponed events.
	 * @param array<string|string>        $clauses       The standard clauses to get all events.
	 *
	 * @return string                      $where_clause The where clause to filter.
	 */
	public function filter_where_clause( $where_clause, $current_value, $alias, $hide_clauses, $clauses ) {
		return $this->container->make( Events_Status_Virtual_Filter::class )->filter_where_clause( $where_clause, $current_value, $alias, $hide_clauses, $clauses );
	}
}
