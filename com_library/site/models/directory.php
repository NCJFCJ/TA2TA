<?php
/**
 * @package     com_library
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @author      NCJFCJ <zdraper@ncjfcj.org> - http://ncjfcj.org
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

/**
 * Library model.
 */
class LibraryModeldirectory extends JModelItem{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_LIBRARY';	
	 
	/**
	 * Returns all active Target Audiences defined in the administrator section of this component
	 * @return array of objects
	 */
	public function getTargetAudiences(){
		// Create a new query object
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		// Select the required columns
        $query->select('id, name');
		
		// Identify the table from which to pull
		$query->from('`#__target_audiences`');
		
		// Constrain results to only active event types
		$query->where('state = 1');
		
		// Alphabatize
		$query->order('name ASC');
		
		// Set the query
		$db->setQuery($query);
		
		// Execute the query and return the result
		return $db->loadObjectList();
	}
	 
	/**
	 * Retrieves all items from the database
	 */ 
	public function getItems(){
		// variables	
		$db	= $this->getDbo();
		$items = array();
		
		// obtain the basic item information
		$query = $db->getQuery(true);
		$query->select(array(
			$db->quoteName('lib.id'),
			$db->quoteName('lib.name'),
			$db->quoteName('pro.name', 'org'),
			$db->quoteName('pro.website', 'org_website'),
			$db->quoteName('lib.description'),
			$db->quoteName('lib.base_file_name'),
			$db->quoteName('lib.created') 
		));
		$query->from($db->quoteName('#__library', 'lib'));
		$query->join('LEFT', $db->quoteName('#__ta_providers','pro') . ' ON ' . $db->quoteName('lib.org') . ' = ' . $db->quoteName('pro.id'));
		$query->where($db->quoteName('lib.state') . ' = ' . $db->quote('1'));
		$query->order($db->quoteName('lib.name') . ' ASC');
		$db->setQuery($query);
		try{
			$items = $db->loadObjectList();
		}catch(Exception $e){
			JError::raiseWarning(100, 'Unable to retrieve items. Please contact us.');
			return $items;
		}
	
		// obtain the target audiences
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array(
			'id',
			'library_item',
			'target_audience'
		)));
		$query->from($db->quoteName('#__library_target_audiences'));
		$db->setQuery($query);
		try{
			$targetAudiences = $db->loadObjectList();
		}catch(Exception $e){
			JError::raiseWarning(100, 'Unable to retrieve item target audiences. Please contact us.');
			return $items;
		}
		
		// combine the target audiences with the library items and check that files exist
		foreach($items as $key=>&$item){
			// Add the new flag
			if(strtotime($item->created) > strtotime('-30 days')){
				$item->new = true;
			}else{
				$item->new = false;
			}

			// target audiences	
			$item->targetAudiences = array();
			foreach($targetAudiences as $key => &$targetAudience){
				
				// check if this target audience belongs with the item
				if($targetAudience->library_item == $item->id){
					// add this target audience to the array associated with the item
					$item->targetAudiences[] = $targetAudience->target_audience;
					
					// remove this item so it isn't checked in subsequent loops
					unset($targetAudiences[$key]);
				}
			}
			
			// pdf file
			$item->document_path = '/media/com_library/resources/' . $item->id . '-' . $item->base_file_name . '.pdf';
			if(!file_exists(JPATH_SITE . $item->document_path)){
				unset($items[$key]);
				return;
			}			
			
			// cover file
			$item->cover_path = '/media/com_library/covers/' . $item->id . '-' . $item->base_file_name . '.png';
			if(!file_exists(JPATH_SITE . $item->cover_path)){
				$item->cover_path = '/media/com_library/covers/no-cover.jpg';
			}
		}

		// reorganize so new items show first
		usort($items, array('LibraryModeldirectory', 'orderByNewAndName'));

		return $items;
	}

	/**
	 * This is a custom sort function which takes in the list of items and organizes them alphabetically, giving
	 * priority to new items first.
	 * @param object An item object
	 * @param object An item object
	 */
	private static function orderByNewAndName($a, $b){
		// both are new, or neither are new just sort alpha
		if(($a->new && $b->new) || (!$a->new && !$b->new)){
			return strcasecmp($a->name, $b->name);
		}

		// if only one is new, sort on that
		if($a->new || $b->new){
			return $a->new ? -1 : 1;
		}
	}
}
?>