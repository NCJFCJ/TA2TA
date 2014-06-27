<?php
/**
 * @package     com_help_videos
 * @copyright   Copyright (C) 2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Help_videos model
 */
class Help_videosModelcategory extends JModelAdmin{
	/**
	 * @var		string	The prefix to use with controller messages
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_HELP_VIDEOS';


	/**
	 * Returns a reference to the a Table object, always creating it
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name (Optional)
	 * @param	array	Configuration array for model (Optional)
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Category', $prefix = 'Help_videosTable', $config = array()){
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form
	 *
	 * @param	array	$data		An optional array of data for the form to interogate
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true){
		// Initialise variables.
		$app	= JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_help_video.category', 'category', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form
	 *
	 * @return	mixed	The data for the form
	 * @since	1.6
	 */
	protected function loadFormData(){
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_help_video.edit.category.data', array());

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
		if ($item = parent::getItem($pk)) {

			//Do any procesing on fields here if needed

		}

		return $item;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since	1.6
	 */
	protected function prepareTable($table){
		jimport('joomla.filter.output');

		if(empty($table->id)){

		}
	}

	/**
	 * Method to save the form data
	 *
	 * @param   array  $data  The form data
	 *
	 * @return  boolean  True on success, False on error
	 *
	 * @since   12.2
	 */
	public function save($data){
		// set the alias
		if(empty($data['alias'])){
			jimport('joomla.filter.output');
			$data['alias'] = JFilterOutput::stringURLSafe($data['name']);
		}

		$dispatcher = JEventDispatcher::getInstance();
		$table = $this->getTable();

		if((!empty($data['tags']) && $data['tags'][0] != '')){
			$table->newTags = $data['tags'];
		}

		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;

		// Include the content plugins for the on save events.
		JPluginHelper::importPlugin('content');

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
			
			$db = $this->getDBO();
		 
			// Clean the cache.
			$this->cleanCache();

			// Trigger the onContentAfterSave event.
			$dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, $table, $isNew));
		}catch(Exception $e){
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