<?php
/**
 * @package     com_library
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @author      NCJFCJ <zdraper@ncjfcj.org> - http://ncjfcj.org
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.helper');
jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

// require the admin model
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/models/item.php');

// require the helper
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/library.php');

/**
 * Library model.
 */
class LibraryModeledit extends JModelForm{
		
	var $_item = null;
	protected $resource = null;
	protected $user = null;
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState(){
		$app = JFactory::getApplication('com_library');

		// Load state from the request userState on edit or from the passed variable on default
    if(JFactory::getApplication()->input->get('layout') == 'edit'){
        $id = JFactory::getApplication()->getUserState('com_library.edit.resource.id');
    }else{
        $id = JFactory::getApplication()->input->get('id');
        JFactory::getApplication()->setUserState('com_library.edit.resource.id', $id);
    }
		$this->setState('resource.id', $id);

		// Load the parameters.
    $params = $app->getParams();
    $params_array = $params->toArray();
    if(isset($params_array['item_id'])){
      $this->setState('resource.id', $params_array['item_id']);
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
		$form = $this->loadForm('com_library.edit.settings.data', 'edit', array('control' => 'jform', 'load_data' => $loadData));
		if(empty($form)){
			return false;
		}

		return $form;
	}

	/**
	 * Returns a list of projects
	 *
	 * @return array of objects
	 */
	public function getProjects(){
		$projects = array();

		// obtain the projects
		$db	= $this->getDbo();
		$query = $db->getQuery(true);
		$query->select(array(
			$db->quoteName('id'),
			$db->quoteName('title')
		));
		$query->from($db->quoteName('#__tapd_provider_projects'));
		$query->where($db->quoteName('provider') . ' = ' . $db->quote(LibraryHelper::getUserOrgId()) . ' AND ' . $db->quoteName('state') . ' >= ' . $db->quote('0'));
		$db->setQuery($query);
		try{
			$projects = $db->loadObjectList();
		}catch(Exception $e){
			JError::raiseWarning(100, 'Unable to retrieve the list of projects for the current organization.');
		}

		return $projects;
	}
	
	/**
	 * Returns an object containing the data for a single resource
	 * 
	 * @return indexed array of objects
	 */
	public function getResource(){
		$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

		// if id is 0, don't continue
		if(empty($id)){
			return new stdClass();
		}		
				
		// don't run if we already have the data
		if(!empty($this->resources)){
			return $this->resources;
		}	
		
		// variables	
		$db	= $this->getDbo();
		$resource = new stdClass();
		
		// obtain the basic item information
		$query = $db->getQuery(true);
		$query->select(array(
			$db->quoteName('l.id'),
			$db->quoteName('l.state'),
			$db->quoteName('l.name'),
			$db->quoteName('l.description'),
			$db->quoteName('l.base_file_name'),
			$db->quoteName('u.name','created_by')
		));
		$query->from($db->quoteName('#__library', 'l'));
		$query->join('LEFT', $db->quoteName('#__users', 'u') . ' ON (' . $db->quoteName('l.created_by') . '=' . $db->quoteName('u.id') . ')');
		$query->where($db->quoteName('l.org') . ' = ' . $db->quote(LibraryHelper::getUserOrgId()) . ' AND l.id=' . $db->quote($id));
		$query->order($db->quoteName('l.name') . ' ASC');
		$db->setQuery($query);
		try{
			$resource = $db->loadObject();
		}catch(Exception $e){
			JError::raiseWarning(100, 'Unable to retrieve resources. Please contact us.');
			return $resource;
		}
		
		if(empty($resource)){
			return new stdClass;
		}

		// obtain the target audiences
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array(
			'id',
			'library_item',
			'target_audience'
		)));
		$query->from($db->quoteName('#__library_target_audiences'));
		$db->setQuery($query);
		try{
			$targetAudiences = $db->loadObjectList();
		}catch(Exception $e){
			JError::raiseWarning(100, 'Unable to retrieve resource target audiences. Please contact us.');
			return $resource;
		}
		
		// combine the target audiences with the library item
		$resource->target_audiences = array();
		foreach($targetAudiences as $key => &$targetAudience){
			
			// check if this target audience belongs with the item
			if($targetAudience->library_item == $resource->id){
				// add this target audience to the array associated with the item
				$resource->target_audiences[] = $targetAudience->target_audience;
				
				// remove this item so it isn't checked in subsequent loops
				unset($targetAudiences[$key]);
			}
		}
		$resource->target_audiences = implode(',',$resource->target_audiences);
			
		// pdf file
		$resource->document_path = '/media/com_library/resources/' . $resource->id . '-' . $resource->base_file_name . '.pdf';
		if(!file_exists(JPATH_SITE . $resource->document_path)){
			$resource->document_path = '';
		}	
		
		// cover file
		$resource->cover_path = '/media/com_library/covers/' . $resource->id . '-' . $resource->base_file_name . '.png';
		if(!file_exists(JPATH_SITE . $resource->cover_path)){
			$resource->cover_path = '/media/com_library/covers/no-cover.jpg';
		}

		$this->resource = $resource;
		return $resource;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData(){
		$data = JFactory::getApplication()->getUserState('com_library.edit.settings.data', array());
    if(empty($data)){
      $data = $this->getResource();
    }
    
    return $data;
	}
	
	/**
	 * Saves the user's settings
	 * 
	 * @param $data Associative Array containing data to be saved
	 * @return boolean True on success, false otherwise
	 */
 	public function save($data){
		$data['org'] = LibraryHelper::getUserOrgId();
		if(!$data['org']){
			return false;
		}
		$admin_model = new LibraryModelitem();
		if($admin_model->save($data)){
			return true;
		}else{
			return false;
		}
	}
}
?>