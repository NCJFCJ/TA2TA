<?php
/**
 * @package     com_services
 * @copyright   Copyright (C) 2016 NCJFCJ. All rights reserved.
 * @author      Zadra Design - http://zadradesign.com
 */
 
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class ServicesController extends JControllerLegacy{
	function fileUpload(){
		require_once('views/ajax/fileUpload.php');
	}
}