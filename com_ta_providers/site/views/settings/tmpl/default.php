<?php
/**
 * @package     com_ta_providers
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */
 
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');

// https?
$https = false;
if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'){
  $https = true;
}
?>
<script type="text/javascript">
	// document ready
	jQuery(function($){
		var orgName = '<?php echo $this->item->name; ?>';

		/**
		 * Listen for changes to the logo and upload right away
		 */
		$('#jform_logoFile').fileupload({
      dataType: 'json',
      url: '<?php echo "http" . ($https ? "s" : "") . "://{$_SERVER['HTTP_HOST']}/index.php?option=com_ta_providers&task=fileUpload";?>',
      add: function(e,data){
        // reset all alerts
        ta2ta.bootstrapHelper.removeAlert($('#orgLogoForm'));

        // show the loading image
        var loadingImage = new Image();
        $('#tmpLogoWrapper').html(loadingImage);
        $('#tmpLogoWrapper img').css({
        	'margin': ' 50px 0 0 134px',
        	'width': '80px'
        });

        loadingImage.src = '<?php echo "http" . ($https ? "s" : "") . "://{$_SERVER['HTTP_HOST']}"; ?>/templates/ta2ta/img/loading.gif';
    
				// show the image edit popup
				$('#imageEditPopup').modal('show');	

        //show loading graphic
        data.submit();
      },

      done: function(e,data){
        if(data._response.result.status == 'success'){
          // show the image
          var tmpLogo = new Image();
          tmpLogo.src = '<?php echo "http" . ($https ? "s" : "") . "://{$_SERVER['HTTP_HOST']}"; ?>/media/com_ta_providers/tmp/' + data._response.result.message;
     
          $(tmpLogo).on('load', function(){
	     			$('#tmpLogoWrapper').html(tmpLogo);

	     			// start the editor
	     			$('#tmpLogoWrapper img').cropper({
	     				aspectRatio: 45 / 29,
	     				crop: function(e){
	          		// load the coordinates and dimmensions into the form to be sent to PHP
	          		$('#jform_logoX').val(e.x);
						    $('#jform_logoY').val(e.y);
						    $('#jform_logoWidth').val(e.width);
						    $('#jform_logoHeight').val(e.height);
	     				},
	     				data: {
	     					height: 280,
	     					width: 450,
	     					x: 0,
	     					y: 0
	     				},
	     				preview: '#logoImg',
	     				rotatable: false,
	     				scalable: false,
	     				zoomable: false
	     			});
	     		});
        }else{
          // an error occured
          ta2ta.bootstrapHelper.showAlert($('#orgLogoForm'), data._response.result.message);
            	
        	// hide the image edit popup
					$('#imageEditPopup').modal('hide');	
        }
      },
      fail: function(e,data){
      	ta2ta.bootstrapHelper.showAlert($('#orgLogoForm'), 'An AJAX error occured. Please try again.', 'error');
        	
      	// hide the image edit popup
				$('#imageEditPopup').modal('hide');	
      }
    });

		/**
		 * Listen for the user to change the organization name, prompt for confirmation
		 */
		$('#jform_name').change(function(){
			// show the name change popup
			$('#nameChangePopup').modal('show');	
		});

		/**
		 * Listen for clicks on the 'yes' button of the name change popup
		 */
		$('#nameChangePopup .btn-primary').click(function(){
			// hide the popup
			$('#nameChangePopup').modal('hide');

			// set the new name as the current orgName
			orgName = $('#jform_name').val();
		});

		/**
		 * Listen for clicks on the 'no' button of the name change popup
		 */
		$('#nameChangePopup .btn-default').click(function(){
			// hide the popup
			$('#nameChangePopup').modal('hide');

			// reset the organization name to its previous value
			$('#jform_name').val(orgName);
		});

		/**
		 * Validate the form on submit
		 */
		 $('#orgSettingsForm').submit(function(event){
		 	var errors = [];
			var parent = $(this).parent();

			// remove old errors
			ta2ta.bootstrapHelper.removeAlert(parent);
			ta2ta.bootstrapHelper.hideAllValidationStates();

			// validate name
			if(!ta2ta.validate.hasValue($('#jform_name'), 3)){
				errors.push('<?php echo JText::_('COM_TA_PROVIDERS_SETTINGS_ORGANIZATION_NAME_REQUIRED'); ?>');
			}else{
				if(!ta2ta.validate.maxLength($('#jform_name'),150,3)){
					errors.push('<?php echo JText::_('COM_TA_PROVIDERS_SETTINGS_ORGANIZATION_NAME_TOO_LONG'); ?>');
				}
				if(!ta2ta.validate.minLength($('#jform_name'),4,3)){
					errors.push('<?php echo JText::_('COM_TA_PROVIDERS_SETTINGS_ORGANIZATION_NAME_TOO_SHORT'); ?>');
				}
				if(!ta2ta.validate.title($('#jform_name'), 3)){
					errors.push('<?php echo JText::_('COM_TA_PROVIDERS_SETTINGS_ORGANIZATION_NAME_INVALID'); ?>');
				}
			}

			// validate website
			if(ta2ta.validate.hasValue($('#jform_website'))){
				if(!ta2ta.validate.maxLength($('#jform_website'),255,3)){
					errors.push('<?php echo JText::_('COM_TA_PROVIDERS_SETTINGS_ORGANIZATION_WEBSITE_TOO_LONG'); ?>');
				}
				if(!ta2ta.validate.minLength($('#jform_website'),7,3)){
					errors.push('<?php echo JText::_('COM_TA_PROVIDERS_SETTINGS_ORGANIZATION_WEBSITE_TOO_SHORT'); ?>');
				}
				if(!ta2ta.validate.url($('#jform_website'),3)){
					errors.push('<?php echo JText::_('COM_TA_PROVIDERS_SETTINGS_ORGANIZATION_WEBSITE_INVALID'); ?>');
				}
			}

			// if an error occured, show the message and stop submission
			if(errors.length){
				ta2ta.bootstrapHelper.showAlert(parent, ta2ta.bootstrapHelper.constructErrorMessage(errors), 'warning', true, true);
				event.preventDefault();
			}
		 });

		// check name on change
		$('#jform_name').change(function(){
			if(ta2ta.validate.maxLength($(this),150,3)){
				if(ta2ta.validate.minLength($(this),4,3)){
					ta2ta.validate.title($(this),3);
				}
			}
		});

		// check website on change
		$('#jform_website').change(function(){
			if(ta2ta.validate.hasValue($(this))){
				if(ta2ta.validate.maxLength($(this),255,3)){
					if(ta2ta.validate.minLength($(this),7,3)){
						ta2ta.validate.url($(this),3);
					}
				}
			}
		});
	});

	/**
	 * Open the file upload prompt
	 */
	function chooseFile(){
		jQuery('#jform_logoFile').click();
	}
