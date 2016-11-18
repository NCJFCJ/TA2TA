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
 * Service Meeting model
 */
class ServicesModelMeeting extends JModelForm{
    
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
		$form = $this->loadForm('com_services.meeting', 'meeting', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_services.edit.meeting.data', array());

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
				$query->from($db->quoteName('#__services_meeting_requests'));
				$query->where($db->quoteName('file') . ' = ' . $db->quote($file));
				$db->setQuery($query);
				$result = $db->query();
				if($db->getNumRows() == 0){
					$keyUnique = true;
				}
			}

			//move the uploaded file to its permenant home
			$uploadPath = JPATH_SITE . '/media/com_services/materials/meetings/' . $file . '.pdf';
			if(!JFile::upload($fileObj->tmp_name, $uploadPath)) {
				$this->setError(JText::_('COM_SERVICES_FILE_MOVE_ERROR'));
				return false;
			}
		}

    // construct the query to save this resource
    $query = $db->getQuery(true);
    $query->insert($db->quoteName('#__services_meeting_requests'));
    $query->columns($db->quoteName(array(
			'state',
			'org',
			'project',
			'suggested_dates',
			'comments',
			'file',
			'created',
			'created_by',
		)));
    $query->values(implode(',',array(
    	$db->quote('-1'),
    	$db->quote($org->id),
    	$db->quote((int)$data['project']),
    	$db->quote($data['suggested_dates']),
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

		// prepare the rows to be inserted
		$values = array();
		foreach($data['types_of_support'] as $support_option){
			$values[] = $db->quote($request_id) . ', ' . $db->quote((int)$support_option);
		}

		// store the support types
  	$query = $db->getQuery(true);
  	$query->insert($db->quoteName('#__services_meeting_request_support_options'));
    $query->columns($db->quoteName(array(
			'request',
			'support_option'
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

		// get the list of supported support options
		$query = $db->getQuery(true);
		$query->select(array(
			$db->quoteName('id'),
			$db->quoteName('name')
		));
		$query->from($db->quoteName('#__services_meeting_support_options'));
		$query->where($db->quoteName('state') . '=1');
		$db->setQuery($query);
		$support_option_list = $db->loadObjectList();

		$support_options = array();
		foreach($support_option_list as $support_option){
			$support_options[$support_option->id] = $support_option->name;
		}

		/* --- Email NCJFCJ --- */

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
		$mailer->setSubject('[TA2TA] New Meeting Request');

		// prepare the message content
		$message = '<table style="border-bottom:1px solid #DDD;border-top:1px solid #DDD;padding:20px 0 30px 0;width:100%">';
		$message .= '<tbody>';
		$message .= '<tr><td style="width:160px;" valign="top"><b>' . JText::_('COM_SERVICES_MEETING_FORM_PROJECT_LBL') . '</b></td><td style="padding-bottom:10px;">' . $project_title . '</td></tr>';
		$message .= '<tr><td style="width:160px;" valign="top"><b>' . JText::_('COM_SERVICES_MEETING_FORM_SUGGESTED_DATES_LBL') . ':</b></td><td style="padding-bottom:10px;">' . $data['suggested_dates'] . '</td></tr>';
		$message .= '<tr><td style="width:160px;" valign="top"><b>' . JText::_('COM_SERVICES_SUPPORT_REQUESTED') . ':</b></td><td style="padding-bottom:10px;"><ul style="margin:0;padding:0;">';
		foreach($data['types_of_support'] as $type_of_support){
			$message .= '<li>' . $support_options[$type_of_support] . '</li>';
		}
		$message .= '</ul></td></tr>';
		if(isset($uploadPath)){
			$message .= '<tr><td style="width:160px;" valign="top"><b>' . JText::_('COM_SERVICES_MATERIALS') . ':</b></td><td><a href="' . JURI::base() . 'media/com_services/materials/meetings/' . $file . '.pdf" target="_blank">Download</a></td></tr>';
		}
		if(!empty($data['comments'])){
			$message .= '<tr><td colspan="2" style="padding-bottom: 10px;" valign="top"><b>' . JText::_('COM_SERVICES_MEETING_FORM_COMMENTS_LBL') . ':</b><br>' . $data['comments'] . '</td></tr>';
		}
		$message .= '</tbody>';
		$message .= '</table>';

		// prepare the NCJFCJ part of the message
		$NCJFCJMessage = '<p style="margin-bottom: 20px;text-align:left;">The following meeting request was submitted through the TA2TA website by ' . $user->name . ' (<a href="mailto:' . $user->email . '">' . $user->email . '</a>) from ' . $org->name . '.</p>';
		
		$mailer->setBody(ServicesHelper::buildEmail('New Meeting Request', $NCJFCJMessage . $message));

		// send the message, if it errors out, just ignore it as we don't want the user affected
		$mailer->Send();

		/* --- Email The User --- */

		// create a mailer object	
		$mailer2 = JFactory::getMailer();
			
		$mailer2->isHTML(true);
		$mailer2->Encoding = 'base64';
		$mailer2->setSender($sender);
			
		// set the recipients
		$mailer2->addRecipient($user->email);

		// set the message subject
		$mailer2->setSubject('Meeting Services Request Received');

		// prepare the user part of the message
		$userMessage = '<p style="margin-bottom: 20px;text-align:left;">Dear ' . $user->name . ',</p>';
		$userMessage .= '<p style="text-align: left;">Thank you for submitting a meeting services request on the TA2TA website. We have received your request and will contact you shortly. For your records, the following is the details of your request:</p>';

		$mailer2->setBody(ServicesHelper::buildEmail('Meeting Services Request Received', $userMessage . $message));

		// send the message, if it errors out, just ignore it as we don't want the user affected
		$mailer2->Send();

		return true;
	}
}