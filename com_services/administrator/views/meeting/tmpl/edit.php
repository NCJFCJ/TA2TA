<?php
/**
 * @package     com_services
 * @copyright   Copyright (C) 2016 NCJFCJ. All rights reserved.
 * @author      Zadra Design - http://zadradesign.com
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// https?
$https = false;
if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off'){
  $https = true;
}
$link = 'http' . ($https ? 's' : '') . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
?>
<script type="text/javascript">
 Joomla.submitbutton = function(task){
    if(task == 'meeting.cancel'){
      Joomla.submitform(task, document.getElementById('meeting-form'));
    }else{
      if(task != 'meeting.cancel' && document.formvalidator.isValid(document.id('meeting-form'))){   
        Joomla.submitform(task, document.getElementById('meeting-form'));
      }else{
        $('#AJAXMessageContainer').html('<div class="alert alert-error"><strong>Error!</strong> <?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?></div>');
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
        $('#error-message').html('<strong>Error!</strong> AJAX error. Please contact us.');
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
<style type="text/css">
  ul{
    margin-left: 0;
  }
  #jform_registration_q1,
  #jform_registration_q2,
  #jform_registration_q3{
    width: 85%;
  }
  #jform_registration_q1_type,
  #jform_registration_q2_type,
  #jform_registration_q3_type{
    width: 12%;
  }
</style>
<form action="<?php echo JRoute::_('index.php?option=com_services&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="meeting-form" class="form-validate">
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
    			<div class="control-label"><?php echo $this->form->getLabel('created_by'); ?></div>
    			<div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
    		</div>
        <div class="control-group">
          <div class="control-label"><?php echo $this->form->getLabel('org'); ?></div>
          <div class="controls"><?php echo $this->form->getInput('org'); ?></div>
        </div>
    		<div class="control-group">
    			<div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
    			<div class="controls"><?php echo $this->form->getInput('state'); ?></div>
    		</div>
        <div class="control-group">
          <div class="control-label"><?php echo $this->form->getLabel('alias'); ?></div>
          <div class="controls"><?php echo $this->form->getInput('alias'); ?></div>
        </div>
    		<div class="control-group">
    			<div class="control-label"><?php echo $this->form->getLabel('suggested_dates'); ?></div>
    			<div class="controls"><?php echo $this->form->getInput('suggested_dates'); ?></div>
    		</div>
        <div class="control-group">
          <div class="control-label"><?php echo $this->form->getLabel('project'); ?></div>
          <div class="controls"><?php echo $this->form->getInput('project'); ?></div>
        </div>
        <div class="control-group">
          <div class="control-label"><?php echo $this->form->getLabel('support_options'); ?></div>
          <div class="controls">
            <div style="width: 700px;">
              <?php echo $this->form->getInput('support_options'); ?>
            </div>
          </div>
        </div>
        <div class="control-group">
          <div class="control-label"><?php echo $this->form->getLabel('file'); ?></div>
          <div class="controls">
            <?php
              if(!empty($this->item->file)){
                echo 'Uploaded file: <a href="/media/com_services/materials/meetings/' . $this->item->file . '.pdf" target="_blank">' . $this->item->file . '.pdf</a><br><br>Caution: Uploading a new file will replace the current file uploaded by the user. Only one file is supported per request.<br>';
              }
              echo $this->form->getInput('file');
            ?>
          </div>
        </div>
    		<div class="control-group">
    			<div class="control-label"><?php echo $this->form->getLabel('comments'); ?></div>
    			<div class="controls"><?php echo $this->form->getInput('comments'); ?></div>
    		</div>
        <div style="border: 2px solid #000; margin-top: 20px; padding: 10px;">
          <h2>Registration Page</h2>
          <p>When registration is enabled, the website will automatically generate a registration page that will collect information from users who wish to attend this event.</p>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('registration'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('registration'); ?></div>
          </div>
          <?php if($this->item->registration): ?>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('registration_cutoff'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('registration_cutoff'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('description'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('registration_adv_accessibility'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('registration_adv_accessibility'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('registration_q1'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('registration_q1') . ' ' . $this->form->getInput('registration_q1_type'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('registration_q2'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('registration_q2') . ' ' . $this->form->getInput('registration_q2_type'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('registration_q3'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('registration_q3') . ' ' . $this->form->getInput('registration_q3_type'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label">Registration URL</div>
            <div class="controls">
              <?php
              if($this->item->state == 0):
                $url = 'http' . (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/meetings/registration/' . $this->item->alias . '.html';
              ?>
              <a href="<?php echo $url; ?>" target="_blank"><?php echo $url; ?></a>
              <?php else: ?>
              The registration URL is only available after this event is scheduled (see the status field above)
              <?php endif; ?>
            </div>
          </div>
          <?php endif; ?>
          <?php if($this->item->registration_records): ?>
          <div class="control-group">
            <div class="control-label">Registrants</div>
            <div class="controls"><?php echo count($this->item->registration_records); ?> <a href="<?php echo $link; ?>&amp;registrationDownload=*"><span class="icon-file-2"></span></a></div>
          </div>
          <?php endif; ?>
        </div>
      </fieldset>
      <input type="hidden" name="task" value="" />
      <?php
        echo $this->form->getInput('id');
        echo JHtml::_('form.token');
      ?>
    </div>
  </div>
</form>