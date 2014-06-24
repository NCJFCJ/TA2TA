<?php
/**
 * @package     com_help_videos
 * @copyright   Copyright (C) 2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of help video records.
 */
class Help_videosModelVideos extends JModelList {

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

        $query->from('`#__help_videos` AS a');
        $query->where($db->quoteName('a.state') . '=' . $db->quote('1'));
    
		// Join over the foreign key 'category'
		$query->select($db->quoteName('cat.name', 'category_name'));
		$query->join('LEFT', $db->quoteName('#__help_videos_categories', 'cat') . ' ON ' . $db->quoteName('cat.id') . '=' . $db->quoteName('a.category'));

		//Filtering category
		$filter_category = $this->state->get("filter.category");
		if ($filter_category) {
			$query->where($db->quoteName('a.category') . '=' . $db->quote($filter_category));
		}

        // order by published
        $query->order($db->quoteName('a.published') . ' DESC');

        return $query;
    }

    public function getItems(){
        return parent::getItems();
    }

    /**
     * Returns an object consisting of an alphabetical list of all
     * active categories containing active videos
     */
    public function getCategories(){
        $db = $this->getDbo();
        
        // sub query - active video category ids
        $subquery = $db->getQuery(true);
        $subquery->select('DISTINCT ' . $db->quoteName('category'));
        $subquery->from($db->quoteName('#__help_videos'));
        $subquery->where($db->quoteName('state') . '=' . $db->quote('1'));

        // category query
        $query = $db->getQuery(true);
        $query->select(array(
            $db->quoteName('id'),
            $db->quoteName('name')
        ));
        $query->from($db->quoteName('#__help_videos_categories'));
        $query->where($db->quoteName('state') . '=' . $db->quote('1') . ' AND ' . $db->quoteName('id') . ' IN(' . $subquery . ')');
        $query->order($db->quoteName('name') . ' ASC');
        $db->setQuery($query);
        return $db->loadObjectList();
    }
}