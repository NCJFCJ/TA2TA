<?php
/**
 * Handles the compatibility with the Filter Bar plugin for event status.
 *
 * @since  1.7.3
 *
 * @package Tribe\Events\Virtual\Event_Status\Compatibility\Filter_Bar
 */

namespace Tribe\Events\Virtual\Event_Status\Compatibility\Filter_Bar;

/**
 * Class Events_Virtual_Filter.
 *
 * @since   1.7.3
 *
 * @package Tribe\Events\Virtual\Event_Status\Compatibility\Filter_Bar
 */
class Events_Status_Virtual_Filter {

	/**
	 * Value checked for moved online events.
	 *
	 * @since   1.7.3
	 */
	const MOVEDONLINE = 'moved-online';

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

		$filter_values[ 'moved-online' ] = [
				'name'  => _x( 'Show only moved online events', 'Moved online label for filter bar to show moved online events.', 'events-virtual' ),
				'value' => static::MOVEDONLINE,
			];

		return $filter_values;
	}


	/**
	 * Filter the event statuses where clause to only show moved online events.
	 *
	 * @since 1.7.3
	 *
	 * @param string                      $where_clause The where clause to filter.
	 * @param string|array<string|string> $currentValue A string or array of the current values selected for the filter.
	 * @param string                      $alias         The table alias that will be used for the postmeta table.
	 * @param array<string|string>        $hide_clauses The hide clauses on whether to hide canceled and postponed events.
	 * @param array<string|string>        $clauses      The standard clauses to get all events.
	 *
	 * @return string                      $where_clause The where clause to filter.
	 */
	public function filter_where_clause( $where_clause, $current_value, $alias, $hide_clauses, $clauses ) {
		/** @var \wpdb $wpdb */
		global $wpdb;

		// If the moved online status is not a selected value, then return existing clause.
		if (
			(
				is_array( $current_value ) &&
				! in_array( static::MOVEDONLINE, $current_value )
			) &&
			static::MOVEDONLINE !== $current_value
		) {
			return $where_clause;
		}


		$moved_online_clauses[] = $wpdb->prepare(
			" {$alias}.meta_value IN (%s) ",
			self::MOVEDONLINE
		);

		return ' AND ( ' . implode( ' OR ', $moved_online_clauses ) . ') ';
	}
}
