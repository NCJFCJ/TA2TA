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
JHtml::_('behavior.keepalive');

// https?
$https = false;
if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off'){
  $https = true;
}
$link = 'http' . ($https ? 's' : '') . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
?>
<script type="text/javascript">
js = jQuery.noConflict();
 Joomla.submitbutton = function(task){
    // remove unused series fields from the DOM
    if(js('#jform_series1').is(':checked')){
      // this is a series of webinars
      js('#singleWebinar').remove();

      // check all display checkboxes before submit so the values are passed through
      js('input[name="jform[state][]"]').each(function(){
        // set the value based on whether the checkbox is checked
        if(js(this).is(':checked')){
          js(this).val(0);
        }else{
          js(this).val(-3);
        }       

        // set the checkbox to checked
        js(this).prop('checked', true);

        // enable all fields
        js('input[name="jform[date][]"]').prop('disabled', false);
        js('select[name="jform[start_time][]"]').prop('disabled', false);
        js('select[name="jform[end_time][]"]').prop('disabled', false);
        js('input[name="jform[sub_title][]"]').prop('disabled', false);
        js('input[name="jform[webinar_id][]"]').prop('disabled', false);
        js('input[name="jform[state][]"]').prop('disabled', false);
      });
    }else{
      // this is a single webinar
      js('#webinarSeries').remove(); 
    }

    if(task == 'webinar.cancel'){
      Joomla.submitform(task, document.getElementById('webinar-form'));
    }else{
      if(task != 'webinar.cancel' && document.formvalidator.isValid(document.id('webinar-form'))){   
        Joomla.submitform(task, document.getElementById('webinar-form'));
      }else{
        $('#AJAXMessageContainer').html('<div class="alert alert-error"><strong>Error!</strong> <?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?></div>');
      }
    }
  }

  var webinars = new Array();
  <?php
  foreach($this->item->webinars as $webinar){
    echo 'webinars.push(["' . (isset($webinar->start) ? $webinar->start->format('m/d/Y') : '') . '","' . (isset($webinar->start) ? $webinar->start->format('g:ia') : '') . '","' . (isset($webinar->end) ? $webinar->end->format('g:ia') : '') . '","' . (isset($webinar->sub_title) ? $webinar->sub_title : '') . '","' . $webinar->id . '","' . $webinar->state . '"]);';
  }
  ?>

  js(document).ready(function($){
    var selectedProject = <?php echo ($this->item->project > 0 ? $this->item->project : 0); ?>;
    var tableRow = $('#webinarSeries tbody').html();
    //$('#webinarSeries tbody').html('');
    
    // toggle fields for series webinars
    $('#jform_series').change(function(){
      toggleSeriesFields();
    });

    // on load, toggle the fields and start the datepickers
    toggleSeriesFields();     
    initDatepickers(); 

    // listen for clicks on the webinar series button
    $('#webinarSeries button.btn-primary').click(function(event){
      event.preventDefault();
      addWebinarSeries();
    });

    /**
     * Adds a row to the webinar series table
     */
    function addWebinarSeries(){
      var temp = tableRow.replace(/jform_date1/g, 'jform_date' + ($('#webinarSeries tbody tr').length + 1));
      $('#webinarSeries tbody').append(temp);
      initDatepickers();
    }
    
    Calendar._DN = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
    Calendar._SDN = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
    Calendar._FD = 0;
    Calendar._MN = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    Calendar._SMN = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
    Calendar._TT = {"INFO":"About the Calendar","ABOUT":"DHTML Date\/Time Selector\n(c) dynarch.com 2002-2005 \/ Author: Mihai Bazon\nFor latest version visit: http:\/\/www.dynarch.com\/projects\/calendar\/\nDistributed under GNU LGPL.  See http:\/\/gnu.org\/licenses\/lgpl.html for details.\n\nDate selection:\n- Use the \u00ab and \u00bb buttons to select year\n- Use the < and > buttons to select month\n- Hold mouse button on any of the buttons above for faster selection.","ABOUT_TIME":"\n\nTime selection:\n- Click on any of the time parts to increase it\n- or Shift-click to decrease it\n- or click and drag for faster selection.","PREV_YEAR":"Select to move to the previous year. Select and hold for a list of years.","PREV_MONTH":"Select to move to the previous month. Select and hold for a list of the months.","GO_TODAY":"Go to today","NEXT_MONTH":"Select to move to the next month. Select and hold for a list of the months.","SEL_DATE":"Select a date.","DRAG_TO_MOVE":"Drag to move.","PART_TODAY":" Today ","DAY_FIRST":"Display %s first","WEEKEND":"0,6","CLOSE":"Close","TODAY":"Today","TIME_PART":"(Shift-)Select or Drag to change the value.","DEF_DATE_FORMAT":"%Y-%m-%d","TT_DATE_FORMAT":"%a, %b %e","WK":"wk","TIME":"Time:"};
    
    /**
     * Initialize the datepickers
     */
    function initDatepickers(){
      $("input[name^='jform[date]']").each(function(index){
        Calendar.setup({
          inputField: $(this).attr('id'),
          ifFormat: '%m/%d/%Y',
          button: $(this).attr('id') + '_img',
          align: 'Tl',
          singleClick: true,
          firstDay: 0
        });
      });
    }

    /**
     * Populate the TA Project field from the database
     */
    function populateProjectsByOrg(){     
      var org = $('#jform_org').val();

      // hide the error, just incase
      $('#error').hide();

      // remove all options
      $('#jform_project option').remove();

      // add back in the select option
      $('#jform_project').append('<option value="">--Select One--</option>');

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
            $('#jform_project').append('<option value="' + value.id + '"' + (value.id == selectedProject ? ' selected' : '') + '>' +  value.title + '</option>');
          });    
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
    
    /**
     * Toggles the view of the date, start time, end time, and subtitle fields
     * as is required for series webinars versus single webinars
     */
    function toggleSeriesFields(){
      if($('#jform_series1').is(':checked')){
        // if this is the first time we are loading, add a new row so there are two by default
        if($('#webinarSeries tbody tr').length == 1){
          addWebinarSeries();

          // grab the single webinar data, if any, and replace it in the table
          var single = $('#singleWebinar');
          var single_date = single.find('#jform_date').val();
          var single_start = single.find('#jform_start_time').val();
          var single_end = single.find('#jform_end_time').val();
          
          var first_of_series = $('#webinarSeries tbody tr').eq(0);
          first_of_series.find('input[name="jform[date][]"]').val(single_date);
          first_of_series.find('select[name="jform[start_time][]"]').val(single_start);
          first_of_series.find('select[name="jform[end_time][]"]').val(single_end);
          first_of_series.find('input[name="jform[state][]"]').prop('checked', true);
        }

        // load webinar dates, if any
        if(webinars.length > $('#webinarSeries tbody tr').length){
          for(i = 0; i < webinars.length; i++){
            // check if we need to add a new row
            if(i > 1){
              addWebinarSeries();
            }

            // add the data for this webinar to the table
            var row = $('#webinarSeries tbody tr').eq(i);
            row.find('input[name="jform[date][]"]').val(webinars[i][0]);
            row.find('select[name="jform[start_time][]"]').val(webinars[i][1]);
            row.find('select[name="jform[end_time][]"]').val(webinars[i][2]);
            row.find('input[name="jform[sub_title][]"]').val(webinars[i][3]);
            row.find('input[name="jform[webinar_id][]"]').val(webinars[i][4]);
            if(webinars[i][5] == 0){
              row.find('input[name="jform[state][]"]').prop('checked', true);
            }else{
              row.find('input[name="jform[state][]"]').prop('checked', false);
            }

            // check if this date is past and disable the inputs if so
            var now = new Date();
            var webinarDate = new Date(webinars[i][0]);
            if(webinars[i][1].indexOf('am')){
              webinarDate.setHours((webinars[i][1].split(':'))[0]);
            }else{
              webinarDate.setHours((webinars[i][1].split(':'))[0]+12);
            }
            webinarDate.setMinutes((webinars[i][1].split(':'))[1].replace('am', '').replace('pm', ''));
            if(webinarDate < now){
              row.find('input[name="jform[date][]"]').prop('disabled', true);
              row.find('select[name="jform[start_time][]"]').prop('disabled', true);
              row.find('select[name="jform[end_time][]"]').prop('disabled', true);
              row.find('input[name="jform[sub_title][]"]').prop('disabled', true);
              row.find('input[name="jform[webinar_id][]"]').prop('disabled', true);
              row.find('input[name="jform[state][]"]').prop('disabled', true);
            }
          }
        }

        // this is a series
        $('#singleWebinar').hide();
        $('#webinarSeries').show();
      }else{
        // this is a single webinar
        $('#singleWebinar').show();
        $('#webinarSeries').hide();
      }
      initDatepickers();
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
  #webinarSeries table tbody tr td:nth-of-type(1){
   width: 125px;
  }
  #webinarSeries table tbody tr td:nth-of-type(2),
  #webinarSeries table tbody tr td:nth-of-type(3){
    width: 155px;
  }
  #webinarSeries table tbody tr td select{
    width: 100px;
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
<form action="<?php echo JRoute::_('index.php?option=com_services&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="webinar-form" class="form-validate">
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
          <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
          <div class="controls"><?php echo $this->form->getInput('title'); ?></div>
        </div>
        <div class="control-group">
          <div class="control-label"><?php echo $this->form->getLabel('alias'); ?></div>
          <div class="controls"><?php echo $this->form->getInput('alias'); ?></div>
        </div>
        <div class="control-group">
          <div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
          <div class="controls"><?php echo $this->form->getInput('description'); ?></div>
        </div>
        <div class="control-group">
          <div class="control-label"><?php echo $this->form->getLabel('project'); ?></div>
          <div class="controls"><?php echo $this->form->getInput('project'); ?></div>
        </div>
    		<div class="control-group">
    			<div class="control-label"><?php echo $this->form->getLabel('series'); ?></div>
    			<div class="controls"><?php echo $this->form->getInput('series'); ?></div>
    		</div>
        <div id="singleWebinar">
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('date'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('date', null, (empty($this->item->webinars[0]) ? '' : $this->item->webinars[0]->date)); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('start_time'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('start_time', null, (empty($this->item->webinars[0]) ? '' : $this->item->webinars[0]->start_time)); ?> PST</div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('end_time'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('end_time', null, (empty($this->item->webinars[0]) ? '' : $this->item->webinars[0]->end_time)); ?> PST</div>
          </div>
        </div>
        <div id="webinarSeries">
          <div class="control-group">
            <div class="control-label"></div>
            <div class="controls">
              <table class="table table-stripped" style="width: auto;">
                <thead>
                  <tr>
                    <th><?php echo JText::_('COM_SERVICES_FORM_WEBINAR_DATE_LBL'); ?></th>
                    <th><?php echo JText::_('COM_SERVICES_FORM_WEBINAR_START_TIME_LBL'); ?></th>
                    <th><?php echo JText::_('COM_SERVICES_FORM_WEBINAR_END_TIME_LBL'); ?></th>
                    <th><?php echo JText::_('COM_SERVICES_FORM_WEBINAR_SUB_TITLE_LBL'); ?></th>
                    <th class="center"><?php echo JText::_('COM_SERVICES_FORM_WEBINAR_DISPLAY_LBL'); ?></th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <div class="input-append">
                        <input name="jform[webinar_id][]" type="hidden" value="">
                        <input aria-invalid="false" class="input-medium hasTooltip" data-original-title="" id="jform_date1" maxlength="45" name="jform[date][]" title="" type="text" value="">
                        <button class="btn" id="jform_date1_img" type="button">
                          <span class="icon-calendar"></span>
                        </button>
                      </div>
                    <td>
                      <select class="form-control" name="jform[start_time][]">
                        <option value="8:00am">8:00am</option>
                        <option value="8:15am">8:15am</option>
                        <option value="8:30am">8:30am</option>
                        <option value="8:45am">8:45am</option>
                        <option value="9:00am">9:00am</option>
                        <option value="9:15am">9:15am</option>
                        <option value="9:30am">9:30am</option>
                        <option value="9:45am">9:45am</option>
                        <option value="10:00am">10:00am</option>
                        <option value="10:15am">10:15am</option>
                        <option value="10:30am">10:30am</option>
                        <option value="10:45am">10:45am</option>
                        <option value="11:00am">11:00am</option>
                        <option value="11:15am">11:15am</option>
                        <option value="11:30am">11:30am</option>
                        <option value="11:45am">11:45am</option>
                        <option value="12:00pm">12:00pm</option>
                        <option value="12:15pm">12:15pm</option>
                        <option value="12:30pm">12:30pm</option>
                        <option value="12:45pm">12:45pm</option>
                        <option value="1:00pm">1:00pm</option>
                        <option value="1:15pm">1:15pm</option>
                        <option value="1:30pm">1:30pm</option>
                        <option value="1:45pm">1:45pm</option>
                        <option value="2:00pm">2:00pm</option>
                        <option value="2:15pm">2:15pm</option>
                        <option value="2:30pm">2:30pm</option>
                        <option value="2:45pm">2:45pm</option>
                        <option value="3:00pm">3:00pm</option>
                        <option value="3:15pm">3:15pm</option>
                        <option value="3:30pm">3:30pm</option>
                        <option value="3:45pm">3:45pm</option>
                        <option value="4:00pm">4:00pm</option>
                        <option value="4:15pm">4:15pm</option>
                        <option value="4:30pm">4:30pm</option>
                        <option value="4:45pm">4:45pm</option>
                        <option value="5:00pm">5:00pm</option>
                      </select>&nbsp;&nbsp;PST
                    </td>
                    <td>
                      <select class="form-control" name="jform[end_time][]">
                        <option value="8:15am">8:15am</option>
                        <option value="8:30am">8:30am</option>
                        <option value="8:45am">8:45am</option>
                        <option value="9:00am">9:00am</option>
                        <option value="9:15am">9:15am</option>
                        <option value="9:30am">9:30am</option>
                        <option value="9:45am">9:45am</option>
                        <option value="10:00am">10:00am</option>
                        <option value="10:15am">10:15am</option>
                        <option value="10:30am">10:30am</option>
                        <option value="10:45am">10:45am</option>
                        <option value="11:00am">11:00am</option>
                        <option value="11:15am">11:15am</option>
                        <option value="11:30am">11:30am</option>
                        <option value="11:45am">11:45am</option>
                        <option value="12:00pm">12:00pm</option>
                        <option value="12:15pm">12:15pm</option>
                        <option value="12:30pm">12:30pm</option>
                        <option value="12:45pm">12:45pm</option>
                        <option value="1:00pm">1:00pm</option>
                        <option value="1:15pm">1:15pm</option>
                        <option value="1:30pm">1:30pm</option>
                        <option value="1:45pm">1:45pm</option>
                        <option value="2:00pm">2:00pm</option>
                        <option value="2:15pm">2:15pm</option>
                        <option value="2:30pm">2:30pm</option>
                        <option value="2:45pm">2:45pm</option>
                        <option value="3:00pm">3:00pm</option>
                        <option value="3:15pm">3:15pm</option>
                        <option value="3:30pm">3:30pm</option>
                        <option value="3:45pm">3:45pm</option>
                        <option value="4:00pm">4:00pm</option>
                        <option value="4:15pm">4:15pm</option>
                        <option value="4:30pm">4:30pm</option>
                        <option value="4:45pm">4:45pm</option>
                        <option value="5:00pm">5:00pm</option>
                      </select>&nbsp;&nbsp;PST
                    </td>
                    <td><input class="form-control" name="jform[sub_title][]" type="text" value=""></td>
                    <td class="center"><input class="form-control" name="jform[state][]" type="checkbox" value="0" checked></td>
                  </tr>
                </tbody>
              </table>
              <button class="btn btn-primary"><span class="icomoon-plus-circle"></span> Add Webinar In Series</button>
              <br>
              <br>
            </div>
          </div>
        </div>
        <div class="control-group">
          <div class="control-label"><?php echo $this->form->getLabel('num_participants'); ?></div>
          <div class="controls"><?php echo $this->form->getInput('num_participants'); ?></div>
        </div>
        <div class="control-group">
          <div class="control-label"><?php echo $this->form->getLabel('features'); ?></div>
          <div class="controls" style="width: 600px;"><?php echo $this->form->getInput('features'); ?></div>
        </div>
        <div class="control-group">
          <div class="control-label"><?php echo $this->form->getLabel('file'); ?></div>
          <div class="controls">
            <?php
              if(!empty($this->item->file)){
                echo 'Uploaded file: <a href="/media/com_services/materials/webinars/' . $this->item->file . '.pdf" target="_blank">' . $this->item->file . '.pdf</a><br><br>Caution: Uploading a new file will replace the current file uploaded by the user. Only one file is supported per request.<br>';
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
            <div class="control-label"><?php echo $this->form->getLabel('registration_adv_accessibility'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('registration_adv_accessibility'); ?></div>
          </div>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('adobe_number'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('adobe_number'); ?></div>
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
                $url = 'http' . (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/webinars/registration/' . $this->item->alias . '.html';
              ?>
              <a href="<?php echo $url; ?>" target="_blank"><?php echo $url; ?></a>
              <?php else: ?>
              The registration URL is only available after this webinar is scheduled (see the status field above)
              <?php endif; ?>
            </div>
          </div>
          <?php endif; ?>
          <?php if($this->item->registration_records): ?>
          <div class="control-group">
            <div class="control-label">Registration Results</div>
            <div class="controls">
              <table class="table" style="width: auto;">
                <thead>
                  <tr>
                    <th style="width: 150px;">Date</th>
                    <th>Registrants</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                <?php 
                  $lastDate = new DateTime('1970-01-01 00:00:00');
                  $numDates = 1;
                  $registrants = 0;          
                  foreach($this->item->registration_records as $record){
                    // convert the MySQL date to a PHP DateTime object so we can compare
                    $recordDate = new DateTime($record->start);

                    // check if this is the first loop, and if so, set the last date used
                    if($record === reset($this->item->registration_records)){
                      $lastDate = $recordDate;
                    }else{
                      // if this isn't the first loop, check if it is a new date
                      if($lastDate->format('y-m-d') != $recordDate->format('y-m-d')){
                        // draw the row for the last information set
                        echo '<tr><td>' . $lastDate->format('m/d/Y g:ia') . '</td><td>' . $registrants . '</td><td><a href="' . $link . '&amp;registrationDownload=' . $lastDate->format('Y-m-d H:i:s') . '"><span class="icon-file-2"></span></a></td></tr>';

                        // this is a new date
                        $registrants = 0;
                        $lastDate = $recordDate;
                        $numDates++;
                      }
                    }

                    // increment the viewrs and visitors
                    $registrants++;

                    // if this is the last loop, print its row
                    if($record === end($this->item->registration_records)){
                      // draw the row for the last information set
                      echo '<tr><td>' . $lastDate->format('m/d/Y g:ia') . '</td><td>' . $registrants . '</td><td><a href="' . $link . '&amp;registrationDownload=' . $lastDate->format('Y-m-d H:i:s') . '"><span class="icon-file-2"></span></a></td></tr>';
                    }
                  }
                ?>
                </tbody>
                <?php
                  if($numDates > 1){
                    echo '<tfoot><tr><td><strong>Total:</strong></td><td><strong>' . count($this->item->registration_records) . '</strong></td><td><a href="' . $link . '&amp;registrationDownload=*"><span class="icon-file-2"></span></a></td></tr></tfoot>';
                  }
                ?>
              </table>
            </div>
          </div>
          <?php endif; ?>
        </div>
        <div style="border: 2px solid #000; margin-top: 20px; padding: 10px;">
          <h2>Webinar Login Portal</h2>
          <p>The webinar login portal is a page on the TA2TA website that will allow users to access this webinar. When a user accesses a webinar through a TA2TA webinar portal page, additional information about that user is collected which can then be used in reporting. In order to activate the webinar login portal for this webinar, enter an Adobe Connect webinar link below.</p>
          <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('adobe_link'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('adobe_link'); ?></div>
          </div>
          <?php if(!empty($this->item->adobe_link)): ?>
          <div class="control-group">
            <div class="control-label">Webinar Portal URL</div>
            <div class="controls">
              <?php
              if($this->item->state == 0):
                $url = 'http' . (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/webinars/' . $this->item->alias . '.html';
              ?>
              <a href="<?php echo $url; ?>" target="_blank"><?php echo $url; ?></a>
              <?php else: ?>
              The portal URL is only available after this webinar is scheduled (see the status field above)
              <?php endif; ?>
            </div>
          </div>
          <?php endif; ?>
          <?php if(!empty($this->item->portal_records)): ?>
          <div class="control-group">
            <div class="control-label">Portal Login Stats</div>
            <div class="controls">
              <table class="table" style="width: auto;">
                <thead>
                  <tr>
                    <th style="width: 110px;">Date</th>
                    <th>Visitors</th>
                    <th>Viewers</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                <?php 
                  $lastDate = new DateTime('1970-01-01 00:00:00');
                  $numDates = 1;
                  $totalViewers = 0;
                  $viewers = 0;                  
                  $visitors = 0;
                  foreach($this->item->portal_records as $record){
                    // convert the MySQL date to a PHP DateTime object so we can compare
                    $recordDate = new DateTime($record->created);

                    // check if this is the first loop, and if so, set the last date used
                    if($record === reset($this->item->portal_records)){
                      $lastDate = $recordDate;
                    }else{
                      // if this isn't the first loop, check if it is a new date
                      if($lastDate->format('y-m-d') != $recordDate->format('y-m-d')){
                        // draw the row for the last information set
                        echo '<tr><td>' . $lastDate->format('m/d/Y') . '</td><td>' . $visitors . '</td><td>' . $viewers . '</td><td><a href="' . $link . '&amp;portalStatsDownload=' . $lastDate->format('Ymd') . '"><span class="icon-file-2"></span></a></td></tr>';

                        // this is a new date
                        $viewers = 0;
                        $visitors = 0;
                        $lastDate = $recordDate;
                        $numDates++;
                      }
                    }

                    // increment the viewrs and visitors
                    $totalViewers += $record->num_viewers;
                    $viewers += $record->num_viewers;
                    $visitors++;

                    // if this is the last loop, print its row
                    if($record === end($this->item->portal_records)){
                      // draw the row for the last information set
                      echo '<tr><td>' . $lastDate->format('m/d/Y') . '</td><td>' . $visitors . '</td><td>' . $viewers . '</td><td><a href="' . $link . '&amp;portalStatsDownload=' . $lastDate->format('Ymd') . '"><span class="icon-file-2"></span></a></td></tr>';
                    }
                  }
                ?>
                </tbody>
                <?php
                  if($numDates > 1){
                    echo '<tfoot><tr><td><strong>Total:</strong></td><td><strong>' . count($this->item->portal_records) . '</strong></td><td><strong>' . $totalViewers . '</strong></td><td><a href="' . $link . '&amp;portalStatsDownload=*"><span class="icon-file-2"></span></a></td></tr></tfoot>';
                  }
                ?>
              </table>
            </div>
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