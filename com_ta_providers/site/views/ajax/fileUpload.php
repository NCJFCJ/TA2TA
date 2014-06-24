<?php
/**
 * @package     com_ta_providers
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

// variables	
$return = array();	
$return['message'] = '';
$return['status'] = '';

// Check that data was submitted via post and that the proper variables were received
if($_SERVER['REQUEST_METHOD'] == 'POST'
	&& is_uploaded_file($_FILES['jform']['tmp_name']['logoFile'])
){		
	// create an empty class to hold information on the file we are uploading
	$file = new stdClass;
	$file->name = '';
	$file->type = '';
	$file->tmp_name = '';
	$file->error = 0;
	$file->size = '';

	// get information about the uploaded file
	if(isset($_FILES['jform'])){
		$file->name = $_FILES['jform']['name']['logoFile'];
		$file->type = $_FILES['jform']['type']['logoFile'];
		$file->tmp_name = $_FILES['jform']['tmp_name']['logoFile'];
		$file->error = $_FILES['jform']['error']['logoFile'];
		$file->size = $_FILES['jform']['size']['logoFile'];
	}else{
		$return['message'] = JText::_('COM_TA_PROVIDERS_NO_FILE');
		$return['status'] = 'error';
		echo json_encode($return);
		die();
	}

	// check if an error occured
	if($file->error){
		switch($file->error){
			case 1:
				$return['message'] = JText::_('COM_TA_PROVIDERS_FILE_TOO_LARGE');
				$return['status'] = 'error';
	        case 2:
				$return['message'] = JText::_('COM_TA_PROVIDERS_FILE_TOO_LARGE');
				$return['status'] = 'error';
	        case 3:
				$return['message'] = JText::_('COM_TA_PROVIDERS_FILE_UPLOAD_FAILED');
				$return['status'] = 'error';
	        case 4:
				$return['message'] = JText::_('COM_TA_PROVIDERS_NO_FILE');
				$return['status'] = 'error';
		}
		echo json_encode($return);
		die();
	}

	//check the filesize (no more than 100KB)
	if($file->size > 102400){
		$return['message'] = JText::_('COM_TA_PROVIDERS_FILE_TOO_LARGE');
		$return['status'] = 'error';
		echo json_encode($return);
		die();
	}

	//check that the file extension is ok
	$allowed_extensions = array('jpg', 'gif', 'png');
	$filePathInfo = pathinfo('/'.$file->name);
	if(!in_array($filePathInfo['extension'], $allowed_extensions)){
		$return['message'] = JText::_('COM_TA_PROVIDERS_INVALID_FILE_TYPE');
		$return['status'] = 'error';
		echo json_encode($return);
		die();
	}

	// get the ID of the user's organization
	require_once(JPATH_COMPONENT . '/helpers/ta_providers.php');
	$userOrg = Ta_providersHelper::getUserOrg();
	
	// get the name from the organization ID
	$db = JFactory::getDBO();
	$query = $db->getQuery(true);
	$query->select($db->quoteName('name'));
	$query->from('#__ta_providers');
	$query->where($db->quoteName('id') . '=' . $db->quote($userOrg));
	$db->setQuery($query, 0 ,1);
	if(!($orgName = $db->loadResult())){
		$orgName = 'undeterminable_' . $userOrg;
	}
	
	//lose any special characters in the filename
	$file->name = strtolower(preg_replace('/[^A-Za-z0-9]/i', '-', substr($orgName,0,46))) . '.' . $filePathInfo['extension'];

	// construct the file path
	$uploadPath = JPATH_SITE . '/media/com_ta_providers/tmp/' . $file->name;
	
	// if a file already exists, delete it	
	if(file_exists($uploadPath)){
		unlink($uploadPath);
	}	

	//move the uploaded file to its temporary home
	if(JFile::upload($file->tmp_name, $uploadPath)){
		$size = getimagesize($uploadPath);
		$return['height'] = $size[1];
		$return['width'] = $size[0];
		$return['message'] = $file->name;
		$return['status'] = 'success';
	}else{
		$return['message'] = JText::_('COM_TA_PROVIDERS_FILE_MOVE_ERROR');
		$return['status'] = 'error';
	}
}else{
	// not all the required data was submitted (the user is up to no good)
	$return['message'] = 'Invalid request.';
	$return['status'] = 'error';
}

// return the result
echo json_encode($return);
die();