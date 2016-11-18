<?php
/**
 * @package     com_services
 * @copyright   Copyright (C) 2016 NCJFCJ. All rights reserved.
 * @author      Zadra Design - http://zadradesign.com
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');
jimport('joomla.filesystem.file');

/**
 * Services model.
 */
class ServicesModelroundtable extends JModelAdmin{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_SERVICES';


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Roundtable', $prefix = 'ServicesTable', $config = array()){
		return JTable::getInstance($type, $prefix, $config);
	}

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
		$form = $this->loadForm('com_services.roundtable', 'roundtable', array('control' => 'jform', 'load_data' => $loadData));
        
        
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData(){
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_services.edit.roundtable.data', array());

		if(empty($data)){
			$data = $this->getItem();
			
			// Support for topic areas
			$data->topic_areas = '';
			if($data->id){
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select($db->quoteName('topic_area'));
				$query->from($db->quoteName('#__services_roundtable_request_topic_areas'));
				$query->where($db->quoteName('request') . ' = ' . $data->id);
				$db->setQuery($query);
				$data->topic_areas = implode(',',$db->loadColumn());
			}     
		}

		return $data;
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
		if($item = parent::getItem($pk)){

			if($item->id){
				// get the registration records
				$db = $this->getDbo();
				$query = $db->getQuery(true);
				$query->select($db->quoteName(array(
					'a.fname',
					'a.lname',
					'a.email',
					'a.zip',
					'a.org',
					'a.address',
					'a.address2',
					'a.phone',
					'a.fax',
					'a.accessibility_interpreter',
					'a.accessibility_interpreter_lang',
					'a.accessibility_simultaneous_interpretation',
					'a.accessibility_braille',
					'a.accessibility_large_print',
					'a.accessibility',
					'a.q1_answer',
					'a.q2_answer',
					'a.q3_answer',
					'a.registered',
					'z.city',
					'z.territory'
				)));
				$query->select($db->quoteName('o.name', 'occupation'));
				$query->from($db->quoteName('#__services_registrations', 'a'));
				$query->join('LEFT', $db->quoteName('#__services_zip_codes', 'z') . ' ON ' . $db->quoteName('z.zip') . ' = ' . $db->quoteName('a.zip'));
				$query->join('LEFT', $db->quoteName('#__services_occupations', 'o') . ' ON ' . $db->quoteName('o.id') . ' = ' . $db->quoteName('a.occupation'));
				$query->where($db->quoteName('a.service') . ' = ' . $db->quote($item->id) . ' AND ' . $db->quoteName('a.service_type') . '=' . $db->quote('roundtable'));
				$query->order($db->quoteName('a.registered') . ' ASC');
				$db->setQuery($query);
				$item->registration_records = $db->loadObjectList();
			}
		}

		return $item;
	}

	/**
	 * TA providers after delete content method
	 * TA Provider object is passed by reference, all files associated with it are deleted
	 * 
	 * @param string $context The content of the content passed to the plugin
	 * @param object $table A JTableContent object
	 */
	public function onContentAfterDelete($context, $table){
		// delete the logo file
		$logo_file = JPATH_SITE . '/media/com_services/logos/' . $table->logo;
		if(file_exists($logo_file)){
			unlink($logo_file);
		}
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since	1.6
	 */
	protected function prepareTable($table){
		jimport('joomla.filter.output');

		if (empty($table->id)) {

			// Set ordering to the last item if not set
			if (@$table->ordering === '') {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__ta_providers');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}

		}
	}
	
	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @since   12.2
	 */
	public function save($data){
		$dispatcher = JEventDispatcher::getInstance();
		$table = $this->getTable();

		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;

		// Include the content plugins for the on save events.
		JPluginHelper::importPlugin('content');

		$curDateTime = gmdate('Y-m-d H:i:s');
		$user = JFactory::getUser();
			
		// Support for modified and created fields
		if($data['id']){
			$data['modified'] = $curDateTime;
			$data['modified_by'] = $user->id;
		}else{
			$data['created'] = $curDateTime;
			$data['created_by'] = $user->id;
		}
			
		$db = $this->getDBO();

		/* --- Process File Upload --- */
		
		// Check whether a file upload should be processed
		$fileUpload = false;
		$old_file_name = '';
		if(is_uploaded_file($_FILES['jform']['tmp_name']['file'])){
			$fileUpload = true;
			// create an empty class to hold information on the file we are uploading
			$file = new stdClass;
			$file->name = '';
			$file->type = '';
			$file->tmp_name = '';
			$file->error = 0;
			$file->size = '';
			
			// get information about the uploaded file
			if(isset($_FILES['jform'])){
				$file->name = $_FILES['jform']['name']['file'];
				$file->type = $_FILES['jform']['type']['file'];
				$file->tmp_name = $_FILES['jform']['tmp_name']['file'];
				$file->error = $_FILES['jform']['error']['file'];
				$file->size = $_FILES['jform']['size']['file'];
			}else{
				$this->setError(JText::_('COM_SERVICES_NO_FILE'));
			    return false;
			}
			
			// check if an error occured
			if($file->error){
				switch($file->error){
					case 1:
						$this->setError(JText::_('COM_SERVICES_FILE_TOO_LARGE'));
	        	return false;
	        case 2:
						$this->setError(JText::_('COM_SERVICES_FILE_TOO_LARGE'));
	        	return false;
	        case 3:
						$this->setError(JText::_('COM_SERVICES_FILE_UPLOAD_FAILED'));
	        	return false;
	        case 4:
						if($data['id'] <= 0){
							$this->setError(JText::_('COM_SERVICES_NO_FILE'));
	        		return false;
						}
				}
			}
			
			//check for filesize
			if($file->size > 26210000){
				$this->setError(JText::_('COM_SERVICES_FILE_TOO_LARGE'));
				return false;
			}
			
			//check the file extension is ok
			$filePathInfo = pathinfo('/'.$file->name);
			if($filePathInfo['extension'] != 'pdf'){
				$this->setError(JText::_('COM_SERVICES_INVALID_FILE_TYPE'));
				return false;
			}

			// generate a unqiue key
			require_once(__DIR__ . '/../helpers/services.php');
			$keyUnique = false;
			while(!$keyUnique){
				$data['file'] = ServicesHelper::getUniqueKey();
				
				$query = $db->getQuery(true);
				$query->select($db->quoteName('id'));
				$query->from($db->quoteName('#__services_roundtable_requests'));
				$query->where($db->quoteName('file') . ' = ' . $db->quote($data['file']));
				$db->setQuery($query);
				$result = $db->query();
				if($db->getNumRows() == 0){
					$keyUnique = true;
				}
			}
			
			//lose any special characters in the filename
			$file->name = strtolower(preg_replace('/[^A-Za-z0-9]/i', '-', $filePathInfo['filename']));
			
			// if the user is changing the file (editing), grab the information for the previous file
			if($data['id'] > 0){
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select($db->quoteName('file'));
				$query->from($db->quoteName('#__services_roundtable_requests'));
				$query->where($db->quoteName('id') . ' = ' . $data['id']);
				$db->setQuery($query);
				$old_file_name = $db->loadResult();
			}
		}
		
		// Allow an exception to be thrown.
		try{
			// Load the row if saving an existing record.
			if($pk > 0){
				$table->load($pk);
				$isNew = false;
			}

			// Bind the data.
			if(!$table->bind($data)){
				$this->setError($table->getError());
				return false;
			}

			// Prepare the row for saving
			$this->prepareTable($table);

			// Check the data.
			if(!$table->check()){
				$this->setError($table->getError());
				return false;
			}

			// Trigger the onContentBeforeSave event.
			$result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, $table, $isNew));
			if(in_array(false, $result, true)){
				$this->setError($table->getError());
				return false;
			}

			// Store the data.
			if(!$table->store()){
				$this->setError($table->getError());
				return false;
			}

			/* -- process topic areas -- */
			
			if(isset($data['topic_areas'])){
				// store the data locally	
				$topic_areas = $data['topic_areas'];
				
				// remove the data from the array
				unset($data['topic_areas']);
				
				// remove any unaccosiated records
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__services_roundtable_request_topic_areas'));
				$query->where(array(
					$db->quoteName('request') . '=' . $db->quote($table->id),
					$db->quoteName('topic_area') . 'NOT IN(' . implode(',', $topic_areas) . ')'
				));
				$db->setQuery($query);
				$db->query();
				
				// now, add in any new records
				$query = 'INSERT INTO ' . $db->quoteName('#__services_roundtable_request_topic_areas');
				$query .= '(' . $db->quoteName('request') . ',' . $db->quoteName('topic_area') . ') VALUES ';
				$values = array();
				foreach($topic_areas as $topic_area){
					$values[] = '(' . $db->quote($table->id) . ',' . $db->quote($topic_area) . ')';
				}
				$query .= implode(',', $values);
				$query .= ' ON DUPLICATE KEY UPDATE ' . $db->quoteName('topic_area') . '=' . $db->quoteName('topic_area') .';';
				$db->setQuery($query);
				$db->query();
			}else{
				// remove any unaccosiated records
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__services_roundtable_request_topic_areas'));
				$query->where($db->quoteName('request') . '=' . $db->quote($table->id));
				$db->setQuery($query);
				$db->query();
			}
						
			/* -- complete the file upload -- */

			if($fileUpload){
				// construct the file path
				$uploadPath = JPATH_SITE . '/media/com_services/materials/roundtable/' . $data['file'] . '.pdf';
							
				//move the uploaded file to its permenant home
				if(!JFile::upload($file->tmp_name, $uploadPath)) {
					$this->setError(JText::_('COM_SERVICES_FILE_MOVE_ERROR'));
					
					// erase this record so we don't end up with a record missing a file
					$query = $db->getQuery(true);
					$query->delete($db->quoteName('#__services_roundtable_requests'));
					$query->where($db->quoteName('id') . '=' . $db->quote($table->id));
					$db->setQuery($query,0,1);
					$db->query();
					
			    return false;
				}
				
				/* -- remove the old file information -- */
				
				if(!empty($old_file_name)){
					// first, make sure the names are not the same
					if($old_file_name != $data['file']){
						// delete the pdf file
						$old_file_path = JPATH_SITE . '/media/com_services/materials/roundtable/' . $old_file_name . '.pdf';
						if(file_exists($old_file_path)){
							unlink($old_file_path);
						}
					}
				}
			}

			// Clean the cache.
			$this->cleanCache();

			// Trigger the onContentAfterSave event.
			$dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, $table, $isNew));
		}catch (Exception $e){
			$this->setError($e->getMessage());

			return false;
		}

		$pkName = $table->getKeyName();

		if(isset($table->$pkName)){
			$this->setState($this->getName() . '.id', $table->$pkName);
		}
		$this->setState($this->getName() . '.new', $isNew);

		return true;
	}
}