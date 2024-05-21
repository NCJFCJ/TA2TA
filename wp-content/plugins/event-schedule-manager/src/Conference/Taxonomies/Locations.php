<?php

/**
 * Handles Setup of Locations Taxonomy.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Taxonomies
 */

namespace TEC\Conference\Taxonomies;

use TEC\Conference\Plugin;

/**
 * Class Locations
 *
 * Handles the registration and management of the Locations taxonomy.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Taxonomies
 */
class Locations extends Abstract_Taxonomy {

	/**
	 * @inheritdoc
	 */
	public function register_taxonomy() {

		// Labels for locations.
		$location_labels = [
			'name'              => _x( 'Locations', 'Locations taxonomy label', 'event-schedule-manager' ),
			'singular_name'     => _x( 'Location', 'Locations taxonomy label', 'event-schedule-manager' ),
			'search_items'      => _x( 'Search Locations', 'Locations taxonomy label', 'event-schedule-manager' ),
			'popular_items'     => _x( 'Popular Locations', 'Locations taxonomy label', 'event-schedule-manager' ),
			'all_items'         => _x( 'All Locations', 'Locations taxonomy label', 'event-schedule-manager' ),
			'parent_item'       => _x( 'Parent Location Category', 'Locations taxonomy label', 'event-schedule-manager' ),
			'parent_item_colon' => _x( 'Parent Location Category:', 'Locations taxonomy label', 'event-schedule-manager' ),
			'edit_item'         => _x( 'Edit Location', 'Locations taxonomy label', 'event-schedule-manager' ),
			'update_item'       => _x( 'Update Location', 'Locations taxonomy label', 'event-schedule-manager' ),
			'add_new_item'      => _x( 'Add Location', 'Locations taxonomy label', 'event-schedule-manager' ),
			'new_item_name'     => _x( 'New Location', 'Locations taxonomy label', 'event-schedule-manager' ),
		];

		$args = [
			'labels'            => $location_labels,
			'rewrite'           => [ 'slug' => 'location' ],
			'query_var'         => 'location',
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rest_base'         => 'session_location',
		];

		/**
		 * Filters the arguments for registering the 'tec_location' taxonomy.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args The arguments for registering the taxonomy.
		 *
		 * @return array The filtered arguments.
		 */
		$args = apply_filters( 'tec_conference_schedule_tec_location_taxonomy_args', $args );

		// Register the Locations taxonomy.
		$this->taxonomy_object = register_taxonomy( Plugin::LOCATION_TAXONOMY, Plugin::SESSION_POSTTYPE, $args );
	}
}
