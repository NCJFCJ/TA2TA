<?php
/**
 * @package     com_event_manager
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */
 
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
//JHtml::_('behavior.tooltip');
//JHtml::_('behavior.formvalidation');

// https?
$https = false;
if(!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]!=="off"){
  $https = true;
}

// Time quick pick options
$quick_pick_times = array(
  '7:00am',
  '7:30am',
  '8:00am',
  '8:30am',
  '9:00am',
  '9:30am',
  '10:00am',
  '10:30am',
  '11:00am',
  '11:30am',
  '12:00pm',
  '12:30pm',
  '1:00pm',
  '1:30pm',
  '2:00pm',
  '2:30pm',
  '3:00pm',
  '3:30pm',
  '4:00pm',
  '4:30pm',
  '5:00pm',
  '5:30pm',
  '6:00pm'
);
// determine the user's timezone, and adopt it
$timezoneAbbr = 'PST';
if(isset($this->userSettings->timezone)){
  foreach($this->timezones as $tz){
    if($tz->abbr == $this->userSettings->timezone){
      $timezoneAbbr = $tz->abbr;
      break;
    }
  }
}

?>
<script type="text/javascript" src="/media/editors/tinymce/tinymce.min.js"></script>

<script type = "text/javascript">


jQuery(function($){
  var currentStep = 1;
  changeStep(currentStep);
  // Click handler for Next button 
  $('#nextBlock').click(function(){
    currentStep++;
    changeStep(currentStep);
  });
  // Click handler for Prev button 
  $('#previousBlock').click(function(){
    currentStep--;
    changeStep(currentStep);
  });
  
   /**
   * Function to change current step/pagination.
   *
   * @param int currentStep   current step/pg number.
   */
  function changeStep(currentStep){
    $('.step-wrapper').hide();
    $('.step-wrapper').eq(currentStep-1).show();
    $('#currentStep').text(currentStep);
    // Handles Prev button display
    if(currentStep>1){
      $('#previousBlock').show();
    }else{
      $('#previousBlock').hide();
    }
    // Handles Next button display
   console.log(currentStep + "<" + $('.step-wrapper').length);
    if(currentStep<$('.step-wrapper').length){
      $('#nextBlock').show();
      $('#submitButton').hide();
    }else{
      $('#nextBlock').hide();
      $('#submitButton').show();
    }
  }

 // Initialize TinyMCE for summary, assitance and comments fields
    tinyMCE.init({
      dialog_type: 'modal',
      doctype: '<!DOCTYPE html>',
      editor_selector: 'mce-editor',
      element_format: 'html',
      menubar: false,
      mode: 'specific_textareas',
      paste_word_valid_elements: 'b,strong,i,em,u,li,ul,ol,ul',
      plugins: 'paste',
      statusbar: false,
      toolbar: 'bold,italic,underline,separator,bullist,numlist,separator,outdent,indent,separator,undo,redo',
      setup: function(editor){
        editor.on('change', function(e){
          // summary
          var summary = tinyMCE.activeEditor.getContent();
          if(summary.length < 20 || summary.length > 1500){
            ta2ta.bootstrapHelper.showValidationState($('.mce-tinymce'), 'error', true);
          }else{
            ta2ta.bootstrapHelper.showValidationState($('.mce-tinymce'), 'success', true);
          }
          // assistance
          var assistance = tinyMCE.activeEditor.getContent();
          if(assistance.length < 20 || assistance.length > 1500){
            ta2ta.bootstrapHelper.showValidationState($('.mce-tinymce'), 'error', true);
          }else{
            ta2ta.bootstrapHelper.showValidationState($('.mce-tinymce'), 'success', true);
          }
          // comments
          var comments = tinyMCE.activeEditor.getContent();
          if(comments.length < 20 || comments.length > 1500){
            ta2ta.bootstrapHelper.showValidationState($('.mce-tinymce'), 'error', true);
          }else{
            ta2ta.bootstrapHelper.showValidationState($('.mce-tinymce'), 'success', true);
          }
        });
      }
    });
    
 // date pickers
   var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
  
   $('#startPicker').datepicker({
     autoclose: true,
     format: 'mm-dd-yyyy',
     startDate: now,
     todayHighlight: true
   }).on('changeDate', function(e) {
     var newDate = new Date(e.date);
     $('#endPicker').datepicker('update', newDate);
     $('#endPicker').datepicker('setStartDate', newDate);
   }).data('datepicker');

   $('#endPicker').datepicker({
     autoclose: true,
     format: 'mm-dd-yyyy'
   }).on('changeDate', function(e) {
     editLimitEndTimeSelect();
   }).data('datepicker');


    /* --- time quick pick --- */
    
    var listeningToFormat = false;
    
    $('.time-quick-pick input').focus(function(){
      // Show the select list when the time field gains
      $(this).next().show();
    }).keypress(function(event){
      // hide the select if the user begins typing in the time field, ignore tab
      if(event.keyCode != 9){
        $(this).next().hide();
      }
      if(!listeningToFormat){
        $(this).one('blur', function(){
          // set the value of the field
          var newTime = formatTime($(this));
          $(this).val(newTime);
          $(this).change();
          
          // udpate the select to match, if there is one and scroll to it
          var selectObj = $(this).next();
          selectObj.val(newTime);
          
          listeningToFormat = false;
        });
        listeningToFormat = true;
      }
    }).blur(function(event){
      var thisObj = $(this);
      setTimeout(function(){
        var selectObj = thisObj.next();
        if(!(selectObj.is(':focus'))){
          selectObj.hide();
        }
      },100);
    });

    $('.time-quick-pick select').blur(function(){
      // Hide the select list when it loses focus
      $(this).hide();
    }).change(function(){
      // Update the time input field when a selection is made
      $(this).prev().val($(this).val());
      if($(this).prev().attr('id') == 'starttime'){
        $('#starttime').change();
      }
    }).click(function(event){
      // Close the select on click
      $(this).hide();         
    });
    
    /**
     * This function takes user input and tries to make it a logical time string
     * @param input A $ object for the field to be validated
     */
    function formatTime(input){
      // variables
      var timeString = input.val();
      var returnString = '';
      
      // strip any non-valid characters
      timeString = timeString.replace(/[^\d:ampAMP ]/g,'');
      
      // convert to lower case
      timeString.toLowerCase();
      
      // checks
      var hasColon = timeString.indexOf(':') > -1 ? true : false;
      var hasLetter = timeString.indexOf('a') > -1 
              || timeString.indexOf('m') > -1
              || timeString.indexOf('p') > -1 ? true : false;
      
      // Note: The following code intentionally drops malformed data. You will see no trapping for bad dates,
      // these are addressed by a default below.
      if(hasColon){
        // there is a colon
        // split the string based on the colon
        var stringArray = timeString.split(':');
        // only bother processing if there is the proper number of colons (1)
        if(stringArray.length == 2){
          // check hour
          var hour = parseInt(stringArray[0], 10);
          if(hour >= 1 && hour <= 23){
            // strip AM or PM from the minute string
            var minutes = parseInt(stringArray[1].replace(/[^\d]/g,''), 10);
            if(minutes >= 0 && minutes <= 59){
              // both the hour and minutes are valid, let's reconstruct and format the string
              var ampm = 'am';
              if(hour > 12){
                // correcting military time
                hour = hour - 12;
                ampm = 'pm';
              }else{
                if(hasLetter){
                  var letters = stringArray[1].replace(/[^amp]/g,'');
                  if(letters == 'p' || letters == 'pm'){
                    ampm = 'pm';
                  }else{
                    ampm = 'am';
                  }
                }else{
                  // base am or pm on the hour provided
                  if((hour >= 1 && hour <= 6) || hour == 12){
                    ampm = 'pm';
                  }
                }
              }
              returnString = hour + ':' + (minutes < 10 ? '0' + minutes : minutes) + ampm;
            }
          }
        }
      }else{
        // no colon exists
        switch(timeString){
          case '1':
            returnString = '1:00pm';
            break;
          case '2':
            returnString = '2:00pm';
            break;
          case '3':
            returnString = '3:00pm';
            break;
          case '4':
            returnString = '4:00pm';
            break;
          case '5':
            returnString = '5:00pm';
            break;
          case '6':
            returnString = '6:00pm';
            break;
          case '7':
            returnString = '7:00am';
            break;
          case '8':
            returnString = '8:00am';
            break;
          case '9':
            returnString = '9:00am';
            break;
          case '10':
            returnString = '10:00am';
            break;
          case '11':
            returnString = '11:00am';
            break;
          case '12':
            returnString = '12:00pm';
            break;
        }
      }
      
      if(returnString != ''){
        return returnString;
      }
      // if this is an end time, base it on the start time
      if(input.attr('name') == 'endtime'){
        var startTime = $('#starttime').val();
        if(startTime != ''){
          // split this on the colon
          var startTimeArray = startTime.split(':');
          var endHour = parseInt(startTimeArray[0], 10) + 1;
          var endMinutes = startTimeArray[1].replace(/[^\d]/g,'');
          var endAmpm = startTimeArray[1].replace(/[^amp]/g,'');
          
          // is the hour in bounds?
          if(endHour > 12){
            if(endAmpm == 'am'){
              endHour = 1;
            }else{
              endHour = 12;
            }
            endAmpm = 'pm';
          }
          
          // build the return, adding one hour
          return endHour + ':' + endMinutes + endAmpm;
        }
      }
      
      // just send 5pm for end times and 8am for all else
      if(input.attr('name') == 'endtime'){
        return '5:00pm';
      }else{
        return '8:00am';
      }
    }

/**
     * Checks the appropriate checkboxes for a given field with the given values
     * 
     * @params string The name of the checkbox (sans brackets)
     * @params array The values corresponding to the checkboxes to be checked
     */

    function checkEditCheckboxes(name, values){
      // uncheck all
      $("#mtgForm input[name='" + name + "[]']").prop('checked', false);

      // check selected
      $.each(values, function(index,value){
        $("#mtgForm input[name='" + name + "[]'][value='" + value + "']").prop('checked', true);
      });
    }

 // on load, update the edit box timezone
    $('.timezoneLabel').text('<?php echo $timezoneAbbr; ?>');

  /** ----- Meeting Registration Form Live Validation ----- **/
  // Page 1
  // start date
      $('#startdate').change(function(){
    ta2ta.validate.date($(this),3);
      });

  // start time
      $('#starttime').change(function(){
        ta2ta.validate.time($(this),3);
      });

      // end date
      $('#enddate').change(function(){
        ta2ta.validate.date($(this),3);
      });

  // end time
      $('#endtime').change(function(){
        ta2ta.validate.time($(this),3);
      });

  // title
      $('#title').change(function(){
        if(!ta2ta.validate.hasValue($(this))
        || !ta2ta.validate.minLength($(this),10)
        || !ta2ta.validate.maxLength($(this),255)
        || !ta2ta.validate.title($(this))){
            ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
          }else{
                ta2ta.bootstrapHelper.showValidationState($(this), 'success', true);
          }
      });

  // event_url
      $('#event_url').change(function(){
        if($(this).val()){
          ta2ta.validate.url($(this), 3);
        }
      });

  // Page 2
    
  // project
      $('#project').change(function(){
        console.log('I m here!');
       ta2ta.validate.hasValue($(this),3);
      });

  // grantPrograms
 
  // approval

  // Page 3

  // topicAreas

  // targetAudiences

  // Page 4

  // regchoice

  // registration_url
    $('#registration_url').change(function(){
      if($(this).val()){
        ta2ta.validate.url($(this), 3);
      }
  });  
  // open

  // assistance

  // comments

/**
     * Populate the grant programs from the selected TA Project
     */

    $('#project').change(function(){
     // send the AJAX request
      var request = $.ajax({
        data: {project: $(this).val()},
        dataType: 'json',
        type: 'POST',
        url: '<?php echo 'http' . ($https ? 's' : '') . '://'. $_SERVER['HTTP_HOST'] . '/index.php?option=com_ta_calendar&task=getPrograms';?>',
      });
      
      // fires when the AJAX call completes
      request.done(function(response, textStatus, jqXHR){
        // check if this has an error
        if(response.status == 'success'){
          // check if any selections were made previously
          if($("#mtgForm input[name='grantPrograms[]']:checked").length){
            pastGrantPrograms = true;
          }

          // check the appropriate boxes
          checkEditCheckboxes('grantPrograms',response.data);
        }else{
          ta2ta.bootstrapHelper.showAlert(response.message, response.status);
        }
      });

      // catch if the AJAX call fails completelly
      request.fail(function(jqXHR, textStatus, errorThrown){
        // notify the user that an error occured
        ta2ta.bootstrapHelper.showAlert('Server error. AJAX connection failed.', 'error', true);
      });

    });

});        
</script>

