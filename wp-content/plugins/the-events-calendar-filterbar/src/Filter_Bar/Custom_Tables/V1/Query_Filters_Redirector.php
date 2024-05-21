<?php
/**
 * Detaches, and reattaches, Filter Bar SQL queries modifications from the original Query to the corresponding
 * Custom Tables one.
 *
 * @since   5.4.0
 *
 * @package TEC\Filter_Bar\Custom_Tables\V1
 */

namespace TEC\Filter_Bar\Custom_Tables\V1;

use SplObjectStorage;
use TEC\Events\Custom_Tables\V1\Tables\Occurrences;
use TEC\Events\Custom_Tables\V1\WP_Query\Custom_Tables_Query;
use TEC\Events_Pro\Custom_Tables\V1\Events\Provisional\ID_Generator;
use Tribe__Events__Filterbar__Filter as Filter;
use Tribe__Events__Filterbar__Filters__Day_Of_Week as Day_Of_Week_Filter;
use Tribe__Events__Filterbar__Filters__Time_Of_Day as Time_Of_Day_Filter;
use WP_Query;

/**
 * Class Query_Filters_Redirector
 *
 * @since   5.4.0
 *
 * @package TEC\Filter_Bar\Custom_Tables\V1
 */
class Query_Filters_Redirector {
	/**
	 * A reference to the object storage map from `WP_Query` instances to the WHERE and JOIN clauses that should be
	 * applied to them.
	 *
	 * @since 5.4.0
	 *
	 * @var SplObjectStorage<WP_Query,<string,mixed>>
	 */
	private $query_clauses;

	/**
	 * A reference to the Filtering References Collector instance.
	 *
	 * @since 5.4.0
	 *
	 * @var Filtering_Refs_Collector
	 */
	private $refs_collector;

	/**
	 * Query_Filters_Redirector constructor.
	 *
	 * @since 5.4.0
	 *
	 * @param Filtering_Refs_Collector $refs_collector A reference to the Filtering References Collector instance.
	 */
	public function __construct( Filtering_Refs_Collector $refs_collector ) {
		$this->refs_collector = $refs_collector;
		$this->query_clauses  = new SplObjectStorage;
	}

	/**
	 * Collects, by "discharging" them, the JOIN and WHERE SQL clause modifications Filter Bar
	 * filters would do the current Query.
	 *
	 * This method is the first part in a "filter transplant" from the original Query Filter Bar
	 * filters would target, to the Custom Tables Query that replaces it.
	 *
	 * @since 5.4.0
	 *
	 * @param string   $sql   The input SQL (WHERE) statement.
	 * @param WP_Query $query A reference to the Query object that is currently being filtered.
	 *
	 * @return string The input SQL statement, untouched at this stage.
	 */
	public function collect_clause_updates( string $sql, WP_Query $query ): string {
		// Do not filter if the original Filter would not apply at all.
		if (
		! (
			$query->tribe_is_event
			|| $query->tribe_is_event_category
			|| doing_action( 'tribe_repository_events_query' )
		)
		) {
			// Not a query the Filters would apply to, bail.
			return $sql;
		}

		if ( ! $query instanceof WP_Query ) {
			return $sql;
		}

		if ( isset( $this->query_clauses[ $query ] ) ) {
			// Already fetched for this query.
			return $sql;
		}

		$where_clauses_buffer = [];
		$join_clauses_buffer  = [];

		/** @var \Tribe__Events__Filterbar__Filter $filter */
		foreach ( $this->refs_collector->get_references() as $filter ) {
			/*
			 * "Discharge" the Filter JOIN and WHERE clause filtering, save the Filter instance reference.
			 * Use a space string to bypass some Filters sensing of empty input queries.
			 */
			$where = (string) $filter->addQueryWhere( ' ', $query );
			$join  = (string) $filter->addQueryJoin( ' ', $query );

			if ( ' ' !== $where ) {
				$where_clauses_buffer[] = [ $filter, $where ];
			}

			if ( ' ' !== $join ) {
				$join_clauses_buffer[] = [ $filter, $join ];
			}
		}

		if ( count( $where_clauses_buffer ) || count( $join_clauses_buffer ) ) {
			$this->query_clauses[ $query ] = [
				'where' => $where_clauses_buffer,
				'join'  => $join_clauses_buffer,
			];
		}

		// Return the SQL, not modified.
		return $sql;
	}

