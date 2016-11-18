<?php
/**
 * @package     com_services
 * @copyright   Copyright (C) 2016 NCJFCJ. All rights reserved.
 * @author      Zadra Design - http://zadradesign.com
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldCreatedby extends JFormField{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'createdby';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput(){
		// Initialize variables.
		$html = array();
        
        
		//Load user
		$user_id = $this->value;
		if($user_id){
			$user = JFactory::getUser($user_id);
		}else{
			$user = JFactory::getUser();
			$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$user->id.'" />';
		}
        
		return implode($html);
	}
}