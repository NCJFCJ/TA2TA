<?php

/**
 * Handles Setup of Groups Taxonomy.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Taxonomies
 */

namespace TEC\Conference\Taxonomies;

use TEC\Conference\Plugin;

/**
 * Class Groups
 *
 * Handles the registration and management of the Groups taxonomy.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Taxonomies
 */
class Groups extends Abstract_Taxonomy {

	/**
	 * @inheritdoc
	 */
	public function register_taxonomy() {

		// Labels for groups.
		$group_labels = [
			'name'              => _x( 'Speaker Groups', 'Groups taxonomy label', 'event-schedule-manager' ),
			'singular_name'     => _x( 'Speaker Group', 'Groups taxonomy label', 'event-schedule-manager' ),
			'search_items'      => _x( 'Search Groups', 'Groups taxonomy label', 'event-schedule-manager' ),
			'popular_items'     => _x( 'Popular Groups', 'Groups taxonomy label', 'event-schedule-manager' ),
			'all_items'         => _x( 'All Groups', 'Groups taxonomy label', 'event-schedule-manager' ),
			'parent_item'       => _x( 'Parent Group Category', 'Groups taxonomy label', 'event-schedule-manager' ),
			'parent_item_colon' => _x( 'Parent Group Category:', 'Groups taxonomy label', 'event-schedule-manager' ),
			'edit_item'         => _x( 'Edit Group', 'Groups taxonomy label', 'event-schedule-manager' ),
			'update_item'       => _x( 'Update Group', 'Groups taxonomy label', 'event-schedule-manager' ),
			'add_new_item'      => _x( 'Add Group', 'Groups taxonomy label', 'event-schedule-manager' ),
			'new_item_name'     => _x( 'New Group', 'Groups taxonomy label', 'event-schedule-manager' ),
		];

		$args = [
			'labels'            => $group_labels,
			'rewrite'           => [ 'slug' => 'session_group' ],
			'query_var'         => 'session_group',
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rest_base'         => 'session_group',
		];

		/**
		 * Filters the arguments for registering the 'tec_group' taxonomy.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args The arguments for registering the taxonomy.
		 *
		 * @return array The filtered arguments.
		 */
		$args = apply_filters( 'tec_conference_schedule_tec_group_taxonomy_args', $args );

		// Register the Groups taxonomy.
		$this->taxonomy_object = register_taxonomy( Plugin::GROUP_TAXONOMY, Plugin::SPEAKER_POSTTYPE, $args );
	}

	/**
	 * Get the taxonomy name for the Group taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @return string  The taxonomy name.
	 */
	public function get_taxonomy_name(): string {
		return Plugin::GROUP_TAXONOMY;
	}
}
