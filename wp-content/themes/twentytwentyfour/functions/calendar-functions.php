<?php
/*
 * This file has all the functions related to the calendar page
 * Gravity Forms
 * 
 */

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
		
		$city = get_post_meta($post_id, '_ecp_custom_25', true);
		$state = get_post_meta($post_id, '_ecp_custom_27', true);
		
		if( !empty($city) || !empty($state) ){
			$virtual_string .= 'Location: ';
			
			if( !empty($city) ) {
				$virtual_string .= $city;
			}
			if( !empty($state) ) {
				$virtual_string .= ', ' . $state;
			}
		} else {
			$virtual = get_post_meta($post_id, '_tribe_events_is_virtual', true);
			$is_virtual = get_post_meta($post_id, '_tribe_virtual_events_type', true);
			//if( $virtual == "Yes" || $virtual == 1 || $is_virtual == 'Virtual' ) {
				$virtual_string .= '<div style="font-weight: 600; text-align: center;">ONLINE EVENT</div>';
			//}
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
		
		$registration_string .= '<div class="registration"> Registration: '; 				
		
		$registration_type = get_post_meta($post_id, '_ecp_custom_17', true);
		
		if( $registration_type == "Invite Only" ) {
			$registration_string .= 'Invite Only <br />';
		}
		else {
			$registration_string .= 'Open <br />';					
		}
		
		$registration_url = get_post_meta($post_id, '_ecp_custom_10', true);	
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
		$fullname = get_post_meta($post_id, '_ecp_custom_12', true);
		$email = get_post_meta($post_id, '_ecp_custom_13', true);
		$phone = get_post_meta($post_id, '_ecp_custom_15', true);
		
		if( !empty( $fullname ) || !empty( $email ) || !empty( $phone ) ) {
			$contact_name_string .= '<div class="contact-card" itemscope itemtype="https://schema.org/Person"><h5 class="header-no-margin" style="padding-bottom:10px;">Contact Information</h5>';  
			if( !empty( $fullname ) ) {
				$contact_name_string .= $fullname .'<br />';
			}
			if( !empty($email) ) {
				$contact_name_string .= 'Email: <a href="mailto:'. $email . '">' . $email .'</a><br />';
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
		$org = get_post_meta($post_id, '_ecp_custom_49', true);
		
		$post_slug = str_replace(" ","-", $org);		
		$post_slug = str_replace("'","", $post_slug);		
		$org_link = get_site_url().'/'.$post_slug;
		
		$org_string .= '<div class="e-host"> Hosted by: <a href="' . $org_link . '" target="">' . $org . '</a><br />';  
		
		$org_string .= '</div>';		
	}
	
	return $org_string;
}
add_shortcode('event_organization', 'display_org');

function display_grant_project() {
	$post_id = get_the_ID();
	
	$project_string = '';
	
	if( isset($post_id) ) {		
		$project = get_post_meta($post_id, '_ecp_custom_3', true);
		$award = get_post_meta($post_id, '_ecp_custom_5', true);
		
		$org = get_post_meta($post_id, '_ecp_custom_49', true);
		$post_slug = str_replace(" ","-", $org);		
		$post_slug = str_replace("'","", $post_slug);		
		$org_link_project = get_site_url() . '/' . $post_slug;
		
		$project_string .= '<div class="projects-s"> Project : <a href="' . $org_link_project . '" target="">' . $project . '</a>';  
		
		$project_string .= '</div>';
		$project_string .= '<div class="projects-s"> Award : ' . $award . '</div>';
	}
	
	return $project_string;
}
add_shortcode('event_grant_project', 'display_grant_project');

function display_grant_programs() {
	$post_id = get_the_ID();
	
	$grant_programs_string = '';
	
	if( isset($post_id) ) {		
		$grant_programs = get_post_meta($post_id, '__ecp_custom_6');	
		
		$grant_programs_string .= '<div class="grant-programs-s"><ul>';  
		
		foreach( $grant_programs as $grant_program ) {
			$grant_programs_string .= '<li>' . $grant_program . '</li>'; 
		}
		
		$grant_programs_string .= '</ul></div>';  
	}
	
	return $grant_programs_string;
}
add_shortcode('event_grant_programs', 'display_grant_programs');

function display_topic_areas() {
	$post_id = get_the_ID();
	
	$topic_areas_string = '';
	
	if( isset($post_id) ) {		
		$topic_areas = get_post_meta($post_id, '_ecp_custom_6');	
		
		$topic_areas_string .= '<div class="topic-areas-s"><ul>';  
		
		foreach( $topic_areas as $topic_area ) {
			$topic_areas_string .= '<li>' . $topic_area . '</li>'; 
		}
		
		$topic_areas_string .= '</ul></div>';  
	}
	
	return $topic_areas_string;
}
add_shortcode('event_topic_areas', 'display_topic_areas');

function display_target_audiences() {
	$post_id = get_the_ID();
	
	$target_audiences_string = '';
	
	if( isset($post_id) ) {		
		$target_audiences = get_post_meta($post_id, '__ecp_custom_7');	
		
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

	$event_type_options = get_field( 'event_type', 'options', true );
	$colors=[];
	foreach($event_type_options as $event_color){
		$colors += [
			$event_color['name'] => [
				'background_color' => $event_color['background_color'], 
				'color' => $event_color['color']
				]
			];
	}

	$event_type_string = '';
	
	if( isset($post_id) ) {		

		$event_type_string .= '<span style="padding:4px 10px; background-color: '. $colors[$event_type[0]->name]['background_color'] . '; color: '. $colors[$event_type[0]->name]['color'] .'; border-radius: 3px; -webkit-box-sizing: border-box; box-sizing: border-box; box-shadow: 0 2px 2px 0 rgba(0,0,0,0.14), 0 1px 5px 0 rgba(0,0,0,0.12), 0 3px 1px -2px rgba(0,0,0,0.2);">';  
		
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
		$pending = get_post_meta($post_id, '_ecp_custom_24', true);
				
		if( $pending == 'Yes' || $pending == 1) {
			$pending_string = '
						<p class="e-ovw-approval">
							<span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M19 9a7 7 0 1 0-10.974 5.76L5 20l2.256.093L8.464 22l3.466-6.004c.024 0 .046.004.07.004s.046-.003.07-.004L15.536 22l1.232-1.866L19 20l-3.026-5.24A6.99 6.99 0 0 0 19 9M7 9a5 5 0 1 1 5 5a5 5 0 0 1-5-5"></path><circle cx="12" cy="9" r="3" fill="currentColor"></circle></svg>
                            </span>
							OVW approved!
						<p>
						';
		}
		else {
			//$pending_string = '<p class="e-ovw-pending-approval" style="color:darkred">Pending OVW Approval</p>';
		}

	}
	
	return $pending_string;
}
add_shortcode('event_pending', 'display_pending');



?>