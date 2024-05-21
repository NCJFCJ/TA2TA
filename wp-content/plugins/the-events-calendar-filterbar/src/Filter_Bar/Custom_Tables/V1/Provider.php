<?php
/**
 * Handles the integration between Filter Bar and the custom tables based event fetching.
 *
 * @since   5.4.0
 *
 * @package TEC\Filter_Bar\Custom_Tables\V1
 */

namespace TEC\Filter_Bar\Custom_Tables\V1;

use Exception;
use TEC\Events\Custom_Tables\V1\Migration\State;
use TEC\Events\Custom_Tables\V1\Provider as TEC_Provider;
use TEC\Events_Pro\Custom_Tables\V1\Integrations\Filter_Bar\Series_Filter;
use TEC\Filter_Bar\Custom_Tables\V1\Query_Filters_Redirector as Redirector;
use Throwable;
use Tribe__Context as Context;
use Tribe__Dependency;
use Tribe__Events__Filterbar__Filter as Filter;
use Tribe__Utils__Array as Arr;
use TEC\Common\Contracts\Service_Provider;
use WP_Query;

/**
 * Class Provider
 *
 * @since   5.4.0
 *
 * @package TEC\Filter_Bar\Custom_Tables\V1
 */
class Provider extends Service_Provider {

	/**
	 * A flag property indicating whether the Service Provider did register or not.
	 *
	 * @since 5.4.0
	 *
	 * @var bool
	 */
	private $did_register = false;

	/**
	 * Registers the filters and implementations required by the Custom Tables implementation.
	 *
	 * @since 5.4.0
	 *
	 * @return bool Whether the Provider did register or not.
	 */
	public function register() {
		if ( $this->did_register ) {
			// Let's avoid double filtering by making sure we're registering at most once.
			return true;
		}

		if ( ! ( class_exists( TEC_Provider::class ) && TEC_Provider::is_active() && class_exists( State::class ) ) ) {
			return false;
		}

		$state = tribe( State::class );
		if ( ! $state->is_migrated() ) {
			return false;
		}

		try {
			$this->set_up_query_filter_redirection();

			if ( tribe( Tribe__Dependency::class )->is_plugin_active( 'Tribe__Events__Pro__Main' ) ) {
				$this->register_series_filter();
				add_filter( 'tec_events_filter_stack_where_table_field_ids', [ $this, 'redirect_table_field_ids' ] );
			}

			$this->did_register = true;

			return true;
		} catch ( Throwable $t ) {
			// This code will never fire on PHP 5.6, but will do in PHP 7.0+.

			/**
			 * Fires an action when an error or exception happens in the
			 * context of Custom Tables v1 implementation AND the server
			 * runs PHP 7.0+.
			 *
			 * @since 5.4.0
			 *
			 * @param Throwable $t The thrown error.
			 */
			do_action( 'tec_custom_tables_v1_error', $t );
		} catch ( Exception $e ) {
			// PHP 5.6 compatible code.

			/**
			 * Fires an action when an error or exception happens in the
			 * context of Custom Tables v1 implementation AND the server
			 * runs PHP 5.6.
			 *
			 * @since 5.4.0
			 *
			 * @param Exception $e The thrown error.
			 */
			do_action( 'tec_custom_tables_v1_error', $e );
		}
	}

	/**
	 * Sets up the redirection of Filter Bar modification to JOIN and WHERE clauses to the Custom Tables queries.
	 *
	 * @since 5.4.0
	 */
	private function set_up_query_filter_redirection() {
		// The Redirector will need to keep state between its different applications: make it a singleton.
		$this->container->singleton( Redirector::class,
			static function () {
				$collector = new Filtering_Refs_Collector(
					Filter::class,
					[
						'posts_where' => 11,
						'posts_join'  => 11
					] );

				return new Redirector( $collector );
			} );

		/*
		 * Step 1: collect the methods hooked by the currently active Filters.
		 * Do this on `posts_where` as it will be triggered before the `posts_join` filter in the `WP_Query`
		 * code.
		 */
		add_filter( 'posts_where', [ $this, 'collect_clause_updates' ], 10, 2 );

		/**
		 * Step 2: apply the updates to the Custom Tables Query WHERE and JOIN clauses.
		 */
		add_filter( 'posts_where', [ $this, 'apply_where_clauses' ], 20, 2 );
		add_filter( 'posts_join', [ $this, 'apply_join_clauses' ], 20, 2 );
	}

	/**
	 * Hooks and sets up the Series Filter to correctly integrate with Filter Bar and Views v2.
	 *
	 * The method will take care to delay the Series Filter and hooking to the very last moment filters are built
	 * by Filter Bar or the Context Filters manager.
	 *
	 * @since 5.4.0
	 */
	private function register_series_filter() {
		$this->container->singleton( Series_Filter::class, static function () {
			return new Series_Filter(
				_x( 'Series', 'The name of the Series Filter Bar Filter.', 'tribe-events-filter-view' ),
				'series_id'
			);
		} );
		$this->hook_to_context();

		add_filter( 'tribe_events_filter_bar_default_filter_names_map', [ $this, 'filter_default_filter_names_map' ] );
	}

