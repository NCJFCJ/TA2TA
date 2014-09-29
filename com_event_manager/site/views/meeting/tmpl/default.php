<?php
/**
 * @package     com_event_manager
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */
 
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

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

?>
<script type = "text/javascript">
var currentStep = 1;
jQuery(function($){
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

  /**
   * Function to change current step/pagination.
   *
   * @param int currentStep   current step/pg number.
   */
  function changeStep(currentStep){
    $('.step-wrapper').hide();
    $('.step-wrapper')[currentStep-1].show();
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



  /** ----- Meeting Registration Form Live Validation ----- **/

    /* --- Page 1 --- */

//  // start date
//      $('#startdate').change(function(){
//    ta2ta.validate.date($(this),3);
//      });

//  // start time
//      $('#starttime').change(function(){
//        ta2ta.validate.time($(this),3);
//      });

//      // end date
//      $('#enddate').change(function(){
//        ta2ta.validate.date($(this),3);
//      });

//  // end time
//      $('#endtime').change(function(){
//        ta2ta.validate.time($(this),3);
//      });

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

    /* --- Page 2 --- */

    // project
//  $('#project').change(function(){
//    ta2ta.validate.hasValue($(this),3);
//  });
//
//  // event_url
//  $('#event_url').change(function(){
//    if($(this).val()){
//      ta2ta.validate.url($(this), 3);
//    }
//  });

//  // registration_url
//  $('#registration_url').change(function(){
//    if($(this).val()){
//      ta2ta.validate.url($(this), 3);
//    }
//  });
      
    /**
     * These are the validation procedures for each page of the edit popup. Triggers the display of error messages.
     * @return boolean Each function returns false if there is a validation error, true otherwise
     */
//  var pageValidation = {
//    1: function(){
//      var rtn = true;
//      
//      // type
//      if(!ta2ta.validate.hasValue($('#type'),1)){
//        errors.push('You must select an event type.');
//        rtn = false;
//      }

//      // start date
//      var startdate = $('#startdate').val();
//      if(ta2ta.validate.hasValue($('#startdate'),1)){
//        if(!ta2ta.validate.date($('#startdate'), 1)){
//          errors.push('The start date you entered is invalid (format: mm-dd-yyyy).');
//          rtn = false;
//        }
//      }else{
//        errors.push('You must select a start date.');
//        rtn = false;
//      }
//      
//      // start time
//      var starttime = $('#starttime').val();
//      if(ta2ta.validate.hasValue($('#starttime'),1)){
//        if(!ta2ta.validate.time($('#starttime'),1)){
//          errors.push('The start time you entered is invalid (example: 8:30am).');
//          rtn = false;
//        }
//      }else{
//        errors.push('You must select a start time.');
//        rtn = false;
//      }
        
      // end date
//      var enddate = $('#enddate').val();
//      if(ta2ta.validate.hasValue($('#enddate'),1)){
//        if(!ta2ta.validate.date($('#enddate'), 1)){
//          errors.push('The end date you entered is invalid (format: mm-dd-yyyy).');
//          rtn = false;
//        }
//      }else{
//        errors.push('You must select an end date.');
//        rtn = false;
//      }
        
//       // end time
//       var endtime = $('#endtime').val();
//       if(ta2ta.validate.hasValue($('#endtime'),1)){
//         if(!ta2ta.validate.time($('#endtime'),1)){
//           errors.push('The end time you entered is invalid (example: 3:30pm).');
//           rtn = false;
//         }
//       }else{
//         errors.push('You must select an end time.');
//         rtn = false;
//       }
        
        // end must be after start
//       var startDateObj = new Date(parseInt(startdate.substr(6,4)), parseInt(startdate.substr(0,2)) - 1, parseInt(startdate.substr(3,2)), (starttime.substring(0,starttime.indexOf(':')) != '12' && starttime.substr(-2) == 'pm' ? parseInt(starttime.substring(0,starttime.indexOf(':'))) + 12 : parseInt(starttime.substring(0,starttime.indexOf(':')))), parseInt(starttime.substr(starttime.indexOf(':') + 1, 2)));
//       var endDateObj = new Date(parseInt(enddate.substr(6,4)), parseInt(enddate.substr(0,2)) - 1, parseInt(enddate.substr(3,2)), (endtime.substr(-2) == 'pm' ? parseInt(endtime.substring(0,endtime.indexOf(':'))) + 12 : parseInt(endtime.substring(0,endtime.indexOf(':')))), parseInt(endtime.substr(endtime.indexOf(':') + 1, 2)));
//       if(startDateObj.getTime() >= endDateObj.getTime()){
//         errors.push('You must enter an end date and time that is after your start date and time.');
//         ta2ta.bootstrapHelper.showValidationState($('#enddate'), 'error', true);
//         ta2ta.bootstrapHelper.showValidationState($('#endtime'), 'error', true);
//         rtn = false;
//       }
});        
</script>

<!-- START REG FORM _____________________________________________________ -->
<h3>Meeting Request Form</h3>
<p>To request a meeting, please provide the following information:</p> 
<div class="row">
  <div class="col-sm-8 col-md-6">
    <form role="form">   
      <!-- Page 1 -->
      <div class="step-wrapper">
        <!-- Meeting Start Date and Time -->
        <div class="form-group">
          <label class="control-label" for="startdate">Start Date*</label>
          <div class="input-group date" id="startPicker" data-date="" data-date-format="mm-dd-yyyy">
            <input id="startdate" name="startdate" class="input-date form-control" type="text" value="">
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
          </div>
          <div class="timezoneLabel"></div>
        </div>
        <div class="form-group">
        <label class="control-label" for="enddate">End Date*</label>
          <div class="input-group" id="endPicker" data-date="" data-date-format="mm-dd-yyyy">
            <input type="text" class="input-date form-control" value="" id="enddate" name="enddate">
            <span class="add-on icomoon-calendar" style="cursor:pointer;"></span>
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
          </div>
          <div class="timezoneLabel"></div>
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
          <textarea class="form-control" rows="3" id="assistance" name="assistance" maxlength="255"></textarea>
        </div>   
        <!-- Comments -->
        <div class="form-group">
          <label for="comments" >Additional Comments</label>
          <textarea class="form-control" rows="3" id="comments" name="comments" maxlength="255"></textarea>
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