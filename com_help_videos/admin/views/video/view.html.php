<?php
/**
 * @package     com_help_videos
 * @copyright   Copyright (C) 2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit
 */
class Help_videosViewVideo extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

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
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
        if (isset($this->item->checked_out)) {
		    $checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        } else {
            $checkedOut = false;
        }
		$canDo		= Help_videosHelper::getActions();

		JToolBarHelper::title(JText::_('COM_HELP_VIDEOS_TITLE_VIDEO'), 'video.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create'))))
		{

			JToolBarHelper::apply('video.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('video.save', 'JTOOLBAR_SAVE');
		}
		if (!$checkedOut && ($canDo->get('core.create'))){
			JToolBarHelper::custom('video.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}
		if (empty($this->item->id)) {
			JToolBarHelper::cancel('video.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('video.cancel', 'JTOOLBAR_CLOSE');
		}

	}
}
