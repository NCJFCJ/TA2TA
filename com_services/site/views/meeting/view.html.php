<?php
/**
 * @package     com_services
 * @copyright   Copyright (C) 2016 NCJFCJ. All rights reserved.
 * @author      Zadra Design - http://zadradesign.com
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit
 */
class ServicesViewMeeting extends JViewLegacy{
  protected $state;
  protected $form;
  protected $params;

  /**
   * Display the view
   */
  public function display($tpl = null){
		$app = JFactory::getApplication();
    $this->form	= $this->get('Form');
		$this->item	= $this->get('Data');
	  $this->state = $this->get('State');
		
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
		$app = JFactory::getApplication();
		$menus = $app->getMenu();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		$this->params = $menu->params;
		if($menu){
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}else{
			$this->params->def('page_heading', JText::_('COM_SERVICES_MEETING_TITLE'));
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