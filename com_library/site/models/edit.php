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
        if (JFactory::getApplication()->input->get('layout') == 'edit') {
            $id = JFactory::getApplication()->getUserState('com_library.edit.resource.id');
        } else {
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
	 * Gets the organization information the current user's organization.
	 * 
	 * @return object An object containing the organization information
	 */ 
	public function getOrg(){
		// get the user's organization
		$org = $this->getUserOrg();
		
		// get the item information
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id', 'name')));
		$query->from($db->quoteName('#__ta_providers'));
		$query->where($db->quoteName('id') . '=' . $org);
		$db->setQuery($query, 0, 1);
		
		// check that the query was successful
		if(!($organization = $db->loadObject())){
			JError::raiseWarning(100, 'Unable to retrieve organization details.');
			return false;
		}
		
		// return the organization information
		return $organization;
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
		if (empty($form)) {
			return false;
		}

		return $form;
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
		$query->where($db->quoteName('l.org') . ' = ' . $db->quote($this->getUserOrg()) . ' AND l.id=' . $db->quote($id));
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
			return $items;
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
	 * Returns the username of the current user
	 */
	public function getUserName(){
		// get the user object	
		$user = $this->getUserObj();
		
		// return the username
		return $user->username;
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
		$data['org'] = $this->getUserOrg();
		if(!$data['org']){
			return false;
		}
		$admin_model = new LibraryModelitem();
		if($admin_model->save($data)){
			if($data['id'] == 0){
				// Email notification that a new item was added

				// create a mailer object	
				$mailer = JFactory::getMailer();
				
				$mailer->isHTML(true);
				$mailer->Encoding = 'base64';
				
				// set the sender to the site default
				$config = JFactory::getConfig();
				$sender = array( 
			    $config->get('config.mailfrom'),
			    $config->get('config.fromname'));

				$mailer->setSender($sender);
				
				// set the recipient
				$mailer->addRecipient('info@ta2ta.org');

				// set the message subject
				$mailer->setSubject('[TA2TA] New Library Item Pending Approval');

				// set the message body
				$mailer->setBody('A user has uploaded a new Library Item entitled "' . $data['name'] . '" to the TA2TA website which is waiting to be approved. Please visit the <a href="https://ta2ta.org/administrator/index.php?option=com_library&view=items">library component</a> in the TA2TA administrator console to review and approve this item. Remember that you must check the website to ensure the library still loads after this item is approved. If it fails to load, please change the status back to Pending Approval and contact our web development team.');

				// send the message, if it errors out, just ignore it as we don't want the user affected
				$mailer->Send();
			}
			return true;
		}else{
			return false;
		}
	}
}
?>