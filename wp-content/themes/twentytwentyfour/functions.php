<?php
/**
 * Twenty Twenty-Four functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Twenty Twenty-Four
 * @since Twenty Twenty-Four 1.0
 */
// Start the clock
$start_time = microtime(true); //TEST
/**
 * Register block styles.
 */

if ( ! function_exists( 'twentytwentyfour_block_styles' ) ) :
	/**
	 * Register custom block styles
	 *
	 * @since Twenty Twenty-Four 1.0
	 * @return void
	 */
	function twentytwentyfour_block_styles() {

		register_block_style(
			'core/details',
			array(
				'name'         => 'arrow-icon-details',
				'label'        => __( 'Arrow icon', 'twentytwentyfour' ),
				/*
				 * Styles for the custom Arrow icon style of the Details block
				 */
				'inline_style' => '
				.is-style-arrow-icon-details {
					padding-top: var(--wp--preset--spacing--10);
					padding-bottom: var(--wp--preset--spacing--10);
				}

				.is-style-arrow-icon-details summary {
					list-style-type: "\2193\00a0\00a0\00a0";
				}

				.is-style-arrow-icon-details[open]>summary {
					list-style-type: "\2192\00a0\00a0\00a0";
				}',
			)
		);
		register_block_style(
			'core/post-terms',
			array(
				'name'         => 'pill',
				'label'        => __( 'Pill', 'twentytwentyfour' ),
				/*
				 * Styles variation for post terms
				 * https://github.com/WordPress/gutenberg/issues/24956
				 */
				'inline_style' => '
				.is-style-pill a,
				.is-style-pill span:not([class], [data-rich-text-placeholder]) {
					display: inline-block;
					background-color: var(--wp--preset--color--base-2);
					padding: 0.375rem 0.875rem;
					border-radius: var(--wp--preset--spacing--20);
				}

				.is-style-pill a:hover {
					background-color: var(--wp--preset--color--contrast-3);
				}',
			)
		);
		register_block_style(
			'core/list',
			array(
				'name'         => 'checkmark-list',
				'label'        => __( 'Checkmark', 'twentytwentyfour' ),
				/*
				 * Styles for the custom checkmark list block style
				 * https://github.com/WordPress/gutenberg/issues/51480
				 */
				'inline_style' => '
				ul.is-style-checkmark-list {
					list-style-type: "\2713";
				}

				ul.is-style-checkmark-list li {
					padding-inline-start: 1ch;
				}',
			)
		);
		register_block_style(
			'core/navigation-link',
			array(
				'name'         => 'arrow-link',
				'label'        => __( 'With arrow', 'twentytwentyfour' ),
				/*
				 * Styles for the custom arrow nav link block style
				 */
				'inline_style' => '
				.is-style-arrow-link .wp-block-navigation-item__label:after {
					content: "\2197";
					padding-inline-start: 0.25rem;
					vertical-align: middle;
					text-decoration: none;
					display: inline-block;
				}',
			)
		);
		register_block_style(
			'core/heading',
			array(
				'name'         => 'asterisk',
				'label'        => __( 'With asterisk', 'twentytwentyfour' ),
				'inline_style' => "
				.is-style-asterisk:before {
					content: '';
					width: 1.5rem;
					height: 3rem;
					background: var(--wp--preset--color--contrast-2, currentColor);
					clip-path: path('M11.93.684v8.039l5.633-5.633 1.216 1.23-5.66 5.66h8.04v1.737H13.2l5.701 5.701-1.23 1.23-5.742-5.742V21h-1.737v-8.094l-5.77 5.77-1.23-1.217 5.743-5.742H.842V9.98h8.162l-5.701-5.7 1.23-1.231 5.66 5.66V.684h1.737Z');
					display: block;
				}

				/* Hide the asterisk if the heading has no content, to avoid using empty headings to display the asterisk only, which is an A11Y issue */
				.is-style-asterisk:empty:before {
					content: none;
				}

				.is-style-asterisk:-moz-only-whitespace:before {
					content: none;
				}

				.is-style-asterisk.has-text-align-center:before {
					margin: 0 auto;
				}

				.is-style-asterisk.has-text-align-right:before {
					margin-left: auto;
				}

				.rtl .is-style-asterisk.has-text-align-left:before {
					margin-right: auto;
				}",
			)
		);
	}
endif;

add_action( 'init', 'twentytwentyfour_block_styles' );

/**
 * Enqueue block stylesheets.
 */

if ( ! function_exists( 'twentytwentyfour_block_stylesheets' ) ) :
	/**
	 * Enqueue custom block stylesheets
	 *
	 * @since Twenty Twenty-Four 1.0
	 * @return void
	 */
	function twentytwentyfour_block_stylesheets() {
		/**
		 * The wp_enqueue_block_style() function allows us to enqueue a stylesheet
		 * for a specific block. These will only get loaded when the block is rendered
		 * (both in the editor and on the front end), improving performance
		 * and reducing the amount of data requested by visitors.
		 *
		 * See https://make.wordpress.org/core/2021/12/15/using-multiple-stylesheets-per-block/ for more info.
		 */
		wp_enqueue_block_style(
			'core/button',
			array(
				'handle' => 'twentytwentyfour-button-style-outline',
				'src'    => get_parent_theme_file_uri( 'assets/css/button-outline.css' ),
				'ver'    => wp_get_theme( get_template() )->get( 'Version' ),
				'path'   => get_parent_theme_file_path( 'assets/css/button-outline.css' ),
			)
		);
	}
