<?php
/**
 * Abstract class to handle setup of custom taxonomies
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Taxonomies
 */

namespace TEC\Conference\Taxonomies;

use TEC\Conference\Admin\Traits\Menu_Utilities;
use WP_Taxonomy;

/**
 * Class Abstract_Taxonomy
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Taxonomies
 */
abstract class Abstract_Taxonomy {

	use Menu_Utilities;

	/**
	 * The registered post type object.
	 *
	 * @since 1.0.0
	 *
	 * @var WP_Taxonomy
	 */
	protected $taxonomy_object;

	/**
	 * Abstract_Post_Types constructor.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Taxonomy $post_type_object The custom post type object.
	 */
	public function get_taxonomy_object() {
		return $this->taxonomy_object;
	}

	/**
	 * Registers the custom taxonomy.
	 *
	 * @since 1.0.0
	 */
	abstract public function register_taxonomy();
}
