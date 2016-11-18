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
class ServicesModelwebinar extends JModelAdmin{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_SERVICES';

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
		$form = $this->loadForm('com_services.webinar', 'webinar', array('control' => 'jform', 'load_data' => $loadData));
        
        
		if(empty($form)){
			return false;
		}

		return $form;
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
	public function getTable($type = 'Webinar', $prefix = 'ServicesTable', $config = array()){
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
		$data = JFactory::getApplication()->getUserState('com_services.edit.webinar.data', array());

		if(empty($data)){
			$data = $this->getItem();
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

			$item->portal_records = array();			
			$item->registration_records = array();
			$item->webinars = array();

			if($item->id){

				// get the database object
				$db = $this->getDbo();

				// handle series
				if($item->series){
					// get the other webinars in the series
					$query = $db->getQuery(true);
					$query->select($db->quoteName(array(
						'id',
						'state',
						'start',
						'end',
						'sub_title'
					)));
					$query->from($db->quoteName('#__services_webinar_requests'));
					$query->where($db->quoteName('parent') . ' = ' . $db->quote($item->id));
					$query->where('(' . $db->quoteName('state') . ' = ' . $db->quote('-3') . ' OR ' . $db->quoteName('state') . ' = ' . $db->quote('0') . ')');
					$query->order($db->quoteName('start') . ' ASC');
					$db->setQuery($query);
					foreach($db->loadObjectList() as $webinar){
						$webinar->end = DateTime::createFromFormat('Y-m-d H:i:s', $webinar->end);
						$webinar->start = DateTime::createFromFormat('Y-m-d H:i:s', $webinar->start);

						// format the date, start_time, and end_time
						$webinar->date = $webinar->start->format('Y-m-d H:i:s');
						$webinar->end_time = $webinar->end->format('g:ia');
						$webinar->start_time = $webinar->start->format('g:ia');
						
						$item->webinars[] = $webinar;
					}
				}else{
					// add the details of the current item to the webinars array
					$w = new stdClass();
					$w->end = DateTime::createFromFormat('Y-m-d H:i:s', $item->end);
					$w->start = DateTime::createFromFormat('Y-m-d H:i:s', $item->start);
					$w->sub_title = $item->sub_title;
					$w->state = $item->state;

					// format the date, start_time, and end_time
					$w->id = $item->id;
					$w->date = $w->start->format('Y-m-d H:i:s'); 
					$w->end_time = $w->end->format('g:ia');
					$w->start_time = $w->start->format('g:ia');

					$item->webinars[] = $w;
				}

				// get the features
				$query = $db->getQuery(true);
				$query->select($db->quoteName(array(
					'feature'
				)));
				$query->from($db->quoteName('#__services_webinar_request_features'));
				$query->where($db->quoteName('request') . ' = ' . $db->quote($item->id));
				$db->setQuery($query);
				$item->features = $db->loadColumn();

				// get the portal records
				$query = $db->getQuery(true);
				$query->select(array(
					$db->quoteName('a.fname'),
					$db->quoteName('a.lname'),
					$db->quoteName('a.email'),
					$db->quoteName('o.name', 'occupation'),
					$db->quoteName('a.num_viewers'),
					$db->quoteName('a.created')
				));
				$query->from($db->quoteName('#__services_webinar_attendees', 'a'));
				$query->join('LEFT', $db->quoteName('#__services_occupations', 'o') . ' ON (' . $db->quoteName('a.occupation') . ' = ' . $db->quoteName('o.id') . ')');
				$query->where($db->quoteName('a.webinar') . ' = ' . $db->quote($item->id));
				$query->order($db->quoteName('a.created') . ' ASC');
				$db->setQuery($query);
				$item->portal_records = $db->loadObjectList();
			
				// get the registration records
				$query = $db->getQuery(true);
				$query->select($db->quoteName(array(
					'a.fname',
					'a.lname',
					'a.email',
					'a.zip',
					'a.org',
					'a.position',
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
					'w.start',
					'z.city',
					'z.territory'
				)));
				$query->select($db->quoteName('o.name', 'occupation'));
				$query->from($db->quoteName('#__services_registrations', 'a'));
				$query->join('LEFT', $db->quoteName('#__services_zip_codes', 'z') . ' ON ' . $db->quoteName('z.zip') . ' = ' . $db->quoteName('a.zip'));
				$query->join('LEFT', $db->quoteName('#__services_occupations', 'o') . ' ON ' . $db->quoteName('o.id') . ' = ' . $db->quoteName('a.occupation'));
				$query->join('LEFT', $db->quoteName('#__services_webinar_requests', 'w') . ' ON ' . $db->quoteName('w.id') . ' = ' . $db->quoteName('a.service'));
				if($item->series){
					$items = array();
					foreach($item->webinars as $webinar){
						$items[] = $webinar->id;
					}
					$query->where($db->quoteName('a.service') . ' IN(' . join(',', $items) . ')');					
				}else{
					$query->where($db->quoteName('a.service') . ' = ' . $db->quote($item->id));
				}
				$query->where($db->quoteName('a.service_type') . '=' . $db->quote('webinar'));
				$query->order($db->quoteName('w.start') . ' ASC,' . $db->quoteName('a.registered') . ' ASC');
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

		$db = $this->getDbo();
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
				$query->from($db->quoteName('#__services_webinar_requests'));
				$query->where($db->quoteName('file') . ' = ' . $db->quote($data['file']));
				$db->setQuery($query);
				$result = $db->query();
				if($db->getNumRows() == 0){
					$keyUnique = true;
				}
			}
			
			// if the user is changing the file (editing), grab the information for the previous file
			if($data['id'] > 0){
				$query = $db->getQuery(true);
				$query->select($db->quoteName('file'));
				$query->from($db->quoteName('#__services_webinar_requests'));
				$query->where($db->quoteName('id') . ' = ' . $data['id']);
				$db->setQuery($query);
				$old_file_name = $db->loadResult();
			}
		}

		$master_webinar = $data;
		$webinars = array();

		// check if this is a series
		if($data['series']){
			// populate a list of the child webinars
			for($i = 0; $i < count($data['webinar_id']); $i++){
				// if we are adding and this is empty, just skip it
				if(!$data['webinar_id'][$i] && empty($data['date'][$i])){
					continue;
				}

				// figure out the state
				$state = 0;
				if(empty($data['date'][$i]) || $data['date'][$i] == '11/30/-0001'){
					$state = -4;
				}else{
					$state = ($data['state'][$i] == 0 || $data['state'][$i] == -3 ? $data['state'][$i] : 0);
				}

				$d = array();
				$d['start'] = $this->prepareDatetime($data['date'][$i], $data['start_time'][$i]);
  			$d['end'] = $this->prepareDatetime($data['date'][$i], $data['end_time'][$i]);
    		$d['id'] = $data['webinar_id'][$i];
    		$d['sub_title'] = $data['sub_title'][$i];
    		$d['state'] = $state;
    		$webinars[] = $d;
			}

			// prepare the date and time fields for the master webinar
			$master_webinar['start'] = '0000-00-00 00:00:00';
			$master_webinar['end'] = '0000-00-00 00:00:00';
		}else{
			$master_webinar['start'] = $this->prepareDatetime($master_webinar['date'], $master_webinar['start_time']);
    	$master_webinar['end'] = $this->prepareDatetime($master_webinar['date'], $master_webinar['end_time']);
		}

  	unset($master_webinar['date']);
  	unset($master_webinar['end_time']);
  	unset($master_webinar['start_time']);

    // save the master webinar
    try{
    	// Load the row if saving an existing record
			if($pk > 0){
				$table->load($master_webinar['id']);
				$isNew = false;
			}

			// Bind the data
			if(!$table->bind($master_webinar)){
				$this->setError($table->getError());
				return false;
			}

			// Prepare the row for saving
			$this->prepareTable($table);

			// Check the data
			if(!$table->check()){
				$this->setError($table->getError());
				return false;
			}

			// Trigger the onContentBeforeSave event
			$result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, $table, $isNew));
			if(in_array(false, $result, true)){
				$this->setError($table->getError());
				return false;
			}