endif;

add_action( 'init', 'twentytwentyfour_block_stylesheets' );

/**
 * Register pattern categories.
 */

if ( ! function_exists( 'twentytwentyfour_pattern_categories' ) ) :
	/**
	 * Register pattern categories
	 *
	 * @since Twenty Twenty-Four 1.0
	 * @return void
	 */
	function twentytwentyfour_pattern_categories() {

		register_block_pattern_category(
			'twentytwentyfour_page',
			array(
				'label'       => _x( 'Pages', 'Block pattern category', 'twentytwentyfour' ),
				'description' => __( 'A collection of full page layouts.', 'twentytwentyfour' ),
			)
		);
	}
endif;

add_action( 'init', 'twentytwentyfour_pattern_categories' );


########################################################################

//NEED SOME CORRECTION require ABSPATH 

$roots_includes = array(  
	'/functions/calendar-functions.php', // functions related to the calendar
	'/functions/library-functions.php', // functions related to the library
	'/functions/acf_choices_functions.php', // functions related to the library
	'/functions/user-customfield-function.php', // functions add custom field organization to choice forms
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
	$org = get_user_meta( $user_id, 'organization_for_user', true ); // get_field( "organization_for_user" , "user_{$user_id}" );

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
	$o_query = new WP_Query($args);
	$array_obj_of_type = $o_query->posts;
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
	$array_of_grant_project = array();
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
				$array_of_grant_project = [];
				while( have_rows('grant_projects_for_directory' ) ) : the_row();
					$array_of_grant_project[] = ['project_title' => get_sub_field('project_title'), 'award_number' => get_sub_field('list_of_award_numbers')[0] ['award_number'] ?? '', 'grant_programs' => get_sub_field('grant_programs')?? ''];
				endwhile;
			endif;
			$array_obj_of_orgs[ get_the_ID() ] = $array_of_grant_project;
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
	foreach($grant_projects as $grant_project){
		$grant_projects_choices[] = $grant_project['project_title'];
		$award_choices[$grant_project['project_title']] = $grant_project['award_number'];
		$grant_program_choices[$grant_project['award_number']] = $grant_project['grant_programs'];
	}
	return array_merge( ['grant_projects' => $grant_projects_choices] , $award_choices, $grant_program_choices );

}

function organization_user_grant_project_grant_programs( $post_id, $param = null, $exctract_field = null ){
	$org_post_id = get_post_id_for_organization();
	$projects = organizations_grant_projects();
	$grant_projects = $projects[ $org_post_id ];
	$field_choices = [];	
	$grant_projects_choices = [];
	$award_choices = [];
	$grant_program_choices = [];
	foreach($grant_projects as $grant_project){
		$grant_projects_choices[] = $grant_project['project_title'];
		$award_choices[$grant_project['project_title']] = $grant_project['award_number'];
		$grant_program_choices[$grant_project['award_number']] = $grant_project['grant_programs'];
	}
	if ( isset( $exctract_field ) && !empty( $exctract_field ) ){

		switch ($exctract_field){
			case 'grant_project':
				$field_choices = $grant_projects_choices[$param] ?? $grant_projects_choices;
				break;
			case 'awards':
				$field_choices = $award_choices[$param] ?? $award_choices;
				break;
			case 'grant_programs':
				$field_choices = $grant_program_choices[ $param ] ?? $grant_program_choices;
				break;
			default :
				$field_choices = array_merge( $grant_projects_choices[ $grant_project['project_title'] ] = [ $grant_project['award_number'] => $grant_project['grant_programs'] ] );
		}
	}
	return $field_choices;

}


////////////////////////////////////////////////////////////////////////////////////
// Table for document Editing
////////////////////////////////////////////////////////////////////////////////////
function display_grid_table() {

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
			//'comments',
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


/**
 * Populate user data on edit user form
 */
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

	$phone_number = get_user_meta( $user_id, 'phone_number_for_user', true );
	
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

	$city = get_user_meta( $user_id, 'city_for_user', true );
	
    return $city;
}

