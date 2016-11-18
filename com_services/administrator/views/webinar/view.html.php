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
class ServicesViewWebinar extends JViewLegacy{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null){

		$this->state = $this->get('State');
		$this->item	= $this->get('Item');
		$this->form	= $this->get('Form');

		// if this is a download request, output a CSV file
		if(filter_has_var(INPUT_GET, 'portalStatsDownload')){
			$date = $_GET['portalStatsDownload'] == '*' ? '*' : filter_input(INPUT_GET, 'portalStatsDownload', FILTER_SANITIZE_NUMBER_INT);
			
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename=viewers.csv');
			
			// create a file pointer connected to the output stream
			$output = fopen('php://output', 'w');

			// output the column headings
			fputcsv($output, array('Date/Time', 'First Name', 'Last Name', 'Email Address', 'Occupation', '# of Viewers'));

			foreach($this->item->portal_records as $record){
				$rd = new DateTime($record->created);
				if($date == '*' || $date == $rd->format('Ymd')){
					fputcsv($output, array(
						$rd->format('m/d/Y h:iA'),
						$record->fname,
						$record->lname,
						$record->email,
						$record->occupation,
						$record->num_viewers
					));
				}
			}
			die();
		}else if(filter_has_var(INPUT_GET, 'registrationDownload')){		
			$date = $_GET['registrationDownload'] == '*' ? '*' : filter_input(INPUT_GET, 'registrationDownload', FILTER_SANITIZE_STRING);
			
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename=registrants.csv');
			
			// create a file pointer connected to the output stream
			$output = fopen('php://output', 'w');

			// determine the column headings
			$headings = array(
				'Webinar',
				'Date/Time',
				'First Name',
				'Last Name',
				'Email Address',
				'City',
				'State',
				'Zip',
				'Organization',
				'Occupation');

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
				$wd = new DateTime($record->start);
				if($date == '*' || $date == $wd->format('Y-m-d H:i:s')){
					// output array
					$data = array(
						$wd->format('m/d/Y g:iA'),
						$rd->format('m/d/Y g:iA'),
						$record->fname,
						$record->lname,
						ServicesHelper::decrypt($record->email),
						ucwords(strtolower($record->city)),
						$record->territory,
						$record->zip,
						$record->org,
						$record->occupation
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

		JToolBarHelper::title(JText::_('COM_SERVICES_TITLE_WEBINAR'), 'webinar.png');

		// If not checked out, can save the item.
		if(!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create')))){
			JToolBarHelper::apply('webinar.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('webinar.save', 'JTOOLBAR_SAVE');
		}
		if(!$checkedOut && ($canDo->get('core.create'))){
			JToolBarHelper::custom('webinar.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}
		if(empty($this->item->id)){
			JToolBarHelper::cancel('webinar.cancel', 'JTOOLBAR_CANCEL');
		}else{
			JToolBarHelper::cancel('webinar.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