	/**
	 * Hooks this Filter instance to the additional, Context-related, filters.
	 *
	 * @since 5.4.0
	 */
	private function hook_to_context() {
		if ( ! has_action( 'tribe_events_filters_create_filters', [ $this, 'build_series_filter' ] ) ) {
			add_action( 'tribe_events_filters_create_filters', [ $this, 'build_series_filter' ] );
		}

		if ( ! has_filter( 'tribe_context_locations', [ $this, 'filter_context_locations' ] ) ) {
			add_filter( 'tribe_context_locations', [ $this, 'filter_context_locations' ] );
		}

		if ( ! has_filter( 'tribe_events_filter_bar_context_to_filter_map', [ $this, 'filter_context_to_filter_map' ] ) ) {
			add_filter( 'tribe_events_filter_bar_context_to_filter_map', [ $this, 'filter_context_to_filter_map' ] );
		}

		if ( ! has_filter( 'tribe_events_filter_bar_option_key_map', [ $this, 'filter_option_key_map' ] ) ) {
			add_filter( 'tribe_events_filter_bar_option_key_map', [ $this, 'filter_option_key_map' ] );
		}
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
	 * @see   Redirector::collect_clause_updates() for the method proxied by this one.
	 *
	 */
	public function collect_clause_updates( $sql, $query ) {
		$redirector = $this->container->make( Redirector::class );

		return $redirector->collect_clause_updates( $sql, $query );
	}

	/**
	 * Filters a Query WHERE SQL clause to add, if required and applicable, the modifications
	 * Filter Bar filters would have, originally, applied to it.
	 *
	 * @param string   $where The input WHERE SQL clause.
	 * @param WP_Query $query A reference to the Query object that is currently being filtered.
	 *
	 * @return string The filtered JOIN SQL clause.
	 *
	 * @see redirector::apply_where_clauses() for the method proxied by this one.
	 */
	public function apply_where_clauses( $where, $query ) {
		$redirector = $this->container->make( Redirector::class );

		return $redirector->apply_where_clauses( $where, $query );
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
	 *
	 * @see   redirector::apply_join_clauses() for the method proxied by this one.
	 */
	public function apply_join_clauses( $join, $query ) {
		$redirector = $this->container->make( Redirector::class );

		return $redirector->apply_join_clauses( $join, $query );
	}

	/**
	 * Builds, thus hooking it to the legacy Filter Bar actions and filters, the instance of the
	 * Series Filter.
	 *
	 * @since 5.4.0
	 */
	public function build_series_filter() {
		$this->container->make( Series_Filter::class );
	}

	/**
	 * Filters the Context locations to add the one that will allow looking up the current
	 * Series Filter values.
	 *
	 * @since 5.4.0
	 *
	 * @param array<string,array> $locations A map of the current Context locations.
	 *
	 * @return array<string,array> A filtered map of Context locations.
	 */
	public function filter_context_locations( array $locations = [] ) {
		$parse_data_from_url = static function ( $view_data ) {
			if ( ! isset( $view_data['url'] ) ) {
				return Context::NOT_FOUND;
			}

			wp_parse_str(
				(string) wp_parse_url( $view_data['url'], PHP_URL_QUERY ),
				$parsed
			);

			return isset( $parsed['tribe_filterbar_series_id'] ) ?
				Arr::list_to_array( $parsed['tribe_filterbar_series_id'] )
				: Context::NOT_FOUND;
		};

		$locations = array_merge( $locations,
			[
				'filterbar_series_id' => [
					'read' => [
						Context::QUERY_VAR     => [ 'tribe_series_id' ],
						Context::REQUEST_VAR   => [ 'tribe_series_id' ],
						Context::LOCATION_FUNC => [ 'view_data', $parse_data_from_url ],
					],
				]
			]
		);

		return $locations;
	}

	/**
	 * Filters the map from Context keys to the corresponding Filter class to add the Series
	 * filter entry.
	 *
	 * @since 5.4.0
	 *
	 * @param array<string,string> $map The unfiltered Context key to Filter class map.
	 *
	 * @return array<string,string> The filtered Context key to Filter class map.
	 */
	public function filter_context_to_filter_map( array $map ) {
		$map['filterbar_series_id'] = Series_Filter::class;

		return $map;
	}

	/**
	 * Filters the reverse map that allows going from a Filter option key to the Filter class to
	 * instantiate.
	 *
	 * @since 5.4.0
	 *
	 * @param array<string,string> $map The input map.
	 *
	 * @return array<string,string> The filtered map, the Series Filter entry added to it.
	 */
	public function filter_option_key_map( array $map = [] ) {
		$map [ Series_Filter::class ] = 'series_id';

		return $map;
	}

	/**
	 * Filters the map that will set the default filter names to add the Series one.
	 *
	 * @since 5.4.0
	 *
	 * @param array<class-string,string> $map A map from the Filter class to their
	 *                                        default names.
	 *
	 * @return array<class-string,string> A map from the Filter class to their
	 *                                    default names updated to add the Series one.
	 */
	public function filter_default_filter_names_map( array $map ) {
		$map[ Series_Filter::class ] = _x( 'Series', 'The default Filter Bar Series filter title.', 'tribe-events-filter-view' );

		return $map;
	}

	/**
	 * Redirects the table, field and IDs set from the posts table to the Occurrences table, if required.
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
		return $this->container->make( Redirector::class )->redirect_table_field_ids( $table_field_ids );
	}
}
