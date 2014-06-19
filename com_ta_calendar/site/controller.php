<?php
/**
* @version 1.3.0
* @package com_ta_calendar
* @copyright Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
* @license
* @author Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
*/
 
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class Ta_calendarController extends JControllerLegacy{
	function deleteEvent(){
		require_once('views/ajax/delete.php');
	}
	function getCalendar(){
		require_once('views/ajax/getCalendar.php');
	}
	function getEvent(){
		require_once('views/ajax/getEvent.php');
	}
	function getPrograms(){
		require_once('views/ajax/getPrograms.php');
	}
	function saveEvent(){
		require_once('views/ajax/save.php');
	}
	function viewEvent(){
		require_once('views/ajax/viewSingleEvent.php');
	}
}