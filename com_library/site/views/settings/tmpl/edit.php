<?php
/**
 * @version     1.0.0
 * @package     com_library
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */
 
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');

// before continuing, we need to handle quotes in the PHP data
foreach($this->resources as &$resource){
	$resource->name = xmlentities($resource->name);
	$resource->description = xmlentities($resource->description);
	$resource->base_file_name = xmlentities($resource->base_file_name);
	$resource->document_path = xmlentities($resource->document_path);
	$resource->cover_path = xmlentities($resource->cover_path);
	$resource->created_by = xmlentities($resource->created_by);
}
// htmlentities is insufficient as it doesn't handle apostrophes
function xmlentities($string){
    return str_replace (array('&','"','’',"'",'<','>'),array('&amp;','&quot;','&apos;','&#039;','&lt;','&gt;'),$string);
}
?>
<div id="resourceForm">
	<div class="alert-wrapper"></div>
	<form action="/" method="post" enctype="multipart/form-data" name="resourceForm" id="resourceForm" class="form-horizontal form-validate">  
		<?php echo $this->form->getInput('id'); ?>
		<fieldset class="resource">
			<div class="control-group">
				<div class="control-label"><label id="jform_state-lbl" title="" for="jform_state"><?php echo JText::_('JSTATUS'); ?></label></div>
				<div class="controls"><?php echo $this->form->getInput('state'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><label id="jform_name-lbl" title="" for="jform_name"><?php echo JText::_('COM_LIBRARY_FORM_NAME_LBL'); ?></label></div>
				<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><label id="jform_description-lbl" title="" for="jform_description"><?php echo JText::_('COM_LIBRARY_FORM_DESCRIPTION_LBL'); ?></label></div>
				<div class="controls"><?php echo $this->form->getInput('description'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><label id="jform_file-lbl" title="" for="jform_file"><?php echo JText::_('COM_LIBRARY_FORM_FILE_LBL'); ?></label></div>
				<div class="controls">
					<?php echo $this->form->getInput('file'); ?>
					<div id="thumbnail"></div>
				</div>
			</div>
			<div class="control-group" id="resourceAudiences">
				<div class="control-label"><label id="jform_target_audiences-lbl" title="" for="jform_target_audiences"><?php echo JText::_('COM_LIBRARY_FORM_TARGET_AUDIENCES_LBL'); ?></label></div>
				<div class="controls"><?php echo $this->form->getInput('target_audiences'); ?></div>
			</div>
			<input type="hidden" id="jform_resourceID" name="jform[resourceID]" value="" />
		</fieldset>
	</form>
	<a href="" class="btn">Close</a>
	<a href="javascript:saveResource();" class="btn btn-primary" id="resourceSaveBtn">Save</a>
</div>