<?php
/**
 * @package     com_event_manager
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

/**
 * Ta_calendar model.
 */
class Event_managerModelMeeting extends JModelForm{
    
    var $_item = null;
   	protected $user = null;
    
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState(){
		$app = JFactory::getApplication('com_event_manager');

		// Load state from the request userState on edit or from the passed variable on default
        if (JFactory::getApplication()->input->get('layout') == 'edit') {
            $id = JFactory::getApplication()->getUserState('com_event_manager.edit.meeting.id');
        } else {
            $id = JFactory::getApplication()->input->get('id');
            JFactory::getApplication()->setUserState('com_event_manager.edit.meeting.id', $id);
        }
		$this->setState('meeting.id', $id);

		// Load the parameters.
        $params = $app->getParams();
        $params_array = $params->toArray();
        if(isset($params_array['item_id'])){
            $this->setState('meeting.id', $params_array['item_id']);
        }
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
		if ($this->_item === null){
			$this->_item = false;

			if (empty($id)) {
				$user = JFactory::getUser();	
				$id = $user->id;
			}

			// Get a level row instance.
			$table = $this->getTable();

			// Attempt to load the row.
			if ($table->load($id)){
				// Convert the JTable to a clean JObject.
				$properties = $table->getProperties(1);
				$this->_item = JArrayHelper::toObject($properties, 'JObject');
			} elseif ($error = $table->getError()) {
				$this->setError($error);
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
		$form = $this->loadForm('com_event_manager.meeting', 'meeting', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData(){
		$data = JFactory::getApplication()->getUserState('com_event_manager.edit.meeting.data', array());
        if(empty($data)){
            $data = $this->getData();
        }
        
        return $data;
	}

	/**
	 * Method to retrieve topic areas from the calendar data, table ta_calendar_topic_areas.
	 *
	 * @return	array of objects	Id and description of topic areas.
	 */
	public function getTopicAreas(){
		// grab the database object and begin the query
	    $db = JFactory::getDbo();
	    $query = $db->getQuery(true);

		$query->select($db->quoteName(array('id', 'name')));
		$query->from($db->quoteName('#__ta_calendar_topic_areas'));
		$query->where($db->quoteName('state') . ' = '. $db->quote('1'));
		$query->order('name ASC');

		$db->setQuery($query);
		return $db->loadObjectList();
		 
	}
	
	/**
	 * Method to retrieve grant descripsions from table grant_programs.
	 *
	 * @return	array of objects	Id and description of grant programs.
	 */
	public function getGrantPrograms(){
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('id', 'name')));
		$query->from($db->quoteName('#__grant_programs'));
		$query->where($db->quoteName('state') . ' = '. $db->quote('1'));
		$query->order('name ASC');

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Method to retrieve target audiences from table target_audiences.
	 *
	 * @return	array of objects	Id and description of target audiences.
	 */
	public function getTargetAudiences(){
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('id', 'name')));
		$query->from($db->quoteName('#__target_audiences'));
		$query->where($db->quoteName('state') . ' = '. $db->quote('1'));
		$query->order('name ASC');

		$db->setQuery($query);
		return $db->loadObjectList();
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
		$db = JFactory::getDBO();
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
	 * Returns all provider programs
	 */
	public function getProviderProjects(){
		// get the user's organization
		$org = $this->getUserOrg();
		if($org > 0){
			// get the projects for this provider
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select(array(
				$db->quoteName('id'),
				$db->quoteName('title')
			));
			$query->from($db->quoteName('#__tapd_provider_projects'));
			$query->where($db->quoteName('state') . '=1 AND ' . $db->quoteName('provider') . '=' . $org);
			$query->order($db->quoteName('title') . ' ASC');
			$db->setQuery($query);
			return $db->loadObjectList();
		}
		return new stdClass();
	}
}