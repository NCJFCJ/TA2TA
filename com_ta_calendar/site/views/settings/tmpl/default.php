<?php
/**
 * @version     1.3.0
 * @package     com_ta_calendar
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */
 
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
/*
 * REMINDER: The filter arrays store the values of UNCHECKED or unselected filter items.
 * While this may seem a bit backwards, it is important for scalability. For instance, if a
 * new event type is introduced at a later date, no changes must be made to the filters; however,
 * if the filters stored the ids of checked items, the new event type would need to be added to
 * every existing user record.
 */
?>
<script type="text/javascript">
	// document ready
	jQuery(function($){
		/**
		 * Checks all checkboxes within the same fieldset on click
		 */
		$('.checkAll').click(function(){
			$(this).closest('fieldset').find(':checkbox').prop('checked', true);
			$(this).closest('fieldset').find(':checkbox').change();
			loadCalendar();
		});

		/**
		 * Unchecks all checkboxes within the same fieldset on click
		 */
		$('.uncheckAll').click(function(){
			$(this).closest('fieldset').find(':checkbox').removeAttr('checked');
			$(this).closest('fieldset').find(':checkbox').change();
			loadCalendar();
		});
		
		// checkbox listener to populate hidden field
		$('.filters input:checkbox').change(function(){
			// get the value of the hidden field
			var hidden = $(this).closest('.row').siblings('input:hidden');
			var unchecked = hidden.val();
			if(unchecked == ''){
				unchecked = new Array();
			}else{
				unchecked = unchecked.split(',');
			}
			
			// check checkbox state
			if($(this).is(':checked')){
				// remove from array
				var checkValue = $(this).val();
				unchecked = jQuery.grep(unchecked, function(value){
					return value != checkValue;
				});
 			}else{
				// add to array
				unchecked.push($(this).val());
			}
			
			// put the array back in the text field
			hidden.val(unchecked.join(','));
		});
	});
