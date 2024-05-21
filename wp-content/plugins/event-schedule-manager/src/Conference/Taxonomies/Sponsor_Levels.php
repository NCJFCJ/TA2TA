<?php

/**
 * Handles Setup of Sponsor Levels Taxonomy.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Taxonomies
 */

namespace TEC\Conference\Taxonomies;

use TEC\Conference\Plugin;

/**
 * Class Sponsor_Levels
 *
 * Handles the registration and management of the Sponsor Levels taxonomy.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Taxonomies
 */
class Sponsor_Levels extends Abstract_Taxonomy {

	/**
	 * @inheritdoc
	 */
	public function register_taxonomy() {

		// Labels for sponsor levels.
		$sponsor_level_labels = [
			'name'              => _x( 'Sponsor Levels', 'Sponsor Levels taxonomy label', 'event-schedule-manager' ),
			'singular_name'     => _x( 'Sponsor Level', 'Sponsor Levels taxonomy label', 'event-schedule-manager' ),
			'search_items'      => _x( 'Search Sponsor Levels', 'Sponsor Levels taxonomy label', 'event-schedule-manager' ),
			'popular_items'     => _x( 'Popular Sponsor Levels', 'Sponsor Levels taxonomy label', 'event-schedule-manager' ),
			'all_items'         => _x( 'All Sponsor Levels', 'Sponsor Levels taxonomy label', 'event-schedule-manager' ),
			'parent_item'       => _x( 'Parent Sponsor Levels Category', 'Sponsor Levels taxonomy label', 'event-schedule-manager' ),
			'parent_item_colon' => _x( 'Parent Sponsor Levels Category:', 'Sponsor Levels taxonomy label', 'event-schedule-manager' ),
			'edit_item'         => _x( 'Edit Sponsor Level', 'Sponsor Levels taxonomy label', 'event-schedule-manager' ),
			'update_item'       => _x( 'Update Sponsor Level', 'Sponsor Levels taxonomy label', 'event-schedule-manager' ),
			'add_new_item'      => _x( 'Add Sponsor Level', 'Sponsor Levels taxonomy label', 'event-schedule-manager' ),
			'new_item_name'     => _x( 'New Sponsor Level', 'Sponsor Levels taxonomy label', 'event-schedule-manager' ),
		];

		$args = [
			'labels'            => $sponsor_level_labels,
			'rewrite'           => [ 'slug' => 'sponsor-level' ],
			'query_var'         => 'sponsor-level',
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rest_base'         => 'session_sponsor_level',
		];

		/**
		 * Filters the arguments for registering the 'tec_sponsor_level' taxonomy.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args The arguments for registering the taxonomy.
		 *
		 * @return array The filtered arguments.
		 */
		$args = apply_filters( 'tec_conference_schedule_tec_sponsor_level_taxonomy_args', $args );

		// Register the Sponsor Levels taxonomy.
		$this->taxonomy_object = register_taxonomy( Plugin::SPONSOR_LEVEL_TAXONOMY, Plugin::SPONSOR_POSTTYPE, $args );
	}

	/**
	 * Get the taxonomy name for the Sponsor Levels taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @return string  The taxonomy name.
	 */
	public function get_taxonomy_name(): string {
		return Plugin::SPONSOR_LEVEL_TAXONOMY;
	}
}
