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
 * Service Registration controller class
 */
class ServicesControllerRegistration extends ServicesController{
	
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
	public function getModel($name = 'Registration', $prefix = 'ServicesModel', $config = array()){
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
		$model = $this->getModel('Registration', 'ServicesModel');

		// Get the user data.
		$data = $app->input->get('jform', array(), 'array');

		// save the return URL
		$return_url = filter_input(INPUT_POST, 'return_url', FILTER_SANITIZE_URL);

		// Validate the posted data.
		$data = $this->validate($data);

		// Attempt to save the data.
		$return	= $model->save($data);

		// Check for errors.
		if($return === false){
			// Save the data in the session.
			$app->setUserState('com_services.edit.registration.data', $data);

			// Redirect back to the edit screen.
			$app->enqueueMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
			$this->setRedirect($return_url);
			return false;
		}

		$service_type = substr($_SERVER['REQUEST_URI'], 1, strpos($_SERVER['REQUEST_URI'], 's') - 1);

		if($service_type == 'webinar'){
			$app->enqueueMessage('Thank you for registering for this webinar. You will receive an email with access information shortly.', 'success');
		}else{
			$app->enqueueMessage('Thank you for registering. You will receive a confirmation email shortly.', 'success');
		}
		$app->redirect($return_url);
	}
	
	/**
	 * Validates the data supplied, returns data on success, false on fail
	 */
	function validate($data){
		$app = JFactory::getApplication();

		// service_type
		if(!in_array($data['service_type'], array(
			'meeting',
			'roundtable',
			'webinar'
		))){
			$app->enqueueMessage(JText::_('COM_SERVICES_BAD_SERVICE_TYPE'), 'warning');
			return false;
		}

		// fname
		if(empty($data['fname'])){
			// fname is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_REGISTRATION_FORM_FNAME_LBL')), 'warning');
			return false;
		}else{
			$data['fname'] = filter_var($data['fname'], FILTER_SANITIZE_STRING);
		}

		// lname
		if(empty($data['lname'])){
			// lname is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_REGISTRATION_FORM_LNAME_LBL')), 'warning');
			return false;
		}else{
			$data['lname'] = filter_var($data['lname'], FILTER_SANITIZE_STRING);
		}

		// email
		if(empty($data['email'])){
			// email is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_REGISTRATION_FORM_EMAIL_LBL')), 'warning');
			return false;
		}else{
			if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false){
				$data['email'] = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
			}else{
				// email is invalid
				$app->enqueueMessage(JText::_('COM_SERVICES_REGISTRATION_FORM_EMAIL_INVALID'), 'warning');
				return false;	
			}
		}

		// zip
		if(empty($data['zip'])){
			// zip is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_REGISTRATION_FORM_ZIP_LBL')), 'warning');
			return false;
		}else{
			$data['zip'] = filter_var($data['zip'], FILTER_SANITIZE_STRING);
		}

		// organization
		if(empty($data['organization'])){
			// org is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_REGISTRATION_FORM_ORGANIZATION_LBL')), 'warning');
			return false;
		}else{
			$data['organization'] = filter_var($data['organization'], FILTER_SANITIZE_STRING);
		}

		// occupation
		if(empty($data['occupation'])){
			// occupation is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_REGISTRATION_FORM_OCCUPATION_LBL')), 'warning');
			return false;
		}else{
			$data['occupation'] = filter_var($data['occupation'], FILTER_SANITIZE_STRING);
		}

		// accessibility
		if(!empty($data['accessibility'])){
			$data['accessibility'] = filter_var($data['accessibility'], FILTER_SANITIZE_STRING);
		}

		// accessibility_braille
		$data['accessibility_braille'] = isset($data['accessibility_braille']) ? ($data['accessibility_braille'] == '1' ? 1 : 0) : 0;

