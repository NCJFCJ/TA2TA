<?php
/**
 * @package     com_services
 * @copyright   Copyright (C) 2016 NCJFCJ. All rights reserved.
 * @author      Zadra Design - http://zadradesign.com
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Services helper.
 */
class ServicesHelper{

	// 64 hex characters
	private static $key = '602f771dc1b6b98945ae0f5693ae27f0bd22f3c44057ba1d1bd2ffa927f8c8b0';

	/**
	 * Configure the Linkbar
	 */
	public static function addSubmenu($vName = ''){
		JHtmlSidebar::addEntry(
			JText::_('COM_SERVICES_TITLE_MEETINGS'),
			'index.php?option=com_services&view=meetings',
			$vName == 'meetings'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_SERVICES_TITLE_ROUNDTABLES'),
			'index.php?option=com_services&view=roundtables',
			$vName == 'roundtables'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_SERVICES_TITLE_WEBINARS'),
			'index.php?option=com_services&view=webinars',
			$vName == 'webinars'
		);
	}

	/**
	 * Builds a nicely formatted HTML email
	 */
	public static function buildEmail($heading, $content){
		// get the root URL
		$root = JURI::root();
		$pos = strpos($root, 'media/');
		if($pos){
			$root = substr($root, 0, $pos);	
		}

		// build the message
		$message = '<html><body>';
		$message .= '<div style="width:100%!important;padding:0;margin:0;background-color:#807C7C">';
		$message .= '<table style="font-family:Helvetica;font-size:12px" border="0" cellpadding="0" cellspacing="0" width="100%">';
		$message .= '<tbody>';
		$message .= '<tr>';
		$message .= '<td style="padding:40px 0px" align="center">';
		$message .= '<table style="font-family:Helvetica;font-size:12px" align="center" border="0" cellpadding="0" cellspacing="0" width="640">';
		$message .= '<tbody>';
		$message .= '<tr>';
		$message .= '<td valign="top">';
		$message .= '<table style="background-color:#FFF;font-family:Helvetica;font-size:12px" border="0" cellpadding="0" cellspacing="0" width="650">';
		$message .= '<tbody>';
		$message .= '<tr>';
		$message .= '<td style="padding-bottom:10px" valign="top">';
		$message .= '<table border="0" cellpadding="0" cellspacing="0" width="650">';
		$message .= '<tbody>';
		$message .= '<tr>';
		$message .= '<td align="center"><a href="' . $root . '" target="_blank"><img alt="TA2TA" style="margin: 30px 0;" src="' . $root . 'templates/ta2ta/img/logo.png"></a></td>';
		$message .= '</tr>';
		$message .= '<tr style="background-color:#F19244;color:#FFF;font-size:30px;font-weight:bold;">';
		$message .= '<td align="center" style="padding:15px 0;">' . $heading . '</td>';
		$message .= '</tr>';
		$message .= '<tr>';
		$message .= '<td align="center" style="padding:30px;">' . $content . '</td>';
		$message .= '</tr>';
		$message .= '</tbody>';
		$message .= '</table>';
		$message .= '</td>';
		$message .= '</tr>';
		$message .= '</tbody>';
		$message .= '</table>';
		$message .= '</td>';
		$message .= '</tr>';
		$message .= '</tbody>';
		$message .= '</table>';
		$message .= '</td>';
		$message .= '</tr>';
		$message .= '</tbody>';
		$message .= '</table>';
		$message .= '</div>';
		$message .= '</body></html>';

		// return the message
		return $message;
	}

	/**
	 * Decrypts a value
	 *
	 * @param string The encrypted value
	 * @return mixed The decrypted value on success, false otherwise
	 */
	public static function decrypt($value){
	  // separate the value from its IV
	  $pieces = explode('|', $value.'|');

	  // decode the value
	  $value = base64_decode($pieces[0]);

	  // decode the IV
	  $iv = base64_decode($pieces[1]);

	  // check that the IV is okay
	  if(strlen($iv) !== mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC)){
	    return false;
	  }

