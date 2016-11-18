<?php
/**
 * @version     2.0.0
 * @package     com_ta_provider_directory
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Provider controller class.
 */
class Ta_provider_directoryControllerProvider extends JControllerForm{

    function __construct(){
        $this->view_list = 'providers';
        parent::__construct();
    }

	/**
	 * Function that allows child controller access to model data
	 * after the data has been saved.
	 *
	 * @param   JModelLegacy  $model      The data model object.
	 * @param   array         $validData  The validated data.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function postSaveHook(JModelLegacy $model, $validData = array()){
		// variablses	
		$db = JFactory::getDbo();
		$item = $model->getItem();
		$providerID = $validData['id'];
		$user = JFactory::getUser();
		$userID = $user->get('id');
			
		// grab the id of the saved record
    $provider_id = $item->get('id'); 
		
		/* Process Projects */
		
		// grab the projects associated with this TA Provider from the POST data array	
		$projects = json_decode($_POST['jform']['projects']);

		// process each project individually
		$insert_projects = array();
		$update_projects = array();
		foreach($projects as &$project){
			// validate the data, if there is an error, state as much
			// id
			if(!isset($project->id)
			|| !preg_match('/^n?\d+$/', $project->id)){
				JError::raiseWarning(100, 'Unable to save project, previous entries were retained (' . __LINE__ . '). Please contact Zachary at 41966.');
				break;
			}
			// state
			if(!isset($project->state)
			|| $project->state < -1 
			|| $project->state > 1){
				JError::raiseWarning(100, 'Unable to save project, previous entries were retained (' . __LINE__ . '). Please contact Zachary at 41966.');
				break;
			}
			// title
			$project->title = str_replace(array('&#39;','&#039;','&apos;'), "'", $project->title);
			$project->title = str_replace(array('&#34;','&#034;','&quot;'), '"', $project->title);
			if(empty($project->title) 
			|| strlen($project->title) < 2 
			|| strlen($project->title) > 255 
			|| !preg_match('/^[a-zA-Z0-9():,\-\.\'\"\/\\\ ]*$/', $project->title)){
				JError::raiseWarning(100, 'Unable to save project. A valid Title is required for every project. (' . __LINE__ . ')');
				break;
			}else{
				// sanitize field
				$project->title = htmlentities($project->title, ENT_QUOTES, 'UTF-8', false);
			}
			// summary
			if(empty($project->summary) 
			|| strlen($project->summary) < 10 
			|| strlen($project->summary) > 1500){
				JError::raiseWarning(100, 'Unable to save project. A valid Summary is required for every project. (' . __LINE__ . ')');
				break;
			}else{
				// sanitize field
				$project->summary = htmlentities($project->summary, ENT_QUOTES, 'UTF-8', false);
			}
			// award number
			if(empty($project->award_number) 
			|| strlen($project->award_number) < 10 
			|| strlen($project->award_number) > 15){
				JError::raiseWarning(100, 'Unable to save project. A valid Award Number is required for every project. (' . __LINE__ . ')');
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
						JError::raiseWarning(100, 'Unable to save contact, previous entries were retained (' . __LINE__ . '). Please contact Zachary at 41966.');
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
						JError::raiseWarning(100, 'Unable to save contact, previous entries were retained (' . __LINE__ . '). Please contact Zachary at 41966.');
						break;
					}

					// last_name
					$contact->last_name = str_replace(array('&#39;','&#039;','&apos;'), "'", $contact->last_name);
					if(empty($contact->last_name) 
					|| strlen($contact->last_name) < 2 
					|| strlen($contact->last_name) > 30 
					|| !preg_match('/^[a-zA-Z-\' \\\]*$/', $contact->last_name)){
						JError::raiseWarning(100, 'Unable to save contact, previous entries were retained (' . __LINE__ . '). Please contact Zachary at 41966.');
						break;
					}else{
						// sanitize field
						$contact->last_name = htmlentities($contact->last_name, ENT_QUOTES, 'UTF-8', false);
					}

					// title
					if(isset($contact->title) 
					&& !empty($contact->title)
					&& (strlen($contact->title) < 2
					|| strlen($contact->title) > 255 
					|| !preg_match('/^[a-zA-Z()0-9- \\\]*$/', $contact->title))){
						JError::raiseWarning(100, 'Unable to save contact, previous entries were retained (' . __LINE__ . '). Please contact Zachary at 41966.');
						break;
					}

					// email
					if(isset($contact->email) 
					&& !empty($contact->email)
					&& (strlen($contact->email) < 3
					|| strlen($contact->email) > 150 
					|| !filter_var($contact->email, FILTER_VALIDATE_EMAIL))){
						JError::raiseWarning(100, 'Unable to save contact, previous entries were retained (' . __LINE__ . '). Please contact Zachary at 41966.');
						break;
					}
					// phone
					if(isset($contact->phone) 
					&& !empty($contact->phone)
					&& (strlen($contact->phone) < 10
					|| strlen($contact->phone) > 15 
					|| !preg_match('/^\d+$/', $contact->phone))){
						JError::raiseWarning(100, 'Unable to save contact, previous entries were retained (' . __LINE__ . '). Please contact Zachary at 41966.');
						break;
					}
					// add this for later processing
					$validContacts[] = $contact;
				}
				// overwrite the contacts with the valid ones
				$project->contacts = $validContacts;
			}
			// add this data to the appropriate variable for later use
			$first_id_char = substr($project->id, 0, 1);
			if($first_id_char == 'n'){
				// this is a new program
				$insert_projects[] = array(
					'queryString' => "'" . $project->state . "',NOW(),'" . $userID . "','" . $project->title . "','" . $project->summary . "','" . $providerID . "','" . $project->award_number . "'",
					'grantPrograms' => $project->grantPrograms,
					'contacts' => $project->contacts
				);
			}else{
				// this is not a new program
				if($project->id > 0){
					$update_projects[] = array(
						'id' => $project->id,
						'queryString' => "state = '" . $project->state . "', modified = NOW(), modified_by = '" . $userID . "', title = '" . $project->title . "', summary = '" . $project->summary . "', award_number = '" . $project->award_number . "'",
						'grantPrograms' => $project->grantPrograms,
						'contacts' => $project->contacts
					);
				}
			}
		}

		// create new projects
		foreach($insert_projects as $insert_project){
			// new id variable	
			$newProjectID;
			
			// insert a single project
			$query = $db->getQuery(true);
			$query->insert($db->quoteName('#__tapd_provider_projects'));
			$query->columns('state, created, created_by, title, summary, provider, award_number');
			$query->values($insert_project['queryString']);
			$db->setQuery($query);
			try{
				$db->query();
				$newProjectID = $db->insertid();
				
				// store grant programs
				$this->storeGrantPrograms($newProjectID, $insert_project['grantPrograms']);
				
				// store contacts
				$this->storeContacts($newProjectID, $insert_project['contacts']);
			}catch(Exception $e){
				JError::raiseWarning(100, 'Unable to save project (' . __LINE__ . '). Please contact Zachary at 41966.');
			}
		}
		
		// update existing projects
		foreach($update_projects as $update_project){
			// update a single project
			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__tapd_provider_projects'));
			$query->set($update_project['queryString']);
			$query->where($db->quoteName('id') . '=' . $db->quote($update_project['id']));
			$db->setQuery($query);
			try{
				$db->query();
			}catch(Exception $e){
				JError::raiseWarning(100, 'Unable to update a project (' . __LINE__ . '). Please contact Zachary at 41966.');
				break;
			}
			
			// delete its grant program associations
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__tapd_project_programs'));
			$query->where($db->quoteName('project') . '=' . $db->quote($update_project['id']));
			$db->setQuery($query);
			try{
				$db->query();
			}catch(Exception $e){
				JError::raiseWarning(100, 'Unable to update the grant programs for a project, previous selections were retained (' . __LINE__ . '). Please contact Zachary at 41966.');
				break;
			}
			
			// store grant programs
			$this->storeGrantPrograms($update_project['id'], $update_project['grantPrograms']);
			
			// store contacts
			$this->storeContacts($update_project['id'], $update_project['contacts']);
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
						JError::raiseWarning(100, 'Unable to save a new contact (' . __LINE__ . '). Please contact Zachary at 41966.');
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
						JError::raiseWarning(100, 'Unable to update a contact (' . __LINE__ . '). Please contact Zachary at 41966.');
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
				JError::raiseWarning(100, 'Unable to save grant programs for a new project (' . __LINE__ . '). Please contact Zachary at 41966.');
			}
		}
	}
}