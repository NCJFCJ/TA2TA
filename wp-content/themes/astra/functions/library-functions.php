<?php
/*
 * This file has all the functions related to the documents and library pages
 * Gravity Forms
 * advanced custom fields
 * document library pro
 */

////////////////////////////////////////////////////////////////////////////////////
// add title to edit event page
// shortcode
////////////////////////////////////////////////////////////////////////////////////
function edit_doc_title() {

	//$post_id = $_GET["post_id"];
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
				
				// $list_string .= '<tr role="row" class=""><td tabindex="0" class="sorting_1"><div class="div-list-button"><a href="'. get_site_url() . '/edit-my-documents-directory/?post_id='; 
				// $list_string .= get_the_id() . '" >' . get_the_title(); 
				// $list_string .= '</a></div></td></tr>'; 
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

////////////////////////////////////////////////////////////////////////////////////
// edit document
// function to populate form
////////////////////////////////////////////////////////////////////////////////////
// add_filter( 'gform_pre_render_10', 'populate_edit_library_form' );
add_filter( 'gform_pre_render_16', 'populate_edit_library_form' );
add_filter( 'gform_pre_validation_16', 'populate_edit_library_form' );
add_filter( 'gform_pre_submission_filter_16', 'populate_edit_library_form' );
add_filter( 'gform_admin_pre_render_16', 'populate_edit_library_form' );
function populate_edit_library_form( $form ) {

	// get from query string
	// $doc_post_id = $_GET["post_id"];
	$doc_post_id = isset($_GET['post_id'])? $_GET['post_id'] : NULL;

	// get post_id using url/permalink
	$org_post_id = get_post_id_for_organization();
   
	foreach ( $form['fields'] as &$field ) {

		if( $field->label == 'Title' ) {
			
			$title = get_the_title( $doc_post_id );	
    		
			$field->defaultValue  = $title;			
		} 
		
		if( $field->label == 'Description' ) {
			
			$description =  get_the_excerpt( $doc_post_id );
    		
			$field->defaultValue = $description;			
		} 
		
      	if( $field->label == 'Should this Resource be available to the public?' ) {
			$public = get_post_meta($doc_post_id, 'available_to_the_public', true);
    		if( $public == '1' || $public == 'TRUE' ) {
				$public = 'Yes';
			}
			elseif ( $public == '0' || $public == 'FALSE') {
				$public = 'No';
			}	
			$field->defaultValue = $public;	
		} 
		
		if( $field->label == "Please list your OVW program specialist's name" ) {
			
			$name = get_post_meta($doc_post_id, 'ovw_program_specialists_name', true);
    		
			$field->defaultValue = $name;			
		} 
		
		if( $field->label == 'Grant Project(s)' ) {
			/**
			 * get projects from their org post
			 * 
			 */
			
			// get post_id using url/permalink
			$org_post_id = get_post_id_for_organization();
	
			//post meta from library
			$ta_project = get_post_meta($doc_post_id, 'grant_project_for_library', true);
			// echo $ta_project . ' : TA Project <br>';
    		$choices = array();
 
  			$repeater_value = get_post_meta($org_post_id, 'grant_projects_for_directory', true);	 
			// echo $repeater_value . '<br>';
  			for ($i=0; $i<$repeater_value; $i++) {
				
    	 		$meta_key = 'grant_projects_for_directory_'.$i.'_project_title';
				// echo $meta_key . '<br>';
				$meta_value = get_post_meta($org_post_id, $meta_key, true);
				// echo $meta_value . '<br>';
		 		if( $meta_value == $ta_project ) {
						$choices[] = array('text'  		=> get_post_meta($org_post_id, $meta_key, true), 
								   'value' 		=> get_post_meta($org_post_id, $meta_key, true),
						       	   'isSelected' => true
						   );  
				} 	 
				else {
					$choices[] = array('text'  		=> get_post_meta($org_post_id, $meta_key, true), 
								   'value' 		=> get_post_meta($org_post_id, $meta_key, true),
						       	   'isSelected' => false
						   );  
				}
  			} 
			$field->placeholder = 'Select a grant project';
			$field->choices = $choices;
			// die();
			unset( $choices );
		}
		
		if( $field->label == 'Target Audience(s)' ) {
			
			$target_audiences = get_post_meta( $doc_post_id, 'target_audiences_for_library', true);
			if(empty($target_audiences)) {
				$target_audiences = array();
			}
			//admin options page
			//$post_id = 12646;
			$target_audiences_list = ta2ta_get_terms_list(get_target_audiences_Obj());
			$choices = array();
			$inputs = array();
  			//$repeater_value = get_post_meta($post_id, 'target_audience', true);	 
  			$repeater_value = count($target_audiences_list);	 
			 			
			$input_id = 1;
			$skip = false;
  			for ($i=0; $i<$repeater_value; $i++) {
				
				$archived_key = 'target_audience_'.$i.'_archived';
				
				// if( get_post_meta($post_id, $archived_key, true) ) {
				// 	continue;
				// }
							
    	 		$meta_key = 'target_audience_'.$i.'_item';
				
				if ( $input_id % 10 == 0 ) {
                	$input_id++;
            	}
				
		 		foreach( $target_audiences as $target_audience) {
					if( $target_audience == $target_audiences_list[$i] ) {
						$choices[] = array('text' 	=> $target_audiences_list[$i],
										   'value' 	=> $target_audiences_list[$i],
										   'isSelected' => true
										  ); 
						$inputs[] = array('label' 	=> $target_audiences_list[$i], 
								  		  'id'		=> "22.{$input_id}",
								    	 );  
						$skip = true;
					}
				}
				if( !$skip ) {
					$choices[] = array('text' 	=> $target_audiences_list[$i], 
								   	   'value' 	=> $target_audiences_list[$i]	
								  	  );   					
				
					$inputs[] = array('label' 	=> $target_audiences_list[$i], 
								      'id'		=> "22.{$input_id}"
								     );  
				}
				$skip = false;
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
///////////////////////////////////////////////////////////////////////////
// chained select for award number (library form)
///////////////////////////////////////////////////////////////////////////
add_filter( 'gform_chained_selects_input_choices_16_35_1', 'gf_edit_grant_projects', 10, 7 );
function gf_edit_grant_projects( $input_choices, $form_id, $field, $input_id, $chain_value, $value, $index ) { 

	// get post_id using url/permalink
	$org_post_id = get_post_id_for_organization();
	// get from query string
	$doc_post_id = $_GET["post_id"];
	
    $choices = array();
 
	//post meta from library
	$ta_project = get_post_meta($doc_post_id, 'grant_project_for_library', true);
	
  	$repeater_value = get_post_meta($org_post_id, 'grant_projects_for_directory', true);	 
	
  	for ($i=0; $i<$repeater_value; $i++) {
				
		$meta_key = 'grant_projects_for_directory_'.$i.'_project_title';
		$meta_value = get_post_meta($org_post_id, $meta_key, true);

		if( $meta_value == $ta_project ) {
				$choices[] = array('text'  		=> get_post_meta($org_post_id, $meta_key, true), 
								   'value' 		=> get_post_meta($org_post_id, $meta_key, true),
						       	   'isSelected' => true
								  );  
		 } 	 
		 else {
				$choices[] = array('text'  		=> get_post_meta($org_post_id, $meta_key, true), 
								   'value' 		=> get_post_meta($org_post_id, $meta_key, true),
						       	   'isSelected' => false
								  );  
		 }  	 									
  	}      

    return $choices;
}


add_filter( 'gform_chained_selects_input_choices_16_35_2', 'gf_edit_award_number', 10, 7 );
function gf_edit_award_number( $input_choices, $form_id, $field, $input_id, $chain_value, $value, $index ) {
  
	// get post_id using url/permalink
	$org_post_id = get_post_id_for_organization();
    // get from query string
	$doc_post_id = $_GET["post_id"];
	
    $choices = array();
 
    $selected_project = $chain_value[ "{$field->id}.1" ];
    if( ! $selected_project ) {
        return $input_choices;
    } 
    
	$repeater_value = get_post_meta($org_post_id, 'grant_projects_for_directory', true);	 
	//post meta from library
	$award_number = get_post_meta($doc_post_id, 'award_number_for_library', true);	
	
  	for ($i=0; $i<$repeater_value; $i++) {
				
    	$meta_key = 'grant_projects_for_directory_'.$i.'_project_title';
			
		$project_title = get_post_meta($org_post_id, $meta_key, true);
			
		if($project_title == $selected_project){
			
			$sub_repeater = 'grant_projects_for_directory_'.$i.'_list_of_award_numbers';
			
			$sub_repeater_value = get_post_meta($org_post_id, $sub_repeater, true);
			
			for ($sub_i=0; $sub_i<$sub_repeater_value; $sub_i++) {
				
				$sub_meta_key = 'grant_projects_for_directory_'.$i.'_list_of_award_numbers_'.$sub_i.'_award_number';
				$sub_meta_value = get_post_meta($org_post_id, $sub_meta_key, true);

				if( $sub_meta_value == $award_number ) {
					$choices[] = array('text'  		=> $sub_meta_value, 
									   'value' 		=> $sub_meta_value,
									   'isSelected' => true
									  );  
		 		}	 
		 		else {
					$choices[] = array('text'  		=> $sub_meta_value, 
									   'value' 		=> $sub_meta_value,
									   'isSelected' => false
									  );  
		 		}  		
  			}
		}
				
	}
	
	if( $field->label == 'Select Grant Program(s)' ) {
			
		$grant_programs = get_post_meta( $doc_post_id, 'grant_programs', true);
		if(empty($grant_programs)) {
			$grant_programs = array();
		}
		//admin options page
		$projects = organizations_grant_projects()[$org_post_id];
		if(is_array($projects)){
			foreach($projects as $project){
				if($project == $selected_project){
					$grant_programs = $project['grant_programs'];
					foreach($grant_programs as $grant_program){
						$choices[] = array('text'  	=> $grant_program,
									'value' 		=> $grant_program,
									'isSelected' => true
									);
					}	  
				} 
				//else {
				//		$choices[] = array('text'  		=> $awards_list[$i], 
				//						'value' 		=> $meta_key,
				//						'isSelected' => false
				//						);  
				//} 									
			}
		} else {
			$grant_programs = $project[0]['grant_programs'];
			$choices[] = array(	'text'  	=> $awards_list,
								'value' => $awards_list,
								'isSelected' => true
										);  
		}

		// $grant_programs_list = ta2ta_get_terms_list(get_grant_programs_Obj());
		// $choices = array();
		$inputs = array();
		  //	$repeater_value = get_post_meta($post_id, 'target_audience', true);
		  $repeater_value = count($grant_programs_list);	 
					 
		$input_id = 1;
		$skip = false;
		  for ($i=0; $i<$repeater_value; $i++) {
			
			$archived_key = 'grant_programs_'.$i.'_archived';
			
			// if( get_post_meta($post_id, $archived_key, true) ) {
			// 	continue;
			// }
						
			 $meta_key = 'grant_programs_'.$i.'_item';
			
			if ( $input_id % 10 == 0 ) {
				$input_id++;
			}
			
			 foreach( $grant_programs as $grant_program) {
				if( $grant_program == $grant_programs_list[$i] ) {
					$choices[] = array('text' 	=> $grant_programs_list[$i],
									   'value' 	=> $grant_programs_list[$i],
									   'isSelected' => true
									  ); 
					$inputs[] = array('label' 	=> $grant_programs_list[$i], 
										'id'		=> "36.{$input_id}",
									 );  
					$skip = true;
				}
			}
			if( !$skip ) {
				$choices[] = array('text' 	=> $grant_programs_list[$i], 
									  'value' 	=> $grant_programs_list[$i]	
									);   					
			
				$inputs[] = array('label' 	=> $grant_programs_list[$i], 
								  'id'		=> "36{$input_id}"
								 );  
			}
			$skip = false;
			 $input_id++;
		  } 
	
		$field->choices = $choices;	
		$field->inputs = $inputs;	
		
		unset( $choices );
		unset( $inputs );
	}
 
    return $choices;
}

////////////////////////////////////////////////////////////////////////////////////
// update document
// function to update document after edit form is submitted
////////////////////////////////////////////////////////////////////////////////////
add_action( 'gform_after_submission_16', 'get_document_update_form', 10, 2 );
function get_document_update_form( $entry, $form ) {
	
	//get from query string
	$doc_post_id = $_GET["post_id"];
	
	$page_status = rgar( $entry, '24' );
	
	if( $page_status == "Edit" ) {
		
		// 20 - input for title
		$title_updated = rgar( $entry, '20' );
		$title_current = get_the_title( $doc_post_id );
		if( $title_updated != $title_current ) {
			wp_update_post(
    			array (
        			'ID'         => $doc_post_id,
        			'post_title' => $title_updated
    			)
			);		
		}  
		
		// 21 - input for contenct
		$content_updated = rgar( $entry, '21' );
		$content_current = get_the_content( $doc_post_id );
		if( $content_updated != $content_current ) {
			wp_update_post(
    			array(
      				'ID'           => $doc_post_id,//the ID of the Post
      				'post_content' => $content_updated
  				)
			);		
		} 
		
		// 23 - input for ta project
		$public_updated = rgar( $entry, '23' );
		if( $public_updated == 'Yes' || $public_updated == '1' ) {
			$public_updated = 'TRUE';
			wp_set_object_terms( $doc_post_id, "Public", 'doc_categories', false );
		}
		elseif ( $public_updated == 'No') {
			$public_updated = 'FALSE';
			wp_set_object_terms( $doc_post_id, "TA Providers Only", 'doc_categories', false );		
		}	
		$public_current = get_post_meta( $doc_post_id, 'available_to_the_public', true );

	  	if ( $public_updated != $public_current ) {
			update_post_meta( $doc_post_id, 'available_to_the_public', $public_updated );
	  	}
		
		// 17 - input for specialist's name
		$name_updated = rgar( $entry, '17' );
		$name_current = get_post_meta( $doc_post_id, 'ovw_program_specialists_name', true );

	  	if ( $name_updated != $name_current ) {
			update_post_meta( $doc_post_id, 'ovw_program_specialists_name', $name_updated );
	  	}
		
		  
		// 33 - award number
		$award_number_updated = rgar( $entry, '33' );
		$award_number_current = get_post_meta( $doc_post_id, 'award_number_for_library', true );
		
		if ( $award_number_updated != $award_number_current ) {
			update_post_meta( $doc_post_id, 'award_number_for_library', $award_number_updated );
		}

		// Get field object.
		$grant_programs_form = GFAPI::get_field( $form, 30 );

		// Get a comma separated list of checkboxes checked
		$grant_programs_checked = $grant_programs_form->get_value_export( $entry );

		$grant_programs_updated = explode(", ", $grant_programs_checked);;
		$grant_programs_current = get_post_meta( $doc_post_id, 'grant_project_for_library', true );

		if ( $grant_programs_updated != $grant_programs_current ) {
			update_post_meta( $doc_post_id, 'grant_programs', $grant_programs_updated );
		}

 		// Get field object.
    	$target_audience_form = GFAPI::get_field( $form, 22 );
		   
    	// Get a comma separated list of checkboxes checked
    	$target_audience_checked = $target_audience_form->get_value_export( $entry );
    	    	
        // Convert to serialized array.
    	$target_audience_updated = explode(", ", $target_audience_checked);
		
		$target_audience_current = get_post_meta( $doc_post_id, 'target_audiences_for_library', true );
    	if( $target_audience_updated !=  $target_audience_current ) {	
			// Replace my_custom_field_key with your custom field meta key.
    		update_post_meta( $doc_post_id, 'target_audiences_for_library', $target_audience_updated );		
		}    	
		
	}
	elseif( $page_status == "Archive" ) {
		// 180 is the id number for Archive, set to false to replace current category (event type)
		wp_set_object_terms( $doc_post_id, "Archived", 'doc_categories', false );
		update_post_meta( $doc_post_id, 'available_to_the_public', '' );
	}
	elseif( $page_status == "Delete" ) {
		wp_trash_post( $doc_post_id );
	}
}

////////////////////////////////////////////////////////////////////////////////////////////////////
// library form function, dynamically populate
// /////////////////////////////////////////////////////////////////////////////////////////////////
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

///////////////////////////////////////////////////////////////////////////
// chained select for award number (library form)
///////////////////////////////////////////////////////////////////////////
add_filter( 'gform_chained_selects_input_choices_10_18_1', 'gf_populate_grant_projects', 10, 7 );
function gf_populate_grant_projects( $input_choices, $form_id, $field, $input_id, $chain_value, $value, $index ) { 
	//$input_choices = gf_apply_filters( 'gform_chained_selects_input_choices', array( $this->formId, $this->id, $index ), $input_choices, $this->formId, $this, $input_id, $full_chain_value, $value, $index );
			

	// get post_id using url/permalink
	$post_id = get_post_id_for_organization();
	
	
    $choices = array();
 
  	$repeater_value = get_post_meta($post_id, 'grant_projects_for_directory', true);	 
			 
  	for ($i=0; $i<$repeater_value; $i++) {
				
    	 $meta_key = 'grant_projects_for_directory_'.$i.'_project_title';
			
		 $choices[] = array('text' => get_post_meta($post_id, $meta_key, true), 
							'value' => get_post_meta($post_id, $meta_key, true),
						    'isSelected' => false
						   );   	 									
  	}      
  
 
    return $choices;
}

add_filter( 'gform_chained_selects_input_choices_10_18_2', 'gf_populate_award_number', 10, 7 );
function gf_populate_award_number( $input_choices, $form_id, $field, $input_id, $chain_value, $value, $index ) {
  
	// get post_id using url/permalink
	$post_id = get_post_id_for_organization();
   
    $choices = array();
 
    $selected_project = $chain_value[ "{$field->id}.1" ];
    if( ! $selected_project ) {
        return $input_choices;
    } 
    
	$repeater_value = get_post_meta($post_id, 'grant_projects_for_directory', true);	 
	
  	for ($i=0; $i<$repeater_value; $i++) {
				
    	$meta_key = 'grant_projects_for_directory_'.$i.'_project_title';
			
		$project_title = get_post_meta($post_id, $meta_key, true);
			
		if($project_title == $selected_project){
			
			$sub_repeater = 'grant_projects_for_directory_'.$i.'_list_of_award_numbers';
			
			$sub_repeater_value = get_post_meta($post_id, $sub_repeater, true);
			
			for ($sub_i=0; $sub_i<$sub_repeater_value; $sub_i++) {
				
			$sub_meta_key = 'grant_projects_for_directory_'.$i.'_list_of_award_numbers_'.$sub_i.'_award_number';
				
			$choices[] = array('text' => get_post_meta($post_id, $sub_meta_key, true), 
							   'value' => get_post_meta($post_id, $sub_meta_key, true),
							  );
  			}   
		}
				
	}			
 
    return $choices;
}

?>