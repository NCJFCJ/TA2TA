<?php
/**
 * @package     com_ta_calendar
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

// Set flag that this is a parent file
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);
define('JPATH_BASE', dirname(__FILE__).DS.'..'.DS.'..');

// include Joomla! core
require_once(JPATH_BASE.DS.'includes'.DS.'defines.php');
require_once(JPATH_BASE.DS.'includes'.DS.'framework.php');

// initialize Joomla!
$app = JFactory::getApplication('site');
$app->initialise();

// get the from email defaults
$config = JFactory::getConfig();
$sender = array( 
	$config->get('config.mailfrom'),
	$config->get('config.fromname'));

// get all events that require a notification
$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select($db->quoteName(array(
	'id',
	'org',
	'start',
	'title',
	'type',
	'7dayalert',
	'30dayalert'
)));
$query->from($db->quoteName('#__ta_calendar_events'));
$query->where($db->quoteName('state') . '=' . $db->quote('1') . ' AND ' . $db->quoteName('approved') . '=' . $db->quote('0000-00-00 00:00:00') . ' AND ((' . $db->quoteName('7dayalert') . '=' . $db->quote('0') . ' AND ' . $db->quoteName('start') . ' BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)) OR (' . $db->quoteName('30dayalert') . '=' . $db->quote('0') . ' AND ' . $db->quoteName('start') . ' BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 DAY)));');
$db->setQuery($query); 
$events = $db->loadObjectList();

// send each notification to only those users that want it
$day7AlertsCompleted = array();
$day30AlertsCompleted = array();
foreach($events as $event){
	// build a subquery that matches only the users in this organization
	$subquery =  $db->getQuery(true);
	$subquery->select($db->quoteName('user_id'));
	$subquery->from($db->quoteName('#__user_profiles'));
	$subquery->where($db->quoteName('profile_value') . '=' . $db->quote('"' . $event->org . '"') . ' AND ' . $db->quoteName('profile_key') . '=' . $db->quote('profile.org'));

	// build a subquery to return users with alerts enabled
	$subquery2 = $db->getQuery(true);
	$subquery2->select($db->quoteName('id'));
	$subquery2->from($db->quoteName('#__ta_calendar_user_settings'));
	$subquery2->where($db->quoteName('alerts') . '=' . $db->quote('1') . ' AND ' . $db->quoteName('id') . ' IN (' . $subquery . ')');

	// get all users who want alerts
	$query = $db->getQuery(true);
	$query->select($db->quoteName(array(
		'name',
		'email'
	)));
	$query->from($db->quoteName('#__users'));
	$query->where($db->quoteName('id') . ' IN(' . $subquery2 . ')');
	$db->setQuery($query);
	$users = $db->loadObjectList();

	// send the email to each user
	foreach($users as $user){
		// create a mailer object	
		$mailer = JFactory::getMailer();
					
		$mailer->isHTML(true);
		$mailer->Encoding = 'base64';
					
		// set the sender to the site default
		$mailer->setSender($sender);
					
		// set the recipient
		$mailer->addRecipient($user->email);
					
		// set the message subject
		$mailer->setSubject('TA2TA Event Requires Approval');
					
		// set the message body
		$message = "Dear $user->name,<br><br>";
		$message .= 'The following event your organization entered on the TA2TA website requires OVW approval:<br><br>';
		$message .= "<b>$event->title</b> ($event->id)<br><br>"; 
		$message .= 'If OVW has already approved this event, please login to the TA2TA website, edit your event, and indicate that it has been approved. If the event will not be approved by OVW, please delete it from the website.<br><br>';
		$message .= 'Thank you for your attention and for helping us to keep the TA2TA Event Calendar up-to-date. Have a great day!';
		$mailer->setBody($message);

		// send the message
		$mailer->Send();
	}

	// determine which alert was sent and save this event ID for further processing
	if($event->{'30dayalert'} == '1'){
		$day7AlertsCompleted[] = $event->id;
	}else{
		$day30AlertsCompleted[] = $event->id;
	}
}

// mark all 7 day alert flags
if(!empty($day7AlertsCompleted)){
	$query = $db->getQuery(true);
	$query->update($db->quoteName('#__ta_calendar_events'));
	$query->set($db->quoteName('7dayalert') . '=' . $db->quote('1'));
	$query->where($db->quoteName('id') . ' IN (' . join(',', $day7AlertsCompleted) . ')');
	$db->setQuery($query);
	$result = $db->query();
}

// mark all 30 day alert flags
if(!empty($day30AlertsCompleted)){
	$query = $db->getQuery(true);
	$query->update($db->quoteName('#__ta_calendar_events'));
	$query->set($db->quoteName('30dayalert') . '=' . $db->quote('1'));
	$query->where($db->quoteName('id') . ' IN (' . join(',', $day30AlertsCompleted) . ')');
	$db->setQuery($query);
	$result = $db->query();
}
?>