<?php
/**
 * @package     com_library
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @author      NCJFCJ <zdraper@ncjfcj.org> - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Event controller class.
 */
class LibraryControllerEdit extends LibraryController{
	// default error message
	protected $errorMessage = 'An error occured, please try again later.';
	
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
	public function getModel($name = 'Edit', $prefix = 'LibraryModel', $config = array()){
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	/*
	 * Fires when the user saves an event, runs validation checks and then saves
	 */
	public function save(){
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// Initialise variables.
		$app	= JFactory::getApplication();
		$model 	= $this->getModel();
		
		// Get the user data.
		$data = $app->input->get('jform', array(), 'array');
		
		// Validate the posted data.
		$data = $this->validate($data);

		// Check for errors.
		if($data === false){
			// Get the validation messages.
			$app->enqueueMessage($this->errorMessage, 'warning');

			// Save the data in the session.			
			$app->setUserState('com_library.edit.settings.data', JRequest::getVar('jform'),array());

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=com_library&view=edit&id=' . (isset($data['id']) ? $data['id'] : '0'), false));
			return false;
		}
		
		// Attempt to save the data.
		$return	= $model->save($data);

		// Check for errors.
		if($return === false){
			// Save the data in the session.
			$app->setUserState('com_library.edit.settings.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_library&view=edit&id=' . (isset($data['id']) ? $data['id'] : '0'), false));
			return false;
		}

    // Redirect to the list screen.
		if($data['state'] == -1){
        	$this->setMessage(JText::_('COM_LIBRARY_RESOURCE_SAVED_SUCCESSFULLY_PENDING'));
		}else{
        	$this->setMessage(JText::_('COM_LIBRARY_RESOURCE_SAVED_SUCCESSFULLY'));
		}
        $this->setRedirect(JRoute::_('index.php?option=com_library&view=settings', false));

		// Flush the data from the session.
		$app->setUserState('com_library.edit.settings.data', null);
	}

	/**
	 * Validates the data entered by the user
	 */
	public function validate($data){
		if(!isset($data['id'])
		|| !isset($data['state'])
		|| !isset($data['name'])
		|| !isset($data['description'])){
			$this->errorMessage = 'An error occured. Required variables are missing. (' . __LINE__ . ')';
			return false;	
		}
		
		// id
		if(!preg_match('/^n?\d+$/', $data['id'])){
			$this->errorMessage = 'An error occured. Invalid ID. (' . __LINE__ . ')';
			return false;	
		}

		// state
		if(!is_numeric($data['state'])
		|| ($data['state'] != '-1'
		&& $data['state'] != '0'
		&& $data['state'] != '1')){
			$this->errorMessage = 'An error occured. Invalid State. (' . __LINE__ . ')';
			return false;
		}
		
		// name
		$data['name'] = $data['name'];
		if(empty($data['name'])
		|| strlen($data['name']) < 3
		|| strlen($data['name']) > 150
		|| !preg_match('/^[a-zA-Z0-9():,\-\.\'\"\/\\\ ]*$/', $data['name'])){
			$this->errorMessage = 'The title you entered is invalid. Please try again.';
			return false;
		}
		
		// description
		$data['description'] = $data['description'];
		if(empty($data['description'])){
			$this->errorMessage = 'You must enter a description of your resource.';
			return false;
		}
		
		// target audiences
		if(!isset($data['target_audiences']) || empty($data['target_audiences'])){
			$this->errorMessage = 'You must select at least one target audience.';
			return false;
		}	
		
		// everything is valid, return the data
		return $data;
	}
}