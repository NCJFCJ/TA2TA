<?php
/**
 * @version     1.3.0
 * @package     com_ta_calendar
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Ta_calendar.
 */
class Ta_calendarViewEvents extends JViewLegacy {
	protected $items;
	protected $pagination;
	protected $state;
    protected $params;

	/**
	 * Display the view
	 */
	public function display($tpl = null){
        $app               		= JFactory::getApplication();
        $this->state			= $this->get('State');
        $this->items			= $this->get('Items');
        $this->pagination		= $this->get('Pagination');
        
        $this->params       	= $app->getParams('com_ta_calendar');
		
		$this->eventTypes 		= $this->get('EventTypes');
		$this->grantPrograms	= $this->get('GrantPrograms');
		$this->targetAudiences 	= $this->get('TargetAudiences');
		$this->timezones 		= $this->get('Timezones');
		$this->topicAreas 		= $this->get('TopicAreas');
		
		$this->userSettings	= $this->get('UserSettings');

		// if user is logged in
		require_once(JPATH_COMPONENT . '/helpers/ta_calendar.php');
		$permission_level = Ta_calendarHelper::getPermissionLevel();
		if($permission_level > 0){	
			$this->providerProjects = $this->get('ProviderProjects');
		}
        
        // Check for errors.
        if(count($errors = $this->get('Errors'))){
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
		if($menu){
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}else{
			$this->params->def('page_heading', JText::_('com_ta_calendar_DEFAULT_PAGE_TITLE'));
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
		if($this->params->get('menu-meta_description')){
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}
		if($this->params->get('menu-meta_keywords')){
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}
		if($this->params->get('robots')){
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}    	
}