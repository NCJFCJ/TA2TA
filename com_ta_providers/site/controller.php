<?php
/**
 * @package     com_ta_providers
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */
 
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class Ta_providersController extends JControllerLegacy{
	function fileUpload(){
		require_once('views/ajax/fileUpload.php');
	}
}