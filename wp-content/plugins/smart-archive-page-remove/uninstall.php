<?php 

/**
 * The smart Archive Page Remove Plugin Uninstall
 */
 
// If this file is called directly, abort
if ( ! defined( 'WPINC' ) ) {
  
  die;
  
}


// If this file is accessed withot plugin uninstall is requested, abort
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) || ! WP_UNINSTALL_PLUGIN ||	dirname( WP_UNINSTALL_PLUGIN ) != dirname( plugin_basename( __FILE__ ) ) ) {
  
  status_header( 404 );
  exit;
  
}

  
/**
 * Loader
 */
require_once( plugin_dir_path( __FILE__ ) . '/loader.php' );

/**
 * Run Uninstaller
 */
pp_smart_archive_page_remove()->uninstall();
  
?>