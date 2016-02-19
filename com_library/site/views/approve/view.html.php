<?php
/**
 * @package     com_library
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @author      NCJFCJ <zdraper@ncjfcj.org> - http://ncjfcj.org
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die(); 
  
// import Joomla! view library
jimport('joomla.application.component.view');

// require the helper
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/library.php');
 
/**
 * HTML View class for the Programs Component
 */
 
class LibraryViewApprove extends JViewLegacy{
	
	// Overwriting JView display method
	function display($tpl = null) {
		// get variables	
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		$this->error = false;
		
		// get the token and state
		$jinput = JFactory::getApplication()->input;
		$this->state = $jinput->get('state', '', 'int');
		$doc_id = $jinput->get('id', '', 'int');
		$token = $jinput->get('token', '', 'alnum');

		/* Get the permission level
		 * 0 = Public (view only)
		 * 1 = TA Provider (restricted to adding and editing own)
		 * 2 = Administrator (full access and ability to edit)
		 */
		$permission_level = LibraryHelper::getPermissionLevel();

		// if there is no state, the state is invalid, or there is no token AND the user is not OVW or NCJFCJ
		if(empty($this->state) || !in_array($this->state, array(1,2)) || (empty($token) && $permission_level < 2)){
			JError::raiseError(404, 'Page Not Found');
			return false;
		}
 
		// check if the token is valid
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		if(!empty($token)){
			$query->select(implode(',', array(
				$db->quoteName('lib.id'),
				$db->quoteName('lib.state'),
				$db->quoteName('pro.name', 'org_name'),
				$db->quoteName('pro.website', 'org_website'),
				$db->quoteName('lib.org'),
				$db->quoteName('lib.name'),
				$db->quoteName('lib.description'),
				$db->quoteName('lib.base_file_name')
			)));
			$query->from($db->quoteName('#__library_access_tokens', 'tok'));
			$query->join('LEFT', $db->quoteName('#__library', 'lib') . ' ON (' . $db->quoteName('lib.id') . ' = ' . $db->quoteName('tok.resource') . ')');
			$query->join('LEFT', $db->quoteName('#__ta_providers', 'pro') . ' ON (' . $db->quoteName('pro.id') . ' = ' . $db->quoteName('lib.org') . ')');
			$query->where($db->quoteName('tok.token') . '=' . $db->quote($token) . ' AND ' . $db->quoteName('tok.exp_date') . ' > NOW()');
			$db->setQuery($query);
			$this->item = $db->loadObject();
		}else{
			if(!empty($doc_id)){
				$query->select(implode(',', array(
					$db->quoteName('lib.id'),
					$db->quoteName('lib.state'),
					$db->quoteName('pro.name', 'org_name'),
					$db->quoteName('pro.website', 'org_website'),
					$db->quoteName('lib.org'),
					$db->quoteName('lib.name'),
					$db->quoteName('lib.description'),
					$db->quoteName('lib.base_file_name')
				)));
				$query->from($db->quoteName('#__library', 'lib'));
				$query->join('LEFT', $db->quoteName('#__ta_providers', 'pro') . ' ON (' . $db->quoteName('pro.id') . ' = ' . $db->quoteName('lib.org') . ')');
				$query->where($db->quoteName('lib.id') . '=' . $db->quote($doc_id));
				$db->setQuery($query);
				$this->item = $db->loadObject();
			}else{
				JError::raiseError(400, 'Bad request, no document ID provided.');
				return false;
			}
		}

		// check that we have one and only one result
		if(!$this->item){
			if($permission_level < 2){
				JError::raiseError(404, 'Page Not Found');
				return false;
			}else{
				JError::raiseError(400, 'Bad request, document could not be retrieved.');
				return false;
			}
		}

		// publish or archive this library resource
		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__library'));
		$query->set($db->quoteName('state') . ' = ' . $db->quote($this->state));
		$query->where($db->quoteName('id') . ' = ' . $db->quote($this->item->id));
		$db->setQuery($query);
		$result = $db->execute();
		if($db->getAffectedRows($result) != 1 && $this->state != $this->item->state){
			$this->error = true;
		}

		// delete the token, and for good measure, all expired tokens also
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__library_access_tokens'));
		$query->where($db->quoteName('token') . '=' . $db->quote($token) . ' OR ' . $db->quoteName('exp_date') . '< NOW()');
		$db->setQuery($query);
		$db->execute();

		// Check for errors.
		if(count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		// Display the view
		parent::display($tpl);
	}
}