</script>
<div class="organization-settings-edit">
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<div class="page-header">
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	</div>
	<?php endif; ?>
	<p><?php echo JText::_('COM_TA_PROVIDERS_SETTINGS_INTRO'); ?></p>
	<form id="orgLogoForm" action="<?php echo "http" . ($https ? "s" : "") . "://{$_SERVER['HTTP_HOST']}/index.php?option=com_ta_providers&task=fileUpload";?>" method="post" class="form-validate big-inputs" enctype="multipart/form-data" role="form" style="overflow: hidden !important;">
		<div class="row">
			<div class="col-sm-6">
				<div class="form-group">
					<div class="input-group">
						<div style="height: 0px; overflow: hidden;">
							<input
								type="file"
								name="jform[logoFile]"
								id="jform_logoFile"
								/>
						</div>
						<?php
						$logoSrc = "/media/com_ta_providers/logos/no-logo.jpg";
						if(!empty($this->item->logo)){
              $imgPath = '/media/com_ta_providers/logos/' . $this->item->logo;
              if(file_exists(JPATH_SITE . $imgPath)){
                // show the image
                $logoSrc =  $imgPath;
              }
            }
            ?>
            <div id="logoImg" style="height: 140px; overflow: hidden; width: 225px;">
							<img onclick="chooseFile();" src="<?php echo $logoSrc; ?>" alt="<?php echo $this->item->name; ?> Logo" style="max-height: 100%; max-width: 100%;">
						</div>
						<small><?php echo JText::_('COM_TA_PROVIDERS_SETTINGS_CHANGE_IMAGE'); ?></small>
					</div>
				</div>
			</div>
		</div>
	</form>
	<form id="orgSettingsForm" action="<?php echo JRoute::_('index.php?option=com_ta_providers&task=settings.save'); ?>" method="post" class="form-validate big-inputs" enctype="multipart/form-data" role="form">
		<div class="row">
			<div class="col-sm-6">		
				<div class="form-group">
					<div class="input-group">
						<input 
							type="hidden"
							name="jform[logo]"
							id="jform_logo"
							value="<?php echo $this->item->logo; ?>"
							/>
						<input 
							type="hidden"
							name="jform[logoX]"
							id="jform_logoX"
							value=""
							/>
						<input 
							type="hidden"
							name="jform[logoY]"
							id="jform_logoY"
							value=""
							/>
						<input 
							type="hidden"
							name="jform[logoWidth]"
							id="jform_logoWidth"
							value=""
							/>
						<input 
							type="hidden"
							name="jform[logoHeight]"
							id="jform_logoHeight"
							value=""
							/>
						<span 
							class="input-group-addon has-tooltip icomoon-office"
							data-original-title="<?php echo JText::_('COM_TA_PROVIDERS_SETTINGS_ORGANIZATION_NAME'); ?>"
							data-placement="top"
							data-toggle="tooltip">
						</span>
						<input 
							type="text" 
							name="jform[name]"
							id="jform_name"
							value="<?php echo $this->item->name; ?>"
							placeholder="<?php echo JText::_('COM_TA_PROVIDERS_SETTINGS_ORGANIZATION_NAME'); ?>"
							class="form-control required"
							aria-invalid="false"
							required="true"
							aria-required="true"
							/>
					</div>
				</div>
				<div class="form-group">
					<div class="input-group">
						<span 
							class="input-group-addon has-tooltip icomoon-earth"
							data-original-title="<?php echo JText::_('COM_TA_PROVIDERS_SETTINGS_ORGANIZATION_WEBSITE'); ?>"
							data-placement="top"
							data-toggle="tooltip">
						</span>
						<input 
							type="text" 
							name="jform[website]"
							id="jform_website"
							value="<?php echo $this->item->website; ?>"
							placeholder="<?php echo JText::_('COM_TA_PROVIDERS_SETTINGS_ORGANIZATION_WEBSITE') . JText::_('COM_TA_PROVIDERS_SETTINGS_OPTIONAL'); ?>"
							class="form-control"
							aria-invalid="false"
							/>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="span12 form-actions">
						<button type="submit" class="btn btn-primary btn-lg validate"><span class="icomoon-disk"></span> Save Changes</button>
						<button type="reset" class="btn btn-default btn-lg"><span class="icomoon-undo"></span> Start Over</button>
						<input type="hidden" name="option" value="com_ta_providers" />
			   			<input type="hidden" name="task" value="settings.save" />
			    		<?php echo JHtml::_('form.token'); ?>
					</div>
				</div>	
			</div>
		</div>	
	</form>
