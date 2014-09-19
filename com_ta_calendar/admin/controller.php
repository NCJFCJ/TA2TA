<?php
/**
 * @package     com_ta_calendar
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;

class Ta_calendarController extends JControllerLegacy{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			$cachable	If true, the view output will be cached
	 * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false){
		require_once JPATH_COMPONENT.'/helpers/ta_calendar.php';

		$view = JFactory::getApplication()->input->getCmd('view', 'events');
    JFactory::getApplication()->input->set('view', $view);

		parent::display($cachable, $urlparams);

		return $this;
	}

	public function getPrograms(){
		require_once('views/ajax/getPrograms.php');
	}

	public function getProjects(){
		require_once('views/ajax/getProjects.php');
	}
}