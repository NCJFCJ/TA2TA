<?php
/**
 * @package     com_help_videos
 * @copyright   Copyright (C) 2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Help Video records
 */
class Help_videosModelvideos extends JModelList {

    /**
     * Constructor
     *
     * @param    array    An optional associative array of configuration settings
     * @see      JController
     * @since    1.6
     */
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'state', 'a.state',
                'title', 'a.title',
                'category', 'a.category'
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state
     *
     * Note. Calling getState in this method will result in recursion
     */
    protected function populateState($ordering = null, $direction = null) {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');

        // Load the filter state.
        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);

        $category = $app->getUserStateFromRequest($this->context . '.filter.category', 'filter_category');
        $this->setState('filter.category', $category);

		// Load the parameters.
        $params = JComponentHelper::getParams('com_help_videos');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.title', 'asc');
    }

    /**
     * Method to get a store id based on model configuration state
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements
     *
     * @param	string		$id	A prefix for the store id
     * @return	string		A store id
     * @since	1.6
     */
    protected function getStoreId($id = '') {
        // Compile the store id.
        $id.= ':' . $this->getState('filter.search');
        $id.= ':' . $this->getState('filter.state');
        $id.= ':' . $this->getState('filter.category');

        return parent::getStoreId($id);
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
        $query->from('`#__help_videos` AS a');
        
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

        // Filter by category
        $category = $this->getState('filter.category');
        if(is_numeric($category)){
            $query->where('a.category = ' . (int) $category);
        }

        return $query;
    }

    public function getItems() {
        $items = parent::getItems();
        
		// get all categories from the databse
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select(array(
            $db->quoteName('id'),
            $db->quoteName('name')
        ));
        $query->from($db->quoteName('#__help_videos_categories'));
        $db->setQuery($query);
        $categories = $db->loadAssocList('id');
        
        // apply the categories to the table data
        foreach ($items as &$oneItem) {

			if (isset($oneItem->category)) {
                $oneItem->category = $categories[$oneItem->category]['name'];
			}
		}
        return $items;
    }
}