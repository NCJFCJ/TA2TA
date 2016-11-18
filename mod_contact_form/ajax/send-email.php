<?php
// Set flag that this is a parent file
define('_JEXEC', 1);

define('DS', DIRECTORY_SEPARATOR);

define('JPATH_BASE', dirname(__FILE__).DS.'..'.DS.'..'.DS.'..' );

// include Joomla! core
require_once(JPATH_BASE.DS.'includes'.DS.'defines.php');
require_once(JPATH_BASE.DS.'includes'.DS.'framework.php');

// initialize Joomla!
$app = JFactory::getApplication('site');
$app->initialise();

// grab the module helper
jimport('joomla.application.module.helper');
jimport('joomla.html.parameter');

// variables	
$return = array();	
$return['error'] = 'An unspecified error occurred.';

// check if this form was submitted
if($_SERVER['REQUEST_METHOD'] == "POST" && filter_has_var(INPUT_POST, 'inputEmail')){		
	// grab and sanitize all data
	$inputName = filter_input(INPUT_POST, 'inputName', FILTER_SANITIZE_STRING);
	$inputEmail = filter_input(INPUT_POST, 'inputEmail', FILTER_SANITIZE_EMAIL);
	$inputMessage = filter_input(INPUT_POST, 'inputMessage', FILTER_SANITIZE_STRING);
	
	// make sure we have what we need
	if($inputName && $inputEmail && $inputMessage && empty($_POST['firstName'])){
		// get the module parameters for email
		// get the article to display
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('params'));
		$query->from('#__modules');
		$query->where('module = \'mod_contact_form\'');
		$db->setQuery($query, 0, 1);
		$params = $db->loadResult();
		$params = json_decode($params);
		$toEmail = $params->toEmail;
		$ccEmail = $params->ccEmail;
		if($toEmail){
			// create a mailer object	
			$mailer = JFactory::getMailer();
			
			$mailer->isHTML(true);
			$mailer->Encoding = 'base64';
			
			// set the sender to the site default
			$config = JFactory::getConfig();
			$sender = array( 
			    $config->get('mailfrom'),
			    $config->get('fromname'));
			$mailer->setSender($sender);
			
			// set the recipient
			$recipients = explode(',',$toEmail);
			foreach($recipients as $recipient){
				$mailer->addRecipient($recipient);
			}
			
			// set CCs
			if($ccEmail){
				$ccRecipients = explode(',',$ccEmail);
				foreach($ccRecipients as $ccRecipient){
					$mailer->addCC($ccRecipient);
				}
			}
			
			// set the message subject
			$mailer->setSubject('Contact from Website');
			
			// set the message body
			$mailer->setBody("The following message was submitted by <b>$inputName ($inputEmail)</b> through your website:<br><br>$inputMessage<br><br>To respond, <a href=\"mailto:$inputEmail\">email $inputName</a>.");
		
			// send the message
			if($mailer->Send()){
				$return['error'] = false;
			}else{
				$return['error'] = 'Your message could not be sent at this time.';
			}			
		}else{
			$return['error'] = 'A configuration error prevented your message from being sent.';
		}
	}else{
		$return['error'] = 'Invalid input received.';
	}
}
echo json_encode($return);
?>	