	  // pack the key
	  $key = pack('H*', self::$key);

	  // decrypt
	  $value = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $value, MCRYPT_MODE_CBC, $iv));

	  // strip off the mac
	  $mac = substr($value, -64);
	  $value = substr($value, 0, -64);

	  // check the mac
	  $calcmac = hash_hmac('sha256', $value, substr(bin2hex($key), -32));
	  if($calcmac !== $mac){
	    return false;
	  }

	  // unserialize
	  $value = unserialize($value);
	  
	  // return the value
	  return $value;
	}

	/**
	 * Encrypts a value
	 *
	 * @param mixed Any value to be encrypted
	 * @return string The encrypted value
	 */
	public static function encrypt($value){
	  // serialize the value for easy storage
	  $value = serialize($value);

	  // generate the IV
	  $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_RAND);
	 
	  // pack the key
	  $key = pack('H*', self::$key);

	  // generate a keyed hash value
	  $mac = hash_hmac('sha256', $value, substr(bin2hex($key), -32));

	  // encrypt
	  $passcrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $value.$mac, MCRYPT_MODE_CBC, $iv);

	  // encode and attach the IV
	  $value = base64_encode($passcrypt).'|'.base64_encode($iv);

	  // return the value
	  return $value;
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions(){
		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_services';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach($actions as $action){
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}

	/**
	 * Generates a sudeorandom 32 character hexidecimal string
	 */
	public static function getUniqueKey(){
		return bin2hex(openssl_random_pseudo_bytes(16));
	}

	/**
 	 * Sends an email to the specified email account confirming registration
 	 * into a meeting.
 	 *
 	 * @param obj The meeting data object
 	 * @param array The registration data (must contain the email address of the user)
 	 * @return boolean True on success, false otherwise
	 */
	public static function sendMeetingRegConf($meeting, $registrationData){
		// if no email address was supplied, or it is invalid, don't continue
		if(!array_key_exists('email', $registrationData)
			|| !$registrationData['email']
			|| filter_var($registrationData['email'], FILTER_VALIDATE_EMAIL) === false){
			return false;
		}

		// make sure the meeting object is complete and has what we need
		if(!property_exists($meeting, 'alias')
		|| !property_exists($meeting, 'suggested_dates')
		|| !property_exists($meeting, 'description')
		|| !property_exists($meeting, 'name')
		|| !property_exists($meeting, 'website')){
			return false;
		}		

		// settings
		$subject = 'Meeting Registration Confirmation';
	
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
			
		// set the recipient
		$mailer->addRecipient($registrationData['email']);

		// set the message subject
		$mailer->setSubject($subject);

		$message = '<div style="text-align: left;"><p>Hello ' . $registrationData['fname'] . ',</p>';
		$message .= '<p>Thank you for registering for the ' . $meeting->suggested_dates . ' meeting hosted by <a href="' . $meeting->website . '" target="_blank">' . $meeting->name . '</a>. We have received your registration information and will follow up with you.</p>';
		$message .= '<p>Should you have questions regarding this meeting please email me us <a href="mailto:info@ncjfcj.org" target="_blank">info@ncjfcj.org</a>.</p>';
		$message .= '<p>Best,</p>';
		$message .= '<p>TA2TA Team<br>';
		$message .= 'National Council of Juvenile and Family Court Judges<br>';
		$message .= '<a href="mailto:info@ncjfcj.org" target="_blank">info@ncjfcj.org</a></p></div>';
		
		$mailer->setBody(ServicesHelper::buildEmail($subject, $message));

		// send the message, if it errors out, just ignore it as we don't want the user affected
		$mailer->Send();
	}

	/**
 	 * Sends an email to the specified email account confirming registration
 	 * into a roundtable.
 	 *
 	 * @param obj The roundtable data object
 	 * @param array The registration data (must contain the email address of the user)
 	 * @return boolean True on success, false otherwise
	 */
	public static function sendRoundtableRegConf($roundtable, $registrationData){
		// if no email address was supplied, or it is invalid, don't continue
		if(!array_key_exists('email', $registrationData)
			|| !$registrationData['email']
			|| filter_var($registrationData['email'], FILTER_VALIDATE_EMAIL) === false){
			return false;
		}

		// make sure the roundtable object is complete and has what we need
		if(!property_exists($roundtable, 'alias')
		|| !property_exists($roundtable, 'topic')
		|| !property_exists($roundtable, 'suggested_dates')
		|| !property_exists($roundtable, 'description')
		|| !property_exists($roundtable, 'name')
		|| !property_exists($roundtable, 'website')){
			return false;
		}		

		// settings
		$subject = 'Roundtable Registration Confirmation';
	
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
			
		// set the recipient
		$mailer->addRecipient($registrationData['email']);

		// set the message subject
		$mailer->setSubject($subject);

		$message = '<div style="text-align: left;"><p>Hello ' . $registrationData['fname'] . ',</p>';
		$message .= '<p>Thank you for registering for the ' . $roundtable->topic . ' roundtable hosted by <a href="' . $roundtable->website . '" target="_blank">' . $roundtable->name . '</a>. We have received your registration information and will follow up with you.</p>';
		$message .= '<p>Should you have questions regarding this roundtable please email me us <a href="mailto:info@ncjfcj.org" target="_blank">info@ncjfcj.org</a>.</p>';
		$message .= '<p>Best,</p>';
		$message .= '<p>TA2TA Team<br>';
		$message .= 'National Council of Juvenile and Family Court Judges<br>';
		$message .= '<a href="mailto:info@ncjfcj.org" target="_blank">info@ncjfcj.org</a></p></div>';
		
		$mailer->setBody(ServicesHelper::buildEmail($subject, $message));

		// send the message, if it errors out, just ignore it as we don't want the user affected
		$mailer->Send();
	}

	/**
 	 * Sends an email to the specified email account confirming registration
 	 * into the webinar.
 	 *
 	 * @param obj The webinar data object
 	 * @param array The registration data (must contain the email address of the user)
 	 * @param boolean Whether or not this is a reminder email
 	 * @return boolean True on success, false otherwise
	 */
	public static function sendWebinarRegConf($webinar, $registrationData, $reminder = false){

		// if no email address was supplied, or it is invalid, don't continue
		if(!array_key_exists('email', $registrationData)
			|| !$registrationData['email']
			|| filter_var($registrationData['email'], FILTER_VALIDATE_EMAIL) === false){
			return false;
		}

		// make sure the webinar object is complete and has what we need
		if(!property_exists($webinar, 'start')
		|| !property_exists($webinar, 'end')
		|| !property_exists($webinar, 'series')
		|| !property_exists($webinar, 'title')
		|| !property_exists($webinar, 'sub_title')
		|| !property_exists($webinar, 'alias')
		|| !property_exists($webinar, 'adobe_number')
		|| !property_exists($webinar, 'name')
		|| !property_exists($webinar, 'website')){
			return false;
		}

		// make sure the registration data array is complete and has what we need

		// check if this is a series
		$series = ($webinar->series && property_exists($webinar, 'webinars') ? true : false);

		// settings
		$subject = ($reminder ? 'Upcoming Webinar Reminder' : 'Webinar Registration Confirmation');
		$pt = new DateTimeZone('America/Los_Angeles');
		$mt = new DateTimeZone('America/Denver');
		$ct = new DateTimeZone('America/Chicago');
		$et = new DateTimeZone('America/New_York');			

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
			
		// set the recipient
		$mailer->addRecipient($registrationData['email']);

		// set the message subject
		$mailer->setSubject($subject);

		// prepare the message content
		$message = '<h3 style="color:red;">' . $webinar->title . '</h3><br>';
		$message .= '<div style="text-align: left;"><p>Hello ' . $registrationData['fname'] . ',</p>';
		$message .= '<p>Thank you for registering for the ' . $webinar->title . ' ' . ($series ? 'webinar series' : 'webinar') . ', hosted by <a href="' . $webinar->website . '" target="_blank">' . $webinar->name . '</a>.</p>';
		$message .= '<h4>WEBINAR ACCESS INFORMATION:</h4>';
		if($series){
			$message .= '<table style="width: 100%;">';
			$message .= '<thead>';
			$message .= '<tr>';
			$message .= '<td>Webinar</td>';
			$message .= '<td>Date and Time</td>';
			$message .= '<td>Duration</td>';
			$message .= '</tr>';
			$message .= '</thead>';
			$message .= '<tbody>';
			foreach($webinar->webinars as $w){
				$message .= '<tr>';
				$message .= '<td>' . $w->sub_title . '</td>';
				$start_time = new DateTime($w->start, $pt);
				$message .= '<td>' . $start_time->format('m/d/Y g:ia') . ' PT, ';
				$start_time->setTimezone($mt);
				$message .= $start_time->format('g:ia') . ' MT, ';
				$start_time->setTimezone($ct);
				$message .= $start_time->format('g:ia') . ' CT, ';
				$start_time->setTimezone($et);
				$message .= $start_time->format('g:ia') . ' ET';
				$message .= '</td>';
				$end_time = new DateTime($w->end, $pt);
				$interval = $start_time->diff($end_time);
				$message .= '<td>' . ($interval->h ? $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') : $interval->m . ' minutes') . '</td>';
				$message .= '</tr>';
			}
			$message .= '</tbody>';
			$message .= '</table>';
		}else{
			$message .= '<p>Title: ' . $webinar->title . '<br>';
			$start_time = new DateTime($webinar->start, $pt);
			$message .= 'Date: ' . $start_time->format('F j, Y') . '<br>';
			$message .= 'Time: ' . $start_time->format('g:ia') . ' PT, ';
			$start_time->setTimezone($mt);
			$message .= $start_time->format('g:ia') . ' MT, ';
			$start_time->setTimezone($ct);
			$message .= $start_time->format('g:ia') . ' CT, ';
			$start_time->setTimezone($et);
			$message .= $start_time->format('g:ia') . ' ET<br>';
			$end_time = new DateTime($webinar->end, $pt);
			$interval = $start_time->diff($end_time);
			$message .= 'Duration: ' . ($interval->h ? $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') : $interval->m . ' minutes') . '</p>';
		}
		$message .= '<p>To join ' . ($series ? 'each webinar in the series' : 'the webinar') . ', please click or copy/paste the following link. Note that the webinar will be unavailable until 10 minutes before the webinar is scheduled to start.</p>';
		$message .= '<p><strong><a href="http' . (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/webinars/' . $webinar->alias . '.html" target="_blank">http' . (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/webinars/' . $webinar->alias . '.html</a></strong></p>';
		$message .= '<p>The audio for this ' . ($series ? 'webinar series' : 'webinar') . ' will be broadcast over the Internet to your computer. Please ensure your computer speakers are connected, turned on, and the volume is set. If you are having trouble with audio or do not have computer speakers, please join the webinar via phone by calling 1-800-832-0736 and using room number *' . $webinar->adobe_number . '#.</p>';
		$message .= '<p>We look forward to your participation.</p>';
		$message .= '<p>Should you have questions regarding this ' . ($series ? 'webinar series' : 'webinar') . ' please email me us <a href="mailto:info@ncjfcj.org" target="_blank">info@ncjfcj.org</a>.</p>';
		$message .= '<p>Best,</p>';
		$message .= '<p>TA2TA Team<br>';
		$message .= 'National Council of Juvenile and Family Court Judges<br>';
		$message .= '<a href="mailto:info@ncjfcj.org" target="_blank">info@ncjfcj.org</a></p></div>';

		$mailer->setBody(ServicesHelper::buildEmail($subject, $message));

		// send the message, if it errors out, just ignore it as we don't want the user affected
		$mailer->Send();
	}
}