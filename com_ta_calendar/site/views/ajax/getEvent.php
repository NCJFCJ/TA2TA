<?php
/**
 * @package     com_ta_calendar
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */
  
// no direct access
defined('_JEXEC') or die;

// include the helper
require_once(JPATH_COMPONENT_SITE . '/helpers/ta_calendar.php');

/* Get the permission level
 * 0 = Public (view only)
 * 1 = TA Provider (restricted to adding and editing own)
 * 2 = Administrator (full access and ability to edit)
 */
$permission_level = Ta_calendarHelper::getPermissionLevel();

// variables	
$return = array();
$return['data'] = new stdClass();
$return['message'] = '';
$return['status'] = '';

// get the timezone
$userTimezone = 'America/New_York';
if(filter_has_var(INPUT_POST, 'userTimezone')){
	$tmpTimezone = filter_input(INPUT_POST, 'userTimezone', FILTER_SANITIZE_STRING);
	if(in_array($tmpTimezone, DateTimeZone::listIdentifiers())){
		$userTimezone = $tmpTimezone;
	}
}
$userTimezone = new DateTimeZone($userTimezone);

// get the user organization
$user = JFactory::getUser();
$userOrg = Ta_calendarHelper::getUserOrg();

// Check that data was submitted via post, that the proper variable was received, and that we have a user
if($_SERVER['REQUEST_METHOD'] == 'POST'
	&& filter_has_var(INPUT_POST, 'event')
	&& filter_has_var(INPUT_POST, 'edit')
){
	// retrieve and sanitize the data
	$event = filter_input(INPUT_POST, 'event', FILTER_SANITIZE_NUMBER_INT);
	$edit = filter_input(INPUT_POST, 'edit', FILTER_SANITIZE_NUMBER_INT);

	// validate
	if($event > 0){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select(array(
			$db->quoteName('e.id'),
			$db->quoteName('e.org'),
			$db->quoteName('pr.name', 'org_name'),
			$db->quoteName('e.start'),
			$db->quoteName('e.end'),
			$db->quoteName('e.title'),
			$db->quoteName('e.summary'),
			$db->quoteName('e.type'),
			$db->quoteName('et.name', 'type_name'),
			$db->quoteName('e.event_url'),
			$db->quoteName('e.open'),
			$db->quoteName('e.registration_url'),
			$db->quoteName('e.provider_project'),
			$db->quoteName('pj.title', 'provider_project_name'),
			$db->quoteName('e.created'),
			$db->quoteName('uc.name', 'created_by'),
			$db->quoteName('e.modified'),
			$db->quoteName('um.name', 'modified_by'),
			$db->quoteName('e.deleted'),
			$db->quoteName('ud.name', 'deleted_by'),
			$db->quoteName('e.approved'),
			$db->quoteName('a.name', 'approved_by') ,
			$db->quoteName('e.city'),
			$db->quoteName('e.territory'),
			$db->quoteName('e.timezone'),
			$db->quoteName('tz.abbr', 'timezone_abbr')
		));
		$query->from($db->quoteName('#__ta_calendar_events', 'e'));
		$query->join('LEFT', $db->quoteName('#__ta_providers', 'pr') . ' ON (' . $db->quoteName('pr.id') . ' = ' . $db->quoteName('e.org') . ')');
		$query->join('LEFT', $db->quoteName('#__ta_calendar_event_types', 'et') . ' ON (' . $db->quoteName('et.id') . ' =  ' . $db->quoteName('e.type') . ')');
		$query->join('LEFT', $db->quoteName('#__tapd_provider_projects', 'pj') . ' ON (' . $db->quoteName('pj.id') . ' =  ' . $db->quoteName('e.provider_project') . ')');
		$query->join('LEFT', $db->quoteName('#__ta_calendar_timezones', 'tz') . ' ON (' . $db->quoteName('tz.description') . ' = ' . $db->quoteName('e.timezone') . ')');
		$query->join('LEFT', $db->quoteName('#__users', 'uc') . ' ON (' . $db->quoteName('uc.id') . ' = ' . $db->quoteName('e.created_by') . ')');
		$query->join('LEFT', $db->quoteName('#__users', 'um') . ' ON (' . $db->quoteName('um.id') . ' = ' . $db->quoteName('e.modified_by') . ')');
		$query->join('LEFT', $db->quoteName('#__users', 'ud') . ' ON (' . $db->quoteName('ud.id') . ' = ' . $db->quoteName('e.deleted_by') . ')');
		$query->join('LEFT', $db->quoteName('#__users', 'a') . ' ON (' . $db->quoteName('a.id') . ' = ' . $db->quoteName('e.approved_by') . ')');
		$query->where($db->quoteName('e.state') . '=1 AND ' . $db->quoteName('e.id') . '=' . $db->quote($event));
		$db->setQuery($query, 0, 1);
			
		if($eventData = $db->loadObject()){
			// check if this user can edit
			if(!$edit 
			|| ($permission_level == 2
				|| ($permission_level == 1 
					&& $userOrg == $eventData->org
				   )
			   )
			){
				// grab the topic areas
				$subquery = $db->getQuery(true);
				$subquery->select($db->quoteName('eta.topic_area'));
				$subquery->from($db->quoteName('#__ta_calendar_event_topic_areas', 'eta'));
				$subquery->where($db->quoteName('eta.event') . " = " . $db->quote($event));
				$query = $db->getQuery(true);
				$query->select($db->quoteName(array(
					'ta.id',
					'ta.name'
				)));
				$query->from($db->quoteName('#__ta_calendar_topic_areas', 'ta'));
				$query->where($db->quoteName('ta.id') . " IN (" . $subquery . ")");
				$query->order($db->quoteName('ta.name') . " ASC");
				$db->setQuery($query);
				if($topic_areas = $db->loadObjectList()){
					// grab target audiences
					$subquery = $db->getQuery(true);
					$subquery->select($db->quoteName('eta.target_audience'));
					$subquery->from($db->quoteName('#__ta_calendar_event_target_audiences', 'eta'));
					$subquery->where($db->quoteName('eta.event') . " = " . $db->quote($event));
					$query = $db->getQuery(true);
					$query->select($db->quoteName(array(
						'ta.id',
						'ta.name'
					)));
					$query->from($db->quoteName('#__target_audiences', 'ta'));
					$query->where($db->quoteName('ta.id') . " IN (" . $subquery . ")");
					$query->order($db->quoteName('ta.name') . ' ASC');
					$db->setQuery($query);
					if($target_audiences = $db->loadObjectList()){
						if($eventData->open){
							// grab grant programs
							$subquery = $db->getQuery(true);
							$subquery->select($db->quoteName('ep.program'));
							$subquery->from($db->quoteName('#__ta_calendar_event_programs', 'ep'));
							$subquery->where($db->quoteName('ep.event') . ' = ' . $db->quote($event));
							$query = $db->getQuery(true);
							$query->select($db->quoteName(array(
								'gp.id',
								'gp.name'
							)));
							$query->from($db->quoteName('#__grant_programs', 'gp'));
							$query->where($db->quoteName('gp.id') . ' IN (' . $subquery . ')');
							$query->order($db->quoteName('gp.name') . ' ASC');
							$db->setQuery($query);
							$grant_programs = $db->loadObjectList();
						}
						if(!$eventData->open || isset($grant_programs)){
							// process the start and end dates
							$eventStart = ($eventData->start == '0000-00-00 00:00:00' ? false : new DateTime($eventData->start, new DateTimeZone('UTC')));
							$eventEnd = ($eventData->end == '0000-00-00 00:00:00' ? false : new DateTime($eventData->end, new DateTimeZone('UTC')));	

							// determine which timezone to use
							if(strlen($eventData->timezone) <= 5){
								$eventData->timezone = timezone_name_from_abbr($eventData->timezone);
							}
							$event_timezone = $eventData->timezone;
							if(in_array($event_timezone, DateTimeZone::listIdentifiers())){
								// use the event specific timezone (preferred)
								$timezone = new DateTimeZone($event_timezone);
							}else{
								// use the user's timezone
								$timezone = $userTimezone;
							}

							// update each date time to the proper timezone
							if($eventStart){
								$eventStart->setTimezone($timezone);
							}
							if($eventEnd){
								$eventEnd->setTimezone($timezone);
							}

							// configure the date string
							$dateString = '';					
							if($eventStart->format('Y-m-d') == $eventEnd->format('Y-m-d')){
								// single day
								$dateString = $eventStart->format('M j, Y g:ia') . ' - ' . $eventEnd->format('g:ia') . ' ' . $eventData->timezone_abbr;
							}else{
								// multi-day
								$dateString = $eventStart->format('M j, Y g:ia') . ' - ' . $eventEnd->format('M j, Y g:ia') . ' ' . $eventData->timezone_abbr;
							}

							// begin building the return object
							$return['data'] = $eventData;
							$return['data']->caldate = $eventStart->format('Ymd');
							$return['data']->startdate = $eventStart->format('m-d-Y');
							$return['data']->starttime = $eventStart->format('g:ia'); 
							$return['data']->enddate = $eventEnd->format('m-d-Y');
							$return['data']->endtime = $eventEnd->format('g:ia');
							$return['data']->date_string = $dateString;
							if($eventData->open){
								$return['data']->grant_programs = $grant_programs;
							}
							$return['data']->target_audiences = $target_audiences;
							$return['data']->topic_areas = $topic_areas;

							// process data visible to event owners and admins
							if($permission_level == 2 || ($permission_level == 1 && $userOrg == $eventData->org)){
								// create date time objects from the database data
								$eventCreated = ($eventData->created == '0000-00-00 00:00:00' ? false : new DateTime($eventData->created, new DateTimeZone('UTC')));
								$eventModified = ($eventData->modified == '0000-00-00 00:00:00' ? false : new DateTime($eventData->modified, new DateTimeZone('UTC')));
								$eventDeleted = ($eventData->deleted == '0000-00-00 00:00:00' ? false : new DateTime($eventData->deleted, new DateTimeZone('UTC')));
								$eventApproved = ($eventData->approved == '0000-00-00 00:00:00' ? false : new DateTime($eventData->approved, new DateTimeZone('UTC')));
								
								// update each date time to the user's timezone
								if($eventCreated){
									$eventCreated->setTimezone($timezone);
									$return['data']->created = $eventCreated->format('M j, Y g:ia');
								}
								if($eventModified){
									$eventModified->setTimezone($timezone);
									$return['data']->modified = $eventModified->format('M j, Y g:ia');
								}
								if($eventDeleted){
									$eventDeleted->setTimezone($timezone);
									$return['data']->deleted = $eventDeleted->format('M j, Y g:ia');
								}
								if($eventApproved){
									$eventApproved->setTimezone($timezone);	
									$return['data']->approved = $eventApproved->format('M j, Y g:ia');
								}

								// set editing flag
								$return['data']->can_edit = true;
							}else{
								// determine whether or not the event was approved
								$eventApproved = ($eventData->approved == '0000-00-00 00:00:00' ? false : true);
							
								// unset protected data
								unset($return['data']->id);
								unset($return['data']->created);
								unset($return['data']->created_by);
								unset($return['data']->modified);
								unset($return['data']->modified_by);
								unset($return['data']->deleted);
								unset($return['data']->deleted_by);
								unset($return['data']->approved);
								unset($return['data']->approved_by);

								// set editing flag
								$return['data']->can_edit = false;
							}

							// complete the return object
							$return['data']->approved_status = (!$eventApproved ? 0 : 1);
							
							// set the status
							$return['status'] = 'success';
						}else{
							$return['message'] = 'Unable to retrieve grant programs. Please contact us.';
							$return['status'] = 'error';
						}
					}else{
						$return['message'] = 'Unable to retrieve target audiences. Please contact us.';
						$return['status'] = 'error';
					}
				}else{
					$return['message'] = 'Unable to retrieve topic areas. Please contact us.';
					$return['status'] = 'error';
				}
			}else{
				$return['message'] = 'Access denied.';
				$return['status'] = 'error';
			}
		}else{
			$return['message'] = 'Database query failed. Please contact us.';
			$return['status'] = 'error';
		}
	}else{
		$return['message'] = 'Unable to retrieve event details. Invalid ID provided. Please contact us.';
		$return['status'] = 'error';
	}
}else{
	$return['message'] = 'Invalid request recieved. Please contact us.';
	$return['status'] = 'error';
}

// return the result
echo json_encode($return);
die();