			// Store the data
			if(!$table->store()){
				$this->setError($table->getError());
				return false;
			}

			/* -- complete the file upload -- */

			if($fileUpload){
				// construct the file path
				$uploadPath = JPATH_SITE . '/media/com_services/materials/webinars/' . $master_webinar['file'] . '.pdf';
							
				//move the uploaded file to its permenant home
				if(!JFile::upload($file->tmp_name, $uploadPath)) {
					$this->setError(JText::_('COM_SERVICES_FILE_MOVE_ERROR'));
					
					// erase this record so we don't end up with a record missing a file
					$query = $db->getQuery(true);
					$query->delete($db->quoteName('#__services_webinar_requests'));
					$query->where($db->quoteName('id') . '=' . $db->quote($table->id));
					$db->setQuery($query,0,1);
					$db->query();
					
			    return false;
				}
				
				/* -- remove the old file information -- */
				
				if(!empty($old_file_name)){
					// first, make sure the names are not the same
					if($old_file_name != $master_webinar['file']){
						// delete the pdf file
						$old_file_path = JPATH_SITE . '/media/com_services/materials/webinars/' . $old_file_name . '.pdf';
						if(file_exists($old_file_path)){
							unlink($old_file_path);
						}
					}
				}
			}

			/* -- process features -- */

