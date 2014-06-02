<?php
/**
 * @package     com_ta_providers
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ta2ta.org
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
?>
<script type="text/javascript">
   Joomla.submitbutton = function(task)
    {
        if(task == 'taprovider.cancel'){
            Joomla.submitform(task, document.getElementById('taprovider-form'));
        }
        else{
            
            if (task != 'taprovider.cancel' && document.formvalidator.isValid(document.id('taprovider-form'))) {
                
                Joomla.submitform(task, document.getElementById('taprovider-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

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
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('logo'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('logo');
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
                    }?><p><small>Please upload a logo that is exactly 450px by 280px on either a transparent or white background.</small></p></div>
                </div>
            </fieldset>
        </div>
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>