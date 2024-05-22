<?php
/**
 * These function are used on the pages that has advanced custom fields forms.
 */

function add_more_js() {
	acf_localize_data(array( 'pa_nonce'	=> wp_create_nonce( 'pa_nonce' ), 'ta2ta_org_data' => organization_user_grant_projects() ));
	wp_enqueue_script( 'populate-awards', get_stylesheet_directory_uri() . '/assets/js/populate_form_choices.js' );
}
//add_action( 'admin_enqueue_scripts', 'add_more_js' );
add_action('wp_enqueue_scripts', 'add_more_js');
  
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
	if( !empty($file) && $file != ''){
		$file_url = $file['url'];
		$image_id = Barn2\Plugin\Document_Library_Pro\Util\Media::attach_file_from_url( 'https://test.ta2ta.org/wp-content/uploads/2024/04/newsletter-placeholder.png', $post_id );
		set_post_thumbnail( $post_id, $image_id );
		?><script>var image_id = <?php echo $image_id; ?></script><?php
	} else {
		set_post_thumbnail( $post_id, 39929 ); // IMG 1 = 39929  IMG 2 = 39932
		?><script>var image_id = <?php echo $image_id; ?></script><?php
	}

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
	// if( !is_user_logged_in() ){
	// 	wp_redirect( '/ta-login' );
	// 		exit;
	// }
	// Reset choices
    $field[ 'choices' ] = array();
	$user_id = get_current_user_id();
	$org = get_field( "organization_for_user", "user_{$user_id}" );
	$field[ 'choices' ][ $org ] = $org;
    // Return the field
    return $field;
}
add_filter('acf/load_field/name=organization_for_library', 'acf_load_organization_field_for_document_choices');

function acf_load_grant_projects_field_choices( $field ) {

    $field[ 'choices' ] = array();
	//Get the organization's grant projects list
	$org_post_id = get_post_id_for_organization();
	$projects = organizations_grant_projects();
	$grant_projects_list_obj = $projects[$org_post_id];

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

	$awards_lister = organization_user_grant_project_grant_programs('', '', 'awards');
	$field['choices'][ 'None' ] = 'Select Award';
	foreach($awards_lister as $award){
		$field['choices'][ $award ] = $award;
	}
	
    // Return the field
    return $field;
    
}
add_filter('acf/load_field/name=award_number_for_library', 'acf_load_awards_field_choices');


function acf_load_grant_programs_field_choices( $field ) {
	//REMEMBER THIS MIGHT VURNERABLE TO SQL INJECTION 
	$post_id = $_GET['post_id'] ?? 0;
    $field[ 'choices' ] = array();
	if($post_id != 0){
		$selected_award = get_post_meta( $post_id , 'award_number_for_library', true );
		$selected_grant_programs = get_post_meta( $post_id , 'grant_programs_for_library', true );
		$grant_programs_lister = organization_user_grant_project_grant_programs( $post_id, $selected_award, 'grant_programs' );
		foreach( $grant_programs_lister as $grant_program ){
			$field[ 'choices' ][ $grant_program ] = $grant_program;
		}
	} else {
		$grant_programs_lister = get_field( 'grant_program', 'options', true );
		foreach( $grant_programs_lister as $grant_program ){
			$field[ 'choices' ][ $grant_program['item'] ] = $grant_program['item'];
		}
	}
    // Return the field
    return $field;
}
add_filter( 'acf/load_field/name=grant_programs_for_library', 'acf_load_grant_programs_field_choices' );

function acf_load_directory_grant_programs_field_choices( $field ) {
	//REMEMBER THIS MIGHT VURNERABLE TO SQL INJECTION 
    $field[ 'choices' ] = array();
	$grant_programs_lister = get_field( 'grant_program', 'options', true );
	foreach( $grant_programs_lister as $grant_program ){
		$field[ 'choices' ][ $grant_program['item'] ] = $grant_program['item'];
	}
    // Return the field
    return $field;
}
add_filter( 'acf/load_field/name=grant_programs', 'acf_load_directory_grant_programs_field_choices' );


function acf_load_target_audiences_field_choices( $field ) {
    
    // Reset choices
    $field['choices'] = array();
	$target_audiences_lister = get_field( 'target_audiences', 'options', true );//ta2ta_get_terms_list(get_target_audiences_Obj());
	//$field['choices'][ 'None' ] = 'Check Target Audiences on the list below';
	foreach($target_audiences_lister as $target_audience){
		$field['choices'][ $target_audience['item'] ] = $target_audience['item'];
	}
    // Return the field
    return $field;
}
add_filter('acf/load_field/name=target_audiences', 'acf_load_target_audiences_field_choices');

function acf_load_target_audiences_field_for_documents_choices( $field ) {
    
    // Reset choices
    $field['choices'] = array();
	$target_audiences_options = get_field( 'target_audience', 'options', true );
	foreach( $target_audiences_options as $t ){
		$field['choices'][ $t['item'] ] = $t['item'];
	}
    return $field;
}
add_filter('acf/load_field/name=target_audiences_for_library', 'acf_load_target_audiences_field_for_documents_choices');