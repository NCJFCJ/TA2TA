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
    * Retrieves the details of the current video from the database
    *
    * @return object
    */
    public function getVideo(){
        $id = JRequest::getVar('id');

        // get the video information
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array(
            'title',
            'summary',
            'youtube_id',
            'duration',
            'published'
        )));
        $query->from($db->quoteName('#__help_videos'));
        $query->where($db->quoteName('id') . '=' . (int) $id);
        $db->setQuery($query);

        return $db->loadObject();
    }
}