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
 * Service Webinar model
 */
class ServicesModelWebinar extends JModelForm{
    
    var $_item = null;
    var $user = null;
    
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
		$form = $this->loadForm('com_services.webinar', 'webinar', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_services.edit.webinar.data', array());

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param	array		The form data.
	 * @return	mixed		The user id on success, false on failure.
	 * @since	1.6
	 */
	public function save($data){
		// require the helper
		require_once JPATH_COMPONENT.'/helpers/services.php';

		// get the user's organization
		$org = ServicesHelper::getUserOrg();

		// grab the database object and begin the query
		$db = $this->getDbo();

		// check if materials were uploaded
		$file = '';
		if(isset($_FILES['jform']['tmp_name'])
			&& file_exists($_FILES['jform']['tmp_name']['materials'])
			&& is_uploaded_file($_FILES['jform']['tmp_name']['materials'])){

			// create an empty class to hold information on the file we are uploading
			$fileObj = new stdClass;
			$fileObj->name = '';
			$fileObj->type = '';
			$fileObj->tmp_name = '';
			$fileObj->error = 0;
			$fileObj->size = '';
			
			// get information about the uploaded file
			if(isset($_FILES['jform'])){
				$fileObj->name = $_FILES['jform']['name']['materials'];
				$fileObj->type = $_FILES['jform']['type']['materials'];
				$fileObj->tmp_name = $_FILES['jform']['tmp_name']['materials'];
				$fileObj->error = $_FILES['jform']['error']['materials'];
				$fileObj->size = $_FILES['jform']['size']['materials'];
			}

			// check if an error occured
			if($fileObj->error){
				switch($fileObj->error){
					case 1:
						$this->setError(JText::_('COM_SERVICES_FILE_TOO_LARGE'));
	        	return false;
					case 2:
						$this->setError(JText::_('COM_SERVICES_FILE_TOO_LARGE'));
	        	return false;
	 				case 3:
						$this->setError(JText::_('COM_SERVICES_FILE_UPLOAD_FAILED'));
	        	return false;
	        default:
	        	break;
				}
			}

			//check for filesize
			if($fileObj->size > 26210000){
				$this->setError(JText::_('COM_SERVICES_FILE_TOO_LARGE'));
				return false;
			}
			
			//check the file extension is ok
			$filePathInfo = pathinfo('/'.$fileObj->name);
			if($filePathInfo['extension'] != 'pdf'){
				$this->setError(JText::_('COM_SERVICES_FILE_INVALID_TYPE'));
				return false;
			}

			// generate a unqiue key
			$keyUnique = false;
			while(!$keyUnique){
				$file = ServicesHelper::getUniqueKey();
				
				$query = $db->getQuery(true);
				$query->select($db->quoteName('id'));
				$query->from($db->quoteName('#__services_webinar_requests'));
				$query->where($db->quoteName('file') . ' = ' . $db->quote($file));
				$db->setQuery($query);
				$result = $db->query();
				if($db->getNumRows() == 0){
					$keyUnique = true;
				}
			}

			//move the uploaded file to its permenant home
			$uploadPath = JPATH_SITE . '/media/com_services/materials/webinars/' . $file . '.pdf';
			if(!JFile::upload($fileObj->tmp_name, $uploadPath)) {
				$this->setError(JText::_('COM_SERVICES_FILE_MOVE_ERROR'));
				return false;
			}
		}

    // construct the query to save this resource
    $columns = array(
			'state',
			'org',
			'project',
			'parent',
			'start',
			'end',
			'series',
			'registration',
			'title',
			'sub_title',
			'num_participants',
			'description',
			'comments',
			'file',
			'created',
			'created_by',
		);

    

    if($data['series'] == 1){
    	// begin the insert query for the first webinar in the series
    	$query = $db->getQuery(true);
	    $query->insert($db->quoteName('#__services_webinar_requests'));
	    $query->columns($db->quoteName($columns));
			$query->values(implode(',',array(
	    	$db->quote('-1'),
	    	$db->quote($org->id),
	    	$db->quote((int)$data['project']),
	    	'NULL',
	    	$db->quote('0000-00-00 00:00:00'),
	    	$db->quote('0000-00-00 00:00:00'),
	    	$db->quote('1'),
	    	$db->quote($data['registration']),
	    	$db->quote($data['title']),
	    	$db->quote(''),
	    	$db->quote($data['num_participants']),
	    	$db->quote($data['description']),
	    	$db->quote($data['comments']),
	    	$db->quote($file),
	    	'NOW()',
	    	$db->quote(ServicesHelper::getUserId())
	    )));
	    $db->setQuery($query);
	    
			// execute the query
			if(!$db->execute()){
				return false;
			}

			// get the ID of the new item
			$request_id = $db->insertid();

    	// begin the insert query for the other webinars in the series
			$query = $db->getQuery(true);
    	$query->insert($db->quoteName('#__services_webinar_requests'));
    	$query->columns($db->quoteName($columns));
		
    	// this is a series
    	foreach($data['webinars'] as $webinar){
	    	$query->values(implode(',',array(
		    	$db->quote('0'),
		    	$db->quote($org->id),
		    	$db->quote((int)$data['project']),
		    	$db->quote($db->insertid()),
		    	$db->quote($webinar->start->format('Y-m-d H:i:s')),
		    	$db->quote($webinar->end->format('Y-m-d H:i:s')),
		    	$db->quote('1'),
		    	$db->quote($data['registration']),
		    	$db->quote($data['title']),
		    	$db->quote($webinar->sub_title),
		    	$db->quote($data['num_participants']),
		    	$db->quote($data['description']),
		    	$db->quote($data['comments']),
		    	$db->quote($file),
		    	'NOW()',
		    	$db->quote(ServicesHelper::getUserId())
		    )));
    	}
	    $db->setQuery($query);
	    
			// execute the query
			if(!$db->execute()){
				return false;
			}
    }else{
    	// begin the insert query for the single webinar
    	$query = $db->getQuery(true);
	    $query->insert($db->quoteName('#__services_webinar_requests'));
	    $query->columns($db->quoteName($columns));
			$query->values(implode(',',array(
	    	$db->quote('-1'),
	    	$db->quote($org->id),
	    	$db->quote((int)$data['project']),
	    	'NULL',
	    	$db->quote($data['webinars'][0]->start->format('Y-m-d H:i:s')),
	    	$db->quote($data['webinars'][0]->end->format('Y-m-d H:i:s')),
	    	$db->quote('0'),
	    	$db->quote($data['registration']),
	    	$db->quote($data['title']),
	    	$db->quote(''),
	    	$db->quote($data['num_participants']),
	    	$db->quote($data['description']),
	    	$db->quote($data['comments']),
	    	$db->quote($file),
	    	'NOW()',
	    	$db->quote(ServicesHelper::getUserId())
	    )));
	    $db->setQuery($query);
	    
			// execute the query
			if(!$db->execute()){
				return false;
			}

			// get the ID of the new item
			$request_id = $db->insertid();
    }

		// prepare the rows to be inserted
		$values = array();
		foreach($data['features'] as $feature){
			$values[] = $db->quote($request_id) . ', ' . $db->quote((int)$feature);
		}

		// store the topic areas
  	$query = $db->getQuery(true);
  	$query->insert($db->quoteName('#__services_webinar_request_features'));
    $query->columns($db->quoteName(array(
			'request',
			'feature'
		)));
    $query->values($values);
  	$db->setQuery($query);

		// execute the query
		if(!$db->execute()){
			return false;
		}

		// get the information for this user
		$user = JFactory::getUser();

		// get the TA project title
		$query = $db->getQuery(true);
		$query->select(array(
			$db->quoteName('title')
		));
		$query->from($db->quoteName('#__tapd_provider_projects'));
		$query->where($db->quoteName('id') . '=' . $db->quote((int)$data['project']));
		$db->setQuery($query);
		$project_title = $db->loadResult();

		// get the list of supported topic areas
		$query = $db->getQuery(true);
		$query->select(array(
			$db->quoteName('id'),
			$db->quoteName('name')
		));
		$query->from($db->quoteName('#__services_webinar_features'));
		$query->where($db->quoteName('state') . '=1');
		$db->setQuery($query);
		$features_list = $db->loadObjectList();

		$features = array();
		foreach($features_list as $feature){
			$features[$feature->id] = $feature->name;
		}

		/* --- Email NCJFCJ --- */

		try{
			// create a mailer object	
			$mailer = JFactory::getMailer();
				
			$mailer->isHTML(true);
			$mailer->Encoding = 'base64';
				
			// set the sender to the site default
			$config = JFactory::getConfig();
			$sender = array( 
	    	$config->get('mailfrom'),
	    	$config->get('fromname')
	    );

			$mailer->setSender($sender);
				
			// set the recipients
			$mailer->addRecipient('info@ta2ta.org');

			// set the message subject
			$mailer->setSubject('[TA2TA] New Webinar Request');

			// prepare the message content
			$message = '<table style="border-bottom:1px solid #DDD;border-top:1px solid #DDD;padding:20px 0 30px 0;width:100%">';
			$message .= '<tbody>';
			$message .= '<tr><td style="width:160px;" valign="top"><b>' . JText::_('COM_SERVICES_WEBINAR_FORM_TITLE_LBL') . ':</b></td><td style="padding-bottom:10px;">' . $data['title'] . '</td></tr>';
			$message .= '<tr><td style="width:160px;" valign="top"><b>' . JText::_('COM_SERVICES_WEBINAR_FORM_DESCRIPTION_LBL') . ':</b></td><td style="padding-bottom:10px;">' . $data['description'] . '</td></tr>';
			$message .= '<tr><td style="width:160px;" valign="top"><b>' . JText::_('COM_SERVICES_WEBINAR_FORM_PROJECT_LBL') . ':</b></td><td style="padding-bottom:10px;">' . $project_title . '</td></tr>';
			$message .= '<tr><td style="width:160px;" valign="top"><b>' . JText::_('COM_SERVICES_WEBINAR_FORM_SERIES_LBL') . ':</b></td><td style="padding-bottom:10px;">' . ($data['series'] == '1' ? 'Series of Webinars' : 'Single Webinar') . '</td></tr>';
			if($data['series'] == 1){
				// this is a series
				$message .= '<tr><td colspan="2" style="padding-bottom: 10px;" valign="top">';
				$message .= '<table style="width: 100%;">';
				$message .= '<thead>';
				$message .= '<tr>';
				$message .= '<th style="width: 125px;">' . JText::_('COM_SERVICES_WEBINAR_FORM_DATE_LBL') . '</th>';
				$message .= '<th style="width: 100px;">' . JText::_('COM_SERVICES_WEBINAR_FORM_START_TIME_LBL') . '</th>';
				$message .= '<th style="width: 100px;">' . JText::_('COM_SERVICES_WEBINAR_FORM_END_TIME_LBL') . '</th>';
				$message .= '<th>' . JText::_('COM_SERVICES_WEBINAR_FORM_SUB_TITLE_LBL') . '</th>';
				$message .= '</tr>';
				$message .= '</thead>';
				$message .= '<tbody>';
				foreach($data['webinars'] as $webinar){
					$message .= '<tr>';
					$message .= '<td>' . $webinar->start->format('m/d/Y') . '</td>';
					$message .= '<td>' . $webinar->start->format('g:ia') . '</td>';
					$message .= '<td>' . $webinar->end->format('g:ia') . '</td>';
					$message .= '<td>' . $webinar->sub_title . '</td>';
					$message .= '</tr>';
				}
				$message .= '</tbody>';
				$message .= '</table>';
				$message .= '</td></tr>';
			}else{
				// this is a single webinars
				$message .= '<tr><td style="width:160px;" valign="top"><b>' . JText::_('COM_SERVICES_WEBINAR_FORM_DATE_LBL') . ':</b></td><td style="padding-bottom:10px;">' . $data['webinars'][0]->start->format('m/d/Y') . '</td></tr>';
				$message .= '<tr><td style="width:160px;" valign="top"><b>' . JText::_('COM_SERVICES_WEBINAR_FORM_START_TIME_LBL') . ':</b></td><td style="padding-bottom:10px;">' . $data['webinars'][0]->start->format('g:ia') . '</td></tr>';
				$message .= '<tr><td style="width:160px;" valign="top"><b>' . JText::_('COM_SERVICES_WEBINAR_FORM_END_TIME_LBL') . ':</b></td><td style="padding-bottom:10px;">' . $data['webinars'][0]->end->format('g:ia') . '</td></tr>';
			}
			$message .= '<tr><td style="width:160px;" valign="top"><b>' . JText::_('COM_SERVICES_WEBINAR_FORM_NUMBER_OF_PARTICIPANTS_LBL') . ':</b></td><td style="padding-bottom:10px;">' . $data['num_participants'] . '</td></tr>';
			$message .= '<tr><td colspan="2" style="padding-bottom: 10px;" valign="top"><b>' . JText::_('COM_SERVICES_WEBINAR_FORM_FEATURES_LBL') . ':</b><br><ul style="margin:0;padding:0;">';
			foreach($data['features'] as $feature){
				$message .= '<li>' . $features[$feature] . '</li>';
			}
			$message .= '</ul></td></tr>';
			$message .= '<tr><td colspan="2" style="padding-bottom: 10px;" valign="top"><b>' . JText::_('COM_SERVICES_WEBINAR_FORM_REGISTRATION_LBL') . '</b><br>' . ($data['registration'] == 1 ? 'Yes' : 'No') . '</td></tr>';
			if(isset($uploadPath)){
				$message .= '<tr><td style="width:160px;" valign="top"><b>' . JText::_('COM_SERVICES_MATERIALS') . ':</b></td><td><a href="' . JURI::base() . 'media/com_services/materials/webinars/' . $file . '.pdf" target="_blank">Download</a></td></tr>';
			}
			if(!empty($data['comments'])){
				$message .= '<tr><td colspan="2" style="padding-bottom: 10px;" valign="top"><b>' . JText::_('COM_SERVICES_WEBINAR_FORM_COMMENTS_LBL') . ':</b><br>' . $data['comments'] . '</td></tr>';
			}
			$message .= '</tbody>';
			$message .= '</table>';

			// prepare the NCJFCJ part of the message
			$NCJFCJMessage = '<p style="margin-bottom: 20px;text-align:left;">The following webinar request was submitted through the TA2TA website by ' . $user->name . ' (<a href="mailto:' . $user->email . '">' . $user->email . '</a>) from ' . $org->name . '.</p>';
			
			$mailer->setBody(ServicesHelper::buildEmail('New Webinar Request', $NCJFCJMessage . $message));

			// send the message, if it errors out, just ignore it as we don't want the user affected
			$mailer->Send();
		}catch(Exception $e){}

		/* --- Email The User --- */

		try{
			// create a mailer object	
			$mailer2 = JFactory::getMailer();
				
			$mailer2->isHTML(true);
			$mailer2->Encoding = 'base64';
			$mailer2->setSender($sender);
				
			// set the recipients
			$mailer2->addRecipient($user->email);

			// set the message subject
			$mailer2->setSubject('Webinar Services Request Received');

			// prepare the user part of the message
			$userMessage = '<p style="margin-bottom: 20px;text-align:left;">Dear ' . $user->name . ',</p>';
			$userMessage .= '<p style="text-align: left;">Thank you for submitting a webinar services request on the TA2TA website. We have received your request and will contact you shortly. For your records, the following is the details of your request:</p>';

			$mailer2->setBody(ServicesHelper::buildEmail('Webinar Services Request Received', $userMessage . $message));

			// send the message, if it errors out, just ignore it as we don't want the user affected
			$mailer2->Send();
		}catch(Exception $e){}

		return true;
	}
}