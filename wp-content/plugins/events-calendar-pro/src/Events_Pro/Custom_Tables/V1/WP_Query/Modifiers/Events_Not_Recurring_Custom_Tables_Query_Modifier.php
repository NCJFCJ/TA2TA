<?php
/**
 * Handles the `post__not_recurring` query var to exclude Occurrences of Recurring Events from the results
 * when the filtered query is a Custom Tables Query.
 *
 * If the query is not a Custom Tables Query, the modifier that should be used is the
 * `Events_Not_Recurring_Query_Modifier`
 *
 * @since   6.2.2
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\WP_Query\Modifiers;
 */

namespace TEC\Events_Pro\Custom_Tables\V1\WP_Query\Modifiers;

use TEC\Events\Custom_Tables\V1\Tables\Occurrences;
use TEC\Events\Custom_Tables\V1\WP_Query\Modifiers\Base_Modifier;
use WP_Query;
use TEC\Events_Pro\Custom_Tables\V1\WP_Query\Modifiers\Events_Not_Recurring_Query_Modifier as Query_Modifier;

/**
 * Class Events_Not_Recurring_Custom_Tables_Query_Modifier.
 *
 * @since   6.2.2
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\WP_Query\Modifiers;
 */
class Events_Not_Recurring_Custom_Tables_Query_Modifier extends Base_Modifier {

	/**
	 * {@inheritDoc}
	 *
	 * @since 6.2.2
	 *
	 * @return void
	 */
	public function hook() {
		add_filter( 'posts_where', [ $this, 'exclude_recurring_occurrences' ], 10, 2 );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 6.2.2
	 *
	 * @return void
	 */
	public function unhook() {
		remove_filter( 'posts_where', [ $this, 'exclude_recurring_occurrences' ], 10, 2 );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 6.2.2
	 *
	 * @param WP_Query|null $query
	 *
	 * @return void
	 */
	public function applies_to( WP_Query $query = null ): bool {
		return $query && ! empty( $query->query_vars[ Query_Modifier::POST_NOT_RECURRING_QUERY_VAR ] );
	}

	/**
	 * Excludes Occurrences of Recurring Events from the results when the filtered query is a Custom Tables Query.
	 *
	 * @since 6.2.2
	 *
	 * @param string   $where The WHERE clause of the query.
	 * @param WP_Query $query The WP_Query instance.
	 *
	 * @return string The modified WHERE clause.
	 */
	public function exclude_recurring_occurrences( string $where, WP_Query $query ): string {
		if ( $query !== $this->query ) {
			return $where;
		}

		remove_filter( 'posts_where', [ $this, 'exclude_recurring_occurrences' ], 10, 2 );

		$occurrences = Occurrences::table_name( true );

		$where .= " AND ($occurrences.has_recurrence IS NULL OR $occurrences.has_recurrence = 0)";

		return $where;
	}
}
