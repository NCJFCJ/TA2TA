<?php
/**
 * @package     com_ta_providers
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

/**
 * TA Provider Settings model
 */
class Ta_providersModelSettings extends JModelForm{
    
    var $_item = null;
    var $user = null;
    
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState(){
		$app = JFactory::getApplication('com_ta_providers');

		// Load the parameters.
        $params = $app->getParams();
        $params_array = $params->toArray();
		$this->setState('params', $params);
	}
        

	/**
	 * Method to get an ojbect.
	 *
	 * @param	integer	The id of the object to get.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function &getData($id = null){
		if($this->_item === null){
			$this->_item = false;

			// get the user's organization
			$org = $this->getUserOrg();

			if(!empty($org)){
				// retrieve the information for this organization
				$db = $this->getDbo();
				$query = $db->getQuery(true);
				$query->select($db->quoteName(array(
				     'name',
				     'website',
				     'logo'
				)));
				$query->from($db->quoteName('#__ta_providers'));
				$query->where($db->quoteName('id') . ' = ' . $db->quote($org));
				$db->setQuery($query, 0 ,1);
				if($result = $db->loadObject()){
					$this->_item = $result;
				}else{
					$this->setError(JText::_('COM_TA_PROVIDERS_SETTINGS_QUERY_ERROR'));	
				}
			}else{
				$this->setError(JText::_('COM_TA_PROVIDERS_SETTINGS_NO_ORG'));
			}
		}
		return $this->_item;
	}
	        
	/**
	 * Method to get the profile form.
	 *
	 * The base form is loaded from XML 
     * 
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true){
		// Get the form.
		$form = $this->loadForm('com_ta_providers.settings', 'settings', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}
	
	/**
	 * Returns the ID of the current user
	 */
	protected function getUserId(){
		// get the user object	
		$user = $this->getUserObj();
		
		return $user->id;
	}
	
	/**
	 * Returns the user object
	 */
	protected function getUserObj(){
		// check if we already have the user object, return it if we do
		if($this->user){
			return $this->user;
		}
		return JFactory::getUser();
	}
	
	/**
	 * Gets the organization of this user
	 * @return int The ID of the user's organization, 0 on fail
	 */
	public function getUserOrg(){
		// get the user's organization
		$db = $this->getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('profile_value'));
		$query->from($db->quoteName('#__user_profiles'));
		$query->where($db->quoteName('user_id') . '=' . $db->quote($this->getUserId()) . ' AND ' . $db->quoteName('profile_key') . ' = ' . $db->quote('profile.org'));
		$db->setQuery($query, 0, 1);
		
		// check that the query was successful
		if(!($org = $db->loadResult())){
			JError::raiseWarning(100, 'Unable to determine user\'s organization.');
			return 0;
		}
		
		// remove quotes
		$org = substr($org, 1, -1);
		
		// return the result
		return (int)$org;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData(){
		$data = JFactory::getApplication()->getUserState('com_ta_providers.edit.settings.data', array());
        if (empty($data)) {
            $data = $this->getData();
        }
        
        return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param	array		The form data.
	 * @return	mixed		The user id on success, false on failure.
	 * @since	1.6
	 */
	public function save($data){	
		$org = $this->getUserOrg();
		if(!$org){
			return false;
		}

		// check if the organization name changed by first grabbing the current name
		$db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName('name'));
        $query->from($db->quoteName('#__ta_providers'));
		$query->where($db->quoteName('id') . '=' . $org);
		$db->setQuery($query);
		$oldOrgName = $db->loadResult();

		if($oldOrgName != $data['name']){
			// the user changed the organization name, email us
			$mailer = JFactory::getMailer();
			$mailer->isHTML(true);
			$mailer->Encoding = 'base64';

			// set the sender to the site default
			$config = JFactory::getConfig();
			$sender = array(
				$config->get('config.mailfrom'),
				$config->get('config.fromname')
			);
			$mailer->setSender($sender);

			$mailer->addRecipient('info@ta2ta.org');
			$mailer->setSubject('Organization Name Update on TA2TA Website');
			$mailer->setBody("A user has updated their organization name on the TA2TA website. Previously, the organization was known as<br><br><b>$oldOrgName</b><br><br>and it will now be known as<br><br><b>{$data['name']}</b>.<br><br>The user indicated that they had the approval of OVW in order to make this change.");

			$mailer->Send();
		}

		// grab the database object and begin the query
        $query = $db->getQuery(true);

        // fields to update
        $fields = array(
        	$db->quoteName('name') . '=' . $db->quote($data['name']),
        	$db->quoteName('website') . '=' . $db->quote($data['website']),
        	$db->quoteName('logo') . '=' . $db->quote($data['logo']),
        	$db->quoteName('modified') . '=' . $db->quote(gmdate('Y-m-d H:i:s')),
        	$db->quoteName('modified_by') . '=' . $db->quote($this->getUserId())
        );

		// construct the query
		$query->update($db->quoteName('#__ta_providers'));
		$query->set($fields);
		$query->where($db->quoteName('id') . '=' . $org);

		// set and execute the query
		$db->setQuery($query);
		$result = $db->query();

		// check the result
		if($result){
			// process the logo image
			if(!empty($data['logo'])){
				$tmpPath = JPATH_SITE . '/media/com_ta_providers/tmp/' . $data['logo'];
				if(file_exists($tmpPath)){
					// move the tmp file into its permenant home 
					rename($tmpPath, JPATH_SITE . '/media/com_ta_providers/logos/' . $data['logo']);
				}
			}

			return true;
		}else{
			return false;
		}
	}
}