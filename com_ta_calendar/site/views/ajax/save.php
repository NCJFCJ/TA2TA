<?php
/**
 * @package     com_ta_calendar
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */
 
// no direct access
defined('_JEXEC') or die;

// variables	
$return = array();	
$return['message'] = '';
$return['status'] = '';

/* Get the permission level
 * 0 = Public (view only)
 * 1 = TA Provider (restricted to adding and editing own)
 * 2 = Administrator (full access and ability to edit)
 */
require_once(JPATH_COMPONENT_SITE . '/helpers/ta_calendar.php');
$permission_level = Ta_calendarHelper::getPermissionLevel();
 
if($permission_level > 0){
	// Check that data was submitted via post and that the proper variables were received
	if ($_SERVER['REQUEST_METHOD'] == "POST"
		&& filter_has_var(INPUT_POST, 'id')
		&& filter_has_var(INPUT_POST, 'startdate')
		&& filter_has_var(INPUT_POST, 'starttime')
		&& filter_has_var(INPUT_POST, 'enddate')
		&& filter_has_var(INPUT_POST, 'endtime')
		&& filter_has_var(INPUT_POST, 'title')
		&& filter_has_var(INPUT_POST, 'type')
		&& filter_has_var(INPUT_POST, 'summary')
		&& filter_has_var(INPUT_POST, 'open')
		&& filter_has_var(INPUT_POST, 'project')
		&& filter_has_var(INPUT_POST, 'topicAreas')
		&& filter_has_var(INPUT_POST, 'approved')
		&& filter_has_var(INPUT_POST, 'grantPrograms')
		&& filter_has_var(INPUT_POST, 'targetAudiences')
		&& filter_has_var(INPUT_POST, 'timezone')
		){
		// retrieve and sanitize the data
		$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
		$startdate = filter_input(INPUT_POST, 'startdate', FILTER_SANITIZE_STRING);
		$starttime = filter_input(INPUT_POST, 'starttime', FILTER_SANITIZE_STRING);	
		$enddate = filter_input(INPUT_POST, 'enddate', FILTER_SANITIZE_STRING);
		$endtime = filter_input(INPUT_POST, 'endtime', FILTER_SANITIZE_STRING);
		$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
		$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_NUMBER_INT);
		$summary = filter_input(INPUT_POST, 'summary', FILTER_SANITIZE_STRING);
		$event_url = filter_input(INPUT_POST, 'event_url', FILTER_SANITIZE_URL);
		$open = ($_POST['open'] == 1 ? 1 : 0);
		$registration_url = filter_input(INPUT_POST, 'registration_url', FILTER_SANITIZE_URL);
		$project = filter_input(INPUT_POST, 'project', FILTER_SANITIZE_NUMBER_INT);
		$topicAreas = filter_var_array($_POST['topicAreas'], FILTER_SANITIZE_NUMBER_INT);
		$approved = ($_POST['approved'] == 1 ? 1 : 0);
		$grantPrograms = filter_var_array($_POST['grantPrograms'], FILTER_SANITIZE_NUMBER_INT);
		$targetAudiences = filter_var_array($_POST['targetAudiences'], FILTER_SANITIZE_NUMBER_INT);
		$timezone = filter_input(INPUT_POST, 'timezone', FILTER_SANITIZE_STRING);
		
		// validate
		$warnings = array();
		
		// regular expressions
		$dateRegEx = '/^((0?[1-9]|1[012])[-](0?[1-9]|[12][0-9]|3[01])[-](19|20)?[0-9]{2})*$/';
		$timeRegEx = '/^(([1-9]|1[012])[:](0[0-9]|[12345][0-9])[ap][m])*$/';
		$titleRegEx = '/^[a-zA-Z0-9-@ &,:\'()\[\]]*$/';
		
		// enddate
		if(empty($enddate)){
			$warnings[] = 'You must select an end date.';
		}else{
			if(!preg_match($dateRegEx, $enddate)){
				$warnings[] = 'The end date you entered is invalid (format: mm-dd-yyyy).';
			}		
		}
		
		// endtime
		if(empty($endtime)){
			$warnings[] = 'You must select an end time.';
		}else{
			if(!preg_match($timeRegEx, $endtime)){
				$warnings[] = 'The end time you entered is invalid (exmaple: 3:30pm).';
			}
		}
		
		// event_url
		if(!empty($event_url)){
			// add on the protocol if missing
			if(substr($event_url,0,4) != 'http'){
				$event_url = 'http://' . $event_url;
			}
			if(!filter_var($event_url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)){
				$warnings[] = 'The event URL you entered is not valid';
			}
		}
		
		// project
		if(empty($project)){
			$warnings[] = 'You must select a TA Project.';
		}
		
		// registration_url
		if(!empty($registration_url)){
			// add on the protocol if missing
			if(substr($registration_url,0,4) != 'http'){
				$registration_url = 'http://' . $registration_url;
			}
			if(!filter_var($registration_url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)){
				$warnings[] = 'The registration URL you entered is not valid';
			}
		}
		
		// startdate
		if(empty($startdate)){
			$warnings[] = 'You must select a start date.';
		}else{
			if(!preg_match($dateRegEx, $startdate)){
				$warnings[] = 'The start date you entered is invalid (format: mm-dd-yyyy).';
			}		
		}
		
		// starttime
		if(empty($starttime)){
			$warnings[] = 'You must select an start time.';
		}else{
			if(!preg_match($timeRegEx, $starttime)){
				$warnings[] = 'The start time you entered is invalid (exmaple: 3:30pm).';
			}
		}
		
		// summary
		if(empty($summary)){
			$warnings[] = 'You must provide a summary.';
		}

		//grantPrograms
		if(empty($grantPrograms)){
			$warnings[] = 'You must choose at least one grant program.';
		}

		//targetAudiences
		if(empty($targetAudiences)){
			$warnings[] = 'You must choose at least one target audience.';
		}
		
		// title
		if(empty($title)){
			$warnings[] = 'You must enter a title for your event.';
		}else{
			if(!preg_match($titleRegEx, $title)){
				$warnings[] = 'The title you entered is invalid (hint: allowed special characters are @ & - ( ) [ ])';
			}
		}

		//topicArea
		if(empty($topicAreas)){
			$warnings[] = 'You must choose at least one topic area.';
		}

		// type
		if(empty($type)){
			$warnings[] = 'You must select an event type.';
		}

		// combine the dates and times provided by the user
		$userTimeZone = new DateTimeZone($timezone);
		$startDateTime = DateTime::createFromFormat('m-d-Y g:ia', $startdate . ' ' . $starttime, $userTimeZone);
		$endDateTime = DateTime::createFromFormat('m-d-Y g:ia', $enddate . ' ' . $endtime, $userTimeZone);
		
		// check that the end date is after the start date
		if($startDateTime >= $endDateTime){
			$warnings[] = 'You must enter an end date and time that is after your start date and time.';
		}

		if(empty($warnings)){
			// there were no validation issues, save this record
			
			// variables
			$curDateTime = gmdate('Y-m-d H:i:s');
			$user = JFactory::getUser();
			
			// conver the datetimes to UTC
			$utcTimeZone = new DateTimeZone('UTC');
			$startDateTime->setTimezone($utcTimeZone);
			$endDateTime->setTimezone($utcTimeZone);
			
			// render the date in a way that is compatible with MYSQL
			$start = $startDateTime->format('Y-m-d H:i:s');
			$end = $endDateTime->format('Y-m-d H:i:s');

			// get the user's organization
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select($db->quoteName('profile_value'));
			$query->from($db->quoteName('#__user_profiles'));
			$query->where($db->quoteName('user_id') . '=' . $db->quote($user->id) . ' AND ' . $db->quoteName('profile_key') . ' = ' . $db->quote('profile.org'));
			$db->setQuery($query);
			if($org = $db->loadResult()){
				$newId = 0;
				if($id == 0){
					// created
					$created = $curDateTime;
					$created_by = $user->id;

					// approved
					if($approved){
						$approved = $curDateTime;
						$approved_by = $user->id;
					}else{		
						$approved = 'NULL';
						$approved_by = 0;
					}

					// save as new event
					$query = $db->getQuery(true);
					$query->insert($db->quoteName('#__ta_calendar_events'));
					$query->columns($db->quoteName(array('state', 'org', 'start', 'end', 'title', 'summary', 'type', 'event_url', 'open', 'registration_url', 'provider_project', 'created', 'created_by', 'approved', 'approved_by')));
					$query->values(implode(',', array(
						'1',
						$org,
						$db->quote($start),
						$db->quote($end),
						$db->quote($title),
						$db->quote($summary),
						$db->quote($type),
						$db->quote($event_url),
						$db->quote($open),
						$db->quote($registration_url),
						$db->quote($project),
						$db->quote($created),
						$db->quote($created_by),
						$db->quote($approved),
						$db->quote($approved_by)
					)));
					$db->setQuery($query);
					if($db->query()){
						$newId = $db->insertid();

						// email us the details
							
						// create a mailer object	
						$mailer = JFactory::getMailer();
						$mailer->isHTML(true);
						$mailer->Encoding = 'base64';

						// set the sender to the site default
						$config = JFactory::getConfig();
						$sender = array( 
						    $config->get('config.mailfrom'),
						    $config->get('config.fromname')
						);
						$mailer->setSender($sender);

						// set the recipient
						$mailer->addRecipient('info@ta2ta.org');
					
						// set the message subject
						$mailer->setSubject('[TA2TA] - New Calendar Event Added');

						// get the user's organization name
						$query = $db->getQuery(true);
						$query->select($db->quoteName('name'));
						$query->from($db->quoteName('#__ta_providers'));
						$query->where($db->quoteName('id') . '=' . $org);
						$db->setQuery($query, 0 ,1);
						$orgName = $db->loadResult();

						// get the user's name
						$query = $db->getQuery(true);
						$query->select($db->quoteName('name'));
						$query->from($db->quoteName('#__users'));
						$query->where($db->quoteName('id') . '=' . $db->quote($user->id));
						$db->setQuery($query, 0 ,1);
						$userName = $db->loadResult();			 		
						
						// start the message body
						$message = "$userName of $orgName has submitted a new event to the TA2TA Event Calendar. The details of the event are as follows:<br><br>";
						$message .= '<table>';
						$message .= "<tr style=\"background: #DDD;\"><td style=\"170px;\"><b>ID<b></td><td>$newId</td></tr>";

						// get the event type
						$query = $db->getQuery(true);
						$query->select($db->quoteName('name'));
						$query->from($db->quoteName('#__ta_calendar_event_types'));
						$query->where($db->quoteName('id') . '=' . $db->quote($type));
						$db->setQuery($query, 0 ,1);
						$typeName = $db->loadResult();	

						$message .= "<tr><td><b>Type<b></td><td>$typeName</td></tr>";
						$message .= "<tr style=\"background: #DDD;\"><td><b>Title<b></td><td>$title</td></tr>";

						// process the start and end dates
						$startDateTime->setTimezone($utcTimeZone);
						$endDateTime->setTimezone($utcTimeZone);

						// update each date time to the user's timezone
						$tz = new DateTimeZone('America/Los_Angeles');
						if($startDateTime){
							$startDateTime->setTimezone($tz);
						}
						if($endDateTime){
							$endDateTime->setTimezone($tz);
						}

						// configure the date string
						$dateString = '';					
						if($startDateTime->format('Y-m-d') == $endDateTime->format('Y-m-d')){
							// single day
							$dateString = $startDateTime->format('M j, Y g:ia') . ' - ' . $endDateTime->format('g:ia');
						}else{
							// multi-day
							$dateString = $startDateTime->format('M j, Y g:ia') . ' - ' . $endDateTime->format('M j, Y g:ia');
						}

						$message .= "<tr><td><b>Date<b></td><td>$dateString PT</td></tr>";
						$message .= '<tr style="background: #DDD;"><td><b>OVW Approved<b></td><td>' . ($approved == 1 ? 'Yes' : 'No') . '</td></tr>';
						$message .= "<tr><td><b>Summary<b></td><td>$summary</td></tr>";

						// get the project name
						$query = $db->getQuery(true);
						$query->select($db->quoteName('title'));
						$query->from($db->quoteName('#__tapd_provider_projects'));
						$query->where($db->quoteName('id') . '=' . $db->quote($project));
						$db->setQuery($query, 0 ,1);
						$projectName = $db->loadResult();

						$message .= "<tr style=\"background: #DDD;\"><td><b>Project<b></td><td>$projectName</td></tr>";

						if(!empty($event_url)){
							$message .= "<tr><td><b>Event URL<b></td><td><a href=\"$event_url\" target=\"_blank\">Click Here</a></td></tr>";
						}
						if($open && !empty($registration_url)){
							$message .= "<tr><td" . (!empty($event_url) ? ' style="background: #DDD;"' : '') . "><b>Registration URL<b></td><td><a href=\"$registration_url\" target=\"_blank\">Click Here</a></td></tr>";
						}

/*
$topicAreas = filter_var_array($_POST['topicAreas'], FILTER_SANITIZE_NUMBER_INT);
$grantPrograms = filter_var_array($_POST['grantPrograms'], FILTER_SANITIZE_NUMBER_INT);
$targetAudiences = filter_var_array($_POST['targetAudiences'], FILTER_SANITIZE_NUMBER_INT);
*/

						$message .= '</table><br><br>';

						// view event button
						$message .= "<div style=\"background:#428BCA;display:inline-block;padding:10px;\"><a href=\"http://{$_SERVER['HTTP_HOST']}/calendar.html?event=$newId\" style=\"color:#fff;font-weight:bold;text-decoration:none;\" target=\"_blank\">View on Website</a></div>";

						// set the body
						$mailer->setBody($message);

						// send the message
						$mailer->Send();
					}else{
						$return['message'] = 'Unable to store event. Please contact us.';
						$return['status'] = 'error';

						// return the result
						echo json_encode($return);
						die();
					}
				}else{
					// check that this user has permission to edit this item
					if($permission_level > 0){
						// modified
						$modified = $curDateTime;
						$modified_by = $user->id;
						
						// approved
						$updateApproved = false;
						if($approved){
							// get the approved status before the update to determine if it changed
							$query = $db->getQuery(true);
							$query->select($db->quoteName('approved'));
							$query->from($db->quoteName('#__ta_calendar_events'));
							$query->where($db->quoteName('id') . "=$id");
							$db->setQuery($query);
							$db_approved = $db->loadResult();

							if($db_approved == '0000-00-00 00:00:00'){
								// was not approved before, but is now, update
								$approved = $curDateTime;
								$approved_by = $user->id;
								$updateApproved = true;
							}
						}else{		
							$approved = 'NULL';
							$approved_by = 0;
							$updateApproved = true;
						}

						// update the existing event
						$query = $db->getQuery(true);
						$query->update($db->quoteName('#__ta_calendar_events'));
						$fields = array(
							$db->quoteName('start') . '=' . $db->quote($start),
							$db->quoteName('end') . '=' . $db->quote($end),
							$db->quoteName('title') . '=' . $db->quote($title),
							$db->quoteName('summary') . '=' . $db->quote($summary),
							$db->quoteName('type') . '=' . $db->quote($type),
							$db->quoteName('event_url') . '=' . $db->quote($event_url),
							$db->quoteName('registration_url') . '=' . $db->quote($registration_url),
							$db->quoteName('open') . '=' . $db->quote($open),
							$db->quoteName('provider_project') . '=' . $db->quote($project),
							$db->quoteName('modified') . '=' . $db->quote($modified),
							$db->quoteName('modified_by') . '=' . $db->quote($modified_by)
						);
						if($updateApproved){
							$fields[] = $db->quoteName('approved') . '=' . $db->quote($approved);
							$fields[] = $db->quoteName('approved_by') . '=' . $db->quote($approved_by);
						}
						$query->set($fields);
						$conditions = array();
						$conditions[] = $db->quoteName('id') . "=$id";
						// if the user is not an admin, check the org
						if($permission_level != 2){
							$conditions[] = $db->quoteName('org') . "=$org";
						}
						$query->where($conditions);
						$db->setQuery($query);
						if($db->query()){
							// for brevity, just remove all past topic areas, target audiences, and event programs

							// topic areas
							$query = $db->getQuery(true);
							$query->delete($db->quoteName('#__ta_calendar_event_topic_areas'));
							$query->where($db->quoteName('event') . "=$id");
							$db->setQuery($query);
							$db->query();

							// target audiences
							$query = $db->getQuery(true);
							$query->delete($db->quoteName('#__ta_calendar_event_target_audiences'));
							$query->where($db->quoteName('event') . "=$id");
							$db->setQuery($query);
							$db->query();

							// grant programs
							$query = $db->getQuery(true);
							$query->delete($db->quoteName('#__ta_calendar_event_programs'));
							$query->where($db->quoteName('event') . "=$id");
							$db->setQuery($query);
							$db->query();
						}else{
							$return['message'] = 'You are not authorized to edit this event or some other error occured. Please contact us.';
							$return['status'] = 'error';

							// return the result
							echo json_encode($return);
							die();
						}

						// use the id of this record for all following queries
						$newId = $id;
					}else{
						$return['message'] = 'You are not authorized to edit this event.';
						$return['status'] = 'error';

						// return the result
						echo json_encode($return);
						die();
					}
				}
								
				// execute the query
				if($newId > 0){					
					// Store topic areas
					$query = $db->getQuery(true);
					$query->insert($db->quoteName('#__ta_calendar_event_topic_areas'));
					$query->columns($db->quoteName(array('event', 'topic_area')));
					foreach($topicAreas as $topicArea){
						$query->values($db->quote($newId) . ',' . $db->quote($topicArea));
					}
					$db->setQuery($query);
					$db->query();
					
					// target audiences
					$query = $db->getQuery(true);
					$query->insert($db->quoteName('#__ta_calendar_event_target_audiences'));
					$query->columns($db->quoteName(array('event', 'target_audience')));
					foreach($targetAudiences as $targetAudience){
						$query->values($db->quote($newId) . ',' . $db->quote($targetAudience));
					}
					$db->setQuery($query);
					$db->query();

					// grant programs
					$query = $db->getQuery(true);
					$query->insert($db->quoteName('#__ta_calendar_event_programs'));
					$query->columns($db->quoteName(array('event', 'program')));
					foreach($grantPrograms as $grantProgram){
						$query->values($db->quote($newId) . ',' . $db->quote($grantProgram));
					}
					$db->setQuery($query);
					$db->query();

					// post a successful save =)
					$return['message'] = 'Event saved!';
					$return['status'] = 'success';
				}else{
					$return['message'] = 'Unable to get event ID. Please contact us.';
					$return['status'] = 'error';
				}
			}else{
				$return['message'] = 'Unable to retrieve user organization. Please contact us.';
				$return['status'] = 'error';			
			} 
		}else{
			$return['message'] = 'Please correct the following:<ul>';
			foreach($warnings as $warning){
				$return['message'] .= '<li>' . $warning . '</li>';
			}
			$return['message'] .= '</ul>';
			$return['status'] = 'warning';
		}
	}else{
		// not all the required data was submitted (the user is up to no good)
		$return['message'] = 'Invalid request.';
		$return['status'] = 'error';
	}
}else{
	// this is a guest who should have never gotten this far
	$return['message'] = 'Access Denied. You must login to a valid account to complete this action.';
	$return['status'] = 'error';
}

// return the result
echo json_encode($return);
die();