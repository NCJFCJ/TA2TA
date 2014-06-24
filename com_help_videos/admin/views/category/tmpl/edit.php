<?php
/**
 * @package     com_help_videos
 * @copyright   Copyright (C) 2014 NCJFCJ. All rights reserved.
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
$document->addStyleSheet('components/com_help_videos/assets/css/help_videos.css');
?>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function(){
        
    });
    
    Joomla.submitbutton = function(task)
    {
        if(task == 'category.cancel'){
            Joomla.submitform(task, document.getElementById('category-form'));
        }
        else{
            
            if (task != 'category.cancel' && document.formvalidator.isValid(document.id('category-form'))) {
                
                Joomla.submitform(task, document.getElementById('category-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_help_videos&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="category-form" class="form-validate">
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
            </fieldset>
        </div>

        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>

    </div>
</form>