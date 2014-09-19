<?php
/**
 * @package     com_ta_calendar
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;

/**
 * event Table class
 */
class TableSettings extends JTable{

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db){
        parent::__construct('#__ta_calendar_user_settings', 'user', $db);
    } 
}