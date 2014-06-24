<?php
/**
 * @package     com_help_videos
 * @copyright   Copyright (C) 2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Video controller class.
 */
class Help_videosControllerVideo extends JControllerForm
{

    function __construct() {
        $this->view_list = 'videos';
        parent::__construct();
    }

}