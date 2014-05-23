<?php
/**
 * @version     1.3.0
 * @package     com_ta_calendar
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Events list controller class.
 */
class Ta_calendarControllerEvents extends Ta_calendarController
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Events', $prefix = 'Ta_calendarModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
		
	}
}