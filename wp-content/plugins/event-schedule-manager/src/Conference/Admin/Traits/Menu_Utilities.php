<?php
/**
 * Traits to use for Admin Menus
 *
 * @since 1.0.0
 *
 * @package TEC\Conference\Admin\Traits
 */

namespace TEC\Conference\Admin\Traits;

use TEC\Conference\Plugin;

/**
 * Class Menu_Utilities
 *
 * @since 1.0.0
 *
 * @package TEC\Conference\Admin\Traits
 */
trait Menu_Utilities {

	/**
	 * The Event Schedule Manager menu slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $menu_slug = 'edit.php?post_type=' . Plugin::SESSION_POSTTYPE;

	/**
	 * Get the menu slug for the Event Schedule Manager menu items.
	 *
	 * @since 1.0.0
	 *
	 * @return string  The menu slug.
	 */
	public function get_menu_slug(): string {
		/**
		 * Filters the menu slug for the Event Schedule Manager menu items.
		 *
		 * @since 1.0.0
		 *
		 * @param string $menu_slug The default menu slug.
		 *
		 * @return string $menu_slug The menu slug.
		 */
		return apply_filters( 'tec_conference_schedule_menu_slug', $this->menu_slug );
	}

	/**
	 * Modify the parent file to keep the conference menu open.
	 *
	 * @since 1.0.0
	 *
	 * @param string $parent_file The parent file string.
	 *
	 * @return string The parent file string.
	 */
	public function keep_taxonomy_menu_open( $parent_file ) {
		global $current_screen;
		if ( empty( $current_screen->taxonomy ) ) {
			return $parent_file;
		}

		if ( $current_screen->taxonomy !== $this->get_taxonomy_name() ) {
			return $parent_file;
		}

		return $this->get_menu_slug();
	}
}
