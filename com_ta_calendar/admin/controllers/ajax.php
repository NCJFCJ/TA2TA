<?php
/**
 * @version     1.3.0
 * @package     com_ta_calendar
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * AJAX controller class.
 */
class Ta_calendarControllerAjax extends Ta_calendarController
{
	public function __construct()
	{
	    parent::__construct();
	    $document = JFactory::getDocument();
	    $document->setType('raw');
	}
}