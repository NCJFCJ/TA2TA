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
				<?php echo $this->form->getLabel('project'); ?>
				<?php echo $this->form->getInput('project'); ?>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('suggested_dates'); ?>
				<?php echo $this->form->getInput('suggested_dates'); ?>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('types_of_support'); ?>
				<?php echo $this->form->getInput('types_of_support'); ?>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('materials'); ?> <small>(optional)</small>
				<?php echo $this->form->getInput('materials'); ?>
			</div>
			<div class="form-group">
				<?php echo $this->form->getLabel('comments'); ?> <small>(optional)</small>
				<?php echo $this->form->getInput('comments'); ?>
			</div>
			<div class="form-actions">
				<div class="pull-right">
						<?php echo JHtml::_('form.token'); ?>
						<input type="hidden" name="return_url" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
						<input type="hidden" name="MAX_FILE_SIZE" value="26210000" />
						<input type="hidden" name="option" value="com_services" />
						<input type="hidden" name="task" value="meeting.save" />
				    <button type="reset" class="btn btn-default btn-lg">Clear Form</button>
				    <button type="submit" class="btn btn-lg btn-primary">Complete Request</button>
			    </div>
			</div>
		</div>
	</form>
	<script type="text/javascript">
		jQuery(function($){
			$('#com_servicesServiceForm form').submit(function(event){
				var errors = false;

				// reset all error indicators
				ta2ta.bootstrapHelper.removeAlert($('#alertWrapper'));

				// suggested_dates
				if(!ta2ta.validate.hasValue($('#jform_suggested_dates'), 1)){
					ta2ta.bootstrapHelper.showAlert($('#alertWrapper'), 'Please enter your suggested meeting dates.', 'warning');
					scrollToTop();
					errors = true;
				}else{
					if(!ta2ta.validate.maxLength($('#jform_suggested_dates'),255)){
						ta2ta.bootstrapHelper.showAlert($('#alertWrapper'), 'Please limit your suggested dates to 255 characters.', 'warning');
						scrollToTop();
						errors = true;
					}
				}

				// types_of_support
				if(!ta2ta.validate.checkboxes($('[name="jform[types_of_support][]"]'), 1)){
	        ta2ta.bootstrapHelper.showValidationState($('#jform_types_of_support-lbl'), 'error', true);
					ta2ta.bootstrapHelper.showAlert($('#alertWrapper'), 'You must select at least one type of support you are requesting.', 'warning');
				}
				
				// comments
				if(!ta2ta.validate.maxLength($('#jform_comments'), 500)){
	        ta2ta.bootstrapHelper.showValidationState($('#jform_comments'), 'error', true);
					ta2ta.bootstrapHelper.showAlert($('#alertWrapper'), 'Please limit your comments to 500 characters.', 'warning');
					errors = true;
				}

				// if there are erros, stop
				if(errors){
					// prevent default posting of form
		    	event.preventDefault();
					return;
				}
			});

			// comments
			$('#jform_comments').change(function(){
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
		});
	</script>
</div>