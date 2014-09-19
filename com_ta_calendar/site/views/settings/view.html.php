<?php
/**
 * @package     com_ta_calendar
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit
 */
class Ta_calendarViewSettings extends JViewLegacy {
    protected $state;
    protected $form;
    protected $params;

    /**
     * Display the view
     */
    public function display($tpl = null){
		$app 					= JFactory::getApplication();
        $this->state 			= $this->get('State');
		
		/*
		 * TO DO: FIX THIS MESS!!!
		 * Background: For whatever reason, the controller is not loading. Due to that, I was
		 * unable to add the model into the controler's display function properly. As such,
		 * I have included the additional model here and referenced it through my own call to its
		 * parent class. Once the controller is fixed, this code can be removed and can be replaced
		 * with the following lines:
		 * $this->eventTypes 		= $this->get('EventTypes', 'Events');
		 * $this->topicAreas 		= $this->get('TopicAreas', 'Events');
		 * $this->targetAudiences 	= $this->get('TargetAudiences', 'Events');
		 * $this->timezones			= $this->get('Timezones', 'Events');
		 * $this->userSettings		= $this->get('UserSettings', 'Events');
		 */
		 
		// BEGIN IMPROPER HACK
		require_once(JPATH_COMPONENT_SITE . '/models/events.php');
		$eventsModel = new Ta_calendarModelEvents();
		$this->eventTypes 		= $eventsModel->getEventTypes();
		$this->grantPrograms	= $eventsModel->getGrantPrograms();
		$this->topicAreas 		= $eventsModel->getTopicAreas();
		$this->targetAudiences 	= $eventsModel->getTargetAudiences();
		$this->timezones		= $eventsModel->getTimezones();
		$this->userSettings		= $eventsModel->getUserSettings();
		// END IMPROPER HACK
		
        // Check for errors.
		if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }
        
        $this->_prepareDocument();

        parent::display($tpl);
    }


	/**
	 * Prepares the document
	 */
	protected function _prepareDocument(){
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$title	= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		$this->params = $menu->params;
		if($menu){
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}else{
			$this->params->def('page_heading', JText::_('COM_TA_CALENDAR_SETTINGS_TITLE'));
		}
		$title = $this->params->get('page_title', '');
		if(empty($title)){
			$title = $app->getCfg('sitename');
		}elseif($app->getCfg('sitename_pagetitles', 0) == 1){
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}elseif($app->getCfg('sitename_pagetitles', 0) == 2){
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		if($menu->params->get('menu-meta_description')){
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if($menu->params->get('menu-meta_keywords')){
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if($menu->params->get('robots')){
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}    
}