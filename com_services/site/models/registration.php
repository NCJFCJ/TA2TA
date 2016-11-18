<?php
/**
 * @package     com_services
 * @copyright   Copyright (C) 2016 NCJFCJ. All rights reserved.
 * @author      Zadra Design - http://zadradesign.com
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

/**
 * Service Registration model
 */
class ServicesModelRegistration extends JModelForm{
    
  /**
   * Gets the data pertaining to the given slug 
   */
  public function getData(){
  	// get the alias
  	$alias = JRequest::getVar('alias');

  	// get the service type
  	$service_type = substr($_SERVER['REQUEST_URI'], 1, strpos($_SERVER['REQUEST_URI'], 's') - 1);

  	if($alias){
  		switch($service_type){
  			case 'meeting':
  				if($return = $this->getMeetingData($alias)){
  					return $return;
  				}
  				break;
  			case 'roundtable':
  				if($return = $this->getRoundtableData($alias)){
  					return $return;
  				}
  				break;
  			case 'webinar':
  				if($return = $this->getWebinarData($alias)){
  					return $return;
  				}
  				break;
  		}
		}

	  // no page was found
	  throw new Exception(JText::_('COM_SERVICES_ERROR_MESSAGE_NOT_FOUND'), 404);
  }

  /**
   * Retreives data for the specified meeting
   * 
   * @param String The system alias of this item
   * @return Object with meeting data on success, false on failure
   */
  private function getMeetingData($alias){
  	$db = $this->getDbo();
    $query = $db->getQuery(true);
    $query->select($db->quoteName(array(
    	'mr.id',
    	'mr.project',
    	'mr.suggested_dates',
    	'mr.description',
    	'mr.registration',
    	'mr.registration_cutoff',
    	'mr.registration_adv_accessibility',
    	'mr.registration_q1',
    	'mr.registration_q1_type',
    	'mr.registration_q2',
    	'mr.registration_q2_type',
    	'mr.registration_q3',
    	'mr.registration_q3_type',
    	'tp.name',
    	'tp.website',
    	'tp.logo'
    )));
    $query->from($db->quoteName('#__services_meeting_requests', 'mr'));
    $query->join('LEFT', $db->quoteName('#__ta_providers', 'tp') . ' ON (' . $db->quoteName('tp.id') . ' = ' . $db->quoteName('mr.org') . ')');
    $query->where($db->quoteName('mr.alias') . '=' . $db->quote($alias) . ' AND ' . $db->quoteName('mr.state') . ' = ' . $db->quote('0'));
		$db->setQuery($query, 0, 1);  	
		if(!($meeting = $db->loadObject())){
		  return false;
		}

		// put in some required fields
		$meeting->series = 0;
		$meeting->title = $meeting->name . ' Meeting';
		
		// return the result
		return $meeting;
  }

  /**
   * Retreives data for the specified roundtable
   * 
   * @param String The system alias of this item
   * @return Object with roundtable data on success, false on failure
   */
  private function getRoundtableData($alias){
  	$db = $this->getDbo();
    $query = $db->getQuery(true);
    $query->select($db->quoteName(array(
    	'rr.id',
    	'rr.project',
    	'rr.topic',
    	'rr.suggested_dates',
    	'rr.description',
    	'rr.proposed_locations',
    	'rr.registration',
    	'rr.registration_cutoff',
    	'rr.registration_adv_accessibility',
    	'rr.registration_q1',
    	'rr.registration_q1_type',
    	'rr.registration_q2',
    	'rr.registration_q2_type',
    	'rr.registration_q3',
    	'rr.registration_q3_type',
    	'tp.name',
    	'tp.website',
    	'tp.logo'
    )));
    $query->from($db->quoteName('#__services_roundtable_requests', 'rr'));
    $query->join('LEFT', $db->quoteName('#__ta_providers', 'tp') . ' ON (' . $db->quoteName('tp.id') . ' = ' . $db->quoteName('rr.org') . ')');
    $query->where($db->quoteName('rr.alias') . '=' . $db->quote($alias) . ' AND ' . $db->quoteName('rr.state') . ' = ' . $db->quote('0'));
		$db->setQuery($query, 0, 1);
		if(!($roundtable = $db->loadObject())){
		  return false;
		}

		// put in some required fields
		$roundtable->title = $roundtable->topic;
		$roundtable->series = 0;
		
		// return the result
		return $roundtable;
  }