	/**
	 * Filters a Query WHERE SQL clause to add, if required and applicable, the modifications
	 * Filter Bar filters would have, originally, applied to it.
	 *
	 * @param string   $where The input WHERE SQL clause.
	 * @param WP_Query $query A reference to the Query object that is currently being filtered.
	 *
	 * @return string The filtered JOIN SQL clause.
	 */
	public function apply_where_clauses( string $where, WP_Query $query ): string {
		return $this->filter_custom_tables_query_clause( $where, $query, 'where' );
	}

	/**
	 * Filters a Query JOIN SQL clause to add, if required and applicable, the modifications
	 * Filter Bar filters would have, originally, applied to it.
	 *
	 * @since 5.4.0
	 *
	 * @param WP_Query $query A reference to the Query object that is currently being filtered.
	 * @param string   $join  The input JOIN SQL clause.
	 *
	 * @return string The filtered JOIN SQL clause.
	 */
	public function apply_join_clauses( string $join, WP_Query $query ): string {
		return $this->filter_custom_tables_query_clause( $join, $query, 'join' );
	}

	/**
	 * Filters a SQL clause to add, if applicable, the modifications Filter Bar filters would have
	 * applied to the original query.
	 *
	 * This method is the second part in a "filter transplant" from the original Query Filter Bar
	 * filters would target, to the Custom Tables Query that replaces it.
	 *
	 * @since 5.4.0
	 *
	 * @param WP_Query $query        A reference to the Query object currently being filtered.
	 * @param string   $type         The type of SQL clause to filter, supported are `join` and `where`.
	 *
	 * @param string   $input_clause The input SQL clause to filter.
	 *
	 * @return string The filtered SQL clause string, if applicable.
	 */
	private function filter_custom_tables_query_clause( string $input_clause, WP_Query $query, string $type ): string {
		if ( ! $query instanceof Custom_Tables_Query ) {
			return $input_clause;
		}

		$wp_query = $query->get_wp_query();

		if ( ! (
			$wp_query instanceof WP_Query
			&& isset( $this->query_clauses[ $wp_query ][ $type ] )
		) ) {
			return $input_clause;
		}

		foreach ( $this->query_clauses[ $wp_query ][ $type ] as list( $filter, $clause ) ) {
			$clean_clause = trim( $clause );

			if ( empty( $clause ) || false !== strpos( $input_clause, $clean_clause ) ) {
				// Already applied, continue.
				continue;
			}

			// Add a newline and a space to insulate from some malformed Filter Bar query modifications.
			$input_clause .= "\n " . $this->redirect_filter_clause( $filter, $clean_clause, $type );
		}

		if ( 'join' === $type ) {
			// Since the `posts_join` filter will fire after the `posts_where` one, we can garbage collect a bit.
			unset( $this->query_clauses[ $wp_query ] );
		}

		return $input_clause;
	}

	/**
	 * Returns a list of the clauses of a type collected for a WordPress query reference.
	 *
	 * @since 5.4.0
	 *
	 * @param WP_Query $query A reference to the WordPress Query object to return the collected clauses for.
	 * @param string   $type  The type of SQL clause, `where` or `join` supported, to return collected clauses for.
	 *
	 * @return array<string> A list of the clauses of the specified type collected for the Query.
	 */
	public function get_collected_clauses( WP_Query $query, string $type ): array {
		return isset( $this->query_clauses[ $query ][ $type ] ) ?
			$this->query_clauses[ $query ][ $type ]
			: [];
	}

	/**
	 * Redirects the SQL clause produced by a Filter Bar Filter to the Custom Tables, if required.
	 *
	 * @since 5.4.0
	 *
	 * @param Filter $filter A reference to the Filter instance that produced the SQL clause to redirect.
	 * @param string $clause The SQL clause, as produced by the Filter.
	 * @param string $type   The type of SQL clause to redirect, either `join` or `where`.
	 *
	 * @return string The SQL clause, its content modified to redirect it to the Custom Tables, if required.
	 */
	private function redirect_filter_clause( Filter $filter, string $clause, string $type ): string {
		if ( $filter instanceof Day_Of_Week_Filter ) {
			return $this->redirect_day_of_week_clause( $clause, $type );
		}

		if ( $filter instanceof Time_Of_Day_Filter ) {
			return $this->redirect_time_of_day_clause( $filter, $clause, $type );
		}

		// Another type of filter that will not require redirection.
		return $clause;
	}

