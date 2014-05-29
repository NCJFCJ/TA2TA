<?php
/**
 * @version     1.3.0
 * @package     com_ta_calendar
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */
 
// no direct access
defined('_JEXEC') or die;

// require the helper file
require_once(JPATH_COMPONENT . '/helpers/ta_calendar.php');

// determine the media images path
$media_images = '/media/com_ta_calendar/images/';

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

/* Get the permission level
 * 0 = Public (view only)
 * 1 = TA Provider (restricted to adding and editing own)
 * 2 = Administrator (full access and ability to edit)
 */
$permission_level = Ta_calendarHelper::getPermissionLevel();

// determine the current view
$currentView = (isset($this->userSettings->view) ? $this->userSettings->view : 'month');

// determine the user's timezone, and adopt it
$timezone = 'America/Los_Angeles';
$timezoneAbbr = 'PST';
if(isset($this->userSettings->timezone)){
	foreach($this->timezones as $tz){
		if($tz->abbr == $this->userSettings->timezone){
			$timezone = $tz->description;
			$timezoneAbbr = $tz->abbr;
			break;
		}
	}
}

// determine the current date
$currentDateTime = new DateTime('now', new DateTimeZone($timezone));
$currentDate = $currentDateTime->format('Ymd');

// https?
$https = false;
if(!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]!=="off"){
	$https = true;
}
?>
<script type="text/javascript" src="/media/editors/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
	// document ready	
	jQuery(function($){
		// variables
		var currentDate = '<?php echo $currentDate; ?>';
		var currentView = '<?php echo $currentView; ?>';
		var editStep = 1;
		var errors = new Array();
		var monthNamesLong = new Array(
			'January',
			'February',
			'March',
			'April',
			'May',
			'June',
			'July',
			'August',
			'September',
			'October',
			'November',
			'December'
		);
		var monthNamesShort = new Array(
			'Jan',
			'Feb',
			'Mar',
			'Apr',
			'May',
			'Jun',
			'Jul',
			'Aug',
			'Sep',
			'Oct',
			'Nov',
			'Dec'
		);
		var request;
		var timezone = '<?php echo $timezone; ?>';
		var queuedMessage = '';

		$('#refreshLink').click(function(){
			loadCalendar();
		});

		/**
		 * Loads the contents of the calendar by making an AJAX call to the appropriate script and providing the proper data
		 * 
		 * @return null Prints to screen
		 */
		var loadAjaxRequest;
		function loadCalendar(){
			// stop any active AJAX request
			if(typeof loadAjaxRequest != 'undefined'){
				loadAjaxRequest.abort();
			}

			// remove existing popover
			removePopover();

			// show the loading graphic and clear the old content
			$('#calendarContent').hide().html('');
			$('#calendarPane .loading').show();
				
			// grab the filters, format them as a jSON blob
			var approved = $("#filterList input[name='approved[]']:checked").map(function(){
				return $(this).val();
			}).get();
			var eventTypes = $("#filterList input[name='eventTypes[]']:checked").map(function(){
				return $(this).val();
			}).get();
			var grantPrograms = $("#filterList input[name='grantPrograms[]']:checked").map(function(){
				return $(this).val();
			}).get();
			var targetAudiences = $("#filterList input[name='targetAudiences[]']:checked").map(function(){
				return $(this).val();
			}).get();
			var topicAreas = $("#filterList input[name='topicAreas[]']:checked").map(function(){
				return $(this).val();
			}).get();
					
			var jsonFilters = {};
			jsonFilters['approved'] = approved;
			jsonFilters['eventTypes'] = eventTypes;
			jsonFilters['grantPrograms'] = grantPrograms;
			jsonFilters['targetAudiences'] = targetAudiences;
			jsonFilters['topicAreas'] = topicAreas;
			
			// send the AJAX request
			loadAjaxRequest = $.ajax({
				data: {
					calDate: currentDate,
					calTimezone: timezone,
					calView: currentView,
					filters: jsonFilters,
				},
				dataType: 'html',
				type: 'POST',
				url: '<?php echo 'http' . ($https ? 's' : '') . '://'. $_SERVER['HTTP_HOST'] . '/index.php?option=com_ta_calendar&task=getCalendar';?>',
			});

			// fire when the ajax request completes successfully
			loadAjaxRequest.done(function(msg){
				// hide the loading indicator and show the content
				$('#calendarPane .loading').hide();
				$('#calendarContent').html(msg).show();				
				
				// checked for and display any queued message
				if(queuedMessage instanceof Object && queuedMessage.hasOwnProperty('message')){
					// only continue if there is a message
					if(queuedMessage.hasOwnProperty('message')){
						var aliveTime = queuedMessage.hasOwnProperty('aliveTime') ? queuedMessage.aliveTime : 0;
						var block = queuedMessage.hasOwnProperty('block') ? queuedMessage.block : false;
						var dismissable = queuedMessage.hasOwnProperty('dismissable') ? queuedMessage.dismissable : false;
						var type = queuedMessage.hasOwnProperty('type') ? queuedMessage.type : '';
		
						// show the message
						calendarAlert(queuedMessage.message, type, block, dismissable, aliveTime);
					}

					// clear the queued message
					queuedMessage = '';
				}
			});

			// fire if the ajax request fails
			loadAjaxRequest.fail(function(jqXHR, textStatus){
				$('#calendarPane .loading').hide();
				$('#calendarContent').html('<div class="alert alert-block alert-error"><h4>Error!</h4>There was an error loading the calendar. Please try again later and contact us if this issue persists.</div>').show();
			});
		}
		
		function printCalendar(){
			var mywindow = window.open('', 'calendar', 'height=400,width=600');
			mywindow.document.write('<html><head><title>Calendar</title>');
			mywindow.document.write('<link rel="stylesheet" href="/templates/ta2ta/styles/css/template.css" type="text/css" />');
			mywindow.document.write('</head><body >');
			mywindow.document.write($('#calendarPane').html());
			mywindow.document.write('</body></html>');

			mywindow.print();
			mywindow.close();

			return true;
		}


				
		/**
		 * Displays an alert box above the calendar, removes any open popovers
		 * @param message string The HTML message to be displayed
		 * @param string The type of alert to render (error, warning, success, or info) (default warning)
		 * @param boolean Whether or not to display this message in block format (default false)
		 * @param boolean Whether or not the alert is dismissable (default false)
		 * @param int How long to display the alert in milliseconds before automatically closing (default disabled)
		 */
		function calendarAlert(message, type, block, dismissable, aliveTime){
			ta2ta.bootstrapHelper.showAlert($('#calendarPane'), message, type, block, dismissable, aliveTime);
			removePopover();
		}

		/**
		 * Updates the current calendar date and then reloads the calendar
		 */
		function updateDate(){
			// create a date object based on the current date
			var date = getDateObjFromCurrentDate();
			var dateString = '';
			
			// change the date display based on the current view
			switch(currentView){
				case 'month' :
					dateString = monthNamesLong[date.getMonth()] + ' ' + date.getFullYear();
					break;
				case 'week' :
				case 'list' :
					// figure out the start date for this week
					var startDate = new Date(date.setDate(date.getDate() - date.getDay()));
					var endDate = new Date(date.setDate(date.getDate() + 6));
					
					// build the date string
					dateString = monthNamesShort[startDate.getMonth()] + ' ' + startDate.getDate();
					if(startDate.getFullYear() != endDate.getFullYear()){
						// no need to show the year on the first date
						dateString += ', ' + startDate.getFullYear();
					}
					dateString += ' - ' + monthNamesShort[endDate.getMonth()] + ' ' + endDate.getDate() + ', ' + endDate.getFullYear();
				default :
					break;
			}
			
			// inject the new date string
			$('#currentDateRange').text(dateString);
			
			// load the calendar
			loadCalendar();
		}
		
		/**
		 * Creates a date object from the current date variable
		 */
		function getDateObjFromCurrentDate(){
			return new Date(currentDate.substr(0,4),currentDate.substr(4,2)-1,currentDate.substr(6,2));
		}
		
		var curPopover = null;
		/**
		 * Removes the current popover, if any
		 */
		function removePopover(){
			// remove any existing popover
			if(curPopover !== null){
				curPopover.popover('destroy');
				curPopover = null;
			}
		}
		
		/**
		 * Writes the current date given a date object
		 */
		function writeCurrentDate(date){
			if(!(date instanceof Date)){
				alert('you must pass a date object to the writeCurrentDate function');
				return false;
			}

			// get the month (remember JS counts from 0) and add a leading 0 if needed
			var month = ('0' + (date.getMonth() + 1)).slice(-2);

			// get the day, add a leading 0 if needed
			var day = ('0' + date.getDate()).slice(-2);

			currentDate = date.getFullYear() + month + day;
		}

		// hide all modal form blocks
		$('.modal-form-block').hide();
		
		/**
		 * Checks all checkboxes within the same fieldset on click
		 */
		$('#filterList .checkAll').click(function(){
			loadCalendar();
		});

		/**
		 * Unchecks all checkboxes within the same fieldset on click
		 */
		$('#filterList .uncheckAll').click(function(){
			loadCalendar();
		});

		/**
		 * Advances to the next date based on the view
		 */
		$('#calNext').click(function(){
			// create a date object based on the current date
			var date = getDateObjFromCurrentDate();
			
			// change the date based on the current view
			switch(currentView){
				case 'month' :
					// add one month
					date = new Date(date.getFullYear(), date.getMonth() + 1, 1);
					break;
				case 'week' :
				case 'list' :
					// add seven days
					date = new Date(date.getFullYear(), date.getMonth(), date.getDate() + 7);
					break;				
				default :
					break;
			}
			
			// store this new date
			writeCurrentDate(date);
			
			// update the date indicator
			updateDate();
		});

		/**
		 * Advances to the previous date based on the view
		 */
		$('#calPrev').click(function(){
			// create a date object based on the current date
			var date = getDateObjFromCurrentDate();
			
			// change the date based on the current view
			switch(currentView){
				case 'month' :
					// subtract one month
					date = new Date(date.getFullYear(), date.getMonth()-1, 1);
					break;
				case 'week' :
				case 'list' :
					// subtract seven days
					date = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 7);
				default :
					break;
			}
			
			// store this new date
			writeCurrentDate(date);
			
			// update the date indicator
			updateDate();
		});
		
		/**
		 * Resets the calendar date back to today
		 */
		$('#calToday').click(function(){
			// get the current date
			var date = new Date();
			
			// store this new date
			writeCurrentDate(date);
			
			// update the date indicator
			updateDate();
		});

		/**
		 * Changes the current calendar view
		 */
		$('.calendar-view-button').click(function(){
			// update which button is active
			$('.calendar-view-button').removeClass('active');
			$(this).addClass('active');
			
			// update the view
			currentView = $(this).data('view');
			
			// update the date indicator
			updateDate();
		});

		/**
		 * Changes the current timezone
		 */
		$('.timezone').click(function(){
			// update the timezone
			timezone = $(this).data('timezone');
			
			// show the newly selected timezone as the default
			$('#currentTimezone').text($(this).data('timezone-abbr'));
			
			// show the new timezone in the edit form
			$('.timezoneLabel').text($(this).data('timezone-abbr'));

			// reload the calendar
			loadCalendar();
		});
		
		/**
		 * Run every time a filter is updated
		 */
		$('#filterList input:checkbox').change(function(){
			loadCalendar();
		});
		
		/* --- Popovers --- */
		$('#calendarPane').on('click', '.calendar-event', function(event){
			// remove existing popover
			removePopover();
			
			// create the new popover
			var eventId = $(this).data('event-id');	
			$(this).popover({
				container: 'body',
				content: function(){
					return $("*[data-popover-event-id='" + eventId + "']").html();
				},
				html: true,
				placement: 'top',
				trigger: 'manual',
			});

			// open the popover
			$(this).popover('show');

			// add a close button to the popup
			$('.popover-title').append('<button type="button" class="close">&times;</button>');

			// save this as the current popover
			curPopover = $(this);

			// listens to all clicks and removes the popover
			$(document).on('click', function(event){
				removePopover();
			});

			// listen for clicks on the close button
			$('.popover').on('click', '.close', function(event){
				removePopover();
			});

			// prevent clicks on the popover from closing it
			$('body').on('click', '.popover, #deletePopup, #editPopup, #viewPopup', function(event){
				event.stopPropagation();
			});

			// stop this event from triggering removePopover on the document
			event.stopPropagation();
		});
		
		/* --- Detail View --- */
		$(document.body).on('click', '.view-event', function(){
			showEventDetails($(this).data('event-id'));	
		});
		
		/**
		 * Hide the modal
		 */
		$('#viewPopup .closePopup').click(function(){
			$('#viewPopup').modal('hide');
		});
		
		/**
		 * Initialize the detail view popup
		 */
		$('#viewPopup').modal({
			show: false,
		});

		/* --- Run on load --- */
		
		// mark the appropriate view as active
		$('.calendar-view-button[data-view="' + currentView + '"]').addClass('active');
		
		// update the date display
		updateDate();

		/** 
		 * Displays the details for an event to the user
		 *
		 * @param int The ID of the event to display
		 */
		function showEventDetails(eventId){
			if(typeof eventId === 'number'
				&& eventId % 1 === 0
				&& eventId > 0){
				// hide the content pane
				$('#viewPopupContent').hide();

				// show the loading pane
				$('#viewPopup .loading').show();

				// show the modal
				$('#viewPopup').modal('show');
				
				// make AJAX request
				var request = $.ajax({
					data: {calTimezone: timezone, event: eventId, edit: 0},
					dataType: 'json',
					type: 'POST',
					url: '<?php echo 'http' . ($https ? 's' : '') . '://'. $_SERVER['HTTP_HOST'] . '/index.php?option=com_ta_calendar&task=getEvent';?>'
				});

				request.always(function(response, textStatus, jqXHR){
					// hide the loading pane
					$('#viewPopup .loading').hide();

					// show the content pane
					$('#viewPopupContent').show();	
				});

				// fires when the AJAX call completes
				request.done(function(response, textStatus, jqXHR){
					// check if this has an error
					if(response.status == 'success'){
						// set the event type in the header
						$('#viewEventType').html(response.data.type_name);

						// set the approved state in the header
						if(response.data.approved_status == 1){
							$('#viewEventApprovedState').html('<span style="color:green;font-size:12pt;">(<span class="icomoon-checkmark"></span> OVW Approved)</span>');
						}else{
							$('#viewEventApprovedState').html('<span style="color:red;font-size:12pt;">(Pending Approval)</span>');
						}

						// populate the table data
						$('#viewEventTitle').html(response.data.title);
						$('#viewEventOrg').html(response.data.org_name);
						$('#viewEventProject').html(response.data.provider_project_name);
						$('#viewEventDate').html(response.data.date_string);
						$('#viewEventSummary').html(response.data.summary);

						// grant programs
						if((response.data.grant_programs).length > 1){
							var gpHTML = '<div><div style="float:left;width:48%"><ul style="margin:0 0 0 15px;">';
							var colSplit = Math.ceil((response.data.grant_programs).length / 2);
							$.each(response.data.grant_programs, function(index,value){
								if(index == colSplit){
									gpHTML += '</ul></div><div style="float:left;margin-left:15px;width:48%"><ul style="margin-left:15px;">';
								}
								gpHTML += '<li>' + value.name + '</li>';
							});
							gpHTML += '</ul></div></div>';
							$('#viewEventPrograms').html(gpHTML);
						}else{
							$('#viewEventPrograms').html(response.data.grant_programs[0].name);
						}

						// buttons
						if(response.data.event_url != '' || (response.data.open && response.data.registration_url != '')){
							$('#viewEventButtonsWrapper').show();

							var btnsHTML = '';

							// event url
							if(response.data.event_url != ''){
								btnsHTML += '<a href="' + response.data.event_url + '" class="btn btn-info" target="_blank"><span class="icomoon-earth"></span> Visit Website</a>';
							}

							// registration url
							if(response.data.open && response.data.registration_url != ''){
								btnsHTML += '<a href="' + response.data.registration_url + '" class="btn btn-info" target="_blank"' + (btnsHTML == '' ? '' : ' style="margin-left: 15px;"') + '><span class="icomoon-signup"></span> Register Online</a>';
							}
							$('#viewEventButtons').html(btnsHTML);
						}else{
							$('#viewEventButtonsWrapper').hide();
						}

						// correct row colors
						$('#viewPopup table.table-striped tr:visible').each(function(index){
						    $(this).children('td').css("background-color", (index % 2 ? "transparent" : "#F9F9F9"));
						});

						// topic areas
						if((response.data.topic_areas).length > 1){
							var topicHTML = '<div><div style="float:left;width:48%"><ul style="margin:0 0 0 15px;">';
							var colSplit = Math.ceil((response.data.topic_areas).length / 2);
							$.each(response.data.topic_areas, function(index,value){
								if(index == colSplit){
									topicHTML += '</ul></div><div style="float:left;margin-left:15px;width:48%"><ul style="margin-left:15px;">';
								}
								topicHTML += '<li>' + value.name + '</li>';
							});
							topicHTML += '</ul></div></div>';
							$('#viewEventTopicAreas').html(topicHTML);
						}else{
							$('#viewEventTopicAreas').html(response.data.topic_areas[0].name);
						}

						// target audiences
						if((response.data.target_audiences).length > 1){
							var audienceHTML = '<div><div style="float:left;width:48%"><ul style="margin:0 0 0 15px;">';
							var colSplit = Math.ceil((response.data.target_audiences).length / 2);
							$.each(response.data.target_audiences, function(index,value){
								if(index == colSplit){
									audienceHTML += '</ul></div><div style="float:left;margin-left:15px;width:48%"><ul style="margin-left:15px;">';
								}
								audienceHTML += '<li>' + value.name + '</li>';
							});
							audienceHTML += '</ul></div></div>';
							$('#viewEventTargetAudiences').html(audienceHTML);
						}else{
							$('#viewEventTargetAudiences').html(response.data.target_audiences[0].name);
						}						

						// admin information
						if(response.data.created){
							// created
							$('#viewEventCreated').html(response.data.created + ' by ' + response.data.created_by);

							// modified
							if(response.data.modified != '0000-00-00 00:00:00'){
								$('#viewEventModified').html(response.data.modified + ' by ' + response.data.modified_by);
							}else{
								$('#viewEventModified').html('');
							}

							// deleted
							if(response.data.deleted != '0000-00-00 00:00:00'){
								$('#viewEventDeleted').html(response.data.deleted + ' by ' + response.data.deleted_by);
							}else{
								$('#viewEventDeleted').html('');
							}

							// approved
							if(response.data.approved != '0000-00-00 00:00:00'){
								$('#viewEventApproved').html(response.data.approved + ' by ' + response.data.approved_by);
							}else{
								$('#viewEventApproved').html('');
							}

							// show the history display
							$('#viewEventHistory').show();
						}else{
							// hide the history display
							$('#viewEventHistory').hide();
						}

						// check if user can edit and show the appropriate options
						if(response.data.can_edit){
							// set the id of the buttons
							$('#authorized-user-actions .btn').data('event-id', response.data.id);

							// show the buttons
							$('#authorized-user-actions').show();
						}else{
							// hide the buttons
							$('#authorized-user-actions').hide();
						}
					}else{
						queuedMessage = new Object;
						queuedMessage.block = false;
						queuedMessage.dismissable = true;
						queuedMessage.message = response.message;
						queuedMessage.type = 'error';
						loadCalendar();
						$('#viewPopup').modal('hide');
					}
				});

				// catch if the AJAX call fails completelly
				request.fail(function(jqXHR, textStatus, errorThrown){
					// notify the user that an error occured
					queuedMessage = new Object;
					queuedMessage.block = false;
					queuedMessage.dismissable = true;
					queuedMessage.message = 'Server error. AJAX connection failed.';
					queuedMessage.type = 'error';
					loadCalendar();
					$('#viewPopup').modal('hide');
				});
			}	
		}

		<?php if($permission_level > 0): ?>
		// Edit specific scripts
		var editWasViewing = false;
		var pastGrantPrograms = false;
		var showAllSteps = false;
		var stepDescription = new Array(
			'Basic Information',
			'More Details',
			'Topic Area',
			'Grant Program',
			'Target Audience',
			'Saved'
		);
		var timeOptions = [];
		/**
		 * Prevent clicking of any items with the disabled class
		 */
					
		$(document.body).on('click', '.disabled', function(event){
			event.preventDefault();
		});

		/** ----- Calendar Event Form Live Validation ----- **/

		/* --- Page 1 --- */

		// type
		$('#type').change(function(){
			ta2ta.validate.hasValue($(this),3);
		});

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

		/* --- Page 2 --- */

		// project
		$('#project').change(function(){
			ta2ta.validate.hasValue($(this),3);
		});
	
		// event_url
		$('#event_url').change(function(){
			if($(this).val()){
				ta2ta.validate.url($(this), 3);
			}
		});

		// registration_url
		$('#registration_url').change(function(){
			if($(this).val()){
				ta2ta.validate.url($(this), 3);
			}
		});
			
		/**
		 * These are the validation procedures for each page of the edit popup. Triggers the display of error messages.
		 * @return boolean Each function returns false if there is a validation error, true otherwise
		 */
		var pageValidation = {
			1: function(){
				var rtn = true;
				
				// type
				if(!ta2ta.validate.hasValue($('#type'),1)){
					errors.push('You must select an event type.');
					rtn = false;
				}

				// start date
				var startdate = $('#startdate').val();
				if(ta2ta.validate.hasValue($('#startdate'),1)){
					if(!ta2ta.validate.date($('#startdate'), 1)){
						errors.push('The start date you entered is invalid (format: mm-dd-yyyy).');
						rtn = false;
					}
				}else{
					errors.push('You must select a start date.');
					rtn = false;
				}
				
				// start time
				var starttime = $('#starttime').val();
				if(ta2ta.validate.hasValue($('#starttime'),1)){
					if(!ta2ta.validate.time($('#starttime'),1)){
						errors.push('The start time you entered is invalid (example: 8:30am).');
						rtn = false;
					}
				}else{
					errors.push('You must select a start time.');
					rtn = false;
				}
				
				// end date
				var enddate = $('#enddate').val();
				if(ta2ta.validate.hasValue($('#enddate'),1)){
					if(!ta2ta.validate.date($('#enddate'), 1)){
						errors.push('The end date you entered is invalid (format: mm-dd-yyyy).');
						rtn = false;
					}
				}else{
					errors.push('You must select an end date.');
					rtn = false;
				}
				
				// end time
				var endtime = $('#endtime').val();
				if(ta2ta.validate.hasValue($('#endtime'),1)){
					if(!ta2ta.validate.time($('#endtime'),1)){
						errors.push('The end time you entered is invalid (example: 3:30pm).');
						rtn = false;
					}
				}else{
					errors.push('You must select an end time.');
					rtn = false;
				}
				
				// end must be after start
				var startDateObj = new Date(parseInt(startdate.substr(6,4)), parseInt(startdate.substr(0,2)) - 1, parseInt(startdate.substr(3,2)), (starttime.substring(0,starttime.indexOf(':')) != '12' && starttime.substr(-2) == 'pm' ? parseInt(starttime.substring(0,starttime.indexOf(':'))) + 12 : parseInt(starttime.substring(0,starttime.indexOf(':')))), parseInt(starttime.substr(starttime.indexOf(':') + 1, 2)));
				var endDateObj = new Date(parseInt(enddate.substr(6,4)), parseInt(enddate.substr(0,2)) - 1, parseInt(enddate.substr(3,2)), (endtime.substr(-2) == 'pm' ? parseInt(endtime.substring(0,endtime.indexOf(':'))) + 12 : parseInt(endtime.substring(0,endtime.indexOf(':')))), parseInt(endtime.substr(endtime.indexOf(':') + 1, 2)));
				if(startDateObj.getTime() >= endDateObj.getTime()){
					errors.push('You must enter an end date and time that is after your start date and time.');
					ta2ta.bootstrapHelper.showValidationState($('#enddate'), 'error', true);
					ta2ta.bootstrapHelper.showValidationState($('#endtime'), 'error', true);
					rtn = false;
				}
				
				//title
				var titleError = false;
				if(ta2ta.validate.hasValue($('#title'))){
					if(!ta2ta.validate.minLength($('#title'),10)){
						errors.push('The title you entered is too short. It must be at least 10 characters long.');
						titleError = true;
					}
					if(!ta2ta.validate.maxLength($('#title'),255)){
						errors.push('The title you entered is too long. Please reduce it to a maximum of 255 characters.');
						titleError = true;
					}

					if(!ta2ta.validate.title($('#title'))){
						errors.push('The title you entered is invalid (hint: allowed special characters are @ & - ( ) [ ] : , . \ / " \' )');
						titleError = true;
					}
				}else{
					errors.push('You must enter a title for your event.');
					titleError = true;
				}

				if(titleError){
					ta2ta.bootstrapHelper.showValidationState($('#title'), 'error', true);
					rtn = false;
				}
				
				// show error messages
				if(!rtn){
					editShowErrors();
				}
				
				// return the result
				return rtn;
			},
			2: function(){
				var rtn = true;
				var insecure = false;
	
				// TA Project
				if(!ta2ta.validate.hasValue($('#project'),1)){
					errors.push('You must select a TA project.');
					rtn = false;
				}
				
				// summary
				var summary = tinyMCE.activeEditor.getContent();
				if(summary.length < 20){
					ta2ta.bootstrapHelper.showValidationState($('.mce-tinymce'), 'error', true);
					errors.push('You must enter a summary with sufficient detail.');
					rtn = false;
				}
				if(summary.length > 1500){
					ta2ta.bootstrapHelper.showValidationState($('.mce-tinymce'), 'error', true);
					errors.push('You summary is too verbose. Please cut it back to no more than 1500 characters.');
					rtn = false;
				}
	
				// event_url
				if(($('#event_url').val()).length){
					if(!ta2ta.validate.url($('#event_url'),1)){
						errors.push('The event URL you entered is not valid.');
						rtn = false;
					}
				}
	
				// registration_url
				var registration_url = $('#registration_url').val();
				if(registration_url.length){
					if(ta2ta.validate.url($('#registration_url'),1)){
						// perform an additional security check
						if(registration_url.substr(0,8) != 'https://'){
							insecure = true;
						}
					}else{
						errors.push('The registration URL you entered is not valid.');
						rtn = false;
					}
				}
	
				// show error messages
				if(!rtn){
					editShowErrors();
				}else if(insecure){
					$('#editPopup').modal('hide');
					$('#insecureURLPopup').modal('show');
					rtn = false;
				}
	
				// return the result
				return rtn;
			},
			3: function(){
				var rtn = true;
				
				// topicAreas
				if(!ta2ta.validate.checkboxes($('#editPopup input[name="topicAreas[]"]:checked'), 1)){
					errors.push('You must choose at least one topic area.');
					rtn = false;
				}
				
				// show error messages
				if(!rtn){
					editShowErrors();
				}
				
				// return the result
				return rtn;
			},
			4: function(){
				var rtn = true;
				
				// grantPrograms
				if(!ta2ta.validate.checkboxes($('#editPopup input[name="grantPrograms[]"]:checked'), 1)){
					errors.push('You must choose at least one grant program.');
					rtn = false;
				}
				
				// show error messages
				if(!rtn){
					editShowErrors();
				}
				
				// return the result
				return rtn;
			},
			5: function(){
				var rtn = true;
				
				// targetAudiences
				if(!ta2ta.validate.checkboxes($('#editPopup input[name="targetAudiences[]"]:checked'), 1)){
					errors.push('You must choose at least one target audience.');
					rtn = false;
				}

				// show error messages
				if(!rtn){
					editShowErrors();
				}
				
				// return the result
				return rtn;
			}
		}
		
		/**
		 * Displays an alert box in the edit popup
		 * @param message string The HTML message to be displayed
		 * @param string The type of alert to render (error, warning, success, or info) (default warning)
		 * @param boolean Whether or not to display this message in block format (default false)
		 * @param boolean Whether or not the alert is dismissable (default false) 	
		 * @param int How long to display the alert in milliseconds before automatically closing (default disabled)
		 */
		function editAlert(message, type, block, dismissable, aliveTime){
			ta2ta.bootstrapHelper.showAlert($('#editPopup .modal-body'), message, type, block, dismissable, aliveTime);
		}

		/**
		 * Removes an alert from the edit popup
		 */
		function editRemoveAlert(){
			ta2ta.bootstrapHelper.removeAlert($('#editPopup .modal-body'));
		}
		
		/**
		 * Limits the end times selectable by the user
		 */
		function editLimitEndTimeSelect(){
			// reset all options by first removing them, then adding new
			$('#endQuickPick option').each(function(){
				$(this).remove();
			});
			
			$.each(timeOptions, function(i, v){
				$('#endQuickPick').append('<option value="' + v.value + '">' + v.text + '</option>');
			});
			
			$('#endQuickPick').children('option').show();
			$('#endQuickPick').children('option').prop('disabled', false);
			
			// check if the dates are the same
			if(($('#startPicker').datepicker('getDate')).valueOf() == ($('#endPicker').datepicker('getDate')).valueOf()){
				/* note: while this could all be distilled down to a simple each statement, that would fail to take
				 * into account all user input possibilities, such as entries not present in the select field, like 5:50p
				 */
				
				// establish variables
				var startTime = $('#starttime').val();
				var firstEndTime = '';

				// determine what the first available option should be
				var startHour = parseInt(startTime.substring(0, startTime.indexOf(':')));
				var startMaxMinutes = parseInt(startTime.substr(startTime.indexOf(':') + 1, 1));
				var startAmpm = startTime.substr(-2);
				
				// check if we are past the half hour
				if(startMaxMinutes >= 3){
					var tmpStartHour = startHour + 1;
					// check if the am or pm needs to be adjusted
					if(tmpStartHour >= 12){
						// adjustment is required
						if(startAmpm == 'am'){
							// switch to pm
							firstEndTime = tmpStartHour + ':00pm';
						}
					}else{
						// no adjustment needed, use the next hour
						firstEndTime = tmpStartHour + ':00' + startAmpm;
					}
				}else{
					// not past the half hour, use the same hour just past the half
					firstEndTime = startHour + ':30' + startAmpm;
				}
				
				// hide all options which are prior to the chosen start time
				var endOptions = $('#endQuickPick').children('option');
				endOptions.each(function(index){
					$(this).remove();
					if($(this).val() == startTime){
						return false;
					}
				});
			}
		}
	
		/**
		 * Updates the event type select field to the specified value
		 *
		 * @param string The name of the eventType (must match exactly)
		 */
		function editSetEventType(eventType){
			$('#type option').prop('selected', false);
			$('#type option').each(function(index){
				if($(this).text() == eventType){
					$('#type').val($(this).val());
					$('#type').change();
					return false;
				}
			});
		}
		
		/**
		 * Display error messages to the user
		 */
		function editShowErrors(){
			// make sure we have errors before processing
			if(errors.length){
				editAlert(ta2ta.bootstrapHelper.constructErrorMessage(errors), 'error');
				
				// clean the array
				errors = new Array();
			}
		}
		
		/**
		 * Sets the current edit step and adjusts the edit dialog display accordingly
		 */
		function editUpdateStep(step){	
			// set the step
			editStep = step;
			
			// update the heading
			$('#editHeadingDescription').text(stepDescription[editStep - 1]);
			
			// update the footer
			if(editStep == 6){
				$('#editPopup .modal-status-text').hide();
			}else{
				$('#editPopup .modal-status-text').show();
				$('#editPopup #currentStep').text(editStep);
			}
			
			// determine whether to show the previous button in the footer
			if(editStep == 1 || editStep == 6){
				$('#previousBlock').hide();
			}else{
				$('#previousBlock').show();
			}
			
			// determine whether to show the submit and next buttons in the footer
			if(editStep == 5){
				$('#nextBlock').hide();
				$('#submitButton').show();
			}else if(editStep == 6){
				$('#nextBlock').hide();
				$('#submitButton').hide();
			}else{
				$('#nextBlock').show();
				$('#submitButton').hide();
			}
			
			if(editStep == 6){
				$('#closeButton').html('Close');
			}else{
				$('#closeButton').html('Cancel');
			}


			// check if we need to display the project error
			if(editStep == 1 
				&& 0 == <?php echo count($this->providerProjects); ?>){
				// no good, the user cannot proceed
				$('#step1Form').hide();

				// hide all buttons
				$('#previousBlock').hide();
				$('#nextBlock').hide();
				$('#submitButton').hide();
				$('.modal-status-text').hide();
			}else{
				// everything is normal
				$('#projectError').hide();
			}

			// show the warning on step 4 if needed
			if(editStep == 4
				&& pastGrantPrograms){
				editAlert('These grant programs have changed to match the TA Project you selected. Please double check them.', 'info', false);
				pastGrantPrograms = false;
			}
			
			// show the appropriate edit block
			if(!showAllSteps){
				// hide all modal blocks
				$('.modal-form-block').hide();
				
				// show the current modal block			
				$('.modal-form-block:eq(' + (editStep - 1) + ')').show();

				if(editStep == 2){
					// work around for chosen bug where hidden selects have a 0 width
					$('#project').chosen('destroy');
					$('#project').chosen({
						disable_search_threshold: 10
					});
				}
			}
		}
		
		// TinyMCE
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
			var newDate = new Date(e.date)
			$('#endPicker').datepicker('update', newDate);
			$('#endPicker').datepicker('setStartDate', newDate);
		}).data('datepicker');

		$('#endPicker').datepicker({
			autoclose: true,
			format: 'mm-dd-yyyy'
		}).on('changeDate', function(e) {
			editLimitEndTimeSelect();
		}).data('datepicker');

		$('#starttime').change(function(){
			editLimitEndTimeSelect();
		});
		
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
		
		/* --- Popup Fuctions --- */
		
		/**
		 * Update the popup heading if the type is changed
		 */
		$('#type').change(function(){
			$('#editHeadingEventType').text($(this).find(':selected').text());
		});
		
		/**
		 * Advances to the next block in the modal form
		 */
		$('#nextBlock').click(function(){
			// clear any old errors
			editRemoveAlert();
			ta2ta.bootstrapHelper.hideAllValidationStates();
			
			// validate this step
			if(pageValidation[editStep]()){
				// update the step
				editUpdateStep(editStep + 1);
			}
		});

		/**
		 * Show or hide the Registration URL field based on the registration type
		 */

		function toggleRegistrationURLField(){
			if($("input[name='open']:checked").val() == 1){
				$('#registrationURLControlGroup').show();
			}else{
				$('#registrationURLControlGroup').hide().val('');
			}
		}

		// listen for changes
		$("input[name='open']").change(toggleRegistrationURLField);

		// run on document ready
		toggleRegistrationURLField();

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
					if($("#editPopup input[name='grantPrograms[]']:checked").length){
						pastGrantPrograms = true;
					}

					// check the appropriate boxes
					checkEditCheckboxes('grantPrograms',response.data);
				}else{
					editAlert(response.message, response.status);
				}
			});

			// catch if the AJAX call fails completelly
			request.fail(function(jqXHR, textStatus, errorThrown){
				// notify the user that an error occured
				editAlert('Server error. AJAX connection failed.', 'error', true);
			});
		});

		/**
		 * Fire when the edit event form is submitted
		 */
		$('#submitButton').click(function(){
			editRemoveAlert();
			ta2ta.bootstrapHelper.hideAllValidationStates();
			
			// validate the last step
			if(pageValidation[5]()){
				// variables
				var inputs = $('#editPopup').find('input, select, button');
				var validationErr = false;

				// serialize the data in the form
				$('#summary').val(tinyMCE.activeEditor.getContent());
				var serialData = $('#editEventForm').serialize();

				// I don't know why, but the event_url and registration_url are not being serialized, adding them back in manually
				serialData += '&event_url=' + $('#event_url').val() + '&registration_url=' + $('#registration_url').val();
				
				// disable all form elements to prevent double entry
				inputs.prop('disabled', true);
				$('.closePopup').addClass('disabled');
				$('#nextBlock').addClass('disabled');
				$('#previousBlock').addClass('disabled');
				$('#submitButton').addClass('disabled');

				// reset all error indicators
				
				// Set the submit button to say loading
				var dots = 0;
				$('#submitButton').html('Loading');
				var loadingDots = setInterval(function(){
					switch(dots){
						case 0:
							$('#submitButton').html('Loading&nbsp;&nbsp;&nbsp;');
							break;
						case 1:
							$('#submitButton').html('Loading.&nbsp;&nbsp;');
							break;
						case 2:
							$('#submitButton').html('Loading..&nbsp;');
							break;
						case 3:
							$('#submitButton').html('Loading...');
							break;
						default:
							$('#submitButton').html('Loading&nbsp;&nbsp;&nbsp;');
							dots = 0;
							break;
					}
					dots++;
				}, 500);
				
				// make AJAX request
				var request = $.ajax({
					data: serialData,
					dataType: 'json',
					type: 'POST',
					url: '<?php echo 'http' . ($https ? 's' : '') . '://'. $_SERVER['HTTP_HOST'] . '/index.php?option=com_ta_calendar&task=saveEvent';?>',
				});
				
				// fires when the AJAX call completes
				request.done(function(response, textStatus, jqXHR){
					// check if this has an error
					if(response.status == 'success'){
						editUpdateStep(6);
						loadCalendar();
					}else{
						editAlert(response.message, response.status);
					}
				});

				// catch if the AJAX call fails completelly
				request.fail(function(jqXHR, textStatus, errorThrown){
					// notify the user that an error occured
					editAlert('Server error. AJAX connection failed.', 'error', true);
				});

				// no matter what happens, enable the form again
				request.always(function (){
					// enable fields
					inputs.prop('disabled', false);
					$('.closePopup').removeClass('disabled');
					$('#nextBlock').removeClass('disabled');
					$('#previousBlock').removeClass('disabled');
					$('#submitButton').removeClass('disabled');

					// reset the submit button
					window.clearInterval(loadingDots);
					$('#submitButton').html('<span class="icomoon-checkmark"></span> Submit');
				});
			}
		});

		/**
		 * Checks the appropriate checkboxes for a given field with the given values
		 * 
		 * @params string The name of the checkbox (sans brackets)
		 * @params array The values corresponding to the checkboxes to be checked
		 */

		function checkEditCheckboxes(name, values){
			// uncheck all
			$("#editPopup input[name='" + name + "[]']").prop('checked', false);

			// check selected
			$.each(values, function(index,value){
				$("#editPopup input[name='" + name + "[]'][value='" + value + "']").prop('checked', true);
			});
		}
		
		/**
		 * Goes back to the previous block in the modal form
		 */
		$('#previousBlock').click(function(){
			// clear any old errros
			editRemoveAlert();
			
			// update the step
			editUpdateStep(editStep - 1);
		});

		/**
		 * Initialize the modal popup box
		 */
		$('#editPopup').modal({
			backdrop: 'static',
			show: false
		});
		
		/**
		 * Run when the edit popup opens
		 */
		$('#editPopup').on('shown.bs.modal', function(e){
			pastGrantPrograms = false;
			// work around for chosen bug where hidden selects have a 0 width
			$('#type').chosen('destroy');
			$('#type').chosen({
				disable_search_threshold: 10,
				width: '150px'
			});
		});

		/**
		 * Initialize the insecure URL popup
		 */
		$('#insecureURLPopup').modal({
			backdrop: 'static',
			show: false,
		});
		
		/**
		 * Advances to the next page after the user confirms
		 */	
		$('#insecureURLPopup #confirm').click(function(){
			$('#insecureURLPopup').modal('hide');
			$('#editPopup').modal('show');
			editUpdateStep(3);
		});
		
		/**
		 * Hide the modal and simply reopoen the edit modal
		 */
		$('#insecureURLPopup .closePopup').click(function(){
			$('#insecureURLPopup').modal('hide');
			$('#editPopup').modal('show');
		});

		/**
		 * Initializes and displays the event creation modal
		 */
		$('.new-event-btn').click(function(){
			// variables
			var eventType = $(this).text();
			editWasViewing = false;

			// set the heading headings and other visual cues
			$('#editHeadingAction').text('New');
			$('#editHeadingEventType').text(eventType);

			// set the step to 1
			editUpdateStep(1);

			// reset the past grant programs flag
			pastGrantPrograms = false;

			// set the event type
			editSetEventType(eventType);

			// set the id to 0
			$('#id').val(0);
			
			// set the timezone
			$('#editPopup #timezone').val(timezone);

			// popup the modal
			$('#editPopup').modal('show');
		});
		
		// on load, update the edit box timezone
		$('#editPopup .timezoneLabel').text($('#currentTimezone').text());
		
		// save the value of all time options
		$('#editPopup #endQuickPick option').each(function(){
			timeOptions.push({value: $(this).val(), text: $(this).text()});
		});
		
		/* --- Delete Event --- */
		$(document.body).on('click', '.delete-event', function(){
			var eventId = $(this).data('event-id');
			var wasViewing = false;

			// check if the view was open
			if($('#viewPopup').hasClass('in')){
				wasViewing = true;
			}

			// hide the view popup
			$('#viewPopup').modal('hide');

			// show the delete popup
			$('#deletePopup').modal('show');
			
			/**
			* Advances to the next page after the user confirms
			*/	
			$('#deletePopup .btn-danger').click(function(){
				$('#deletePopup').modal('hide');
				deleteEvent(eventId);
			});
				
			/**
			 * Hide the modal and simply reopoen the edit modal
			 */
			$('#deletePopup .closePopup').click(function(){
				$('#deletePopup').modal('hide');

				// reopen the view if needed
				if(wasViewing){
					$('#viewPopup').modal('show');
				}
			});
		});
		
		/**
		* Initialize the delete popup
		*/
		$('#deletePopup').modal({
			backdrop: 'static',
			show: false,
		});
		
		// cleanup listeners when the modal is closed
		$('#deletePopup').on('hidden', function (){
			$('#deletePopup .btn-danger').unbind('click');
			$('#deletePopup .closePopup').unbind('click');
		});
				
		function deleteEvent(id){
			// make AJAX request
			var request = $.ajax({
				data: {event:id},
				dataType: 'json',
				type: 'POST',
				url: '<?php echo 'http' . ($https ? 's' : '') . '://'. $_SERVER['HTTP_HOST'] . '/index.php?option=com_ta_calendar&task=deleteEvent';?>'
			});
			
			// fires when the AJAX call completes
			request.done(function(response, textStatus, jqXHR){
				// check if this has an error
				if(response.status == 'success'){
					queuedMessage = new Object;
					queuedMessage.aliveTime = 5000;
					queuedMessage.block = false;
					queuedMessage.dismissable = true;
					queuedMessage.message = 'The selected event was successfully deleted.';
					queuedMessage.type = 'success';
					loadCalendar();					
				}else{
					queuedMessage = new Object;
					queuedMessage.block = false;
					queuedMessage.dismissable = true;
					queuedMessage.message = response.message;
					queuedMessage.type = 'error';
					loadCalendar();
				}
			});

			// catch if the AJAX call fails completelly
			request.fail(function(jqXHR, textStatus, errorThrown){
				// notify the user that an error occured
				queuedMessage = new Object;
				queuedMessage.block = false;
				queuedMessage.dismissable = true;
				queuedMessage.message = 'Server error. AJAX connection failed.';
				queuedMessage.type = 'error';
				loadCalendar();
			});
		}
		
		/* --- Edit Event --- */
		$(document.body).on('click', '.edit-event', function(){
			var eventId = $(this).data('event-id');
			
			// check if the view was open
			if($('#viewPopup').hasClass('in')){
				editWasViewing = true;
			}else{
				editWasViewing = false;
			}

			// hide the view popup
			$('#viewPopup').modal('hide');
			
			// make AJAX request
			var request = $.ajax({
				data: {
					calTimezone:timezone,
					event:eventId,
					edit:1
				},
				dataType: 'json',
				type: 'POST',
				url: '<?php echo 'http' . ($https ? 's' : '') . '://'. $_SERVER['HTTP_HOST'] . '/index.php?option=com_ta_calendar&task=getEvent';?>'
			});
			
			// fires when the AJAX call completes
			request.done(function(response, textStatus, jqXHR){
				// check if this has an error
				if(response.status == 'success'){
					// populate the data
					// step 1
					$('#id').val(response.data.id);
					$('#startdate').val(response.data.startdate);
					$('#starttime').val(response.data.starttime);
					$('#enddate').val(response.data.enddate);
					$('#endtime').val(response.data.endtime);
					$('#title').val(response.data.title);
					editSetEventType(response.data.type_name);

					// step 2
					tinyMCE.activeEditor.setContent(response.data.summary);
					$('#event_url').val(response.data.event_url);
					$("label[for='open" + response.data.open + "']").click();
					toggleRegistrationURLField();
					$('#registration_url').val(response.data.registration_url);
					$('#editPopup #project').val(response.data.provider_project);

					// step 3
					var topicAreasArray = [];
					$.each(response.data.topic_areas, function(index,value){
						topicAreasArray.push(value.id);
					});
					checkEditCheckboxes('topicAreas', topicAreasArray);
					console.log(response.data.approved_status);
					$("label[for='approved" + response.data.approved_status + "']").click();
					
					// step 4
					var grantProgramsArray = [];
					$.each(response.data.grant_programs, function(index,value){
						grantProgramsArray.push(value.id);
					});
					checkEditCheckboxes('grantPrograms', grantProgramsArray);

					// step 5
					var targetAudiencesArray = [];
					$.each(response.data.target_audiences, function(index,value){
						targetAudiencesArray.push(value.id);
					});
					checkEditCheckboxes('targetAudiences', targetAudiencesArray);
					
					// set the heading headings and other visual cues
					$('#editHeadingAction').text('Edit');
					$('#editHeadingEventType').text(response.data.type_name);

					// set the step to 1
					editUpdateStep(1);

					// reset the past grant programs flag
					pastGrantPrograms = false;				

					// set the timezone
					$('#editPopup #timezone').val(timezone);

					// popup the modal
					$('#editPopup').modal('show');
				}else{
					calendarAlert(response.message, 'error', false, true);
				}
			});

			// catch if the AJAX call fails completelly
			request.fail(function(jqXHR, textStatus, errorThrown){
				// notify the user that an error occured
				calendarAlert('Server error. AJAX connection failed.', 'error', false, true);
			});	
		});

		// listen for clicks on the close button 
		$('#editPopup .closePopup').click(function(){
			var eventId = parseInt($('#id').val());
			$('#editPopup').modal('hide');
			editRemoveAlert();
			$('#startPicker').datepicker('update', nowTemp);
			$('#startPicker').datepicker('setStartDate', nowTemp);
			$('#endPicker').datepicker('update', nowTemp);
			$('#endPicker').datepicker('setStartDate', nowTemp);
			$('#editEventForm')[0].reset();

			// hide all validation states
			ta2ta.bootstrapHelper.hideAllValidationStates();

			// reopen the view and refresh its data if needed
			if(editWasViewing){
				showEventDetails(eventId);
			}
		});
	<?php endif; ?>
	});