<!-- START REG FORM _____________________________________________________ -->
<h3>Meeting Request Form</h3>
<p>To request a meeting, please provide the following information:</p> 
<div class="row" id="mtgForm">
  <div class="col-sm-8 col-md-6">
    <form role="form" >   
      <!-- Page 1 -->
      <div class="step-wrapper">
        <!-- Meeting Start Date and Time -->
        <div class="form-group">
          <label class="control-label" for="startdate">Start Date*</label>
          <div class="input-group date col-xs-4" id="startPicker" data-date="" data-date-format="mm-dd-yyyy">
            <input id="startdate" name="startdate" class="form-control" type="text" value="">
            <span class="input-group-addon icomoon-calendar" style="cursor:pointer;"></span>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label" for="starttime">Start Time*</label>
          <div class="time-quick-pick" style="position: relative;">
            <div class="short-field">
              <input type="text" class="form-control" name="starttime" id="starttime">
              <select name="startQuickPick" class="form-control" size="4">
                <?php foreach($quick_pick_times as $qpt): ?>
                <option value="<?php echo $qpt; ?>"><?php echo $qpt; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="timezoneLabel"></div>
          </div>
        </div>
        <div class="form-group">
        <label class="control-label" for="enddate">End Date*</label>
          <div class="input-group date col-xs-4" id="endPicker" data-date="" data-date-format="mm-dd-yyyy">
            <input type="text" class="form-control" value="" id="enddate" name="enddate">
            <span class="input-group-addon icomoon-calendar" style="cursor:pointer;"></span>
          </div>
        </div>
        <!-- End Time -->
        <div class="form-group">
          <label class="control-label" for="endtime">End Time*</label>
          <div class="time-quick-pick" style="position: relative;">
            <div class="short-field">
              <input type="text" class="form-control" name="endtime" id="endtime">
              <select class="form-control" name="endQuickPick" size="4">
                <?php foreach($quick_pick_times as $qpt): ?>
                <option value="<?php echo $qpt; ?>"><?php echo $qpt; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="timezoneLabel"></div>
          </div>
        </div>
        <!-- Meeting Title -->
        <div class="form-group">
          <label class="control-label" for="title">Meeting Title*</label>
          <input type="text" class="form-control" name="title" id="title" placeholder="Meeting Title" required>
        </div>
        <!-- Description -->
        <div class="form-group">
          <label class="control-label" for="summary">Description of Meeting*</label>
          <textarea class="mce-editor form-control" id="summary" name="summary" rows="3" required></textarea>
        </div>
        <!-- Event Web page -->
        <div class="form-group">                  
          <label class="control-label" for="event_url">Event Information URL</label>
          <input type="url" class="form-control" id="event_url" placeholder="Event Webpage URL">
        </div>
      </div>
      <!-- Page 2 -->
      <div class="step-wrapper">
        <!-- TA Project -->
        <div class="form-group">
          <label class="control-label" for="project">TA Project*</label>
          <select id="project" class="form-control" name="project">
            <option value="0">- Select One -</option>
            <?php foreach($this->providerProjects as $project): ?>
            <option value="<?php echo $project->id; ?>"><?php echo $project->title; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
         <!-- Grant Program -->
        <div class="form-group">
          <label class="control-label" for="program">Grant Program(s)*</label>
          <p>Please select the grant programs that are eligible to attend your event:</p>
          <fieldset>
            <small>Select:  <a class="checkAll">All</a> / <a class="uncheckAll">None</a></small><br>
            <div class="row">
              <div class="col-sm-6">
                <?php 
                  $records_in_column = ceil(count($this->grantPrograms) / 2);
                  for($i = 0; $i < count($this->grantPrograms); $i++): ?>
                  <label class="checkbox">
                    <input type="checkbox" id="grantProgram" name="grantPrograms[]" value="<?php echo $this->grantPrograms[$i]->id; ?>"><?php echo $this->grantPrograms[$i]->name; ?>
                  </label>
                    <?php if($i+1 == $records_in_column): ?>
                      </div><div class="col-sm-6">
                    <?php endif; ?>
                  <?php endfor; ?>
              </div>
            </div>
          </fieldset>
        </div>      
        <!-- OVW Approval -->
        <div class="form-group">
          <label class="control-label">OVW Approval</label>
          <p>Is this meeting OVW approved?</p>
            <fieldset id="approved" class="radio btn-group" style="padding: 0;">
              <input id="approved0" type="radio" value="0" name="approved" checked="">
               <label class="btn btn-default" for="approved0">No</label>
              <input id="approved1" type="radio" value="1" name="approved">
                <label class="btn btn-default" for="approved1">Yes</label>
            </fieldset>
        </div>  
      </div>
      <!-- Page 3 -->
      <div class="step-wrapper">
        <!-- Topic Area -->
        <div class="form-group">
          <label class="control-label">Topic Area(s)*<br><small>(Check all that apply)</small>
          </label>
          <fieldset>
            <small>Select:  <a class="checkAll">All</a> / <a class="uncheckAll">None</a></small><br>
            <?php foreach($this->topicAreas as $filter_topic_areas): ?>
            <label class="checkbox">
              <input type="checkbox" name="topicAreas[]" value="<?php echo $filter_topic_areas->id; ?>"> <?php echo $filter_topic_areas->name; ?>
            </label>
            <?php endforeach; ?>
          </fieldset>
        </div>
        <!-- Target Audience -->
        <div class="form-group">
          <label class="control-label" for="targetAudience">Target Audiences(s)*</label>
          <p>Please select each target audience to which your event will appeal:</p>
          <fieldset>
            <small>Select:  <a class="checkAll">All</a> / <a class="uncheckAll">None</a></small><br>
            <div class="row">
              <div class="col-sm-6">
              <?php
                $records_in_column = ceil(count($this->targetAudiences) / 2);
                for($i = 0; $i < count($this->targetAudiences); $i++): ?>
                <label class="checkbox">
                  <input type="checkbox" name="targetAudiences[]" value="<?php echo $this->targetAudiences[$i]->id; ?>"><?php echo str_replace('&', '&amp;', $this->targetAudiences[$i]->name); ?>
                </label>
                  <?php if($i+1 == $records_in_column): ?>
                  </div><div class="col-sm-6">
                  <?php endif; ?>
                <?php endfor; ?>
              </div>
            </div>
          </fieldset>
        </div>   
      </div>
      <!-- Page 4 -->
      <div class="step-wrapper">
        <!-- Registration Choice -->
        <div class="form-group">
          <label class="control-label">Registration Assistance*</label>
          <p>Will we be handling registration for your event?</p>
          <fieldset id="regchoice" class="radio btn-group" style="padding: 0;">
            <input id="regchoice0" type="radio" value="0" name="regchoice" checked="">
            <label class="btn btn-default" for="regchoice0">No</label>
            <input id="regchoice1" type="radio" value="1" name="regchoice">
            <label class="btn btn-default" for="regchoice1">Yes</label>
          </fieldset>
        </div>
        <!-- Registration URL --> 
        <div class="form-group">
          <label class="control-label">Registration URL</label>
          <p>If No, please enter the URL for your registration form.</p>
          <input type="url" class="form-control" id="registration_url" placeholder="Registration URL">
        </div>
        <!-- Registration Type -->        
        <div class="form-group">
          <label class="control-label">Registration Type*</label>
          <p>Will registration be open to anyone or by invitation only?</p>
          <fieldset id="open" class="radio btn-group" style="padding: 0;">
            <input id="open0" type="radio" value="0" name="open" checked />
            <label class="btn btn-default" for="open0">Invite Only</label>
            <input id="open1" type="radio" value="1" name="open" />
            <label class="btn btn-default" for="open1">Open</label>
          </fieldset>
        </div>
        <!-- Assistance -->
        <div class="form-group">
          <label for="assistance" >Please desribe what type of assistance you are requesting.</label>
         <textarea class="mce-editor form-control" id="assistance" name="assistance" rows="3"></textarea>
        </div>   
        <!-- Comments -->
        <div class="form-group">
          <label for="comments" >Additional Comments</label>
          <textarea class="mce-editor form-control" id="comments" name="comments" rows="3"></textarea>
        </div>
      </div>
      <div>
        <div class="pull-left">Step <span id="currentStep">4</span> of 4</div>
          <a class="btn btn-success" id="previousBlock"><span class="icomoon-arrow-left"></span> Prev</a>
          <a class="btn btn-success" id="nextBlock">Next <span class="icomoon-arrow-right"></span></a>
          <a class="btn btn-primary" id="submitButton"><span class="icomoon-checkmark"></span> Submit</a>
        </div>
      </div>     
    </form>
  </div>
</div>