<?php
/**
 * Add theme compatibility things here.
 *
 * @since   4.10.0
 *
 */
use Tribe\Utils\Body_Classes;
use Tribe\Utils\Theme_Compatibility as Common_Theme_Compatibility;

class Tribe__Events__Community__Theme_Compatibility extends Common_Theme_Compatibility {
  /**
   * Add body classes.
   *
   * @since 4.10.0
   *
   * @return array $classes List of body classes.
   */
  public function add_body_classes( $classes ) {
	return array_merge( $classes, static::get_compatibility_classes() );
  }
}