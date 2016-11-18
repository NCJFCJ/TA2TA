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
 * Service Roundtable controller class
 */
class ServicesControllerRoundtable extends ServicesController{
	
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
	public function getModel($name = 'Roundtable', $prefix = 'ServicesModel', $config = array()){
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
		$model = $this->getModel('Roundtable', 'ServicesModel');

		// Get the user data.
		$data = $app->input->get('jform', array(), 'array');

		// save the return URL
		$return_url = filter_input(INPUT_POST, 'return_url', FILTER_SANITIZE_URL);

		// Validate the posted data.
		$data = $this->validate($data);
		
		// Check for errors.
		if($data === false){
			// Save the data in the session.			
			$app->setUserState('com_services.edit.roundtable.data', JRequest::getVar('jform'),array());

			// Redirect back to the edit screen.
			$this->setRedirect($return_url, false);
			return false;
		}

		// Attempt to save the data.
		$return	= $model->save($data);

		// Check for errors.
		if($return === false){
			// Save the data in the session.
			$app->setUserState('com_services.edit.roundtable.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
			$this->setRedirect($return_url, false);
			return false;
		}

	  // Redirect to the list screen.
	  $this->setMessage(JText::_('COM_SERVICES_ROUNDTABLE_SAVED_SUCCESSFULLY'));
    $this->setRedirect($return_url);

		// Flush the data from the session.
		$app->setUserState('com_services.edit.roundtable.data', null);
	}
	
	/**
	 * Validates the data supplied, returns data on success, false on fail
	 */
	function validate($data){
		$app = JFactory::getApplication();

		//topic
		if(empty($data['topic'])){
			// topic is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_ROUNDTABLE_FORM_TOPIC_LBL')), 'warning');
			return false;
		}else{
			$data['topic'] = filter_var($data['topic'], FILTER_SANITIZE_STRING);
			if(strlen($data['topic']) > 255){
				// topic too long
				$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_TOO_LONG', JText::_('COM_SERVICES_ROUNDTABLE_FORM_TOPIC_LBL')), 'warning');
				return false;
			}
		}

		// project
		if(empty($data['project'])){
			// project is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_ROUNDTABLE_FORM_PROJECT_LBL')), 'warning');
			return false;
		}else{
			$data['project'] = filter_var($data['project'], FILTER_SANITIZE_NUMBER_INT);
		}

		// suggested_dates
		if(empty($data['suggested_dates'])){
			// suggested_dates is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_ROUNDTABLE_FORM_SUGGESTED_DATES_LBL')), 'warning');
			return false;
		}else{
			$data['suggested_dates'] = filter_var($data['suggested_dates'], FILTER_SANITIZE_STRING);
			if(strlen($data['suggested_dates']) > 255){
				// suggested_dates too long
				$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_TOO_LONG', JText::_('COM_SERVICES_ROUNDTABLE_FORM_SUGGESTED_DATES_LBL')), 'warning');
				return false;
			}
		}
		
		//proposed_locations
		if(empty($data['proposed_locations'])){
			// proposed_locations is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_ROUNDTABLE_FORM_PROPOSED_LOCATIONS_LBL')), 'warning');
			return false;
		}else{
			$data['proposed_locations'] = filter_var($data['proposed_locations'], FILTER_SANITIZE_STRING);
			if(strlen($data['proposed_locations']) > 500){
				// proposed_locations too long
				$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_TOO_LONG', JText::_('COM_SERVICES_ROUNDTABLE_FORM_PROPOSED_LOCATIONS_LBL')), 'warning');
				return false;
			}
		}
		
		//description
		if(empty($data['description'])){
			// description is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_ROUNDTABLE_FORM_DESCRIPTION_LBL')), 'warning');
			return false;
		}else{
			$data['description'] = filter_var($data['description'], FILTER_SANITIZE_STRING);
			if(strlen($data['description']) > 500){
				// description too long
				$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_TOO_LONG', JText::_('COM_SERVICES_ROUNDTABLE_FORM_DESCRIPTION_LBL')), 'warning');
				return false;
			}
		}
		
		//num_participants
		if(empty($data['num_participants'])){
			// num_participants is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_ROUNDTABLE_FORM_NUMBER_OF_PARTICIPANTS_LBL')), 'warning');
			return false;
		}else{
			$data['num_participants'] = filter_var($data['num_participants'], FILTER_SANITIZE_NUMBER_INT);
			if(strlen($data['num_participants']) > 6){
				// num_participants too long
				$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_TOO_LONG', JText::_('COM_SERVICES_ROUNDTABLE_FORM_NUMBER_OF_PARTICIPANTS_LBL')), 'warning');
				return false;
			}
		}
		
		//topic_areas
		if(empty($data['topic_areas'])){
			// topic_areas is required
			$app->enqueueMessage(JText::_('COM_SERVICES_ROUNDTABLE_FORM_TOPIC_AREAS_REQUIRED'), 'warning');
			return false;
		}
		
		//is_partner
		if(empty($data['is_partner'])){
			// is_partner is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_ROUNDTABLE_FORM_IS_PARTNER_REQUIRED_LBL')), 'warning');
			return false;
		}else{
			$data['is_partner'] = filter_var($data['is_partner'], FILTER_SANITIZE_STRING);
			if(strlen($data['is_partner']) > 500){
				// is_partner too long
				$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_TOO_LONG', JText::_('COM_SERVICES_ROUNDTABLE_FORM_IS_PARTNER_REQUIRED_LBL')), 'warning');
				return false;
			}
		}
		
		//benefit
		if(empty($data['benefit'])){
			// benefit is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_ROUNDTABLE_FORM_BENEFIT_REQUIRED_LBL')), 'warning');
			return false;
		}else{
			$data['benefit'] = filter_var($data['benefit'], FILTER_SANITIZE_STRING);
			if(strlen($data['benefit']) > 500){
				// benefit too long
				$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_TOO_LONG', JText::_('COM_SERVICES_ROUNDTABLE_FORM_BENEFIT_REQUIRED_LBL')), 'warning');
				return false;
			}
		}
		
		//how_advance
		if(empty($data['how_advance'])){
			// how_advance is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_ROUNDTABLE_FORM_HOW_ADVANCE_REQUIRED_LBL')), 'warning');
			return false;
		}else{
			$data['how_advance'] = filter_var($data['how_advance'], FILTER_SANITIZE_STRING);
			if(strlen($data['how_advance']) > 500){
				// how_advance too long
				$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_TOO_LONG', JText::_('COM_SERVICES_ROUNDTABLE_FORM_HOW_ADVANCE_REQUIRED_LBL')), 'warning');
				return false;
			}
		}
		
		//goals
		if(empty($data['goals'])){
			// goals is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_ROUNDTABLE_FORM_GOALS_REQUEST_LBL')), 'warning');
			return false;
		}else{
			$data['goals'] = filter_var($data['goals'], FILTER_SANITIZE_STRING);
			if(strlen($data['goals']) > 500){
				// goals too long
				$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_TOO_LONG', JText::_('COM_SERVICES_ROUNDTABLE_FORM_GOALS_REQUEST_LBL')), 'warning');
				return false;
			}
		}
		
		//resources_needed
		if(empty($data['resources_needed'])){
			// resources_needed is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_ROUNDTABLE_FORM_RESOURCES_NEEDED_REQUIRED_LBL')), 'warning');
			return false;
		}else{
			$data['resources_needed'] = filter_var($data['resources_needed'], FILTER_SANITIZE_STRING);
			if(strlen($data['resources_needed']) > 500){
				// resources_needed too long
				$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_TOO_LONG', JText::_('COM_SERVICES_ROUNDTABLE_FORM_RESOURCES_NEEDED_REQUIRED_LBL')), 'warning');
				return false;
			}
		}
		
		//resources_provided
		if(empty($data['resources_provided'])){
			// resources_provided is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_ROUNDTABLE_FORM_RESOURCES_PROVIDED_REQUIRED_LBL')), 'warning');
			return false;
		}else{
			$data['resources_provided'] = filter_var($data['resources_provided'], FILTER_SANITIZE_STRING);
			if(strlen($data['resources_provided']) > 500){
				// resources_provided too long
				$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_TOO_LONG', JText::_('COM_SERVICES_ROUNDTABLE_FORM_RESOURCES_PROVIDED_REQUIRED_LBL')), 'warning');
				return false;
			}
		}
		
		// comments
		if(empty($data['comments'])){
			// comments is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_ROUNDTABLE_FORM_COMMENTS_LBL')), 'warning');
			return false;
		}else{
			$data['comments'] = filter_var($data['comments'], FILTER_SANITIZE_STRING);
			if(strlen($data['comments']) > 500){
				// comments too long
				$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_TOO_LONG', JText::_('COM_SERVICES_ROUNDTABLE_FORM_COMMENTS_LBL')), 'warning');
				return false;
			}
		}

		return $data;
	}
}