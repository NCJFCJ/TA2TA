<?php
/**
 * @package     com_services
 * @copyright   Copyright (C) 2016 NCJFCJ. All rights reserved.
 * @author      Zadra Design - http://zadradesign.com
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Service Webinar controller class
 */
class ServicesControllerWebinar extends ServicesController{
	
	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   12.2
	 */
	public function getModel($name = 'Webinar', $prefix = 'ServicesModel', $config = array()){
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	/**
	 * Method to save a user's profile data.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function save(){
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app = JFactory::getApplication();
		$model = $this->getModel('Webinar', 'ServicesModel');

		// Get the user data.
		$data = $app->input->get('jform', array(), 'array');

		// save the return URL
		$return_url = filter_input(INPUT_POST, 'return_url', FILTER_SANITIZE_URL);

		// Validate the posted data.
		$data = $this->validate($data);
		
		// Check for errors.
		if($data === false){
			// grab the webinar data
			$data = JRequest::getVar('jform',array());

			$data['webinars'] = array();
			for($i = 0; $i < count($_POST['date']); $i++){
				// loop through each webinar
				$tmp_date;
				$tmp_webinar = new stdClass();

				// check the date
				if(empty($_POST['date'][$i])){
					continue;
				}else{
					$tmp_date = DateTime::createFromFormat('m-d-Y H:i:s', $_POST['date'][$i] . ' 00:00:00');
				}

				if(!$tmp_date){
					continue;
				}

				// start time
				if(!empty($_POST['start_time'][$i])){
					$time_parts = explode(':', $_POST['start_time'][$i]);
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

					// add on the time
					$start = clone $tmp_date;
					$start->add(new DateInterval('PT' . $hours . 'H' . $minutes . 'M'));

					$tmp_webinar->start = $start;
				}

				// end time
				if(!empty($_POST['end_time'][$i])){
					$time_parts = explode(':', $_POST['end_time'][$i]);
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

					$end = clone $tmp_date;
					$end->add(new DateInterval('PT' . $hours . 'H' . $minutes . 'M'));

					$tmp_webinar->end = $end;
				}

				// get the sub title
				if(empty($_POST['sub_title'][$i])){
					$tmp_webinar->sub_title = '';
				}else{
					$tmp_webinar->sub_title = filter_var($_POST['sub_title'][$i], FILTER_SANITIZE_STRING);
				}
		
				// save this webinar for later
				$data['webinars'][] = $tmp_webinar;
			}

			// Save the data in the session.			
			$app->setUserState('com_services.edit.webinar.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect($return_url, false);
			return false;
		}

		// Attempt to save the data.
		$return	= $model->save($data);

		// Check for errors.
		if($return === false){
			// Save the data in the session.
			$app->setUserState('com_services.edit.webinar.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
			$this->setRedirect($return_url, false);
			return false;
		}

    // Redirect to the list screen.
    $this->setMessage(JText::_('COM_SERVICES_WEBINAR_SAVED_SUCCESSFULLY'));
    $this->setRedirect($return_url);

		// Flush the data from the session.
		$app->setUserState('com_services.edit.webinar.data', null);
	}
	
	/**
	 * Validates the data supplied, returns data on success, false on fail
	 */
	function validate($data){
		$app = JFactory::getApplication();

		//title
		if(empty($data['title'])){
			// title is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_WEBINAR_FORM_TITLE_LBL')), 'warning');
			return false;
		}else{
			$data['title'] = filter_var($data['title'], FILTER_SANITIZE_STRING);
			if(strlen($data['title']) > 150){
				// title too long
				$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_TOO_LONG', JText::_('COM_SERVICES_WEBINAR_FORM_TITLE_LBL')), 'warning');
				return false;
			}
		}

		//description
		if(empty($data['description'])){
			// description is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_WEBINAR_FORM_DESCRIPTION_LBL')), 'warning');
			return false;
		}else{
			$data['description'] = filter_var($data['description'], FILTER_SANITIZE_STRING);
			if(strlen($data['description']) > 500){
				// description too long
				$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_TOO_LONG', JText::_('COM_SERVICES_WEBINAR_FORM_DESCRIPTION_LBL')), 'warning');
				return false;
			}
		}

		// project
		if(empty($data['project'])){
			// project is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_WEBINAR_FORM_PROJECT_LBL')), 'warning');
			return false;
		}else{
			$data['project'] = filter_var($data['project'], FILTER_SANITIZE_NUMBER_INT);
		}

		// series
		$data['series'] = ($data['series'] == 1 ? 1 : 0);

		if(empty($_POST['date'][$data['series']])){
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_WEBINAR_FORM_DATE_LBL')), 'warning');
			return false;
		}

		// webinars
		$pst = new DateTimeZone('America/Los_Angeles');
		$timezone = new DateTimeZone($data['time_zone']);
		$data['webinars'] = array();
		for($i = $data['series']; $i < count($_POST['date']); $i++){
			// loop through each webinar
			$tmp_date;
			$tmp_webinar = new stdClass();

			// check the date
			if(empty($_POST['date'][$i])){
				continue;
			}else{
				if(preg_match("/^(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-[0-9]{4}$/",$_POST['date'][$i])){
					$tmp_date = DateTime::createFromFormat('m-d-Y H:i:s', $_POST['date'][$i] . ' 00:00:00', $timezone);
				}else{
					$app->enqueueMessage(JText::_('COM_SERVICES_WEBINAR_FORM_DATE_BAD_FORMAT'), 'warning');
					return false;
				}
			}

			// figure the start time
			if(empty($_POST['start_time'][$i])){
				if($data['series']){
					$app->enqueueMessage(JText::_('COM_SERVICES_WEBINAR_FORM_MISSING_SERIES_INFO'), 'warning');
				}else{
					$app->enqueueMessage(JText::_('COM_SERVICES_WEBINAR_FORM_MISSING_INFO'), 'warning');
				}
				return false;
			}else{
				$time_parts = explode(':', $_POST['start_time'][$i]);
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

				$start = clone $tmp_date;
				$start->add(new DateInterval('PT' . $hours . 'H' . $minutes . 'M'));
				$start->setTimezone($pst);
				$tmp_webinar->start = $start;
			}

			// figure the end time
			if(empty($_POST['end_time'][$i])){
				if($data['series']){
					$app->enqueueMessage(JText::_('COM_SERVICES_WEBINAR_FORM_MISSING_SERIES_INFO'), 'warning');
				}else{
					$app->enqueueMessage(JText::_('COM_SERVICES_WEBINAR_FORM_MISSING_INFO'), 'warning');
				}
				return false;
			}else{
				$time_parts = explode(':', $_POST['end_time'][$i]);
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

				$end = clone $tmp_date;
				$end->add(new DateInterval('PT' . $hours . 'H' . $minutes . 'M'));
				$end->setTimezone($pst);
				$tmp_webinar->end = $end;
			}

			// get the sub title
			if($data['series']){
				if(empty($_POST['sub_title'][$i])){
					$app->enqueueMessage(JText::_('COM_SERVICES_WEBINAR_FORM_MISSING_SERIES_INFO'), 'warning');
					return false;
				}else{
					$tmp_webinar->sub_title = filter_var($_POST['sub_title'][$i], FILTER_SANITIZE_STRING);
				}
			}else{
				$tmp_webinar->sub_title = '';
			}

			// save this webinar for later
			$data['webinars'][] = $tmp_webinar;
		}
		
		//num_participants
		if(empty($data['num_participants'])){
			// num_participants is required
			$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_REQUIRED', JText::_('COM_SERVICES_WEBINAR_FORM_NUMBER_OF_PARTICIPANTS_LBL')), 'warning');
			return false;
		}else{
			$data['num_participants'] = filter_var($data['num_participants'], FILTER_SANITIZE_NUMBER_INT);
			if(strlen($data['num_participants']) > 6){
				// num_participants too long
				$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_TOO_LONG', JText::_('COM_SERVICES_WEBINAR_FORM_NUMBER_OF_PARTICIPANTS_LBL')), 'warning');
				return false;
			}
		}
		
		//registration
		$data['registration'] = ($data['registration'] == 1 ? 1 : 0);

		// comments
		if(!empty($data['comments'])){
			$data['comments'] = filter_var($data['comments'], FILTER_SANITIZE_STRING);
			if(strlen($data['comments']) > 500){
				// comments too long
				$app->enqueueMessage(JText::sprintf('COM_SERVICES_FORM_FIELD_TOO_LONG', JText::_('COM_SERVICES_ROUNDTABLE_FORM_COMMENTS_LBL')), 'warning');
				return false;
			}
		}

		return $data;
	}
}