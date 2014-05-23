<?php
/**
 * @version     2.0.0
 * @package     com_ta_provider_directory
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */

defined('_JEXEC') or die;

abstract class Ta_provider_directoryHelper{
	/**
	 * Determines the permission level used for calendar tasks
	 * 
	 * @return int Permission level
	 * 0 = Public (view only)
	 * 1 = TA Provider (restricted to adding and editing own)
	 * 2 = Administrator (full access and ability to edit)
	 */
	 
	public static function getPermissionLevel(){
		// variables	
		$permission_level = 0;
		
		// get the user groups for this user
		$user_groups = JFactory::getUser()->getAuthorisedGroups();	
		
		// determine if this is a TA provider
		if(in_array(10, $user_groups)){
			$permission_level = 1;
		}
		
		// determine if this is an administrator
		if(in_array(7, $user_groups)
		|| in_array(8, $user_groups)
		|| in_array(11, $user_groups)
		|| in_array(12, $user_groups)){
			$permission_level = 2;
		}

		return $permission_level;
	}
}