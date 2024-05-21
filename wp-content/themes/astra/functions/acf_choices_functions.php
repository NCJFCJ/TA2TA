<?php
/**
 * These function are used on the pages that has advanced custom fields, if there is a conflict if they are loaded on page that has facetwp filter.
 */


function acf_admin_enqueue( $hook ) {
	wp_enqueue_script( 'populate-awards', get_stylesheet_directory_uri() . '/assets/js/populate_form_choices.js' );
   
	wp_localize_script( 'populate-awards', 'pa_vars', array(
			'pa_nonce'	=> wp_create_nonce( 'pa_nonce' ), // Create nonce which we later will use to verify AJAX request
			'choices'	=> organization_user_grant_projects()
		)
	);
  }
add_action( 'admin_enqueue_scripts', 'acf_admin_enqueue' );
add_action('wp_enqueue_scripts', 'acf_admin_enqueue');

  // Return awards by grant project
function awards_by_grant_project( $selected_grant_project ) {
	// Verify nonce
	if( !isset( $_POST['pa_nonce'] ) || !wp_verify_nonce( $_POST['pa_nonce'], 'pa_nonce' ) ){
		die('Permission denied');
	}
	// Get grant project var
	$selected_grant_project = $_POST['grant_project'];
	// Get field from options page
	$org_post_id = get_post_id_for_organization();
	$projects = organizations_grant_projects();
	$grant_projects_listerr = $projects[ $org_post_id ];

	$grantproject_data = [];
	foreach( $grant_projects_listerr as $grant_project ){
		if( $grant_project[ 'project_title' ] == $selected_grant_project ){
			$grantproject_data[ 'awards' ] = [ $grant_project[ 'award_number' ] ];
			$grantproject_data[ 'grant_programs' ] = $grant_project[ 'grant_programs' ];
		}
	}
	return wp_send_json($grantproject_data);
	die();
  }
   
add_action( 'wp_ajax_awards_by_grant_project', 'awards_by_grant_project' );
//add_action( 'wp_ajax_nopriv_awards_by_grant_project', 'awards_by_grant_project' );

// add_action( 'wp_ajax_grant_programs', 'awards_by_grant_project' );
// add_action( 'wp_ajax_nopriv_grant_programs', 'awards_by_grant_project' );

// add_action( 'wp_ajax_award_number_for_library', 'awards_by_grant_project' );
// add_action( 'wp_ajax_nopriv_award_number_for_library', 'awards_by_grant_project' );
  
/**
 * Setup the saving of the new/edited document
 */
add_action( 'acfe/form/submit/post/form=submit-a-new-document-old', 'document_submitted', 10, 5 );
function document_submitted( $post_id, $type, $args, $form, $action ){
	/**
	 * Use Document Library Pro Document::class to update document post
	 */
	$file = get_field('document_file');
	$file_url = $file['url'];
	$document = dlp_get_document( $post_id );
	if ( ! $document || ! $file_url ) {
        return;
    }

	$document->set_file_size( $file['filesize'] );
	$file_id = Barn2\Plugin\Document_Library_Pro\Util\Media::attach_file_from_url( $file['url'], $file['id'] );
	$document->set_document_link( 'file', [ 'file_id' => $file_id ] );
	$image_id = Barn2\Plugin\Document_Library_Pro\Util\Media::attach_file_from_url( $file['icon'], $file_id );
	set_post_thumbnail( $file_id, $image_id );
	set_post_thumbnail( $post_id, $image_id );
	$public = get_field( 'available_to_the_public' );
	if(  $public = 'TRUE' ) { 
		wp_set_object_terms( $post_id, "Public", 'doc_categories', true );
	} else {
		wp_set_object_terms( $post_id, "Public", 'doc_categories', false );		
	}

	$org = get_field( 'organization_for_library' );

	if( ! empty( $org  ) ){ 
		wp_set_object_terms( $post_id, $org, 'doc_author', false );
	} 	

}

/**
 * Setting up the saving of the new newsletter
 */
add_action( 'acfe/form/submit/post/form=add-new-newsletter', 'newsletter_submitted', 10, 5 );
function newsletter_submitted( $post_id, $type, $args, $form, $action ){
	$file = get_field('newsletter_file');
	$file_url = $file['url'];
	$image_id = Barn2\Plugin\Document_Library_Pro\Util\Media::attach_file_from_url( $file['icon'], $post_id );
	set_post_thumbnail( $post_id, $image_id );
}

/**
 * 
 */

function acf_load_organization_field_choices( $field ) {
	
    // Reset choices
    $field['choices'] = array();
	$organizations_list = organizations_posts_list();
	$field[ 'none' ] = 'Select Organization';
	foreach( $organizations_list as $organization ){
		$field[ 'choices' ][ $organization ] = $organization;
	}
    // Return the field
    return $field;  
}
add_filter('acf/load_field/name=organization_for_user', 'acf_load_organization_field_choices');

function acf_load_organization_field_for_document_choices( $field ) {
	if( !is_user_logged_in() ){
		wp_redirect( '/ta-login' );
			exit;
	}
	// Reset choices
    $field[ 'choices' ] = array();
	$user_id = get_current_user_id();
	$org = get_field( "organization_for_user", "user_{$user_id}" );
	$field[ 'choices' ][ $org ] = $org;
    // Return the field
    return $field;
}
add_filter('acf/load_field/name=organization_for_library', 'acf_load_organization_field_for_document_choices');

