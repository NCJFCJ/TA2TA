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
class Ta_calendarModelevents extends JModelList{

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array()){
        if(empty($config['filter_fields'])){
            $config['filter_fields'] = array(
                'id', 'a.id',
                'state', 'a.state',
                'approved', 'a.approved',
                'datetime', 'a.datetime',
                'title', 'a.title',
                'summary', 'a.summary',
                'type', 'a.type',
                'event_url', 'a.event_url',
                'registration_url', 'a.registration_url',
                'created_by', 'a.created_by',
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     */
    protected function populateState($ordering = null, $direction = null){
        // Initialise variables.
        $app = JFactory::getApplication('administrator');

        // Load the filter state.
        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);
        
		//Filtering approved
		$this->setState('filter.approved', $app->getUserStateFromRequest($this->context.'.filter.approved', 'filter_approved', '', 'string'));

		//Filtering type
		$this->setState('filter.type', $app->getUserStateFromRequest($this->context.'.filter.type', 'filter_type', '', 'string'));

        // Load the parameters.
        $params = JComponentHelper::getParams('com_ta_calendar');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.title', 'asc');
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param	string		$id	A prefix for the store id.
     * @return	string		A store id.
     * @since	1.6
     */
    protected function getStoreId($id = ''){
        // Compile the store id.
        $id.= ':' . $this->getState('filter.search');
        $id.= ':' . $this->getState('filter.state');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
    protected function getListQuery(){
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
    	$query->select('#__ta_calendar_event_types.name AS typess_name');
    	$query->join('LEFT', '#__ta_calendar_event_types AS #__ta_calendar_event_types ON #__ta_calendar_event_types.id = a.type');
    	
        // Join over the user field 'created_by'
    	$query->select('created_by.name AS created_by');
    	$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');
            
        // Filter by published state
        $published = $this->getState('filter.state');
        if (is_numeric($published)) {
            $query->where('a.state = '.(int) $published);
        } else if ($published === '') {
            $query->where('(a.state IN (0, 1))');
        }
    
        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where('( a.title LIKE '.$search.' )');
            }
        }

		//Filtering approved
		$filter_approved = $this->state->get("filter.approved");
		if ($filter_approved != '') {
            if($filter_approved == 1){
                // only show the approved
                $query->where("a.approved != '0000-00-00 00:00:00'");
            }else{
                // only show pending approval
                $query->where("a.approved = '0000-00-00 00:00:00'");
            }			
		}

		//Filtering type
		$filter_type = $this->state->get("filter.type");
		if ($filter_type) {
			$query->where("a.type = '".$db->escape($filter_type)."'");
		}


        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }

    public function getItems() {
        $items = parent::getItems();
        
		foreach ($items as $oneItem) {

			if (isset($oneItem->type)) {
				$values = explode(',', $oneItem->type);

				$textValue = array();
				foreach ($values as $value){
					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query  ->select('name')
							->from('`#__ta_calendar_event_types`')
							->where('id = ' .$value);
					$db->setQuery($query);
					$results = $db->loadObject();
					if ($results) {
						$textValue[] = $results->name;
					}
				}

			$oneItem->type = !empty($textValue) ? implode(', ', $textValue) : $oneItem->type;

			}
		}
        return $items;
    }
}