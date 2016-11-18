<?php
/**
 * @package     com_services
 * @copyright   Copyright (C) 2016 NCJFCJ. All rights reserved.
 * @author      Zadra Design - http://zadradesign.com
 *
 * This cron script determines whether there are any webinars scheduled for tomorrow
 * and sends an email to all registrants reminding them about the webinar and giving
 * them the connection details again.
 */

// Set flag that this is a parent file
define('_JEXEC', 1);

define('DS', DIRECTORY_SEPARATOR);

define('JPATH_BASE', dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');

// include Joomla! core
require_once(JPATH_BASE.DS.'includes'.DS.'defines.php');
require_once(JPATH_BASE.DS.'includes'.DS.'framework.php');

// include the admin helper for this component
require_once(JPATH_BASE.DS.'administrator'.DS.'components'.DS.'com_services'.DS.'helpers'.DS.'services.php');

// initialize Joomla!
$app = JFactory::getApplication('site');
$app->initialise();

// determine tomorrow's date
$pst = new DateTimeZone('America/Los_Angeles');
$tomorrow = new DateTime('now', $pst);
$tomorrow->modify('+1 day');

// query the database for upcoming reminders
$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select($db->quoteName(array(
	'wr.id',
	'wr.start',
	'wr.end', 
	'wr.series',
	'wr.title',
	'wr.sub_title',
	'wr.alias',
	'wr.adobe_number',
	'tp.name',
	'tp.website'
)));
$query->from($db->quoteName('#__services_webinar_requests', 'wr'));
$query->join('LEFT', $db->quoteName('#__ta_providers', 'tp') . ' ON (' . $db->quoteName('tp.id') . ' = ' . $db->quoteName('wr.org') . ')');
$query->where($db->quoteName('wr.start') . ' LIKE ' . $db->quote($tomorrow->format('Y-m-d') . '%'));
$db->setQuery($query);
$webinars = $db->loadObjectList();

// loop through and process each webinar
foreach($webinars as $webinar){
	// get the registration records for this webinar
	$query = $db->getQuery(true);
	$query->select($db->quoteName(array(
		'fname',
		'email'
	)));
	$query->from($db->quoteName('#__services_registrations'));
	$query->where($db->quoteName('service') . '=' . $db->quote($webinar->id) . ' AND ' . $db->quoteName('service_type') . '=' . $db->quote('webinar'));
	$db->setQuery($query);
	$registrants = $db->loadAssocList();

	// loop through each registrant and send an email
	foreach($registrants as $registrant){
		// decrpt the email address
		$registrant['email'] = ServicesHelper::decrypt($registrant['email']);

		// send an email to this 
		ServicesHelper::sendWebinarRegConf($webinar, $registrant, true);
	}
}