<?php
/**
 * @package     com_services
 * @copyright   Copyright (C) 2016 NCJFCJ. All rights reserved.
 * @author      Zadra Design - http://zadradesign.com
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Service Meeting controller class
 */
class ServicesControllerMeeting extends ServicesController{
	
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
	public function getModel($name = 'Meeting', $prefix = 'ServicesModel', $config = array()){
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
		$app = JFactory::getApplication();
		$model = $this->getModel('Meeting', 'ServicesModel');

		// Get the user data.
		$data = $app->input->get('jform', array(), 'array');

		// save the return URL
		$return_url = filter_input(INPUT_POST, 'return_url', FILTER_SANITIZE_URL);

		// Validate the posted data.
		$data = $this->validate($data);
		
		// Check for errors.
		if($data === false){
			// Save the data in the session.			
			$app->setUserState('com_services.edit.meeting.data', JRequest::getVar('jform'), array());

			// Redirect back to the edit screen.
			$this->setRedirect($return_url, false);
			return false;
		}

		// Attempt to save the data.
		$return	= $model->save($data);

		// Check for errors.
		if($return === false){
			// Save the data in the session.
			$app->setUserState('com_services.edit.meeting.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
			$this->setRedirect($return_url, false);
			return false;
		}

    // Redirect to the list screen.
    $this->setMessage(JText::_('COM_SERVICES_MEETING_SAVED_SUCCESSFULLY'));
    $this->setRedirect($return_url);

		// Flush the data from the session.
		$app->setUserState('com_services.edit.meeting.data', null);
	}    
	
	/**
	 * Validates the data supplied, returns data on success, false on fail
	 */
	function validate($data){
		$app = JFactory::getApplication();

		// suggested_dates
		if(empty($data['suggested_dates'])){
			// suggested_dates is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_MEETING_FORM_SUGGESTED_DATES_LBL')), 'warning');
			return false;
		}else{
			$data['suggested_dates'] = filter_var($data['suggested_dates'], FILTER_SANITIZE_STRING);
		}

		// project
		if(empty($data['project'])){
			// project is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_MEETING_FORM_PROJECT_LBL')), 'warning');
			return false;
		}else{
			$data['project'] = filter_var($data['project'], FILTER_SANITIZE_NUMBER_INT);
		}

		// types_of_support
		if(empty($data['types_of_support'])){
			// types_of_support is required
			$app->enqueueMessage(JText::_('COM_SERVICES_MEETING_FORM_TYPES_OF_SUPPORT_REQUIRED'), 'warning');
			return false;
		}

		// comments
		if(!empty($comments)){
			$data['comments'] = filter_var($data['comments'], FILTER_SANITIZE_STRING);
			if(strlen($data['comments']) > 255){
				// comments too long
				$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_TOO_LONG', JText::_('COM_SERVICES_MEETING_FORM_COMMENTS_LBL')), 'warning');
				return false;
			}
		}

		return $data;
	}
}