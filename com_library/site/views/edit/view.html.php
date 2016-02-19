<?php
/**
 * @package     com_library
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @author      NCJFCJ <zdraper@ncjfcj.org> - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

// require the helper
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/library.php');

/**
 * View to edit
 */
class LibraryViewEdit extends JViewLegacy{
  protected $state;
  protected $form;
	protected $listing;
  protected $params;

  /**
   * Display the view
   */
  public function display($tpl = null){
		$app = JFactory::getApplication();
    $this->form	= $this->get('Form');
		$this->org = LibraryHelper::getUserOrg();
		$this->projects = $this->get('Projects');
		$this->resource = $this->get('Resource');
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
			$this->params->def('page_heading', JText::_('COM_LIBRARY_SETTINGS_TITLE'));
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