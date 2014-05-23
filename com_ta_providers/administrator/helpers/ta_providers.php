<?php
/**
 * @version     1.0.0
 * @package     com_ta_providers
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ta2ta.org
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Ta_providers helper.
 */
class Ta_providersHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($vName = '')
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_TA_PROVIDERS_TITLE_TAPROVIDERS'),
			'index.php?option=com_ta_providers&view=taproviders',
			$vName == 'taproviders'
		);

	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_ta_providers';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}
