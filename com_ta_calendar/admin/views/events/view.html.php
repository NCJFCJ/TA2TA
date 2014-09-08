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
 * View class for a list of Ta_calendar.
 */
class Ta_calendarViewEvents extends JViewLegacy{
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
        
		Ta_calendarHelper::addSubmenu('events');
        
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
		require_once JPATH_COMPONENT.'/helpers/ta_calendar.php';

		$state	= $this->get('State');
		$canDo	= Ta_calendarHelper::getActions($state->get('filter.category_id'));

		JToolBarHelper::title(JText::_('COM_TA_CALENDAR_TITLE_EVENTS'), 'events.png');

    //Check if the form exists before showing the add/edit buttons
    $formPath = JPATH_COMPONENT_ADMINISTRATOR.'/views/event';
    if(file_exists($formPath)){
	    if($canDo->get('core.create')){
		    JToolBarHelper::addNew('event.add','JTOOLBAR_NEW');
	    }

	    if($canDo->get('core.edit') && isset($this->items[0])){
		    JToolBarHelper::editList('event.edit','JTOOLBAR_EDIT');
	    }

    }

		if($canDo->get('core.edit.state')){
      if(isset($this->items[0]->state)){
		    JToolBarHelper::divider();
		    JToolBarHelper::custom('events.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
		    JToolBarHelper::custom('events.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
      }else if(isset($this->items[0])){
        //If this component does not use state then show a direct delete button as we can not trash
        JToolBarHelper::deleteList('', 'events.delete','JTOOLBAR_DELETE');
      }

      if(isset($this->items[0]->state)){
		    JToolBarHelper::divider();
		    JToolBarHelper::archiveList('events.archive','JTOOLBAR_ARCHIVE');
      }
      if(isset($this->items[0]->checked_out)){
      	JToolBarHelper::custom('events.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
      }
		}
        
    //Show trash and delete for components that uses the state field
    if(isset($this->items[0]->state)){
	    if($state->get('filter.state') == -2 && $canDo->get('core.delete')){
		    JToolBarHelper::deleteList('', 'events.delete','JTOOLBAR_EMPTY_TRASH');
		    JToolBarHelper::divider();
	    }else if($canDo->get('core.edit.state')){
		    JToolBarHelper::trash('events.trash','JTOOLBAR_TRASH');
		    JToolBarHelper::divider();
	    }
    }

		if($canDo->get('core.admin')){
			JToolBarHelper::preferences('com_ta_calendar');
		}
        
    //Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_ta_calendar&view=events');
        
    $this->extra_sidebar = '';
        
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)
		);

		//Filter for the field approved
		$select_label = JText::sprintf('COM_TA_CALENDAR_FILTER_SELECT_LABEL', 'Approved?');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "1";
		$options[0]->text = "Approved";
		$options[1] = new stdClass();
		$options[1]->value = "0";
		$options[1]->text = "Not Approved";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_approved',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.approved'), true)
		);
    //Filter for the field ".type;
    jimport('joomla.form.form');
    $options = array();
    JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
    $form = JForm::getInstance('com_ta_calendar.event', 'event');

    $field = $form->getField('type');

    $query = $form->getFieldAttribute('type','query');
    $translate = $form->getFieldAttribute('type','translate');
    $key = $form->getFieldAttribute('type','key_field');
    $value = $form->getFieldAttribute('type','value_field');

    // Get the database object.
    $db = JFactory::getDBO();

    // Set the query and get the result list.
    $db->setQuery($query);
    $items = $db->loadObjectlist();

    // Build the field options.
    if(!empty($items)){
      foreach($items as $item){
        if($translate == true){
          $options[] = JHtml::_('select.option', $item->$key, JText::_($item->$value));
        }else{
          $options[] = JHtml::_('select.option', $item->$key, $item->$value);
        }
      }
    }

    JHtmlSidebar::addFilter(
      'Type',
      'filter_type',
      JHtml::_('select.options', $options, "value", "text", $this->state->get('filter.type')),
      true
    );
	}
    
	protected function getSortFields(){
		return array(
		'a.id' => JText::_('JGRID_HEADING_ID'),
		'a.state' => JText::_('JSTATUS'),
		'a.approved' => JText::_('COM_TA_CALENDAR_EVENTS_APPROVED'),
		'a.datetime' => JText::_('COM_TA_CALENDAR_EVENTS_DATETIME'),
		'a.title' => JText::_('COM_TA_CALENDAR_EVENTS_TITLE'),
		'a.type' => JText::_('COM_TA_CALENDAR_EVENTS_TYPE'),
		'a.checked_out' => JText::_('COM_TA_CALENDAR_EVENTS_CHECKED_OUT'),
		'a.checked_out_time' => JText::_('COM_TA_CALENDAR_EVENTS_CHECKED_OUT_TIME'),
		'a.created_by' => JText::_('COM_TA_CALENDAR_EVENTS_CREATED_BY'),
		);
	}
}