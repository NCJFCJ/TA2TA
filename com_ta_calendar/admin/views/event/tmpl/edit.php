<?php
/**
 * @package     com_ta_calendar
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_ta_calendar/assets/css/ta_calendar.css');

// https?
$https = false;
if(!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]!=="off"){
	$https = true;
}
?>
<script type="text/javascript">
	var selectedProject = <?php echo ($this->item->provider_project > 0 ? $this->item->provider_project : 0); ?>;

  js = jQuery.noConflict();
  js(document).ready(function($){
	$('input:hidden.type').each(function(){
		var name = $(this).attr('name');
		if(name.indexOf('typehidden')){
			$('#jform_type option[value="' + $(this).val() + '"]').attr('selected',true);
		}
	});
	$("#jform_type").trigger("liszt:updated");

	/**
	 * Checks all checkboxes within the same fieldset on click
	 */
	$('.checkAll').click(function(){
		$(this).closest('.control-group').find(':checkbox').prop('checked', true);
		loadCalendar();
	});

	/**
	 * Unchecks all checkboxes within the same fieldset on click
	 */
	$('.uncheckAll').click(function(){
		$(this).closest('.control-group').find(':checkbox').removeAttr('checked');
		loadCalendar();
	});

	/**
	 * Toggle the Registration URL field based on the value of the open field
	 */
	function toggleRegistrationURLField(){
		var registrationURLwrapper = $('#jform_registration_url').parent().parent();
		if($('#jform_open0').is(':checked')){
			registrationURLwrapper.show();
		}else{
			registrationURLwrapper.hide();
		}
	}

	// listen for changes to open
	$('#jform_open label').click(toggleRegistrationURLField);

	/**
	 * Populate the grant program checkboxes
	 */

	function populateGrantPrograms(){
		var project = $('#jform_provider_project').val();
		
		// only proceed if a project was selected
		if(project > 0){
			// close the notice
			$('#grantProgramNotice').hide();

			// make AJAX request
			var request = $.ajax({
				data: {'project':project},
				dataType: 'json',
				type: 'POST',
				url: '<?php echo 'http' . ($https ? 's' : '') . '://'. $_SERVER['HTTP_HOST'] . '/administrator/index.php?option=com_ta_calendar&task=getPrograms';?>'
			});

			// fires when the AJAX call completes
			request.done(function(response, textStatus, jqXHR){
				// check if this has an error
				if(response.status == 'success'){
					// check if any selections were made previously
					if($("input[name='jform[grant_programs][]']:checked").length){
						// show the notice
						$('#grantProgramNotice').show();
					}
					
					// uncheck all programs
					$("input[name='jform[grant_programs][]']").prop('checked', false);

					// check the corresponding programs
					$.each(response.data, function(index,value){
						$("input[name='jform[grant_programs][]'][value='" + value + "'] ").prop('checked', true);
					});					
				}else{
					$('#error-message').html('<strong>Error!</strong> ' + response.message);
					$('#error').show();
				}
			});

			// catch if the AJAX call fails completelly
			request.fail(function(jqXHR, textStatus, errorThrown){
				// notify the user that an error occured
				$('#error-message').html('<strong>Error!</strong> AJAX error. Please contact Zachary at 41966.');
				$('#error').show();
			});
		}
	}

	// listen for changes to the project
	$('#jform_provider_project').change(populateGrantPrograms);

	// hide the notice on load
	$('#grantProgramNotice').hide();

	/**
	 * Populate the TA Project field from the database
	 */
	function populateProjectsByOrg(){			
		var org = $('#jform_org').val();

		// hide the error, just incase
		$('#error').hide();

		// remove all options
		$("#jform_provider_project option").remove();

		// add back in the select option
		$("#jform_provider_project").append('<option value="">--Select One--</option>');

		// update Chosen
		$('#jform_provider_project').trigger("liszt:updated");

		// make AJAX request
		var request = $.ajax({
			data: {'org':org},
			dataType: 'json',
			type: 'POST',
			url: '<?php echo 'http' . ($https ? 's' : '') . '://'. $_SERVER['HTTP_HOST'] . '/administrator/index.php?option=com_ta_calendar&task=getProjects';?>'
		});

		// fires when the AJAX call completes
		request.done(function(response, textStatus, jqXHR){
			// check if this has an error
			if(response.status == 'success'){
				// add in relevant options from the response
				$.each(response.data, function(index, value){
					$("#jform_provider_project").append('<option value="' + value.id + '"' + (value.id == selectedProject ? ' selected' : '') + '>' +  value.title + '</option>');
				});

				// update Chosen
				$('#jform_provider_project').trigger("liszt:updated");					
			}else{
				$('#error-message').html('<strong>Error!</strong> ' + response.message);
				$('#error').show();
			}
		});

		// catch if the AJAX call fails completelly
		request.fail(function(jqXHR, textStatus, errorThrown){
			// notify the user that an error occured
			$('#error-message').html('<strong>Error!</strong> AJAX error. Please contact Zachary at 41966.');
			$('#error').show();
		});
	}

	// listen for changes to organization
	$('#jform_org').change(populateProjectsByOrg);

	// run on load
	toggleRegistrationURLField();
	if($('#jform_org').val() > 0){
		populateProjectsByOrg();
	}

	// hide the error
	$('#error').hide();
  });

  Joomla.submitbutton = function(task){
    if(task == 'event.cancel'){
      Joomla.submitform(task, document.getElementById('event-form'));
    }else{ 
      if(task != 'event.cancel' && document.formvalidator.isValid(document.id('event-form'))){
          
          Joomla.submitform(task, document.getElementById('event-form'));
      }else{
          alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
      }
    }
  }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_ta_calendar&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="event-form" class="form-validate">
    <div id="error">
	    <div class="alert alert-error">
	    	<button type="button" class="close" data-dismiss="alert">&times;</button>
	    	<div id="error-message"></div>
	    </div>
    </div>
    <div class="row-fluid">
        <div class="span10 form-horizontal">
            <fieldset class="adminform">
	            <div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('state'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('approved'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('approved'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('org'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('org'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('provider_project'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('provider_project'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('start'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('start'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('end'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('end'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('summary'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('summary'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('type'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('type'); ?></div>
				</div>
	
				<?php
					foreach((array)$this->item->type as $value): 
						if(!is_array($value)):
							echo '<input type="hidden" class="type" name="jform[typehidden]['.$value.']" value="'.$value.'" />';
						endif;
					endforeach;
				?>			<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('event_url'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('event_url'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('open'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('open'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('registration_url'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('registration_url'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('topic_areas'); ?>
						<p><small>Select:  <a class="checkAll">All</a> / <a class="uncheckAll">None</a></small></p>
					</div>
					<div class="controls"><?php echo $this->form->getInput('topic_areas'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('grant_programs'); ?>
						<p><small>Select:  <a class="checkAll">All</a> / <a class="uncheckAll">None</a></small></p>
					</div>
					<div class="controls">
						<div class="alert alert-info alert-dismissable" id="grantProgramNotice">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<strong>Notice</strong> These grant programs have changed to match the TA Project you selected. Please double check them.
						</div>
						<?php echo $this->form->getInput('grant_programs'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('target_audiences'); ?>
						<p><small>Select:  <a class="checkAll">All</a> / <a class="uncheckAll">None</a></small></p>
					</div>
					<div class="controls"><?php echo $this->form->getInput('target_audiences'); ?></div>
				</div>
				<?php if(empty($this->item->created_by)): ?>
					<input type="hidden" name="jform[created_by]" value="<?php echo JFactory::getUser()->id; ?>" />
				<?php else: ?>
					<input type="hidden" name="jform[created_by]" value="<?php echo $this->item->created_by; ?>" />
				<?php endif; ?>
            </fieldset>
        </div>
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>