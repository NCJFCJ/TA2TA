<?php
/**
 * @package     com_library
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @author      NCJFCJ <zdraper@ncjfcj.org> - http://ncjfcj.org
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die(); 
  
// import Joomla! view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the Programs Component
 */
 
class LibraryViewDirectory extends JViewLegacy{
	
	// Overwriting JView display method
	function display($tpl = null) {
		// get variables	
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		
		// get data
		$this->items = $this->get('Items');
		$this->targetAudiences = $this->get('TargetAudiences');
				
		// Check for errors.
		if(count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		// Display the view
		parent::display($tpl);
	}
}