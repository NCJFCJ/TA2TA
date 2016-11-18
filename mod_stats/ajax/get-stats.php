<?php
// Set flag that this is a parent file
define('_JEXEC', 1);

define('DS', DIRECTORY_SEPARATOR);

define('JPATH_BASE', dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');

// variables
$gaFilters = array();
$return = array(
	'end' => '',
	'error' => 'An unspecified error occurred.',
	'html' => '',
	'start' => ''
);	

// include Joomla! core
require_once(JPATH_BASE.DS.'includes'.DS.'defines.php');
require_once(JPATH_BASE.DS.'includes'.DS.'framework.php');

// initialize Joomla!
$app = JFactory::getApplication('site');
$app->initialise();

// grab the module helper
jimport('joomla.application.module.helper');
jimport('joomla.html.parameter');
require_once('..'.DS.'helpers'.DS.'stats.php');

// get the user's org
$user_org = StatsHelper::getUserOrgId();

// get the user's library items
$db	= JFactory::getDbo();
$items = array();

// obtain the basic library item information
$query = $db->getQuery(true);
$query->select(array(
	$db->quoteName('l.id'),
	$db->quoteName('l.state'),
	$db->quoteName('l.name'),
	$db->quoteName('l.base_file_name')
));
$query->from($db->quoteName('#__library', 'l'));
$query->where($db->quoteName('l.org') . ' = ' . $db->quote($user_org));
$query->order($db->quoteName('l.name') . ' ASC');
$db->setQuery($query);
try{
	$docs = $db->loadObjectList();
	foreach($docs as $doc){
		$gaFilters[] = 'ga:eventLabel=@' . $doc->base_file_name;
	}
}catch(Exception $e){
	echo json_encode($return);
	die();
}

// obtain the webinars of this user
$subquery = $db->getQuery(true);
$subquery->select('COUNT(' . $db->quoteName('wa.id') . ')');
$subquery->from($db->quoteName('#__services_webinar_attendees', 'wa'));
$subquery->where($db->quoteName('wa.webinar') . ' = ' . $db->quoteName('w.id'));

$query = $db->getQuery(true);
$query->select(array(
	$db->quoteName('w.series'),
	$db->quoteName('w.title'),
	$db->quoteName('w.sub_title'),
	$db->quoteName('p.title', 'project'),
	$db->quoteName('w.start'),
	'(' . $subquery . ') AS ' . $db->quoteName('attendees')
));
$query->from($db->quoteName('#__services_webinar_requests', 'w'));
$query->join('LEFT', $db->quoteName('#__tapd_provider_projects', 'p') . ' ON ' . $db->quoteName('p.id') . '=' . $db->quoteName('w.project'));
$query->where($db->quoteName('w.org') . ' = ' . $db->quote($user_org));
$query->where($db->quoteName('w.end') . ' < NOW()');
$query->order($db->quoteName('w.start') . ' DESC');
$db->setQuery($query);
try{
	$webinars = $db->loadObjectList();
}catch(Exception $e){
	echo json_encode($return);
	die();
}

// get the data from the filters, if any
$end = filter_has_var(INPUT_POST, 'end') ? filter_input(INPUT_POST, 'end', FILTER_SANITIZE_STRING) : false;
$start = filter_has_var(INPUT_POST, 'start') ? filter_input(INPUT_POST, 'start', FILTER_SANITIZE_STRING) : false;

// conver the dates to objects we can work with and establish defaults
if($end){
	$end = DateTime::createFromFormat('m-d-Y', $end);
}else{
	$end = new DateTime();
	$end->sub(new DateInterval('P1D'));
}
if($start){
	$start = DateTime::createFromFormat('m-d-Y', $start);
}else{
	$start = clone $end;
	$start->sub(new DateInterval('P31D'));
}

// return the start and end dates so that the UI can be updated if needed
$return['end'] = $end->format('m/d/Y');
$return['start'] = $start->format('m/d/Y');

if(count($docs)){
	// include the Google API Library
	require_once(__DIR__ . '/../inc/google-api-php-client-1.1.7/src/Google/autoload.php');

	// initialize the Google API
	$service_account_email = 'ta2ta-reporting-statistics-701@abstract-stream-132423.iam.gserviceaccount.com';
	$key_file_location = __DIR__ . '/../inc/ta2ta-google-api-credentials-aa984e8199e2.p12';

	// create and configure a new client object
	$client = new Google_Client();
	$client->setApplicationName('TA2TA Reporting Statistics');
	$analytics = new Google_Service_Analytics($client);

	// authenticate with Google
	$key = file_get_contents($key_file_location);
	$cred = new Google_Auth_AssertionCredentials(
	  $service_account_email,
	  array(Google_Service_Analytics::ANALYTICS_READONLY),
	  $key
	);
	$client->setAssertionCredentials($cred);
	if($client->getAuth()->isAccessTokenExpired()){
	  $client->getAuth()->refreshTokenWithAssertion($cred);
	}

	// get the list of accounts for the authorized user
	$accounts = $analytics->management_accounts->listManagementAccounts();

	// process library items
	if(count($accounts->getItems()) > 0){
	  $items = $accounts->getItems();
		$firstAccountId = $items[0]->getId();

	  // get the list of properties for the authorized user
	  $properties = $analytics->management_webproperties->listManagementWebproperties($firstAccountId);
	  if(count($properties->getItems()) > 0){  	
	    $items = $properties->getItems();
	    $firstPropertyId = $items[0]->getId();

	    // get the list of views (profiles) for the authorized user
	    $profiles = $analytics->management_profiles->listManagementProfiles($firstAccountId, $firstPropertyId);

	   	if(count($profiles->getItems()) > 0){
	      $items = $profiles->getItems();
	      $profileId = $items[0]->getId();
	    	
	    	if(count($items) > 0){
	  			try{
	  				$data = $analytics->data_ga->get(
			        'ga:' . $profileId,
			        $start->format('Y-m-d'),
			        $end->format('Y-m-d'),
			        'ga:totalEvents,ga:uniqueEvents',
			        array(
			        	'dimensions' => 'ga:eventAction,ga:eventCategory,ga:eventLabel',
			        	 //in filters, a comma (,) is OR and a semi-colon (;) is AND logically
				        'filters' => 'ga:eventAction==click-pdf;ga:eventCategory==download;' . implode(',', $gaFilters)
			        )
			      );
			    }catch(Exception $e){
			    	$return['error'] = 'Unable to retrieve stats. Please contact us. (Error code ' . __LINE__ . ')';
						echo json_encode($return);
						die();
			    }

			    // start the library display table
			    $return['html'] .= '<div id="libraryStats"><h3>Library Publications</h3>';
			    $return['html'] .= '<table class="table table-striped"><thead><tr><th>Title</th><th>Total Downloads</th><th>Unique Downloads</th></tr></thead><tbody>';

		      if(isset($data->rows) && count($data->rows)){
		      	$rows = array();
		      	foreach($data->rows as $publication){
		      		// determine the name of this publication
		      		$name = $publication[2];
		      		foreach($docs as $doc){
		      			if(strpos($publication[2], $doc->base_file_name) !== false){
		      				$name = $doc->name;
		      				break;
		      			}
		      		}
		      		$rows[$name] = "<tr><td>{$name}</td><td>{$publication[3]}</td><td>{$publication[4]}</td></tr>";
		      	}

		      	// alphabetize rows
		      	ksort($rows);

		      	// combine the rows
		      	$return['html'] .= implode('', $rows);
		      }else{
		      	// no informationt to display
		      	$return['html'] .= '<tr><td class="text-center" colspan="3"><b>There are no downloads for the date range you specified.</b></td></tr>';
		      }

		      // close the library display table
		      $return['html'] .= '</tbody></table></div>';
			    $return['error'] = false;
			  }else{
			  	$return['error'] = 'Unable to retrieve stats. Please contact us. (Error code ' . __LINE__ . ')';
			  }
	   	}else{
		  	$return['error'] = 'Unable to retrieve stats. Please contact us. (Error code ' . __LINE__ . ')';
		  }
	  }else{
	  	$return['error'] = 'Unable to retrieve stats. Please contact us. (Error code ' . __LINE__ . ')';
	  }
	}
}

// process webinars
if(count($webinars)){
	$return['html'] .= '<div id="webinarStats"><h3>Webinars</h3>';
	$return['html'] .= '<table class="table table-striped"><thead><tr><th>Date &amp; Time</th><th>Title</th><th>Attendees</th></tr></thead><tbody>';
	foreach($webinars as $webinar){
		$return['html'] .= '<tr><td>' . date('M j, Y g:ia', strtotime($webinar->start)) . '</td><td>' . $webinar->title . ($webinar->series ? '<br><i>' . $webinar->sub_title . '</i>': '') . '</td><td>' . $webinar->attendees . '</td></tr>';
	}
	$return['html'] .= '</tbody></table></div>';
}
echo json_encode($return);
?>	