  /**
   * Retreives data for the specified webinar
   * 
   * @param String The system alias of this item
   * @return Object with webinar data on success, false on failure
   */
  private function getWebinarData($alias){
  	$db  = $this->getDbo();
    $query = $db->getQuery(true);
    $query->select($db->quoteName(array(
    	'wr.id',
    	'wr.project',
    	'wr.start',
    	'wr.end', 
    	'wr.series',
    	'wr.registration',
    	'wr.registration_adv_accessibility',
    	'wr.registration_q1',
    	'wr.registration_q1_type',
    	'wr.registration_q2',
    	'wr.registration_q2_type',
    	'wr.registration_q3',
    	'wr.registration_q3_type',
    	'wr.title',
    	'wr.sub_title',
    	'wr.description',
    	'wr.file',
    	'wr.created',
    	'tp.name',
    	'tp.website',
    	'tp.logo'
    )));
    $query->from($db->quoteName('#__services_webinar_requests', 'wr'));
    $query->join('LEFT', $db->quoteName('#__ta_providers', 'tp') . ' ON (' . $db->quoteName('tp.id') . ' = ' . $db->quoteName('wr.org') . ')');
    $query->where($db->quoteName('wr.alias') . '=' . $db->quote($alias) . ' AND ' . $db->quoteName('wr.state') . ' = ' . $db->quote('0'));
		$db->setQuery($query);
		if(!($webinar = $db->loadObject())){
		  return false;
		}

		if($webinar->series){
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array(
				'id',
				'start',
				'end',
				'sub_title'
			)));
			$query->from($db->quoteName('#__services_webinar_requests'));
			$query->where($db->quoteName('created') . ' = ' . $db->quote($webinar->created) . ' AND ' . $db->quoteName('title') . ' = ' . $db->quote($webinar->title) . ' AND ' . $db->quoteName('state') . ' = ' . $db->quote('0') . ' AND ' . $db->quoteName('end') . '>NOW()');
			$query->order($db->quoteName('start') . ' ASC');
			$db->setQuery($query);
			$webinar->webinars = $db->loadObjectList();
		}
  	return $webinar;
  }
	        
	/**
	 * Method to get the profile form.
	 *
	 * The base form is loaded from XML 
   * 
	 * @param	array	$data	An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true){
		// Get the form.
		$form = $this->loadForm('com_services.registration', 'registration', array('control' => 'jform', 'load_data' => $loadData));
		if(empty($form)){
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
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_services.edit.registration.data', array());

		return $data;
	}
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState(){
		$app = JFactory::getApplication('com_services');

		// Load the parameters.
    $params = $app->getParams();
    $params_array = $params->toArray();
		$this->setState('params', $params);
	}

	/**
	 * Method to save the form data.
	 *
	 * @param	array		The form data.
	 * @return	mixed		The user id on success, false on failure.
	 * @since	1.6
	 */
	public function save($data){
		if(!$data){
			return false;
		}

		switch($data['service_type']){
			case 'meeting':
				return $this->saveMeeting($data);
			case 'roundtable':
				return $this->saveRoundtable($data);
			case 'webinar':
				return $this->saveWebinar($data);
		}
	}

	/**
	 * Method to save the registration data for a user registering to attend a meeting
	 * 
	 * @param array An array containing submission data from the user
	 * @return boolean True on success, false otherwise
	 */
	private function saveMeeting($data){
		// include the admin helper
		require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/services.php');

		// grab the database object and begin the query
		$db = $this->getDbo();

    // construct the query to save this attendee's data
    $query = $db->getQuery(true);
    $query->insert($db->quoteName('#__services_registrations'));
    $query->columns($db->quoteName(array(
    	'service',
    	'service_type',
			'fname',
			'lname',
			'email',
			'zip',
			'org',
			'occupation',
			'address',
			'address2',
			'phone',
			'fax',
			'accessibility',
			'accessibility_braille',
			'accessibility_interpreter',
			'accessibility_large_print',
			'accessibility_interpreter_lang',
			'accessibility_simultaneous_interpretation',
			'q1_answer',
			'q2_answer',
			'q3_answer',
			'registered'
		)));
	  $query->values(implode(',', array(
	  	$db->quote($data['services'][0]),
	  	$db->quote($data['service_type']),
	  	$db->quote($data['fname']),
	  	$db->quote($data['lname']),
	  	$db->quote(ServicesHelper::encrypt($data['email'])),
	  	$db->quote($data['zip']),
	  	$db->quote($data['organization']),
	  	$db->quote($data['occupation']),
			$db->quote(ServicesHelper::encrypt($data['address'])),
			$db->quote(ServicesHelper::encrypt($data['address2'])),
			$db->quote(ServicesHelper::encrypt($data['phone'])),
			$db->quote(ServicesHelper::encrypt($data['fax'])),
	  	$db->quote($data['accessibility']),
			$db->quote($data['accessibility_braille']),
			$db->quote($data['accessibility_interpreter']),
			$db->quote($data['accessibility_large_print']),
			$db->quote($data['accessibility_interpreter_lang']),
			$db->quote($data['accessibility_simultaneous_interpretation']),
			$db->quote($data['q1_answer']),
			$db->quote($data['q2_answer']),
			$db->quote($data['q3_answer']),
	  	'NOW()'
	  )));
    $db->setQuery($query);

		// execute the query
		if(!$db->execute()){
			return false;
		}

		// get the meeting information
    $query = $db->getQuery(true);
    $query->select($db->quoteName(array(
    		'mr.alias',
	    	'mr.suggested_dates',
	    	'mr.description',
	    	'tp.name',
	    	'tp.website'
	 	)));
    $query->from($db->quoteName('#__services_meeting_requests', 'mr'));
    $query->join('LEFT', $db->quoteName('#__ta_providers', 'tp') . ' ON (' . $db->quoteName('tp.id') . ' = ' . $db->quoteName('mr.org') . ')');
		$query->where($db->quoteName('mr.id') . '=' . $db->quote($data['services'][0]));
		$db->setQuery($query);
		$meeting = $db->loadObject();

		// email the user
		ServicesHelper::sendMeetingRegConf($meeting, $data);

		return true;
	}

	/**
	 * Method to save the registration data for a user registering to attend a roundtable
	 * 
	 * @param array An array containing submission data from the user
	 * @return boolean True on success, false otherwise
	 */
	private function saveRoundtable($data){
		// include the admin helper
		require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/services.php');

		// grab the database object and begin the query
		$db = $this->getDbo();

    // construct the query to save this attendee's data
    $query = $db->getQuery(true);
    $query->insert($db->quoteName('#__services_registrations'));
    $query->columns($db->quoteName(array(
    	'service',
    	'service_type',
			'fname',
			'lname',
			'email',
			'zip',
			'org',
			'occupation',
			'address',
			'address2',
			'phone',
			'fax',
			'accessibility',
			'accessibility_braille',
			'accessibility_interpreter',
			'accessibility_large_print',
			'accessibility_interpreter_lang',
			'accessibility_simultaneous_interpretation',
			'q1_answer',
			'q2_answer',
			'q3_answer',
			'registered'
		)));
	  $query->values(implode(',', array(
	  	$db->quote($data['services'][0]),
	  	$db->quote($data['service_type']),
	  	$db->quote($data['fname']),
	  	$db->quote($data['lname']),
	  	$db->quote(ServicesHelper::encrypt($data['email'])),
	  	$db->quote($data['zip']),
	  	$db->quote($data['organization']),
	  	$db->quote($data['occupation']),
			$db->quote(ServicesHelper::encrypt($data['address'])),
			$db->quote(ServicesHelper::encrypt($data['address2'])),
			$db->quote(ServicesHelper::encrypt($data['phone'])),
			$db->quote(ServicesHelper::encrypt($data['fax'])),
	  	$db->quote($data['accessibility']),
			$db->quote($data['accessibility_braille']),
			$db->quote($data['accessibility_interpreter']),
			$db->quote($data['accessibility_large_print']),
			$db->quote($data['accessibility_interpreter_lang']),
			$db->quote($data['accessibility_simultaneous_interpretation']),
			$db->quote($data['q1_answer']),
			$db->quote($data['q2_answer']),
			$db->quote($data['q3_answer']),
	  	'NOW()'
	  )));
    $db->setQuery($query);

		// execute the query
		if(!$db->execute()){
			return false;
		}

		// get the roundtable information
    $query = $db->getQuery(true);
    $query->select($db->quoteName(array(
    		'rr.alias',
				'rr.topic',
	    	'rr.suggested_dates',
	    	'rr.description',
	    	'tp.name',
	    	'tp.website'
	 	)));
    $query->from($db->quoteName('#__services_roundtable_requests', 'rr'));
    $query->join('LEFT', $db->quoteName('#__ta_providers', 'tp') . ' ON (' . $db->quoteName('tp.id') . ' = ' . $db->quoteName('rr.org') . ')');
		$query->where($db->quoteName('rr.id') . '=' . $db->quote($data['services'][0]));
		$db->setQuery($query);
		$roundtable = $db->loadObject();

		// email the user
		ServicesHelper::sendRoundtableRegConf($roundtable, $data);

		return true;
	}

	/**
	 * Method to save the registration data for a user registering to attend a webinar
	 * 
	 * @param array An array containing submission data from the user
	 * @return boolean True on success, false otherwise
	 */
	private function saveWebinar($data){
		// include the admin helper
		require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/services.php');

		// grab the database object and begin the query
		$db = $this->getDbo();

    // construct the query to save this attendee's data
    $query = $db->getQuery(true);
    $query->insert($db->quoteName('#__services_registrations'));
    $query->columns($db->quoteName(array(
    	'service',
    	'service_type',
			'fname',
			'lname',
			'email',
			'zip',
			'org',
			'occupation',
			'accessibility',
			'accessibility_braille',
			'accessibility_interpreter',
			'accessibility_large_print',
			'accessibility_interpreter_lang',
			'accessibility_simultaneous_interpretation',
			'q1_answer',
			'q2_answer',
			'q3_answer',
			'registered'
		)));

    foreach($data['services'] as $webinar_id){
		  $query->values(implode(',', array(
		  	$db->quote($webinar_id),
		  	$db->quote($data['service_type']),
		  	$db->quote($data['fname']),
		  	$db->quote($data['lname']),
		  	$db->quote(ServicesHelper::encrypt($data['email'])),
		  	$db->quote($data['zip']),
		  	$db->quote($data['organization']),
		  	$db->quote($data['occupation']),
		  	$db->quote($data['accessibility']),
				$db->quote($data['accessibility_braille']),
				$db->quote($data['accessibility_interpreter']),
				$db->quote($data['accessibility_large_print']),
				$db->quote($data['accessibility_interpreter_lang']),
				$db->quote($data['accessibility_simultaneous_interpretation']),
				$db->quote($data['q1_answer']),
				$db->quote($data['q2_answer']),
				$db->quote($data['q3_answer']),
		  	'NOW()'
		  )));
		}

    $db->setQuery($query);
    
		// execute the query
		if(!$db->execute()){
			return false;
		}

		// get the webinar information
    $query = $db->getQuery(true);
    $query->select($db->quoteName(array(
    		'wr.alias',
				'wr.title',
	    	'wr.adobe_number',
	    	'wr.start',
	    	'wr.end', 
	    	'wr.series',
	    	'wr.parent',
	    	'wr.sub_title',
	    	'tp.name',
	    	'tp.website'
	 	)));
    $query->from($db->quoteName('#__services_webinar_requests', 'wr'));
    $query->join('LEFT', $db->quoteName('#__ta_providers', 'tp') . ' ON (' . $db->quoteName('tp.id') . ' = ' . $db->quoteName('wr.org') . ')');
		$query->where($db->quoteName('wr.id') . '=' . $db->quote($data['services'][0]));
		$db->setQuery($query);
		$webinar = $db->loadObject();

		// check if this is a series, and if so, pull additional data
		if($webinar->series){
			// pull the master data
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array(
				'alias',
				'title',
	    	'adobe_number'
			)));
			$query->from($db->quoteName('#__services_webinar_requests'));
			$query->where($db->quoteName('id') . '=' . $db->quote($webinar->parent));
			$db->setQuery($query);
			$master_webinar = $db->loadObject();

			$webinar->adobe_number = $master_webinar->adobe_number;
			$webinar->alias = $master_webinar->alias;
			$webinar->title = $master_webinar->title;

			// pull the data for each webinar in the series
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array(
				'start',
				'end',
				'sub_title'
			)));
			$query->from($db->quoteName('#__services_webinar_requests'));
    	$query->where($db->quoteName('id') . ' IN (' . join(',', $data['services']) . ')');
    	$db->setQuery($query);
    	$webinar->webinars = $db->loadObjectList();
		}

		// email the user
		ServicesHelper::sendWebinarRegConf($webinar, $data);

		return true;
	}
}