<?php
/**
 * Abstract class to handle setup of post types.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Post_Types
 */

namespace TEC\Conference\Post_Types;

use TEC\Conference\Admin\Traits\Menu_Utilities;
use WP_Post_Type;
use WP_Post;

/**
 * Class Abstract_Post_Type
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Post_Types
 */
abstract class Abstract_Post_Type {

	use Menu_Utilities;

	/**
	 * The registered post type object.
	 *
	 * @since 1.0.0
	 *
	 * @var WP_Post_Type
	 */
	protected $post_type_object;

	/**
	 * Abstract_Post_Types constructor.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Post_Type $post_type_object The custom post type object.
	 */
	public function get_post_type_object() {
		return $this->post_type_object;
	}

	/**
	 * Registers the custom post type.
	 *
	 * @since 1.0.0
	 */
	abstract public function register_post_type();

	/**
	 * Changes the title placeholder text for the custom post type.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $title The current placeholder text.
	 * @param WP_Post $post  The current post object.
	 *
	 * @return string The modified placeholder text.
	 */
	public function change_title_text( $title, $post ) {
		if ( $post->post_type !== $this->post_type_object->name ) {
			return $title;
		}

		$title = $this->get_title_text();

		return $title;
	}

	/**
	 * Returns the title text for the custom post type.
	 *
	 * @since 1.0.0
	 *
	 * @return string The title text.
	 */
	abstract public function get_title_text(): string;
}
