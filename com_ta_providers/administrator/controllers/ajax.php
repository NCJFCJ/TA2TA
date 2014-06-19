<?php
/**
 * @package     com_ta_providers
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * AJAX controller class.
 */
class Ta_providersControllerAjax extends Ta_providersController
{
	public function __construct()
	{
	    parent::__construct();
	    $document = JFactory::getDocument();
	    $document->setType('raw');
	}
}