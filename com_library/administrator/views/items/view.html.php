<?php
/**
 * @package     com_library
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @author      NCJFCJ <zdraper@ncjfcj.org> - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Library.
 */
class LibraryViewItems extends JViewLegacy{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null){
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if(count($errors = $this->get('Errors'))){
			throw new Exception(implode("\n", $errors));
		}
        
		LibraryHelper::addSubmenu('items');
        
		$this->addToolbar();
        
        $this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar(){
		require_once JPATH_COMPONENT.'/helpers/library.php';

		$state	= $this->get('State');
		$canDo	= LibraryHelper::getActions($state->get('filter.category_id'));

		JToolBarHelper::title(JText::_('COM_LIBRARY_TITLE'), 'items.png');

    //Check if the form exists before showing the add/edit buttons
    $formPath = JPATH_COMPONENT_ADMINISTRATOR.'/views/item';
    if(file_exists($formPath)){
	  	if($canDo->get('core.create')){
		    JToolBarHelper::addNew('item.add','JTOOLBAR_NEW');
	    }
	    if($canDo->get('core.edit') && isset($this->items[0])){
		    JToolBarHelper::editList('item.edit','JTOOLBAR_EDIT');
	    }
    }

		if($canDo->get('core.edit.state')){
			if(isset($this->items[0]->state)){
		    JToolBarHelper::divider();
		    JToolBarHelper::custom('items.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
		    JToolBarHelper::custom('items.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
      }else if(isset($this->items[0])){
        //If this component does not use state then show a direct delete button as we can not trash
        JToolBarHelper::deleteList('', 'items.delete','JTOOLBAR_DELETE');
      }

      if(isset($this->items[0]->state)){
		    JToolBarHelper::divider();
		    JToolBarHelper::archiveList('items.archive','JTOOLBAR_ARCHIVE');
      }
      if(isset($this->items[0]->checked_out)){
      	JToolBarHelper::custom('items.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
      }
		}
        
    //Show trash and delete for components that uses the state field
    if(isset($this->items[0]->state)){
	    if($state->get('filter.state') == -2 && $canDo->get('core.delete')){
		    JToolBarHelper::deleteList('', 'items.delete','JTOOLBAR_EMPTY_TRASH');
		    JToolBarHelper::divider();
	    }else if($canDo->get('core.edit.state')){
		    JToolBarHelper::trash('items.trash','JTOOLBAR_TRASH');
		    JToolBarHelper::divider();
	    }
    }
        
        //Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_library&view=items');

		$filterState = $this->state->get('filter.state');
        $this->extra_sidebar = '
        <hr>
        <div class="filter-select hidden-phone">
			<h4 class="page-header">Filter:</h4>
			<select style="display: none;" name="filter_published" id="filter_published" class="span12 small chzn-done" onchange="this.form.submit()">
				<option value="">- Select Status -</option>
				<option value="1"' . ($filterState == '1' ? ' selected="selected"' : '') . '>Published</option>
				<option value="0"' . ($filterState == '0' ? ' selected="selected"' : '') . '>Unpublished</option>
				<option value="-1"' . ($filterState == '-1' ? ' selected="selected"' : '') . '>Pending Approval</option>
				<option value="2"' . ($filterState == '2' ? ' selected="selected"' : '') . '>Outdated</option>
				<option value="3"' . ($filterState == '3' ? ' selected="selected"' : '') . '>OVW Only</option>
				<option value="-2"' . ($filterState == '-2' ? ' selected="selected"' : '') . '>Trashed</option>
				<option value="*"' . ($filterState == '*' || $filterState == '' ? ' selected="selected"' : '') . '>All</option>
			</select>
			<hr class="hr-condensed">
		</div>';		
	}
    
	protected function getSortFields(){
		return array(
			'a.id' => JText::_('JGRID_HEADING_ID'),
			'a.state' => JText::_('JSTATUS'),
			'a.checked_out' => JText::_('COM_LIBRARY_CHECKED_OUT'),
			'a.checked_out_time' => JText::_('COM_LIBRARY_CHECKED_OUT_TIME'),
			'a.name' => JText::_('COM_LIBRARY_NAME'),
			'a.created_by' => JText::_('COM_LIBRARY_CREATED_BY'),
		);
	}   
}