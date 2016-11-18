<?php
/**
 * @package     mod_stats
 * @copyright   Copyright (C) 2016 NCJFCJ. All rights reserved.
 * @author      Zadra Design <zachary@zadradesign.com> - http://zadradesign.com
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Stats helper
 */
abstract class StatsHelper{
	
	private $user;
	
	/**
	 * Returns the ID of the current user
	 */
	public static function getUserId(){
		// get the user object	
		$user = StatsHelper::getUserObj();
		
		return $user->id;
	}
	
	/**
	 * Returns the user object
	 */
	public static function getUserObj(){
		return JFactory::getUser();
	}

	/**
	 * Gets the organization of this user
	 * @return int The ID of the user's organization, 0 on fail
	 */
	public static function getUserOrgId(){
		// get the user's organization
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('profile_value'));
		$query->from($db->quoteName('#__user_profiles'));
		$query->where($db->quoteName('user_id') . '=' . $db->quote(StatsHelper::getUserId()) . ' AND ' . $db->quoteName('profile_key') . ' = ' . $db->quote('profile.org'));
		$db->setQuery($query, 0, 1);
		
		// check that the query was successful
		if(!($org = $db->loadResult())){
			JError::raiseWarning(100, 'Unable to determine user\'s organization.');
			return 0;
		}
		
		// remove quotes
		$org = substr($org, 1, -1);
		
		// return the result
		return (int)$org;
	}	
}