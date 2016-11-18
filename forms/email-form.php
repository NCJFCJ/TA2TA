<?php
// Set flag that this is a parent file
define('_JEXEC', 1);

define('DS', DIRECTORY_SEPARATOR);

define('JPATH_BASE', dirname(__FILE__).DS.'..');

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
if($_SERVER['REQUEST_METHOD'] == "POST" && filter_has_var(INPUT_POST, 'serviceType')){
	// grab and sanitize everything
	$tableData = array();
	$otherData = array();
	foreach($_POST as $key => $value){
		$key = filter_var($key, FILTER_SANITIZE_STRING);
		if(is_array($value)){
			$tmpArray = array();
			foreach($value as $v){
				$tmpArray[] = filter_var($v, FILTER_SANITIZE_STRING);
			}
			$tableData[$key] = $tmpArray;
		}else{
			if(substr($key,0,2) == 'll'){
				$otherData[$key] = filter_var($value, FILTER_SANITIZE_STRING);
			}else{
				$tableData[$key] = filter_var($value, FILTER_SANITIZE_STRING);
			}
		}
	}
		
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
	$mailer->addRecipient('ta2ta@ncjfcj.org');
	
	// set the message subject
	$mailer->setSubject('[TA2TA] - Request For ' . ucfirst($tableData['serviceType']));

	// set the message body
	
	// table data
	$message = 'A user has requested a <b>' . $tableData['serviceType'] . '</b> through the TA2TA website. The following information was provided:<br><br><table cellpadding="5">';
	foreach($tableData as $field => $input){
		$message .= '<tr><td><b>';
		// label
		preg_match_all('/((?:^|[A-Z])[a-z]+)/',$field,$words);
		foreach($words[1] as $word){
			$message .= ucfirst($word) . ' ';
		}
		$message .= '</b></td><td>&nbsp;&nbsp;</td><td>';
		// value
		switch($field){
			case 'serviceType':
				$message .= '<b>' . ucfirst($input) . '</b>';
				break;
			case 'email':
				// create mailto link
				$message .= '<a href="mailto:' . $input . '">' . $input . '</a>';
				break;
			case 'phone':
				// format the phone number
				$input = preg_replace('/[^0-9]/','',$input);
				if($input{0} == '1'){
					$message .= '1-' . substr($input,1,3) . '-' . substr($input,4,3) . '-' . substr($input,7,4);
					if(strlen($input) > 11){
						$message .= ' ext. ' + substr($input,11);
					}
				}else{
					$message .= '(' . substr($input,0,3) . ') ' . substr($input,3,3) . '-' . substr($input,6,4);
					if(strlen($input) > 10){
						$message .= ' ext. ' + substr($input,10);
					}
				}
				break;
			default:
				if(is_array($input)){
					for($i = 0; $i < count($input); $i++){
						$message .= ($i > 0 ? '<br>-' : '-') . $input[$i];
 					}
				}else{
					$message .= $input;
				}
		}
		$message .= "</td></tr>";
	}
	$message .= '</table>';
	
	// other data
	foreach($otherData as $field => $value){
		$message .= '<br><br><b>';
		switch($field){
			case 'llActiveProjectsAndGrants':
				$message .= 'List your organization\'s active TA projects and grant numbers:'; 
				break;
			case 'llOtherProjects':
				$message .= 'Are you a partner on any other TA project that is focused on a similar topic as the proposed roundtable topic?';
				break;
			case 'llBenefit':
				$message .= 'How does the proposed roundtable benefit the domestic violence, dating violence, sexual assault and stalking field?';
				break;
			case 'llAdvanceMission':
				$message .= 'How does the proposed roundtable advance the mission of OVW?';
				break;
			case 'llGoals':
				$message .= 'What are the tentative goals and outcomes for the proposed roundtable?';
				break;
			case 'llResources':
				$message .= 'What resources will your organization provide?';
				break;
			case 'llNCJFCJResources':
				$message .= 'What resources are needed from the NCJFCJ?';
				break;
			case 'llNotes':
				$message .= 'Additional Notes:';
				break;
		}
		$message .= '</b><br>' . $value;
	}
	$mailer->setBody($message);
		
	// send the message
	if($mailer->Send()){
		$return['error'] = false;
	}else{
		$return['error'] = 'Your request could not be processed at this time.';
	}
}		

// encode and send the return data
echo json_encode($return);
?>