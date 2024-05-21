<?php
/**
 * Astra functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
// Start the clock
$start_time = microtime(true); //TEST
/**
 * Define Constants
 */
define( 'ASTRA_THEME_VERSION', '4.6.9' );
define( 'ASTRA_THEME_SETTINGS', 'astra-settings' );
define( 'ASTRA_THEME_DIR', trailingslashit( get_template_directory() ) );
define( 'ASTRA_THEME_URI', trailingslashit( esc_url( get_template_directory_uri() ) ) );

/**
 * Minimum Version requirement of the Astra Pro addon.
 * This constant will be used to display the notice asking user to update the Astra addon to the version defined below.
 */
define( 'ASTRA_EXT_MIN_VER', '4.6.4' );

/**
 * Setup helper functions of Astra.
 */
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-theme-options.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-theme-strings.php';
require_once ASTRA_THEME_DIR . 'inc/core/common-functions.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-icons.php';

define( 'ASTRA_PRO_UPGRADE_URL', astra_get_pro_url( 'https://wpastra.com/pro/', 'dashboard', 'free-theme', 'upgrade-now' ) );
define( 'ASTRA_PRO_CUSTOMIZER_UPGRADE_URL', astra_get_pro_url( 'https://wpastra.com/pro/', 'customizer', 'free-theme', 'upgrade' ) );

/**
 * Update theme
 */
require_once ASTRA_THEME_DIR . 'inc/theme-update/astra-update-functions.php';
require_once ASTRA_THEME_DIR . 'inc/theme-update/class-astra-theme-background-updater.php';

/**
 * Fonts Files
 */
require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-font-families.php';
if ( is_admin() ) {
	require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-fonts-data.php';
}

require_once ASTRA_THEME_DIR . 'inc/lib/webfont/class-astra-webfont-loader.php';
require_once ASTRA_THEME_DIR . 'inc/lib/docs/class-astra-docs-loader.php';
require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-fonts.php';

require_once ASTRA_THEME_DIR . 'inc/dynamic-css/custom-menu-old-header.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/container-layouts.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/astra-icons.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-walker-page.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-enqueue-scripts.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-gutenberg-editor-css.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-wp-editor-css.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/block-editor-compatibility.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/inline-on-mobile.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/content-background.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-dynamic-css.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-global-palette.php';

/**
 * Custom template tags for this theme.
 */
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-attr.php';
require_once ASTRA_THEME_DIR . 'inc/template-tags.php';

require_once ASTRA_THEME_DIR . 'inc/widgets.php';
require_once ASTRA_THEME_DIR . 'inc/core/theme-hooks.php';
require_once ASTRA_THEME_DIR . 'inc/admin-functions.php';
require_once ASTRA_THEME_DIR . 'inc/core/sidebar-manager.php';

/**
 * Markup Functions
 */
require_once ASTRA_THEME_DIR . 'inc/markup-extras.php';
require_once ASTRA_THEME_DIR . 'inc/extras.php';
require_once ASTRA_THEME_DIR . 'inc/blog/blog-config.php';
require_once ASTRA_THEME_DIR . 'inc/blog/blog.php';
require_once ASTRA_THEME_DIR . 'inc/blog/single-blog.php';

/**
 * Markup Files
 */
require_once ASTRA_THEME_DIR . 'inc/template-parts.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-loop.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-mobile-header.php';

/**
 * Functions and definitions.
 */
require_once ASTRA_THEME_DIR . 'inc/class-astra-after-setup-theme.php';

// Required files.
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-admin-helper.php';

require_once ASTRA_THEME_DIR . 'inc/schema/class-astra-schema.php';

/* Setup API */
require_once ASTRA_THEME_DIR . 'admin/includes/class-astra-api-init.php';

if ( is_admin() ) {
	/**
	 * Admin Menu Settings
	 */
	require_once ASTRA_THEME_DIR . 'inc/core/class-astra-admin-settings.php';
	require_once ASTRA_THEME_DIR . 'admin/class-astra-admin-loader.php';
	require_once ASTRA_THEME_DIR . 'inc/lib/astra-notices/class-astra-notices.php';
}

/**
 * Metabox additions.
 */
require_once ASTRA_THEME_DIR . 'inc/metabox/class-astra-meta-boxes.php';

require_once ASTRA_THEME_DIR . 'inc/metabox/class-astra-meta-box-operations.php';

/**
 * Customizer additions.
 */
require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-customizer.php';

/**
 * Astra Modules.
 */
require_once ASTRA_THEME_DIR . 'inc/modules/posts-structures/class-astra-post-structures.php';
require_once ASTRA_THEME_DIR . 'inc/modules/related-posts/class-astra-related-posts.php';

/**
 * Compatibility
 */
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-gutenberg.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-jetpack.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/woocommerce/class-astra-woocommerce.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/edd/class-astra-edd.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/lifterlms/class-astra-lifterlms.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/learndash/class-astra-learndash.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-beaver-builder.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-bb-ultimate-addon.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-contact-form-7.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-visual-composer.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-site-origin.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-gravity-forms.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-bne-flyout.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-ubermeu.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-divi-builder.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-amp.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-yoast-seo.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-surecart.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-starter-content.php';
require_once ASTRA_THEME_DIR . 'inc/addons/transparent-header/class-astra-ext-transparent-header.php';
require_once ASTRA_THEME_DIR . 'inc/addons/breadcrumbs/class-astra-breadcrumbs.php';
require_once ASTRA_THEME_DIR . 'inc/addons/scroll-to-top/class-astra-scroll-to-top.php';
require_once ASTRA_THEME_DIR . 'inc/addons/heading-colors/class-astra-heading-colors.php';
require_once ASTRA_THEME_DIR . 'inc/builder/class-astra-builder-loader.php';

// Elementor Compatibility requires PHP 5.4 for namespaces.
if ( version_compare( PHP_VERSION, '5.4', '>=' ) ) {
	require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-elementor.php';
	require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-elementor-pro.php';
	require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-web-stories.php';
}

// Beaver Themer compatibility requires PHP 5.3 for anonymous functions.
if ( version_compare( PHP_VERSION, '5.3', '>=' ) ) {
	require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-beaver-themer.php';
}

require_once ASTRA_THEME_DIR . 'inc/core/markup/class-astra-markup.php';

/**
 * Load deprecated functions
 */
require_once ASTRA_THEME_DIR . 'inc/core/deprecated/deprecated-filters.php';
require_once ASTRA_THEME_DIR . 'inc/core/deprecated/deprecated-hooks.php';
require_once ASTRA_THEME_DIR . 'inc/core/deprecated/deprecated-functions.php';


