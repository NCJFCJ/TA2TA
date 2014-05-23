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

jimport('joomla.application.component.modeladmin');

/**
 * Ta_provider_directory model.
 */
class Ta_provider_directoryModelprovider extends JModelAdmin{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_TA_PROVIDER_DIRECTORY';	
	 
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true){
		// Initialise variables.
		$app	= JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_ta_provider_directory.provider', 'provider', array('control' => 'jform', 'load_data' => $loadData));
		if(empty($form)){
			return false;
		}

		return $form;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	1.6
	 */
	public function getItem($pk = null){
		// continue here:: we need to get the item but from a different component table ta_providers
		if($item = parent::getItem($pk)){
			// get the grant programs
			$item->projects = $this->getProjects($item);
		}          
		return $item;
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
			JError::raiseWarning(100, 'Unable to retrieve grant programs. Please contact Zachary at 41966.');
			return $grantPrograms;
		}
		
		// return the array
		return $grantPrograms;
	}
	
	/**
	 * Method to obtain all projects for this provider
	 *
	 * @param	object The current Joomla! item
	 *
	 * @return	mixed	Array of Objects on success, empty array on failure.
	 */
	public function getProjects($item){
		// variables	
		$projects = array();
		
		// If we are editing, grab the projects, otherwise do nothing
		if(!empty($item) 
		&& isset($item->id)){
			// get all grant programs
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('p.id, p.state, u.name as created_by, p.title, p.summary');
			$query->from('#__tapd_provider_projects as p');
			$query->join('LEFT', '#__users as u ON (p.created_by = u.id)');
			$query->where('provider = ' . $item->id . ' AND state >= 0');
			$query->order('title ASC');
			$db->setQuery($query);
			try{
				$projects = $db->loadObjectList();
			}catch(Exception $e){
				JError::raiseWarning(100, 'Unable to retrieve projects.');
				return $projects;
			}
			
			// now that we have the projects, we need their associated data
			foreach($projects as &$project){
				
				// grant programs
				$query = $db->getQuery(true);
				$query->select('program');
				$query->from('#__tapd_project_programs');
				$query->where('project = ' . $project->id);
				$db->setQuery($query);
				try{
					$project->grantPrograms = $db->loadColumn();
				}catch(Exception $e){
					JError::raiseWarning(100, 'Unable to retrieve project grant programs.');
				}
				
				// contacts
				$query = $db->getQuery(true);
				$query->select('id, ordering, state, first_name, last_name, title, email, phone');
				$query->from('#__tapd_project_contacts');
				$query->where('project = ' . $project->id . ' AND state >= 0');
				$query->order('ordering ASC');
				$db->setQuery($query);
				try{
					$project->contacts = $db->loadObjectList();
				}catch(Exception $e){
					JError::raiseWarning(100, 'Unable to retrieve project contacts.');
				}
			}
		}
	
		// return the array
		return $projects;
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Provider', $prefix = 'Ta_provider_directoryTable', $config = array()){
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData(){
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_ta_provider_directory.edit.provider.data', array());

		if(empty($data)){
			$data = $this->getItem();
            
		}

		return $data;
	}
	
	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since	1.6
	 */
	protected function prepareTable($table){
		jimport('joomla.filter.output');

		if(empty($table->id)){
			// Set ordering to the last item if not set
			if (@$table->ordering === '') {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__tapd_providers');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}
		}
	}
}