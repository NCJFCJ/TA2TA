<?php
/*
 * This file has all the functions related to the calendar page
 * Gravity Forms
 * 
 */
////////////////////////////////////////////////////////////////////////////////////
// create event
// function to populate form
////////////////////////////////////////////////////////////////////////////////////
add_filter( 'gform_pre_render_14', 'populate_calendar_form' );
add_filter( 'gform_pre_validation_14', 'populate_calendar_form' );
add_filter( 'gform_pre_submission_filter_14', 'populate_calendar_form' );
add_filter( 'gform_admin_pre_render_14', 'populate_calendar_form' );
function populate_calendar_form( $form ) {
  
	foreach ( $form['fields'] as &$field ) {
 
      
		if( $field->label == 'TA Project' ) {
			
			// get post_id using url/permalink
			$post_id = get_post_id_for_organization();
	
    		$choices = array();
 
  			$repeater_value = get_post_meta($post_id, 'grant_projects_for_directory', true);	 
			 
  			for ($i=0; $i<$repeater_value; $i++) {
				
    	 		$meta_key = 'grant_projects_for_directory_'.$i.'_project_title';
			
		 		$choices[] = array('text'  		=> get_post_meta($post_id, $meta_key, true), 
								   'value' 		=> get_post_meta($post_id, $meta_key, true),
						       	   'isSelected' => false
						   );   	 									
  			} 
			$field->placeholder = 'Select a grant project';
			$field->choices = $choices;
			
			unset( $choices );
		} 
		
		if( $field->label == 'Event Type' ) {
			
			//admin options page
			$post_id = 12646;
			
			$choices = array();
			
  			$repeater_value = get_post_meta($post_id, 'event_type', true);	 
			 
			$input_id = 1;
  			for ($i=0; $i<$repeater_value; $i++) {
				
				$archived_key = 'event_type_'.$i.'_archived';
				
				if( get_post_meta($post_id, $archived_key, true) ) {
					continue;
				}
					
				if ( $input_id % 10 == 0 ) {
                	$input_id++;
            	}
				
    	 		$meta_key = 'event_type_'.$i.'_item';
			
		 		$choices[] = array('text' 	=> get_post_meta($post_id, $meta_key, true), 
								   'value' 	=> get_post_meta($post_id, $meta_key, true),
					       	   	   'isSelected' => false								   	
						     );
 
  			} 
		
			$field->placeholder = 'Select an event type';
			$field->choices = $choices;	

			
			unset( $choices );
		}
		
		if( $field->label == 'Topic Area' ) {
			
			//admin options page
			$post_id = 12646;
			
			$choices = array();
			$inputs = array();
			
  			$repeater_value = get_post_meta($post_id, 'topic_area', true);	 
			 
			$input_id = 1;
  			for ($i=0; $i<$repeater_value; $i++) {
				
				$archived_key = 'topic_area_'.$i.'_archived';
				
				if( get_post_meta($post_id, $archived_key, true) ) {
					continue;
				}
					
				if ( $input_id % 10 == 0 ) {
                	$input_id++;
            	}
				
    	 		$meta_key = 'topic_area_'.$i.'_item';
			
		 		$choices[] = array('text' 	=> get_post_meta($post_id, $meta_key, true), 
								   'value' 	=> get_post_meta($post_id, $meta_key, true),
								   	
						     );
				$inputs[] = array('text' 	=> get_post_meta($post_id, $meta_key, true), 
								  'value' 	=> get_post_meta($post_id, $meta_key, true),	
								  'id'		=> "21.{$input_id}"
						     );  
 				$input_id++;
 
  			} 
		
			$field->choices = $choices;	
			$field->inputs = $inputs;	
			
			unset( $choices );
			unset( $inputs );
		}
		
		if( $field->label == 'Grant Programs' ) {
			
			$value = rgpost( 'input_12' ); // replace "1" with your field ID
			
			//admin options page
			$post_id = get_post_id_for_organization();
			
			$choices = array();
			$inputs = array();
			
  			$repeater_value = get_post_meta($post_id, 'grant_projects_for_directory', true);				 
			
			
  			for ($i=0; $i<$repeater_value; $i++) {
				
				$archived_key = 'grant_projects_for_directory_'.$i.'_archived';
				
				if( get_post_meta($post_id, $archived_key, true) ) {
					continue;
				}
							
    	 		$meta_key = 'grant_projects_for_directory_'.$i.'_project_title';
			
				if( get_post_meta($post_id, $meta_key, true) == $value ) {
					
					$meta_key_programs = 'grant_projects_for_directory_'.$i.'_grant_programs';
					
					
					$grant_programs = get_post_meta($post_id, $meta_key_programs, true);					
					
					$input_id = 1;
					foreach($grant_programs as $program){
						
						if ( $input_id % 10 == 0 ) {
                			$input_id++;
            			}	
						
						$choices[] = array('text' 	=> $program, 
								   		   'value' 	=> $program,
										   'isSelected' => true
						     ); 
						$inputs[] = array('text' 	=> get_post_meta($post_id, $meta_key, true), 
								  'value' 	=> get_post_meta($post_id, $meta_key, true),	
								  'id'		=> "23.{$input_id}"
						     ); 
						
 						$input_id++;
					}
				}
		 							
 
  			} 
		
			$field->choices = $choices;	
			$field->inputs = $inputs;	
			
			unset( $choices );
			unset( $inputs );
		}
		
		if( $field->label == 'Target Audience' ) {			
			
			//admin options page
			$post_id = 12646;
			
			$choices = array();
			$inputs = array();
  			$repeater_value = get_post_meta($post_id, 'target_audience', true);	 
			 
			$input_id = 1;
  			for ($i=0; $i<$repeater_value; $i++) {
				
				$archived_key = 'target_audience_'.$i.'_archived';
				
				if( get_post_meta($post_id, $archived_key, true) ) {
					continue;
				}
							
    	 		$meta_key = 'target_audience_'.$i.'_item';
				
				if ( $input_id % 10 == 0 ) {
                	$input_id++;
            	}
		
		 		$choices[] = array('text' 	=> get_post_meta($post_id, $meta_key, true), 
								   'value' 	=> get_post_meta($post_id, $meta_key, true),		    
						     );   					
				
				
				$inputs[] = array('text' 	=> get_post_meta($post_id, $meta_key, true), 
								  'value' 	=> get_post_meta($post_id, $meta_key, true),	
								  'id'		=> "25.{$input_id}"
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
// after event is created
// function to update and set event after post is created
// update page template and content added at the end of this function
////////////////////////////////////////////////////////////////////////////////////

add_action( 'gform_advancedpostcreation_post_after_creation_14', 'update_event_post_checkboxes', 10, 4 );

function update_event_post_checkboxes( $post_id, $feed, $entry, $form ) {
    
	$topic_area_field_id = 21;
	$grant_programs_field_id = 23;
 	$target_audiences_field_id = 25;
	
	// Get field object.
    $topic_area_field = GFAPI::get_field( $form, $topic_area_field_id );
  
    if( isset($topic_area_field) ) {	
	
        // Get a comma separated list of checkboxes checked
        $checked = $topic_area_field->get_value_export( $entry );
  
        // Convert to array.
        $values = str_replace(", ", "|", $checked);
  
		// Replace my_custom_field_key with your custom field meta key.
    	update_post_meta( $post_id, '_ecp_custom_15', $values );
				
		unset( $values );
		
        // Convert to array.
        $values = explode(", ", $checked);
			
		foreach( $values as $value ) {    
    		add_post_meta( $post_id, '__ecp_custom_15', $value );
		}
		
		unset( $checked );
		unset( $values );
	}
	
	// Get field object.
    $grant_programs_field = GFAPI::get_field( $form, $grant_programs_field_id );
  
    if( isset($grant_programs_field) ) {	
	
        // Get a comma separated list of checkboxes checked
        $checked = $grant_programs_field->get_value_export( $entry );
  
        // Convert to array.
        $values = str_replace(", ", "|", $checked);
  
		// Replace my_custom_field_key with your custom field meta key.
    	update_post_meta( $post_id, '_ecp_custom_17', $values );
				
		unset( $values );
		
        // Convert to array.
        $values = explode(", ", $checked);
			
		foreach( $values as $value ) {    
    		add_post_meta( $post_id, '__ecp_custom_17', $value );
		}
		
		unset( $checked );
		unset( $values );
    }
	
	// Get field object.
    $target_audiences_field = GFAPI::get_field( $form, $target_audiences_field_id );
  
    if( isset($target_audiences_field) ) {
	
        // Get a comma separated list of checkboxes checked
        $checked = $target_audiences_field->get_value_export( $entry );
  
        $values = str_replace(", ", "|", $checked);
		
		// Replace my_custom_field_key with your custom field meta key.
    	update_post_meta( $post_id, '_ecp_custom_16', $values );
		
		unset( $values );
		
        // Convert to array.
        $values = explode(", ", $checked);


		foreach( $values as $value ) {    
    		add_post_meta( $post_id, '__ecp_custom_16', $value );
		}
		
		unset( $checked );
		unset( $values );
    }
 
	$start_date = rgar( $entry, 4 );
	
	$start_time = date('H:i:s', strtotime( rgar( $entry, 5 ) ));
	
	$start_date_time = $start_date . " " . $start_time;
  
	if( isset($start_date_time) ) {
		update_post_meta( $post_id, '_EventStartDate', $start_date_time );
		
		$tz = rgar( $entry, 8 );
		$dt = new DateTime($start_date_time, new DateTimeZone($tz)); 
		$dt->setTimezone(new DateTimeZone("UTC")); //first argument "must" be a string
		
		update_post_meta( $post_id, '_EventStartDateUTC', $dt->format('Y-m-d H:i:s') );

	}  
	
	$end_date = rgar( $entry, 9 );
	
	$end_time = date('H:i:s', strtotime( rgar( $entry, 10 ) ));
	
	if( !isset( $end_date ) || (strtotime($end_date) < strtotime($start_date) )) {
		$end_date = $start_date;
	}
	
	if( !isset( $end_time ) || (strtotime($end_time) < strtotime($start_time) )) {
		$end_time = $start_time;
	}
	
	$end_date_time = $end_date . " " . $end_time;
	if( isset($end_date_time) ) {
		update_post_meta( $post_id, '_EventEndDate', $end_date_time );
		
		$tz = rgar( $entry, 8 );
		$dt = new DateTime($end_date_time, new DateTimeZone($tz)); 
		$dt->setTimezone(new DateTimeZone("UTC")); //first argument "must" be a string
		update_post_meta( $post_id, '_EventEndDateUTC', $dt->format('Y-m-d H:i:s') );
	}  
	
	// update post attributes
	$content = rgar( $entry, 16 );		
	wp_update_post(
    	array(
      			'ID'           	=> $post_id,//the ID of the Post
      			'post_content' 	=> $content,
				'page_template' => 'single-tribe_events'
  		)
	);		

	// update post template
	update_post_meta( $post_id, '_wp_page_template', 'single-tribe_events' );
}

////////////////////////////////////////////////////////////////////////////////////
// add title to edit event page
// shortcode
////////////////////////////////////////////////////////////////////////////////////
function edit_event_title() {

	//$post_id = $_GET["post_id"];

	$post_id = isset($_GET['post_id']) ? $_GET['post_id'] : NULL;
	
	$title = '';
	
	if( isset($post_id) && !empty($post_id) ) {
		$title = get_the_title($post_id);
	}
	else {
		$title = '*Select an event from the list*';
	}
	
	$title_string = '<h4 class="header-no-margin"> Now editing: <strong><div id="selected_event_to_edit">' . $title . '</div></strong></h4>';
	
	return $title_string;
}
add_shortcode('event_title', 'edit_event_title');

////////////////////////////////////////////////////////////////////////////////////
// add list of events to edit event page
// shortcode
////////////////////////////////////////////////////////////////////////////////////
function edit_event_list() {
	$user_id = get_current_user_id();
		
	$user_org = get_field('organization_for_user', 'user_'.$user_id);
	
	$list_string = '';
	
	// query will only pull everything from today and forward
	// This is due to the post type, tribe_events
	// This custom post type was made to behave like this.
	$posts = new WP_Query( array(
			'posts_per_page'	=> -1,		
			'post_type' 		=> 'tribe_events',
			'orderby' 			=> [
				'date'	=> 'DESC' ,
				'title'	=> 'ASC'
			]
		) 
	);
	
	//check
	if ( $posts->have_posts() ):
    	//loop
	    while ($posts->have_posts()): $posts->the_post();
			$current_post_org = get_post_meta(get_the_id(), '_ecp_custom_27', true);
	
			if( $current_post_org == $user_org) {				
				$timezone = get_post_meta(get_the_id(), '_EventTimezone', true);
				$start_date_time = new DateTime(get_post_meta(get_the_id(), '_EventStartDate', true)); 
				$end_date_time = new DateTime(get_post_meta(get_the_id(), '_EventEndDate', true)); 
				
				$list_string .= '<div class="div-list-button"><a href="'.get_site_url().'/edit-event/?post_id='; 
				$list_string .= get_the_id() . '" >' . get_the_title(); 
				$list_string .= '</a><br />'; 
				$list_string .= $start_date_time->format('F d, Y @ h:i a') . ' - ';
				if( $start_date_time->format('F d') == $end_date_time->format('F d') ) {
					$list_string .= $end_date_time->format('h:i a');
				}
				else {
					$list_string .= $end_date_time->format('F d, Y @ h:i a');
				}

				$list_string .= ' ' . $timezone .'</div>';
			}	
	
    	endwhile;
    	wp_reset_postdata();

	endif;
 
	$list_string .= '';
	
	return $list_string;
}
add_shortcode('event_list', 'edit_event_list');

////////////////////////////////////////////////////////////////////////////////////
// edit event
// function to populate form
////////////////////////////////////////////////////////////////////////////////////
add_filter( 'gform_pre_render_15', 'populate_edit_calendar_form' );
add_filter( 'gform_pre_validation_15', 'populate_edit_calendar_form' );
add_filter( 'gform_pre_submission_filter_15', 'populate_edit_calendar_form' );
add_filter( 'gform_admin_pre_render_15', 'populate_edit_calendar_form' );
function populate_edit_calendar_form( $form ) { 

	// get from query string

	$event_post_id = isset($_GET['post_id'])? $_GET['post_id'] : NULL;
	  
	foreach ( $form['fields'] as &$field ) {
 
		if( $field->label == 'Title' ) {
			
			$title = get_the_title( $event_post_id );	
    		
			$field->defaultValue  = $title;			
		} 
		
		if( $field->label == 'Description/Summary' ) {
			
			$description = get_the_excerpt( $event_post_id );	
    		
			$field->defaultValue  = $description;			
		} 
      
		if( $field->label == 'TA Project' ) {

			// get post_id using url/permalink
			$org_post_id = get_post_id_for_organization();	
	
			$ta_project = get_post_meta($event_post_id, '_ecp_custom_3', true);
			
    		$choices = array();
 
  			$repeater_value = get_post_meta($org_post_id, 'grant_projects_for_directory', true);	 
			 
  			for ($i=0; $i<$repeater_value; $i++) {
				
    	 		$meta_key = 'grant_projects_for_directory_'.$i.'_project_title';
			
		 		if( get_post_meta($org_post_id, $meta_key, true) == $ta_project ) {
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
			
			unset( $choices );
		} 
		
		if( $field->label == 'Event Type' ) {
			
			//admin options page
			$post_id = 12646;
			
			$event_type = get_the_terms( $event_post_id, 'tribe_events_cat' ); 
			
			$choices = array();
			
  			$repeater_value = get_post_meta($post_id, 'event_type', true);	 
			 
  			for ($i=0; $i<$repeater_value; $i++) {
				
				$archived_key = 'event_type_'.$i.'_archived';
				
				if( get_post_meta($post_id, $archived_key, true) ) {
					continue;
				}
				
    	 		$meta_key = 'event_type_'.$i.'_item';

		 		if( get_post_meta($post_id, $meta_key, true) == isset($event_type[0]->name )) {
						$choices[] = array('text'  		=> get_post_meta($post_id, $meta_key, true),
										   'value' 		=> get_post_meta($post_id, $meta_key, true),
										   'isSelected' => true
										  );  
				} 	 
				else {
					$choices[] = array('text'  		=> get_post_meta($post_id, $meta_key, true),
									   'value' 		=> get_post_meta($post_id, $meta_key, true),
									   'isSelected' => false
									  );  
				}
  			} 
			
			if( isset($event_type[0]->name) == 'Archived') {
						$choices[] = array('text'  		=> $event_type[0]->name,
										   'value' 		=> $event_type[0]->name,
										   'isSelected' => true
										  );  
				} 

			$field->placeholder = 'Select an event type';
			$field->choices = $choices;	
			
			unset( $choices );
		}
		if( $field->label == 'Start Date' ) {
			$start_date = get_post_meta($event_post_id, '_EventStartDate', true);	
			
			$field->defaultValue = date( 'Y-m-d', strtotime( $start_date ));
		}
		if( $field->label == 'End Date' ) {
			$end_date = get_post_meta($event_post_id, '_EventEndDate', true);	
			
			$field->defaultValue = date( 'Y-m-d', strtotime( $end_date ));
		}
			if( $field->label == 'Start Time' ) {
			$start_time = get_post_meta($event_post_id, '_EventStartDate', true);	
			
			$field->defaultValue = date( 'h:i:s a', strtotime( $start_time ));
		}
		if( $field->label == 'End Time' ) {
			$end_time = get_post_meta($event_post_id, '_EventEndDate', true);	
			
			$field->defaultValue = date( 'h:i:s a', strtotime( $end_time ));
		}
		if( $field->label == 'OVW Approved' ) {
			$event_approved = get_post_meta($event_post_id, '_ecp_custom_5', true);	
			
			$field->defaultValue = $event_approved;
		}
		if( $field->label == 'Is the event virtual?' ) {
			$event_virtual = get_post_meta($event_post_id, '_ecp_custom_2', true);	
			
			$field->defaultValue = $event_virtual;
		}
		if( $field->label == 'Registration Type' ) {
			$registration_type = get_post_meta($event_post_id, '_ecp_custom_7', true);	
			
			$field->defaultValue = $registration_type;
		}
		if( $field->label == 'Event Webpage URL' ) {			
			$event_url = get_post_meta( $event_post_id, '_ecp_custom_6', true );				
			
			$field->defaultValue = $event_url;
		}
		
		if( $field->label == 'Registration URL' ) {			
			$registration_url = get_post_meta( $event_post_id, '_ecp_custom_8', true );				
			
			$field->defaultValue = $registration_url;
		}
		if( $field->label == 'Time Zone' ) {
			
  			$time_zone = get_post_meta($event_post_id, '_EventTimezone', true);	 
			
			$choices = array();
			foreach($field->choices as $choice){				
			
		 		if( $choice['text'] == $time_zone ) {
					$choices[] = array('text' 	=> $time_zone, 
								   	   'value' 	=> $time_zone,
									   'isSelected' => true
									  ); 
				} 
				else {
					$choices[] = array('text' 	=> $choice['text'], 
								   	   'value' 	=> $choice['value'],
									   'isSelected' => false
									   ); 
					}
  			} 
			$field->choices = $choices;	
			unset( $choices );
		}
		if( $field->label == 'Topic Area' ) {
			$topic_areas = get_post_meta( $event_post_id, '__ecp_custom_15' );
			
			//admin options page
			$post_id = 12646;
			
			$choices = array();
			$inputs = array();
			
  			$repeater_value = get_post_meta($post_id, 'topic_area', true);	 
			 
			$input_id = 1;
			$skip = false;			
  			for ($i=0; $i<$repeater_value; $i++) {
				
				$archived_key = 'topic_area_'.$i.'_archived';
				
				if( get_post_meta($post_id, $archived_key, true) ) {
					continue;
				}
					
				if ( $input_id % 10 == 0 ) {
                	$input_id++;
            	}
				
    	 		$meta_key = 'topic_area_'.$i.'_item';
				// Check if $topic_areas is indeed an array or an object.
				if (is_array($topic_areas) || is_object($topic_areas))
					{
					foreach( $topic_areas as $topic_area ) {
						if( $topic_area == get_post_meta($post_id, $meta_key, true) ) {
							$choices[] = array('text' 	=> get_post_meta($post_id, $meta_key, true),
											'value' 	=> get_post_meta($post_id, $meta_key, true),
											'isSelected' => true
											); 
							$inputs[] = array('label' 	=> get_post_meta($post_id, $meta_key, true), 
											'id'		=> "21.{$input_id}",
											);  
							$skip = true;
						}
					}
				} else {
					//Do Nothing  or echo "Unfortunately, an error occured.";
				}
				if( !$skip ) {
					$choices[] = array('text' 	=> get_post_meta($post_id, $meta_key, true), 
								   	   'value' 	=> get_post_meta($post_id, $meta_key, true)	
								  	  );   					
				
					$inputs[] = array('label' 	=> get_post_meta($post_id, $meta_key, true), 
								      'id'		=> "21.{$input_id}"
								     );  
				} 
 				$input_id++;
 				$skip = false;
  			} 
		
			$field->choices = $choices;	
			$field->inputs = $inputs;	
			
			unset( $choices );
			unset( $inputs );
		}

		if( $field->label == 'Grant Programs' ) {
			
			$grant_programs_selected = get_post_meta( $event_post_id, '__ecp_custom_17');
			
			if( !empty(rgpost( 'input_12' ) )) {
				$ta_project = rgpost( 'input_12' ); // replace "1" with your field ID
			}
			else {
				$ta_project = get_post_meta( $event_post_id, '_ecp_custom_3', true);
			}
			
			
			//admin options page
			$post_id = get_post_id_for_organization();
			
			$choices = array();
			$inputs = array();
			
  			$repeater_value = get_post_meta($post_id, 'grant_projects_for_directory', true);	

  			for ($i=0; $i<$repeater_value; $i++) {
				
				$archived_key = 'grant_projects_for_directory_'.$i.'_archived';
				
				if( get_post_meta($post_id, $archived_key, true) ) {
					continue;
				}
							
    	 		$meta_key = 'grant_projects_for_directory_'.$i.'_project_title';
			
				if( get_post_meta($post_id, $meta_key, true) == $ta_project ) {
					
					$meta_key_programs = 'grant_projects_for_directory_'.$i.'_grant_programs';
					
					$grant_programs_org = get_post_meta($post_id, $meta_key_programs, true);					
					
					$input_id = 1;
					$skip = false;	
					foreach($grant_programs_org as $program) {
						
						foreach($grant_programs_selected as $selected_program) {
						
							if ( $input_id % 10 == 0 ) {
                				$input_id++;
            				}
						
							if( $program == $selected_program ) {
								$choices[] = array('text' 	=> $program,
												   'value' 	=> $program,
												   'isSelected' => true
												  ); 
								$inputs[] = array('label' 	=> $program,
												  'id'		=> "23.{$input_id}"
												 ); 
								$skip = true;
							}
						}	
						if( !$skip ) {
							$choices[] = array('text' 	=> $program,
											   'value' 	=> $program
											  );   					
				
							$inputs[] = array('label' 	=> $program, 
											  'id'		=> "23.{$input_id}"
											 );  
						} 
 						$input_id++;
 						$skip = false;
					}
				} 
  			} 

			$field->choices = $choices;	
			$field->inputs = $inputs;	
			
			unset( $choices );
			unset( $inputs );
		}
		
		if( $field->label == 'Target Audience' ) {			
			
			$target_audiences = get_post_meta( $event_post_id, '__ecp_custom_16');
			
			//admin options page
			$post_id = 12646;
			
			$choices = array();
			$inputs = array();
  			$repeater_value = get_post_meta($post_id, 'target_audience', true);	 
			 			
			$input_id = 1;
			$skip = false;
  			for ($i=0; $i<$repeater_value; $i++) {
				
				$archived_key = 'target_audience_'.$i.'_archived';
				
				if( get_post_meta($post_id, $archived_key, true) ) {
					continue;
				}
							
    	 		$meta_key = 'target_audience_'.$i.'_item';
				
				if ( $input_id % 10 == 0 ) {
                	$input_id++;
            	}
				
				// Check if $target_audiencess is indeed an array or an object.
				if (is_array($target_audiences) || is_object($target_audiences))
					{
						foreach( $target_audiences as $target_audience) {
							if( $target_audience == get_post_meta($post_id, $meta_key, true) ) {
								$choices[] = array('text' 	=> get_post_meta($post_id, $meta_key, true),
												'value' 	=> get_post_meta($post_id, $meta_key, true),
												'isSelected' => true
												); 
								$inputs[] = array('label' 	=> get_post_meta($post_id, $meta_key, true), 
												'id'		=> "25.{$input_id}",
												);  
								$skip = true;
							}
						}
					}
				if( !$skip ) {
					$choices[] = array('text' 	=> get_post_meta($post_id, $meta_key, true), 
								   	   'value' 	=> get_post_meta($post_id, $meta_key, true)	
								  	  );   					
				
					$inputs[] = array('label' 	=> get_post_meta($post_id, $meta_key, true), 
								      'id'		=> "25.{$input_id}"
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
		
		if( $field->label == 'Address' ) {			
			$city = get_post_meta( $event_post_id, '_ecp_custom_24', true );	
			
			$state = get_post_meta( $event_post_id, '_ecp_custom_25', true );	
    		
			$inputs = $field->inputs;
			
			$inputs[2]['defaultValue']  = $city;	
			
			$inputs[3]['defaultValue']  = $state;	
			
			$field->inputs = $inputs;
		}
		
		if( $field->label == 'Name' ) {			
			$first_name = get_post_meta( $event_post_id, '_ecp_custom_10', true );	
			
			$last_name = get_post_meta( $event_post_id, '_ecp_custom_11', true );	
    		
			$inputs = $field->inputs;
			
			$inputs[1]['defaultValue']  = $first_name;	
			
			$inputs[3]['defaultValue']  = $last_name;	
			
			$field->inputs = $inputs;
		}
		if( $field->label == 'Email' ) {			
			$email = get_post_meta( $event_post_id, '_ecp_custom_12', true );	
    		
			$field->defaultValue  = $email;	
		}
		if( $field->label == 'Phone' ) {			
			$phone = get_post_meta( $event_post_id, '_ecp_custom_13', true );	
    		
			$field->defaultValue  = $phone;	
		}
		 
    }
 
    return $form;
 
}

////////////////////////////////////////////////////////////////////////////////////
// update event
// function to update event after edit form is submitted
////////////////////////////////////////////////////////////////////////////////////
add_action( 'gform_after_submission_15', 'get_calendar_update_form', 10, 2 );
function get_calendar_update_form( $entry, $form ) {
	
	//get from query string
	$event_post_id = $_GET["post_id"];
	
	$page_status = rgar( $entry, '33' );
	
	if( $page_status == "Edit" ) {
		//3 - input for the event type
		$event_type_updated = rgar( $entry, '3' );
		$event_type_current =  get_the_terms( $event_post_id, 'tribe_events_cat' );

	  	if ( $event_type_updated != $event_type_current[0]->name ) {
			// 180 is the id number for Archive, set to false to replace current category (event type)
			wp_set_object_terms( $event_post_id, $event_type_updated, 'tribe_events_cat', false );			
	  	}

		// 8 - input for the timezone
		$timezone_updated = rgar( $entry, '8' );
		$timezone_current = get_post_meta( $event_post_id, '_EventTimezone', true );

	  	if ( $timezone_updated != $timezone_current ) {
			update_post_meta( $event_post_id, '_EventTimezone', $timezone_updated );
	  	}
		
		// 12 - input for ta project
		$ta_project_updated = rgar( $entry, '12' );
		$ta_project_current = get_post_meta( $event_post_id, '_ecp_custom_3', true );

	  	if ( $ta_project_updated != $ta_project_current ) {
			update_post_meta( $event_post_id, '_ecp_custom_3', $ta_project_updated );
	  	}
		
		// 13 - input for virtual
		$approved_updated = rgar( $entry, '30' );
		$approved_current = get_post_meta( $event_post_id, '_ecp_custom_5', true );

	  	if ( $approved_updated != $approved_current ) {
			update_post_meta( $event_post_id, '_ecp_custom_5', $approved_updated );
	  	}
		
		// 13 - input for virtual
		$virtual_updated = rgar( $entry, '13' );
		$virtual_current = get_post_meta( $event_post_id, '_ecp_custom_2', true );

	  	if ( $virtual_updated != $virtual_current ) {
			update_post_meta( $event_post_id, '_ecp_custom_2', $virtual_updated );
	  	}
		
		// 15.3 - input for the city
		$city_updated = rgar( $entry, '15.3' );
		$city_current = get_post_meta( $event_post_id, '_ecp_custom_24', true );

	  	if ( $city_updated != $city_current ) {
			update_post_meta( $event_post_id, '_ecp_custom_24', $city_updated );
	  	}
	
		// 15.4 - input for state
		$state_updated = rgar( $entry, '15.4' );
		$state_current = get_post_meta( $event_post_id, '_ecp_custom_25', true );

		if ( $state_updated != $state_current ) {
			update_post_meta( $event_post_id, '_ecp_custom_25', $state_updated );
		}
		
		// 18 - input for event_url
		$event_url_updated = rgar( $entry, '18' );
		$event_url_current = get_post_meta( $event_post_id, '_ecp_custom_6', true );

	  	if ( $event_url_updated != $event_url_current ) {
			update_post_meta( $event_post_id, '_ecp_custom_6', $event_url_updated );
	  	}
		
		// 19 - input for registration_type
		$registration_type_updated = rgar( $entry, '19' );
		$registration_type_current = get_post_meta( $event_post_id, '_ecp_custom_7', true );

	  	if ( $registration_type_updated != $registration_type_current ) {
			update_post_meta( $event_post_id, '_ecp_custom_7', $registration_type_updated );
	  	}
		
		// 20 - input for registration_url
		$registration_url_updated = rgar( $entry, '20' );
		$registration_url_current = get_post_meta( $event_post_id, '_ecp_custom_8', true );

	  	if ( $registration_url_updated != $registration_url_current ) {
			update_post_meta( $event_post_id, '_ecp_custom_8', $registration_url_updated );
	  	}
		
		// 27.3 - input for the first name
		$first_name_updated = rgar( $entry, '27.3' );
		$first_name_current = get_post_meta( $event_post_id, '_ecp_custom_10', true );

	  	if ( $first_name_updated != $first_name_current ) {
			update_post_meta( $event_post_id, '_ecp_custom_10', $first_name_updated );
	  	}
	
		// 27.6 - input for the last name
		$last_name_updated = rgar( $entry, '27.6' );
		$last_name_current = get_post_meta( $event_post_id, '_ecp_custom_11', true );

		if ( $last_name_updated != $last_name_current ) {
			update_post_meta( $event_post_id, '_ecp_custom_11', $last_name_updated );
		}
   
		$email_updated = rgar( $entry, '28' );
		$email_current = get_post_meta( $event_post_id, '_ecp_custom_12', true );

		if ( $email_updated != $email_current ) {
			update_post_meta( $event_post_id, '_ecp_custom_12', $email_updated );
	 	}
	
		$phone_updated = rgar( $entry, '29' );
		$phone_current = get_post_meta( $event_post_id, '_ecp_custom_13', true );

		if ( $phone_updated != $phone_current ) {
			update_post_meta( $event_post_id, '_ecp_custom_13', $phone_updated );
	 	}
		
		// 4 input for start date
		$start_date = rgar( $entry, 4 );
		
		// 5 - input for start time
		$start_time = date('H:i:s', strtotime( rgar( $entry, 5 ) ));
	
		$start_date_time_updated = $start_date . " " . $start_time;
  		$start_date_time_current = get_post_meta( $event_post_id, '_EventStartDate', true );
		if( $start_date_time_updated != $start_date_time_current ) {
			update_post_meta( $event_post_id, '_EventStartDate', $start_date_time_updated );
		
			$tz = rgar( $entry, 8 );
			$dt = new DateTime($start_date_time_updated, new DateTimeZone($tz)); 
			$dt->setTimezone(new DateTimeZone("UTC")); //first argument "must" be a string
		
			update_post_meta( $event_post_id, '_EventStartDateUTC', $dt->format('Y-m-d H:i:s') );
		}  
	
		$end_date = rgar( $entry, 9 );
	
		$end_time = date('H:i:s', strtotime( rgar( $entry, 10 ) ));
	
		if( !isset( $end_date ) || (strtotime($end_date) < strtotime($start_date) )) {
			$end_date = $start_date;
		}
	
		if( !isset( $end_time ) || (strtotime($end_time) < strtotime($start_time) )) {
			$end_time = $start_time;
		}
	
		$end_date_time_updated = $end_date . " " . $end_time;
		$end_date_time_current = get_post_meta( $event_post_id, '_EventEndDate', true );
		if( $end_date_time_updated != $end_date_time_current ) {
			update_post_meta( $event_post_id, '_EventEndDate', $end_date_time_updated );
		
			$tz = rgar( $entry, 8 );
			$dt = new DateTime($end_date_time_updated, new DateTimeZone($tz)); 
			$dt->setTimezone(new DateTimeZone("UTC")); //first argument "must" be a string
			update_post_meta( $event_post_id, '_EventEndDateUTC', $dt->format('Y-m-d H:i:s') );
		}  
		
		$title_updated = rgar( $entry, 7 );
		$title_current = get_the_title( $event_post_id );
		if( $title_updated != $title_current ) {
			wp_update_post(
    			array (
        			'ID'         => $event_post_id,
        			'post_title' => $title_updated
    			)
			);		
		}  
		
		$content_updated = rgar( $entry, 16 );
		$content_current = get_the_content( $event_post_id );
		if( $content_updated != $content_current ) {
			wp_update_post(
    			array(
      				'ID'           => $event_post_id,//the ID of the Post
      				'post_content' => $content_updated
  				)
			);		
		} 
		
		// Get field object.
    	$topic_area = GFAPI::get_field( $form, 21 );
    	// Get a comma separated list of checkboxes checked
    	$checked = $topic_area->get_value_export( $entry );
  
    	// Convert to array.
    	$topic_area_updated = str_replace(", ", "|", $checked);
		$topic_area_current =	 get_post_meta( $event_post_id, '_ecp_custom_15', true );
    	if( $topic_area_updated !=  $topic_area_current ) {	
  
			// Replace my_custom_field_key with your custom field meta key.
    		update_post_meta( $event_post_id, '_ecp_custom_15', $topic_area_updated );
		
        	// Convert to array.
        	$values = explode(", ", $checked);
			
			delete_post_meta( $event_post_id, '__ecp_custom_15' );
			foreach( $values as $value ) {    
    			add_post_meta( $event_post_id, '__ecp_custom_15', $value );
			}
			unset( $values );
		}
		unset( $checked );
		
		// Get field object.
    	$grant_program = GFAPI::get_field( $form, 23 );
    	// Get a comma separated list of checkboxes checked
    	$checked = $grant_program->get_value_export( $entry );
  
    	// Convert to array.
    	$grant_program_updated = str_replace(", ", "|", $checked);
		$grant_program_current =	 get_post_meta( $event_post_id, '_ecp_custom_17', true );
    	if( $grant_program_updated !=  $grant_program_current ) {	
  
			// Replace my_custom_field_key with your custom field meta key.
    		update_post_meta( $event_post_id, '_ecp_custom_17', $grant_program_updated );
		
        	// Convert to array.
        	$values = explode(", ", $checked);
			
			delete_post_meta( $event_post_id, '__ecp_custom_17' );
			foreach( $values as $value ) {    
    			add_post_meta( $event_post_id, '__ecp_custom_17', $value );
			}
			unset( $values );
		}
		unset( $checked );
		
		// Get field object.
    	$target_audience = GFAPI::get_field( $form, 25 );
    	// Get a comma separated list of checkboxes checked
    	$checked = $target_audience->get_value_export( $entry );
  
    	// Convert to array.
    	$target_audience_updated = str_replace(", ", "|", $checked);
		$target_audience_current =	 get_post_meta( $event_post_id, '_ecp_custom_16', true );
    	if( $target_audience_updated !=  $target_audience_current ) {	
  
			// Replace my_custom_field_key with your custom field meta key.
    		update_post_meta( $event_post_id, '_ecp_custom_16', $target_audience_updated );
		
        	// Convert to array.
        	$values = explode(", ", $checked);
			
			delete_post_meta( $event_post_id, '__ecp_custom_16' );
			foreach( $values as $value ) {    
    			add_post_meta( $event_post_id, '__ecp_custom_16', $value );
			}
			unset( $values );
		}
		unset( $checked );
	}
	elseif( $page_status == "Archive" ) {
		// 180 is the id number for Archive, set to false to replace current category (event type)
		wp_set_object_terms( $event_post_id, 'Archived', 'tribe_events_cat', false );
	}
	elseif( $page_status == "Delete" ) {
		wp_trash_post( $event_post_id );
	}
}

////////////////////////////////////////////////////////////////////////////////////
// shortcodes for single event - calendar templates
////////////////////////////////////////////////////////////////////////////////////
function display_event_time() {
	$post_id = get_the_ID();
	
	$time_string = '';
	
	if( isset($post_id) ) {				
		$timezone = get_post_meta($post_id, '_EventTimezoneAbbr', true);
		$start_date_time = new DateTime(get_post_meta($post_id, '_EventStartDate', true)); 
		$end_date_time = new DateTime(get_post_meta($post_id, '_EventEndDate', true)); 
				
		$time_string .= '<div>'; 				
		$time_string .= $start_date_time->format('F d, Y @ h:i a') . ' - ';
		
		if( $start_date_time->format('F d') == $end_date_time->format('F d') ) {
			$time_string .= $end_date_time->format('h:i a');
		}
		else {
			$time_string .= $end_date_time->format('F d, Y @ h:i a');
		}

		$time_string .= ' ' . $timezone . '</div>';
	}
	//echo $post_id;
	return $time_string;
}
add_shortcode('event_time', 'display_event_time');
/////////////////////////////######################///
### Test event () 									//

function display_event_ID() {
	$post_id = get_the_ID();
	return $post_id;
}
add_shortcode('event_id', 'display_event_ID');
####################################################//

function display_is_virtual() {
	$post_id = get_the_ID();
	
	$virtual_string = '';
	
	if( isset($post_id) ) {				
		
		$virtual_string .= '<div class="event-location">'; 				
		
		$virtual = get_post_meta($post_id, '_ecp_custom_2', true);
		
		if( $virtual == "Yes" ) {
			$virtual_string .= '<h4>Event Registration</h4><p>This event is virtual</p>';
		}
		else {
			$city = get_post_meta($post_id, '_ecp_custom_24', true);
			$state = get_post_meta($post_id, '_ecp_custom_25', true);
			
			if( !empty($city) || !empty($state) ){
				$virtual_string .= 'Location: ';
				
				if( !empty($city) ) {
					$virtual_string .= $city;
				}
				if( !empty($state) ) {
					$virtual_string .= ', ' . $state;
				}
			} else {
				$virtual_string .= '<div style="font-weight: 600; text-align: center;">ONLINE EVENT</div>';
			}
		}
		
		$virtual_string .= '</div>';
	}
	
	return $virtual_string;
}
add_shortcode('event_virtual', 'display_is_virtual');

function display_event_url() {
	$post_id = get_the_ID();
	
	$event_string = '';
	
	if( isset($post_id) ) {					
		$event_url = get_post_meta($post_id, '_ecp_custom_6', true);
		
		if( !empty($event_url) ) {
			$event_string .= '<div class="event_webpage"><h5>Event Information</h5>'; 	
			$event_string .= '<a href="' . $event_url . '" target="_blank">Click here for more information regarding this event</a>';	
			$event_string .= '</div>';
		}
	}
	
	return $event_string;
}
add_shortcode('event_webpage', 'display_event_url');

function display_registration_url() {
	$post_id = get_the_ID();
	
	$registration_string = '';
	
	if( isset($post_id) ) {				
		
		$registration_string .= '<div class="event-registration"> Registration: '; 				
		
		$registration_type = get_post_meta($post_id, '_ecp_custom_7', true);
		
		if( $registration_type == "Invite Only" ) {
			$registration_string .= 'Invite Only <br />';
		}
		else {
			$registration_string .= 'Open <br />';					
		}
		
		$registration_url = get_post_meta($post_id, '_ecp_custom_8', true);	
		if( !empty($registration_url) ) {
			$registration_string .= '<a href="' . $registration_url . '" target="_blank">Click here to register for this event</a>';
		}
		
		$registration_string .= '</div>';
	}
	
	return $registration_string;
}
add_shortcode('event_registration', 'display_registration_url');

function display_contact_name() {
	$post_id = get_the_ID();
	
	$contact_name_string = '';
	
	if( isset($post_id) ) {	
		$first_name = get_post_meta($post_id, '_ecp_custom_10', true);
		$last_name = get_post_meta($post_id, '_ecp_custom_11', true);
		$email = get_post_meta($post_id, '_ecp_custom_12', true);
		$phone = get_post_meta($post_id, '_ecp_custom_13', true);
		
		if( !empty($first_name) || !empty($email) || !empty($phone) ) {
			$contact_name_string .= '<div class="contact-card"><h5 class="header-no-margin" style="padding-bottom:10px;">Contact Information</h5>';  
			if( !empty($first_name) ) {
				$contact_name_string .= $first_name . ' ' . $last_name . '<br />';
			}
			if( !empty($email) ) {
				$contact_name_string .= 'Email: ' . $email . '<br />';
			}
			if( !empty($phone) ) {
				$contact_name_string .= 'Phone: ' . $phone . '<br />';
			}
			$contact_name_string .= '</div>';
		}		
	}
	
	return $contact_name_string;
}
add_shortcode('event_contact_name', 'display_contact_name');

function display_org() {
	$post_id = get_the_ID();
	
	$org_string = '';
	
	if( isset($post_id) ) {		
		$org = get_post_meta($post_id, '_ecp_custom_27', true);
		
		$post_slug = str_replace(" ","-", $org);		
		$post_slug = str_replace("'","", $post_slug);		
		$org_link = get_site_url().'/'.$post_slug;
		
		$org_string .= '<div>Hosted by: <a href="' . $org_link . '" target="">' . $org . '</a><br />';  
		
		$org_string .= '</div>';		
	}
	
	return $org_string;
}
add_shortcode('event_org', 'display_org');

function display_ta_project() {
	$post_id = get_the_ID();
	
	$project_string = '';
	
	if( isset($post_id) ) {		
		$project = get_post_meta($post_id, '_ecp_custom_3', true);
		
		$org = get_post_meta($post_id, '_ecp_custom_27', true);
		$post_slug = str_replace(" ","-", $org);		
		$post_slug = str_replace("'","", $post_slug);		
		$org_link_project = get_site_url().'/'.$post_slug;
		
		$post_slug = str_replace(" ","-", $project);		
		$post_slug = str_replace("'","", $post_slug);	
		$org_link_project  .= '#'.$post_slug;
		
		$project_string .= '<div class="projects-s">Project: <a href="' . $org_link_project . '" target="">' . $project . '</a><br />';  
		
		$project_string .= '</div>';		
	}
	
	return $project_string;
}
add_shortcode('event_ta_project', 'display_ta_project');

function display_topic_areas() {
	$post_id = get_the_ID();
	
	$topic_areas_string = '';
	
	if( isset($post_id) ) {		
		$topic_areas = get_post_meta($post_id, '__ecp_custom_15');	
		
		$topic_areas_string .= '<div class="topic-areas-s"><ul>';  
		
		foreach( $topic_areas as $topic_area ) {
			$topic_areas_string .= '<li>' . $topic_area . '</li>'; 
		}
		
		$topic_areas_string .= '</ul></div>';  
	}
	
	return $topic_areas_string;
}
add_shortcode('event_topic_areas', 'display_topic_areas');

function display_grant_programs() {
	$post_id = get_the_ID();
	
	$grant_programs_string = '';
	
	if( isset($post_id) ) {		
		$grant_programs = get_post_meta($post_id, '__ecp_custom_17');	
		
		$grant_programs_string .= '<div class="grant-programs-s"><ul>';  
		
		foreach( $grant_programs as $grant_program ) {
			$grant_programs_string .= '<li>' . $grant_program . '</li>'; 
		}
		
		$grant_programs_string .= '</ul></div>';  
	}
	
	return $grant_programs_string;
}
add_shortcode('event_grant_programs', 'display_grant_programs');

function display_target_audiences() {
	$post_id = get_the_ID();
	
	$target_audiences_string = '';
	
	if( isset($post_id) ) {		
		$target_audiences = get_post_meta($post_id, '__ecp_custom_16');	
		
		$target_audiences_string .= '<div class="target-audiences-s"><ul>';  
		
		foreach( $target_audiences as $target_audience ) {
			$target_audiences_string .= '<li>' . $target_audience . '</li>'; 
		}
		
		$target_audiences_string .= '</ul></div>';  
	}
	
	return $target_audiences_string;
}
add_shortcode('event_target_audiences', 'display_target_audiences');

function display_event_type() {
	$post_id = get_the_ID();
	$event_type = get_the_terms( $post_id, 'tribe_events_cat' );	
	$event_type_string = '';

	$background_color = '';
	$color = '';

	switch ($event_type[0]->name) {
		case 'Conference':
			$background_color = 'lightcyan';
			$color = 'black';
		break;
		case 'Meeting':
			$background_color = 'darkviolet';	
			$color = 'white';
		break;
		case 'New Grantee Orientation':
			$background_color = 'darkgreen';	
			$color = 'white';
		break;
		case 'Teleconf':
			$background_color = 'yellow';	
			$color = 'black';
		break;
		case 'Training':
			$background_color = 'lightsalmon';	
			$color = 'black';
		break;
		case 'Webinar':
			$background_color = 'darkslateblue';	
			$color = 'white';
		break;
		default:
		
	}
	
	if( isset($post_id) ) {		

		$event_type_string .= '<span style="padding:4px 10px; background-color: '. $background_color . '; color: '. $color .'; border-radius: 3px; -webkit-box-sizing: border-box; box-sizing: border-box; box-shadow: 0 2px 2px 0 rgba(0,0,0,0.14), 0 1px 5px 0 rgba(0,0,0,0.12), 0 3px 1px -2px rgba(0,0,0,0.2);">';  
		
		$event_type_string .= $event_type[0]->name; 
		
		$event_type_string .= '</span>';  
	}
	
	return $event_type_string;
}
add_shortcode('event_type', 'display_event_type');

function display_description() {
	$post_id = get_the_ID();
	$target_description_string = '';
	
	if( isset($post_id) ) {		
		$event = tribe_get_event($post_id,'', 'raw');
		$target_description_string .= '<h5>Description</h5>';  
		$target_description_string .= '<p class="event-desc-summary">';
		$target_description_string .= strval($event->post_content);
		$target_description_string .= '</p>';  
	}
	return $target_description_string;
}
add_shortcode('event_description', 'display_description');

function display_pending() {
	$post_id = get_the_ID();
	
	$pending_string = '';
	
	if( isset($post_id) ) {		
		$pending = get_post_meta($post_id, '_ecp_custom_5', true);
				
		if( $pending == 'Yes' || $pending == 1) {
			$pending_string = '
							<span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M19 9a7 7 0 1 0-10.974 5.76L5 20l2.256.093L8.464 22l3.466-6.004c.024 0 .046.004.07.004s.046-.003.07-.004L15.536 22l1.232-1.866L19 20l-3.026-5.24A6.99 6.99 0 0 0 19 9M7 9a5 5 0 1 1 5 5a5 5 0 0 1-5-5"></path><circle cx="12" cy="9" r="3" fill="currentColor"></circle></svg>
                            </span>
							<div style="color:darkgreen">OVW approved!</div>
						';
		}
		else {
			$pending_string = '<div style="color:darkred">Pending OVW Approval</div>';
		}

	}
	
	return $pending_string;
}
add_shortcode('event_pending', 'display_pending');


#########################################################################################################################


// add_action( 'tribe_get_custom_fields', 'tribe_add_additional_choices', 100 );
// function tribe_add_additional_choices() {
// // If on the front-end, return no data.
// 	if($field['label'] == 'organization'){

// 		$choices = $organization_list();
		
// 	}
// }
















?>