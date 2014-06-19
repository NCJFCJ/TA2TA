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
        $('#jform_logo').fileupload({
            dataType: 'json',
            url: '<?php echo 'http' . ($https ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/administrator/index.php?option=com_ta_providers&task=fileUpload';?>',
            add: function(e,data){
                //data.context = $('<p/>').text('Uploading...').appendTo(document.body);
                //show loading graphic
                data.submit();
            },
            done: function(e,data){
                if(data.status == 'success'){
                    // show the image
                    $('#logoWrapper').html('<br><br><img src="/media/com_ta_providers/tmp/' + data.message + '" alt="" style="width: 200px;">');

                    // update the logo field
                    $('#logoPath').val(data.message);
                }else{
                    // an error occured
                    $('#AJAXMessageContainer').html('<div class="alert"><strong>Warning!</strong> ' + data.message);
                }
            },
            fail: function(e,data){
                $('#AJAXMessageContainer').html('<div class="alert alert-error"><strong>Error!</strong> An AJAX error occured. Please try again.</div>');
            }
        });
    });
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
            <input type="hidden" name="jform[logoPath]" id="logoPath" />
            <input type="hidden" name="task" value="" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
    </div><!--
</form>
<form action="/media/com_ta_providers/ajax/fileUpload.php" method="post" enctype="multipart/form-data" name="uploadForm" id="uploadForm" class="form-validate">
-->    <div class="row-fluid">
        <div class="span10 form-horizontal">
            <fieldset class="adminform">
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('logo'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('logo'); ?>
                        <div id="logoWrapper">
                        <?php    
                    if($this->item->id > 0){
                        // editing, check if prior file exists
                        if(property_exists($this->item, 'logo')){
                            $imgPath = '/media/com_ta_providers/logos/' . $this->item->logo;
                            if(file_exists(JPATH_SITE . $imgPath)){
                                // show the image
                                echo '<br><br><img src="' . $imgPath . '" alt="' . $this->item->name . ' Logo" style="width: 200px;">';
                            }else{
                                // show the placeholder image
                                echo '<br><br><img src="/media/com_ta_providers/logos/no-logo.jpg" alt="' . $this->item->name . ' Logo" style="width: 200px;">';
                            }
                        }else{
                            // show the placeholder image
                            echo '<br><br><img src="/media/com_ta_providers/logos/no-logo.jpg" alt="' . $this->item->name . ' Logo" style="width: 200px;">';
                        }
                    }?></div><p><small>Please upload a logo that is exactly 450px by 280px on either a transparent or white background.</small></p></div>
                </div>
            </fieldset>
        </div>
    </div>
</form>
