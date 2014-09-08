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

// variables	
$return = array();
$return['data'] = new stdClass();
$return['message'] = '';
$return['status'] = 'success';

// Check that data was submitted via post, that the proper variable was received, and that we have a user
if ($_SERVER['REQUEST_METHOD'] == "POST"
	&& filter_has_var(INPUT_POST, 'org')
){
	// retrieve and sanitize the data
	$org = filter_input(INPUT_POST, 'org', FILTER_SANITIZE_NUMBER_INT);

	// validate
	if(empty($org) || $org <= 0){
		$return['message'] = 'Unable to retrieve projects. Invalid organization provided.';
		$return['status'] = 'error';
	}else{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select(array(
			$db->quoteName('id'),
			$db->quoteName('title')
		));
		$query->from($db->quoteName('#__tapd_provider_projects'));
		$query->where($db->quoteName('state') . '=1 AND ' . $db->quoteName('provider') . '=' . $org);
		$query->order($db->quoteName('title') . ' ASC');
		$db->setQuery($query);
		if(!$return['data'] = $db->loadObjectList()){
			$return['message'] = 'This organization has no TA Projects. Please add one before continuing.';
			$return['status'] = 'error';	
		}
	}
}

// return the result
echo json_encode($return);
die();