<?php
/**
 * @version     2.0.0
 * @package     com_library
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */


// no direct access
defined('_JEXEC') or die;

// Access check.
if(!JFactory::getUser()->authorise('core.manage', 'com_library')){
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

$controller	= JControllerLegacy::getInstance('Library');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();