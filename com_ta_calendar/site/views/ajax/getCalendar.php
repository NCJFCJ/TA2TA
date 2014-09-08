<?php
/**
 * @package     com_ta_calendar
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;
		
// Get the posted data
$app			= JFactory::getApplication();
$calDate		= $app->input->get('calDate', '', 'string');
$calTimezone 	= new DateTimeZone($app->input->get('calTimezone', 'America/New_York', 'string'));
$calView 		= $app->input->get('calView', 'month', 'string');
$curEvent		= $app->input->get('curEvent', 0, 'int');
$filters		= $app->input->get('filters', array(), 'array');

// if a current event is specified, update the date
if($curEvent){
	// get the event start date from the database
	$db = JFactory::getDBO();
	$query = $db->getQuery(true);
	$query->select($db->quoteName('start'));
	$query->from($db->quoteName('#__ta_calendar_events'));
	$query->where($db->quoteName('id') . '=' . $curEvent);
	$db->setQuery($query, 0, 1);
	if($start = $db->loadResult()){
		$start = new DateTime($start, new DateTimeZone('UTC'));
		$start->setTimezone($calTimezone);
		$calDate = $start->format('Ymd');

	};
}

// Require the proper view
switch($calView){
	case 'list' :
		require_once('tmpl/list.php');
		break;
	case 'week' :
		require_once('tmpl/week.php');
		break;
	default :
		require_once('tmpl/month.php');
		break;
}

die();