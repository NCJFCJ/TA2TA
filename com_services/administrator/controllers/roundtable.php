<?php
/**
 * @package     com_services
 * @copyright   Copyright (C) 2016 NCJFCJ. All rights reserved.
 * @author      Zadra Design - http://zadradesign.com
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Roundtable controller class.
 */
class ServicesControllerRoundtable extends JControllerForm{

  function __construct(){
    $this->view_list = 'roundtables';
    parent::__construct();
  }
}