<?php
/**
 * @package     com_ta_providers
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
$document->addStyleSheet('components/com_ta_providers/assets/css/ta_providers.css');

// add imgareaselect support
$document->addScript('components/com_ta_providers/assets/js/imgareaselect/jquery.imgareaselect.js');
$document->addStyleSheet('components/com_ta_providers/assets/js/imgareaselect/imgareaselect-default.css');
$document->addScript('components/com_ta_providers/assets/js/jqueryfileupload/vendor/jquery.ui.widget.js');
$document->addScript('components/com_ta_providers/assets/js/jqueryfileupload/jquery.fileupload.js');
$document->addScript('components/com_ta_providers/assets/js/jqueryfileupload/jquery.iframe-transport.js');

// https?
$https = false;
if(!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]!=="off"){
    $https = true;
}
?>
<script type="text/javascript">
   Joomla.submitbutton = function(task){
        if(task == 'taprovider.cancel'){
            Joomla.submitform(task, document.getElementById('taprovider-form'));
        }else{
            if(task != 'taprovider.cancel' && document.formvalidator.isValid(document.id('taprovider-form'))){   
                Joomla.submitform(task, document.getElementById('taprovider-form'));
            }else{
                 $('#AJAXMessageContainer').html('<div class="alert alert-error"><strong>Error!</strong> <?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?></div>');
            }
        }
    }
    jQuery(function($){
        var uploadImageHeight = 0;
        var uploadImageWidth = 0;

        /**
         * Listen for changes to the logo and upload right away
         */
        $('#jform_logoFile').fileupload({
            dataType: 'json',
            url: '<?php echo "http" . ($https ? "s" : "") . "://{$_SERVER['HTTP_HOST']}/index.php?option=com_ta_providers&task=fileUpload";?>',
            add: function(e,data){
                // reset all alerts
                $('#AJAXMessageContainer').html('');

                // show the loading image
                var loadingImage = new Image();
                $('#tmpLogoWrapper').html(loadingImage);
                $('#tmpLogoWrapper img').css('margin', ' 50px 0 0 155px');
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
                        $('#tmpLogoWrapper img').css('width', '100%');

                        // start the editor
                        $('#tmpLogoWrapper img').imgAreaSelect({
                            aspectRatio: '45:28',
                            handles: true,
                            persistent: true,
                            x1: 0,
                            x2: 225,
                            y1: 0,
                            y2: 140,
                            onInit: function(img, selection){
                                var thumbnailScale =  uploadImageWidth / $('#tmpLogoWrapper img').height();
                                
                                // load the coordinates and dimmensions into the form to be sent to PHP
                                $('#jform_logox1').val(selection.x1 * thumbnailScale);
                                $('#jform_logox2').val(selection.x2 * thumbnailScale);
                                $('#jform_logoy1').val(selection.y1 * thumbnailScale);
                                $('#jform_logoy2').val(selection.y2 * thumbnailScale);
                                $('#jform_logoh').val(selection.height * thumbnailScale);
                                $('#jform_logow').val(selection.width * thumbnailScale);
                            },
                            onSelectChange: function(img, selection){
                                var thumbnailScale =  uploadImageWidth / $('#tmpLogoWrapper img').height();
                                
                                // load the coordinates and dimmensions into the form to be sent to PHP
                                $('#jform_logox1').val(selection.x1 * thumbnailScale);
                                $('#jform_logox2').val(selection.x2 * thumbnailScale);
                                $('#jform_logoy1').val(selection.y1 * thumbnailScale);
                                $('#jform_logoy2').val(selection.y2 * thumbnailScale);
                                $('#jform_logoh').val(selection.height * thumbnailScale);
                                $('#jform_logow').val(selection.width * thumbnailScale);
                            }
                        });
                    });

                    // store the data that was returned for later use
                    uploadImageHeight = data._response.result.height;
                    uploadImageWidth = data._response.result.width;

                    // update the logo field
                    $('#jform_logo').val(data._response.result.message);
                }else{
                    // an error occured
                    $('#AJAXMessageContainer').html('<div class="alert alert-warning"><strong>Warning!</strong> ' + data._response.result.message + '</div>');
                    
                    // hide the image edit popup
                    $('#imageEditPopup').modal('hide'); 
                }
            },
            fail: function(e,data){
                $('#AJAXMessageContainer').html('<div class="alert alert-error"><strong>Error!</strong> An AJAX error occured. Please try again.</div>');
             
                // hide the image edit popup
                $('#imageEditPopup').modal('hide'); 

            }
        });

        /**
         * Close the edit logo popup
         */
        $('#imageEditPopup .closePopup').click(function(){
            // hide the select area
            $('#tmpLogoWrapper img').imgAreaSelect({
                remove: true
            });

            // update the logo image to show the new image
            $('#logoImg').attr('src', $('#tmpLogoWrapper img').attr('src'));

            // figure the scaling
            var scaleX = 450 / $('#jform_logow').val();  
            var scaleY = 280 / $('#jform_logoh').val();

            $('#logoImg').css({
                width: Math.round(scaleX * uploadImageWidth) + 'px',
                height: Math.round(scaleY * uploadImageHeight) + 'px',
                marginLeft: '-' + Math.round(scaleX * $('#jform_logox1').val()) + 'px',
                marginTop: '-' + Math.round(scaleY * $('#jform_logoy1').val()) + 'px'
            });

            // hide the modal
            $('#imageEditPopup').modal('hide');
        });
    });

    /**
     * Open the file upload prompt
     */
    function chooseFile(){
        jQuery('#jform_logoFile').click();
    }
