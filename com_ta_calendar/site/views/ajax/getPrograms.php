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
$return['status'] = '';

// Check that data was submitted via post, that the proper variable was received, and that we have a user
if ($_SERVER['REQUEST_METHOD'] == "POST"
	&& filter_has_var(INPUT_POST, 'project')
){
	// retrieve and sanitize the data
	$project = filter_input(INPUT_POST, 'project', FILTER_SANITIZE_NUMBER_INT);

	// validate
	if(empty($project) || $project <= 0){
		$return['message'] = 'Unable to retrieve grant programs. Invalid project provided. Please contact us.';
		$return['status'] = 'error';
	}else{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('program'));
		$query->from($db->quoteName('#__tapd_project_programs'));
		$query->where($db->quoteName('project') . '=' . $db->quote($project));
		$db->setQuery($query);
			
		if($return['data'] = $db->loadColumn()){
			$return['status'] = 'success';
		}else{
			$return['message'] = 'Unable to retrieve grant programs.';
			$return['status'] = 'error';
		}
	}
}

// return the result
echo json_encode($return);
die();