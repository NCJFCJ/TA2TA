<?php
/**
 * @version     1.2.0
 * @package     com_ta_calendar
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */
  
// no direct access
defined('_JEXEC') or die;

// variables	
$return = array();	
$return['canEdit'] = false;
$return['message'] = '';
$return['status'] = '';

/* Get the permission level
 * 0 = Public (view only)
 * 1 = TA Provider (restricted to adding and editing own)
 * 2 = Administrator (full access and ability to edit)
 */
require_once(JPATH_COMPONENT_SITE . '/helpers/ta_calendar.php');
$permission_level = Ta_calendarHelper::getPermissionLevel();

// get the timezone
$calTimezone = 'America/New_York';
if(filter_has_var(INPUT_POST, 'calTimezone')){
	$tmpTimezone = filter_input(INPUT_POST, 'calTimezone', FILTER_SANITIZE_STRING);
	if(in_array($tmpTimezone, DateTimeZone::listIdentifiers())){
		$calTimezone = $tmpTimezone;
	}
}
$calTimezone = new DateTimeZone($calTimezone);

// Check that data was submitted via post and that the proper variables were received
if ($_SERVER['REQUEST_METHOD'] == "POST"
	&& filter_has_var(INPUT_POST, 'event')
){
	// retrieve and sanitize the data
	$event = filter_input(INPUT_POST, 'event', FILTER_SANITIZE_NUMBER_INT);
	
	// validate
	if(empty($event) || $event <= 0){
		$return['message'] = 'Unable to retrieve event details. Invalid ID provided. Please contact us.';
		$return['status'] = 'error';
	}else{
		// mark this event as deleted, if its organization matches
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select(
			$db->quoteName('e.org') . ',' .
			$db->quoteName('p.name', 'org_name') . ',' .
			$db->quoteName('e.start') . ',' .
			$db->quoteName('e.end') . ',' .
			$db->quoteName('e.title') . ',' .
			$db->quoteName('e.summary') . ',' .
			$db->quoteName('t.name','type') . ',' .
			$db->quoteName('e.event_url') . ',' .
			$db->quoteName('e.open') . ',' .
			$db->quoteName('e.registration_url') . ',' .
			$db->quoteName('pp.title', 'project') . ',' .
			$db->quoteName('e.created') . ',' .
			$db->quoteName('uc.name', 'created_by') . ',' .
			$db->quoteName('e.modified') . ',' .
			$db->quoteName('um.name', 'modified_by') . ',' .
			$db->quoteName('e.deleted') . ',' .
			$db->quoteName('ud.name', 'deleted_by') . ',' .
			$db->quoteName('e.approved') . ',' .
			$db->quoteName('a.name', 'approved_by')
		);
		$query->from($db->quoteName('#__ta_calendar_events', 'e'));
		$query->join('LEFT', $db->quoteName('#__ta_providers', 'p') . ' ON (' . $db->quoteName('p.id') . ' = ' . $db->quoteName('e.org') . ')');
		$query->join('LEFT', $db->quoteName('#__ta_calendar_event_types', 't') . ' ON (' . $db->quoteName('t.id') . ' = ' . $db->quoteName('e.type') . ')');
		$query->join('LEFT', $db->quoteName('#__tapd_provider_projects', 'pp') . ' ON (' . $db->quoteName('pp.id') . ' = ' . $db->quoteName('e.provider_project') . ')');
		$query->join('LEFT', $db->quoteName('#__users', 'uc') . ' ON (' . $db->quoteName('uc.id') . ' = ' . $db->quoteName('e.created_by') . ')');
		$query->join('LEFT', $db->quoteName('#__users', 'um') . ' ON (' . $db->quoteName('um.id') . ' = ' . $db->quoteName('e.modified_by') . ')');
		$query->join('LEFT', $db->quoteName('#__users', 'ud') . ' ON (' . $db->quoteName('ud.id') . ' = ' . $db->quoteName('e.deleted_by') . ')');
		$query->join('LEFT', $db->quoteName('#__users', 'a') . ' ON (' . $db->quoteName('a.id') . ' = ' . $db->quoteName('e.approved_by') . ')');
		$query->where($db->quoteName('e.state') . '=1 AND ' . $db->quoteName('e.id') . '=' . $db->quote($event));
		$db->setQuery($query, 0, 1);
		
		if($eventData = $db->loadObject()){
			// get the organization of the user
			$user = JFactory::getUser();
			$userOrg = Ta_calendarHelper::getUserOrg();
			
			// check if this user can edit
			if($permission_level == 2
			|| ($permission_level == 1 && $userOrg == $eventData->org)){
				$return['canEdit'] = true;
			}

			// if the user cannot edit, remove certian data
			if(!$return['canEdit']){
				unset($eventData->created);
				unset($eventData->created_by);
				unset($eventData->modified);
				unset($eventData->modified_by);
				unset($eventData->deleted);
				unset($eventData->deleted_by);
				unset($eventData->approved_by);
			}
			
			// create date time objects from the database data
			$eventStart = $eventData->start = (empty($eventData->start) ? false : new DateTime($eventData->start, new DateTimeZone('UTC')));
			$eventEnd = $eventData->end = (empty($eventData->end) ? false : new DateTime($eventData->end, new DateTimeZone('UTC')));	
			$eventCreated = $eventData->created = (empty($eventData->created) ? false : new DateTime($eventData->created, new DateTimeZone('UTC')));
			$eventModified = $eventData->modified = (empty($eventData->modified) ? false : new DateTime($eventData->modified, new DateTimeZone('UTC')));
			$eventDeleted = $eventData->deleted = (empty($eventData->deleted) ? false : new DateTime($eventData->deleted, new DateTimeZone('UTC')));
			$eventApproved = $eventData->approved = ($eventData->approved == '0000-00-00 00:00:00' ? false : new DateTime($eventData->approved, new DateTimeZone('UTC')));
			
			// update each date time to the user's timezone
			if($eventStart){
				$eventStart->setTimezone($calTimezone);
			}
			if($eventEnd){
				$eventEnd->setTimezone($calTimezone);
			}
			if($eventCreated){
				$eventCreated->setTimezone($calTimezone);
			}
			if($eventModified){
				$eventModified->setTimezone($calTimezone);
			}
			if($eventDeleted){
				$eventDeleted->setTimezone($calTimezone);
			}
			if($eventApproved){
				$eventApproved->setTimezone($calTimezone);
			}
			
			// configure the date string
			$dateString = '';					
			if($eventStart->format('Y-m-d') == $eventEnd->format('Y-m-d')){
				// single day
				$dateString = $eventStart->format('M j, Y g:i') . substr($eventStart->format('a'), 0 , -1) . ' - ' . $eventEnd->format('g:i') . substr($eventEnd->format('a'), 0 , -1);
			}else{
				// multi-day
				$dateString = $eventStart->format('M j, Y g:i') . substr($eventStart->format('a'), 0 , -1) . ' - ' . $eventEnd->format('M j, Y g:i') . substr($eventEnd->format('a'), 0 , -1);
			}
			
			// compile the HTML and return
			$return['message'] = '<table>';
			$return['message'] .= '<tr><td style="width: 120px;"><strong>Title</strong></td><td>' . $eventData->title . '</td></tr>';
			$return['message'] .= '<tr><td><strong>Organization</strong></td><td>' . $eventData->org_name . '</td></tr>';
			$return['message'] .= '<tr><td><strong>Project</strong></td><td>' . $eventData->project . '</td></tr>';
			$return['message'] .= '<tr><td><strong>Date</strong></td><td>' . $dateString . '</td></tr>';
			$return['message'] .= '<tr><td><strong>Event Type</strong></td><td>' . $eventData->type . '</td></tr>';
			$return['message'] .= '<tr><td><strong>Summary</strong></td><td>' . $eventData->summary . '</td></tr>';
			if(!empty($eventData->event_url)){
				$return['message'] .= '<tr><td><strong>More Information</strong></td><td><a href="' . $eventData->event_url . '">' . $eventData->event_url . '</a></td></tr>';
			}
			if(!empty($eventData->registration_url)){
				$return['message'] .= '<tr><td><strong>Registration</strong></td><td><a href="' . $eventData->registration_url . '">' . $eventData->registration_url . '</a></td></tr>';
			}
			$return['message'] .= '<tr><td><strong>OVW Approved</strong></td><td>' . (empty($eventApproved) ? 'No' : 'Yes') . '</td></tr>';
					
					
					
/*
$db->quoteName('e.created') . ',' .
$db->quoteName('uc.name', 'created_by') . ',' .
$db->quoteName('e.modified') . ',' .
$db->quoteName('um.name', 'modified_by') . ',' .
$db->quoteName('e.deleted') . ',' .
$db->quoteName('ud.name', 'deleted_by') . ',' .
$db->quoteName('e.approved') . ',' .
$db->quoteName('a.name', 'approved_by')		
*/							
					
			//$return['message'] .= '<tr><td><strong>Title</strong></td><td>' . $eventData->title . '</td></tr>';
			
			$return['message'] .= '</table>';
			$return['status'] = 'success';
		}else{
			$return['message'] = 'Unable to retrieve event data. Please contact us.';
			$return['status'] = 'error';
		}		
	}
}else{
	// not all the required data was submitted (the user is up to no good)
	$return['message'] = 'Invalid request.';
	$return['status'] = 'error';
}


// return the result
echo json_encode($return);
die();