add_filter( 'gform_field_value_user_state_param', 'populate_with_state' );
function populate_with_state( $value ) {
	
	$user_id = get_current_user_id();
		
	$state = get_user_meta( $user_id, 'state_for_user', true );
	
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
	$city_current = get_user_meta( $user_id,'city_for_user', true );

	  if ( $city_updated != $city_current ) {
		update_field( 'city_for_user', $city_updated, 'user_'.$user_id );
	  }
   
	$state_updated = rgar( $entry, '5.4' );
	$state_current = get_user_meta( $user_id,'state_for_user', true );

	  if ( $state_updated != $state_current ) {
		update_field( 'state_for_user', $state_updated, 'user_'.$user_id );
	  }
	
	// get phone number and compare
	// if different, update phone number with the values entered on the form
	$phone_updated = rgar( $entry, '6' );
	$phone_current = get_user_meta( $user_id,'phone_number_for_user', true );

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


/**
 * Handle the update Organization Form
 */

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
	$summary_current = get_user_meta( $user_id, 'city_for_user', true );

	 if ( $summary_updated != $summary_current ) {
		wp_update_post( array(
			'ID'           => $post_id,
			'post_content' => $summary_updated
		));
	  }
	 
}

/**
 * Hide some already populated fields on acf forms
 */

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


/**
 * Populate Request roundtable form
 */

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
			
			$choices = array();
			$inputs = array();

			$topic_areas_list = get_field( 'topic_area', 'options', true );

			$input_id = 1;		

  			for ($i=0; $i<count($topic_areas_list); $i++) {
				
				if( $topic_areas_list[$i]['archived'] == true) {
					continue;
				}
					
				if ( $input_id % 10 == 0 ) {
                	$input_id++;
            	}

			
				$choices[] = array(
						'text' 		=> 	$topic_areas_list[$i]['item'], 
						'value' 	=> 	$topic_areas_list[$i]['item'] 	
								);   					
				
				$inputs[] = array('label' 	=> $topic_areas_list[$i]['item'], 
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

/**
 * Make shortcode to add menu on the frontpage 
 */

function createTiles( $atts ) {
    
	$html = '
	<div id="navGroup" class="full-width nav-group">
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

/**
 * Custom Script for the Dashboard - css
 */

function ta2ta_theme_add_customcss(){
	if( is_page_template('page-my-account') || is_page_template('edit-update-grant-projects') || is_page_template('edit-document') || is_page_template('page-reporting')){
		?>

			<link href="https://cdn.materialdesignicons.com/4.4.95/css/materialdesignicons.min.css" rel="stylesheet" />
			
			<!-- PLUGINS CSS STYLE -->
			<link href="<?php echo get_template_directory_uri();?>/assets/plugins/simplebar/simplebar.css" rel="stylesheet" />
			
			<!-- SLEEK CSS -->
			<link href="<?php echo get_template_directory_uri();?>/assets/plugins/data-tables/datatables.bootstrap4.min.css" rel="stylesheet">
			<link href="<?php echo get_template_directory_uri();?>/assets/plugins/data-tables/responsive.datatables.min.css" rel="stylesheet">
			<link id="sleek-css" rel="stylesheet" href="<?php echo get_template_directory_uri();?>/assets/css/sleek.css" />			
			<?php
	}
}

add_action('wp_head', 'ta2ta_theme_add_customcss', 11 );

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
				var heightOffset = document.querySelector("#header-centered-logo").offsetHeight;
				var subNavigationHeight = document.querySelector("#SubNavigation").offsetHeight;
				var headerCenteredLogo = document.querySelector("#header-centered-logo").offsetTop;
				var heightOffset2 = heightOffset + subNavigationHeight;
				if ( scrollUser >= heightOffset) {
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

/**
 * 
 * Add scustom scripts on Dashboard - JS
 */
if( is_page_template('page-my-account') || is_page_template('edit-update-grant-projects') || is_page_template('edit-document') || is_page_template('page-reporting') ){
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
	if ( !is_user_logged_in() && (is_page_template('page-my-account') || is_page_template('edit-update-grant-projects') || is_page_template('edit-document') || is_page_template('page-reporting') ) ) {

	wp_redirect( '/' );
		exit;
	}
}

/**
 * Unset some custom fields from displaying on grid and in single document : Document Library Pro
 */
add_filter( 'document_library_pro_custom_fields', function( $custom_fields_list) {
    // Delete an unwanted custom field from the custom fields list.
    // unset( $custom_fields_list[ 'doc_author'] );
    unset( $custom_fields_list[ 'document_file'] );
    unset( $custom_fields_list[ 'document_title'] );
    unset( $custom_fields_list[ 'document_body'] );
    //unset( $custom_fields_list[ 'organization_for_library'] );
    unset( $custom_fields_list[ 'award_number_for_library'] );
    unset( $custom_fields_list[ 'available_to_the_public'] );
    unset( $custom_fields_list[ 'approved_by_ovw_specialist'] );
    unset( $custom_fields_list[ 'external_link_for_library'] );
    unset( $custom_fields_list[ 'grant_project_for_library'] );
    unset( $custom_fields_list[ 'grant_programs_for_library'] );
    unset( $custom_fields_list[ 'target_audiences_for_library'] );
    unset( $custom_fields_list[ 'ovw_program_specialists_name'] );
    unset( $custom_fields_list[ 'approved_by_ovw_specialist'] );
    unset( $custom_fields_list[ 'gan_number_for_library'] );
    return $custom_fields_list;
}, 10 );


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
