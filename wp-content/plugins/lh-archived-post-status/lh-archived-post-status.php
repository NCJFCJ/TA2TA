<?php 
/**
 * Plugin Name: LH Archived Post Status
 * Plugin URI: https://lhero.org/portfolio/lh-archived-post-status/
 * Description: Creates an archived post status. Content can be excluded from the main loop and feed (but visible with a message), or hidden entirely
 * Version: 3.10
 * Author: Peter Shaw
 * Author URI: https://shawfactor.com/
 * Text Domain: lh_archive_post_status
 * License: GPL2+
 * Domain Path: /languages/
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!class_exists('WP_Statuses')) {
    
    include_once('includes/wp-statuses/wp-statuses.php');
    
}


if (!class_exists('LH_archived_post_status_plugin')) {

class LH_archived_post_status_plugin {

    private static $instance;

    static function return_plugin_namespace(){
    
        return 'lh_archive_post_status';
    
    }
    
    static function plugin_name(){
        
        return __('LH Archived Post Status', self::return_plugin_namespace());
        
    }
    
    
    static function return_opt_name(){
    
        return 'lh_archive_post_status_options';    
    
    }
    
    static function return_file_name(){

        return plugin_basename( __FILE__ );

    }



    static function return_publicly_available(){
    
        return 'public';    
    
    }

    static function return_message_field_name(){
    
        return 'lh_archive_post_status_message';   
    
    }

    static function return_new_status_name(){
    
        return 'archive'; 
    
    }

    static function return_title_label_field_name(){
    
        return 'lh_archive_post_status-title_label';
        
    }

    static function return_new_status_label(){
    
        return __('archived', self::return_plugin_namespace());     
    
    }

    static function return_new_status_count(){
    
        return __('Archived', self::return_plugin_namespace()).' <span class="count">(%s)</span>';
    
    }
    
    static function write_log($log) {
        
        if (true === WP_DEBUG) {
            
            if (is_array($log) || is_object($log)) {
                
                error_log(plugin_basename( __FILE__ ).' - '.print_r($log, true));
                
            } else {
                
                error_log(plugin_basename( __FILE__ ).' - '.$log);
                
            }
            
        }
        
    }
    
    static function isValidURL($url){ 
        
        if (empty($url)){
            
            return false;
        
        } else {
        
        return (bool)parse_url($url);
        
        }
    
    }

    
    static function curpageurl() {
        
    	$pageURL = 'http';
    
    	if ((isset($_SERVER["HTTPS"])) && ($_SERVER["HTTPS"] == "on")){
    	    
    		$pageURL .= "s";
    		
        }  
    
    	$pageURL .= "://";
    
    	if (($_SERVER["SERVER_PORT"] != "80") and ($_SERVER["SERVER_PORT"] != "443")){
    	    
    		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    
    	} else {
    	    
    		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    
    	}
    
    	return $pageURL;
    	
    }
    
    
    static function return_doing_their_own_thing_post_types(){
        
        $doing_their_own_thing_post_types =  array('advanced_ads', 'wc_membership_plan');
        
        return apply_filters( 'lh_archive_post_status_return_doing_their_own_thing_post_types', $doing_their_own_thing_post_types );
        
    }


    static function get_applicable_post_types() {
    
        $post_types = get_post_types( array('public'   => true ), 'names' );
        
        $excludable_post_types = array_unique(array_merge(array('attachment','forum','topic','reply','lh_rpt-post_type'), self::return_doing_their_own_thing_post_types()));

        $post_types = array_diff($post_types, $excludable_post_types);

        $post_types = apply_filters( 'lh_archive_post_status_posttypes_filter', $post_types );
        $post_types = apply_filters( 'lh_archive_post_status_get_applicable_post_types_filter', $post_types );

        return $post_types;
    
    
    }

    static function is_applicable_post_type($post_type) {
        
        if (empty($post_type)){
            
            return false;
            
        } else {

            return in_array( $post_type , self::get_applicable_post_types() );
            
        }
    
    }


    static function current_user_can_view() {
        
	    /**
	    * Default capability to grant ability to view Archived content (if the status is set to non public)
	    *
	    * @since 0.3.0
	    *
	    * @return string
	    */
	    $capability = 'read_private_posts';

	    return current_user_can( $capability );
	    
    }
    
    static function process_expired_posts(){

        $timestamp = date("Y-m-d H:i:s", strtotime('today midnight'));

        $types = get_post_types( array('public'   => true ), 'names' );

        $args = array (
            'post_type' => $types,
            'post_status' => array('publish'),
            'posts_per_page' => '5',
            'ignore_sticky_posts' => 1,
            'meta_query'    => array(
                'relation'  => 'AND',
                array(
                    'key'       => '_lh_archive_post_status-post_expires',
                    'value'     => $timestamp,
                    'compare'   => '<',
                )
            )
        );

        // The Query
        $query = new WP_Query( $args );

        $posts = $query->get_posts();

        foreach($posts as $post) {
    
            $my_post = array(
                'ID'           => $post->ID,
                'post_status' => self::return_new_status_name(),
            );

            wp_update_post( $my_post );

        }

    }
    
    
    
    static function make_status_consistent_with_expiration($post_object, $expiration) { 

        //check to see if is currently archived but expiration is in the future, if so publish it
        if((strtotime($expiration) > strtotime('today midnight')) and ($post_object->post_status == self::return_new_status_name())) {

            $my_post = array(
                'ID'           => $post_object->ID,
                'post_status'   => 'publish'
            );

            // Update the post into the database
            wp_update_post( $my_post );


        //check to see if is currently published but expiration is in the past, if so archive it
        } elseif ((strtotime($expiration) < strtotime('today midnight')) and ($post_object->post_status == 'publish')) {


            $my_post = array(
                'ID'           => $post_object->ID,
                'post_status'   => self::return_new_status_name(),
            );

            // Update the post into the database
            wp_update_post( $my_post );


        }

    }
    
    static function get_archive_post_link( $post = 0) {
        
        $post = get_post( $post );
        
        if ( empty($post->ID) ) {
		
		    return false;
	
        }
        
        if ( ! current_user_can( 'edit_post', $post->ID ) ) {
		
		    return false;
	
        }

        
        if ( !self::is_applicable_post_type($post->post_type)) {
            
            return false;
            
        }
        
        $link = esc_url( add_query_arg( 'redirect_to', self::curpageurl(), add_query_arg( self::return_plugin_namespace().'-post_edit-nonce', wp_create_nonce( self::return_plugin_namespace().'-post_edit-nonce' ), add_query_arg( 'post_id', $post->ID, add_query_arg( 'action', self::return_plugin_namespace().'-do_archive', admin_url( 'admin-ajax.php' ))) )));

        return apply_filters( self::return_plugin_namespace().'_get_archive_post_link', $link, $post);
        
    }
    

    static function setup_crons(){
    
        wp_clear_scheduled_hook( 'lh_archived_post_status_run' ); 
        wp_clear_scheduled_hook( 'lh_archived_post_status_initial' ); 
        wp_schedule_event( time() + wp_rand( 10, 3600 ), 'hourly', 'lh_archived_post_status_run' );
        wp_schedule_single_event(time() + wp_rand( 10, 60), 'lh_archived_post_status_initial');
    
    }

    static function remove_crons(){
    
        wp_clear_scheduled_hook( 'lh_archived_post_status_run' ); 
        wp_clear_scheduled_hook( 'lh_archived_post_status_initial' ); 
    
    }




    public function admin_edit_columns( $columns ) {
		
		$columns['lh_archive_post_status-post_expires'] = __( 'Archive Date', self::return_plugin_namespace());

		return $columns;
		
	}

	public function admin_edit_column_values( $column, $post_id ) {
		
		if ( 'lh_archive_post_status-post_expires' == $column ) {

            $date = get_post_meta( $post_id, '_lh_archive_post_status-post_expires', true );

            if (empty($date)){
                
			    _e( 'Never', self::return_plugin_namespace());

                
            } else {

                echo date(get_option( 'date_format' ), strtotime($date));

            }
		}
	}

    public function add_meta_boxes($post_type, $post)  {
    
    
        if (self::is_applicable_post_type($post_type)) {
        
            add_meta_box(self::return_plugin_namespace()."-archive_date-div", __( 'Archive Date', self::return_plugin_namespace()), array($this,"render_archive_date_box_content"), $post_type, "side", "high", array());

        }
    
    }





    public function render_archive_date_box_content( $post, $callback_args ){
    
        wp_nonce_field( self::return_plugin_namespace()."-post_edit-nonce", self::return_plugin_namespace()."-post_edit-nonce" )."\n";
        $raw_date = get_post_meta( $post->ID, '_'.self::return_plugin_namespace().'-post_expires', true );
        $archive_date = strtotime( $raw_date);
        
        
        echo '<table class="form-table">'."\n";
        echo '<tr valign="top">'."\n";
        echo '<th scope="row"><label for="'.self::return_plugin_namespace().'-post_expires">'.__("Archive Date", self::return_plugin_namespace() ).'</label></th>'."\n";
        echo '<td>'."\n";
        echo '<input type="date" name="'.self::return_plugin_namespace().'-post_expires" id="'.self::return_plugin_namespace().'-post_expires" value="';
        
        if (!empty($raw_date)) {
            
            echo date('Y-m-d', $archive_date);
            
        }
        
        echo '" />'."\n";
        echo '</td>'."\n";
        echo '</tr>'."\n";
        echo '</table>'."\n";
        
    }   


    public function update_post_details( $post_id, $post, $update ) {
    
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
        
            return;
            
        }
    
        if (!empty($_POST[self::return_plugin_namespace()."-post_edit-nonce"]) && wp_verify_nonce($_POST[self::return_plugin_namespace()."-post_edit-nonce"], self::return_plugin_namespace()."-post_edit-nonce")){ 
    
            if (!empty($_POST[self::return_plugin_namespace().'-post_expires'])){
        
                $expiry_time = strtotime($_POST[self::return_plugin_namespace().'-post_expires']);
                update_post_meta( $post_id, '_'.self::return_plugin_namespace().'-post_expires', date('Y-m-d H:i:s', $expiry_time));   
        
            } else {
        
                delete_post_meta( $post_id, '_'.self::return_plugin_namespace().'-post_expires');   
        
            }
    
        }
    
    }

    public function render_publicly_dropdown($args) { 
    
        $options = get_option( self::return_opt_name() );

        if (!empty($options[ $args[0] ])){
    
            $selected = $options[ $args[0] ];
    
        } else {
    
            $selected = false;
    
        }

        ?><select name="<?php echo self::return_opt_name().'['.$args[0].']'; ?>" id="<?php echo self::return_publicly_available(); ?>"><?php
        ?><option value="1" <?php  if ($selected == 1){ echo 'selected="selected"'; }  ?>><?php _e('Yes - But not in the, main loop, frontpage, or feed', self::return_plugin_namespace()); ?></option><?php
        ?><option value="0" <?php  if ($selected == 0){ echo 'selected="selected"'; }  ?>><?php _e('No - only logged in users can view archived posts', self::return_plugin_namespace()); ?></option><?php
        echo '</select>'."\n";
    
    }

    public function render_title_label_input($args) { 
    
        $options = get_option( self::return_opt_name() );

        if (!empty($options[ $args[0] ])){
    
            $value = $options[ $args[0] ];
    
        } else {
    
            $value = false;
    
        }
        
        echo '<input type="text" name="'.self::return_opt_name().'['.$args[0].']" id="'.$args[0].'" value="'.$value.'" size="20" /><br/>'.__('This label will appear after the title for archived posts on the front end of your website', self::return_plugin_namespace())."\n";

    }

    public function render_message_editor($args) {
    
        $options = get_option( self::return_opt_name() );

        if (!empty($options[ $args[0] ])){
    
            $value = $options[ $args[0] ];
    
        } else {
    
            $value = false;
    
        }

        $settings = array(
            'media_buttons' => false,
            'textarea_name' => self::return_opt_name().'['.$args[0].']',
        );
            
        wp_editor( $value, self::return_message_field_name(), $settings);

    }

    public function validate_options( $input ) { 

        $output = $input;

        // Return the array processing any additional functions filtered by this action
        return apply_filters( self::return_plugin_namespace().'_input_validation', $output, $input );

    }

    public function reading_setting_callback($arguments){
    
    
    }


    public function add_configuration_section(){
        
        add_settings_field( // Option 1
            self::return_publicly_available(), // Option ID
            __('Can Archived Posts be read publicly:', self::return_plugin_namespace()), // Label
            array($this, 'render_publicly_dropdown'), // !important - This is where the args go!
            'reading', // Page it will be displayed (General Settings)
            self::return_opt_name(), // Name of our section
            array( // The $args
	            self::return_publicly_available() // Should match Option ID
            ) 
        );
   
    
    
        add_settings_field( // Option 2
            self::return_title_label_field_name(), // Option ID
            __('Title Label:', self::return_plugin_namespace()), // Label
            array($this, 'render_title_label_input'), // !important - This is where the args go!
            'reading', // Page it will be displayed (General Settings)
            self::return_opt_name(), // Name of our section
            array( // The $args
                self::return_title_label_field_name() // Should match Option ID
            )  
        ); 
    
        add_settings_field( // Option 3
            self::return_message_field_name(), // Option ID
            __('Archive Message:', self::return_plugin_namespace()), // Label
            array($this, 'render_message_editor'), // !important - This is where the args go!
            'reading', // Page it will be displayed (General Settings)
            self::return_opt_name(), // Name of our section
            array( // The $args
                self::return_message_field_name() // Should match Option ID
            )  
        ); 
    
    
    
    
        add_settings_section(  
            self::return_opt_name(), // Section ID 
            __('Archiving Settings', self::return_plugin_namespace()), // Section Title
            array($this, 'reading_setting_callback'), // Callback
            'reading' // What Page?  This makes the section show up on the General Settings Page
        );

        register_setting('reading',self::return_opt_name(), array($this, 'validate_options'));
    
    }




    public function modify_title($title, $post_id = NULL){

        
        if (in_the_loop() && is_singular() && !empty($post_id) && is_numeric($post_id) && ($post_id > 0)){
            
            $options = get_option(self::return_opt_name());
            
            if (!empty(get_post_status( $post_id ))){
       
                $post_status = get_post_status( $post_id );
        
            } else {
        
                $post_status = get_post_status();    
        
            }


            if (!empty($post_status) and ($post_status == self::return_new_status_name())){

                if (!empty($options[self::return_title_label_field_name()])){

                    $title .= ' - '.$options[self::return_title_label_field_name()];
	
                }

            }

        }

        return $title;

    }

    public function add_message_to_content($content){

        if (!is_admin() && is_singular() && is_main_query()){
            
            $options = get_option(self::return_opt_name());
            
            remove_filter( 'the_content', array($this,'add_message_to_content'));

            if (!empty(get_post_status()) && (get_post_status() == self::return_new_status_name()) && !empty($options[self::return_message_field_name()])){
    
                $message = apply_filters( 'lh_archive_post_status_message_filter', $options[self::return_message_field_name()], $content );
                $updated_content = apply_filters('lh_archive_post_status_content_filter', $message.$content, $content);
                
            }
            
        }
        
        if (!empty($updated_content)){
            
            return $updated_content;
            
        } else {

            return $content;
            
        }
        
    }


    public function after_body_open(){
        
        add_filter( 'the_content', array($this,'add_message_to_content'));
        
    }





    public function add_posts_rows($actions,$post) {

        if (($post->post_status == "publish") && self::is_applicable_post_type($post->post_type)){

            if ( current_user_can('edit_post', $post->ID) ) {

                if ( current_user_can('publish_posts') ) {

                    $actions['archive_link']  = '<a href="'.self::get_archive_post_link($post->ID).'" title="' . esc_attr( __( 'Archive this post' , self::return_plugin_namespace()) ) . '">' . __( 'Archive', self::return_plugin_namespace() ) . '</a>';

                }

                return $actions;

            }

        } elseif ($post->post_status == self::return_new_status_name()){

            unset($actions['edit']);
            unset($actions['trash']);


        }

        return $actions;

    }
    
    public function ajax_do_archive(){
        
        if (!empty($_GET['action']) && ($_GET['action'] == self::return_plugin_namespace().'-do_archive') && !empty($_GET['post_id']) && is_numeric($_GET['post_id']) && !empty($_GET[self::return_plugin_namespace().'-post_edit-nonce'])){
            
            $post_id = intval($_GET['post_id']);
            
            if (!get_post_status($post_id)){
                
                echo __('invalid post id', self::return_plugin_namespace());
                exit;
                
            }
            
            
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
		
		        echo __('Current user does not have capability', self::return_plugin_namespace());
                exit;
                
	
            }
            
            if (!wp_verify_nonce($_GET[self::return_plugin_namespace().'-post_edit-nonce'], self::return_plugin_namespace()."-post_edit-nonce")){
                
                echo __('Incorrect Nonce', self::return_plugin_namespace());
                exit;
            }
            
            $my_post = array(
                'ID'           => $post_id,
                'post_status' => self::return_new_status_name(),
            );

            wp_update_post( $my_post );
            
            if (!empty($_GET['redirect_to']) && self::isValidURL(trim($_GET['redirect_to']))){
                
                wp_redirect( trim($_GET['redirect_to']),302, self::plugin_name()); exit;
                
            }
            
        }
        
    }



    public function exclude_archive_post_status_from_main_query( $query ) {
	
	    if ( $query->is_main_query() && !is_admin() && !is_singular() && empty($_GET['s'])) {

            if ( current_user_can('read_private_posts') ) {

                $post_status = array( 'publish', 'private' );

            } else {

                $post_status = array( 'publish');

            }
            
		    $query->set( 'post_status', $post_status );
		
	    }
	    
    }


    public function exclude_archive_post_status_from_feed( $query ) {
	    
	    if ($query->is_feed && !is_feed('lh-sitemaps-general')){

            if ( current_user_can('read_private_posts') ) {

                $post_status = array( 'publish', 'private' );

            } else {

                $post_status = array( 'publish');

            }
            
		    $query->set( 'post_status', $post_status );
		
	    }
	    
    }



    public function display_archive_state( $states , $post) {
        
        $arg = get_query_var( 'post_status' );
        
        if($arg != self::return_new_status_name()){
         
            if(!empty($post->post_status) && ($post->post_status == self::return_new_status_name())){
                
               return array(ucwords(self::return_new_status_label()));
               
            }
            
        }
        
        return $states;
        
    }





    public function run_processes(){

        self::process_expired_posts();

    }


    public function initial_processes(){

        if (!get_option(self::return_opt_name())){

            $options[ self::return_publicly_available() ] = true;
            $options[ self::return_message_field_name() ] = '<p>'.__('This content has been archived. It may no longer be relevant', self::return_plugin_namespace()).'</p>';

            update_option( self::return_opt_name(), $options );

        }

        wp_clear_scheduled_hook( 'lh_archived_post_status_initial' );

    }






    public function maybe_add_or_remove_expiry( $new_status, $old_status, $post ){

        if (($new_status == 'publish') && ($old_status == 'archive')){

            delete_post_meta( $post->ID, '_lh_archive_post_status-post_expires');

        } elseif ( 'archive' == $new_status ){

            $expiry = get_post_meta($post->ID , '_lh_archive_post_status-post_expires', true );

            if (empty($expiry)){

                $expiry = current_time('mysql');

                update_post_meta( $post->ID, '_lh_archive_post_status-post_expires', $expiry );


            }


        } else {

            return;

        }

    }

    
    public function maybe_add_status_to_sitemap( $args, $post_type ) {
    
        $options = get_option(self::return_opt_name());
    
        if (!self::is_applicable_post_type($post_type)){
    
            return $args;
            
        }

        if (!empty($options[ self::return_publicly_available() ]) or self::current_user_can_view()){
    
            if (is_string($args['post_status'])){
        
                $args['post_status'] = array($args['post_status']);
        
            }
    
            $args['post_status'][] = self::return_new_status_name();

        }
    
        return $args;   
    
    }
    
    public function lh_super_cache_helper_statuses($statuses){
    
        $statuses[] = 'archive';   
    
        return $statuses;   
    
    }



    public function remove_meta_archive_date($defaults, $post_object){
    
        $defaults['_lh_archive_post_status-post_expires'] = '';   

        return $defaults;
    
    } 

    public function maybe_make_waybackable($statuses){
    
        $options = get_option(self::return_opt_name());   

        if (!empty($options[ self::return_publicly_available() ])){

            $statuses[] = self::return_new_status_name();

        }
    
        return $statuses;    
    
    }




    public function maybe_make_checkable($statuses){
    
        $options = get_option(self::return_opt_name());   

        if (!empty($options[ self::return_publicly_available() ])){

            $statuses[] = self::return_new_status_name();

        }
    
        return $statuses;    
    
    }
    
    public function create_custom_post_status(){
    
        $options = get_option(self::return_opt_name());

        if (!empty($options[ self::return_publicly_available() ])){

            $public = true;

        } else {

            $public = self::current_user_can_view();

        }
        
        $args = array(
    		    'label'                     => _x( ucwords(self::return_new_status_label()), 'post status label', self::return_plugin_namespace() ),
    		    'public'                    => $public,
    		    'label_count'               => _n_noop( self::return_new_status_count(), self::return_new_status_count(), self::return_plugin_namespace() ),
    		    'post_type'                 => self::get_applicable_post_types(), // Define one or more post types the status can be applied to.
    		    'show_in_admin_all_list'    => true,
    		    'show_in_admin_status_list' => true,
    		    'show_in_metabox_dropdown'  => true,
    		    'show_in_inline_dropdown'   => true,
    		    'publicly_queryable'        => true,
    		    'dashicon'                  => 'dashicons-archive',
    		    'labels'                      => array(
        			'metabox_dropdown'    => __( 'Archived', self::return_plugin_namespace() ),
        			'metabox_submit'      => __( 'Archive', self::return_plugin_namespace()),
        			'metabox_save_on'     => __( 'Archive on:', self::return_plugin_namespace() ),
        			/* translators: Post date information. 1: Date on which the post is to be archived */
        			'metabox_save_date'   => __( 'Archive on: <b>%1$s</b>', self::return_plugin_namespace() ),
        			'metabox_saved_on'    => __( 'Archived on:', self::return_plugin_namespace() ),
        			/* translators: Post date information. 1: Date on which the post was archived */
        			'metabox_saved_date'  => __( 'Archived on: <b>%1$s</b>', self::return_plugin_namespace()),
        			'metabox_save_now'    => __( 'Archive <b>now</b>', self::return_plugin_namespace() ),
        			'inline_dropdown'     => __( 'Archived', self::return_plugin_namespace() ),
        			'press_this_dropdown' => __( 'Add to archives', self::return_plugin_namespace() ),
    		    ),
	        );

        register_post_status( self::return_new_status_name(), $args );
     
     
        foreach (self::get_applicable_post_types() as $posttype) {
     
            add_filter( 'manage_'.$posttype.'_posts_columns', array( $this, 'admin_edit_columns' ),5,1 ); 
            add_action( 'manage_'.$posttype.'_posts_custom_column', array( $this, 'admin_edit_column_values' ), 100000, 2 );
    
        }
     
    }
    
    public function exclude_certain_post_types($post_types){
        
        $post_types = array_diff($post_types, self::return_doing_their_own_thing_post_types());
        
        
        return $post_types;
        
    }
    
    public function define_global_functions(){
    
        if (!function_exists("archive_post_link")){ 
           
            function archive_post_link( $text = null, $before = '', $after = '', $post = 0, $class = 'post-archive-link'){
                
                $post = get_post( $post );

            	if ( ! $post ) {
            		return;
            	}

	            $url = LH_archived_post_status_plugin::get_archive_post_link( $post->ID );

            	if ( ! $url ) {
            	    
            		return;
            		
            	}

            	if ( null === $text ) {
            		$text = __( 'Archive This' );
            	}

	            $link = '<a class="' . esc_attr( $class ) . '" href="' . esc_url( $url ) . '">' . $text . '</a>';

            	/**
            	 * Filters the post edit link anchor tag.
            	 *
            	 * @since 2.3.0
            	 *
            	 * @param string $link    Anchor tag for the edit link.
            	 * @param int    $post_id Post ID.
            	 * @param string $text    Anchor text.
            	 */
            	 
            	echo $before . apply_filters( 'archive_post_link', $link, $post->ID, $text ) . $after;
                
            }
        }
    
    }
    
    public function prune_on_transition( $new_status, $old_status, $post ) {
        
        if (!function_exists('prune_super_cache')){
            
            return;
            
        }
    
        if ( $old_status == $new_status ){
            
		    return;
		    
        }
        
        if ($post->post_type != 'post'){
            
            return;
            
        }
        
        if ($old_status != 'publish'){
            
            return;
            
        }
        
        
        if ($new_status == 'archive'){
            
            $dir = str_replace( get_option( 'home' ), '', $url );
            $path = get_supercache_dir().$dir;
            return prune_super_cache( $path, true );
            
        }
        

    }

    public function plugin_init(){

        //load the translations, both plugin specific and the wp-statuses library
        load_plugin_textdomain( self::return_plugin_namespace(), false, basename( dirname( __FILE__ ) ) . '/languages' );
        load_plugin_textdomain( 'wp-statuses', false, basename( dirname( __FILE__ ) ) . '/includes/wp-statuses/languages' );
        
        //Handle access and display of the archived post status
        add_action( 'pre_get_posts', array($this,'exclude_archive_post_status_from_main_query'));
        add_action( 'pre_get_posts', array($this,'exclude_archive_post_status_from_feed'));

        //add a section to the reading settings
        add_action('admin_init', array($this,'add_configuration_section')); 

        //add an expiry to newly archived post objects that don't have one already, remove it if it has been republished
        add_action( 'transition_post_status', array( $this, 'maybe_add_or_remove_expiry' ),10,3);

        //add the expiry metabox
        add_action('add_meta_boxes', array($this,'add_meta_boxes'),10,2);

        //handle posted values from the metabox
        add_action( 'save_post', array($this,'update_post_details'),10,3);
        
        //add messages and labels to titles and post content
        add_filter('the_title', array( $this, 'modify_title' ),10,2);
        add_action( 'wp_body_open', array($this,'after_body_open'));
        
        //add a column for the archive date
        add_filter('page_row_actions',array($this,'add_posts_rows'),10,2);
        add_filter('post_row_actions',array($this,'add_posts_rows'),10,2);
        
        //create a admin ajax endpoint for archiving posts
		add_action( 'wp_ajax_'.self::return_plugin_namespace().'-do_archive', array( $this, 'ajax_do_archive' ) );

        //Add a label to the listing table
        add_filter( 'display_post_states', array($this,'display_archive_state'),10,2);
        
        //Maybe add the post_status to the sitemap
        add_filter('wp_sitemaps_posts_query_args', array($this,'maybe_add_status_to_sitemap'),10,2);
        
        //add tasks to the cron job
        add_action( 'lh_archived_post_status_run', array($this,'run_processes'));
        add_action( 'lh_archived_post_status_initial', array($this,'initial_processes'));
        
        
        //exclude certain post types, some of them do their own thing with metaboxes
        add_filter( 'wp_statuses_get_supported_post_types', array($this,'exclude_certain_post_types'),10,1);
        
        /**
        * The following hooks are just to ensure the plugin plays nice with some other plugins in the LocalHero project.
        *
        * They can safely be ignored for the vast majority
        */
        
        //add archive post status to the wp super cache helper
        add_filter('lh_super_cache_helper_statuses', array($this, 'lh_super_cache_helper_statuses'), 10, 1 );
        
        //filter out the archive to draft if copying post
        add_filter('lh_copy_to_draft_meta_defaults_filter', array($this,'remove_meta_archive_date'), 10, 2);

        //maybe make archived posts, pages, cpts etc, waybackable
        add_filter('lh_wayback_machine_get_applicable_post_statuses', array($this,'maybe_make_waybackable'), 10, 1);

        //maybe check for broken links for archived posts, pages, cpts etc
        add_filter('lh_blc_get_applicable_post_statuses', array($this,'maybe_make_checkable'), 10, 1);
        
        //define some global functions
        $this->define_global_functions();
        
        /**
        * The following hook is just to ensure the plugin plays nice with WP Super Cache.
        *
        * It can safely be ignored for the vast majority
        */
        
        //prune the cahce when a post is transitionsed from published to archived
        add_action( 'transition_post_status', array($this,'prune_on_transition'), 10, 3);
        
    }

	/**
    * Gets an instance of our plugin.
    *
    * using the singleton pattern
    */
    
    public static function get_instance(){
        if (null === self::$instance) {
            self::$instance = new self();
        }
 
        return self::$instance;
    }
    
    static function on_activate($network_wide) {

        if ( is_multisite() && $network_wide ) { 
        
            $args = array('number' => 500, 'fields' => 'ids');
        
            $sites = get_sites($args);
    
            foreach ($sites as $blog_id) {
            
                switch_to_blog($blog_id);
                self::setup_crons();
                restore_current_blog();
            
            } 
        
        } else {


            self::setup_crons();

        }

    }


    static function on_deactivate($network_wide) {
    
        if ( is_multisite() && $network_wide ) { 

            $args = array('number' => 500, 'fields' => 'ids');
            $sites = get_sites($args);
    
            foreach ($sites as $blog_id) {
            
                switch_to_blog($blog_id);
                self::remove_crons();
                restore_current_blog();
            
            } 

        } else {

            self::remove_crons();

        }
    
    }
    




    public function __construct() {
        
        //create the archived custom post status
        add_action( 'init', array($this,'create_custom_post_status'), 1000 );

        //try to run everything on plugins loaded
        add_action( 'plugins_loaded', array($this,'plugin_init'), 1000, 1);

        

    }


}


$lh_archived_post_status_instance = LH_archived_post_status_plugin::get_instance();

register_activation_hook(__FILE__, array('LH_archived_post_status_plugin','on_activate'), 10, 1);
register_deactivation_hook( __FILE__, array('LH_archived_post_status_plugin','on_deactivate') );



}

?>