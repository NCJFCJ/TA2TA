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
		&& filter_has_var(INPUT_POST, 'event')
	){		
		// retrieve and sanitize the data
		$event = filter_input(INPUT_POST, 'event', FILTER_SANITIZE_NUMBER_INT);
		
		// validate
		if(empty($event) || $event <= 0){
			$return['message'] = 'Unable to delete event. Invalid ID provided. Please contact us.';
			$return['status'] = 'error';
		}else{
			// get the organization of the user
			$user = JFactory::getUser();
			$userOrg = Ta_calendarHelper::getUserOrg();

			// mark this event as deleted, if its organization matches
			$db = JFactory::getDBO();
			$fields = array(
				$db->quoteName('state') . '=-2',
				$db->quoteName('deleted') . '=NOW()',
				$db->quoteName('deleted_by') . '=' . $user->id
			);
			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__ta_calendar_events'));
			$query->set($fields);
			$query->where($db->quoteName('id') . '=' . $event . ' AND ' . $db->quoteName('org') . '=' . $userOrg);
			$db->setQuery($query);
			if($db->query()){
				$return['message'] = 'Event deleted!';
				$return['status'] = 'success';
			}else{
				$return['message'] = 'Unable to delete event. Please contact us.';
				$return['status'] = 'error';
			}
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