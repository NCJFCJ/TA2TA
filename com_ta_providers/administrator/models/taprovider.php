<?php
/**
 * @package     com_ta_providers
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');
jimport('joomla.filesystem.file');

/**
 * Ta_providers model.
 */
class Ta_providersModeltaprovider extends JModelAdmin{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_TA_PROVIDERS';


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Taprovider', $prefix = 'Ta_providersTable', $config = array()){
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
		$form = $this->loadForm('com_ta_providers.taprovider', 'taprovider', array('control' => 'jform', 'load_data' => $loadData));
        
        
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
		$data = JFactory::getApplication()->getUserState('com_ta_providers.edit.taprovider.data', array());

		if (empty($data)) {
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
		if ($item = parent::getItem($pk)) {

			//Do any procesing on fields here if needed

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
		$logo_file = JPATH_SITE . '/media/com_ta_providers/logos/' . $table->logo;
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
		// check that website is valid
		if(!empty($data['website'])){
			$data['website'] = strtolower($data['website']);
			if(!filter_var($data['website'], FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)){
				$this->setError('The website you entered is not valid.');
				return false;
			}
		}
		$dispatcher = JEventDispatcher::getInstance();
		$table = $this->getTable();

		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;

		// Include the content plugins for the on save events.
		JPluginHelper::importPlugin('content');

		$curDateTime = gmdate('Y-m-d H:i:s');
		$user = $user = JFactory::getUser();
			
		// Support for modified and created fields
		if($data['id']){
			$data['modified'] = $curDateTime;
			$data['modified_by'] = $user->id;
		}else{
			$data['created'] = $curDateTime;
		}
		
		// Allow an exception to be thrown.
		try{
			// Load the row if saving an existing record.
			if ($pk > 0){
				$table->load($pk);
				$isNew = false;
			}

			// Bind the data.
			if (!$table->bind($data)){
				$this->setError($table->getError());
				return false;
			}

			// Prepare the row for saving
			$this->prepareTable($table);

			// Check the data.
			if (!$table->check()){
				$this->setError($table->getError());
				return false;
			}

			// Trigger the onContentBeforeSave event.
			$result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, $table, $isNew));
			if (in_array(false, $result, true)){
				$this->setError($table->getError());
				return false;
			}

			// Store the data.
			if (!$table->store()){
				$this->setError($table->getError());
				return false;
			}

			/* -- move the file from the temporary folder, if any -- */

			if(filter_has_var(INPUT_POST, 'logoPath')){
				$logoFile = filter_input_var(INPUT_POST, 'logoPath', FILTER_SANITIZE_STRING);
				$tmpPath = JPATH_SITE . '/media/com_ta_providers/tmp/' . $logoFile;
				if(file_exists($tmpPath)){
					$logoPath = JPATH_SITE . '/media/com_ta_providers/logos/' . $logoFile;
					if(!rename($tmppath, $logoPath)){
						$this->setError(JText::_('COM_TA_PROVIDERS_FILE_MOVE_ERROR'));	
						return false;
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