</script>
<div id="AJAXMessageContainer"></div>
<form action="<?php echo JRoute::_('index.php?option=com_ta_providers&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="taprovider-form" class="form-validate">
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
				<div class="control-group" style="display: none;">
					<div class="control-label"><?php echo $this->form->getLabel('created_by'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('website'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('website'); ?></div>
				</div>
            </fieldset>
            <?php echo $this->form->getInput('logo'); ?>
            <?php echo $this->form->getInput('logox1'); ?>
            <?php echo $this->form->getInput('logox2'); ?>
            <?php echo $this->form->getInput('logoy1'); ?>
            <?php echo $this->form->getInput('logoy2'); ?>
            <?php echo $this->form->getInput('logow'); ?>
            <?php echo $this->form->getInput('logoh'); ?>
            <input type="hidden" name="task" value="" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
    </div>
</form>
<form action="/media/com_ta_providers/ajax/fileUpload.php" method="post" enctype="multipart/form-data" name="uploadForm" id="uploadForm" class="form-validate">
    <div class="row-fluid">
        <div class="span10 form-horizontal">
            <fieldset class="adminform">
                <div class="control-group">
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
                    <small><?php echo JText::_('COM_TA_PROVIDERS_SETTINGS_CHANGE_IMAGE'); ?></small><br>
                    <div style="overflow:hidden; width: 450px; height: 280px;">
                        <img onclick="chooseFile();" id="logoImg" src="<?php echo $logoSrc; ?>" alt="<?php echo $this->item->name; ?> Logo" style="height: 280px; max-width: none; width: 450px;">
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</form>
<div class="modal fade" id="imageEditPopup" data-backdrop="static" style="margin-left: -195px; width: 390px;"><!-- TO DO: Fix this, not responsive!!! 320 max on mobile -->
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close closePopup" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo JText::_('COM_TA_PROVIDERS_EDIT_LOGO_HEADING'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?php echo JText::_('COM_TA_PROVIDERS_EDIT_LOGO_PROMPT'); ?></p>
                <div id="tmpLogoWrapper">
                    <img src="" alt="Temporary Image for Editing">
                </div>
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary closePopup"><span class="icomoon-checkmark"></span> <?php echo JText::_('COM_TA_PROVIDERS_DONE'); ?></a>
            </div>
        </div>
    </div>
</div>
