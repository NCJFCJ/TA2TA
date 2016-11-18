<?php
/**
 * @package     com_services
 * @copyright   Copyright (C) 2016 NCJFCJ. All rights reserved.
 * @author      Zadra Design - http://zadradesign.com
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit
 */
class ServicesViewMeeting extends JViewLegacy{
	protected $state;
	protected $item;
	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null){

		$this->state = $this->get('State');
		$this->item	= $this->get('Item');
		$this->form	= $this->get('Form');

		// Render the download file
		if(filter_has_var(INPUT_GET, 'registrationDownload')){		
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename=registrants.csv');
			
			// create a file pointer connected to the output stream
			$output = fopen('php://output', 'w');

			// determine the column headings
			$headings = array(
				'Date/Time',
				'First Name',
				'Last Name',
				'Email Address',
				'City',
				'State',
				'Zip',
				'Organization',
				'Occupation',
				'Address',
				'Phone',
				'Fax');

			// custom questions
			for($i = 1; $i <= 3; $i++){
				if(!empty($this->item->{'registration_q' . $i})){
					$headings[] = $this->item->{'registration_q' . $i};
				}
			}

			// advanced accessibility questions
			if($this->item->registration_adv_accessibility){
				$headings[] = 'Interpreter';
				$headings[] = 'Interpreter Language';
				$headings[] = 'Spoken Interpretation';
				$headings[] = 'Braille';
				$headings[] = 'Large Print';
			}

			$headings[] = 'Other Accessibility Needs';

			// output the column headings
			fputcsv($output, $headings);

			foreach($this->item->registration_records as $record){
				$rd = new DateTime($record->registered, new DateTimeZone('UTC'));
				$rd->setTimezone(new DateTimeZone('America/Los_Angeles'));
				// output array
				$data = array(
					$rd->format('m/d/Y g:iA'),
					$record->fname,
					$record->lname,
					ServicesHelper::decrypt($record->email),
					ucwords(strtolower($record->city)),
					$record->territory,
					$record->zip,
					$record->org,
					$record->occupation,
					ServicesHelper::decrypt($record->address) . ', ' . ServicesHelper::decrypt($record->address2),
					ServicesHelper::decrypt($record->phone),
					ServicesHelper::decrypt($record->fax)
				);

				// custom questions
				for($i = 1; $i <= 3; $i++){
					if(!empty($this->item->{'registration_q' . $i})){
						$data[] = $record->{'q' . $i . '_answer'};
					}
				}

				// advanced accessibility questions
				if($this->item->registration_adv_accessibility){
					$data[] = ($record->accessibility_interpreter == 1 ? 'Yes' : 'No');
					$data[] = $record->accessibility_interpreter_lang;
					$data[] = $record->accessibility_simultaneous_interpretation;
					$data[] = ($record->accessibility_braille == 1 ? 'Yes' : 'No');
					$data[] = ($record->accessibility_large_print == 1 ? 'Yes' : 'No');
				}

				$data[] = $record->accessibility;

				// output the data
				fputcsv($output, $data);
			}

			die();
		}

		// Check for errors.
		if(count($errors = $this->get('Errors'))){
      throw new Exception(implode("\n", $errors));
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar(){
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user = JFactory::getUser();
		$isNew = ($this->item->id == 0);
    if(isset($this->item->checked_out)){
	    $checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
    }else{
      $checkedOut = false;
    }
		$canDo = ServicesHelper::getActions();

		JToolBarHelper::title(JText::_('COM_SERVICES_TITLE_MEETING'), 'meeting.png');

		// If not checked out, can save the item.
		if(!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create')))){
			JToolBarHelper::apply('meeting.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('meeting.save', 'JTOOLBAR_SAVE');
		}
		if(!$checkedOut && ($canDo->get('core.create'))){
			JToolBarHelper::custom('meeting.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}
		if(empty($this->item->id)){
			JToolBarHelper::cancel('meeting.cancel', 'JTOOLBAR_CANCEL');
		}else{
			JToolBarHelper::cancel('meeting.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
