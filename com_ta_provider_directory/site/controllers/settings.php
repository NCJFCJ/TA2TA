<?php
/**
 * @version     2.0.0
 * @package     com_ta_provider_directory
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Event controller class.
 */
class Ta_provider_directoryControllerSettings extends Ta_provider_directoryController
{
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
	public function getModel($name = 'Settings', $prefix = 'Ta_provider_directoryModel', $config = array()){
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
		$model 	= $this->getModel('Settings', 'Ta_provider_directoryModel');

		// Get the user data.
		$data = $app->input->get('jform', array(), 'array');

		// Validate the posted data.
		$data = $this->validate($data);
		
		// Check for errors.
		if ($data === false) {
			// Get the validation messages.
			$app->enqueueMessage($this->errorMessage, 'warning');

			// Save the data in the session.			
			$app->setUserState('com_ta_provider_directory.edit.settings.data', JRequest::getVar('jform'),array());

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=com_ta_provider_directory&view=settings', false));
			return false;
		}
		
		// Attempt to save the data.
		$return	= $model->save($data);

		// Check for errors.
		if ($return === false) {
			// Save the data in the session.
			$app->setUserState('com_ta_provider_directory.edit.settings.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_ta_provider_directory&view=settings', false));
			return false;
		}

        // Redirect to the list screen.
        $this->setMessage(JText::_('COM_TA_PROVIDER_DIRECTORY_ITEM_SAVED_SUCCESSFULLY'));
        $menu = & JSite::getMenu();
        $item = $menu->getActive();
        $this->setRedirect(JRoute::_($item->link, false));

		// Flush the data from the session.
		$app->setUserState('com_ta_provider_directory.edit.settings.data', null);
	}
	
	/**
	 * Validates the data supplied, returns data on success, false on fail
	 */
	function validate($data){
		// check that the website
		if(!empty($data['website'])){
			// check for the http, add if it isn't present	
			if(substr($data['website'], 0, 4) != 'http'){
				$data['website'] = 'http://' . $data['website'];
			}
			$data['website'] = strtolower($data['website']);
			// validate the new url
			if(!filter_var($data['website'], FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)){
				$this->errorMessage = 'The website you entered is invalid.';
				return false;
			}
		}
		return $data;
	}
}