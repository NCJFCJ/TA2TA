<?php

/**
 * The Smart Archive Page Remove deprecated class
 *
 * to ensure backward compatibility
 *
 * @since  5.0.0
 */
 
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The deprecated plugin class
 */
if ( !class_exists( 'PP_Smart_Archive_Page_Remove_Deprecated' ) ) {
  
  class PP_Smart_Archive_Page_Remove_Deprecated extends PPF08_SubClass {  
    
    /**
	   * Do Init
     *
     * @since 5.0.0
     * @access public
     */
    public function init() {

     
      // since 5.0.0 the settings are stored in smart_archive_page_remove_settings (unified by Plugin Foundation)
      // was smart_archive_page_remove before
      
      $oldkey = str_replace( '-', '_', $this->core()->get_plugin_slug() );
      $newkey = $oldkey . '_settings';
      
      if ( false === get_option( $newkey ) && false !== get_option( $oldkey ) ) {
        
        // for some reason get_option() does not give us the unserialized array - no idea why
        $oldvals = unserialize( get_option( $oldkey ) );
        
        $newvals = array();
        
        // we need strings - boolean values are not working, because the toggle buttons result in string values
        foreach ( $oldvals as $key => $value ) {
          
          if ( $value ) {
            
            $newvals[$key] = '1';
            
          } else {
            
            $newvals[$key] = '0';
          }
          
        }
        
        update_option( $newkey,  $newvals );
        delete_option( $oldkey );
        
        // delete user meta for old admin notices for all users
        foreach ( array( 'pp-smart-archive-page-remove-admin-notice-1', 'pp-smart-archive-page-remove-admin-notice-1' ) as $key ) {
          delete_metadata( 'user', false, $key, false, true );
        }
          
      }
    
    }

  }
  
}

?>