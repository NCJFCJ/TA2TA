<?php
/**
 * @version     1.3.0
 * @package     com_ta_calendar
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Ta_calendar model.
 */
class Ta_calendarModelevent extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_TA_CALENDAR';


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Event', $prefix = 'Ta_calendarTable', $config = array())
	{
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
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_ta_calendar.event', 'event', array('control' => 'jform', 'load_data' => $loadData));
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
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_ta_calendar.edit.event.data', array());
		
		
		if (empty($data)) {
			$data = $this->getItem();
            
			//Support for multiple or not foreign key field: type
			$array = array();
			foreach((array)$data->type as $value): 
				if(!is_array($value)):
					$array[] = $value;
				endif;
			endforeach;
			$data->type = implode(',',$array);
			
			//Support for topic areas
			$data->topic_areas = '';
			if($data->id)
			{
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select($db->quoteName('topic_area'));
				$query->from($db->quoteName('#__ta_calendar_event_topic_areas'));
				$query->where($db->quoteName('event') . ' = ' . $data->id);
				$db->setQuery($query);
				$data->topic_areas = implode(',',$db->loadColumn());
			}
			
			// Support for grant programs
			$data->grant_programs = '';
			if($data->id)
			{
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select($db->quoteName('program'));
				$query->from($db->quoteName('#__ta_calendar_event_programs'));
				$query->where($db->quoteName('event') . ' = ' . $data->id);
				$db->setQuery($query);
				$data->grant_programs = implode(',',$db->loadColumn());
			}

			// Support for target audiences
			$data->target_audiences = '';
			if($data->id)
			{
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select($db->quoteName('target_audience'));
				$query->from($db->quoteName('#__ta_calendar_event_target_audiences'));
				$query->where($db->quoteName('event') . ' = ' . $data->id);
				$db->setQuery($query);
				$data->target_audiences = implode(',',$db->loadColumn());
			}
			
			// Support for approved
			$data->approved = ($data->approved == '0000-00-00 00:00:00' ? 0 : 1);
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
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {
			// make field modifications here
		}

		return $item;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since	1.6
	 */
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');
		
		if (empty($table->id)) {

			// Set ordering to the last item if not set
			if (@$table->ordering === '') {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__ta_calendar_events');
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
	public function save($data)
	{
		$dispatcher = JEventDispatcher::getInstance();
		$table = $this->getTable();

		if ((!empty($data['tags']) && $data['tags'][0] != ''))
		{
			$table->newTags = $data['tags'];
		}

		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;

		// Include the content plugins for the on save events.
		JPluginHelper::importPlugin('content');

		/* --- Process Approved and Modified Dates --- */
		
		$curDateTime = gmdate('Y-m-d H:i:s');
		$user = $user = JFactory::getUser();
			
		// Support for modified fields
		if($data['id']){
			$data['modified'] = $curDateTime;
			$data['modified_by'] = $user->id;
		}else{
			$data['created'] = $curDateTime;
		}
		
		// Support for approved fields
		if($data['approved'] == 1){
			$data['approved'] = $curDateTime;
			$data['approved_by'] = $user->id;
		}else{
			$data['approved'] = 'NULL';
			$data['approved_by'] = 0;
		}

		// Allow an exception to be thrown.
		try
		{
			// Load the row if saving an existing record.
			if ($pk > 0)
			{
				$table->load($pk);
				$isNew = false;
			}

			// Bind the data.
			if (!$table->bind($data))
			{
				$this->setError($table->getError());
				return false;
			}

			// Prepare the row for saving
			$this->prepareTable($table);

			// Check the data.
			if (!$table->check())
			{
				$this->setError($table->getError());
				return false;
			}

			// Trigger the onContentBeforeSave event.
			$result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, $table, $isNew));
			if (in_array(false, $result, true))
			{
				$this->setError($table->getError());
				return false;
			}

			// Store the data.
			if (!$table->store())
			{
				$this->setError($table->getError());
				return false;
			}
			
			$db = $this->getDBO();
		 
			/* -- process topic areas -- */
			
			if(isset($data['topic_areas'])){
				// store the data locally	
				$topic_areas = $data['topic_areas'];
				
				// remove the data from the array
				unset($data['topic_areas']);
				
				// remove any unaccosiated records
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ta_calendar_event_topic_areas'));
				$query->where(array(
					$db->quoteName('event') . '=' . $db->quote($table->id),
					$db->quoteName('topic_area') . 'NOT IN(' . implode(',', $topic_areas) . ')'
				));
				$db->setQuery($query);
				$db->query();
				
				// now, add in any new records
				$query = 'INSERT INTO ' . $db->quoteName('#__ta_calendar_event_topic_areas');
				$query .= '(' . $db->quoteName('event') . ',' . $db->quoteName('topic_area') . ') VALUES ';
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
				$query->delete($db->quoteName('#__ta_calendar_event_topic_areas'));
				$query->where($db->quoteName('event') . '=' . $db->quote($table->id));
				$db->setQuery($query);
				$db->query();
			}

			/* -- process grant programs -- */
			
			if(isset($data['grant_programs'])){
				// store the data locally	
				$grant_programs = $data['grant_programs'];
				
				// remove the data from the array
				unset($data['grant_programs']);
				
				// remove any unaccosiated records
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ta_calendar_event_programs'));
				$query->where(array(
					$db->quoteName('event') . '=' . $db->quote($table->id),
					$db->quoteName('program') . 'NOT IN(' . implode(',', $grant_programs) . ')'
				));
				$db->setQuery($query);
				$db->query();
				
				// now, add in any new records
				$query = 'INSERT INTO ' . $db->quoteName('#__ta_calendar_event_programs');
				$query .= '(' . $db->quoteName('event') . ',' . $db->quoteName('program') . ') VALUES ';
				$values = array();
				foreach($grant_programs as $grant_program){
					$values[] = '(' . $db->quote($table->id) . ',' . $db->quote($grant_program) . ')';
				}
				$query .= implode(',', $values);
				$query .= ' ON DUPLICATE KEY UPDATE ' . $db->quoteName('program') . '=' . $db->quoteName('program') .';';
				$db->setQuery($query);
				$db->query();
			}else{
				// remove any unaccosiated records
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ta_calendar_event_programs'));
				$query->where($db->quoteName('event') . '=' . $db->quote($table->id));
				$db->setQuery($query);
				$db->query();
			}
			
			/* -- process target audiences -- */
			
			if(isset($data['target_audiences'])){
				// store the data locally	
				$target_audiences = $data['target_audiences'];
				
				// remove the data from the array
				unset($data['target_audiences']);
				
				// remove any unaccosiated records
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ta_calendar_event_target_audiences'));
				$query->where(array(
					$db->quoteName('event') . '=' . $db->quote($table->id),
					$db->quoteName('target_audience') . 'NOT IN(' . implode(',', $target_audiences) . ')'
				));
				$db->setQuery($query);
				$db->query();
				
				// now, add in any new records
				$query = 'INSERT INTO ' . $db->quoteName('#__ta_calendar_event_target_audiences');
				$query .= '(' . $db->quoteName('event') . ',' . $db->quoteName('target_audience') . ') VALUES ';
				$values = array();
				foreach($target_audiences as $target_audience){
					$values[] = '(' . $db->quote($table->id) . ',' . $db->quote($target_audience) . ')';
				}
				$query .= implode(',', $values);
				$query .= ' ON DUPLICATE KEY UPDATE ' . $db->quoteName('target_audience') . '=' . $db->quoteName('target_audience') .';';
				$db->setQuery($query);
				$db->query();
			}else{
				// remove any unaccosiated records
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ta_calendar_event_target_audiences'));
				$query->where($db->quoteName('event') . '=' . $db->quote($table->id));
				$db->setQuery($query);
				$db->query();
			}
			

			// Clean the cache.
			$this->cleanCache();

			// Trigger the onContentAfterSave event.
			$dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, $table, $isNew));
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		$pkName = $table->getKeyName();

		if (isset($table->$pkName))
		{
			$this->setState($this->getName() . '.id', $table->$pkName);
		}
		$this->setState($this->getName() . '.new', $isNew);

		return true;
	}
}