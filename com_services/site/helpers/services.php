<?php
/**
 * @package     com_services
 * @copyright   Copyright (C) 2016 NCJFCJ. All rights reserved.
 * @author      Zadra Design - http://zadradesign.com
 */

defined('_JEXEC') or die;

abstract class ServicesHelper{
	
	private $user;

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
		$message .= '<td align="center"><a href="' . JURI::root() . '" target="_blank"><img alt="TA2TA" style="margin: 30px 0;" src="' . JURI::root() . 'templates/ta2ta/img/logo.png"></a></td>';
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
	 * Generates a sudeorandom 32 character hexidecimal string
	 */
	public static function getUniqueKey(){
		return bin2hex(openssl_random_pseudo_bytes(16));
	}
	
	/**
	 * Returns the ID of the current user
	 */
	public static function getUserId(){
		// get the user object	
		$user = ServicesHelper::getUserObj();
		
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
		$query->where($db->quoteName('user_id') . '=' . $db->quote(ServicesHelper::getUserId()) . ' AND ' . $db->quoteName('profile_key') . ' = ' . $db->quote('profile.org'));
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
		return ServicesHelper::getOrg(ServicesHelper::getUserOrgId());
	}
}