<?php
/**
 * @package     com_event_manager
 * @copyright   Copyright (C) 2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Events list controller class.
 */
class Event_managerControllerMeeting extends Event_managerController{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Meeting', $prefix = 'Event_managerModel'){
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}