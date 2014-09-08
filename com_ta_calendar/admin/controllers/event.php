<?php
/**
 * @package     com_ta_calendar
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Event controller class.
 */
class Ta_calendarControllerEvent extends JControllerForm{
    function __construct() {
        $this->view_list = 'events';
        parent::__construct();
    }
}