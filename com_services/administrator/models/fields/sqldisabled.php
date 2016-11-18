<?php
/**
 * @package     com_library
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @author      NCJFCJ <zdraper@ncjfcj.org> - http://ncjfcj.org
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list based on a database query. Any disabled records
 * with a state not equal to 1 (active) will be excluded unless they are the currently
 * selected record (in the case of editing). If this is the case, the field name will be
 * preceded by an asterisk and a message will appear on the form below the input.
 * 
 * Form Element Values:
 * type (mandatory) = must be SQLDisabled. 
 * name (mandatory) = the unique name of the field. This must match the name of the query results column that contains
 * 					  the values that will be shown to the user in the drop-down list, unless a different name is specified
 * 					  in the value_field attribute
 * label (mandatory) (translatable) = is the descriptive title of the field. 
 * query (mandatory) = the SQL query which will provide the data for the drop-down list. The query must return two columns;
 * 					   one called 'value' (unless overridden by the key_field attribute) which will hold the values of the
 * 					   list items; the other called the same as the value of the name attribute (unless overridden by the
 * 					   value_field attribute) containing the text to be shown in the drop-down list.
 * default (optional) = the default value. This is the value of the 'value' field, unless overridden by the key_field attribute.
 * description (optional) (translatable) = text that will be shown as a tooltip when the user moves the mouse over the drop-down box. 
 * key_field(optional) = the name of the database table column that will contain values for the parameter.
 * value_field(optional) = the name of the database table column that will contain values to be shown to the user in the drop-down list.
 * state_field(optional) = the name of the database table column that contains the state to check
 * disabled_message(optional) = the message displayed to the user if the currently selected item is disabled.
 * 								Based on the label value if not specified. Use %s in the name to include the label value anywhere in the string
 * warning_symbol(optional) = the symbol that will be displayed at the front of the disabled option. An asterisk will be used if not defined.
 * translate (optional) = will translate the output of the value_field if set to true. It defaults to false. 
 */
class JFormFieldSQLdisabled extends JFormFieldList
{
	/**
	 * Properties
	 */
	private $hasDisabled = false;
	
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'SQLdisabled';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true')
		{
			$attr .= ' disabled="disabled"';
		}

		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';
		$attr .= $this->required ? ' required="required" aria-required="true"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		// Get the field options.
		$options = (array) $this->getOptions();

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true')
		{
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '"/>';
		}
		// Create a regular list.
		else
		{
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		}
		
		// check for any disabled items, and if so add text
		if($this->hasDisabled){
			// grab needed variables	
			$translateLabel = !((string) $this->element['translate_label'] == 'false' || (string) $this->element['translate_label'] == '0');
			$label = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
			$label = $translateLabel ? JText::_($label) : $label;
			$warning_symbol = $this->element['warning_symbol'] ? (string) $this->element['warning_symbol'] : '*';
			$disabled_message = $this->element['disabled_message'] ? $warning_symbol . ' ' . sprintf((string) $this->element['disabled_message'], $label) : $warning_symbol . ' ' . sprintf('This %s has been disabled', $label);
			
			// construct the message
			$html[] = '<br><small>' . $disabled_message . '</small>';
		}

		return implode($html);
	}

	/**
	 * Method to get the field input markup..
	 * Use the query attribute to supply a query to generate the list.
	 *
	 * @return	string	The field input markup.
	 */
	protected function getOptions()
	{
		// Initialize variables.
        $options = array();
		
		// Initialize some field attributes.
		$warning_symbol = $this->element['warning_symbol'] ? (string) $this->element['warning_symbol'] : '*';
		$key = $this->element['key_field'] ? (string) $this->element['key_field'] : 'value';
		$state = $this->element['state_field'] ? (string) $this->element['state_field'] : 'state';
		$translate = $this->element['translate'] ? (string) $this->element['translate'] : false;
		$query = (string) $this->element['query'];
		$value = $this->element['value_field'] ? (string) $this->element['value_field'] : (string) $this->element['name'];
				
		// Get the database object.
        $db = JFactory::getDbo();

		// Set the query and get the result list.
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		if(!empty($items))
		{
			foreach($items as $item)
			{
				if ($translate == true)
				{
					if(!isset($item->$state) || $item->$state == 1)
					{
						$options[] = JHtml::_('select.option', $item->$key, JText::_($item->$value));
					}
					else
					{
						// this record is disabled, check if it is selected, and only then show it
						if($this->value == $item->$key)
						{
							$options[] = JHtml::_('select.option', $item->$key, JText::_($warning_symbol . ' ' . $item->$value));
							$this->hasDisabled = true;
						}
					}
				}
				else
				{
					if(!isset($item->$state) || $item->$state == 1)
					{
						$options[] = JHtml::_('select.option', $item->$key, $item->$value);
					}
					else
					{
						// this record is disabled, check if it is selected, and only then show it
						if($this->value == $item->$key)
						{
							$options[] = JHtml::_('select.option', $item->$key, ($warning_symbol . ' ' . $item->$value));
							$this->hasDisabled = true;
						}
					}
				}
			}
		}
		
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}