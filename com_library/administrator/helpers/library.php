<?php
/**
 * @package     com_library
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @author      NCJFCJ <zdraper@ncjfcj.org> - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Library helper
 */
abstract class LibraryHelper{
	
	private $user;

	/**
	 * Configure the Linkbar
	 */
	public static function addSubmenu($vName = ''){
		JHtmlSidebar::addEntry(
			JText::_('COM_LIBRARY_TITLE'),
			'index.php?option=com_library&view=items',
			$vName == 'items'
		);
	}

	/**
	 * Builds a nicely formatted HTML email
	 */
	public static function buildEmail($heading, $content){
		// build the message
		$message = '<html><body>';
		$message .= '<div style="width:100%!important;padding:0;margin:0;background-color:#807C7C">';
		$message .= '<table style="font-family:Helvetica;font-size:12px" border="0" cellpadding="0" cellspacing="0" width="100%">';
		$message .= '<tbody>';
		$message .= '<tr>';
		$message .= '<td style="padding:40px 0px" align="center">';
		$message .= '<table style="font-family:Helvetica;font-size:12px" align="center" border="0" cellpadding="0" cellspacing="0" width="640">';
		$message .= '<tbody>';
		$message .= '<tr>';
		$message .= '<td valign="top">';
		$message .= '<table style="background-color:#FFF;font-family:Helvetica;font-size:12px" border="0" cellpadding="0" cellspacing="0" width="650">';
		$message .= '<tbody>';
		$message .= '<tr>';
		$message .= '<td style="padding-bottom:10px" valign="top">';
		$message .= '<table border="0" cellpadding="0" cellspacing="0" width="650">';
		$message .= '<tbody>';
		$message .= '<tr>';
		$message .= '<td align="center"><a href="' . JURI::base() . '" target="_blank"><img alt="TA2TA" style="margin: 30px 0;" src="' . JURI::base() . 'templates/ta2ta/img/logo.png"></a></td>';
		$message .= '</tr>';
		$message .= '<tr style="background-color:#F19244;color:#FFF;font-size:30px;font-weight:bold;">';
		$message .= '<td align="center" style="padding:15px 0;">' . $heading . '</td>';
		$message .= '</tr>';
		$message .= '<tr>';
		$message .= '<td align="center" style="padding:30px;">' . $content . '</td>';
		$message .= '</tr>';
		$message .= '</tbody>';
		$message .= '</table>';
		$message .= '</td>';
		$message .= '</tr>';
		$message .= '</tbody>';
		$message .= '</table>';
		$message .= '</td>';
		$message .= '</tr>';
		$message .= '</tbody>';
		$message .= '</table>';
		$message .= '</td>';
		$message .= '</tr>';
		$message .= '</tbody>';
		$message .= '</table>';
		$message .= '</div>';
		$message .= '</body></html>';

		// return the message
		return $message;
	}

	/**
	 * Generates a pseudo-random token that is unique
	 *
	 * @param int The ID of the resource for which this token will be generated
	 * @return string A unique pseudo-random string
	 */
	public static function generateAccessToken($id){
		$db = JFactory::getDbo();
		$token = '';

		// make sure the access token is unique
		$unique = false;
		while(!$unique){
			// generate a pseudo-random access token
			$token = LibraryHelper::generateRandomString(64);

			// add this token to the database
			$query = $db->getQuery(true);
			$query->insert($db->quoteName('#__library_access_tokens'));
			$query->columns($db->quoteName(array(
				'resource',
				'token'
			)));
			$query->values(implode(',',array(
				$db->quote($id),
				$db->quote($token)
			)));
			$db->setQuery($query);
			$db->execute();
			if($db->getAffectedRows() == 1){
				$unique = true;
			}
		}

		return $token;
	}

	/**
	 * Generates a pseudo-random string of alphanumeric characters of the specified length
	 *
	 * @param int The length of the string to be generated
	 * @return string A pseudo-random string
	 */
	private static function generateRandomString($len){
		$string = '';
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		for($i = 0; $i < $len; $i++){
			$string .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $string;
	}

	/**
	 * Gets a list of the actions that can be performed
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions(){
		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_library';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}

	/**
	 * Gets the organization information of the specified organization
	 * 
	 * @param int The ID of an organization
	 *
	 * @return object An object containing the organization information
	 */ 
	public static function getOrg($org){
		// get the item information
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id', 'name', 'website')));
		$query->from($db->quoteName('#__ta_providers'));
		$query->where($db->quoteName('id') . '=' . $org);
		$db->setQuery($query, 0, 1);
		
		// check that the query was successful
		if(!($organization = $db->loadObject())){
			JError::raiseWarning(100, 'Unable to retrieve organization details.');
			return false;
		}
		
		// return the organization information
		return $organization;
	}

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
	
	/**
	 * Returns the ID of the current user
	 */
	public static function getUserId(){
		// get the user object	
		$user = LibraryHelper::getUserObj();
		
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
		$query->where($db->quoteName('user_id') . '=' . $db->quote(LibraryHelper::getUserId()) . ' AND ' . $db->quoteName('profile_key') . ' = ' . $db->quote('profile.org'));
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

	/**
	 * Gets the organization information the current user's organization.
	 * 
	 * @return object An object containing the organization information
	 */ 
	public static function getUserOrg(){
		// return the organization information
		return LibraryHelper::getOrg(LibraryHelper::getUserOrgId());
	}
	
	/**
	 * Returns the username of the current user
	 */
	public static function getUserName(){
		// get the user object	
		$user = LibraryHelper::getUserObj();
		
		// return the username
		return $user->username;
	}
}
