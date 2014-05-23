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

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Event controller class.
 */
class Ta_calendarControllerSettings extends Ta_calendarController
{
	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   12.2
	 */
	public function getModel($name = 'Settings', $prefix = 'Ta_calendarModel', $config = array()){
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	/**
	 * Method to save a user's profile data.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function save(){
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app	= JFactory::getApplication();
		$model = $this->getModel('Settings', 'Ta_calendarModel');

		// Get the user data.
		$data = $app->input->get('jform', array(), 'array');

		// Validate the posted data.
		$data = $this->validate($data);
		
		// Check for errors.
		if ($data === false) {
			// Get the validation messages.
			$app->enqueueMessage('An error occured, please try again later.', 'warning');

			// Save the data in the session.			
			$app->setUserState('com_ta_calendar.edit.settings.data', JRequest::getVar('jform'),array());

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=com_ta_calendar&view=settings', false));
			return false;
		}
		
		// we need to inverse and format the filters
		$data = $model->prepareFilters($data);

		// Attempt to save the data.
		$return	= $model->save($data);

		// Check for errors.
		if ($return === false) {
			// Save the data in the session.
			$app->setUserState('com_ta_calendar.edit.settings.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_ta_calendar&view=settings', false));
			return false;
		}

        // Redirect to the list screen.
        $this->setMessage(JText::_('COM_TA_CALENDAR_ITEM_SAVED_SUCCESSFULLY'));
        $menu = & JSite::getMenu();
        $item = $menu->getActive();
        $this->setRedirect(JRoute::_($item->link, false));

		// Flush the data from the session.
		$app->setUserState('com_ta_calendar.edit.settings.data', null);
	}
    
	
	/**
	 * Validates the data supplied, returns data on success, false on fail
	 */
	function validate($data){
		// grab the list of valid timezones
		$eventModel = $this->getModel('Events');
		$timezones = $eventModel->getTimezones();
		
		// check that this timzone is in our list
		$timezoneMatch = false;
		foreach($timezones as $timezone){
			if($timezone->abbr == $data['timezone']){
				$timezoneMatch = true;
			}	
		}
		if(!$timezoneMatch){
			return false;
		}
		
		// check that the view is valid
		if($data['view'] != 'month' && $data['view'] != 'week' && $data['view'] != 'list'){
			return false;
		}
			
		// event types
		if(!empty($data['filters']['eventTypes'])){
			$data['filters']['eventTypes'] = explode(',',$data['filters']['eventTypes']);
			foreach($data['filters']['eventTypes'] as $eventType){
				if(!preg_match('/^[0-9]+$/',$eventType)){
					return false;
				}
			}
		}
		
		// grant programs
		if(!empty($data['filters']['grantPrograms'])){
			$data['filters']['grantPrograms'] = explode(',',$data['filters']['grantPrograms']);
			foreach($data['filters']['grantPrograms'] as $grantPrograms){
				if(!preg_match('/^[0-9]+$/',$grantPrograms)){
					return false;
				}
			}
		}
		
		// target audiences
		if(!empty($data['filters']['targetAudiences'])){
			$data['filters']['targetAudiences'] = explode(',',$data['filters']['targetAudiences']);
			foreach($data['filters']['targetAudiences'] as $targetAudience){
				if(!preg_match('/^[0-9]+$/',$targetAudience)){
					return false;
				}
			}
		}

		// topic areas
		if(!empty($data['filters']['topicAreas'])){
			$data['filters']['topicAreas'] = explode(',',$data['filters']['topicAreas']);
			foreach($data['filters']['topicAreas'] as $topicArea){
				if(!preg_match('/^[0-9]+$/',$topicArea)){
					return false;
				}
			}
		}
		
		return $data;
	}
}