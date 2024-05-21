<?php

/**
 * The smart Archive Page Remove Plugin Loader
 *
 * @since 3
 *
 **/
 
// If this file is called directly, abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Load Plugin Foundation
 * @since 5.0.0
 */
require_once( plugin_dir_path( __FILE__ ) . '/inc/ppf/loader.php' );
 
 
/**
 * Load Plugin Main File
 */
require_once( plugin_dir_path( __FILE__ ) . '/inc/class-smart-archive-page-remove.php' );


/**
 * Main Function
 */
function pp_smart_archive_page_remove() {

  return PP_Smart_Archive_Page_Remove::getInstance( array(
    'file'      => dirname( __FILE__ ) . '/smart-archive-page-remove.php',
    'slug'      => pathinfo( dirname( __FILE__ ) . '/smart-archive-page-remove.php', PATHINFO_FILENAME ),
    'name'      => 'Smart Archive Page Remove',
    'shortname' => 'Smart Archive Page Remove',
    'version'   => '5.1.1'
  ) );
  
}

/**
 * Run the plugin
 */
pp_smart_archive_page_remove();

?>