	/**
	 * Redirects the Day of Week Filter SQL clause to the Custom Tables.
	 *
	 * @since 5.4.0
	 *
	 * @param string $clause The SQL clause, as produced by the Filter.
	 * @param string $type   The type of SQL clause to redirect, either `join` or `where`.
	 *
	 * @return string The Day of Week SQL clause, modified to redirect it to the Custom Tables.
	 */
	private function redirect_day_of_week_clause( string $clause, string $type ): string {
		if ( 'join' === $type ) {
			// No need to JOIN at all.
			return '';
		}

		// Dealing with another type of clause that should be redirected to the Occurrences Custom Table.

		global $wpdb;

		$occurrences = Occurrences::table_name( true );

		return str_replace(
			[
				$wpdb->postmeta . '.meta_value',
				'tribe_event_end_date.meta_value',
			],
			[
				$occurrences . '.start_date',
				$occurrences . '.end_date',
			],
			$clause
		);
	}

	/**
	 * Redirects the Time of Day Filter SQL clause to the Custom Tables.
	 *
	 * @since 5.4.0
	 *
	 * @param string $clause The SQL clause, as produced by the Filter.
	 * @param string $type   The type of SQL clause to redirect, either `join` or `where`.
	 *
	 * @return string The Time of Day SQL clause, modified to redirect it to the Custom Tables.
	 */
	private function redirect_time_of_day_clause( Time_Of_Day_Filter $filter, string $clause, string $type ): string {
		$all_day_alias = $filter->get_all_day_table_alias_prefix();

		if ( 'join' === $type ) {
			$join_frags = (array) preg_split( '/((?:LEFT|INNER) JOIN)/', $clause, - 1, PREG_SPLIT_DELIM_CAPTURE );
			$join_type  = null;

			foreach ( $join_frags as $join_frag ) {
				if ( empty( trim( $join_frag ) ) ) {
					continue;
				}

				if ( false !== strpos( $join_frag, 'JOIN' ) ) {
					// It's a JOIN type, keep and pre-pend to the next frag.
					$join_type = $join_frag;
					continue;
				}

				// Not a JOIN type, pre-pend type.
				if ( false !== strpos( $join_frag, $all_day_alias ) ) {
					return $join_type . ' ' . $join_frag;
				}
			}

			// By default, do not JOIN and only leave the JOIN on the custom fields table for all-day.
			return '';
		}

		$occurrences = Occurrences::table_name( true );

		/*
		 * The Time of Day Filter will use dynamic, hashed, aliases for its tables.
		 * We do not need to alias the all-day one as it should still be queried from custom fields.
		 */

		return str_replace(
			[
				$filter->get_tod_start_alias() . '.meta_value',
				$filter->get_tod_duration_alias() . '.meta_value',
			],
			[
				$occurrences . '.start_date',
				$occurrences . '.duration',
			],
			$clause
		);
	}

	/**
	 * Redirects a query on the posts table for post IDs to the Occurrences table, on the `occurrence_id`
	 * column.
	 *
	 * @since 5.4.0
	 *
	 * @param array{table: string, field: string, ids: array<int>} $table_field_ids The table, field and IDs
	 *                                                                              set from the posts table.
	 *
	 * @return array{table: string, field: string, ids: array<int>} The filtered table, field and IDs set,
	 *                                                              redirected to the Occurrences table.
	 */
	public function redirect_table_field_ids( array $table_field_ids ): array {
		$table_field_ids['table'] = Occurrences::table_name( true );
		$table_field_ids['field'] = 'occurrence_id';
		$id_gen = tribe( ID_Generator::class );
		$table_field_ids['ids'] = array_map( static function ( $id ) use ( $id_gen ): int {
			return $id_gen->unprovide_id( (int) $id );
		}, $table_field_ids['ids'] );

		return $table_field_ids;
	}
}
