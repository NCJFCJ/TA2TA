<?php
/*
 * This file has all the functions related to the documents and library pages
 * advanced custom fields
 * document library pro
 */

////////////////////////////////////////////////////////////////////////////////////
// add title to edit event page
// shortcode
////////////////////////////////////////////////////////////////////////////////////
function edit_doc_title() {

	$post_id = isset($_GET['post_id'])? $_GET['post_id'] : NULL;
	
	$title = '';
	
	if( isset($post_id) ) {
		$title = get_the_title($post_id);
	}
	else {
		$title = '*Select an event from the list*';
	}
	
	$title_string = '<h4 class="header-no-margin"> Now editing: <strong>' . $title . '</strong></h4>';
	
	return $title_string;
}
add_shortcode('document_title', 'edit_doc_title');

////////////////////////////////////////////////////////////////////////////////////
// add list of events to edit event page
// shortcode
////////////////////////////////////////////////////////////////////////////////////
function edit_document_list() {

	//$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
	
	$user_id = get_current_user_id();

	$user_org = get_field('organization_for_user', 'user_' . $user_id);
	$list_string = '';
	// query will only pull everything from today and forward
	// This is due to the post type, tribe_events
	// This custom post type was made to behave like this.
	$query = new WP_Query( array(
			'post_type' 		=> 'dlp_document',
			'posts_per_page'	=> -1,
			'orderby' 			=> [
				'date'	=> 'DESC',
				'title'	=> 'ASC'
			]
		) 
	);

	$list_string .='
	<div class="responsive-data-table"><h5 style="text-align: center">Document list ( Click the document title to edit)</h5>
	<table id="responsive-data-table" class="table table-bordered table-hover dt-responsive nowrap" style="width: 100%;">
	<thead>
		<tr>
			<th>
				Title
			</th>
		</tr>
	</thead>
	<tbody>';

	//check
	if ( $query->have_posts() ):

	    while ($query->have_posts()){
			
			$query->the_post();
			
			$current_post_org = get_post_meta(get_the_id(), 'organization_for_library', true);

			if( $current_post_org == $user_org) {
				
				$list_string .= '<tr role="row" class=""><td tabindex="0" class="sorting_1"><a href="'. get_site_url() . '/update-document?action=update-document&post_id='; 
				$list_string .= get_the_id() . '" >' . get_the_title(); 
				$list_string .= '</a></td></tr>'; 
			}

		}

	endif;
	$list_string .= '</tbody>
	</table></div>';

    	wp_reset_query();

	return $list_string;
}
add_shortcode('document_list', 'edit_document_list');

add_filter( 'gform_field_value_org_param', 'populate_with_org_name' );
function populate_with_org_name( $value ) {
	
	$user_id = get_current_user_id();
		
	$org = get_field('organization_for_user', 'user_'.$user_id);
	
    return $org;
}

add_filter( 'gform_field_value_link_param', 'populate_with_org_link' );
function populate_with_org_link( $value ) {
	
	// get post_id using url/permalink
	$post_id = get_post_id_for_organization();
	
	$link = get_post_meta($post_id, 'external_link_for_directory', true);
	
    return $link;
}

add_filter( 'gform_field_value_summary_param', 'populate_with_org_summary' );
function populate_with_org_summary( $value ) {
	
	// get post_id using url/permalink
	$post_id = get_post_id_for_organization();
	
	$summary = get_post_field('post_content', $post_id);	
	
    return $summary;
}

add_filter( 'gform_field_value_logo_param', 'populate_with_org_logo' );
function populate_with_org_logo( $value ) {
			
	// get post_id using url/permalink
	$post_id = get_post_id_for_organization();
	
	$logo = basename(wp_get_attachment_url( get_post_thumbnail_id($post_id)));
	
    return $logo;
}



?>