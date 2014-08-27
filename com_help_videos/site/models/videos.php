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

        $query->select(array(
            $db->quoteName('v.id'),
            $db->quoteName('v.title'),
            $db->quoteName('v.alias'),
            $db->quoteName('v.youtube_id'),
            $db->quoteName('v.duration'),
            $db->quoteName('v.published'),
            $db->quoteName('c.id', 'category_id'),
            $db->quoteName('c.name', 'category_name'),
            $db->quoteName('c.alias', 'category_alias'),
            'CONCAT_WS(":", ' . $db->quoteName('v.id') . ', ' . $db->quoteName('v.alias') . ') as video_slug',
            'CONCAT_WS(":", ' . $db->quoteName('c.id') . ', ' . $db->quoteName('c.alias') . ') as category_slug',
        ));

        $query->from($db->quoteName('#__help_videos', 'v'));
        $query->join('LEFT', $db->quoteName('#__help_videos_categories', 'c') . ' ON ' . $db->quoteName('c.id') . '=' . $db->quoteName('v.category'));
        $query->where($db->quoteName('v.state') . '=' . $db->quote('1'));

        // filtering category
        $filter_category = $this->state->get("filter.category");
        if($filter_category){
            $query->where($db->quoteName('v.category') . '=' . $db->quote($filter_category));
        }

        // order by published
        $query->order($db->quoteName('category_name') . ' ASC, ' . $db->quoteName('v.published') . ' DESC');

        return $query;
    }

    public function getItems(){
        return parent::getItems();
    }
}