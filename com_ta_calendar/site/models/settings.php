<?php
/**
 * @version     1.3.0
 * @package     com_ta_calendar
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

/**
 * Ta_calendar model.
 */
class Ta_calendarModelSettings extends JModelForm{
    
    var $_item = null;
    
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState(){
		$app = JFactory::getApplication('com_ta_calendar');

		// Load state from the request userState on edit or from the passed variable on default
        if (JFactory::getApplication()->input->get('layout') == 'edit') {
            $id = JFactory::getApplication()->getUserState('com_ta_calendar.edit.event.id');
        } else {
            $id = JFactory::getApplication()->input->get('id');
            JFactory::getApplication()->setUserState('com_ta_calendar.edit.event.id', $id);
        }
		$this->setState('event.id', $id);

		// Load the parameters.
        $params = $app->getParams();
        $params_array = $params->toArray();
        if(isset($params_array['item_id'])){
            $this->setState('event.id', $params_array['item_id']);
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
		$form = $this->loadForm('com_ta_calendar.settings', 'settings', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_ta_calendar.edit.settings.data', array());
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
		// grab the user object
		$user = JFactory::getUser();
		
		// grab the database object and begin the query
        $db = JFactory::getDbo();
				
		// construct the query
		$query = 'INSERT INTO ' . $db->quoteName('#__ta_calendar_user_settings');
		$query .= ' (' . $db->quoteName('user') . ',' . $db->quoteName('timezone') . ',' . $db->quoteName('view') . ',' . $db->quoteName('filters') . ')';
		$query .= ' VALUES (' . $user->id . ',' . $db->quote($data['timezone']) . ',' . $db->quote($data['view']) . ',' . $db->quote($data['filters']) . ')';
		$query .= ' ON DUPLICATE KEY UPDATE ';
		$query .= ' ' . $db->quoteName('user') . '=' . $user->id . ',';
		$query .= ' ' . $db->quoteName('timezone') . '=' . $db->quote($data['timezone']) . ',';
		$query .= ' ' . $db->quoteName('view') . '=' . $db->quote($data['view']) . ',';
		$query .= ' ' . $db->quoteName('filters') . '=' . $db->quote($data['filters']) . ';';
		
		// set and execute the query
		$db->setQuery($query);
		$result = $db->execute();

		// check the result
		if($result){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Inverts the filters based on the database values and combines them into a JSON string
	 * @param multi-dimmensional array of all data to be saved
	 * @return multi-dimmensional array of all data to be saved
	 */
	function prepareFilters($data){
		// locally store the data we need
		$eventTypes = $data['filters']['eventTypes'];
		$grantPrograms = $data['filters']['grantPrograms'];
		$targetAudiences = $data['filters']['targetAudiences'];
		$topicAreas = $data['filters']['topicAreas'];
		
		// unset these variables from the master data array
		unset($data['filters']);
				
		// build the std class object
		$filters = new stdClass;
		$filters->eventTypes = $eventTypes;
		$filters->grantPrograms = $grantPrograms;
		$filters->targetAudiences = $targetAudiences;
		$filters->topicAreas = $topicAreas;
		
		// convert to JSON and add to the data array
		$data['filters'] = json_encode($filters);
		
		// return the modified data array
		return $data;
	}
}