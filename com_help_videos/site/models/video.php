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
class Help_videosModelVideo extends JModelList{

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
     * Retrieves the list of categories that contain videos
     *
     * return array of objects
     */
    public function getCategories(){
        // establish the database connection
        $db = JFactory::getDbo();

        // construct the subquery (pulls a list of categories with videos by id)
        $subquery = $db->getQuery(true);
        $subquery->select('DISTINCT ' . $db->quoteName('category'));
        $subquery->from('#__help_videos');
        $subquery->where($db->quoteName('state') . '=' . $db->quote('1'));

        // construct the main query
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array(
            'id',
            'name'
        )));
        $query->from($db->quoteName('#__help_videos_categories'));
        $query->where($db->quoteName('id') . ' IN(' . $subquery . ')');
        $query->order($db->quoteName('name') . ' ASC');
        $db->setQuery($query);

        // return the result
        return $db->loadObjectList('id');
    }

    /**
     * Retrieves the details of the current video from the database
     *
     * @return object
     */
    public function getVideo(){
        // get the video id (set in the router)
        $video = JRequest::getVar('id');

        // get the video information
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array(
            'category',
            'title',
            'summary',
            'youtube_id',
            'duration',
            'published'
        )));
        $query->from($db->quoteName('#__help_videos'));
        $query->where($db->quoteName('id') . '=' . (int) $video);
        $db->setQuery($query);

        return $db->loadObject();
    }

    /**
     * Retrieves all videos except the current video
     *
     * @return array of objects
     */
    public function getVideos(){
        // get the video id (set in the router)
        $video = JRequest::getVar('id');
        // get the category id (set in the router)
        $category = JRequest::getVar('catid');

        // establish the database connection
        $db = JFactory::getDbo();
        
        // construct the query
        $query = $db->getQuery(true);
        $query->select(array(
            $db->quoteName('v.id'),
            $db->quoteName('v.title'),
            $db->quoteName('v.category'),
            $db->quoteName('v.alias'),
            $db->quoteName('v.youtube_id'),
            $db->quoteName('v.duration'),
            $db->quoteName('v.published'),
            'CONCAT_WS(":", ' . $db->quoteName('v.id') . ', ' . $db->quoteName('v.alias') . ') as video_slug',
            'CONCAT_WS(":", ' . $db->quoteName('c.id') . ', ' . $db->quoteName('c.alias') . ') as category_slug',
        ));
        $query->from($db->quoteName('#__help_videos', 'v'));
        $query->join('LEFT', $db->quoteName('#__help_videos_categories', 'c') . ' ON ' . $db->quoteName('c.id') . '=' . $db->quoteName('v.category'));
        $query->where($db->quoteName('v.state') . '=' . $db->quote('1') . ' AND ' . $db->quoteName('v.id') . '!=' . (int) $video);
        $query->order($db->quoteName('v.published') . ' DESC');
        $db->setQuery($query);

        // return the result
        return $db->loadObjectList();
    }
}