</script>
<div class="row" id="eventCalendar">
	<div class="col-sm-2" style="position: relative; z-index: 9;">
		<div class="btn-group" style="width: 100%;">
			<?php if($permission_level > 0): ?>
			<button class="btn dropdown-toggle btn-primary" data-toggle="dropdown" style="width: 100%; padding-left: 0; padding-right: 0">
				<span class="icomoon-plus-circle"></span>&nbsp; <b>Add<span class="hidden-sm"> Event</span></b>
				<span class="icomoon-arrow-down"></span>
			</button>
			<ul class="dropdown-menu">
				<?php foreach($this->eventTypes as $add_button_event_type): ?>
					<li><a data-toggle="modal" class="new-event-btn"><?php echo $add_button_event_type->name; ?></a></li>		
				<?php endforeach; ?>
			</ul>
			<?php else: ?>
				<a class="btn btn-default" href="/login.html" style="width: 100%; padding-left: 0; padding-right: 0">
					<span class="icomoon-enter"></span> Sign In
				</a>
			<?php endif; ?>
		</div>
		<br>
		<br>
		<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
			<div class="panel-group filter-list" id="filterList">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#filterList" href="#collapseOne">Topic Areas</a>
						</h4>
					</div>
					<div id="collapseOne" class="panel-collapse collapse in">
						<div class="panel-body">
							<p><small>Select:  <a class="checkAll">All</a> / <a class="uncheckAll">None</a></small></p>
							<?php foreach($this->topicAreas as $filter_topic_areas): ?>
							<label class="checkbox">
								<input type="checkbox" name="topicAreas[]" value="<?php echo $filter_topic_areas->id; ?>"<?php echo (in_array($filter_topic_areas->id, $this->userSettings->filters->topicAreas) ? '' : ' checked'); ?>> <?php echo $filter_topic_areas->name; ?>
							</label>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#filterList" href="#collapseTwo">Eligible Grant Programs</a>
						</h4>
					</div>
					<div id="collapseTwo" class="panel-collapse expanding collapse">
						<div class="panel-body">
							<p><small>Select:  <a class="checkAll">All</a> / <a class="uncheckAll">None</a></small></p>
							<div class="row">
								<div class="col-sm-4">
									<?php 
									$records_in_column = ceil(count($this->grantPrograms) / 3);
									for($i = 0; $i < count($this->grantPrograms); $i++): ?>
									<label class="checkbox">
										<input type="checkbox" name="grantPrograms[]" value="<?php echo $this->grantPrograms[$i]->id; ?>"<?php echo (in_array($this->grantPrograms[$i]->id, $this->userSettings->filters->grantPrograms) ? '' : ' checked'); ?>> <?php echo str_replace('&', '&amp;', $this->grantPrograms[$i]->name); ?>
									</label>
										<?php if($i == $records_in_column
												|| $i == $records_in_column * 2): ?>
										</div><div class="col-sm-4">
										<?php endif; ?>
									<?php endfor; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#filterList" href="#collapseThree">Target Audiences</a>
						</h4>
					</div>
					<div id="collapseThree" class="panel-collapse expanding collapse">
						<div class="panel-body">
							<p><small>Select:  <a class="checkAll">All</a> / <a class="uncheckAll">None</a></small></p>
							<div class="row">
								<div class="col-sm-4">
									<?php 
									$records_in_column = ceil(count($this->targetAudiences) / 3);
									for($i = 0; $i < count($this->targetAudiences); $i++): ?>
									<label class="checkbox">
										<input type="checkbox" name="targetAudiences[]" value="<?php echo $this->targetAudiences[$i]->id; ?>"<?php echo (in_array($this->targetAudiences[$i]->id, $this->userSettings->filters->targetAudiences) ? '' : ' checked'); ?>> <?php echo str_replace('&', '&amp;', $this->targetAudiences[$i]->name); ?>
									</label>
										<?php if($i == $records_in_column
												|| $i == $records_in_column * 2): ?>
										</div><div class="col-sm-4">
										<?php endif; ?>
									<?php endfor; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#filterList" href="#collapseFour"><img src="<?php echo $media_images; ?>color-icon.png" alt="">Type</a>
						</h4>
					</div>
					<div id="collapseFour" class="panel-collapse collapse">
						<div class="panel-body">
							<p><small>Select:  <a class="checkAll">All</a> / <a class="uncheckAll">None</a></small></p>
							<?php foreach($this->eventTypes as $filter_event_type):
								$filter_event_type_class_name = strtolower($filter_event_type->name); ?>
							<label class="checkbox">
								<input type="checkbox" name="eventTypes[]" value="<?php echo $filter_event_type->id; ?>"<?php echo (in_array($filter_event_type->id, $this->userSettings->filters->eventTypes) ? '' : ' checked'); ?>> <span class="color-key <?php echo $filter_event_type_class_name; ?>">&nbsp;</span> <?php echo $filter_event_type->name; ?>
							</label>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#filterList" href="#collapseFive"><img src="<?php echo $media_images; ?>stripe-icon.png" alt="">Status</a>
						</h4>
					</div>
					<div id="collapseFive" class="panel-collapse collapse">
						<div class="panel-body">
							<p><small>Select:  <a class="checkAll">All</a> / <a class="uncheckAll">None</a></small></p>
							<label class="checkbox status-select">
								<input type="checkbox" name="approved[]" value="1" checked="checked"><img src="<?php echo $media_images; ?>stripe-icon-empty.png" alt=""> Approved
							</label>
							<label class="checkbox status-select">
								<input type="checkbox" name="approved[]" value="0" checked="checked"><img src="<?php echo $media_images; ?>stripe-icon-small.png" alt=""> Requested
							</label>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
	<div class="col-sm-10">
		<div class="row" id="toolbar">
			<div class="col-sm-8">
				<div class="btn-group">
					<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						<span id="currentTimezone"><?php echo $timezoneAbbr; ?></span>
						<span class="icomoon-arrow-down"></span>
					</button>
					<ul class="dropdown-menu">
						<?php foreach($this->timezones as $timezone): ?>
						<li><a class="timezone" data-timezone-abbr="<?php echo $timezone->abbr; ?>" data-timezone="<?php echo $timezone->description; ?>"><?php echo $timezone->description . ' (' . $timezone->abbr; ?>)</a></li>	
						<?php endforeach; ?>
					</ul>
				</div> &nbsp;
				<button class="btn btn-default" id="calToday">Today</button> &nbsp;
				<!--<button class="btn btn-default" id="calendar-button"><span class="icomoon-calendar"></span></button> &nbsp;-->
				<div class="btn-group">
					<button class="btn btn-default" id="calPrev"><span class="icomoon-arrow-left"></span></button>
					<button class="btn btn-default" id="calNext"><span class="icomoon-arrow-right"></span></button>
				</div> &nbsp;
				<span id="currentDateRange"></span>
			</div>
			<div class="col-sm-4">
				<div class="pull-right">
					<!--<div class="btn-group hidden-md hidden-lg">
						<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							View
							<span class="icomoon-arrow-down"></span>
						</button>
						<ul class="dropdown-menu">
							<li><a class="calendar-view-button" data-view="month">Month</a></li>
							<li><a class="calendar-view-button" data-view="week">Week</a></li>
							<li><a class="calendar-view-button" data-view="list">List</a></li>
						</ul>
					</div>
					<div class="btn-group visible-md visible-lg">
						<button class="btn btn-default calendar-view-button" data-view="month">Month</button>
						<button class="btn btn-default calendar-view-button" data-view="week">Week</button>
						<button class="btn btn-default calendar-view-button" data-view="list">List</button>
					</div> &nbsp; -->
					<div class="btn-group">
						<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							More
							<span class="icomoon-arrow-down"></span>
						</button>
						<ul class="dropdown-menu">
							<!--<li><a href="javascript:printCalendar();"><span class="icomoon-print"></span> &nbsp;Print</a></li>-->
							<li><a href="javascript:void(0);" id="refreshLink"><span class="icomoon-loop"></span> &nbsp;Refresh</a></li>
							<?php if($permission_level > 0): ?>
							<li><a href="/my-account/calendar-settings.html"><span class="icomoon-cog"></span> &nbsp;Settings</a></li>
							<?php endif; ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-sm-12" id="calendarPane">
				<div class="loading">
					<img src="/templates/ta2ta/img/loading.gif" alt="Loading">
				</div>
				<div id="calendarContent"></div>
			</div>
		</div>
		<?php if($permission_level > 0): ?>
		<div class="modal fade" id="editPopup" data-backdrop="static">
			<div class="modal-dialog">
        		<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close closePopup" aria-hidden="true">&times;</button>
						<h3><span id="editHeadingAction">Add</span> <span id="editHeadingEventType">Event</span> - <span id="editHeadingDescription">Basic Information</span></h3>
					</div>
					<div class="modal-body" style="height: 350px;">
						<form class="form-horizontal" name="editEventForm" id="editEventForm" role="form">
							<input type="hidden" name="timezone" id="timezone" value="">
							<input type="hidden" name="id" id="id" value="0">
							<div class="modal-form-block">
								<div id="projectError">
									<div class="alert alert-warning">
										<h4>Hold on just a minute! You must add a project first.</h4>
										Before an event can be added to the calendar, you must first add a project to your TA Provider Directory listing. All calendar events are tied to specific projects. Projects are added in the 'Directory' section of your account. Click the button below to get started.
										<br><br>
										<a href="/my-account/directory.html?edit=0" class="btn btn-warning"><span class="icomoon-plus-circle"></span>&nbsp; Add Project</a>
									</div>
								</div>
								<div id="step1Form">
									<p>To add an event to the calendar, please provide the following information:</p>
									<div class="form-group" id="typeWrapper">
										<label class="control-label col-sm-4" for="type">Event Type*</label>
										<div class="col-sm-8">
											<select id="type" class="form-control" name="type">
												<?php foreach($this->eventTypes as $add_button_event_type): ?>
													<option value="<?php echo $add_button_event_type->id; ?>"><?php echo $add_button_event_type->name; ?></option>		
												<?php endforeach; ?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-4" for="startdate">Start Date*</label>
										<div class="col-sm-8">
											<div class="input-append date short-field" id="startPicker" data-date="" data-date-format="mm-dd-yyyy">
										    	<input id="startdate" name="startdate" class="input-date form-control" type="text" value="">
										    	<span class="add-on icomoon-calendar" style="cursor:pointer;"></span>
										 </div>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-4" for="starttime">Start Time*</label>
										<div class="col-sm-8 time-quick-pick" style="position: relative;">
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
										<label class="control-label col-sm-4" for="enddate">End Date*</label>
										<div class="col-sm-8">
											<div class="input-append date short-field" id="endPicker" data-date="" data-date-format="mm-dd-yyyy">
										    	<input type="text" class="input-date form-control" value="" id="enddate" name="enddate">
										    	<span class="add-on icomoon-calendar" style="cursor:pointer;"></span>
										 	</div>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-4" for="endtime">End Time*</label>
										<div class="col-sm-8 time-quick-pick" style="position: relative;">
											<div class="short-field">
												<input type="text" class="form-control" name="endtime" id="endtime" value="">
												<select class="form-control" name="endQuickPick" id="endQuickPick" size="4">
													<?php foreach($quick_pick_times as $qpt): ?>
													<option value="<?php echo $qpt; ?>"><?php echo $qpt; ?></option>
													<?php endforeach; ?>
												</select>
											</div>
										 	<div class="timezoneLabel"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-4" for="title">Title*</label>
										<div class="col-sm-8">
											<input type="text" class="form-control" name="title" id="title" placeholder="Title" required>
										</div>
									</div>
								</div>
							</div>
							<div class="modal-form-block">
								<div class="form-group">
									<label class="control-label col-sm-4" for="project">TA Project*</label>
									<div class="col-sm-8">
										<select id="project" class="form-control" name="project">
											<option value="0">- Select One -</option>
											<?php foreach($this->providerProjects as $project): ?>
												<option value="<?php echo $project->id; ?>"><?php echo $project->title; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-4" for="summary">Summary*</label>
									<div class="col-sm-8">
										<textarea class="mce-editor form-control" id="summary" name="summary" rows="3" required></textarea>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-4" for="event_url">Event Webpage URL</label>
									<div class="col-sm-8">
										<input type="url" class="form-control" id="event_url" placeholder="Event Webpage URL">
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-4">Registration Type*</label>
									<div class="col-sm-8">
										<fieldset id="open" class="radio btn-group" style="padding: 0;">
											<input id="open0" type="radio" value="0" name="open" checked />
											<label class="btn btn-default" class="form-control" for="open0">Invite Only</label>
											<input id="open1" type="radio" value="1" name="open" />
											<label class="btn btn-default" class="form-control" for="open1">Open</label>
										</fieldset>
									</div>
								</div>
								<div class="form-group" id="registrationURLControlGroup">
									<label class="control-label col-sm-4" for="registration_url">Registration URL</label>
									<div class="col-sm-8">
										<input type="url" class="form-control" id="registration_url" placeholder="Registration Information URL">
									</div>
								</div>
							</div>
							<div class="modal-form-block">
								<div class="form-group">
									<label class="control-label col-sm-4">Topic Area*<br><small>(Check all that apply)</small></label>
									<div class="col-sm-8">
										<fieldset>
											<small>Select:  <a class="checkAll">All</a> / <a class="uncheckAll">None</a></small><br>
											<?php foreach($this->topicAreas as $filter_topic_areas): ?>
											<label class="checkbox">
												<input type="checkbox" name="topicAreas[]" value="<?php echo $filter_topic_areas->id; ?>"> <?php echo $filter_topic_areas->name; ?>
											</label>
											<?php endforeach; ?>
										</fieldset>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-sm-4">OVW Approved*</label>
									<div class="col-sm-8">
										<fieldset id="approved" class="radio btn-group" style="padding: 0;">
											<input id="approved0" type="radio" value="0" name="approved" checked />
											<label class="btn btn-default" class="form-control" for="approved0">No</label>
											<input id="approved1" type="radio" value="1" name="approved" />
											<label class="btn btn-default" class="form-control" for="approved1">Yes</label>
										</fieldset>
									</div>
								</div>
								<br>
								<br>
								<br>
								<br>
							</div>
							<div class="modal-form-block">
								<p>Please select the grant programs that are eligible to attend your event:</p>
								<fieldset>
									<small>Select:  <a class="checkAll">All</a> / <a class="uncheckAll">None</a></small><br>
									<div class="row">
										<div class="col-sm-6">
											<?php 
											$records_in_column = ceil(count($this->grantPrograms) / 2);
											for($i = 0; $i < count($this->grantPrograms); $i++): ?>
											<label class="checkbox">
												<input type="checkbox" name="grantPrograms[]" value="<?php echo $this->grantPrograms[$i]->id; ?>"> <?php echo $this->grantPrograms[$i]->name; ?>
											</label>
												<?php if($i+1 == $records_in_column): ?>
												</div><div class="col-sm-6">
												<?php endif; ?>
											<?php endfor; ?>
										</div>
									</div>
								</fieldset>
							</div>
							<div class="modal-form-block">
								<p>Please select each target audience to which your event will appeal:</p>
								<fieldset>
									<small>Select:  <a class="checkAll">All</a> / <a class="uncheckAll">None</a></small><br>
									<div class="row">
										<div class="col-sm-6">
											<?php
											$records_in_column = ceil(count($this->targetAudiences) / 2);
											for($i = 0; $i < count($this->targetAudiences); $i++): ?>
											<label class="checkbox">
												<input type="checkbox" name="targetAudiences[]" value="<?php echo $this->targetAudiences[$i]->id; ?>"> <?php echo str_replace('&', '&amp;', $this->targetAudiences[$i]->name); ?>
											</label>
												<?php if($i+1 == $records_in_column): ?>
												</div><div class="col-sm-6">
												<?php endif; ?>
											<?php endfor; ?>
										</div>
									</div>
								</fieldset>
							</div>
							<div class="modal-form-block center" id="success">
								<img src="<?php echo $media_images; ?>star-ribbon.jpg" alt="Gold Star Award" style="width: 185px;">
								<h2>You Deserve A Gold Star!</h2>
								<p>Your event has been successfully added to the calendar and is now available for viewing. Great job!</p>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<div class="pull-left modal-status-text">Step <span id="currentStep">1</span> of 5</div>
						<a class="btn btn-default closePopup" id="closeButton">Cancel</a>&nbsp;
						<a class="btn btn-success" id="previousBlock"><span class="icomoon-arrow-left"></span> Prev</a>
						<a class="btn btn-success" id="nextBlock">Next <span class="icomoon-arrow-right"></span></a>
						<a class="btn btn-primary" id="submitButton"><span class="icomoon-checkmark"></span> Submit</a>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="insecureURLPopup" data-backdrop="static">
			<div class="modal-dialog">
        		<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close closePopup" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Insecure Link</h4>
					</div>
					<div class="modal-body">
						<p>The Registration URL you provided is not secure. You are requesting that users provide personal information over an Internet connection that can be intercepted. It is highly recommended that you use an <b>https:// (SSL secured)</b> web address. For more information, please <a href="http://websearch.about.com/od/dailywebsearchtips/qt/dnt0513.htm" target="_blank">click here</a> or contact your webmaster.</p>
					</div>
					<div class="modal-footer">
						<a class="btn btn-primary closePopup">No</a>&nbsp;
						<a class="btn btn-danger" id="confirm">Yes (not recommended)</a>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="deletePopup" data-backdrop="static">
			<div class="modal-dialog">
        		<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close closePopup" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Event Deletion Confirmation</h4>
					</div>
					<div class="modal-body">
						<p>Are you certain that you would like to delete this event? There is no undo.</p>
					</div>
					<div class="modal-footer">
						<a class="btn btn-default closePopup">Cancel</a>&nbsp;
						<a class="btn btn-danger">Yes, Delete</a>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
		<div class="modal fade" id="viewPopup" data-backdrop="static">
			<div class="modal-dialog">
        		<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close closePopup" aria-hidden="true">&times;</button>
						<h4 class="modal-title"><span id="viewEventType">Event</span> Details <span id="viewEventApprovedState"></span></h4>
					</div>
					<div class="modal-body">
						<div class="loading">
							<img src="/templates/ta2ta/img/loading.gif" alt="Loading">
						</div>
						<div id="viewPopupContent">
							<table class="table table-striped">
								<tr>
									<td style="width: 130px;">
										<strong>Title</strong>
									</td>
									<td id="viewEventTitle"></td>
								</tr>
								<tr>
									<td>
										<strong>Project</strong>
									</td>
									<td id="viewEventProject"></td>
								</tr>
								<tr>
									<td>
										<strong>Date &amp; Time</strong>
									</td>
									<td id="viewEventDate"></td>
								</tr>
								<tr>
									<td>
										<strong>Summary</strong>
									</td>
									<td id="viewEventSummary"></td>
								</tr>
								<tr>
									<td>
										<strong>Eligible Grant Programs</strong>
									</td>
									<td id="viewEventPrograms"></td>
								</tr>
								<tr id="viewEventButtonsWrapper">
									<td>&nbsp;</td>
									<td id="viewEventButtons"></td>
								</tr>
								<tr>
									<td>
										<strong>Topic Areas</strong>
									</td>
									<td id="viewEventTopicAreas"></td>
								</tr>
								<tr>
									<td>
										<strong>Target Audiences</strong>
									</td>
									<td id="viewEventTargetAudiences"></td>
								</tr>
								<tr>
									<td>
										<strong>Organization</strong>
									</td>
									<td id="viewEventOrg"></td>
								</tr>
							</table>
							<div id="viewEventHistory">
								<h3>History</h3>
								<table class="table table-striped">
									<tr>
										<td style="width: 130px;">
											<strong>Created</strong>
										</td>
										<td id="viewEventCreated"></td>
									</tr>
									<tr>
										<td>
											<strong>Last Modified</strong>
										</td>
										<td id="viewEventModified"></td>
									</tr>
									<tr>
										<td>
											<strong>Deleted</strong>
										</td>
										<td id="viewEventDeleted"></td>
									</tr>
									<tr>
										<td>
											<strong>Approved</strong>
										</td>
										<td id="viewEventApproved"></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<a class="btn btn-default closePopup"><span class="icomoon-close"></span> Close</a>
						<span id="authorized-user-actions">
							<a class="btn btn-primary edit-event"><span class="icomoon-edit"></span> Edit</a>
							<a class="btn btn-danger delete-event"><span class="icomoon-remove"></span> Delete</a>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>