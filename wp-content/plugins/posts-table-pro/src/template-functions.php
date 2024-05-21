<?php

/**
 * Template functions for Posts Table Pro.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
use Barn2\Plugin\Posts_Table_Pro\Table_Factory;

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ptp_get_posts_table' ) ) {

	/**
	 * Retrieves a post table for the specified args. The arg names are the same as those used in the shortcode and Table_Args.
	 *
	 * @see The shortcode documentation or See Table_Args::$default_args for the list of supported args.
	 * @param array $args The table args.
	 * @return string The data table as a HTML string.
	 */
	function ptp_get_posts_table( $args = [] ) {
		// Create and return the table as HTML
		$table = Table_Factory::create( $args );
		return $table->get_table( 'html' );
	}

}

if ( ! function_exists( 'ptp_the_posts_table' ) ) {

	/**
	 * Outputs a post table for the specified args. The arg names are the same as those used in the shortcode and Table_Args.
	 *
	 * @see The shortcode documentation or See Table_Args::$default_args for the list of supported args.
	 * @param array $args The table args.
	 */
	function ptp_the_posts_table( $args = [] ) {
		echo ptp_get_posts_table( $args );
	}

}
