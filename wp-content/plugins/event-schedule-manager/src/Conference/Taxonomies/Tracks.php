<?php

/**
 * Handles Setup of Tracks Taxonomy.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Taxonomies
 */

namespace TEC\Conference\Taxonomies;

use TEC\Conference\Plugin;

/**
 * Class Tracks
 *
 * Handles the registration and management of the Tracks taxonomy.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Taxonomies
 */
class Tracks extends Abstract_Taxonomy {

	/**
	 * @inheritdoc
	 */
	public function register_taxonomy() {

		// Labels for tracks.
		$track_labels = [
			'name'              => _x( 'Tracks', 'Tracks taxonomy label', 'event-schedule-manager' ),
			'singular_name'     => _x( 'Track', 'Tracks taxonomy label', 'event-schedule-manager' ),
			'search_items'      => _x( 'Search Tracks', 'Tracks taxonomy label', 'event-schedule-manager' ),
			'popular_items'     => _x( 'Popular Tracks', 'Tracks taxonomy label', 'event-schedule-manager' ),
			'all_items'         => _x( 'All Tracks', 'Tracks taxonomy label', 'event-schedule-manager' ),
			'parent_item'       => _x( 'Parent Track Category', 'Tracks taxonomy label', 'event-schedule-manager' ),
			'parent_item_colon' => _x( 'Parent Track Category:', 'Tracks taxonomy label', 'event-schedule-manager' ),
			'edit_item'         => _x( 'Edit Track', 'Tracks taxonomy label', 'event-schedule-manager' ),
			'update_item'       => _x( 'Update Track', 'Tracks taxonomy label', 'event-schedule-manager' ),
			'add_new_item'      => _x( 'Add Track', 'Tracks taxonomy label', 'event-schedule-manager' ),
			'new_item_name'     => _x( 'New Track', 'Tracks taxonomy label', 'event-schedule-manager' ),
		];

		$args = [
			'labels'            => $track_labels,
			'rewrite'           => [ 'slug' => 'track' ],
			'query_var'         => 'track',
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rest_base'         => 'session_track',
		];

		/**
		 * Filters the arguments for registering the 'tec_track' taxonomy.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args The arguments for registering the taxonomy.
		 *
		 * @return array The filtered arguments.
		 */
		$args = apply_filters( 'tec_conference_schedule_tec_track_taxonomy_args', $args );

		// Register the Tracks taxonomy.
		$this->taxonomy_object = register_taxonomy( Plugin::TRACK_TAXONOMY, Plugin::SESSION_POSTTYPE, $args );
	}
}
