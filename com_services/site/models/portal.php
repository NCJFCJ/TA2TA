<?php
/**
 * @package     com_services
 * @copyright   Copyright (C) 2016 NCJFCJ. All rights reserved.
 * @author      Zadra Design - http://zadradesign.com
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

/**
 * Service Portal model
 */
class ServicesModelPortal extends JModelForm{
    
  /**
   * Gets the data pertaining to the given slug 
   */
  public function getData(){
  	// get the alias
  	$alias = JRequest::getVar('alias');

  	if($alias){

	  	// get the webinar information from the database (note: if this is a series, additional records will be needed)
	    $db  = $this->getDbo();
	    $query = $db->getQuery(true);	       
	    $query->select($db->quoteName(array(
	    	'wr.id',
	    	'wr.project',
	    	'wr.start',
	    	'wr.end', 
	    	'wr.series',
	    	'wr.title',
	    	'wr.sub_title',
	    	'wr.description',
	    	'wr.file',
	    	'wr.adobe_link',
	    	'tp.name',
	    	'tp.website',
	    	'tp.logo'
	    )));
	    $query->from($db->quoteName('#__services_webinar_requests', 'wr'));
	    $query->join('LEFT', $db->quoteName('#__ta_providers', 'tp') . ' ON (' . $db->quoteName('tp.id') . ' = ' . $db->quoteName('wr.org') . ')');
	    $query->where($db->quoteName('wr.alias') . '=' . $db->quote($alias) . ' AND ' . $db->quoteName('wr.state') . ' = ' . $db->quote('0') . ' AND ' . $db->quoteName('wr.adobe_link') . ' <> \'\'');
			$db->setQuery($query);
			if($webinar = $db->loadObject()){

				// if this is a series, get the next webinar
				$now = new DateTime('now', new DateTimeZone('America/Los_Angeles'));
		    if($webinar->series){	    
		    	$query = $db->getQuery(true);	       
			    $query->select($db->quoteName(array(
			    	'id',
			    	'start',
			    	'end', 
			    	'sub_title'
			    )));
	    		$query->from($db->quoteName('#__services_webinar_requests'));
	    		$query->where($db->quoteName('parent') . '=' . $db->quote($webinar->id) . ' AND ' . $db->quoteName('end') . ' > ' . $db->quote($now->format('Y-m-d H:i:s')));
					$query->order($db->quoteName('start') . ' ASC');
					$db->setQuery($query, 0, 1);
					$series_webinar = $db->loadObject();

					$webinar->end = $series_webinar->end;
					$webinar->id = $series_webinar->id;
					$webinar->start = $series_webinar->start;
					$webinar->sub_title = $series_webinar->sub_title;
		    }

		    // return the webinar data
		    return $webinar;
			}
		}

	  // no page was found
	  throw new Exception(JText::_('COM_SERVICES_ERROR_MESSAGE_NOT_FOUND'), 404);
  }
	        
	/**
	 * Method to get the profile form.
	 *
	 * The base form is loaded from XML 
   * 
	 * @param	array	$data	An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true){
		// Get the form.
		$form = $this->loadForm('com_services.portal', 'portal', array('control' => 'jform', 'load_data' => $loadData));
		if(empty($form)){
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
		$data = JFactory::getApplication()->getUserState('com_services.edit.webinar.data', array());

		return $data;
	}
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState(){
		$app = JFactory::getApplication('com_services');

		// Load the parameters.
    $params = $app->getParams();
    $params_array = $params->toArray();
		$this->setState('params', $params);
	}

	/**
	 * Method to save the form data.
	 *
	 * @param	array		The form data.
	 * @return	mixed		The user id on success, false on failure.
	 * @since	1.6
	 */
	public function save($data){
		// grab the database object and begin the query
		$db = $this->getDbo();

    // construct the query to save this attendee's data
    $query = $db->getQuery(true);
    $query->insert($db->quoteName('#__services_webinar_attendees'));
    $query->columns($db->quoteName(array(
			'webinar',
			'fname',
			'lname',
			'email',
			'occupation',
			'num_viewers',
			'created'
		)));

	  $query->values(implode(',',array(
	  	$db->quote($data['id']),
	  	$db->quote($data['fname']),
	  	$db->quote($data['lname']),
	  	$db->quote($data['email']),
	  	$db->quote($data['occupation']),
	  	$db->quote($data['num_viewers']),
	  	'NOW()'
	  )));    
    $db->setQuery($query);
    
		// execute the query
		if(!$db->execute()){
			return false;
		}

		return true;
	}
}