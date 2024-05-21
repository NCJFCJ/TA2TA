<?php
/**
 * Collects references to objects of a specific type attached to a WordPress filter.
 *
 * @since   5.4.0
 *
 * @package TEC\Filter_Bar\Custom_Tables\V1
 */

namespace TEC\Filter_Bar\Custom_Tables\V1;

use SplObjectStorage;

/**
 * Class Filtering_Refs_Collector
 *
 * @since   5.4.0
 *
 * @package TEC\Filter_Bar\Custom_Tables\V1
 */
class Filtering_Refs_Collector {

	/**
	 * The fully-qualified name of the class to collect references for.
	 *
	 * @since 5.4.0
	 *
	 * @var string
	 */
	private $target_class;

	/**
	 * A list of the target filter tags the references collector should scan.
	 *
	 * @since 5.4.0
	 *
	 * @var array<string,int> A map from the target filters to the priority to check.
	 */
	private $target_tags;

	/**
	 * Filtering_Refs_Collector constructor.
	 *
	 * @since 5.4.0
	 *
	 * @param string            $target_class   The fully-qualified name of the class to collect references for.
	 * @param array<string,int> $target_filters A map from the target filters to the priority to check.
	 */
	public function __construct( $target_class, array $target_filters ) {
		$this->target_class = $target_class;
		$this->target_tags  = $target_filters;
	}

	/**
	 * Parses the global filters list to collect the references to any Filter Bar filter
	 * that might filter the Queries SQL clauses.
	 *
	 * @since 5.4.0
	 *
	 * @return SplObjectStorage A collection of references to Filter Bar filters that are, currently,
	 *                          filtering the query.
	 */
	public function get_references() {
		$filters = new SplObjectStorage();

		global $wp_filter;
		foreach ( $this->target_tags as $tag => $priority ) {
			if ( isset( $wp_filter[ $tag ][ $priority ] ) ) {
				foreach ( $wp_filter[ $tag ][ $priority ] as $callback ) {
					if ( ! (
						isset( $callback['function'][0] )
						&& $callback['function'][0] instanceof $this->target_class
						&& ! $filters->contains( $callback['function'][0] )
					) ) {
						continue;
					}

					// The instance reference will be the first argument of the callback callable array.
					$filters->attach( $callback['function'][0] );
				}
			}
		}

		return $filters;
	}
}
