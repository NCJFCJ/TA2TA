<?php
/**
 * @version     2.0.0
 * @package     com_ta_provider_directory
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die(); 
  
// import Joomla! view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the Programs Component
 */
 
class Ta_provider_directoryViewDirectory extends JViewLegacy{
	
	// Overwriting JView display method
	function display($tpl = null) {
		// get variables	
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		
		// get data
		$this->providers = $this->get('Providers');
		$this->grantPrograms = $this->get('GrantPrograms');	
				
		// Check for errors.
		if(count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		// Display the view
		parent::display($tpl);
	}
}