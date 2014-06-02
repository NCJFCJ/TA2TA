<?php
/**
 * @package     com_ta_providers
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ta2ta.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit
 */
class Ta_providersViewTaprovider extends JViewLegacy{
	protected $state;
	protected $item;
	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null){

		// add imgareaselect support
		$document = JFactory::getDocument();
		$document->addScript('/media/com_ta_providers/imgareaselect/jquery.imgareaselect.js');
		$document->addStyleSheet('/media/com_ta_providers/imgareaselect/imgareaselect-default.css');

		$this->state = $this->get('State');
		$this->item	= $this->get('Item');
		$this->form	= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
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

		$user = JFactory::getUser();
		$isNew = ($this->item->id == 0);
        if (isset($this->item->checked_out)) {
		    $checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        }else{
            $checkedOut = false;
        }
		$canDo = Ta_providersHelper::getActions();

		JToolBarHelper::title(JText::_('COM_TA_PROVIDERS_TITLE_TAPROVIDER'), 'taprovider.png');

		// If not checked out, can save the item.
		if(!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create')))){

			JToolBarHelper::apply('taprovider.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('taprovider.save', 'JTOOLBAR_SAVE');
		}
		if(!$checkedOut && ($canDo->get('core.create'))){
			JToolBarHelper::custom('taprovider.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}
		// If an existing item, can save to a copy.
		if(!$isNew && $canDo->get('core.create')){
			JToolBarHelper::custom('taprovider.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		if(empty($this->item->id)){
			JToolBarHelper::cancel('taprovider.cancel', 'JTOOLBAR_CANCEL');
		}else{
			JToolBarHelper::cancel('taprovider.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
