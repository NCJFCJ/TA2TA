<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Script file for the com_ta_calendar component
 */
class com_ta_calendarInstallerScript{
	/**
	 * Method to run when updating the component
	 *
	 * @return void
	 */
	function update($parent){
		// $parent is the class calling this method
		switch($parent->get('manifest')->version){
			case '1.2.0':
				// Add all grant programs to all existing events without grant programs
				
				// get the grant programs
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select(array(
					$db->quoteName('id')
				));
				$query->from($db->quoteName('#__grant_programs'));
				$db->setQuery($query);
				$grant_programs =  $db->loadColumn();

				// get the database object
				$query = 'SELECT e.id FROM #__ta_calendar_events as e WHERE e.id NOT IN (SELECT DISTINCT p.event FROM #__ta_calendar_event_programs as p);';
				$db->setQuery($query);
				$past_events = $db->loadColumn();

				// associate past events to all grant programs
				if(!empty($past_events) && !empty($grant_programs)){
					$query = $db->getQuery(true);
					$query->insert($db->quoteName('#__ta_calendar_event_programs'));
					$query->columns($db->quoteName(array('event', 'program')));
					foreach($past_events as $event){
						foreach($grant_programs as $program){
							$query->values($db->quote($event) . ',' . $db->quote($program));
						}
					}
					$db->setQuery($query);
					$db->query();
				}
				break;
			default:
				break;
		}
	}
}