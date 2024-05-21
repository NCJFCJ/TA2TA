<?php
/**
 * Handles Setup of Speakers Post Types.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Post_Types
 */

namespace TEC\Conference\Post_Types;

use TEC\Conference\Plugin;

/**
 * Class Speakers
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Post_Types
 */
class Speakers extends Abstract_Post_Type {

	/**
	 * @inheritDoc
	 */
	public function register_post_type() {

		// Speaker post type labels.
		$speakerlabels = [
			'name'               => _x( 'Speakers', 'Speaker post type label', 'event-schedule-manager' ),
			'singular_name'      => _x( 'Speaker', 'Speaker post type label', 'event-schedule-manager' ),
			'add_new'            => _x( 'Add New', 'Speaker post type label', 'event-schedule-manager' ),
			'add_new_item'       => _x( 'Create New Speaker', 'Speaker post type label', 'event-schedule-manager' ),
			'edit'               => _x( 'Edit', 'Speaker post type label', 'event-schedule-manager' ),
			'edit_item'          => _x( 'Edit Speaker', 'Speaker post type label', 'event-schedule-manager' ),
			'new_item'           => _x( 'New Speaker', 'Speaker post type label', 'event-schedule-manager' ),
			'view'               => _x( 'View Speaker', 'Speaker post type label', 'event-schedule-manager' ),
			'view_item'          => _x( 'View Speaker', 'Speaker post type label', 'event-schedule-manager' ),
			'search_items'       => _x( 'Search Speakers', 'Speaker post type label', 'event-schedule-manager' ),
			'not_found'          => _x( 'No speakers found', 'Speaker post type label', 'event-schedule-manager' ),
			'not_found_in_trash' => _x( 'No speakers found in Trash', 'Speaker post type label', 'event-schedule-manager' ),
			'parent_item_colon'  => _x( 'Parent Speaker:', 'Speaker post type label', 'event-schedule-manager' ),
		];

		$args = [
			'labels'             => $speakerlabels,
			'rewrite'            => [ 'slug' => 'speakers', 'with_front' => false ],
			'supports'           => [ 'title', 'editor', 'revisions', 'thumbnail', 'page-attributes', 'excerpt' ],
			'menu_position'      => 20,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'can_export'         => true,
			'capability_type'    => 'post',
			'hierarchical'       => false,
			'query_var'          => true,
			'show_in_menu'       => $this->get_menu_slug(),
			'show_in_rest'       => true,
			'rest_base'          => 'speakers',
			'has_archive'        => false,
		];

		/**
		 * Filters the arguments for registering the 'speakers' post type.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args The arguments for registering the post type.
		 *
		 * @return array The filtered arguments.
		 */
		apply_filters( 'tec_conference_schedule_speakers_post_type_args', $args );

		$this->post_type_object = register_post_type( Plugin::SPEAKER_POSTTYPE, $args );
	}

	/**
	 * @inheritDoc
	 */
	public function get_title_text(): string {
		return _x( 'Enter Speaker Full Name Here', 'Speaker title placeholder', 'event-schedule-manager' );
	}

	/**
	 * Sets the single template for the speaker post type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $single_template The single template path.
	 *
	 * @return string The single template path.
	 */
	public function set_single_template( $single_template ) {
		global $post;

		if ( $post->post_type !== Plugin::SPEAKER_POSTTYPE ) {
			return $single_template;
		}

		return trailingslashit( dirname( CONFERENCE_SCHEDULE_PRO_FILE ) ) . 'templates/speaker-template.php';
	}
}
