<?php
/**
 * @package     com_event_manager
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit
 */
class event_managerViewMeeting extends JViewLegacy {
    protected $state;
    protected $form;
    protected $params;

    /**
     * Display the view
     */
    public function display($tpl = null){
		$app 					= JFactory::getApplication();
        $this->state 			= $this->get('State');
		
		
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
			$this->params->def('page_heading', JText::_('COM_EVENT_MANAGER_MEETING_TITLE'));
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