<?php
/**
 * @package     com_services
 * @copyright   Copyright (C) 2016 NCJFCJ. All rights reserved.
 * @author      Zadra Design - http://zadradesign.com
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * AJAX controller class.
 */
class ServicesControllerAjax extends ServicesController{
	public function __construct(){
	    parent::__construct();
	    $document = JFactory::getDocument();
	    $document->setType('raw');
	}
}