<?php 
/**
 * Title: Display Grant Project Form
 * Slug: twentytwentyfour/display-grant-project-form
 * Categories: 
 * Keywords: grant-project
 * Block Types: layout
 */
?>

<?php
	
	if( is_user_logged_in() ) {
		
		$post_id = get_post_id_for_organization();
		
		acf_form_head();
            
		acf_form(array(
			'post_id' 						=> $post_id,
			'_thumbnail_id' 				=> true,
			'external_link_for_directory'	=> false,
			'submit_value' 					=> __('Update')
		)); 
	}	
?>
