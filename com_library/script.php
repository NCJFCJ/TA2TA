<?php
/**
 * @package     com_library
 * @copyright   Copyright (C) 2016 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Script file for the com_library component
 */
class com_libraryInstallerScript{

	/**
 	 * Executed after the Joomla install, update or discover_update actions have completed
 	 *
 	 * @param string The type of action (install, update or discover_install)
 	 * @param ?? 
	 */
	function postflight($type, $parent){
		// check if this component already has a record in the content_types table
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		$query->where($db->quoteName('type_alias') . ' = ' . $db->quote('com_library.resource'));
		$db->setQuery($query, 0, 1);
		$db->execute();

		// if this component doesn't already have a record in the content_types table, add it
		if($db->getNumRows() == 0){
			$table_data = new stdClass();
			
			$table_data->common = new stdClass();
    	$table_data->common->config = array();
    	$table_data->common->dbtable = '#__ucm_content';
    	$table_data->common->key = 'ucm_id';
    	$table_data->common->prefix = 'JTable';
    	$table_data->common->type = 'Corecontent';
			
			$table_data->special = new stdClass();
			$table_data->special->config = array();
			$table_data->special->dbtable = '#__library';
			$table_data->special->key = 'id';
			$table_data->special->prefix = 'LibraryTable';
			$table_data->special->type = 'item';

			$field_mappings_data = new stdClass();

			$field_mappings_data->common = new stdClass();
    	$field_mappings_data->common->asset_id = 'null';
    	$field_mappings_data->common->core_access = 'null';
    	$field_mappings_data->common->core_alias = 'null';
    	$field_mappings_data->common->core_body = 'description';
    	$field_mappings_data->common->core_catid = 'null';
    	$field_mappings_data->common->core_content_item_id = 'id';
    	$field_mappings_data->common->core_created_time = 'created';
    	$field_mappings_data->common->core_featured = 'null';
    	$field_mappings_data->common->core_hits = 'null';
    	$field_mappings_data->common->core_images = 'null';
    	$field_mappings_data->common->core_language = 'null';
    	$field_mappings_data->common->core_metadata = 'null';
    	$field_mappings_data->common->core_metadesc = 'null';
    	$field_mappings_data->common->core_metakey = 'null';
    	$field_mappings_data->common->core_modified_time = 'modified';
    	$field_mappings_data->common->core_ordering = 'null';
    	$field_mappings_data->common->core_params = 'null';
    	$field_mappings_data->common->core_publish_down = 'null';
    	$field_mappings_data->common->core_publish_up = 'null';
    	$field_mappings_data->common->core_state = 'state';
    	$field_mappings_data->common->core_title = 'name';
    	$field_mappings_data->common->core_urls = 'null';
    	$field_mappings_data->common->core_version = 'null';
    	$field_mappings_data->common->core_xreference = 'null';

			$field_mappings_data->special = new stdClass();
	    $field_mappings_data->special->base_file_name = 'base_file_name';
	    $field_mappings_data->special->checked_out = 'checked_out';
	    $field_mappings_data->special->checked_out_time = 'checked_out_time';
	    $field_mappings_data->special->created_by = 'created_by';
	    $field_mappings_data->special->deleted = 'deleted';
	    $field_mappings_data->special->deleted_by = 'deleted_by';
	    $field_mappings_data->special->modified_by = 'modified_by';
	    $field_mappings_data->special->org = 'org';
	    $field_mappings_data->special->project = 'project';
  
			$query = $db->getQuery(true);
			$query->insert($db->quoteName('#__content_types'));
			$query->columns($db->quoteName(array(
				'type_title',
				'type_alias',
				'table',
				'field_mappings'
			)));
			$query->values(implode(',', array(
				$db->quote('Resource'),
				$db->quote('com_library.resource'),
				$db->quote(json_encode($table_data)),
				$db->quote(json_encode($field_mappings_data))
			)));
			$db->setQuery($query);
			$db->execute();
		}
	}
}