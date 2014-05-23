<?php
/**
 * @version     2.0.0
 * @package     com_ta_provider_directory
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Ta_provider_directory helper.
 */
class Ta_provider_directoryHelper{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($vName = ''){
		JHtmlSidebar::addEntry(
			JText::_('COM_TA_PROVIDER_DIRECTORY_TITLE_PROVIDERS'),
			'index.php?option=com_ta_provider_directory&view=providers',
			$vName == 'providers'
		);
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions(){
		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_ta_provider_directory';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}
