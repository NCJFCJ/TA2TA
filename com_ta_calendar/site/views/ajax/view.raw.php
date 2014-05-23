<?php
/**
 * @version     1.3.0
 * @package     com_ta_calendar
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Ta_calendar.
 */
class Ta_calendarViewAjax extends JViewLegacy {
	/**
	 * Display the view
	 */
	public function display($tpl = null){
		// Check for errors.
	    if(count($errors = $this->get('Errors'))){
	        throw new Exception(implode("\n", $errors));
	    }
		
		$pre_micro_start = microtime();
		
		// Get the posted data
		$app			= JFactory::getApplication();
		$calDate		= $app->input->get('calDate', '', 'string');
		$calTimezone 	= new DateTimeZone($app->input->get('calTimezone', 'America/New_York', 'string'));
		$calView 		= $app->input->get('calView', 'month', 'string');
		$filters		= $app->input->get('filters', array(), 'array');
		
		// Require the proper view
		switch($calView){
			case 'list' :
				require_once('tmpl/list.php');
				break;
			case 'week' :
				require_once('tmpl/week.php');
				break;
			default :
				require_once('tmpl/month.php');
				break;
		}


		// end
		$pre_micro_end = microtime();

		echo "\n\n<!--- VIEW EXECUTION PROFILE ---\n\n";
		echo 'Total View Execution Time: ' . ($pre_micro_start - $pre_micro_end) . "seconds\n";
		echo "--->\n\n";		
	}
}
