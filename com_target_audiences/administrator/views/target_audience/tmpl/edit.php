<?php
/**
 * @version     1.0.0
 * @package     com_target_audiences
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
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
$document->addStyleSheet('components/com_target_audiences/assets/css/target_audiences.css');
?>
<script type="text/javascript">
    js = jQuery.noConflict();
    Joomla.submitbutton = function(task)
    {
        if(task == 'target_audience.cancel'){
            Joomla.submitform(task, document.getElementById('target_audience-form'));
        }
        else{
            
            if (task != 'target_audience.cancel' && document.formvalidator.isValid(document.id('target_audience-form'))) {
                
                Joomla.submitform(task, document.getElementById('target_audience-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_target_audiences&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="target_audience-form" class="form-validate">
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
				<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
			</div>
			<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
			<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
			<?php if(empty($this->item->created_by)){ ?>
				<input type="hidden" name="jform[created_by]" value="<?php echo JFactory::getUser()->id; ?>" />
			<?php } 
			else{ ?>
				<input type="hidden" name="jform[created_by]" value="<?php echo $this->item->created_by; ?>" />

			<?php } ?>
            </fieldset>
        </div>
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>