			if(isset($master_webinar['features'])){
				// store the data locally	
				$features = $master_webinar['features'];
				
				// remove the data from the array
				unset($master_webinar['features']);
				
				// remove any unaccosiated records
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__services_webinar_request_features'));
				$query->where(array(
					$db->quoteName('request') . '=' . $db->quote($table->id),
					$db->quoteName('feature') . 'NOT IN(' . implode(',', $features) . ')'
				));
				$db->setQuery($query);
				$db->query();
				
				// now, add in any new records
				$query = 'INSERT INTO ' . $db->quoteName('#__services_webinar_request_features');
				$query .= '(' . $db->quoteName('request') . ',' . $db->quoteName('feature') . ') VALUES ';
				$values = array();
				foreach($features as $feature){
					$values[] = '(' . $db->quote($table->id) . ',' . $db->quote($feature) . ')';
				}
				$query .= implode(',', $values);
				$query .= ' ON DUPLICATE KEY UPDATE ' . $db->quoteName('feature') . '=' . $db->quoteName('feature') .';';
				$db->setQuery($query);
				$db->query();
			}else{
				// remove any unaccosiated records
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__services_webinar_request_features'));
				$query->where($db->quoteName('request') . '=' . $db->quote($table->id));
				$db->setQuery($query);
				$db->query();
			}

			// Clean the cache
			$this->cleanCache();

			// Trigger the onContentAfterSave event
			$dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, $table, $isNew));
    }catch(Exception $e){
			$this->setError($e->getMessage());

			return false;
		}

    // get the parent ID
    $parent_id = $table->id;

    // save each webinar in the series
    if($data['series']){
	    foreach($webinars as &$w){
		    $w['parent'] = $parent_id;
	    	try{
					// Load the row if saving an existing record
					if($pk > 0){
						$table->load($w['id']);
					}

					// Bind the data
					if(!$table->bind($w)){
						$this->setError($table->getError());
						return false;
					}

					// Prepare the row for saving
					$this->prepareTable($table);

					// Check the data
					if(!$table->check()){
						$this->setError($table->getError());
						return false;
					}

					// Store the data
					if(!$table->store()){
						$this->setError($table->getError());
						return false;
					}
				}catch(Exception $e){
					$this->setError($e->getMessage());

					return false;
				}
	    }
	  }

		$this->setState($this->getName() . '.id', $parent_id);
		$this->setState($this->getName() . '.new', $isNew);

		return true;
	}

	/**
	 * Converts user-friendly date and time elements into a single MySQL formatted datetime string
	 *
	 * @param string A user-friendly date string formatted as mm/dd/yyyy
	 * @param string A user-friendly time string formatted as g:ia
	 * @return string A MySQL datetime
	 */
	private function prepareDatetime($date_string, $time){

		if($date_string && $time){
			// create a date object
			if($date = DateTime::createFromFormat('m/d/Y H:i:s', $date_string . ' 00:00:00')){

				// split the time into hours and minutes
				$time_parts = explode(':', $time);
				$hours = $time_parts[0];
				$minutes = substr($time_parts[1], 0, 2);

				// adjust hours for pm
				if($hours == 12){
					if(substr($time_parts[1], 2, 2) == 'pm'){
						$hours = 12;
					}else{
						$hours = 0;
					}
				}else{
					if(substr($time_parts[1], 2, 2) == 'pm'){
						$hours += 12;
					}
				}

				// add the time onto the date object
				$date->add(new DateInterval('PT' . $hours . 'H' . $minutes . 'M'));

				// return a MySQL formatted datetime
				return $date->format('Y-m-d H:i:s');
			}
		}		
		return $date_string;
	}
}