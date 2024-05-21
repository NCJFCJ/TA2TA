<?php

/**
 * The Smart Archive Page Remove Plugin
 *
 * Smart Archive Page Remove allows you to completely remove Archive Pages from your Blog
 *
 * @wordpress-plugin
 * Plugin Name: Smart Archive Page Remove
 * Plugin URI: https://wordpress.org/plugins/smart-archive-page-remove/
 * Description: Completely remove unwanted Archive Pages from your Blog
 * Version: 5.1.1
 * Author: Peter Raschendorfer
 * Author URI: https://profiles.wordpress.org/petersplugins/
 * Text Domain: smart-archive-page-remove
 * License: GPL2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Loader
 */
require_once( plugin_dir_path( __FILE__ ) . '/loader.php' );

?>