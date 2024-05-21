<?php

/**
 * The Smart Archive Page Remove admin plugin class
 *
 * @since  5.0.0
 */
 
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The admin plugin class
 */
if ( !class_exists( 'PP_Smart_Archive_Page_Remove_Admin' ) ) {
  
  class PP_Smart_Archive_Page_Remove_Admin extends PPF08_Admin {

    
    /**
	   * Do Init
     *
     * @since 5.0.0
     * @access public
     */
    public function init() {
      
      $this->add_actions( array( 
        'admin_init',
        'admin_menu'
      ) );
    
    }
    
    
    /**
     * init admin 
     * moved to PP_Smart_Archive_Page_Remove_Admin in v 5.0.0
     */
    function action_admin_init() {
      
      $this->add_setting_sections(
      
        array(
          
          array(
        
            'section' => 'general',
            'order'   => 10,
            'title'   => esc_html__( 'Archive Pages to remove', 'smart-archive-page-remove' ),
            'icon'    => 'general',
            'fields' => array(
              array(
                'key'      =>'author',
                'callback' => 'admin_author'
              ),
              array(
                'key'      =>'category',
                'callback' => 'admin_category'
              ),
              array(
                'key'      =>'tag',
                'callback' => 'admin_tag'
              ),
              array(
                'key'      =>'daily',
                'callback' => 'admin_daily'
              ),
              array(
                'key'      =>'monthly',
                'callback' => 'admin_monthly'
              ),
              array(
                'key'      =>'yearly',
                'callback' => 'admin_yearly'
              ),
			  array(
				'key'      =>'notice',
                'callback' => 'retired_notice'
			  )
            
            )
        
          )
          
        )
        
      );
      
    }
	
	function retired_notice() {
			echo '<h2>PLEASE NOTE</h2><p>Development, maintenance and support of this plugin has been retired. You can use this plugin as long as is works for you. Thanks for your understanding.<br />Regards, Peter</p>';
	}
    
    /**
     * handle the settings field author
     * moved to PP_Smart_Archive_Page_Remove_Admin in v 5.0.0
     */
    function admin_author() {
        
      $this->print_slider_check( 
        'author', 
        esc_html__( 'Author Archive Page', 'smart-archive-page-remove' ),
        false,
        false,
        '<span style="white-space: nowrap">' . __( 'e.g.', 'smart-archive-page-remove' ) . ' <code>' . get_author_posts_url( get_current_user_id() ) . '</code></span>'
      );
      
    }
    
    
    /**
     * handle the settings field category
     * moved to PP_Smart_Archive_Page_Remove_Admin in v 5.0.0
     */
    function admin_category() {
      
      $terms = get_terms( 'category', array( 'orderby' => 'count', 'order' => 'desc', 'hide_empty' => 0, 'childless' => true, 'parent' => 0, 'number' => 1 ) );
      if ( count( $terms ) > 0 ) {
        $termsample = get_term_link( $terms[0] );
      } else {
        $termsample = trailingslashit( get_site_url( get_option( 'category_base' ) ) ) . __( 'my-category', 'smart-archive-page-remove' );
      }
        
      $this->print_slider_check( 
        'category', 
        esc_html__( 'Category Archive Page', 'smart-archive-page-remove' ),
        false,
        false,
        '<span style="white-space: nowrap">' . __( 'e.g.', 'smart-archive-page-remove' ) . ' <code>' . $termsample . '</code></span>'
      );
      
    }
    
    
    /**
     * handle the settings field tag
     * moved to PP_Smart_Archive_Page_Remove_Admin in v 5.0.0
     */
    function admin_tag() {
      
      $tags = get_tags( array ( 'orderby' => 'count', 'order' => 'desc', 'hide_empty' => 0, 'number' => 1) );
      if ( count( $tags ) > 0 ) {
        $tagsample = get_tag_link( $tags[0]->term_id );
      } else {
        $tagsample = trailingslashit( get_site_url( get_option( 'tag_base' ) ) ) . __( 'my-tag', 'smart-archive-page-remove' );
      }
      
      $this->print_slider_check( 
        'tag', 
        esc_html__( 'Tag Archive Page', 'smart-archive-page-remove' ),
        false,
        false,
        '<span style="white-space: nowrap">' . __( 'e.g.', 'smart-archive-page-remove' ) . ' <code>' . $tagsample . '</code></span>'
      );
      
    }
    
    
    /**
     * handle the settings field daily
     * moved to PP_Smart_Archive_Page_Remove_Admin in v 5.0.0
     */
    function admin_daily() {
        
      $this->print_slider_check( 
        'daily', 
        esc_html__( 'Daily Archive Page', 'smart-archive-page-remove' ),
        false,
        false,
        '<span style="white-space: nowrap">' . __( 'e.g.', 'smart-archive-page-remove' ) . ' <code>' . get_day_link( false, false, false ) . '</code></span>'
      );
      
    }
    
    
    /**
     * handle the settings field monthly
     * moved to PP_Smart_Archive_Page_Remove_Admin in v 5.0.0
     */
    function admin_monthly() {
        
      $this->print_slider_check( 
        'monthly', 
        esc_html__( 'Monthly Archive Page', 'smart-archive-page-remove' ),
        false,
        false,
        '<span style="white-space: nowrap">' . __( 'e.g.', 'smart-archive-page-remove' ) . ' <code>' . get_month_link( false, false ) . '</code></span>'
      );
      
    }
    
    
    /**
     * handle the settings field yearly
     * moved to PP_Smart_Archive_Page_Remove_Admin in v 5.0.0
     */
    function admin_yearly() {
        
      $this->print_slider_check( 
        'yearly', 
        esc_html__( 'Yearly Archive Page', 'smart-archive-page-remove' ),
        false,
        false,
        '<span style="white-space: nowrap">' . __( 'e.g.', 'smart-archive-page-remove' ) . ' <code>' . get_year_link( false ) . '</code></span>'
      );
      
    }
    
    
    /**
     * create the menu entry
     * moved to PP_Smart_Archive_Page_Remove_Admin in v 5.0.0
     */
    function action_admin_menu() {
      $screen_id = add_options_page( esc_html__( 'Archive Pages', "smart-archive-page-remove" ), esc_html__( 'Archive Pages', 'smart-archive-page-remove' ), 'manage_options', 'smartarchivepageremovesettings', array( $this, 'show_admin' ) );
      $this->set_screen_id( $screen_id );
    }
    
   
    /**
     * show admin page
     * moved to PP_Smart_Archive_Page_Remove_Admin in v 5.0.0
     */
    function show_admin() {
      
      $this->show( 'manage_options' );
      
    }
    
    /**
     * create nonce
     *
     * @since  5.0.0
     * @access private
     * @return string Nonce
     */
    private function get_nonce() {
      
      return wp_create_nonce( 'pp_smart_archive_page_remove_dismiss_admin_notice' );
      
    }
    
    
    /**
     * check nonce
     *
     * @since  5.0.0
     * @access private
     * @return boolean
     */
    private function check_nonce() {
      
      return check_ajax_referer( 'pp_smart_archive_page_remove_dismiss_admin_notice', 'securekey', false );
      
    }

  }
  
}

?>