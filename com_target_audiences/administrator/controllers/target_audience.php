<?php
/**
 * @version     1.0.0
 * @package     com_target_audiences
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
class Target_audiencesControllerTarget_audience extends JControllerForm
{

    function __construct() {
        $this->view_list = 'target_audiences';
        parent::__construct();
    }
}