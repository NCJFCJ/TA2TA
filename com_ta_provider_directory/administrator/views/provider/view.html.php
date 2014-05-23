<?php
/**
 * @version     2.0.0
 * @package     com_ta_provider_directory
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit
 */
class Ta_provider_directoryViewProvider extends JViewLegacy{
	protected $state;
	protected $item;
	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null){
		$user = JFactory::getUser();		
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->userName = $user->name;

		// Grab the grant programs
		$this->grantPrograms = $this->get('GrantPrograms');

		// Check for errors.
		if(count($errors = $this->get('Errors'))){
            throw new Exception(implode("\n", $errors));
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar(){
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
        if(isset($this->item->checked_out)){
		    $checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        }else{
            $checkedOut = false;
        }
		$canDo = Ta_provider_directoryHelper::getActions();

		JToolBarHelper::title(JText::_('COM_TA_PROVIDER_DIRECTORY_TITLE_PROVIDER'), 'provider.png');

		// If not checked out, can save the item.
		if(!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create')))){

			JToolBarHelper::apply('provider.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('provider.save', 'JTOOLBAR_SAVE');
		}
		if(!$checkedOut && ($canDo->get('core.create'))){
			JToolBarHelper::custom('provider.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}
		// If an existing item, can save to a copy.
		if(!$isNew && $canDo->get('core.create')){
			JToolBarHelper::custom('provider.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		if(empty($this->item->id)){
			JToolBarHelper::cancel('provider.cancel', 'JTOOLBAR_CANCEL');
		}else{
			JToolBarHelper::cancel('provider.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
