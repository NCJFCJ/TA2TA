<?php
/**
 * @package     com_services
 * @copyright   Copyright (C) 2016 NCJFCJ. All rights reserved.
 * @author      Zadra Design - http://zadradesign.com
 */

// no direct access
defined('_JEXEC') or die;

$mainframe =JFactory::getApplication();
$stateVar = $mainframe->getUserState('com_services.edit.webinar.data', array());
if(isset($stateVar['webinars'])){
	$webinars = $stateVar['webinars'];
}else{
	$webinars = array();
}

// end time
$end_time = (isset($this->item->webinars[0]->end) ? $this->item->webinars[0]->end->format('g:ia') : '9:00am');

// start time
$start_time = (isset($this->item->webinars[0]->start) ? $this->item->webinars[0]->start->format('g:ia') : '8:00am');

JHtml::_('behavior.keepalive');
?>
<div id="com_servicesServiceForm">
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<div class="page-header">
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	</div>
	<?php endif; ?>
	<form enctype="multipart/form-data" method="post" action="/" role="form" novalidate>
		<div id="alertWrapper"></div>
		<div id="formWrapper">
			<div class="form-group">
				<?php echo $this->form->getLabel('title'); ?>
				<?php echo $this->form->getInput('title'); ?>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('description'); ?>
				<?php echo $this->form->getInput('description'); ?>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('project'); ?>
				<?php echo $this->form->getInput('project'); ?>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('series'); ?>
				<br>
				<?php $series = $this->form->getValue('series'); ?>
				<fieldset class="radio btn-group" id="jform_series" style="padding-left: 0;">
					<input id="jform_series0" type="radio" value="0" name="jform[series]" <?php if($series == 0){echo 'checked ';} ?>/>
					<label class="btn btn-default" for="jform_series0">Single Webinar</label>
					<input id="jform_series1" type="radio" value="1" name="jform[series]" <?php if($series == 1){echo 'checked ';} ?>/>
					<label class="btn btn-default" for="jform_series1">Series</label>
				</fieldset>
			</div>
			<div class="form-group row" style="margin-bottom: 15px;">
				<div class="col-xs-12">
					<?php echo $this->form->getLabel('time_zone'); ?>
				</div>
				<div class="col-sm-6 col-md-5 col-lg-4">
					<?php echo $this->form->getInput('time_zone'); ?>
				</div>
			</div>
			<div id="singleWebinar">
				<div class="form-group row">
					<div class="col-xs-12">
						<?php echo $this->form->getLabel('date'); ?>
					</div>
					<div class="col-sm-4 col-md-3 col-lg-2">
						<input name="date[]" value="<?php echo (isset($webinars[0]->start) ? $webinars[0]->start->format('m/d/Y') : ''); ?>" class="form-control" type="text">
					</div>
				</div>
				<div class="form-group row">
					<div class="col-xs-12">
						<?php echo $this->form->getLabel('start_time'); ?>
					</div>
					<div class="col-sm-4 col-md-3 col-lg-2">
						<select class="form-control no-chosen" name="start_time[]">
				      <option value="8:00am"<?php echo ($start_time == '8:00am' ? 'selected' : '') ?>>8:00am</option>
				      <option value="8:15am"<?php echo ($start_time == '8:15am' ? 'selected' : '') ?>>8:15am</option>
				      <option value="8:30am"<?php echo ($start_time == '8:30am' ? 'selected' : '') ?>>8:30am</option>
				      <option value="8:45am"<?php echo ($start_time == '8:45am' ? 'selected' : '') ?>>8:45am</option>
				      <option value="9:00am"<?php echo ($start_time == '9:00am' ? 'selected' : '') ?>>9:00am</option>
				      <option value="9:15am"<?php echo ($start_time == '9:15am' ? 'selected' : '') ?>>9:15am</option>
				      <option value="9:30am"<?php echo ($start_time == '9:30am' ? 'selected' : '') ?>>9:30am</option>
				      <option value="9:45am"<?php echo ($start_time == '9:45am' ? 'selected' : '') ?>>9:45am</option>
				      <option value="10:00am"<?php echo ($start_time == '10:00am' ? 'selected' : '') ?>>10:00am</option>
				      <option value="10:15am"<?php echo ($start_time == '10:15am' ? 'selected' : '') ?>>10:15am</option>
				      <option value="10:30am"<?php echo ($start_time == '10:30am' ? 'selected' : '') ?>>10:30am</option>
				      <option value="10:45am"<?php echo ($start_time == '10:45am' ? 'selected' : '') ?>>10:45am</option>
				      <option value="11:00am"<?php echo ($start_time == '11:00am' ? 'selected' : '') ?>>11:00am</option>
				      <option value="11:15am"<?php echo ($start_time == '11:15am' ? 'selected' : '') ?>>11:15am</option>
				      <option value="11:30am"<?php echo ($start_time == '11:30am' ? 'selected' : '') ?>>11:30am</option>
				      <option value="11:45am"<?php echo ($start_time == '11:45am' ? 'selected' : '') ?>>11:45am</option>
				      <option value="12:00pm"<?php echo ($start_time == '12:00pm' ? 'selected' : '') ?>>12:00pm</option>
				      <option value="12:15pm"<?php echo ($start_time == '12:15pm' ? 'selected' : '') ?>>12:15pm</option>
				      <option value="12:30pm"<?php echo ($start_time == '12:30pm' ? 'selected' : '') ?>>12:30pm</option>
				      <option value="12:45pm"<?php echo ($start_time == '12:45pm' ? 'selected' : '') ?>>12:45pm</option>
				      <option value="1:00pm"<?php echo ($start_time == '1:00pm' ? 'selected' : '') ?>>1:00pm</option>
				      <option value="1:15pm"<?php echo ($start_time == '1:15pm' ? 'selected' : '') ?>>1:15pm</option>
				      <option value="1:30pm"<?php echo ($start_time == '1:30pm' ? 'selected' : '') ?>>1:30pm</option>
				      <option value="1:45pm"<?php echo ($start_time == '1:45pm' ? 'selected' : '') ?>>1:45pm</option>
				      <option value="2:00pm"<?php echo ($start_time == '2:00pm' ? 'selected' : '') ?>>2:00pm</option>
				      <option value="2:15pm"<?php echo ($start_time == '2:15pm' ? 'selected' : '') ?>>2:15pm</option>
				      <option value="2:30pm"<?php echo ($start_time == '2:30pm' ? 'selected' : '') ?>>2:30pm</option>
				      <option value="2:45pm"<?php echo ($start_time == '2:45pm' ? 'selected' : '') ?>>2:45pm</option>
				      <option value="3:00pm"<?php echo ($start_time == '3:00pm' ? 'selected' : '') ?>>3:00pm</option>
				      <option value="3:15pm"<?php echo ($start_time == '3:15pm' ? 'selected' : '') ?>>3:15pm</option>
				      <option value="3:30pm"<?php echo ($start_time == '3:30pm' ? 'selected' : '') ?>>3:30pm</option>
				      <option value="3:45pm"<?php echo ($start_time == '3:45pm' ? 'selected' : '') ?>>3:45pm</option>
				      <option value="4:00pm"<?php echo ($start_time == '4:00pm' ? 'selected' : '') ?>>4:00pm</option>
				      <option value="4:15pm"<?php echo ($start_time == '4:15pm' ? 'selected' : '') ?>>4:15pm</option>
				      <option value="4:30pm"<?php echo ($start_time == '4:30pm' ? 'selected' : '') ?>>4:30pm</option>
				      <option value="4:45pm"<?php echo ($start_time == '4:45pm' ? 'selected' : '') ?>>4:45pm</option>
				      <option value="5:00pm"<?php echo ($start_time == '5:00pm' ? 'selected' : '') ?>>5:00pm</option>
						</select>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-xs-12">
						<?php echo $this->form->getLabel('end_time'); ?>
					</div>
					<div class="col-sm-4 col-md-3 col-lg-2">
						<select class="form-control no-chosen" name="end_time[]">
				      <option value="8:15am"<?php echo ($end_time == '8:15am' ? 'selected' : '') ?>>8:15am</option>
				      <option value="8:30am"<?php echo ($end_time == '8:30am' ? 'selected' : '') ?>>8:30am</option>
				      <option value="8:45am"<?php echo ($end_time == '8:45am' ? 'selected' : '') ?>>8:45am</option>
				      <option value="9:00am"<?php echo ($end_time == '9:00am' ? 'selected' : '') ?>>9:00am</option>
				      <option value="9:15am"<?php echo ($end_time == '9:15am' ? 'selected' : '') ?>>9:15am</option>
				      <option value="9:30am"<?php echo ($end_time == '9:30am' ? 'selected' : '') ?>>9:30am</option>
				      <option value="9:45am"<?php echo ($end_time == '9:45am' ? 'selected' : '') ?>>9:45am</option>
				      <option value="10:00am"<?php echo ($end_time == '10:00am' ? 'selected' : '') ?>>10:00am</option>
				      <option value="10:15am"<?php echo ($end_time == '10:15am' ? 'selected' : '') ?>>10:15am</option>
				      <option value="10:30am"<?php echo ($end_time == '10:30am' ? 'selected' : '') ?>>10:30am</option>
				      <option value="10:45am"<?php echo ($end_time == '10:45am' ? 'selected' : '') ?>>10:45am</option>
				      <option value="11:00am"<?php echo ($end_time == '11:00am' ? 'selected' : '') ?>>11:00am</option>
				      <option value="11:15am"<?php echo ($end_time == '11:15am' ? 'selected' : '') ?>>11:15am</option>
				      <option value="11:30am"<?php echo ($end_time == '11:30am' ? 'selected' : '') ?>>11:30am</option>
				      <option value="11:45am"<?php echo ($end_time == '11:45am' ? 'selected' : '') ?>>11:45am</option>
				      <option value="12:00pm"<?php echo ($end_time == '12:00pm' ? 'selected' : '') ?>>12:00pm</option>
				      <option value="12:15pm"<?php echo ($end_time == '12:15pm' ? 'selected' : '') ?>>12:15pm</option>
				      <option value="12:30pm"<?php echo ($end_time == '12:30pm' ? 'selected' : '') ?>>12:30pm</option>
				      <option value="12:45pm"<?php echo ($end_time == '12:45pm' ? 'selected' : '') ?>>12:45pm</option>
				      <option value="1:00pm"<?php echo ($end_time == '1:00pm' ? 'selected' : '') ?>>1:00pm</option>
				      <option value="1:15pm"<?php echo ($end_time == '1:15pm' ? 'selected' : '') ?>>1:15pm</option>
				      <option value="1:30pm"<?php echo ($end_time == '1:30pm' ? 'selected' : '') ?>>1:30pm</option>
				      <option value="1:45pm"<?php echo ($end_time == '1:45pm' ? 'selected' : '') ?>>1:45pm</option>
				      <option value="2:00pm"<?php echo ($end_time == '2:00pm' ? 'selected' : '') ?>>2:00pm</option>
				      <option value="2:15pm"<?php echo ($end_time == '2:15pm' ? 'selected' : '') ?>>2:15pm</option>
				      <option value="2:30pm"<?php echo ($end_time == '2:30pm' ? 'selected' : '') ?>>2:30pm</option>
				      <option value="2:45pm"<?php echo ($end_time == '2:45pm' ? 'selected' : '') ?>>2:45pm</option>
				      <option value="3:00pm"<?php echo ($end_time == '3:00pm' ? 'selected' : '') ?>>3:00pm</option>
				      <option value="3:15pm"<?php echo ($end_time == '3:15pm' ? 'selected' : '') ?>>3:15pm</option>
				      <option value="3:30pm"<?php echo ($end_time == '3:30pm' ? 'selected' : '') ?>>3:30pm</option>
				      <option value="3:45pm"<?php echo ($end_time == '3:45pm' ? 'selected' : '') ?>>3:45pm</option>
				      <option value="4:00pm"<?php echo ($end_time == '4:00pm' ? 'selected' : '') ?>>4:00pm</option>
				      <option value="4:15pm"<?php echo ($end_time == '4:15pm' ? 'selected' : '') ?>>4:15pm</option>
				      <option value="4:30pm"<?php echo ($end_time == '4:30pm' ? 'selected' : '') ?>>4:30pm</option>
				      <option value="4:45pm"<?php echo ($end_time == '4:45pm' ? 'selected' : '') ?>>4:45pm</option>
				      <option value="5:00pm"<?php echo ($end_time == '5:00pm' ? 'selected' : '') ?>>5:00pm</option>
						</select>
						<input name="sub_title[]" value="" class="form-control" type="hidden">
					</div>
				</div>
			</div>
			<div id="webinarSeries">
				<div class="form-group">
					<table class="table table-stripped">
						<thead>
							<tr>
								<th style="width: 125px;"><?php echo JText::_('COM_SERVICES_WEBINAR_FORM_DATE_LBL'); ?></th>
								<th style="width: 115px;"><?php echo JText::_('COM_SERVICES_WEBINAR_FORM_START_TIME_LBL'); ?></th>
								<th style="width: 115px;"><?php echo JText::_('COM_SERVICES_WEBINAR_FORM_END_TIME_LBL'); ?></th>
								<th><?php echo JText::_('COM_SERVICES_WEBINAR_FORM_SUB_TITLE_LBL'); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><input name="date[]" value="" class="form-control" type="text"></td>
								<td>
									<select class="form-control no-chosen" name="start_time[]">
							      <option value="8:00am">8:00am</option>
							      <option value="8:15am">8:15am</option>
							      <option value="8:30am">8:30am</option>
							      <option value="8:45am">8:45am</option>
							      <option value="9:00am">9:00am</option>
							      <option value="9:15am">9:15am</option>
							      <option value="9:30am">9:30am</option>
							      <option value="9:45am">9:45am</option>
							      <option value="10:00am">10:00am</option>
							      <option value="10:15am">10:15am</option>
							      <option value="10:30am">10:30am</option>
							      <option value="10:45am">10:45am</option>
							      <option value="11:00am">11:00am</option>
							      <option value="11:15am">11:15am</option>
							      <option value="11:30am">11:30am</option>
							      <option value="11:45am">11:45am</option>
							      <option value="12:00pm">12:00pm</option>
							      <option value="12:15pm">12:15pm</option>
							      <option value="12:30pm">12:30pm</option>
							      <option value="12:45pm">12:45pm</option>
							      <option value="1:00pm">1:00pm</option>
							      <option value="1:15pm">1:15pm</option>
							      <option value="1:30pm">1:30pm</option>
							      <option value="1:45pm">1:45pm</option>
							      <option value="2:00pm">2:00pm</option>
							      <option value="2:15pm">2:15pm</option>
							      <option value="2:30pm">2:30pm</option>
							      <option value="2:45pm">2:45pm</option>
							      <option value="3:00pm">3:00pm</option>
							      <option value="3:15pm">3:15pm</option>
							      <option value="3:30pm">3:30pm</option>
							      <option value="3:45pm">3:45pm</option>
							      <option value="4:00pm">4:00pm</option>
							      <option value="4:15pm">4:15pm</option>
							      <option value="4:30pm">4:30pm</option>
							      <option value="4:45pm">4:45pm</option>
							      <option value="5:00pm">5:00pm</option>
									</select>
								</td>
								<td>
									<select class="form-control no-chosen" name="end_time[]">
							      <option value="8:15am">8:15am</option>
							      <option value="8:30am">8:30am</option>
							      <option value="8:45am">8:45am</option>
							      <option value="9:00am">9:00am</option>
							      <option value="9:15am">9:15am</option>
							      <option value="9:30am">9:30am</option>
							      <option value="9:45am">9:45am</option>
							      <option value="10:00am">10:00am</option>
							      <option value="10:15am">10:15am</option>
							      <option value="10:30am">10:30am</option>
							      <option value="10:45am">10:45am</option>
							      <option value="11:00am">11:00am</option>
							      <option value="11:15am">11:15am</option>
							      <option value="11:30am">11:30am</option>
							      <option value="11:45am">11:45am</option>
							      <option value="12:00pm">12:00pm</option>
							      <option value="12:15pm">12:15pm</option>
							      <option value="12:30pm">12:30pm</option>
							      <option value="12:45pm">12:45pm</option>
							      <option value="1:00pm">1:00pm</option>
							      <option value="1:15pm">1:15pm</option>
							      <option value="1:30pm">1:30pm</option>
							      <option value="1:45pm">1:45pm</option>
							      <option value="2:00pm">2:00pm</option>
							      <option value="2:15pm">2:15pm</option>
							      <option value="2:30pm">2:30pm</option>
							      <option value="2:45pm">2:45pm</option>
							      <option value="3:00pm">3:00pm</option>
							      <option value="3:15pm">3:15pm</option>
							      <option value="3:30pm">3:30pm</option>
							      <option value="3:45pm">3:45pm</option>
							      <option value="4:00pm">4:00pm</option>
							      <option value="4:15pm">4:15pm</option>
							      <option value="4:30pm">4:30pm</option>
							      <option value="4:45pm">4:45pm</option>
							      <option value="5:00pm">5:00pm</option>
									</select>
								</td>
								<td><input name="sub_title[]" value="" class="form-control" type="text"></td>
							</tr>
						</tbody>
					</table>
					<button class="btn btn-primary"><span class="icomoon-plus-circle"></span> Add Webinar In Series</button>
					<br>
				</div>
			</div>
			<div class="form-group row">
				<div class="col-xs-12">
					<?php echo $this->form->getLabel('num_participants'); ?>
				</div>
				<div class="col-sm-4 col-md-3 col-lg-2">
					<?php echo $this->form->getInput('num_participants'); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('features'); ?>
				<?php echo $this->form->getInput('features'); ?>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('registration'); ?>
				<br>
				<?php $series = $this->form->getValue('registration'); ?>
				<fieldset class="radio btn-group" id="jform_registration" style="padding-left: 0;">
					<input id="jform_registration0" type="radio" value="0" name="jform[registration]" <?php if($series == 0){echo 'checked ';} ?>/>
					<label class="btn btn-default" for="jform_registration0">No</label>
					<input id="jform_registration1" type="radio" value="1" name="jform[registration]" <?php if($series == 1){echo 'checked ';} ?>/>
					<label class="btn btn-default" for="jform_registration1">Yes</label>
				</fieldset>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('materials'); ?>
				<?php echo $this->form->getInput('materials'); ?>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('comments'); ?>
				<?php echo $this->form->getInput('comments'); ?>
			</div>
			<div class="form-actions">
				<div class="pull-right">
						<?php echo JHtml::_('form.token'); ?>
						<input type="hidden" name="return_url" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
						<input type="hidden" name="MAX_FILE_SIZE" value="26210000" />
						<input type="hidden" name="option" value="com_services" />
						<input type="hidden" name="task" value="webinar.save" />
				    <button type="reset" class="btn btn-default btn-lg">Clear Form</button>
				    <button type="submit" class="btn btn-lg btn-primary">Complete Request</button>
			    </div>
			</div>
		</div>
	</form>
	<script type="text/javascript">
		jQuery(function($){
			var tableRow = $('#webinarSeries tbody').html();
			var webinars = new Array();
			<?php
			foreach($webinars as $webinar){
				echo 'webinars.push(["' . (isset($webinar->start) ? $webinar->start->format('m/d/Y') : '') . '","' . (isset($webinar->start) ? $webinar->start->format('g:ia') : '') . '","' . (isset($webinar->end) ? $webinar->end->format('g:ia') : '') . '","' . (isset($webinar->sub_title) ? $webinar->sub_title : '') . '"]);';
			}
			?>

			// populate the select fields
			function populateTimes(lastRowOnly){
				var lastRowOnly = (lastRowOnly === undefined ? false : lastRowOnly);

				var ampm = 'am';
				var endHour = 5;
				var options = [];
				var startHour = 8;
				var time_zone = $('#jform_time_zone').val();

				// adjust the start and end hour based on the timezone
				switch(time_zone){
					case 'America/Boise':
						startHour++;
						endHour++;
						break;
					case 'America/Chicago':
						startHour = startHour + 2;
						endHour = endHour + 2;
						break;
					case 'America/New_York':
						startHour = startHour + 3;
						endHour = endHour + 3;
						break;
					default:
						break;
				}

				// create the select options
				var hour = startHour;
				var looping = true;
				var mins = 0;
				while(looping){
					// determine the time
					var time = hour + ':' + (mins == 0 ? '00' : mins) + ampm;

					// write the option
					options.push('<option value="' + time + '">' + time + '</option>');

					// increment values
					mins = mins + 15;
					if(mins >= 60){
						mins = 0;
						hour++;
						if(hour == 12){
							ampm = 'pm';
						}
					}
					if(hour > 12){
						hour = 1;
					}

					// determine if we should stop looping
					if(ampm == 'pm' && mins == 15 && hour == endHour){
						looping = false;
					}
				}

				// limit the option sets
				var endOptions = options.slice(0);
				endOptions = endOptions.slice(1);
				var startOptions = options.slice(0);
				startOptions = startOptions.slice(0,-4);

				if(lastRowOnly){
					// populate the start times
					$('#webinarSeries table tbody tr:last-of-type [name="start_time[]"]').html('');
					$.each(startOptions, function(index,value){
						$('#webinarSeries table tbody tr:last-of-type [name="start_time[]"]').append(value);
					});

					// populate the end times
					$('#webinarSeries table tbody tr:last-of-type [name="end_time[]"]').html('');
					$.each(endOptions, function(index,value){
						$('#webinarSeries table tbody tr:last-of-type [name="end_time[]"]').append(value);
					});
				}else{
					// populate the start times
					$('[name="start_time[]"]').html('');
					$.each(startOptions, function(index,value){
						$('[name="start_time[]"]').append(value);
					});

					// populate the end times
					$('[name="end_time[]"]').html('');
					$.each(endOptions, function(index,value){
						$('[name="end_time[]"]').append(value);
					});
				}
			}
			$('#jform_time_zone').change(function(){
				populateTimes();
			});
			populateTimes();

			$('#com_servicesServiceForm form').submit(function(event){
				var errors = [];

				// reset all error indicators
				ta2ta.bootstrapHelper.removeAlert($('#alertWrapper'));

				// title
				if(!ta2ta.validate.hasValue($('#jform_title'), 1)){
					errors.push('Please enter the title of your webinar.');
				}else{
					if(!ta2ta.validate.maxLength($('#jform_title'),150)){
						errors.push('Please limit your title to 150 characters.');
					}
				}

				// description
				if(!ta2ta.validate.hasValue($('#jform_description'), 1)){
					errors.push('Please enter a description for your webinar.');
				}else{
					if(!ta2ta.validate.maxLength($('#jform_description'),500)){
						errors.push('Please limit your description to 500 characters.');
					}
				}

				// num_participants
				if(!ta2ta.validate.hasValue($('#jform_num_participants'), 1)){
					errors.push('Please enter the estimated number of participants in your webinar.');
				}else{
					if(!ta2ta.validate.maxLength($('#jform_num_participants'),6)){
						errors.push('Please call us to request a webinar over 999,999 participants.');
					}
				}

				// comments
				if(!ta2ta.validate.maxLength($('#jform_comments'), 500)){
	        ta2ta.bootstrapHelper.showValidationState($('#jform_comments'), 'error', true);
	        errors.push('Please limit your comments to 500 characters.');
				}

				// if there are erros, stop
				if(errors.length > 0){
					// prevent default posting of form
		    	event.preventDefault();

		    	// show the error message
		    	if(errors.length == 1){
		    		ta2ta.bootstrapHelper.showAlert($('#alertWrapper'), errors[0], 'warning');
		    	}else{
		    		message = 'To submit your request, correct the following errors:<ul>';
		    		$.each(errors,function(index,value){
		    			message += '<li>' + value + '</li>';
		    		});
		    		message += '</ul>';
		    		ta2ta.bootstrapHelper.showAlert($('#alertWrapper'), message, 'warning');
		    	}
					return;
				}
			});

			// comments
			$('#jform_comments').change(function(){
				if(!ta2ta.validate.maxLength($(this),500)){
	        ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
	      }
			});

			// description
			$('#jform_description').change(function(){
				ta2ta.validate.hasValue($(this),1);
				if(!ta2ta.validate.maxLength($(this),500)){
	        ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
	      }
			});

			// num_participants
			$('#jform_num_participants').change(function(){
				ta2ta.validate.hasValue($(this),1);
				if(!ta2ta.validate.maxLength($(this),6)){
	        ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
	      }
			});

			// title
			$('#jform_title').change(function(){
				ta2ta.validate.hasValue($(this),1);
				if(!ta2ta.validate.maxLength($(this),150)){
	        ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
	      }
			});

			// listen for clicks on the webinar series button
			$('#webinarSeries button').click(function(event){
				event.preventDefault();
				addWebinarSeries();
			});

			// toggle fields for series webinars
			$('#jform_series').change(function(){
				toggleSeriesFields();
			});

			// on load, toggle the series fields
			toggleSeriesFields();

			/**
			 * Adds a row to the webinar series table
			 */
			function addWebinarSeries(){
				$('#webinarSeries tbody').append(tableRow);
				initDatepickers();
				populateTimes(true);
			}

			/**
			 * Initialize the datepickers
			 */
			var nowTemp = new Date();
			var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
			function initDatepickers(){
				$('[name="date[]"]').datepicker({
					autoclose: true,
					format: 'mm-dd-yyyy',
					startDate: now,
					todayHighlight: true
				}).data('datepicker');
			}

			/**
			 * Toggles the view of the date, start time, end time, and subtitle fields
			 * as is required for series webinars versus single webinars
			 */
			function toggleSeriesFields(){
				if($('#jform_series1').is(':checked')){
					// if this is the first time we are loading, add a new row so there are two by default
					if($('#webinarSeries tbody tr').length == 1){
						addWebinarSeries();
					}

					// load webinar dates, if any
					if(webinars.length > 0){
						for(i = 0; i < webinars.length; i++){
							// check if we need to add a new row
							if(i > 1){
								addWebinarSeries();
							}

							// add the data for this webinar to the table
							var row = $('#webinarSeries tbody tr').eq(i);
							row.find('input[name="date[]"]').val(webinars[i][0]);
							row.find('input[name="start_time[]"]').val(webinars[i][1]);
							row.find('input[name="end_time[]"]').val(webinars[i][2]);
							row.find('input[name="sub_title[]"]').val(webinars[i][3]);
						}
					}

					// grab the single webinar data, if any, and replace it in the table
					var single = $('#singleWebinar');
					var single_date = single.find('input[name="date[]"]').val();
					var single_start = single.find('input[name="start_time[]"]').val();
					var single_end = single.find('input[name="end_time[]"]').val();

					var first_of_series = $('#webinarSeries tbody tr').eq(0);
					first_of_series.find('input[name="date[]"]').val(single_date);
					first_of_series.find('input[name="start_time[]"]').val(single_start);
					first_of_series.find('input[name="end_time[]"]').val(single_end);

					// this is a series
					$('#singleWebinar').hide();
					$('#webinarSeries').show();
				}else{
					// this is a single webinar
					$('#singleWebinar').show();
					$('#webinarSeries').hide();
				}
				initDatepickers();
			}
		});
	</script>
</div>