<?php
/**
 * @package     com_ta_calendar
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Ta_calendar records.
 */
class Ta_calendarModelEvents extends JModelList {

	protected $user = null;
	
    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array()) {
        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState($ordering = null, $direction = null) {

        // Initialise variables.
        $app = JFactory::getApplication();

        // List state information
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
        $this->setState('list.limit', $limit);

        $limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);
        $this->setState('list.start', $limitstart);

        // List state information.
        parent::populateState($ordering, $direction);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
    protected function getListQuery() {
      // Create a new query object.
      $db = $this->getDbo();
      $query = $db->getQuery(true);

      // Select the required fields from the table.
      $query->select(
        $this->getState(
          'list.select', 'a.*'
        )
      );

      $query->from('`#__ta_calendar_events` AS a');

    // Join over the users for the checked out user.
    $query->select('uc.name AS editor');
    $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');
    
		// Join over the foreign key 'type'
		$query->select('#__ta_calendar_event_types_717072.name AS typess_name_717072');
		$query->join('LEFT', '#__ta_calendar_event_types AS #__ta_calendar_event_types_717072 ON #__ta_calendar_event_types_717072.id = a.type');

		// Join over the created by field 'created_by'
		$query->select('created_by.name AS created_by');
		$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');
        
    // Filter by search in title
    $search = $this->getState('filter.search');
    if(!empty($search)){
      if(stripos($search, 'id:') === 0){
        $query->where('a.id = ' . (int) substr($search, 3));
      }else{
        $search = $db->Quote('%' . $db->escape($search, true) . '%');
        $query->where('( a.title LIKE '.$search.' )');
      }
		}

		//Filtering approved
		$filter_approved = $this->state->get("filter.approved");
		if($filter_approved){
			$query->where("a.approved = '".$filter_approved."'");
		}

		//Filtering type
		$filter_type = $this->state->get("filter.type");
		if($filter_type){
			$query->where("a.type = '".$filter_type."'");
		}

      return $query;
  }

	/**
	 * Method to get the timezones from the database
	 */
	 
	public function getTimezones(){
		// build the query
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('abbr, description');
		$query->from('#__ta_calendar_timezones');
		$db->setQuery($query);
		
		// execute the query and return the result
		return $db->loadObjectList();
	}
	
	/**
	 * Method to get the settings for the current user
	 */

	public function getUserSettings(){
		// get the user object
		$user = JFactory::getUser();
		
		// build the query
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('timezone, view, filters, alerts');
		$query->from('#__ta_calendar_user_settings');
		$query->where('user = ' . ($user->id ? $user->id : 0));
		$db->setQuery($query, 0, 1);
		
		// execute the query
		$result = $db->loadObject();
		
		// execute the query and return the result
		if($result){
			// check an enforce defaults
			if(!isset($result->alerts)){
				$result->alerts = 1;
			}
			if(empty($result->timezone)){
				$result->timezone = 'America/New_York';
			}
			if(empty($result->view)){
				$result->view = 'month';
			}
			if(isset($result->filters)){
				// parse the json
				$result->filters = json_decode($result->filters);
			}
			if(!isset($result->filters) || empty($result->filters)){
				$result->filters = new stdClass;
			}
			if(!isset($result->filters->eventTypes) || empty($result->filters->eventTypes)){
				$result->filters->eventTypes = array();
			}
			if(!isset($result->filters->grantPrograms) || empty($result->filters->grantPrograms)){
				$result->filters->grantPrograms = array();
			}
			if(!isset($result->filters->targetAudiences) || empty($result->filters->targetAudiences)){
				$result->filters->targetAudiences = array();
			}
			if(!isset($result->filters->topicAreas) || empty($result->filters->topicAreas)){
				$result->filters->topicAreas = array();
			}
			return $result;
		}else{
			// establish defaults
			$result = new stdClass;
			$result->alerts = 1;
			$result->timezone = 'America/New_York';
			$result->view = 'month';
			$result->filters = new stdClass;
			$result->filters->eventTypes = array();
			$result->filters->grantPrograms = array();
			$result->filters->targetAudiences = array();
			$result->filters->topicAreas = array();
			return $result;
		}
	}

    public function getItems(){
        return parent::getItems();
    }
	
	/**
	 * Returns all active Event Types defined in the database
	 * 
	 * @return array of objects
	 */
	public function getEventTypes(){
		// Create a new query object
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		// Select the required columns
        $query->select('id, name');
		
		// Identify the table from which to pull
		$query->from('`#__ta_calendar_event_types`');
		
		// Constrain results to only active event types
		$query->where('state = 1');
		
		// Alphabatize
		$query->order('name ASC');
		
		// Set the query
		$db->setQuery($query);
		
		// Execute the query and return the result
		return $db->loadObjectList();
	}
	
	/**
	 * Returns all grant programs 
	 */
	public function getGrantPrograms(){
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select(array(
			$db->quoteName('id'),
			$db->quoteName('name'),
			$db->quoteName('fund')
		));
		$query->from($db->quoteName('#__grant_programs'));
		$query->where($db->quoteName('state') . '=1');
		$query->order($db->quoteName('name') . ' ASC');
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
	
	/**
	 * Returns all active Target Audiences defined in the administrator section of this component
	 * @return array of objects
	 */
	public function getTargetAudiences(){
		// Create a new query object
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		// Select the required columns
        $query->select('id, name');
		
		// Identify the table from which to pull
		$query->from('`#__target_audiences`');
		
		// Constrain results to only active event types
		$query->where('state = 1');
		
		// Alphabatize
		$query->order('name ASC');
		
		// Set the query
		$db->setQuery($query);
		
		// Execute the query and return the result
		return $db->loadObjectList();
	}
	
	/**
	 * Returns all active Topic Areas defined in the administrator section of this component
	 * @return array of objects
	 */
	public function getTopicAreas(){
		// Create a new query object
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		// Select the required columns
		$query->select('id, name');
		
		// Identify the table from which to pull
		$query->from('`#__ta_calendar_topic_areas`');
		
		// Constrain results to only active event types
		$query->where('state = 1');
		
		// Alphabatize
		$query->order('name ASC');
		
		// Set the query
		$db->setQuery($query);
		
		// Execute the query and return the result
		return $db->loadObjectList();
	}
}