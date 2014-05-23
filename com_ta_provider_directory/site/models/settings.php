<?php
/**
 * @version     2.0.0
 * @package     com_ta_provider_directory
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

// include the admin model
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/models/provider.php');

/**
 * Ta_provider_directory model.
 */
class Ta_provider_directoryModelsettings extends JModelForm{
		
	var $_item = null;
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
	 * Gets the listing for the current user's organization. Includes organization information, programs, and contacts
	 * 
	 * @return object An object containing the directory listing information for this organization. False on fail.
	 */ 
	public function getData(){
		// get the user's organization
		$org = $this->getUserOrg();
		
		// get the provider information
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id', 'name', 'website')));
		$query->from($db->quoteName('#__ta_providers'));
		$query->where($db->quoteName('id') . '=' . $org);
		$db->setQuery($query, 0, 1);
		
		// check that the query was successful
		if(!($listing = $db->loadObject())){
			JError::raiseWarning(100, 'Unable to retrieve organization details.');
			return false;
		}
		
		// get the projects
		$admin_model = new Ta_provider_directoryModelprovider();
		$listing->projects = $admin_model->getProjects($listing);
		
		// return the listing
		return $listing;
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
		$data = JFactory::getApplication()->getUserState('com_ta_calendar.edit.settings.data', array());
        if (empty($data)) {
            $data = $this->getData();
        }
        
        return $data;
	}
	
	/**
	 * Method to obtain all active grant programs
	 *
	 * @return	mixed	Array of Objects on success, empty array on failure.
	 */
	public function getGrantPrograms(){
		// variables
		$grantPrograms = array();
		$providerPrograms = array();
		
		// get all grant programs
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, name, fund, 0 AS checked');
		$query->from('#__grant_programs');
		$query->where('state = 1');
		$query->order('name ASC');
		$db->setQuery($query);
		try{
			$grantPrograms = $db->loadObjectList('id');
		}catch(Exception $e){
			JError::raiseWarning(100, 'Unable to retrieve grant programs.');
			return $grantPrograms;
		}
		
		// return the array
		return $grantPrograms;
	}
	
	/**
	 * Saves the user's settings
	 * 
	 * @param $data Associative Array containing data to be saved
	 * @return boolean True on success, false otherwise
	 */
 	public function save($data){
 		// get the user's organization
		$org = $this->getUserOrg();
		
 		// save the website
 		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__ta_providers'));
		$query->set(array(
			$db->quoteName('website') . '=' . $db->quote($data['website']),
			$db->quoteName('modified') . '=NOW()',
			$db->quoteName('modified_by') . '=' . $db->quote($this->getUserId())
		));
		$query->where($db->quoteName('id') . '=' . $org);
		$db->setQuery($query);
		if($db->query()){
			// save project data
			if($this->saveProjects($data['projects'], $org)){
				return true;
			}else{
				return false;
			}
		}else{
			JError::raiseWarning(100, 'Unable to save organization information.');
			return false;
		}
 	}
	
