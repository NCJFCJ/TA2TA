<?php
/**
 * Handles Setup of Sponsors Post Types.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Post_Types
 */

namespace TEC\Conference\Post_Types;

use TEC\Conference\Plugin;

/**
 * Class Sponsors
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Post_Types
 */
class Sponsors extends Abstract_Post_Type {

	/**
	 * @inheritDoc
	 */
	public function register_post_type() {

		// Sponsor post type labels.
		$sponsorlabels = [
			'name'               => _x( 'Sponsors', 'Sponsor post type label', 'event-schedule-manager' ),
			'singular_name'      => _x( 'Sponsor', 'Sponsor post type label', 'event-schedule-manager' ),
			'add_new'            => _x( 'Add New', 'Sponsor post type label', 'event-schedule-manager' ),
			'add_new_item'       => _x( 'Create New Sponsor', 'Sponsor post type label', 'event-schedule-manager' ),
			'edit'               => _x( 'Edit', 'Sponsor post type label', 'event-schedule-manager' ),
			'edit_item'          => _x( 'Edit Sponsor', 'Sponsor post type label', 'event-schedule-manager' ),
			'new_item'           => _x( 'New Sponsor', 'Sponsor post type label', 'event-schedule-manager' ),
			'view'               => _x( 'View Sponsor', 'Sponsor post type label', 'event-schedule-manager' ),
			'view_item'          => _x( 'View Sponsor', 'Sponsor post type label', 'event-schedule-manager' ),
			'search_items'       => _x( 'Search Sponsors', 'Sponsor post type label', 'event-schedule-manager' ),
			'not_found'          => _x( 'No sponsors found', 'Sponsor post type label', 'event-schedule-manager' ),
			'not_found_in_trash' => _x( 'No sponsors found in Trash', 'Sponsor post type label', 'event-schedule-manager' ),
			'parent_item_colon'  => _x( 'Parent Sponsor:', 'Sponsor post type label', 'event-schedule-manager' ),
		];

		$args = [
			'labels'          => $sponsorlabels,
			'rewrite'         => [ 'slug' => 'sponsors', 'with_front' => false ],
			'supports'        => [ 'title', 'editor', 'revisions', 'thumbnail' ],
			'menu_position'   => 21,
			'public'          => true,
			'show_ui'         => true,
			'can_export'      => true,
			'capability_type' => 'post',
			'hierarchical'    => false,
			'query_var'       => true,
			'show_in_menu'    => $this->get_menu_slug(),
			'show_in_rest'    => true,
			'rest_base'       => 'sponsors',
		];

		/**
		 * Filters the arguments for registering the 'sponsors' post type.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args The arguments for registering the post type.
		 *
		 * @return array The filtered arguments.
		 */
		apply_filters( 'tec_conference_schedule_sponsors_post_type_args', $args );

		$this->post_type_object = register_post_type( Plugin::SPONSOR_POSTTYPE, $args );
	}

	/**
	 * @inheritDoc
	 */
	public function get_title_text(): string {
		return _x( 'Enter Sponsoring Company Name Here', 'Sponsor title placeholder', 'event-schedule-manager' );
	}

	/**
	 * Sets the single template for the sponsor post type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $single_template The single template path.
	 *
	 * @return string The single template path.
	 */
	public function set_single_template( $single_template ) {
		global $post;

		if ( $post->post_type !== Plugin::SPONSOR_POSTTYPE ) {
			return $single_template;
		}

		return trailingslashit( dirname( CONFERENCE_SCHEDULE_PRO_FILE ) ) . 'templates/sponsor-template.php';
	}
}
