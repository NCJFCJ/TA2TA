<?php
/**
 * @version     1.0.0
 * @package     com_grant_programs
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Grant_program controller class.
 */
class Grant_programsControllerGrant_program extends JControllerForm
{

    function __construct() {
        $this->view_list = 'grant_programs';
        parent::__construct();
    }

}