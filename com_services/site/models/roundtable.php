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
 * Service Roundtable model
 */
class ServicesModelRoundtable extends JModelForm{
    
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
				$query->from($db->quoteName('#__services_roundtable_requests'));
				$query->where($db->quoteName('file') . ' = ' . $db->quote($file));
				$db->setQuery($query);
				$result = $db->query();
				if($db->getNumRows() == 0){
					$keyUnique = true;
				}
			}

			//move the uploaded file to its permenant home
			$uploadPath = JPATH_SITE . '/media/com_services/materials/roundtables/' . $file . '.pdf';
			if(!JFile::upload($fileObj->tmp_name, $uploadPath)) {
				$this->setError(JText::_('COM_SERVICES_FILE_MOVE_ERROR'));
				return false;
			}
		}

    // construct the query to save this resource
    $query = $db->getQuery(true);
    $query->insert($db->quoteName('#__services_roundtable_requests'));
    $query->columns($db->quoteName(array(
			'state',
			'org',
			'project',
			'topic',
			'suggested_dates',
			'benefit',
			'comments',
			'description',
			'goals',
			'how_advance',
			'is_partner',
			'num_participants',
			'proposed_locations',
			'resources_needed',
			'resources_provided',
			'file',
			'created',
			'created_by',
		)));
    $query->values(implode(',',array(
    	$db->quote('-1'),
    	$db->quote($org->id),
    	$db->quote((int)$data['project']),
    	$db->quote($data['topic']),
    	$db->quote($data['suggested_dates']),
			$db->quote($data['benefit']),
			$db->quote($data['comments']),
			$db->quote($data['description']),
			$db->quote($data['goals']),
			$db->quote($data['how_advance']),
			$db->quote($data['is_partner']),
			$db->quote($data['num_participants']),
			$db->quote($data['proposed_locations']),
			$db->quote($data['resources_needed']),
			$db->quote($data['resources_provided']),
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
		foreach($data['topic_areas'] as $topic_area){
			$values[] = $db->quote($request_id) . ', ' . $db->quote((int)$topic_area);
		}

		// store the topic areas
  	$query = $db->getQuery(true);
  	$query->insert($db->quoteName('#__services_roundtable_request_topic_areas'));
    $query->columns($db->quoteName(array(
			'request',
			'topic_area'
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
		$query->from($db->quoteName('#__ta_calendar_topic_areas'));
		$query->where($db->quoteName('state') . '=1');
		$db->setQuery($query);
		$topic_areas_list = $db->loadObjectList();

		$topic_areas = array();
		foreach($topic_areas_list as $topic_area){
			$topic_areas[$topic_area->id] = $topic_area->name;
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
		$mailer->setSubject('[TA2TA] New Roundtable Request');

		// prepare the message content
		$message = '<table style="border-bottom:1px solid #DDD;border-top:1px solid #DDD;padding:20px 0 30px 0;width:100%">';
		$message .= '<tbody>';
		$message .= '<tr><td style="width:160px;" valign="top"><b>' . JText::_('COM_SERVICES_ROUNDTABLE_FORM_PROJECT_LBL') . ':</b></td><td style="padding-bottom:10px;">' . $project_title . '</td></tr>';
		$message .= '<tr><td style="width:160px;" valign="top"><b>' . JText::_('COM_SERVICES_ROUNDTABLE_FORM_TOPIC_LBL') . ':</b></td><td style="padding-bottom:10px;">' . $data['topic'] . '</td></tr>';
		$message .= '<tr><td style="width:160px;" valign="top"><b>' . JText::_('COM_SERVICES_ROUNDTABLE_FORM_SUGGESTED_DATES_LBL') . ':</b></td><td style="padding-bottom:10px;">' . $data['suggested_dates'] . '</td></tr>';
		$message .= '<tr><td style="width:160px;" valign="top"><b>' . JText::_('COM_SERVICES_ROUNDTABLE_FORM_PROPOSED_LOCATIONS_LBL') . ':</b></td><td style="padding-bottom:10px;">' . $data['proposed_locations'] . '</td></tr>';
		$message .= '<tr><td style="width:160px;" valign="top"><b>' . JText::_('COM_SERVICES_ROUNDTABLE_FORM_DESCRIPTION_LBL') . ':</b></td><td style="padding-bottom:10px;">' . $data['description'] . '</td></tr>';
		$message .= '<tr><td style="width:160px;" valign="top"><b>' . JText::_('COM_SERVICES_ROUNDTABLE_FORM_NUMBER_OF_PARTICIPANTS_LBL') . ':</b></td><td style="padding-bottom:10px;">' . $data['num_participants'] . '</td></tr>';
		$message .= '<tr><td style="width:160px;" valign="top"><b>' . JText::_('COM_SERVICES_ROUNDTABLE_FORM_TOPIC_AREAS_LBL') . ':</b></td><td style="padding-bottom:10px;"><ul style="margin:0;padding:0;">';
		foreach($data['topic_areas'] as $topic_area){
			$message .= '<li>' . $topic_areas[$topic_area] . '</li>';
		}
		$message .= '</ul></td></tr>';
		$message .= '<tr><td colspan="2" style="padding-bottom: 10px;" valign="top"><b>' . JText::_('COM_SERVICES_ROUNDTABLE_FORM_IS_PARTNER_LBL') . '</b><br>' . $data['is_partner'] . '</td></tr>';
		$message .= '<tr><td colspan="2" style="padding-bottom: 10px;" valign="top"><b>' . JText::_('COM_SERVICES_ROUNDTABLE_FORM_BENEFIT_LBL') . '</b><br>' . $data['benefit'] . '</td></tr>';
		$message .= '<tr><td colspan="2" style="padding-bottom: 10px;" valign="top"><b>' . JText::_('COM_SERVICES_ROUNDTABLE_FORM_HOW_ADVANCE_LBL') . '</b><br>' . $data['how_advance'] . '</td></tr>';
		$message .= '<tr><td colspan="2" style="padding-bottom: 10px;" valign="top"><b>' . JText::_('COM_SERVICES_ROUNDTABLE_FORM_GOALS_LBL') . '</b><br>' . $data['goals'] . '</td></tr>';
		$message .= '<tr><td colspan="2" style="padding-bottom: 10px;" valign="top"><b>' . JText::_('COM_SERVICES_ROUNDTABLE_FORM_RESOURCES_NEEDED_LBL') . '</b><br>' . $data['resources_needed'] . '</td></tr>';
		$message .= '<tr><td colspan="2" style="padding-bottom: 10px;" valign="top"><b>' . JText::_('COM_SERVICES_ROUNDTABLE_FORM_RESOURCES_PROVIDED_LBL') . '</b><br>' . $data['resources_provided'] . '</td></tr>';
		if(isset($uploadPath)){
			$message .= '<tr><td style="width:160px;" valign="top"><b>' . JText::_('COM_SERVICES_MATERIALS') . ':</b></td><td><a href="' . JURI::base() . 'media/com_services/materials/roundtables/' . $file . '.pdf" target="_blank">Download</a></td></tr>';
		}
		if(!empty($data['comments'])){
			$message .= '<tr><td colspan="2" style="padding-bottom: 10px;" valign="top"><b>' . JText::_('COM_SERVICES_ROUNDTABLE_FORM_COMMENTS_LBL') . ':</b><br>' . $data['comments'] . '</td></tr>';
		}
		$message .= '</tbody>';
		$message .= '</table>';

		// prepare the NCJFCJ part of the message
		$NCJFCJMessage = '<p style="margin-bottom: 20px;text-align:left;">The following roundtable request was submitted through the TA2TA website by ' . $user->name . ' (<a href="mailto:' . $user->email . '">' . $user->email . '</a>) from ' . $org->name . '.</p>';
		
		$mailer->setBody(ServicesHelper::buildEmail('New Roundtable Request', $NCJFCJMessage . $message));

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
		$mailer2->setSubject('Roundtable Services Request Received');

		// prepare the user part of the message
		$userMessage = '<p style="margin-bottom: 20px;text-align:left;">Dear ' . $user->name . ',</p>';
		$userMessage .= '<p style="text-align: left;">Thank you for submitting a roundtable services request on the TA2TA website. We have received your request and will contact you shortly. For your records, the following is the details of your request:</p>';

		$mailer2->setBody(ServicesHelper::buildEmail('Roundtable Services Request Received', $userMessage . $message));

		// send the message, if it errors out, just ignore it as we don't want the user affected
		$mailer2->Send();

		return true;
	}
}