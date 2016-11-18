<?php
/**
 * @package     com_services
 * @copyright   Copyright (C) 2016 NCJFCJ. All rights reserved.
 * @author      Zadra Design - http://zadradesign.com
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of available TA projects for the user's organization
 */
class JFormFieldTAProjects extends JFormFieldList{
	
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'TAProjects';

	/**
	 * Method to get the field input markup..
	 * Use the query attribute to supply a query to generate the list.
	 *
	 * @return	string	The field input markup.
	 */
	protected function getOptions(){
		// require the helper
		require_once JPATH_COMPONENT.'/helpers/services.php';

		// get the user's organization
		$org = ServicesHelper::getUserOrgId();

		// get a list of TA Projects for the user's organization
		$projects = new stdClass();
		if($org > 0){
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select(array(
				$db->quoteName('id'),
				$db->quoteName('title')
			));
			$query->from($db->quoteName('#__tapd_provider_projects'));
			$query->where($db->quoteName('state') . '=1 AND ' . $db->quoteName('provider') . '=' . $org);
			$query->order($db->quoteName('title') . ' ASC');
			$db->setQuery($query);
			$projects = $db->loadObjectList();
		}

		// loop through each project and prepare the options
		$options = array();
		foreach($projects as $project){		
			$tmp = array(
					'checked'  => false,
					'class'    => '',
					'disable'  => false,
					'onchange' => '',
					'onclick' 	=> '',
					'selected' => false,
					'text'     => $project->title,
					'value'    => $project->id
				);

			// Add the option object to the result set.
			$options[] = (object) $tmp;
		}

		reset($options);

		return $options;
	}
}