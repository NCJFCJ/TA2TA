<?php
/**
 * @version     2.0.0
 * @package     com_library
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */
 
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
	jQuery(function($){
		// remove required flags from everything so our javascript can run
		$('input,textarea,select').removeAttr('aria-required required');

		// validate on form submit
		$('#libraryForm').submit(function(event){
			var errors = [];
			var parent = $(this).parent();

			// remove old errors
			ta2ta.bootstrapHelper.removeAlert(parent);
			ta2ta.bootstrapHelper.hideAllValidationStates();

			// validate name
			if(!ta2ta.validate.hasValue($('#jform_name'))
            || !ta2ta.validate.minLength($('#jform_name'),5)
            || !ta2ta.validate.maxLength($('#jform_name'),150)
            || !ta2ta.validate.title($('#jform_name'))){
                ta2ta.bootstrapHelper.showValidationState($('#jform_name'), 'error', true);
            	errors.push('You must provide a name for this resource that is between 5 and 150 characters in length.');
            }

            // validate description
            if(!ta2ta.validate.hasValue($('#jform_description'))
            || !ta2ta.validate.minLength($('#jform_description'),10)
            || !ta2ta.validate.maxLength($('#jform_description'),1500)){
                ta2ta.bootstrapHelper.showValidationState($('#jform_description'), 'error', true);
            	errors.push('You must enter a description for this resource that is between 10 and 1500 characters in length.');
            }
       
       		// file upload
       		<?php if(empty($this->resource->document_path)): ?>
       		if($('input[type="file"]').val() == ''){
       			errors.push('You must select a PDF file to upload.');
       		}
       		<?php endif; ?>

       		// target audiences
       		if(!$('#resourceAudiences input[type=checkbox]:checked').length){
       			errors.push('You must select at least one target audience.');
       		}

			// if an error occured, show the message and stop submission
			if(errors.length){
				ta2ta.bootstrapHelper.showAlert(parent, ta2ta.bootstrapHelper.constructErrorMessage(errors), 'warning', true, true);
				event.preventDefault();
			}
		});

		// check title on change
		$('#jform_name').change(function(){
            if(!ta2ta.validate.hasValue($(this))
            || !ta2ta.validate.minLength($(this),5)
            || !ta2ta.validate.maxLength($(this),150)
            || !ta2ta.validate.title($(this))){
                ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
            }else{
                ta2ta.bootstrapHelper.showValidationState($(this), 'success', true);
            }
        });

		// description
        $('#jform_description').change(function(){
            if(!ta2ta.validate.hasValue($(this))
            || !ta2ta.validate.minLength($(this),10)
            || !ta2ta.validate.maxLength($(this),1500)){
                ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
            }else{
                ta2ta.bootstrapHelper.showValidationState($(this), 'success', true);
            }
        });
	});
</script>
<div class="page-header">
	<h1><?php $tmpObj = (array)$this->resource; echo (empty($tmpObj) ? 'Add' : 'Edit'); ?> Resource</h1>
</div>
<div id="resourceForm">
	<form action="<?php echo JRoute::_('/my-account/library/edit.html?id=' . (isset($this->resource->id) ? (int) $this->resource->id : 0)); ?>" method="post" enctype="multipart/form-data" class="form-validate big-inputs" id="libraryForm">  
		<?php echo $this->form->getInput('id'); ?>
		<div class="col-sm-12">
			<fieldset>
				<div class="row">
					<div class="col-sm-6">
						<?php if($this->form->getValue('id') == 0):?>
							<input type="hidden" name="jform[state]" value="-1">
						<?php else: ?>
						<div class="form-group">
							<div class="input-group" id="stateOptions">
								<fieldset id="jform_state" class="radio btn-group">
									<?php $state = $this->form->getValue('state'); 
									if($state == -1):?>
									<b>Status: Pending Approval</b>
									<input type="hidden" name="jform[state]" value="-1">
									<?php else: ?>
									<input id="jform_state0" type="radio" value="0" name="jform[state]" <?php if($state == 0){echo 'checked ';} ?>/>
									<label class="btn btn-default" for="jform_state0">Trashed</label>
									<input id="jform_state1" type="radio" value="1" name="jform[state]" <?php if($state == 1){echo 'checked ';} ?>/>
									<label class="btn btn-default" for="jform_state1">Visible on Website</label>
									<?php endif; ?>
								</fieldset>
							</div>
						</div>
						<?php endif; ?>
						<div class="form-group">
							<div class="input-group">
								<span 
									class="input-group-addon has-tooltip icomoon-books"
									data-original-title="<?php echo JText::_('COM_LIBRARY_FORM_NAME_LBL'); ?>"
									data-placement="top"
									data-toggle="tooltip">
								</span>
								<input 
									type="text" 
									name="jform[name]"
									id="jform_name"
									value="<?php echo $this->form->getValue('name'); ?>"
									placeholder="<?php echo JText::_('COM_LIBRARY_FORM_NAME_LBL'); ?>"
									class="form-control required"
									aria-invalid="false"
									required="true"
									aria-required="true"
									/>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12 col-md-10 col-lg-8">
						<div class="form-group">
							<div class="input-group">
								<span
									class="input-group-addon has-tooltip icomoon-file"
									data-original-title="<?php echo JText::_('COM_LIBRARY_FORM_DESCRIPTION_LBL'); ?>"
									data-placement="top"
									data-toggle="tooltip">
								</span>
								<textarea 
									name="jform[description]"
									id="jform_description"
									placeholder="<?php echo JText::_('COM_LIBRARY_FORM_DESCRIPTION_LBL'); ?>"
									class="form-control required"
									rows="6"
									aria-invalid="false"
									required="true"
									aria-required="true"><?php echo $this->form->getValue('description'); ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<p>Please select a PDF file to upload. Note that the cover image shown on the website will be generated from the first page of your PDF.</p>
							<?php 
								echo $this->form->getInput('file');
								if(!empty($this->resource->document_path)){
									echo '<br><br><img src="' . $this->resource->cover_path . '" alt="' . $this->resource->name . '" style="width: 100px;">';
								}
							?>
							<div id="thumbnail"></div>
						</div>
					</div>
				</div>
				<div class="form-group" id="resourceAudiences">
					<h4><?php echo JText::_('COM_LIBRARY_FORM_TARGET_AUDIENCES_LBL'); ?></h4>
					<p><small>Select:  <a class="checkAll">All</a> / <a class="uncheckAll">None</a></small></p>
					<?php echo $this->form->getInput('target_audiences'); ?>
				</div>
			</fieldset>
		</div>
		<input type="hidden" name="MAX_FILE_SIZE" value="26210000" /> 
		<input type="hidden" name="task" value="edit.save" />
		<?php echo JHtml::_('form.token'); ?>
		<div class="col-xs-12 form-actions">
			<button type="submit" class="btn btn-lg btn-primary"><span class="icomoon-disk"></span> Save</button>
			<a href="/my-account/library.html" class="btn btn-lg btn-default"><span class="icomoon-close"></span> Cancel</a>
		</div>
	</form>
</div>