<?php
/**
 * @package     com_library
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @author      NCJFCJ <zdraper@ncjfcj.org> - http://ncjfcj.org
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

// include the admin model
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/models/item.php');

// require the helper
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/library.php');

/**
 * Library model.
 */
class LibraryModelsettings extends JModelForm{
		
	var $_item = null;
	protected $resources = null;
	protected $user = null;
	
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
	 * Returns an array of objects each containing data for one resource
	 * 
	 * @return indexed array of objects
	 */
	public function getResources(){
		// don't run if we already have the data
		if(!empty($this->resources)){
			return $this->resources;
		}	
		
		// variables	
		$db	= $this->getDbo();
		$items = array();
		
		// obtain the basic item information
		$query = $db->getQuery(true);
		$query->select(array(
			$db->quoteName('l.id'),
			$db->quoteName('l.state'),
			$db->quoteName('l.name'),
			$db->quoteName('u.name','created_by')
		));
		$query->from($db->quoteName('#__library', 'l'));
		$query->join('LEFT', $db->quoteName('#__users', 'u') . ' ON (' . $db->quoteName('l.created_by') . '=' . $db->quoteName('u.id') . ')');
		$query->where($db->quoteName('l.org') . ' = ' . $db->quote(LibraryHelper::getUserOrgId()));
		$query->order($db->quoteName('l.name') . ' ASC');
		$db->setQuery($query);
		try{
			$items = $db->loadObjectList();
		}catch(Exception $e){
			JError::raiseWarning(100, 'Unable to retrieve resources. Please contact us.');
			return $items;
		}
		
		$this->resources = $items;
		return $items;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData(){
		$data = JFactory::getApplication()->getUserState('com_library.edit.settings.data', array());
        if (empty($data)) {
            $data = $this->getResources();
        }

        return $data;
	}
}
?>