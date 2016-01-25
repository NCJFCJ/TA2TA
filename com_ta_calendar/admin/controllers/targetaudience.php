<?php
/**
 * @version     1.0.0
 * @package     com_ta_calendar
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Targetaudience controller class.
 */
class Ta_calendarControllerTargetaudience extends JControllerForm
{

    function __construct() {
        $this->view_list = 'targetaudiences';
        parent::__construct();
    }

}