/**
 * Support post_thumbnail
 */

 add_action('after_setup_theme', 'twenty_twenty_four_theme_setup');

 function twenty_twenty_four_theme_setup(){
	 add_theme_support( 'post-thumbnails', array( 'newsletter', 'dpl_document', 'document' ) );
 }
 
 
 ########################################################################
 
 //NEED SOME CORRECTION require ABSPATH
 
 $roots_includes = array(  
	 '/functions/calendar-functions.php', // functions related to the calendar
	 '/functions/library-functions.php', // functions related to the library
	 //'/functions/user-customfield-function.php', // functions add custom field organization to choice forms
 );
 
 foreach($roots_includes as $file){
   if(!$filepath = locate_template($file)) {
	 trigger_error("Error locating `$file` for inclusion!", E_USER_ERROR);
   }
   require_once $filepath;
 }
 unset($file, $filepath);
 
 ///////////////////////////////////////////////////////////////////////////////////
 // Enqueue a custom stylesheet overide all style sheet
 ///////////////////////////////////////////////////////////////////////////////////
 
 if ( ! function_exists( 'ta2ta_theme_load_additional_scripts') ){
	 /**
	  * 	Load custom css scripts 
	  */
	 
	  function ta2ta_theme_load_additional_scripts(){
		 wp_register_style('custom-styles', get_template_directory_uri() . '/assets/custom.css', array(), false, 'all');
		 wp_enqueue_style('custom-styles');
	  }
	 
	 add_action('wp_enqueue_scripts', 'ta2ta_theme_load_additional_scripts');
 }
 
 
 /**
  * Helper Functions
  */
 
 
 /**
  * @param $slug [string] the name of the post to find, $post_type [string] the post type of the post to find
  * @return Object the post 
  */
 
 function ta2ta_get_post_id_by_slug( $slug, $post_type = null ) {
	 $query = new WP_Query(
		 array(
			 'name'   => $slug,
			 'post_type'   => $post_type,
			 'numberposts' => 1,
			 //'fields'      => 'ids',
		 ) );
	 $post_a = $query->get_posts();
	 wp_reset_query();
	 return array_shift( $post_a );
 }
 /**
  * Add column id post tables for admin (-dev)
  */
 
 function add_column( $columns ){
	 $columns['post_id_clmn'] = 'ID';
	 return $columns;
 }
 add_filter('manage_posts_columns', 'add_column', 4);
 
 function column_content( $column, $id ){
	 if( $column === 'post_id_clmn')
		  echo $id;
 }
 add_action('manage_posts_custom_column', 'column_content', 4, 2);
 
 ////////////////////////////////////////////////////////////////////////////////////
 // custom get post id using user and permalink
 ////////////////////////////////////////////////////////////////////////////////////
 
 function get_orientations_post_id(){
	 // # get or make permalink
	 // $permalink_explode = explode('/', "$_SERVER[REQUEST_URI]");
	 // $slug = $permalink_explode[2];
	 // $post = get_page_by_path($slug, OBJECT, 'Orientations');
	 $slug = '';
	 if ( is_page() ){
		 $slug = get_queried_object()->post_name;
	 }
	 $post = ta2ta_get_post_id_by_slug($slug, 'Orientations');
	 if($post){
		 $id = $post->ID;
		 return $id;
	 } else {
		 return 'NOID';
	 }
 }
 
 /**
  * Get organization id for a loggedin user
  */
 
 function get_post_id_for_organization(){
	 // this function requires a logged-in user_id
	 // if( !is_user_logged_in() ){
	 // 	wp_redirect( '/ta-login' );
	 // 		exit;
	 // }
	 $user_id = get_current_user_id();
	 $org = get_field( "organization_for_user" , "user_{$user_id}" );
	 $post = ta2ta_get_post_id_by_slug( $org, 'organizations' );
	 $id= null;
	 if( $post ){
		 $id = $post->ID;
		 $post = null;
		 return $id;
	 } else {
		 return 'Current user has no Organization affiliation';
	 }
 }
 
 /**
  * All Organizations Objects array
  * Used in Events, Users choices for organization
  * @return organizations[]
  * 
  */
 
  function organizations_posts_OBJ(){
	 $array_obj_of_type = array();
	 $args = [
		 'post_type'	=>	'organizations',
		 'posts_per_page' => -1,
		 'post_status' => 'publish',
		 'orderby'	=> 'post_title',
		 'order'	=> 'ASC'
	 ];
	 $query = new WP_Query($args);
	 $array_obj_of_type = $query->posts;
	 wp_reset_query();
	 return $array_obj_of_type;
  }
 /**
  * All Organizations names array
  * Used in Events, Users choices for organization
  * @return organizations[]
  * 
  */
  function organizations_posts_list(){
	 $list_of_type_as_array = [];
	 $all_objs_of_type = organizations_posts_OBJ();
	 foreach($all_objs_of_type as $obj_type){
		 $list_of_type_as_array[] = $obj_type->post_title;
	 }
	 return $list_of_type_as_array;
  }
 
 /**
  * All Organizations Grant Project list array
  * Used in Library choices for Uploading documents
  * @return grant_projects @key : organization_id @value [grant_projects] array
  * 
  */
 
  function organizations_grant_projects(){
	 $array_obj_of_orgs = array();
	 $args = [
		 'post_type'	=>	'organizations',
		 'meta_key'	=> 'grant_projects_for_directory',
		 'posts_per_page' => -1,
		 'post_status' => 'publish',
		 'orderby'	=> 'post_title',
		 'order'	=> 'ASC'
	 ];
	 $org_query = new WP_Query($args);
	 if ( $org_query->have_posts() ) : 
		 while ( $org_query->have_posts() ): $org_query->the_post();
			 if( have_rows('grant_projects_for_directory') ):
				 while( have_rows('grant_projects_for_directory' ) ) : the_row();
					 $array_of_grant_project[] = ['project_title' => get_sub_field('project_title'), 'award_number' => get_sub_field('list_of_award_numbers')[0] ['award_number'] ?? '', 'grant_programs' => get_sub_field('grant_programs')?? ''];
				 endwhile;
			 endif;
			 $array_obj_of_orgs[get_the_ID()] = $array_of_grant_project;
		 endwhile;
		 wp_reset_postdata();
	 endif;
	 wp_reset_query();
	 return $array_obj_of_orgs;
  }
 /**
  * All User Grant Project access : The user in each organization has access only to 
  * projects listed under her/his Institution.
  * @return array | an array of grant project[awards[grant program]]
  */
 
 function organization_user_grant_projects(){
	 $org_post_id = get_post_id_for_organization();
	 $projects = organizations_grant_projects();
	 $grant_projects = $projects[ $org_post_id ];
	 $grant_projects_choices = [];
	 $award_choices = [];
	 $grant_program_choices = [];
	 // foreach($grant_projects as $grant_project){
	 // 	$grant_projects_choices[] = $grant_project['project_title'];
	 // 	$award_choices[$grant_project['project_title']] = $grant_project['award_number'];
	 // 	$grant_program_choices[$grant_project['award_number']] = $grant_project['grant_programs'];
	 // }
	 foreach($grant_projects as $grant_project){
		 $grant_projects_choices[ $grant_project['project_title'] ] = [ $grant_project['award_number'] => $grant_project['grant_programs'] ] ;
	 }
	 return array_merge( $grant_projects_choices );
	 //return array_merge( ['grant_projects' => $grant_projects_choices] , $award_choices, $grant_program_choices );
	 //return $grant_projects;
 }
 
 /**
  * All grant programs terms 
  * @param obj_list object of all terms in a taxonomy e.i grant programs
  * 
  * @return terms_list a list of all terms of one taxonomy 
  */
 
  // Get the object list of terms of grant programs
 function get_grant_programs_Obj(){
	  return get_terms(array('taxonomy'=>'grant-program', 'hide_empty' => false));
	 }
 function get_topic_areas_Obj(){
	 return get_terms(array('taxonomy'=>'topic-area', 'hide_empty' => false));
 }
 function get_target_audiences_Obj(){
	 return get_terms(array('taxonomy'=>'target-audience', 'hide_empty' => false));
 }
 function get_awards_Obj(){
	 return get_terms(array('taxonomy'=>'award', 'hide_empty' => false));
 }
 
 /**
  * All grant programs terms 
  * Function used to get the terms of one taxonomy
  * @param term_OBJ object of all terms in a taxonomy e.i grant programs
  * 
  * @return terms_list a list of all terms of one taxonomy 
  */
  //function to pass Object list to list of names array
  function ta2ta_get_terms_list( $terms_OBJ){
	 $i = 0;
	 $terms_list=[];
	 foreach( $terms_OBJ as $term ){
		 $terms_list[$i] = $term->name;
		 $i++;
	 }
	 return $terms_list;
  };
 /**
  * All Awards and Grant Programs by Grant Projects 
  * 
  */
 
  function grant_project_awards($grant_project){
	 
  }
 
 ////////////////////////////////////////////////////////////////////////////////////
 // Table for document Editing
 ////////////////////////////////////////////////////////////////////////////////////
 function display_grid_table() {
	 // Get the current logged in user ID to find the organization to display for edit
	 $user_id = get_current_user_id();
	 $org = get_field("organization_for_user", "user_{$user_id}");
	 //$string = do_shortcode('[doc_library layout="table" doc_author="' . $org .  '" content="image,title,status,date_modified,link" image_size="300x400" shortcodes="true"]');
	 $string = do_shortcode('[doc_library layout="table" content="image,title,status,date_modified,link" image_size="300x400" shortcodes="true"]');
	 
	 return $string;
 }
 add_shortcode( 'view_my_org_documents', 'display_grid_table' );
 
 
 function user_dashboard_grant_projects (){
	 
	 $post_id = get_post_id_for_organization();
	 
	 $string = '';
	 
	 if( have_rows( 'grant_projects_for_directory', $post_id ) ):	
	 
		 while( have_rows( 'grant_projects_for_directory', $post_id ) ) : the_row(); 
	 
			 $project_archive = get_sub_field( 'archived' );
	 
			 if( !$project_archive ) {
 
				   $project_title = get_sub_field( 'project_title' ); 
				  $project_summary = get_sub_field( 'summary' );
			 
				 $string = $string . '<li>' . $project_title . '</li> <br />';
			 }
	 
		 endwhile;
	 endif;
	 $html = '<div><ul>' . $string . '</ul></div>';
	 
		 
	 return $html;
 }
 add_shortcode( 'my_grant_projects', 'user_dashboard_grant_projects' );
 
 
 ///////////////////////////////////////////////////////////////////////////////////////////////
 // This snippet adds metadata of post to search index
 ///////////////////////////////////////////////////////////////////////////////////////////////
 add_filter('wpfts_index_post', function($index, $post)
  { 
	  global $wpdb;
  
	  // Basic tokens 
	  /* 
	   * This piece of code was commented out intentionally to display things
	   * which was already done before in the caller code
		  $index['post_title'] = $post->post_title;
		  $index['post_content'] = strip_tags($post->post_content);
	  */
	  
		  // Adding new token "organazation_data" specially for posts of type "organization"
		  $data = array(); 
		  $data[] = get_post_meta($post->ID, 'external_link_for_directory', true);
					
		   $repeater_value = get_post_meta($post->ID, 'grant_projects_for_directory', true);
		  if ($repeater_value) {
				for ($i=0; $i<$repeater_value; $i++) {
				  $meta_key1 = 'grant_projects_for_directory_'.$i.'_project_title';
				  $meta_key2 = 'grant_projects_for_directory_'.$i.'_summary';
				  
				 $data[] = get_post_meta($post->ID, $meta_key1, true);
				 $data[] = get_post_meta($post->ID, $meta_key2, true);
				}
		  }
		  $index['directory_data'] = implode(' ', $data);
	  
		  $data = array(); 
		 $data[] = get_post_meta($post->ID, 'organization_for_library', true);
		  $data[] = get_post_meta($post->ID, 'external_link_for_library', true);
		  
		  $index['library_data'] = implode(' ', $data);	 	
 
 
	  return $index; 
  }, 3, 2);
 
 
 /**
  * Document Library Pro Image
  */
 
  add_filter( 'document_library_pro_data_image', function( $image, $post ) {
 
	 $attachment_id = get_post_meta($post->ID, '_dlp_attached_file_id', true);
	 $attachment = wp_get_attachment_url($attachment_id);
	 $image_url = str_replace('.pdf','-pdf.jpg', $attachment);
	 $image_url = str_replace('--pdf','-pdf', $image_url); //some cases have double -- causes image not to be found
	 $image_string = '<div class="container-image-document"><img class="image-document" src="' . $image_url . '" /></div>';
 
	 //$image = get_the_post_thumbnail($this->post->ID, 'full', array( 'class' => 'aligncenter' ));
	 // Do something with $image
	 return '<div class="document-image">' . $image_string . '</div>';
 }, 10, 2 );
 
 ################################
 
 /**
  * Twenty Twenty Four Theme 
  * Create the post type "organizations".
  *
  * @see register_post_type() for registering custom post types.
  */
 
 
  function twenty_twenty_four_theme_create_organizations_post_type()
  {
	  $args = array(
		  'labels' => array(
					 'name' =>  esc_html__('Organizations','twenty-twenty-four-theme'),
					 'singular_name' =>  esc_html__('Organization','twenty-twenty-four-theme'),
					 'search_items'  => __( 'Search Organizations' ),
					 'all_items' => __( 'All Organizations', 'textdomain' ),
					 'parent_item'       => __( 'Parent Organizations', 'textdomain' ),
					 'parent_item_colon' => __( 'Parent Organizations:', 'textdomain' ),
					 'edit_item'         => __( 'Edit Organization' ),
					 'update_item'       => __( 'Update Organization' ),
					 'add_new' 			=> __( 'Add new Organization' ),
					 'add_new_item'      => __( 'Add New Organization' ),
					 'new_item_name'     => __( 'New Organization' ),
					 'view_item' 		=> __( 'view Organization' ),
					 'view_items' 		=> __( 'view Organizations' ),
					 'archives' 			=> __( 'Organizations' )
		  ),
		  'public' => true,
		  'publicaly_queryable'=> true,
		  'query_var'         => true,
		  'hierarchical' => false,
		  'has_archive' => true,
		  'menu_icon' => 'dashicons-bank',
		  'supports' => [
			 'title', 
			 'editor',
			 'author',
			 'thumbnail',
			 'excerpt',
			 'trackbacks',
			 'custom-fields',
			 'comments',
			 'revisions', 
			 'page-attributes',
			 'post-formats'
		  ],
		 'taxonomies' => ['category','post_tag' ],
		 'capabilities' =>[
  
		  ],
		  'can_export' => true,
		  'show_in_rest' => true,
		  'rewrite'           => array( 'slug' => 'organizations', 'with_front' => true ),
	  );
  
  
	  register_post_type('organizations', $args);
  
  }
  add_action('init', 'twenty_twenty_four_theme_create_organizations_post_type');
 
  /**
  * Twenty Twenty Four Theme 
  * Create the post type "ta-project".
  *
  * @see register_post_type() for registering custom post types.
  */
 
  function twenty_twenty_four_theme_create_ta_project_post_type()
  {
	  $args = array(
		  'labels' => array(
					 'name' =>  esc_html__('Project','twenty-twenty-four-theme'),
					 'singular_name' =>  esc_html__('Project','twenty-twenty-four-theme'),
					 'search_items'  => __( 'Search Projects' ),
					 'all_items' => __( 'All Projects', 'textdomain' ),
					 'parent_item'       => __( 'Parent Organizations', 'textdomain' ),
					 'parent_item_colon' => __( 'Parent Organizations:', 'textdomain' ),
					 'edit_item'         => __( 'Edit Project' ),
					 'update_item'       => __( 'Update Project' ),
					 'add_new' 			=> __( 'Add new Project' ),
					 'add_new_item'      => __( 'Add New Project' ),
					 'new_item_name'     => __( 'New Project' ),
					 'view_item' 		=> __( 'view Project' ),
					 'view_items' 		=> __( 'view Projects' ),
					 'archives' 			=> __( 'Projects' )
		  ),
		  'public' => true,
		  'publicaly_queryable'=> true,
		  'query_var'         => true,
		  'hierarchical' => false,
		  'has_archive' => true,
		  'menu_icon' => 'dashicons-portfolio',
		  'supports' => [
			 'title', 
			 'editor',
			 'author',
			 // 'thumbnail',
			 // 'excerpt',
			 'trackbacks',
			 'custom-fields',
			 'comments',
			 'revisions', 
			 // 'page-attributes',
			 'post-formats'
		  ],
		 //'taxonomies' => ['category','post_tag' ],
		 'capabilities' =>[
  
		  ],
		  'can_export' => true,
		  'show_in_rest' => true,
		  'rewrite'           => array( 'slug' => 'projects', 'with_front' => true ),
	  );
  
  
	  register_post_type('projects', $args);
  
  }
  add_action('init', 'twenty_twenty_four_theme_create_ta_project_post_type');
 
 /**
  * Twenty Twenty Four Theme 
  * Create the post type "orientations".
  *
  * @see register_post_type() for registering custom post types.
  */
 
 
 function twenty_twenty_four_theme_create_orientation_post_type()
 {
	 $args = array(
		 'labels' => array(
					 'name' =>  esc_html__('Orientations','twenty-twenty-four-theme'),
					 'singular_name' =>  esc_html__('Orientation','twenty-twenty-four-theme'),
					 'search_items'  => __( 'Search Orientations' ),
					 'all_items' => __( 'All Orientations', 'textdomain' ),
					 'parent_item'       => __( 'Parent Orientations', 'textdomain' ),
					 'parent_item_colon' => __( 'Parent Orientations:', 'textdomain' ),
					 'edit_item'         => __( 'Edit Orientation' ),
					 'update_item'       => __( 'Update Orientation' ),
					 'add_new' 			=> __( 'Add new Orientation' ),
					 'add_new_item'      => __( 'Add New Orientation' ),
					 'new_item_name'     => __( 'New Orientation' ),
					 'view_item' 		=> __( 'view Orientation' ),
					 'view_items' 		=> __( 'view Orientations' ),
					 'archives' 			=> __( 'Orientations' )
		 ),
		 'public' => true,
		 'publicaly_queryable'=> true,
		 'query_var'         => true,
		 'hierarchical' => false,
		 'has_archive' => true,
		 'menu_icon' => 'dashicons-media-interactive',
		 'supports' => [
			 'title', 
			 'editor',
			 'author',
			 'thumbnail',
			 'excerpt',
			 'trackbacks',
			 'custom-fields',
			 'comments',
			 'revisions', 
			 'page-attributes',
			 'post-formats'
		 ],
		 //'taxonomies' => ['category','post_tag' ],
		 'capabilities' =>[
 
		 ],
		 'can_export' => true,
		 'show_in_rest' => true,
		 'rewrite'           => array( 'slug' => 'orientations', 'with_front' => true ),
	 );
 
 
	 register_post_type('orientations', $args);
 
 }
 add_action('init', 'twenty_twenty_four_theme_create_orientation_post_type');
 
 function twenty_twenty_four_theme_create_ovw_checklist_post_type()
 {
	 $args = array(
		 'labels' => array(
					 'name' =>  esc_html__('Ovw Checklists','twenty-twenty-four-theme'),
					 'singular_name' =>  esc_html__('Ovw Checklist','twenty-twenty-four-theme'),
					 'search_items'  => __( 'Search Ovw Checklists' ),
					 'all_items' => __( 'All Ovw Checklists', 'textdomain' ),
					 'parent_item'       => __( 'Parent Ovw_checklists', 'textdomain' ),
					 'parent_item_colon' => __( 'Parent Ovw_checklists:', 'textdomain' ),
					 'edit_item'         => __( 'Edit Ovw Checklist' ),
					 'update_item'       => __( 'Update Ovw Checklist' ),
					 'add_new' 			=> __( 'Add new Ovw Checklist' ),
					 'add_new_item'      => __( 'Add New Ovw Checklist' ),
					 'new_item_name'     => __( 'New Ovw Checklist' ),
					 'view_item' 		=> __( 'view Ovw Checklist' ),
					 'view_items' 		=> __( 'view Ovw Checklist' ),
					 'archives' 			=> __( 'Ovw Checklists' )
		 ),
		 'public' => true,
		 'publicaly_queryable'=> true,
		 'query_var'         => true,
		 'hierarchical' => false,
		 'has_archive' => true,
		 'menu_icon' => 'dashicons-pressthis',
		 'supports' => [
			 'title', 
			 'editor',
			 'author',
			 'thumbnail',
			 'excerpt',
			 'trackbacks',
			 'custom-fields',
			 'comments',
			 'revisions', 
			 'page-attributes',
			 'post-formats'
		 ],
		 //'taxonomies' => ['category','post_tag' ],
		 'capabilities' =>[
 
		 ],
		 'can_export' => true,
		 'show_in_rest' => true,
		 'rewrite'           => array( 'slug' => 'ovw_checklist', 'with_front' => true ),
	 );
 
 
	 register_post_type('ovw_checklist', $args);
 
 }
 add_action('init', 'twenty_twenty_four_theme_create_ovw_checklist_post_type');
 
 
 function twenty_twenty_four_theme_create_resource_post_type()
 {
	 $args = array(
		 'labels' => array(
					 'name' =>  esc_html__('Resources','twenty-twenty-four-theme'),
					 'singular_name' =>  esc_html__('Resource','twenty-twenty-four-theme'),
					 'search_items'  => __( 'Search Resources' ),
					 'all_items' => __( 'All Resources', 'textdomain' ),
					 'parent_item'       => __( 'Parent Resources', 'textdomain' ),
					 'parent_item_colon' => __( 'Parent Resources:', 'textdomain' ),
					 'edit_item'         => __( 'Edit Resource' ),
					 'update_item'       => __( 'Update Resource' ),
					 'add_new' 			=> __( 'Add new Resource' ),
					 'add_new_item'      => __( 'Add New Resource' ),
					 'new_item_name'     => __( 'New Resource' ),
					 'view_item' 		=> __( 'view Resource' ),
					 'view_items' 		=> __( 'view Resources' ),
					 'archives' 			=> __( 'Resources' )
		 ),
		 'public' => true,
		 'publicaly_queryable'=> true,
		 'query_var'         => true,
		 'hierarchical' => false,
		 'has_archive' => true,
		 'menu_icon' => 'dashicons-book',
		 'supports' => [
			 'title', 
			 'editor',
			 'author',
			 'thumbnail',
			 'excerpt',
			 'trackbacks',
			 'custom-fields',
			 'comments',
			 'revisions', 
			 'page-attributes',
			 'post-formats'
		 ],
		 //'taxonomies' => ['category','post_tag' ],
		 'capabilities' =>[
 
		 ],
		 'can_export' => true,
		 'show_in_rest' => true,
		 'rewrite'           => array( 'slug' => 'resource', 'with_front' => true ),
	 );
 
 
	 register_post_type('resource', $args);
 
 }
 add_action('init', 'twenty_twenty_four_theme_create_resource_post_type');
 
 
 function twenty_twenty_four_theme_create_newsletter_post_type()
 {
	 $args = array(
		 'labels' => array(
					 'name' =>  esc_html__('Newsletters','twenty-twenty-four-theme'),
					 'singular_name' =>  esc_html__('Newsletter','twenty-twenty-four-theme'),
					 'search_items'  => __( 'Search Newsletters' ),
					 'all_items' => __( 'All Newsletters', 'textdomain' ),
					 'parent_item'       => __( 'Parent Newsletters', 'textdomain' ),
					 'parent_item_colon' => __( 'Parent Newsletters:', 'textdomain' ),
					 'edit_item'         => __( 'Edit Newsletter' ),
					 'update_item'       => __( 'Update Newsletter' ),
					 'add_new' 			=> __( 'Add new newsletter' ),
					 'add_new_item'      => __( 'Add New Newsletter' ),
					 'new_item_name'     => __( 'New Newsletter' ),
					 'view_item' 		=> __( 'view newsletter' ),
					 'view_items' 		=> __( 'view newsletters' ),
					 'archives' 			=> __( 'newsletters' )
		 ),
		 'public' => true,
		 'publicaly_queryable'=> true,
		 'query_var'         => true,
		 'hierarchical' => false,
		 'has_archive' => true,
		 'menu_icon' => 'dashicons-clipboard',
		 'supports' => [
			 'title', 
			 'editor',
			 'author',
			 'thumbnail',
			 'excerpt',
			 'trackbacks',
			 'custom-fields',
			 'comments',
			 'revisions',
			 'page-attributes',
			 'post-formats'
		 ],
		 'taxonomies' => ['category','post_tag' ],
		 'capabilities' =>[
 
		 ],
		 'can_export' => true,
		 'show_in_rest' => true,
		 'rewrite'           => array( 'slug' => 'newsletter', 'with_front' => true ),
	 );
 
 
	 register_post_type('newsletter', $args);
 
 }
 add_action('init', 'twenty_twenty_four_theme_create_newsletter_post_type');
 
 
 /**
  * Twenty Twenty Four Theme
  * Create Customs taxonomies.
  *
  * @see register_taxomony() for registering custom taxonomies.
  */
 function twenty_twenty_four_theme_create_custom_taxonomies() {
	 // Add new taxonomy, make it hierarchical (like categories)
	 $labels = array(
		 'name'              => _x( 'Orientations Categories', 'taxonomy general name', 'textdomain' ),
		 'singular_name'     => _x( 'Orientation Category', 'taxonomy singular name', 'textdomain' ),
		 'search_items'      => __( 'Search Orientations Categories', 'textdomain' ),
		 'all_items'         => __( 'All Orientations Categories', 'textdomain' ),
		 'parent_item'       => __( 'Parent Orientation Category', 'textdomain' ),
		 'parent_item_colon' => __( 'Parent Orientation Category:', 'textdomain' ),
		 'edit_item'         => __( 'Edit Orientation Category', 'textdomain' ),
		 'update_item'       => __( 'Update Orientation Category', 'textdomain' ),
		 'add_new_item'      => __( 'Add New Orientation Category', 'textdomain' ),
		 'new_item_name'     => __( 'New Orientation Category Name', 'textdomain' ),
		 'not_found'         => __( 'No Orientations Categories Found', 'textdomain' ),
 // 		'back_to_items'     => __( 'Back to Orientations Categories', 'textdomain' ),
		 'menu_name'         => __( 'Orientations Categories', 'textdomain' ),
	 );
 
	 $args = array(
		 'labels'            => $labels,
		 'public' => true,
		 'publicly_queryable' => true,
		 'hierarchical' => true,
		 'show_ui' => true,
		 'show_in_menu' => true,
		 'show_in_nav_menus' => true,
		 'show_admin_column' => true,
		 'query_var'         => true,
		 'rewrite'           => array( 'slug' => 'orientation_categories', 'with_front' => true ),
		 'show_in_rest' => true,
		 'show_tagcloud' => true,
		 'rest_base' => 'orientations-categories',
		 'rest_controller_class' => 'WP_REST_Terms_Controller',
		 //'rest_namespace' => 'wp/v2',
		 'show_in_quick_edit' => true,
		 'sort' => true,
		 'show_in_graphql' => true,
	 );
 
	 register_taxonomy( 'orientations-categories', array( 'orientations', 'resource', 'ovw_checklist' ), $args );
 
	 unset( $args );
	 unset( $labels );
 }
 // hook into the init action and call create_..._taxonomies when it fires
 add_action( 'init', 'twenty_twenty_four_theme_create_custom_taxonomies', 0 );
 
 // /**
 //  * Modify the post thumbnail for newsletter, document post types
 //  * Hook into the post thumbnail retrieval process
 //  * 
 //  */
 
 // add_filter('post_thumbnail_html', 'custom_post_thumbnail', 10, 5);
 
 // function custom_post_thumbnail($html, $post_id, $post_thumbnail_id, $size, $attr) {
 
 //     // Check if it's a single post display
 //     if (is_single() && !empty($post_id)) {
 //         // Get the URL of the new featured image (replace with your logic)
 //         $new_featured_image_url = get_new_featured_image_url($post_id);
 
 //         if (!empty($new_featured_image_url)) {
 //             // Generate the new HTML for the featured image
 //             $html = '<img src="' . esc_url($new_featured_image_url) . '" alt="' . esc_attr(get_the_title($post_id)) . '" />';
 // 			$attach_data = wp_generate_attachment_metadata( $post_id, $new_featured_image_url );
 // 			wp_update_attachment_metadata( $post_id,  $attach_data );
 //             return $html;
 //         }
 //     }
 
 // 	if (is_archive() && !empty($post_id)) {
 //         // Get the new thumbnail ID (replace with your logic)
 //         $new_thumbnail_url = get_new_featured_image_url($post_id); //get_new_thumbnail_id($post_id);
 
 //         if (!empty($new_thumbnail_url)) {
 //             // Generate the new HTML for the featured image
 //             // $html = wp_get_attachment_image($new_thumbnail_id, $size, false, $attr);
 // 			$html = '<img src="' . esc_url($new_thumbnail_url). '" alt="' . esc_attr(get_the_title($post_id)) . '" />';
 // 			// $attach_data = wp_generate_attachment_metadata( $post_id, $new_thumbnail_id['icon'] );
 // 			// wp_update_attachment_metadata( $post_id,  $attach_data );
 //             return $html;
 //         }
 //     }
 
 //     // Return the original thumbnail HTML if conditions are not met
 //     return $html;
 // }
 
 
 // /**
 //  * Set PDF thumbnail as featured image for posts.
 //  */
 // function set_pdf_thumbnail_as_featured_image($post_id) {
 //     if (has_post_thumbnail($post_id)) {
 //         return;
 //     }
 
 // 	if( in_array(get_post_type($post_id), ['newsletter', 'dlp_document', 'document'])){
 // 		// Get the attached document details
 // 		switch (get_post_type($post_id)){
 // 			case 'newsletter':
 // 				$attachment_id = get_post_meta($post_id, 'newsletter_file', true);
 // 			case 'dlp_document':
 // 				$attachment_id = get_post_meta($post_id, '_dlp_attached_file_id', true);
 // 			default :
 // 				$attachment_id = get_post_meta($post_id, 'post', true);
 // 		}
 // 		$attachment = wp_get_attachment_url($attachment_id);
 // 		$image_url = str_replace('.pdf','-pdf.jpg', $attachment);
 // 		$image_url = str_replace('--pdf','-pdf', $image_url); //some cases have double -- causes image not to be found
 // 		$html = '<div class="container-image-document"><img class="image-document" src="' . $image_url . '" /></div>';
		 
 // 		// Get the PDF thumbnail attachment ID (replace 'pdf_thumbnail' with your actual attachment field name).
 // 		$pdf_thumbnail_id = get_post_meta($attachment_id, 'pdf_thumbnail', true);
 
 // 		if ($pdf_thumbnail_id) {
 // 			require_once( ABSPATH . 'wp-admin/includes/image.php' );
 // 			// Set the PDF thumbnail as the featured image.
 // 			set_post_thumbnail($post_id, $pdf_thumbnail_id);
 // 			$attach_data = wp_generate_attachment_metadata( $post_id, $pdf_thumbnail_id );
 // 			update_metadata($post_id, 'thumbnail', $attach_data, get_post_thumbnail_id($post_id));
 // 		}
 // 	}
 
 // }
 // add_action('save_post', 'set_pdf_thumbnail_as_featured_image');
 
 
 // // Function to get the URL of the new featured image (customize as needed)
 // function get_new_featured_image_url($post_id) {
 
 // 	$image_url = '';
 //     // Your logic to determine the new featured image URL
 // 	if(in_array(get_post_type($post_id), ['newsletter', 'dlp_document', 'document'])){
 
 // 		if(get_post_type($post_id) == 'newsletter'){
 // 			$pdf_file = get_field('newsletter_file', $post_id);
 // 			$image_url = $pdf_file['icon'] ?? ''; 
 // 		}
 
 // 		if((get_post_type($post_id) == 'dlp_document') || (get_post_type($post_id) == 'document')){
 // 			$attachment_id = get_post_meta($post_id, '_dlp_attached_file_id', true);
 // 			$attachment = wp_get_attachment_url($attachment_id);
 // 			$image_url = str_replace('.pdf','-pdf.jpg', $attachment);
 // 			$image_url = str_replace('--pdf','-pdf', $image_url); //some cases have double -- causes image not to be found
 // 		}
 // 	}
 // 	else {
 // 		$image_url = wp_get_attachment_image_url($post_id);
 // 	}
 // 	return $image_url;
 // }
 
 /**
  * This function modifies the main WordPress archive query for categories
  * and tags to include an array of post types instead of the default 'post' post type.
  *
  * @param object $query The main WordPress query.
  */
 function twenty_twenty_four_include_custom_post_types_in_archive_pages( $query ) {
	 if ( $query->is_main_query() && ! is_admin() && ( is_category() || is_tag() && empty( $query->query_vars['suppress_filters'] ) ) ) {
		 $query->set( 'post_type', array( 'post', 'documents', 'orientations', 'newsletter', 'organizations' ) );
	 }
 }
 add_action( 'pre_get_posts', 'twenty_twenty_four_include_custom_post_types_in_archive_pages' );
 
 
 /**
  * This function modifies the main WordPress loop to include 
  * custom post types along with the default 'post' post type.
  *
  * @param object $query The main WordPress query.
  */
 function twenty_twenty_four_theme_include_custom_post_types_in_main_loop( $query ) {
	 if ( $query->is_main_query() && $query->is_home() && ! is_admin() ) {
		 $query->set( 'post_type', array( 'post', 'documents', 'orientations', 'newsletter', 'organizations' ) );
	 }
 }
 add_action( 'pre_get_posts', 'twenty_twenty_four_theme_include_custom_post_types_in_main_loop' );
 
 
 /**
  * Document Image shortcode
  */
 
 function display_document_image() {
	 $attachment_id = get_post_meta(get_the_ID(), '_dlp_attached_file_id', true);
	 $attachment = wp_get_attachment_url($attachment_id);
	 $image_url = str_replace('.pdf','-pdf.jpg', $attachment);
	 $image_url = str_replace('--pdf','-pdf', $image_url); //some cases have double -- causes image not to be found
	 $image_string = '<div class="container-image-document"><img class="image-document" src="' . $image_url . '" /></div>';
	 
	 return $image_string;
 }
 add_shortcode('document_image', 'display_document_image');
 
 
 ////////////////////////////////////////////////////////////////////////////////////
 // user update param
 // ability to change email not implemented for security reasons.
 ////////////////////////////////////////////////////////////////////////////////////
 add_filter( 'gform_field_value_user_first_name_param', 'populate_with_first_name' );
 function populate_with_first_name( $value ) {
	 
	 $user_id = get_current_user_id();
		 
	 $first_name = get_user_meta( $user_id, 'first_name', true );
	 
	 return $first_name;
 }
 
 
 add_filter( 'gform_field_value_user_last_name_param', 'populate_with_last_name' );
 function populate_with_last_name( $value ) {
	 
	 $user_id = get_current_user_id();
		 
	 $last_name = get_user_meta( $user_id, 'last_name', true );
	 
	 return $last_name;
 }
 
 add_filter( 'gform_field_value_user_phone_number_param', 'populate_with_phone_number' );
 function populate_with_phone_number( $value ) {
	 
	 $user_id = get_current_user_id();
		 
	 $phone_number = get_field('phone_number_for_user', 'user_'.$user_id);
	 
	 return $phone_number;
 }
 
 add_filter( 'gform_field_value_user_email_param', 'populate_with_email' );
 function populate_with_email( $value ) {
	 
	 $user = wp_get_current_user();		
	 
	 return $user->user_email;
 }
 
 add_filter( 'gform_field_value_user_city_param', 'populate_with_city' );
 function populate_with_city( $value ) {
	 
	 $user_id = get_current_user_id();
		 
	 $city = get_field('city_for_user', 'user_'.$user_id);
	 
	 return $city;
 }
 
 add_filter( 'gform_field_value_user_state_param', 'populate_with_state' );
 function populate_with_state( $value ) {
	 
	 $user_id = get_current_user_id();
		 
	 $state = get_field('state_for_user', 'user_'.$user_id);
	 
	 return $state;
 }
 
 
 add_action( 'gform_after_submission_11', 'get_user_update_form', 10, 2 );
 function get_user_update_form( $entry, $form ) {
	 $user_id = get_current_user_id();
 
	 // get current name and compare to updated name
	 // if different, update name with the name entered on the form
	 $first_name_updated = rgar( $entry, '1.3' );
	 $first_name_current = get_user_meta( $user_id, 'first_name', true );
 
	   if ( $first_name_updated != $first_name_current ) {
		 wp_update_user([
			 'ID' => $user_id, // this is the ID of the user you want to update.
			 'first_name' => $first_name_updated,
		 ]);
	   }
	 
	 $last_name_updated = rgar( $entry, '1.6' );
	 $last_name_current = get_user_meta( $user_id, 'last_name', true );
 
	   if ( $last_name_updated != $last_name_current ) {
		 wp_update_user([
			 'ID' => $user_id, // this is the ID of the user you want to update.
			 'last_name' => $last_name_updated,
		 ]);
	   }
	
	 // get city and state and compare
	 // if different, update city and state with the values entered on the form
	 $city_updated = rgar( $entry, '5.3' );
	 $city_current = get_field('city_for_user', 'user_'.$user_id);
 
	   if ( $city_updated != $city_current ) {
		 update_field( 'city_for_user', $city_updated, 'user_'.$user_id );
	   }
	
	 $state_updated = rgar( $entry, '5.4' );
	 $state_current = get_field('state_for_user', 'user_'.$user_id);
 
	   if ( $state_updated != $state_current ) {
		 update_field( 'state_for_user', $state_updated, 'user_'.$user_id );
	   }
	 
	 // get phone number and compare
	 // if different, update phone number with the values entered on the form
	 $phone_updated = rgar( $entry, '6' );
	 $phone_current = get_field('phone_number_for_user', 'user_'.$user_id);
 
	   if ( $phone_updated != $phone_current ) {
		 update_field( 'phone_number_for_user', $phone_updated, 'user_'.$user_id );
	   }
	 
	 // if value was entered in password, update
	 $password_updated = rgar( $entry, '3' );
	   if ( !empty($password_updated) ) {
		 wp_set_password( $password_updated, $user_id );
	   }
 }
 
 
 // update organization post template
 add_filter( 'gform_after_create_post_6', 'gf_org_post_template', 10, 3 );
 function gf_org_post_template( $post_id, $entry, $form ) {
   
	 update_post_meta( $post_id, '_wp_page_template', 'directory-post' );
 }
 
 
 add_filter( 'gform_pre_render_1', 'user_registration_form' );
 add_filter( 'gform_pre_validation_1', 'user_registration_form' );
 add_filter( 'gform_pre_submission_filter_1', 'user_registration_form' );
 add_filter( 'gform_admin_pre_render_1', 'user_registration_form' );
 function user_registration_form( $form ) {	
  
	 // field id for organization drop down
	 $field = GFAPI::get_field( $form, '14' );
	 
	 // you can add additional parameters here to alter the posts that are retrieved
	 // more info: http://codex.wordpress.org/Template_Tags/get_posts
 
	 //query
	 $posts = new WP_Query( array(
			 'post_type'	=>	'organizations',
			 'posts_per_page'	=> -1,
			 'post_status' 		=> 'publish',
			 'orderby' 			=> 'title',
			 'order'   			=> 'ASC'
		 ) 
	 );
 
 
	 $choices = array();	
 
	 //check
	 if ( $posts->have_posts() ):
		 //loop
		 while ($posts->have_posts()): $posts->the_post();
			 $choices[] = array( 'text' => get_the_title(), 'value' => get_the_title() );
		 endwhile;
		 wp_reset_postdata();
 
	 endif;
	 wp_reset_query();
	 // update 'Select a Post' to whatever you'd like the instructive option to be
	 $field->placeholder = 'Select Organization';
	 $field->choices = $choices; 
	 
	 unset( $choices );
	 return $form;
 }
 
 function my_organization_title_logo (){
	 // get post_id using url/permalink
	 $post_id = get_post_id_for_organization();
	 
	 $post_image_block = get_the_post_thumbnail($post_id, 'medium');
	 $post_image_block = preg_replace( '/(width|height)=\"\d*\"\s/', "style='max-height:100px;'", $post_image_block );
	 
	 $post_title = get_the_title($post_id);
		 
	 $html = sprintf('<h2 style="text-align:center; margin:10px 0 5px 0">%s</h2>%s', esc_html( $post_title ),  $post_image_block );
	 
	 
	 return $html;
 }
 add_shortcode( 'my_organization', 'my_organization_title_logo' );
 
 
 add_action( 'gform_after_submission_8', 'get_organization_update_form', 10, 2 );
 
 function get_organization_update_form( $entry, $form ) {
	 $user_id = get_current_user_id();
 
	 $post_id = get_post_id_for_organization();
 
	 $logo_filename_updated = rgar( $entry, '8' );	   
 
	 if ( !empty($logo_filename_updated) ) {		  
		 
		 // Check the type of file. We'll use this as the 'post_mime_type'.
		 $filetype = wp_check_filetype( basename( $logo_filename_updated ), null );
 
		 // Get the path to the upload directory.
		 $wp_upload_dir = wp_upload_dir();
 
		 // Prepare an array of post data for the attachment.
		 $attachment = array(
			 'guid'           => $wp_upload_dir['url'] . '/' . basename( $logo_filename_updated ), 
			 'post_mime_type' => $filetype['type'],
			 'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $logo_filename_updated ) ),
			 'post_content'   => '',
			 'post_status'    => 'inherit'
		 );
 
		 // Insert the attachment.
		 $attach_id = wp_insert_attachment( $attachment, $logo_filename_updated, $post_id );
 
		 // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
		 require_once( ABSPATH . 'wp-admin/includes/image.php' );
 
		 // Generate the metadata for the attachment, and update the database record.
		 $attach_data = wp_generate_attachment_metadata( $attach_id, $logo_filename_updated );
		 wp_update_attachment_metadata( $attach_id, $attach_data );
 
		 set_post_thumbnail( $post_id, $attach_id );
	 }
	 
	 $link_updated = rgar( $entry, '5' );
	 $link_current = get_user_meta( $user_id, 'last_name', true );
 
	   if ( $link_updated != $link_current ) {
		 update_post_meta( $post_id, 'external_link_for_directory', $link_updated );
	   }
	
	 
	 $summary_updated = rgar( $entry, '7' );
	 $summary_current = get_field('city_for_user', 'user_'.$user_id);
 
	  if ( $summary_updated != $summary_current ) {
		 wp_update_post( array(
			 'ID'           => $post_id,
			 'post_content' => $summary_updated
		 ));
	   }
	  
 }
 
 ///////////////////////////////////////////////////////////////////////
 // hide fields grant project form - display acf form
 //////////////////////////////////////////////////////////////////////
 
 function my_acf_prepare_field( $field ) {
 
	 // Don't show this field once it contains a value.
	 if( $field['value'] ) {
		 return false;
	 }
	 return $field;
 }
 
 // Apply to fields named "example_field".
 add_filter('acf/prepare_field/name=external_link_for_directory', 'my_acf_prepare_field');
 add_filter('acf/prepare_field/name=_thumbnail_id', 'my_acf_prepare_field');
 
 add_filter('acf/prepare_field/name=document_title', 'my_acf_prepare_field');
 add_filter('acf/prepare_field/name=document_body', 'my_acf_prepare_field');
 add_filter('acf/prepare_field/name=newsletter_title', 'my_acf_prepare_field');
 add_filter('acf/prepare_field/name=newsletter_content_summary', 'my_acf_prepare_field');
 
 
 ////////////////////////////////////////////////////////////////////////////////////
 //
 // request rountable services
 // 
 ////////////////////////////////////////////////////////////////////////////////////
 add_filter( 'gform_pre_render_18', 'populate_roundtable_form' );
 add_filter( 'gform_pre_validation_18', 'populate_roundtable_form' );
 add_filter( 'gform_pre_submission_filter_18', 'populate_roundtable_form' );
 add_filter( 'gform_admin_pre_render_18', 'populate_roundtable_form' );
 function populate_roundtable_form( $form ) { 
 
	   foreach ( $form['fields'] as &$field ) {
		 
		 if( $field->label == 'TA Project' ) {
			 
			 $org_post_id = get_post_id_for_organization();
				 
			 $choices = array();
  
			   $repeater_value = get_post_meta($org_post_id, 'grant_projects_for_directory', true);
			  
			   for ($i=0; $i<$repeater_value; $i++) {
				 
				  $meta_key = 'grant_projects_for_directory_'.$i.'_project_title';
			 
				 $choices[] = array('text'  		=> get_post_meta($org_post_id, $meta_key, true), 
									'value' 		=> get_post_meta($org_post_id, $meta_key, true),
									   'isSelected' => false
								   );
			   } 
			 
			 $field->placeholder = 'Select a grant project';
			 $field->choices = $choices;
 
			 unset( $choices );
		 } 
		 
		 if( $field->label == 'Topic Area(s)' ) {
			 //admin options page
			 // $post_id = 12646;
			 
			 $choices = array();
			 $inputs = array();
			 
			   //$repeater_value = get_post_meta($post_id, 'topic_area', true);
			 $topic_areas_list = ta2ta_get_terms_list(get_topic_areas_Obj());
			 $input_id = 1;		
			   //for ($i=0; $i<$repeater_value; $i++) {
			   for ($i=0; $i<count($topic_areas_list); $i++) {
				 
				 $archived_key = 'topic_area_'.$i.'_archived';
				 
				 // if( get_post_meta($post_id, $archived_key, true) ) {
				 // 	continue;
				 // }
					 
				 if ( $input_id % 10 == 0 ) {
					 $input_id++;
				 }
				 
				  //$meta_key = 'topic_area_'.$i.'_item';
			 
				 $choices[] = array(
						 'text' 		=> 	$topic_areas_list[$i], //get_post_meta($post_id, $meta_key, true), 
						 'value' 	=> 	$topic_areas_list[$i] //get_post_meta($post_id, $meta_key, true)	
								 );   					
				 
				 $inputs[] = array('label' 	=> $topic_areas_list[$i], 
									 'id'		=> "8.{$input_id}"
									 );  
 
				  $input_id++;
			   } 
		 
			 $field->choices = $choices;	
			 $field->inputs = $inputs;	
			 
			 unset( $choices );
			 unset( $inputs );
		 }
	 }
	 
	 return $form;
 }
 
 ////////////////////////////////////////////////////////////////////////////////////
 //
 // request virtual services
 // 
 ////////////////////////////////////////////////////////////////////////////////////
 add_filter( 'gform_pre_render_19', 'populate_virtual_form' );
 add_filter( 'gform_pre_validation_19', 'populate_virtual_form' );
 add_filter( 'gform_pre_submission_filter_19', 'populate_virtual_form' );
 add_filter( 'gform_admin_pre_render_19', 'populate_virtual_form' );
 function populate_virtual_form( $form ) { 
 
	   foreach ( $form['fields'] as &$field ) {
		 
		 if( $field->label == 'TA Project' ) {
 
			 $org_post_id = get_post_id_for_organization();
				 
			 $choices = array();
  
			   $repeater_value = get_post_meta($org_post_id, 'grant_projects_for_directory', true);
			  
			   for ($i=0; $i<$repeater_value; $i++) {
				 
				  $meta_key = 'grant_projects_for_directory_'.$i.'_project_title';
			 
				 $choices[] = array('text'  		=> get_post_meta($org_post_id, $meta_key, true), 
									'value' 		=> get_post_meta($org_post_id, $meta_key, true),
									   'isSelected' => false
								   );
			   } 
			 
			 $field->placeholder = 'Select a grant project';
			 $field->choices = $choices;
			 
			 unset( $choices );
		 } 
		 
	 }
	 
	 return $form;
 }
 
 ////////////////////////////////////////////////////////////////////////////////////
 //
 // hover tiles shortcode
 // 
 ////////////////////////////////////////////////////////////////////////////////////
 function createTiles( $atts ) {
	 
	 $html = '
	 <div id="navGroup" class="full-width nav-group" onload="responsiveScroll()">
		 <div class="background-orange">
			 <a id="navButton3" href="'.get_site_url().'/'.'events/" class="whiteText nav-button-display">
				 <div class="nav-button-text position-sticky">Calendar</div>
				 <div class="nav-tile-hidden">Access in-person and online educational opportunities presented by OVW TA providers.</div>
			 </a>
		 </div>
		 <div class="background-purple">
			 <a id="navButton4" href="'.get_site_url().'/'.'directory/" class="whiteText nav-button-display">
				 <div class="nav-button-text position-sticky">Directory</div>
				 <div class="nav-tile-hidden">Access OVW TA provider organizations, TA project descriptions and contact information.</div>
			 </a>
		 </div>
		 <div class="background-teal">
			 <a id="navButton4" href="'.get_site_url().'/'.'document-library/" class="whiteText nav-button-display">
				 <div class="nav-button-text position-sticky">Library</div>
				 <div class="nav-tile-hidden">Access products and resources developed by OVW TA providers.</div>
			 </a>
		 </div>
		 <div class="background-grey">
			 <a id="navButton3" href="'.get_site_url().'/'.'orientations-page/" class="whiteText nav-button-display">
				 <div id="btnOrientations" class="nav-button-text position-sticky">Orientations</div>
				 <div class="nav-tile-hidden">Access orientation materials and information for all grant programs administered by OVW.</div>
			 </a>
		 </div>
	 </div>
	 ';
	 
	 return $html;
 }
 add_shortcode( 'hover-tiles', 'createTiles' );
 
 
 /////////#####################/////////////////////
 //	Add scripts for dashboard
 /////////#####################////////////////////
 
 function ta2ta_theme_add_customcss(){
	 //if( is_page('21') || is_page('9605') || is_page('19348') || is_page('12549') || is_page('30948')){
	 if( is_page_template('page-my-account') || is_page_template('edit-update-grant-projects') || is_page_template('edit-document')){
		 ?>
 
			 <link href="https://cdn.materialdesignicons.com/4.4.95/css/materialdesignicons.min.css" rel="stylesheet" />
			 
			 <!-- PLUGINS CSS STYLE -->
			 <link href="<?php echo get_template_directory_uri();?>/assets/plugins/simplebar/simplebar.css" rel="stylesheet" />
			 
			 <!-- SLEEK CSS -->
			 <link href="<?php echo get_template_directory_uri();?>/assets/plugins/data-tables/datatables.bootstrap4.min.css" rel="stylesheet">
			 <link href="<?php echo get_template_directory_uri();?>/assets/plugins/data-tables/responsive.datatables.min.css" rel="stylesheet">
			 <link id="sleek-css" rel="stylesheet" href="<?php echo get_template_directory_uri();?>/assets/css/sleek.css" />
			 <!-- <link href="https://unpkg.com/sleek-dashboard/dist/assets/css/sleek.min.css" rel="stylesheet"> -->
			 
			 <?php
	 }
 }
 
 add_action('wp_head', 'ta2ta_theme_add_customcss', 11 );
 
 
 ////////////////////////////////////////////////////////////////////////////////////
 //
 // script to scroll page down
 // 
 ////////////////////////////////////////////////////////////////////////////////////
 function scrollPage( $atts ) {
	 $url = urlencode(get_site_url().'/');
	 $html = '
	 <script>
			var url = window.location.href;
			if( url != decodeURI('. $url . '){
			 var access = document.getElementById("SubNavigation");
			 //access.scrollIntoView({behavior: "smooth"}, true);
		 }
	 </script>
	 ';
	 
	 return $html;
 }
 add_shortcode( 'scroll-script', 'scrollPage' );
 
 /**
  * 	Ta2ta Theme add custom javascript into the footer of the page
  * 
  */
 
  function ta2ta_theme_add_customjs(){
	 ?>
 <!-- Google tag (gtag.js) -->
 <script async src='https://www.googletagmanager.com/gtag/js?id=G-1YQ4'></script>
 <script>
	 window.dataLayer = window.dataLayer || [];
	 function gtag(){dataLayer.push(arguments);}
	 gtag('js', new Date());
 
	 gtag('config', 'G-14');
 </script>
 <!-- Smooth scroll header -->
 <script>
	 //document.getElementById("SubNavigation").scrollIntoView({behavior: "smooth"}, true);
 </script>
 <?php
 }
 
 add_action('wp_head', 'ta2ta_theme_add_customjs', 10 );
 
 /** 
  * 
  * Ta2ta theme add custom js in footer 
  * 
  * */
 function ta2ta_theme_add_customjs_footer(){
	 
	 if( is_front_page() ){ ?>
		 <script type="text/javascript">
			 var responsiveHeader = jQuery("#ResponsiveNavigation");
			 var subNavigation = jQuery("#SubNavigation");
			 var header_spacer = jQuery("#header_spacer");
			 jQuery(window).scroll(function() {
				 var scrollUser = jQuery(window).scrollTop();
				 var heightOffset = 207; //document.querySelector("#header-centered-logo").offsetHeight;
				 var subNavigationHeight = document.querySelector("#SubNavigation").offsetHeight;
				 var headerCenteredLogo = document.querySelector("#header-centered-logo").offsetTop;
				 var heightOffset2 = heightOffset + subNavigationHeight;
				 if ( scrollUser >= 207) {
					 responsiveHeader.addClass("is-pinned");
					 subNavigation.addClass("is-pinned");
					 header_spacer.addClass("is-pinned");
				 } else {
					 responsiveHeader.removeClass("is-pinned");
					 subNavigation.removeClass("is-pinned");
					 header_spacer.removeClass("is-pinned");
				 }
				 if( scrollUser >= heightOffset2){
					 //responsiveHeader.addClass("is-pinned");
					 subNavigation.addClass("merged");
				 } else {
					 responsiveHeader.removeClass("is-pinned");
					 subNavigation.removeClass("merged");
				 }
			 });
		 </script>	
		 <?php 
	 }
 
	 if( is_page_template('page-my-account') || is_page_template('edit-update-grant-projects') ){
		 ?>
		 <script>
			 jQuery(document).ready(function() {
			 jQuery('#responsive-data-table').DataTable({
			 "aLengthMenu": [[10, 20, 30, 50, 75, -1], [10, 20, 30, 50, 75, "All"]],
			 "pageLength": 10,
			 "dom": '<"row justify-content-between top-information"lf>rt<"row justify-content-between bottom-information"ip><"clear">'
			 });
			 });
		 </script>
		 <script src="<?php echo get_template_directory_uri();?>/assets/plugins/data-tables/jquery.datatables.min.js"></script>
		 <script src="<?php echo get_template_directory_uri();?>/assets/plugins/data-tables/datatables.bootstrap4.min.js"></script>
		 <script src="<?php echo get_template_directory_uri();?>/assets/plugins/data-tables/datatables.responsive.min.js"></script>
		 <script src="<?php echo get_template_directory_uri();?>/assets/js/sleek.bundle.js"></script>
		 <?php
	 }
	 ?>
	 <script src="<?php echo get_template_directory_uri();?>/assets/js/custom_ta2ta.js"></script>
	 <!-- <script src="<?php //echo get_template_directory_uri();?>/assets/js/fUtil.js"></script> -->
	 <?php
 }
 
 add_action('wp_footer', 'ta2ta_theme_add_customjs_footer', 10 );
 
 function showmeta_custom() {
 
	 $post_id = get_the_ID();
 
	 if( is_user_logged_in() ) {
 
		 acf_form_head($post_id); 
			 
		 acf_form();
	 }
 
 }
 add_shortcode('showmetacode', 'showmeta_custom');
 
 /**
  * Add custom stylesheet to admin area
  */
 
 function custom_admin_css() {
	 wp_enqueue_style('admin_styles' , get_template_directory_uri().'/assets/admin_section.css');
 }
 
 add_action('admin_head', 'custom_admin_css');
 
 /**
  *  REDIRECT IF USER IS NOT LOGGED IN OR IS NOT ADMIN 
  * 
  * */
 
 add_action( 'template_redirect', 'redirect_non_logged_users' );
 
 function redirect_non_logged_users() {
 
 //if ( !is_user_logged_in() && ( is_page('21') || is_page('9605') || is_page('19348') || is_page('12549') || is_page('5796')) && $_SERVER['PHP_SELF'] != '/wp-admin/admin-ajax.php' ) {
 if ( !is_user_logged_in() && (is_page_template('page-my-account') || is_page_template('edit-update-grant-projects') ) ) {
 
	 wp_redirect( '/' );
		 exit;
	 }
 }
 
 
 // Create the post attachement thumbnail
 
 add_filter( 'wp_generate_attachment_metadata', 'Twentytwentyfour_create_pdf_thumbnail', 10, 2 );
 
 function Twentytwentyfour_create_pdf_thumbnail( $metadata, $attachment_id ) {
 
	 //Get the attachment/post object
	 $attachment_obj = get_post( $attachment_id );
 
	 //Check for mime type pdf
	 if( 'application/pdf' == get_post_mime_type( $attachment_obj ) ) {
 
		 //Get attachment URL http://yousite.org/wp-content/uploads/yourfile.pdf
		 $attachment_url = wp_get_attachment_url($attachment_id);
		 //Get attachment path /some/folder/on/server/wp-content/uploads/yourfile.pdf
		 $attachment_path = get_attached_file($attachment_id );
 
		 //By adding [0] the first page gets selected, important because otherwise multi paged files wont't work
		 $pdf_source = $attachment_path.'[0]';
 
		 //Thumbnail format
		 $tn_format = 'jpg';
		 //Thumbnail output as path + format
		 $thumb_out = $attachment_path.'.'.$tn_format;
		 //Thumbnail URL
		 $thumb_url = $attachment_url.'.'.$tn_format;
 
		 //Setup various variables
		 //Assuming A4 - portrait - 1.00x1.41
		 $width = '300';
		 $height = '400';
		 $quality = '90';
		 $dpi = '300';
		 $resize = $width.'x'.$height;
		 $density = $dpi.'x'.$dpi;
 
		 //For configuration/options see: http://www.imagemagick.org/script/command-line-options.php
		 $a_exec = "convert -adaptive-resize $width -density $dpi -quality $quality $pdf_source $thumb_out";
		 $r_exec = "convert -resize $width -density $dpi -quality $quality $pdf_source $thumb_out";
		 $t_exec = "convert -thumbnail $width -density $dpi -quality $quality $pdf_source $thumb_out";
		 $s_exec = "convert -scale $width $pdf_source $thumb_out";
 
		 //Create the thumbnail with choosen option
		 exec($r_exec);
 
		 //Add thumbnail URL as metadata of pdf attachment
		 $metadata['thumbnail'] = $thumb_url;
 
	 }
 
	 return $metadata;
 
 }
 
 /**
  * Unset some custom fields from displaying on grid and in single document : Document Library Pro
  */
 add_filter( 'document_library_pro_custom_fields', function( $custom_fields_list) {
	 // Delete an unwanted custom field from the custom fields list.
	 unset( $custom_fields_list[ 'doc_author'] );
	 unset( $custom_fields_list[ 'document_file'] );
	 unset( $custom_fields_list[ 'document_title'] );
	 unset( $custom_fields_list[ 'document_body'] );
	 return $custom_fields_list;
 }, 10 );
 
 
 /**
  * Add choices for ACF form pages - Conflict with FacetWP form
  */
 // if( is_page('32407') || is_page('37686') || is_page('8003') ){
 // //if( is_page_template( 'edit-document' ) ){
 // 	function include_acf_prefered(){
		 include_once ( get_template_directory() . '/functions/acf_choices_functions.php' );
 // 		die( get_template_directory() . '/functions/acf_choices_functions.php' );
 // 	}
 // 	do_action('wp_head','include_acf_prefered');
 //	die('in is page');
 // }
 //add_action('wp_head','include_acf_prefered');
 //add_action( 'wp_loaded','include_acf_prefered');
 
 
 add_action( 'wp_footer', function() {
	 ?>
	   <script>
		 document.addEventListener('facetwp-refresh', function() {
		   console.log('FACE REFRESH FIRED');
		 });
	   </script>
	 <?php
   }, 100 );
 
 /*
  * Authentication for facet
  * Please note that caching may interfere with the NONCE,
  * causing AJAX requests to fail. Please DISABLE CACHING for facet pages,
  * or set the cache expiration to < 12 hours!
 */
 
 add_action( 'wp_footer', function() {
   ?>
	 <script>
	   document.addEventListener('facetwp-loaded', function() {
		 if (! FWP.loaded) { // initial pageload
		   FWP.hooks.addFilter('facetwp/ajax_settings', function(settings) {
			 settings.headers = { 'X-WP-Nonce': FWP_JSON.nonce };
			 return settings;
		   });
		 }
	   });
	 </script>
   <?php
 }, 100 );
 // End the clock
 $end_time = microtime(true);
 $execution_time = $end_time - $start_time;
 
 //echo "Execution time of script = " . $execution_time . " seconds";
   ?>