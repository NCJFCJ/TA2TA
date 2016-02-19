<?php
/**
 * @package     com_library
 * @copyright   Copyright (C) 2013 NCJFCJ. All rights reserved.
 * @author      NCJFCJ <zdraper@ncjfcj.org> - http://ncjfcj.org
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
$document->addStyleSheet('components/com_library/assets/css/library.css');

// https?
$https = false;
if(!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]!=="off"){
	$https = true;
}
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task){
        if(task == 'item.cancel'){
            Joomla.submitform(task, document.getElementById('item-form'));
        }else{ 
            if(task != 'item.cancel' && document.formvalidator.isValid(document.id('item-form'))){
                Joomla.submitform(task, document.getElementById('item-form'));
            }else{
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }

  js = jQuery.noConflict();
  js(document).ready(function($){
		var selectedProject = <?php echo ($this->item->project > 0 ? $this->item->project : 0); ?>;
		
		/**
		 * Populate the TA Project field from the database
		 */
		function populateProjectsByOrg(){			
			var org = $('#jform_org').val();

			// hide the error, just incase
			$('#error').hide();

			// remove all options
			$("#jform_project option").remove();

			// add back in the select option
			$("#jform_project").append('<option value="">--Select One--</option>');

			// update Chosen
			$('#jform_project').trigger("liszt:updated");

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
						$("#jform_project").append('<option value="' + value.id + '"' + (value.id == selectedProject ? ' selected' : '') + '>' +  value.title + '</option>');
					});

					// update Chosen
					$('#jform_project').trigger("liszt:updated");					
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
		if($('#jform_org').val() > 0){
			populateProjectsByOrg();
		}

		// hide the error
		$('#error').hide();
	});
</script>
<form action="<?php echo JRoute::_('index.php?option=com_library&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="item-form" class="form-validate">
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
					<div class="control-label"><?php echo $this->form->getLabel('org'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('org'); ?></div>
				</div>
	    	<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('project'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('project'); ?></div>
				</div>
	    	<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
				</div>
	    	<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('description'); ?></div>
				</div>
	    	<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('file'); ?></div>
					<div class="controls"><?php
					echo $this->form->getInput('file');
					if($this->item->id > 0){
						// editing, check if prior file exists
						$filePath = '/media/com_library/resources/' . $this->item->id . '-' . $this->item->base_file_name . '.pdf';
						if(file_exists(JPATH_SITE . $filePath)){
							$imgPath = '/media/com_library/covers/' . $this->item->id . '-' . $this->item->base_file_name . '.png';
							if(file_exists(JPATH_SITE . $imgPath)){
								// show the image
								echo '<br><br><img src="' . $imgPath . '" alt="' . $this->item->name . ' Cover Image">';
							}else{
								// show the placeholder image
								echo '<br><br><img src="/media/com_library/covers/no-cover.jpg" alt="' . $this->item->name . ' Cover Image">';
							}
						}			
					}?>	 
					</div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('target_audiences'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('target_audiences'); ?></div>
				</div>
				<input type="hidden" name="MAX_FILE_SIZE" value="26210000" /> 
				<input type="hidden" name="task" value="" />
				<?php echo JHtml::_('form.token'); ?>
			</fieldset>
		</div>
	</div>
</form>