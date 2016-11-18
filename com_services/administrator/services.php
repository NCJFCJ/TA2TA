<?php
/**
 * @package     com_services
 * @copyright   Copyright (C) 2016 NCJFCJ. All rights reserved.
 * @author      Zadra Design - http://zadradesign.com
 */

// no direct access
defined('_JEXEC') or die;

// Access check
if(!JFactory::getUser()->authorise('core.manage', 'com_services')) {
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

$controller	= JControllerLegacy::getInstance('Services');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();