	/**
	 * Saves the projects for a single organization
	 * 
	 * @param data string JSON string containing project data
	 * @param org int ID of the organization
	 */
	protected function saveProjects($data, $org){
		if(is_int($org) && !empty($data)){
			// decode the project data	
			$projects = json_decode($data);
			
			// variables
			$insert_projects = array();
			$update_projects = array();
			
			foreach($projects as $project){
				// validate the data, if there is an error, state as much
				// id
				if(!isset($project->id)
				|| !preg_match('/^n?\d+$/', $project->id)){
					JError::raiseWarning(100, 'Unable to save project, previous entries were retained (' . __LINE__ . ').');
					break;
				}
				// state
				if(!isset($project->state)
				|| $project->state < -1 
				|| $project->state > 1){
					JError::raiseWarning(100, 'Unable to save project, previous entries were retained (' . __LINE__ . ').');
					break;
				}
				// title
				if(empty($project->title) 
				|| strlen($project->title) < 2 
				|| strlen($project->title) > 255 
				|| !preg_match('/^[a-zA-Z0-9():,\-\.\'\"\/\\\ ]*$/', $project->title)){
					JError::raiseWarning(100, 'Unable to save project, previous entries were retained (' . __LINE__ . ').');
					break;
				}
				// summary
				if(empty($project->summary) 
				|| strlen($project->summary) < 10 
				|| strlen($project->summary) > 1500){
					JError::raiseWarning(100, 'Unable to save project, previous entries were retained (' . __LINE__ . ').');
					break;
				}
				// grant programs
				if(empty($project->grantPrograms)){
					$project->grantPrograms = array();
				}else{
					$project->grantPrograms = array_filter($project->grantPrograms, 'ctype_digit');
				}
				// contacts
				if(empty($project->contacts)){
					$project->contacts = array();
				}else{
					$validContacts = array();
					// validate each contact
					foreach($project->contacts as $contact){
						// id
						if(!isset($contact->id) 
						|| !preg_match('/^n?\d+$/', $contact->id)){
							JError::raiseWarning(100, 'Unable to save contact, previous entries were retained (' . __LINE__ . ').');
							break;
						}
						// state
						if(!isset($contact->state)
						|| $contact->state < -1 
						|| $contact->state > 1){
							JError::raiseWarning(100, 'Unable to save project, previous entries were retained (' . __LINE__ . ').');
							break;
						}
						// first_name
						if(empty($contact->first_name) 
						|| strlen($contact->first_name) < 2 
						|| strlen($contact->first_name) > 30 
						|| !preg_match('/^[a-zA-Z-\. \\\]*$/', $contact->first_name)){
							JError::raiseWarning(100, 'Unable to save contact, previous entries were retained (' . __LINE__ . ').');
							break;
						}
						// last_name
						if(empty($contact->last_name) 
						|| strlen($contact->last_name) < 2 
						|| strlen($contact->last_name) > 30 
						|| !preg_match('/^[a-zA-Z-\' \\\]*$/', $contact->last_name)){
							JError::raiseWarning(100, 'Unable to save contact, previous entries were retained (' . __LINE__ . ').');
							break;
						}
						// title
						//$contact->title = mysql_real_escape_string($contact->title);
						if(isset($contact->title) 
						&& !empty($contact->title)
						&& (strlen($contact->title) < 2
						|| strlen($contact->title) > 255 
						|| !preg_match('/^[a-zA-Z()0-9 \\\]*$/', $contact->title))){
							JError::raiseWarning(100, 'Unable to save contact, previous entries were retained (' . __LINE__ . ').');
							break;
						}
						// email
						if(isset($contact->email) 
						&& !empty($contact->email)
						&& (strlen($contact->email) > 150 
						|| !filter_var($contact->email, FILTER_VALIDATE_EMAIL))){
							JError::raiseWarning(100, 'Unable to save contact, previous entries were retained (' . __LINE__ . ').');
							break;
						}
						// phone
						if(isset($contact->phone) 
						&& !empty($contact->phone)
						&& (strlen($contact->phone) < 10
						|| strlen($contact->phone) > 15 
						|| !preg_match('/^\d+$/', $contact->phone))){
							JError::raiseWarning(100, 'Unable to save contact, previous entries were retained (' . __LINE__ . ').');
							break;
						}
						// add this for later processing
						$validContacts[] = $contact;
					}
					// overwrite the contacts with the valid ones
					$project->contacts = $validContacts;
				}
				
				// databsae variables
				$db = JFactory::getDbo();
				$insert_columns = array(
					$db->quoteName('state'),
					$db->quoteName('created'),
					$db->quoteName('created_by'),
					$db->quoteName('title'),
					$db->quoteName('summary'),
					$db->quoteName('provider')
				);
				$insert_projects = array();
				$update_projects = array();

				// add this data to the appropriate variable for later use
				$first_id_char = substr($project->id, 0, 1);
				if($first_id_char == 'n'){
					// this is a new program
					$insert_projects[] = array(
						'contacts' => $project->contacts,
						'grantPrograms' => $project->grantPrograms,
						'queryValues' => array(
							$db->quote($project->state),
							'NOW()',
							$db->quote($this->getUserId()),
							$db->quote($project->title),
							$db->quote($project->summary),
							$db->quote($org)
						)
					);
				}else{
					// this is not a new program
					if($project->id > 0){
						$update_projects[] = array(
							'contacts' => $project->contacts,
							'id' => $project->id,
							'grantPrograms' => $project->grantPrograms,
							'queryFields' => array(
								$db->quoteName('state') . '=' . $db->quote($project->state),
								$db->quoteName('modified') . '=NOW()',
								$db->quoteName('modified_by') . '=' . $db->quote($this->getUserId()),
								$db->quoteName('title') . '=' . $db->quote($project->title),
								$db->quoteName('summary') . '=' . $db->quote($project->summary)
							)
						);
					}
				}
				
				// create new projects
				foreach($insert_projects as $insert_project){
					// new id variable	
					$newProjectID;
					
					// insert a single project
					$query = $db->getQuery(true);
					$query->insert('#__tapd_provider_projects');
					$query->columns($insert_columns);
					$query->values(implode(',', $insert_project['queryValues']));
					$db->setQuery($query);
					
					try{
						$db->query();
						$newProjectID = $db->insertid();
						
						// store grant programs
						$this->storeGrantPrograms($newProjectID, $insert_project['grantPrograms']);
						
						// store contacts
						$this->storeContacts($newProjectID, $insert_project['contacts']);
					}catch(Exception $e){
						JError::raiseWarning(100, 'Unable to save project (' . __LINE__ . ').');
					}
				}
				
				// update existing projects
				foreach($update_projects as $update_project){
					// update a single project
					$query = $db->getQuery(true);
					$query->update('#__tapd_provider_projects');
					$query->set(implode(',',$update_project['queryFields']));
					$query->where('id = ' . $db->quote($update_project['id']));
					$db->setQuery($query);
					try{
						$db->query();
					}catch(Exception $e){
						JError::raiseWarning(100, 'Unable to update a project (' . __LINE__ . ').');
						break;
					}
					
					// delete its grant program associations
					$query = $db->getQuery(true);
					$query->delete('#__tapd_project_programs');
					$query->where('project = ' . $update_project['id']);
					$db->setQuery($query);
					try{
						$db->query();
					}catch(Exception $e){
						JError::raiseWarning(100, 'Unable to update the grant programs for a project, previous selections were retained (' . __LINE__ . ').');
						break;
					}
					
					// store grant programs
					$this->storeGrantPrograms($update_project['id'], $update_project['grantPrograms']);
					
					// store contacts
					$this->storeContacts($update_project['id'], $update_project['contacts']);
				}
			}
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Function to store the contacts for a specific project
	 *
	 * @param   int  		$projectID		The ID of the project
	 * @param   array 		$contacts		An array containing contact information for one or more contacts
	 *
	 * @return  null
	 */

	function storeContacts($projectID, $contacts){
		if(!empty($projectID) && !empty($contacts)){
			// variables
			$db = JFactory::getDbo();
			$user = JFactory::getUser();
			$userID = $user->get('id');
			// loop through the contacts, processing each
			foreach($contacts as $contact){
				// check if inserting or updating	
				$first_id_char = substr($contact->id, 0, 1);
				if($first_id_char == 'n'){
					// inserting
					$query = $db->getQuery(true);
					$query->insert('#__tapd_project_contacts');
					$query->columns('ordering, state, created_by, first_name, last_name, title, phone, email, project');
					$query->values("1,'" . $contact->state . "','$userID','" . $contact->first_name . "','" . $contact->last_name . "','" . $contact->title . "','" . $contact->phone . "','" . $contact->email . "','$projectID'");
					$db->setQuery($query);
					try{
						$db->query();
					}catch(Exception $e){
						JError::raiseWarning(100, 'Unable to save a new contact (' . __LINE__ . ').');
					}
				}else{
					// updating
					$query = $db->getQuery(true);
					$query->update('#__tapd_project_contacts');
					$query->set("ordering = 1, state = '" . $contact->state . "', first_name = '" . $contact->first_name . "', last_name = '" . $contact->last_name . "', title = '" . $contact->title . "', phone = '" . $contact->phone . "', email = '" . $contact->email . "'");
					$query->where('id = ' . $contact->id);
					$db->setQuery($query);
					try{
						$db->query();
					}catch(Exception $e){
						JError::raiseWarning(100, 'Unable to update a contact (' . __LINE__ . ').');
						return;
					}
				}				
			}
		}
	}

	/**
	 * Function to store the grant programs for a specific project
	 *
	 * @param   int  		$projectID		The ID of the project
	 * @param   array 		$grantPrograms	An array of grant programs (ints)
	 *
	 * @return  null
	 */

	function storeGrantPrograms($projectID, $grantPrograms){
		if(!empty($projectID) && !empty($grantPrograms)){	
			$db = JFactory::getDbo();	
			$query = $db->getQuery(true);
			$query->insert('#__tapd_project_programs');
			$query->columns('project, program');
			foreach($grantPrograms as $gp){
				$query->values("'$projectID','$gp'");
			}
			$db->setQuery($query);
			try{
				$db->query();
			}catch(Exception $e){
				JError::raiseWarning(100, 'Unable to save grant programs for a new project (' . __LINE__ . ').');
			}
		}
	}
}
?>