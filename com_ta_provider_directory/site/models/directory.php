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

jimport('joomla.application.component.modelitem');

/**
 * Ta_provider_directory model.
 */
class Ta_provider_directoryModeldirectory extends JModelItem{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_TA_PROVIDER_DIRECTORY';	
	 
	/**
	 * Method to obtain all active grant programs
	 *
	 * @return	array	Array of Objects on success, empty array on failure.
	 */
	public function getGrantPrograms(){
		// variables
		$grantPrograms = array();
		
		// get all grant programs
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, name, fund, 0 AS checked');
		$query->from('#__grant_programs');
		$query->where('state = 1');
		$query->order('name ASC');
		$db->setQuery($query);
		try{
			$grantPrograms = $db->loadObjectList();
		}catch(Exception $e){
			JError::raiseWarning(100, 'Unable to retrieve grant programs. Please contact us.');
			return $grantPrograms;
		}
		
		// return the array
		return $grantPrograms;
	}
	 
	/**
	 * Retrieves all providers from the database
	 */ 
	public function getProviders(){
		// variables	
		$contacts = array();
		$db	= $this->getDbo();
		$projectPrograms = array();
		$projects = array();
		$providers = array();
		
		// obtain the basic provider information
		$query = $db->getQuery(true);
		$query->select('id, name, website');
		$query->from('#__ta_providers');
		$query->where('state = 1');
		$query->order('name ASC');
		$db->setQuery($query);
		try{
			$providers = $db->loadObjectList();
		}catch(Exception $e){
			JError::raiseWarning(100, 'Unable to retrieve providers. Please contact us.');
			return $providers;
		}
		
		// get the projects
		$query = $db->getQuery(true);
		$query->select('id, title, summary, provider');
		$query->from('#__tapd_provider_projects');
		$query->where('state = 1');
		$query->order('title ASC');
		$db->setQuery($query);
		try{
			$projects = $db->loadObjectList();
		}catch(Exception $e){
			JError::raiseWarning(100, 'Unable to retrieve projects. Please contact us.');
			return $providers;
		}
		
		// get the project / grant program associations
		$query = $db->getQuery(true);
		$query->select('project, program');
		$query->from('#__tapd_project_programs');
		$db->setQuery($query);
		try{
			$projectPrograms = $db->loadObjectList();
		}catch(Exception $e){
			JError::raiseWarning(100, 'Unable to retrieve project programs. Please contact us.');
			return $providers;
		}
		
		// get contacts
		$query = $db->getQuery(true);
		$query->select('ordering, first_name, last_name, title, phone, email, project');
		$query->from('#__tapd_project_contacts');
		$query->where('state = 1');
		$db->setQuery($query);
		try{
			$contacts = $db->loadObjectList();
		}catch(Exception $e){
			JError::raiseWarning(100, 'Unable to retrieve contacts. Please contact us.');
			return $providers;
		}
		
		// begin combining information, building projects first
		foreach($projects as &$project){
			// grant programs
			$project->grantPrograms = array();
			foreach($projectPrograms as $projectProgram){
				if($projectProgram->project == $project->id){
					$project->grantPrograms[] = $projectProgram->program;
				}
			}
			
			// contacts
			$project->contacts = array();
			foreach($contacts as $contact){
				if($contact->project == $project->id){
					$project->contacts[] = $contact;
				}
			}
			usort($project->contacts, function($a,$b){
				return strcmp($a->ordering, $b->ordering);
			});
		}
		
		// next, add projects to their provider
		foreach($providers as &$provider){
			$provider->projects = array();
			foreach($projects as $p){
				if($p->provider == $provider->id){
					$provider->projects[] = $p;
				}
			}
		}
		return $providers;
	}
}
?>