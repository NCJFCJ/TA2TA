<?php

/**
 * The Smart Archive Page Remove core plugin class
 */

 
// If this file is called directly, abort
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * The core plugin class
 */
if ( !class_exists( 'PP_Smart_Archive_Page_Remove' ) ) { 

  class PP_Smart_Archive_Page_Remove extends PPF08_Plugin {
    
    /**
     * Admin Class
     *
     * @see    class-smart-archive-page-remove-admin.php
     * @since  5.0.0
     * @var    object
     * @access private
     */
    private $admin;
    
    
    /**
     * Deprecated Class
     *
     * @see    class-smart-archive-page-remove-deprecated.php
     * @since  5.0.0
     * @var    object
     * @access private
     */
    private $deprecated;

    
    /**
     * Init the Class 
     *
     * @since 5.0.0
     * was part of __construct before
     */
    public function plugin_init() {
      
      // settings defaults
      $defaults = array(
        'author' => false,
        'category' => false,
        'tag' => false,
        'daily' => false,
        'monthly' => false,
        'yearly' => false
      );
      
      // since 5.0.0 we use add_settings_class() to load the settings
      $this->add_settings_class( 'PP_Smart_Archive_Page_Remove_Settings', 'class-smart-archive-page-remove-settings', $this, $defaults );
      
      $this->add_actions( array( 
        'init', 
        'wp' 
      ) );
      
    }
    
    
    /**
     * do plugin init 
     */
    function action_init() {
      
      load_plugin_textdomain( 'smart-archive-page-remove' );
      
      // change old stuff to new stuff for backward compatibility
      // since v 5.0.0
      $this->deprecated = $this->add_sub_class_always( 'PP_Smart_Archive_Page_Remove_Deprecated', 'class-smart-archive-page-remove-deprecated', $this );
      
      // since v 5.0.0
      $this->admin      = $this->add_sub_class_backend( 'PP_Smart_Archive_Page_Remove_Admin',     'class-smart-archive-page-remove-admin', $this, $this->settings() );
      
    }
    
    
    /**
     * send an 404 error if accessing an archive page that should be removed
     * was archive_remove before 5.0.0
     */
    function action_wp() {
      
      global $wp_query;
      
      if ( is_archive() ) {
        
        $archive = array(
          'author' => $wp_query->is_author(),
          'category' => $wp_query->is_category(),
          'tag' => $wp_query->is_tag(),
          'daily' => $wp_query->is_day(),
          'monthly' => $wp_query->is_month(),
          'yearly' => $wp_query->is_year()
        );
        
        foreach ( $archive as $key => $value ) {
          
          if ( $value && $this->settings()->get( $key ) ) {
            
            $wp_query->set_404();
            status_header(404);
            break;
            
          }
          
        }
        
      }
      
    }
    
    
    /**
     * uninstall plugin
     */
    public function uninstall() {
      
      if( is_multisite() ) {
        
        $this->uninstall_network();
        
      } else {
        
        $this->uninstall_single();
        
      }
      
    }
    
    
    /**
     * uninstall network wide
     */
    function uninstall_network() {
      global $wpdb;
      $activeblog = $wpdb->blogid;
      $blogids = $wpdb->get_col( esc_sql( 'SELECT blog_id FROM ' . $wpdb->blogs ) );
      foreach ($blogids as $blogid) {
        switch_to_blog( $blogid );
        $this->uninstall_single();
      }
      switch_to_blog( $activeblog );
    }
    
    
    /**
     * uninstall for a single blog
     */
    function uninstall_single() {
      
      $this->data_remove();
      $this->settings()->remove();
      
    }

  }
}
 
?>