</div>
<div class="modal fade" id="nameChangePopup" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true" aria-label="Close">&times;</button>
				<h4 class="modal-title"><?php echo JText::_('COM_TA_PROVIDERS_SETTINGS_NAME_CHANGE_CONFIRMATION_HEADING'); ?></h4>
			</div>
			<div class="modal-body">
				<p><?php echo JText::_('COM_TA_PROVIDERS_SETTINGS_ORGANIZATION_NAME_WARNING'); ?></p>
			</div>
			<div class="modal-footer">
				<a class="btn btn-primary"><span class="icomoon-checkmark"></span> <?php echo JText::_('COM_TA_PROVIDERS_HAVE_APPROVAL'); ?></a>&nbsp;
				<a class="btn btn-default" data-dismiss="modal"><span class="icomoon-close"></span> <?php echo JText::_('COM_TA_PROVIDERS_NO'); ?></a>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="imageEditPopup" data-backdrop="static">
	<div class="modal-dialog" style="width: 390px;"><!-- TO DO: Fix this, not responsive!!! 320 max on mobile -->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php echo JText::_('COM_TA_PROVIDERS_EDIT_LOGO_HEADING'); ?></h4>
			</div>
			<div class="modal-body">
				<p><?php echo JText::_('COM_TA_PROVIDERS_EDIT_LOGO_PROMPT'); ?></p>
				<div id="tmpLogoWrapper">
					<img src="" alt="Temporary Image for Editing">
				</div>
			</div>
			<div class="modal-footer">
				<a class="btn btn-primary" data-dismiss="modal"><span class="icomoon-checkmark"></span> <?php echo JText::_('COM_TA_PROVIDERS_DONE'); ?></a>
			</div>
		</div>
	</div>
</div>