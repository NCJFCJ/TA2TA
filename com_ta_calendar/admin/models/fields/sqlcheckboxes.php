<?php
/**
 * @package     com_ta_calendar
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('checkboxes');

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
 * cols (optional) = the number of columns to use to display the checkboxes (default: 1). Allowable values are (1, 2, 3, 4,  and 6)
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
class JFormFieldSQLcheckboxes extends JFormFieldCheckboxes{
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
	protected $type = 'SQLcheckboxes';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput(){
		$html = array();
		
		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="checkboxes ' . (string) $this->element['class'] . '"' : ' class="checkboxes"';
		$checkedOptions = explode(',', (string) $this->element['checked']);
		
		// Start the checkbox field output.
		$html[] = '<fieldset id="' . $this->id . '"' . $class . '>';
		
		// Get the field options.
		$options = $this->getOptions();
		
		// Build the checkbox field output.
		$cols = $this->element['cols'] ? (int) $this->element['cols'] : 1;
		if($cols != 1 
			&& $cols != 2
			&& $cols != 3
			&& $cols != 4
			&& $cols != 6){
			$cols = 1;		
		}
		
		$html[] = '<div class="row-fluid"><ul>';
		
		$drawnColumns = 0;
		$nextRow = 0;
		$remainingRecords = count($options);
		$totalRecords = count($options);
		for($i = 0; $i < $totalRecords; $i++){
			// Determine if a spanning div is needed
			if($i == $nextRow){
				// first, close the current div, if any
				if($i > 0){
					$html[] = '</div>';
				}	
									
				// create the new div with the appropriate span
				$html[] = '<div class="span' . (12 / $cols) . '">';
				
				// determine the next record to start a column on
				$recordsInColumn = ceil($remainingRecords / ($cols - $drawnColumns));
				
				// adjust state variables accordingly
				$nextRow += $recordsInColumn;
				$remainingRecords -= $recordsInColumn;
				$drawnColumns++;
			}
			
			// Initialize some option attributes.
			if (!isset($this->value) || empty($this->value)){
				$checked = (in_array((string) $options[$i]->value, (array) $checkedOptions) ? ' checked="checked"' : '');
			}else{
				$value = !is_array($this->value) ? explode(',', $this->value) : $this->value;
				$checked = (in_array((string) $options[$i]->value, $value) ? ' checked="checked"' : '');
			}
			
			$class = !empty($options[$i]->class) ? ' class="' . $options[$i]->class . '"' : '';
			$required = !empty($options[$i]->required) ? ' required="required" aria-required="true"' : '';
			$disabled = !empty($options[$i]->disable) ? ' disabled="disabled"' : '';
			
			// Initialize some JavaScript option attributes.
			$onclick = !empty($options[$i]->onclick) ? ' onclick="' . $options[$i]->onclick . '"' : '';

			$html[] = '<li>';
			$html[] = '<input type="checkbox" id="' . $this->id . $i . '" name="' . $this->name . '" value="'
				. htmlspecialchars($options[$i]->value, ENT_COMPAT, 'UTF-8') . '"' . $checked . $class . $onclick . $disabled . $required . '/>';

			$html[] = '<label for="' . $this->id . $i . '"' . $class . ' style="display: inline-block; margin-left: 3px;">' . JText::_($options[$i]->text) . '</label>';
			$html[] = '</li>';
			
			// if this is the last record, close the column div
			if($i == $totalRecords - 1){
				$html[] = '</div>';
			}	
		}
		$html[] = '</ul></div>';
		
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

		// End the checkbox field output.
		$html[] = '</fieldset>';

		return implode($html);
	}

	/**
	 * Method to get the field input markup..
	 * Use the query attribute to supply a query to generate the list.
	 *
	 * @return	string	The field input markup.
	 */
	protected function getOptions(){
		// Initialize variables.
    $options = array();
		
		// Initialize some field attributes.
		$warning_symbol = $this->element['warning_symbol'] ? (string) $this->element['warning_symbol'] : '*';
		$key = $this->element['key_field'] ? (string) $this->element['key_field'] : 'value';
		$state = $this->element['state_field'] ? (string) $this->element['state_field'] : 'state';
		$translate = $this->element['translate'] ? (string) $this->element['translate'] : false;
		$query = (string) $this->element['query'];
		$value = $this->element['value_field'] ? (string) $this->element['value_field'] : (string) $this->element['name'];
		$checkedOptions = explode(',', (string) $this->value);
				
		// Get the database object.
    $db = JFactory::getDbo();

		// Set the query and get the result list.
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		if(!empty($items)){
			foreach($items as $item){
				$tmp = '';
				if($translate == true){
					if(!isset($item->$state) || $item->$state == 1){
						$tmp = JHtml::_('select.option', $item->$key, JText::_($item->$value));
					}else{
						// this record is disabled, check if it is selected, and only then show it
						if(in_array($item->$key, $checkedOptions)){
							$tmp = JHtml::_('select.option', $item->$key, JText::_($warning_symbol . ' ' . $item->$value));
							$this->hasDisabled = true;
						}
					}
				}else{
					if(!isset($item->$state) || $item->$state == 1){
						$tmp = JHtml::_('select.option', $item->$key, $item->$value);
					}else{
						// this record is disabled, check if it is selected, and only then show it
						if(in_array($item->$key, $checkedOptions)){
							$tmp = JHtml::_('select.option', $item->$key, ($warning_symbol . ' ' . $item->$value));
							$this->hasDisabled = true;
						}
					}
				}
				
				if(!empty($tmp)){
					// Set the class atribute
					if(isset($item->class)){
						$tmp->class = (string) $item->class;
					}
					
					// Set the onclick attribute
					if(isset($item->onclick)){
						$tmp->onclick = (string) $item->onclick;
					}
					
					$options[] = $tmp;
				}
			}
		}
		
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}