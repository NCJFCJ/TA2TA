<?php
/**
 * @package     com_services
 * @copyright   Copyright (C) 2016 NCJFCJ. All rights reserved.
 * @author      Zadra Design - http://zadradesign.com
 */
 
// no direct access
defined('_JEXEC') or die;

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
				<?php echo $this->form->getLabel('topic'); ?>
				<?php echo $this->form->getInput('topic'); ?>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('project'); ?>
				<?php echo $this->form->getInput('project'); ?>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('suggested_dates'); ?>
				<?php echo $this->form->getInput('suggested_dates'); ?>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('proposed_locations'); ?>
				<?php echo $this->form->getInput('proposed_locations'); ?>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('description'); ?>
				<?php echo $this->form->getInput('description'); ?>
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
				<?php echo $this->form->getLabel('topic_areas'); ?>
				<?php echo $this->form->getInput('topic_areas'); ?>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('is_partner'); ?>
				<?php echo $this->form->getInput('is_partner'); ?>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('benefit'); ?>
				<?php echo $this->form->getInput('benefit'); ?>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('how_advance'); ?>
				<?php echo $this->form->getInput('how_advance'); ?>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('goals'); ?>
				<?php echo $this->form->getInput('goals'); ?>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('resources_needed'); ?>
				<?php echo $this->form->getInput('resources_needed'); ?>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('resources_provided'); ?>
				<?php echo $this->form->getInput('resources_provided'); ?>
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
						<input type="hidden" name="task" value="roundtable.save" />
				    <button type="reset" class="btn btn-default btn-lg">Clear Form</button>
				    <button type="submit" class="btn btn-lg btn-primary">Complete Request</button>
			    </div>
			</div>
		</div>
	</form>
	<script type="text/javascript">
		jQuery(function($){
			$('#com_servicesServiceForm form').submit(function(event){
				var errors = [];

				// reset all error indicators
				ta2ta.bootstrapHelper.removeAlert($('#alertWrapper'));

				// topic
				if(!ta2ta.validate.hasValue($('#jform_topic'), 1)){
					errors.push('Please enter the topic of your roundtable.');
				}else{
					if(!ta2ta.validate.maxLength($('#jform_topic'),255)){
						errors.push('Please limit your topic to 255 characters.');
					}
				}

				// suggested_dates
				if(!ta2ta.validate.hasValue($('#jform_suggested_dates'), 1)){
					errors.push('Please enter your suggested roundtable dates.');
				}else{
					if(!ta2ta.validate.maxLength($('#jform_suggested_dates'),255)){
						errors.push('Please limit your suggested dates to 255 characters.');
					}
				}

				// proposed_locations
				if(!ta2ta.validate.hasValue($('#jform_proposed_locations'), 1)){
					errors.push('Please enter at least one proposed location for your roundtable.');
				}else{
					if(!ta2ta.validate.maxLength($('#jform_proposed_locations'),500)){
						errors.push('Please limit your proposed locations to 500 characters.');
					}
				}

				// description
				if(!ta2ta.validate.hasValue($('#jform_description'), 1)){
					errors.push('Please enter the description for your roundtable.');
				}else{
					if(!ta2ta.validate.maxLength($('#jform_description'),500)){
						errors.push('Please limit your description to 500 characters.');
					}
				}

				// num_participants
				if(!ta2ta.validate.hasValue($('#jform_num_participants'), 1)){
					errors.push('Please enter the estimated number of participants in your roundtable.');
				}else{
					if(!ta2ta.validate.maxLength($('#jform_num_participants'),6)){
						errors.push('Please call us to request a roundtable over 999,999 participants.');
					}
				}

				// topic_areas
				if(!ta2ta.validate.checkboxes($('[name="jform[topic_areas][]"]'), 1)){
	        ta2ta.bootstrapHelper.showValidationState($('#jform_topic_areas-lbl'), 'error', true);
	        errors.push('You must select at least one topic area that applies to your roundtable.');
				}
				
				// is_partner
				if(!ta2ta.validate.hasValue($('#jform_is_partner'), 1)){
					errors.push('Please describe any TA projects you are a partner on that is focused on a similar topic.');
				}else{
					if(!ta2ta.validate.maxLength($('#jform_is_partner'),500)){
						errors.push('Please limit your description of TA projects on which you are a partner to 500 characters.');
					}
				}

				// benefit
				if(!ta2ta.validate.hasValue($('#jform_benefit'), 1)){
					errors.push('Please describe how this roundtable will benefit the domestic violence, dating violence, sexual assault, and stalking fields.');
				}else{
					if(!ta2ta.validate.maxLength($('#jform_benefit'),500)){
						errors.push('Please limit your description of how this rountable will benefit the domestic violence, etc. fields to 500 characters.');
					}
				}

				// how_advance
				if(!ta2ta.validate.hasValue($('#jform_how_advance'), 1)){
					errors.push('Please describe how this roundtable will advance the mission of OVW.');
				}else{
					if(!ta2ta.validate.maxLength($('#jform_how_advance'),500)){
						errors.push('Please limit your description of how this rountable will advance the mission of OVW to 500 characters.');
					}
				}

				// goals
				if(!ta2ta.validate.hasValue($('#jform_goals'), 1)){
					errors.push('Please describe the goals of this roundtable.');
				}else{
					if(!ta2ta.validate.maxLength($('#jform_goals'),500)){
						errors.push('Please limit your description of the roundtable goals to 500 characters.');
					}
				}

				// resources_needed
				if(!ta2ta.validate.hasValue($('#jform_resources_needed'), 1)){
					errors.push('Please describe the resources you are requesting from NCJFCJ.');
				}else{
					if(!ta2ta.validate.maxLength($('#jform_resources_needed'),500)){
						errors.push('Please limit your description of the resources you are requesting from NCJFCJ to 500 characters.');
					}
				}

				// resources_provided
				if(!ta2ta.validate.hasValue($('#jform_resources_provided'), 1)){
					errors.push('Please describe the resources you are contributing to this roundtable.');
				}else{
					if(!ta2ta.validate.maxLength($('#jform_resources_provided'),500)){
						errors.push('Please limit your description of the resources you are contributing to this roundtable to 500 characters.');
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
				}
			});

			// benefit
			$('#jform_benefit').change(function(){
				ta2ta.validate.hasValue($(this),1);
				if(!ta2ta.validate.maxLength($(this),500)){
	        ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
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

			// goals
			$('#jform_goals').change(function(){
				ta2ta.validate.hasValue($(this),1);
				if(!ta2ta.validate.maxLength($(this),500)){
	        ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
	      }
			});

			// how_advance
			$('#jform_how_advance').change(function(){
				ta2ta.validate.hasValue($(this),1);
				if(!ta2ta.validate.maxLength($(this),500)){
	        ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
	      }
			});

			// is_partner
			$('#jform_is_partner').change(function(){
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

			// proposed_locations
			$('#jform_proposed_locations').change(function(){
				ta2ta.validate.hasValue($(this),1);
				if(!ta2ta.validate.maxLength($(this),500)){
	        ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
	      }
			});

			// resources_needed
			$('#jform_resources_needed').change(function(){
				ta2ta.validate.hasValue($(this),1);
				if(!ta2ta.validate.maxLength($(this),500)){
	        ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
	      }
			});

			// resources_provided
			$('#jform_resources_provided').change(function(){
				ta2ta.validate.hasValue($(this),1);
				if(!ta2ta.validate.maxLength($(this),500)){
	        ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
	      }
			});

			// suggested_dates
			$('#jform_suggested_dates').change(function(){
				ta2ta.validate.hasValue($(this),1);
				if(!ta2ta.validate.maxLength($(this),255)){
	        ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
	      }
			});

			// topic
			$('#jform_topic').change(function(){
				ta2ta.validate.hasValue($(this),1);
				if(!ta2ta.validate.maxLength($(this),255)){
	        ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
	      }
			});
		});
	</script>
</div>