		// accessibility_interpreter
		$data['accessibility_interpreter'] = isset($data['accessibility_interpreter']) ? ($data['accessibility_interpreter'] == '1' ? 1 : 0) : 0;

		// accessibility_large_print
		$data['accessibility_large_print'] = isset($data['accessibility_large_print']) ? ($data['accessibility_large_print'] == '1' ? 1 : 0) : 0;

		// accessibility_interpreter_lang
		if(isset($data['accessibility_interpreter_lang'])){
			$data['accessibility_interpreter_lang'] = filter_var($data['accessibility_interpreter_lang'], FILTER_SANITIZE_STRING);
		}else{
			$data['accessibility_interpreter_lang'] = '';
		}

		// accessibility_simultaneous_interpretation
		if(isset($data['accessibility_simultaneous_interpretation'])){
			$data['accessibility_simultaneous_interpretation'] = filter_var($data['accessibility_simultaneous_interpretation'], FILTER_SANITIZE_STRING);
		}else{
			$data['accessibility_simultaneous_interpretation'] = '';
		}

		// handle additional fields for non-webinar registrations
		if($data['service_type'] != 'webinar'){
			// address
			if(empty($data['address'])){
				// org is required
				$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_REGISTRATION_FORM_ADDRESS_LBL')), 'warning');
				return false;
			}else{
				$data['address'] = filter_var($data['address'], FILTER_SANITIZE_STRING);
			}

			// address2
			$data['address2'] =  filter_var($data['address2'], FILTER_SANITIZE_STRING);

			// fax
			$data['fax'] =  filter_var($data['fax'], FILTER_SANITIZE_STRING);

			// phone
			if(empty($data['phone'])){
				// org is required
				$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_REGISTRATION_FORM_ADDRESS_LBL')), 'warning');
				return false;
			}else{
				$data['phone'] = filter_var($data['phone'], FILTER_SANITIZE_STRING);
			}
		}

		// services
		if(isset($_POST['services'])){
			$data['services'] = filter_var_array($_POST['services'], FILTER_SANITIZE_NUMBER_INT);		
		}else{
			$data['services'] = array();
		}

		// determine if this webinar has a parent or not
		$db = JFactory::getDbo();
		$parent = 0;
		if($data['service_type'] == 'webinar'){
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array(
				'parent'
			)));
			$query->from($db->quoteName('#__services_webinar_requests'));
			$query->where($db->quoteName('id') . '=' . $db->quote($data['services'][0]));
			$db->setQuery($query, 0, 1);
			$parent = $db->loadResult();
		}

		/* --== custom questions ==-- */

		$query = $db->getQuery(true);
		$query->select($db->quoteName(array(
			'registration_q1',
			'registration_q1_type',
			'registration_q2',
			'registration_q2_type',
			'registration_q3',
			'registration_q3_type'
		)));
		$query->from($db->quoteName('#__services_' . $data['service_type'] . '_requests'));
		$query->where($db->quoteName('id') . '=' . $db->quote($parent ? $parent : $data['services'][0]));
		$db->setQuery($query, 0, 1);
		$custom_questions = $db->loadObject();

		// check each question
		for($i = 1; $i <= 3; $i++){
			if(!empty($custom_questions->{'registration_q' . $i})){
				if($custom_questions->{'registration_q' . $i . '_type'} == 1){
					// yes-no
					$data['q' . $i . '_answer'] = ($data['q' . $i . '_answer'] == 1 ? 'Yes' : 'No');
				}else{
					// fill-in
					$data['q' . $i . '_answer'] = filter_var($data['q' . $i . '_answer'], FILTER_SANITIZE_STRING);
					if(empty($data['q' . $i . '_answer'])){
						$app->enqueueMessage(JText::_('COM_SERVICES_ALL_ASTERISK_REQUIRED'), 'warning');
						return false;
					}
				}
			}else{
				$data['q' . $i . '_answer'] = '';
			}
		}

		return $data;
	}
}