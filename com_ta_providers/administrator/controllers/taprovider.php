<?php
/**
 * @package     com_ta_providers
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ta2ta.org
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