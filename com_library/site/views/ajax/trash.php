<?php
/**
 * @package     com_library
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @author      NCJFCJ <zdraper@ncjfcj.org> - http://ncjfcj.org
 */
  
// no direct access
defined('_JEXEC') or die;

// variables	
$return = array();	
$return['message'] = '';
$return['status'] = '';

// Check that data was submitted via post and that the proper variables were received
if ($_SERVER['REQUEST_METHOD'] == "POST"
	&& filter_has_var(INPUT_POST, 'ids')
){
	$ids = array();
	foreach($_POST['ids'] as $unclean_id){
		$ids[] = filter_var($unclean_id, FILTER_SANITIZE_NUMBER_INT);
	}
	
	$return['message'] = '';
	$return['status'] = 'success';
}else{
	$return['message'] = 'Invalid input recieved.';
	$return['status'] = 'error';
}

// return the result
echo json_encode($return);
die();