</script>
<div class="calendar-settings-edit">
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<div class="page-header">
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	</div>
	<?php endif; ?>
	<p>This page allows you to manage how the Event Calendar will display events each time you visit it. These changes only affect your user, and any selections you make here can be overridden within the calendar itself.</p>
	<form id="form-event" action="<?php echo JRoute::_('index.php?option=com_ta_calendar&task=settings.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data" role="form">
		<div class="form-group" style="margin-top: 25px;">
			<label class="control-label" for="jform_timezone"><b>Timezone</b></label>
			<div class="input-group">
				<span class="input-group-addon icomoon-clock"></span>
				<select id="jform_timezone" name="jform[timezone]" class="input-lg">
					<?php foreach($this->timezones as $timezone): ?>
						<option value="<?php echo $timezone->abbr; ?>"<?php echo ($this->userSettings->timezone == $timezone->abbr ? ' selected' : ''); ?>><?php echo str_replace('_', ' ', $timezone->description); ?> (<?php echo $timezone->abbr; ?>)</option>
					<?php endforeach; ?>
				</select>
			</div>		
		</div><!--
		<div class="control-group" style="margin-top: 15px;">
			<label class="control-label" for="jform_view"><b>Default View</b></label>
			<div class="controls">
				<fieldset id="jform_view" class="radio btn-group" style="padding-left: 0">
					<input type="radio" id="jform_view0" name="jform[view]" value="month" <?php echo ($this->userSettings->view == 'month' ? ' checked' : ''); ?>>
					<label class="btn btn-large" for="jform_view0" class="btn-success">Month</label>
					<input type="radio" id="jform_view1" name="jform[view]" value="week" <?php echo ($this->userSettings->view == 'week' ? ' checked' : ''); ?>>
					<label class="btn btn-large" for="jform_view1">Week</label>
					<input type="radio" id="jform_view2" name="jform[view]" value="list" <?php echo ($this->userSettings->view == 'list' ? ' checked' : ''); ?>>
					<label class="btn btn-large" for="jform_view2">List</label>
				</fieldset>
			</div>
		</div>
		<br>
		<br>
		-->
		<input type="hidden" name="jform[view]" value="month" />
		<h2>Default Filters</h2>
		<p>In this section, you can select the filters you would like loaded by default each time the calendar is opened. You can still adjust the filters on the calendar itself.</p>
		<div class="form-group">
			<fieldset class="filters">
				<legend>Topic Areas</legend>
				<p><small>Select:  <a class="checkAll">All</a> / <a class="uncheckAll">None</a></small></p>
				<input type="hidden" name="jform[filters][topicAreas]" value="<?php echo implode(',',$this->userSettings->filters->topicAreas); ?>">
				<div class="row">
					<div class="col-sm-6">
						<?php $records_in_column = ceil(count($this->topicAreas) / 2);
							  for($i = 0; $i < count($this->topicAreas); $i++): ?>
							<label class="checkbox">
								<input type="checkbox" name="topicAreas[]" value="<?php echo $this->topicAreas[$i]->id; ?>"<?php echo (in_array($this->topicAreas[$i]->id, $this->userSettings->filters->topicAreas) ? '' : ' checked'); ?>> <?php echo $this->topicAreas[$i]->name; ?>
							</label>
						<?php 
						if($i + 1 == $records_in_column){
							echo '</div><div class="col-sm-6">';
						}
						endfor; ?>
					</div>
				</div>
			</fieldset>
		</div>
		<br>
		<div class="form-group">
			<fieldset class="filters">
				<legend>Grant Programs</legend>
				<p><small>Select:  <a class="checkAll">All</a> / <a class="uncheckAll">None</a></small></p>
				<input type="hidden" name="jform[filters][grantPrograms]" value="<?php echo implode(',',$this->userSettings->filters->grantPrograms); ?>">
				<div class="row">
					<div class="col-sm-6">
						<?php $records_in_column = ceil(count($this->grantPrograms) / 2);
							  for($i = 0; $i < count($this->grantPrograms); $i++): ?>
							<label class="checkbox">
								<input type="checkbox" name="grantPrograms[]" value="<?php echo $this->grantPrograms[$i]->id; ?>"<?php echo (in_array($this->grantPrograms[$i]->id, $this->userSettings->filters->grantPrograms) ? '' : ' checked'); ?>> <?php echo $this->grantPrograms[$i]->name; ?>
							</label>
						<?php 
						if($i + 1 == $records_in_column){
							echo '</div><div class="col-sm-6">';
						}
						endfor; ?>
					</div>
				</div>
			</fieldset>
		</div>
		<br>
		<div class="form-group">
			<fieldset class="filters">
				<legend>Target Audiences</legend>
				<p><small>Select:  <a class="checkAll">All</a> / <a class="uncheckAll">None</a></small></p>
				<input type="hidden" name="jform[filters][targetAudiences]" value="<?php echo implode(',',$this->userSettings->filters->targetAudiences); ?>">
				<div class="row">
					<div class="col-sm-6">
						<?php $records_in_column = ceil(count($this->targetAudiences) / 2);
							for($i = 0; $i < count($this->targetAudiences); $i++): ?>
							<label class="checkbox">
								<input type="checkbox" name="targetAudiences[]" value="<?php echo $this->targetAudiences[$i]->id; ?>"<?php echo (in_array($this->targetAudiences[$i]->id, $this->userSettings->filters->targetAudiences) ? '' : ' checked'); ?>> <?php echo $this->targetAudiences[$i]->name; ?>
							</label>
						<?php 
						if($i + 1 == $records_in_column){
							echo '</div><div class="col-sm-6">';
						}
						endfor; ?>
					</div>
				</div>
			</fieldset>
		</div>
		<br>
		<div class="form-group">
			<fieldset class="filters">
				<legend>Event Types</legend>
				<p><small>Select:  <a class="checkAll">All</a> / <a class="uncheckAll">None</a></small></p>
				<input type="hidden" name="jform[filters][eventTypes]" value="<?php echo implode(',',$this->userSettings->filters->eventTypes); ?>">
				<div class="row">
					<div class="col-sm-6">
						<?php $records_in_column = ceil(count($this->eventTypes) / 2);
						for($i = 0; $i < count($this->eventTypes); $i++): ?>
							<label class="checkbox">
								<input type="checkbox" name="eventTypes[]" value="<?php echo $this->eventTypes[$i]->id; ?>"<?php echo (in_array($this->eventTypes[$i]->id, $this->userSettings->filters->eventTypes) ? '' : ' checked'); ?>> <?php echo $this->eventTypes[$i]->name; ?>
							</label>
						<?php 
						if($i + 1 == $records_in_column){
							echo '</div><div class="col-sm-6">';
						}
						endfor; ?>
					</div>
				</div>
			</fieldset>
		</div>
		<br>
		<div class="row">
			<div class="span12 form-actions">
				<button type="submit" class="btn btn-primary btn-lg validate"><span class="icomoon-disk"></span> Save Changes</button>
				<button type="reset" class="btn btn-default btn-lg"><span class="icomoon-undo"></span> Start Over</button>
				<input type="hidden" name="option" value="com_ta_calendar" />
	   			<input type="hidden" name="task" value="settings.save" />
	    		<?php echo JHtml::_('form.token'); ?>
			</div>
		</div>		
	</form>
</div>