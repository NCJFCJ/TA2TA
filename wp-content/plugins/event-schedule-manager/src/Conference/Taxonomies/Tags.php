<?php

/**
 * Handles Setup of Tags Taxonomy.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Taxonomies
 */

namespace TEC\Conference\Taxonomies;

use TEC\Conference\Plugin;

/**
 * Class Tags
 *
 * Handles the registration and management of the Tags taxonomy.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Taxonomies
 */
class Tags extends Abstract_Taxonomy {

	/**
	 * @inheritdoc
	 */
	public function register_taxonomy() {

		// Labels for tags.
		$tag_labels = [
			'name'              => _x( 'Tags', 'Tags taxonomy label', 'event-schedule-manager' ),
			'singular_name'     => _x( 'Tag', 'Tags taxonomy label', 'event-schedule-manager' ),
			'search_items'      => _x( 'Search Tags', 'Tags taxonomy label', 'event-schedule-manager' ),
			'popular_items'     => _x( 'Popular Tags', 'Tags taxonomy label', 'event-schedule-manager' ),
			'all_items'         => _x( 'All Tags', 'Tags taxonomy label', 'event-schedule-manager' ),
			'parent_item'       => _x( 'Parent Tag Category', 'Tags taxonomy label', 'event-schedule-manager' ),
			'parent_item_colon' => _x( 'Parent Tag Category:', 'Tags taxonomy label', 'event-schedule-manager' ),
			'edit_item'         => _x( 'Edit Tag', 'Tags taxonomy label', 'event-schedule-manager' ),
			'update_item'       => _x( 'Update Tag', 'Tags taxonomy label', 'event-schedule-manager' ),
			'add_new_item'      => _x( 'Add Tag', 'Tags taxonomy label', 'event-schedule-manager' ),
			'new_item_name'     => _x( 'New Tag', 'Tags taxonomy label', 'event-schedule-manager' ),
		];

		$args = [
			'labels'            => $tag_labels,
			'rewrite'           => [ 'slug' => 'session_tag' ],
			'query_var'         => 'session_tag',
			'hierarchical'      => false,
			'public'            => true,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rest_base'         => 'session_tag',
		];

		/**
		 * Filters the arguments for registering the 'tec_session_tag' taxonomy.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args The arguments for registering the taxonomy.
		 *
		 * @return array The filtered arguments.
		 */
		$args = apply_filters( 'tec_conference_schedule_tec_session_tag_taxonomy_args', $args );

		// Register the Tags taxonomy.
		$this->taxonomy_object = register_taxonomy( Plugin::TAGS_TAXONOMY, Plugin::SESSION_POSTTYPE, $args );
	}
}
