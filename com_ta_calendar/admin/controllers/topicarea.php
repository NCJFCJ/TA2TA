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

jimport('joomla.application.component.controllerform');

/**
 * Topicarea controller class.
 */
class Ta_calendarControllerTopicarea extends JControllerForm
{

    function __construct() {
        $this->view_list = 'topicareas';
        parent::__construct();
    }

}