<?php
/**
 * Handles Setup of Post Types.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Post_Types
 */

namespace TEC\Conference\Post_Types;

use TEC\Conference\Plugin;

/**
 * Class Sessions
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Post_Types
 */
class Sessions extends Abstract_Post_Type {

	/**
	 * @inheritDoc
	 */
	public function register_post_type() {

		// Session post type labels.
		$sessionlabels = [
			'name'               => _x( 'Sessions', 'Session post type label', 'event-schedule-manager' ),
			'singular_name'      => _x( 'Session', 'Session post type label', 'event-schedule-manager' ),
			'add_new'            => _x( 'Add New', 'Session post type label', 'event-schedule-manager' ),
			'add_new_item'       => _x( 'Create New Session', 'Session post type label', 'event-schedule-manager' ),
			'edit'               => _x( 'Edit', 'Session post type label', 'event-schedule-manager' ),
			'edit_item'          => _x( 'Edit Session', 'Session post type label', 'event-schedule-manager' ),
			'new_item'           => _x( 'New Session', 'Session post type label', 'event-schedule-manager' ),
			'view'               => _x( 'View Session', 'Session post type label', 'event-schedule-manager' ),
			'view_item'          => _x( 'View Session', 'Session post type label', 'event-schedule-manager' ),
			'search_items'       => _x( 'Search Sessions', 'Session post type label', 'event-schedule-manager' ),
			'not_found'          => _x( 'No sessions found', 'Session post type label', 'event-schedule-manager' ),
			'not_found_in_trash' => _x( 'No sessions found in Trash', 'Session post type label', 'event-schedule-manager' ),
			'parent_item_colon'  => _x( 'Parent Session:', 'Session post type label', 'event-schedule-manager' ),
		];

		$args = [
			'labels'             => $sessionlabels,
			'rewrite'            => [
				'slug'       => 'sessions',
				'with_front' => false,
			],
			'supports'           => [ 'title', 'editor', 'author', 'revisions', 'thumbnail', 'custom-fields', 'excerpt' ],
			'menu_position'      => 15,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'can_export'         => true,
			'capability_type'    => 'post',
			'hierarchical'       => false,
			'query_var'          => true,
			'show_in_menu'       => $this->get_menu_slug(),
			'show_in_rest'       => true,
			'rest_base'          => 'sessions',
			'has_archive'        => false,
		];

		/**
		 * Filters the arguments for registering the 'sessions' post type.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args The arguments for registering the post type.
		 *
		 * @return array The filtered arguments.
		 */
		apply_filters( 'tec_conference_schedule_sessions_post_type_args', $args );

		$this->post_type_object = register_post_type( Plugin::SESSION_POSTTYPE, $args );
	}

	/**
	 * @inheritDoc
	 */
	public function get_title_text(): string {
		return _x( 'Enter Session Title Here', 'Session title placeholder', 'event-schedule-manager' );
	}

	/**
	 * Displays custom post types in the "At a Glance" dashboard widget.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string> $items The array of items to be displayed.
	 *
	 * @return array<string> $items The maybe modified array of items to be displayed.
	 */
	public function cpt_at_glance( $items ) {
		$post_types = [
			Plugin::SESSION_POSTTYPE,
			Plugin::SPEAKER_POSTTYPE,
			Plugin::SPONSOR_POSTTYPE,
		];

		foreach ( $post_types as $post_type_name ) {
			$post_type = get_post_type_object( $post_type_name );
			$num_posts = wp_count_posts( $post_type->name );
			$num       = number_format_i18n( $num_posts->publish );
			$text      = _n( $post_type->labels->singular_name, $post_type->labels->name, intval( $num_posts->publish ) );
			if ( current_user_can( 'edit_posts' ) ) {
				$items[] = '<a href="edit.php?post_type=' . $post_type->name . '">' . $num . ' ' . $text . '</a>';
			} else {
				$items[] = '<span>' . $num . ' ' . $text . '</span>';
			}
		}

		return $items;
	}

	/**
	 * Sets the single template for the session post type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $single_template The single template path.
	 *
	 * @return string The single template path.
	 */
	public function set_single_template( $single_template ) {
		global $post;

		if ( $post->post_type !== Plugin::SESSION_POSTTYPE ) {
			return $single_template;
		}

		return trailingslashit( dirname( CONFERENCE_SCHEDULE_PRO_FILE ) ) . 'templates/session-template.php';
	}
}
