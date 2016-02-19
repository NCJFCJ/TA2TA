<?php
/**
 * @package     com_ta_calendar
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

defined('_JEXEC') or die;

abstract class Ta_calendarHelper{
	/**
	 * Builds a nicely formatted HTML email
	 */
	public static function buildEmail($heading, $content){
		// build the message
		$message = '<html><body>';
		$message .= '<div style="width:100%!important;padding:0;margin:0;background-color:#807C7C">';
		$message .= '<table style="font-family:Helvetica;font-size:12px" border="0" cellpadding="0" cellspacing="0" width="100%">';
		$message .= '<tbody>';
		$message .= '<tr>';
		$message .= '<td style="padding:40px 0px" align="center">';
		$message .= '<table style="font-family:Helvetica;font-size:12px" align="center" border="0" cellpadding="0" cellspacing="0" width="640">';
		$message .= '<tbody>';
		$message .= '<tr>';
		$message .= '<td valign="top">';
		$message .= '<table style="background-color:#FFF;font-family:Helvetica;font-size:12px" border="0" cellpadding="0" cellspacing="0" width="650">';
		$message .= '<tbody>';
		$message .= '<tr>';
		$message .= '<td style="padding-bottom:10px" valign="top">';
		$message .= '<table border="0" cellpadding="0" cellspacing="0" width="650">';
		$message .= '<tbody>';
		$message .= '<tr>';
		$message .= '<td align="center"><a href="' . JURI::base() . '" target="_blank"><img alt="TA2TA" style="margin: 30px 0;" src="' . JURI::base() . 'templates/ta2ta/img/logo.png"></a></td>';
		$message .= '</tr>';
		$message .= '<tr style="background-color:#F19244;color:#FFF;font-size:30px;font-weight:bold;">';
		$message .= '<td align="center" style="padding:15px 0;">' . $heading . '</td>';
		$message .= '</tr>';
		$message .= '<tr>';
		$message .= '<td align="center" style="padding:30px;">' . $content . '</td>';
		$message .= '</tr>';
		$message .= '</tbody>';
		$message .= '</table>';
		$message .= '</td>';
		$message .= '</tr>';
		$message .= '</tbody>';
		$message .= '</table>';
		$message .= '</td>';
		$message .= '</tr>';
		$message .= '</tbody>';
		$message .= '</table>';
		$message .= '</td>';
		$message .= '</tr>';
		$message .= '</tbody>';
		$message .= '</table>';
		$message .= '</div>';
		$message .= '</body></html>';

		// return the message
		return $message;
	}
	
	/**
	 * Retrieves calendar events from the database
	 * @param int The level of permissions granted to this user. Default is public, 0
	 * @param array The filter values from the front end
	 * @param string The start datetime as a string formatted in the MySQL datetime format
	 * @param string The end datetime as a string formatted in the MySQL datetime format
	 * @param DateTimeZone A PHP DateTimeZone object corresponding to the user's selected timezone
	 * @return array List of objects containing event information, empty array on fail
	 */
	public static function getEvents($permission_level = 0, $filters, $firstDaySQL, $lastDaySQL, $userTimezone){
		// the return variable	
		$return = array();
		
		// get an sanitize filters
		$filters = Ta_calendarHelper::getSanitizedFilters($filters);
		
		// first, check that we actually need a query. If any filter group is set to none, no events will display
		if(!empty($filters->eventTypes)
			&& !empty($filters->grantPrograms)
			&& !empty($filters->targetAudiences)
			&& !empty($filters->topicAreas)
			&& !empty($filters->approved)){
			
			// get the database object
			$db = JFactory::getDbo();
			
			// construct the filter query
			$filter_query = $db->getQuery(true);
			$filter_query->select('DISTINCT ' . $db->quoteName('tar.event'));
			$filter_query->from($db->quoteName('#__ta_calendar_event_target_audiences','tar'));
			$filter_query->join('INNER', $db->quoteName('#__ta_calendar_event_programs','gra') . ' ON ' . $db->quoteName('gra.event') . '=' . $db->quotename('tar.event'));
			$filter_query->join('INNER', $db->quoteName('#__ta_calendar_event_topic_areas','top') . ' ON ' . $db->quoteName('top.event') . '=' . $db->quotename('tar.event'));
			$filter_query->where($db->quoteName('tar.target_audience') . ' IN (' . implode(',', $filters->targetAudiences) . ') AND ' . $db->quoteName('gra.program') . ' IN (' . implode(',', $filters->grantPrograms) . ') AND ' . $db->quoteName('top.topic_area') . ' IN (' . implode(',', $filters->topicAreas) . ')');

			// build the where clause for the main join
			$join_where = array();
			$join_where[] = $db->quoteName('eve.state') . '=' . $db->quote('1');
			$join_where[] = $db->quoteName('eve.start') . ' BETWEEN ' . $db->quote($firstDaySQL) . ' AND ' . $db->quote($lastDaySQL);
			$join_where[] = $db->quoteName('eve.type') . ' IN (' . implode(',', $filters->eventTypes) . ')';
			$join_where[] = $db->quoteName('eve.id') . ' IN (' . $filter_query . ')';

			// approved
			// if count is 2 or more, than show both
			if(count($filters->approved) < 2){
				if(!empty($filters->approved) && $filters->approved[0] == '0'){
					// only show unapproved
					$join_where[] = $db->quoteName('eve.approved') . " LIKE '0000-00-00 00:00:00'";
				}else{
					// only show approved
					$join_where[] = $db->quoteName('eve.approved') . " NOT LIKE '0000-00-00 00:00:00'";
				}
			}

			// construct the main query
			$query = $db->getQuery(true);			
			$query->select(array(
				$db->quoteName('eve.id'),
				$db->quoteName('eve.org'),
				$db->quoteName('pro.name', 'org_name'),
				$db->quoteName('eve.start'),
				$db->quoteName('eve.end'),
				$db->quoteName('eve.title'),
				$db->quoteName('eve.summary'),
				$db->quoteName('eve.type'),
				$db->quoteName('eve.event_url'),
				$db->quoteName('eve.open'),
				$db->quoteName('eve.registration_url'),
				$db->quoteName('eve.provider_project'),
				$db->quoteName('eve.created'),
				$db->quoteName('eve.created_by'),
				$db->quoteName('ucb.name', 'created_by_name'),
				$db->quoteName('eve.modified'),
				$db->quoteName('eve.modified_by'),
				$db->quoteName('umb.name', 'modified_by_name'),
				$db->quoteName('eve.approved'),
				$db->quoteName('eve.approved_by'),
				$db->quoteName('uab.name', 'approved_by_name'),
				$db->quoteName('eve.timezone')
			));
			// $query->union was not working, doing this for now
			$query->from($db->quoteName('#__ta_calendar_events', 'eve'));
			$query->join('LEFT', $db->quoteName('#__ta_providers', 'pro') . ' ON ' . $db->quoteName('eve.org') . ' = ' . $db->quoteName('pro.id'));
			$query->join('LEFT', $db->quoteName('#__users', 'ucb') . ' ON ' . $db->quoteName('eve.created_by') . ' = ' . $db->quoteName('ucb.id'));
			$query->join('LEFT', $db->quoteName('#__users', 'umb') . ' ON ' . $db->quoteName('eve.modified_by') . ' = ' . $db->quoteName('umb.id'));
			$query->join('LEFT', $db->quoteName('#__users', 'uab') . ' ON ' . $db->quoteName('eve.approved_by') . ' = ' . $db->quoteName('uab.id'));
			$query->where(implode(' AND ', $join_where));
			$query->order($db->quoteName('eve.start'), 'ASC');
			$db->setQuery($query);

			// execute the query and return the result
			try{
				$events = $db->loadObjectList();

				// process events before displaying
				foreach($events as &$event){
					// create date time objects for all date times
					$event->start = (empty($event->start) ? false : new DateTime($event->start, new DateTimeZone('UTC')));
					$event->end = (empty($event->end) ? false : new DateTime($event->end, new DateTimeZone('UTC')));
					$event->created = (empty($event->created) ? false : new DateTime($event->created, new DateTimeZone('UTC')));
					$event->modified = (empty($event->modified) ? false : new DateTime($event->modified, new DateTimeZone('UTC')));
					$event->checked_out_time = (empty($event->checked_out_time) ? false : new DateTime($event->checked_out_time, new DateTimeZone('UTC')));
					$event->approved = ($event->approved == '0000-00-00 00:00:00' ? false : new DateTime($event->approved, new DateTimeZone('UTC')));

					// determine which timezone to use
					$event_timezone = timezone_name_from_abbr($event->timezone);
					if(in_array($event_timezone, DateTimeZone::listIdentifiers())){
						// use the event specific timezone (preferred)
						$timezone = new DateTimeZone($event_timezone);
					}else{
						// use the user's timezone
						$timezone = $userTimezone;
					}
					
					// update each date time to the proper timezone
					if($event->start){
						$event->start->setTimezone($timezone);
					}
					if($event->end){
						$event->end->setTimezone($timezone);
					}
					if($event->created){
						$event->created->setTimezone($userTimezone);
					}
					if($event->modified){
						$event->modified->setTimezone($userTimezone);
					}
					if($event->checked_out_time){
						$event->checked_out_time->setTimezone($userTimezone);
					}
					if($event->approved){
						$event->approved->setTimezone($userTimezone);
					}
				}
				return $events;
			} catch (Exception $e) {
				// TO DO: Fix this. Error handling is not working in this function.
			   JError::raiseWarning(100, 'Unable to retrieve events. Please contact us.');
			}
			return $return;
		}
		return $return;
	}
	
	/**
	 * Retrieves the event types and returns an array keyed on the event type id
	 * 
	 * @return array
	 */
	 public static function getEventTypes(){
	 	// build the query	
	 	$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array(
			$db->quoteName('id'),
			$db->quoteName('name')
		));
		$query->from($db->quoteName('#__ta_calendar_event_types'));
		$db->setQuery($query);
	
		// execute the query and return the result
		return $db->loadAssocList('id');
	 }
	 
	/**
	 * Returns all grant programs 
	 */
	public static function getGrantPrograms(){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array(
			$db->quoteName('id'),
			$db->quoteName('name'),
			$db->quoteName('fund')
		));
		$query->from($db->quoteName('#__grant_programs'));
		$query->where($db->quoteName('state') . '=1');
		$query->order($db->quoteName('name') . ' ASC');
		$db->setQuery($query);
		return $db->loadAssocList('id');
	}
	
	/**
	 * Determines the permission level used for calendar tasks
	 * 
	 * @return int Permission level
	 * 0 = Public (view only)
	 * 1 = TA Provider (restricted to adding and editing own)
	 * 2 = Administrator (full access and ability to edit)
	 */
	 
	public static function getPermissionLevel(){
		// variables	
		$permission_level = 0;
		
		// get the user groups for this user
		$user_groups = JFactory::getUser()->getAuthorisedGroups();	
		
		// determine if this is a TA provider
		if(in_array(10, $user_groups)){
			$permission_level = 1;
		}
		
		// determine if this is an administrator
		if(in_array(7, $user_groups)
		|| in_array(8, $user_groups)
		|| in_array(11, $user_groups)
		|| in_array(12, $user_groups)){
			$permission_level = 2;
		}

		return $permission_level;
	}
	
	/**
	 * Gets all filters, sanitizes them, and returns them in one combined object
	 */
	public static function getSanitizedFilters($tmpFilters){
		$filters = new stdClass;	
		$filters->approved = (isset($tmpFilters['approved']) ? array_filter($tmpFilters['approved'], array('Ta_calendarHelper','is_string_integer')) : array());
		$filters->eventTypes = (isset($tmpFilters['eventTypes']) ? array_filter($tmpFilters['eventTypes'], array('Ta_calendarHelper','is_string_integer')) : array());
		$filters->grantPrograms = (isset($tmpFilters['grantPrograms']) ? array_filter($tmpFilters['grantPrograms'], array('Ta_calendarHelper','is_string_integer')) : array());
		$filters->targetAudiences = (isset($tmpFilters['targetAudiences']) ? array_filter($tmpFilters['targetAudiences'], array('Ta_calendarHelper','is_string_integer')) : array());
		$filters->topicAreas = (isset($tmpFilters['topicAreas']) ? array_filter($tmpFilters['topicAreas'], array('Ta_calendarHelper','is_string_integer')) : array());
		return $filters;
	}
	
	/**
	 * Gets the organization of the current user
	 */
	public static function getUserOrg(){
		// get the user's organization
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('profile_value'));
		$query->from($db->quoteName('#__user_profiles'));
		$query->where($db->quoteName('user_id') . '=' . $db->quote(JFactory::getUser()->id) . ' AND ' . $db->quoteName('profile_key') . ' = ' . $db->quote('profile.org'));
		$db->setQuery($query, 0, 1);
		
		// check that the query was successful
		if(!($org = $db->loadResult())){
			JError::raiseWarning(100, 'Unable to determine your organization.');
			return 0;
		}
		
		// remove quotes
		$org = substr($org, 1, -1);
		
		// return the result
		return (int)$org;
	}
	
	/**
	 * Used in filtering, returns true if the input value is a string representing an integer
	 * @param var A single value to test
	 * @return boolean true if integer string, false otherwise
	 */
	public static function is_string_integer($var){
		return ((string)(int)$var == $var);
	}
}

