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
 * Service Portal controller class
 */
class ServicesControllerPortal extends ServicesController{
	
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
	public function getModel($name = 'Portal', $prefix = 'ServicesModel', $config = array()){
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
		$model = $this->getModel('Portal', 'ServicesModel');

		// Get the user data.
		$data = $app->input->get('jform', array(), 'array');

		// get the Adobe Link and Webinar ID
		$data['adobe_link'] = $_POST['adobe_link'];
		$data['id'] = $_POST['id'];

		// save the return URL
		$return_url = filter_input(INPUT_POST, 'return_url', FILTER_SANITIZE_URL);

		// Validate the posted data.
		$data = $this->validate($data);

		// Attempt to save the data.
		$return	= $model->save($data);

		// Check for errors.
		if($return === false){
			// Save the data in the session.
			$app->setUserState('com_services.edit.portal.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
			$this->setRedirect($return_url, false);
			return false;
		}

    // Redirect to Adobe Connect
		$this->setRedirect($data['adobe_link'] . '?guestName=' . $data['fname'] . ' ' . $data['lname']);
	}
	
	/**
	 * Validates the data supplied, returns data on success, false on fail
	 */
	function validate($data){
		$app = JFactory::getApplication();

		// adobe_link
		$data['adobe_link'] = filter_var($data['adobe_link'], FILTER_SANITIZE_URL);

		//fname
		if(empty($data['fname'])){
			// fname is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_PORTAL_FORM_FNAME_LBL')), 'warning');
			return false;
		}else{
			$data['fname'] = filter_var($data['fname'], FILTER_SANITIZE_STRING);
		}

		// id
		$data['id'] = filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT);

		//lname
		if(empty($data['lname'])){
			// lname is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_PORTAL_FORM_LNAME_LBL')), 'warning');
			return false;
		}else{
			$data['lname'] = filter_var($data['lname'], FILTER_SANITIZE_STRING);
		}

		// email
		if(empty($data['email'])){
			// email is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_PORTAL_FORM_EMAIL_LBL')), 'warning');
			return false;
		}else{
			if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false){
				$data['email'] = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
			}else{
				// email is invalid
				$app->enqueueMessage(JText::_('COM_SERVICES_PORTAL_FORM_EMAIL_INVALID'), 'warning');
				return false;	
			}
		}

		//occupation
		if(empty($data['occupation'])){
			// occupation is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_PORTAL_FORM_OCCUPATION_LBL')), 'warning');
			return false;
		}else{
			$data['occupation'] = filter_var($data['occupation'], FILTER_SANITIZE_STRING);
		}

		//num_viewers
		if(empty($data['num_viewers'])){
			//num_viewers is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_PORTAL_FORM_NUM_VIEWERS_LBL')), 'warning');
			return false;
		}else{
			$data['num_viewers'] = filter_var($data['num_viewers'], FILTER_SANITIZE_NUMBER_INT);
		}

		return $data;
	}
}