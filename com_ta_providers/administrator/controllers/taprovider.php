<?php
/**
 * @package     com_ta_providers
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Taprovider controller class.
 */
class Ta_providersControllerTaprovider extends JControllerForm
{

    function __construct() {
        $this->view_list = 'taproviders';
        parent::__construct();
    }

}