function acf_load_grant_programs_field_choices( $field ) {
    // Reset choices
    $field[ 'choices' ] = array();
	$grant_programs_lister = ta2ta_get_terms_list(get_grant_programs_Obj());

	foreach( $grant_programs_lister as $grant_program ){
		$field[ 'choices' ][ $grant_program ] = $grant_program;
	}
    // Return the field
    return $field; 
}
//add_filter( 'acf/load_field/name=grant_programs', 'acf_load_grant_programs_field_choices' );

// function acf_load_grant_programs_field_document_choices( $fieldd ) {
	
//     // Reset choices
//     $fieldd['choices'] = array();
// 	$org_post_id = get_post_id_for_organization();
// 	$projects = organizations_grant_projects();
// 	$grant_projects = $projects[ $org_post_id ];
// 	$fieldd['choices'][ 'None' ] = 'Select Grant Programs';
// 	$grant_programs_list = [];
// 	foreach( $grant_projects as $grant_project ){
// 		$grant_programs_list = $grant_project[ 'grant_programs' ];
// 	}
// 	foreach( $grant_programs_list as $grant_program ){
// 		$fieldd[ 'choices' ][ $grant_program ] = $grant_program;
// 	}
//     // Return the field
//     return $fieldd;
// }
// add_filter('acf/load_field/name=grant_program', 'acf_load_grant_programs_field_document_choices'); //NOT USEFUL ANYMORE

function acf_load_grant_projects_field_choices( $field ) {

    $field[ 'choices' ] = array();
	//Get the organization's grant projects list
	$org_post_id = get_post_id_for_organization();
	$projects = organizations_grant_projects();
	$grant_projects_list_obj = $projects[$org_post_id];
	// echo '<pre>';
	// //print_r(organizations_grant_projects()[$org_post_id]);
	// print_r($field['name']);
	// echo '</pre>';die();

	if($field['name'] == 'grant_project_for_library'){
		
		$field['choices'][ 'None' ] = 'Select Grant Project';
		foreach( $grant_projects_list_obj as $grant_project ){
			$field[ 'choices' ][ $grant_project[ 'project_title' ] ] = $grant_project[ 'project_title' ];
		}
	}

    // Return the field
    return $field;
}

add_filter( 'acf/load_field/name=grant_project_for_library', 'acf_load_grant_projects_field_choices' );
add_filter( 'acf/load_field/name=grant_projects', 'acf_load_grant_projects_field_choices' );

function acf_load_awards_field_choices( $field ) {
    
    // Reset choices
    $field['choices'] = array();

	$awards_lister = ta2ta_get_terms_list(get_awards_Obj());
	$field['choices'][ 'None' ] = 'Select Award';
	foreach($awards_lister as $award){
		$field['choices'][ $award ] = $award;
	}
	
    // Return the field
    return $field;
    
}
//add_filter('acf/load_field/name=award_number_for_library', 'acf_load_awards_field_choices');

/**
 * Award Numbers for documents
 */
// function acf_load_awards_field_for_document_choices( $fieldg ) {
    
//     // Reset choices
//     $fieldg['choices'] = array();
// 	$org_post_id = get_post_id_for_organization();
// 	$projects = organizations_grant_projects();
// 	$grant_projects_lister = $projects[ $org_post_id ];
// 	$awards_lister = [];
// 	//$selected = 'Stalking Prevention, Awareness, and Resource Center (SPARC)';
// 	foreach( $grant_projects_lister as $grant_project ){
// 		//if($grant_project['project_title'] == $selected){
// 			$awards_lister[] = $grant_project[ 'award_number' ];
// 		//}
// 	}
// 	$fieldg['choices'][ 'None' ] = 'Select Award';
// 	foreach($awards_lister as $award){
// 		$fieldg['choices'] [$award] = $award;
// 	}
//     // Return the field
//     return $fieldg;
// }
// add_filter('acf/load_field/name=awards_numbers', 'acf_load_awards_field_for_document_choices');

function acf_load_target_audiences_field_choices( $field ) {
    
    // Reset choices
    $field['choices'] = array();
	$target_audiences_lister = ta2ta_get_terms_list(get_target_audiences_Obj());
	//$field['choices'][ 'None' ] = 'Check Target Audiences on the list below';
	foreach($target_audiences_lister as $target_audience){
		$field['choices'][ $target_audience ] = $target_audience;
	}
    // Return the field
    return $field;
}
add_filter('acf/load_field/name=target_audiences', 'acf_load_target_audiences_field_choices');

function acf_load_target_audiences_field_for_documents_choices( $field ) {
    
    // Reset choices
    $field['choices'] = array();
	$target_audiences_lister = ta2ta_get_terms_list(get_target_audiences_Obj());
	//$field['choices'][ 'None' ] = 'Check Target Audiences on the list below';
	foreach($target_audiences_lister as $target_audience){
		$field['choices'][ $target_audience ] = $target_audience;
	}
    // Return the field
    return $field;
}

add_filter('acf/load_field/name=target_audiences_for_library', 'acf_load_target_audiences_field_for_documents_choices');

function filter_field( array $field ) : array {

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
   
	  //$field['choices'] = time_consuming_call_to_get_choices();
	  $field['choices'] = organization_user_grant_projects();
   
	}
   
	return $field;
   
}
   
add_filter( "acf/load_field/key=awards_numbers", 'filter_field', 10, 1 );