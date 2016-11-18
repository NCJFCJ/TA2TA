<?php
/**
 * @package     com_services
 * @copyright   Copyright (C) 2016 NCJFCJ. All rights reserved.
 * @author      Zadra Design - http://zadradesign.com
 */

// No direct access
defined('_JEXEC') or die;

class ServicesController extends JControllerLegacy{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean		$cachable	If true, the view output will be cached
	 * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false){
		require_once JPATH_COMPONENT.'/helpers/services.php';

		$view	= JFactory::getApplication()->input->getCmd('view', 'meetings');
    JFactory::getApplication()->input->set('view', $view);

		parent::display($cachable, $urlparams);

		return $this;
	}

	function fileUpload(){
		require_once('views/ajax/fileUpload.php');
	}
}