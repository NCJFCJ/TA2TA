<?php
/**
 * @package     com_ta_calendar
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @author      NCJFCJ - http://ncjfcj.org
 */
 
// no direct access
defined('_JEXEC') or die;

// require the helper file
require_once(JPATH_COMPONENT . '/helpers/ta_calendar.php');

/* Get the permission level
 * 0 = Public (view only)
 * 1 = TA Provider (restricted to adding and editing own)
 * 2 = Administrator (full access and ability to edit)
 */
$permission_level = Ta_calendarHelper::getPermissionLevel();

// get the event types
$eventTypes = Ta_calendarHelper::getEventTypes();
$grantPrograms = Ta_calendarHelper::getGrantPrograms();

// get the current date as of midnight
$today = new DateTime('now', $calTimezone);
$today->setTime(0,0,0);

// figure out some basic facts about the month to display
$tmpDate = new DateTime($calDate, $calTimezone);
$curDay = $tmpDate->format('j');
$curMonth = $tmpDate->format('n');
$tmpDate->modify('first day of this month');
$firstFallsOn = $tmpDate->format('w');
$daysInMonth = $tmpDate->format('t');
$tmpDate->modify('last day of this month');
$lastFallsOn = $tmpDate->format('w');
$tmpDate->modify('first day of last month');
$daysInLastMonth = $tmpDate->format('t');

// get the dates needed for the sql query, adjusted to the selected timezone
$tmpDate->modify('first day of next month');
$tmpDate->setTimezone(new DateTimeZone('UTC'));
$tmpDate->modify('first day of this month');
$firstDaySQL = $tmpDate->format('Y-m-d 00:00:00');
$tmpDate->modify('last day of this month');
$lastDaySQL = $tmpDate->format('Y-m-d 23:59:59');

// get the calendar events from the database
$events = Ta_calendarHelper::getEvents($permission_level, $filters, $firstDaySQL, $lastDaySQL, $calTimezone);

// get the user's organization
$user_org = Ta_calendarHelper::getUserOrg();

// Check if there was no result, and if so, tell then user
if(empty($events)): ?>
	<div class="alert alert-warning">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<h4>Warning!</h4>
		<p>There are no events which match your date and filter selections.</p>
	</div>
<?php else: ?>
	<div class="calendar-view" id="cvList">
		<?php foreach($events as $event): ?>
		<div class="row-fluid list-event webinar">
			<div class="list-date-tab col-sm-2 col-md-1">
				<div class="cv-date-week-day"><?php echo $event->start->format('l'); ?></div>
				<div class="cv-date-month"><?php echo $event->start->format('M'); ?></div>
				<div class="cv-date-day"><?php echo $event->start->format('j'); ?></div>
				<div class="cv-date-year"><?php echo $event->start->format('Y'); ?></div>
				<div class="cv-start-time"><?php echo $event->start->format('g:i a'); ?></div>
			</div>
			<div class="list-event-detail col-sm-10 col-md-11">
				<h3><a class="view-event" data-event-id="<?php echo $event->id; ?>" style="color: #333; cursor: pointer; text-decoration: none"><?php echo $event->title; ?></a></h3>
				<p class="time-range">
				<?php 
					// configure the date string
					$dateString = '';					
					if($event->start->format('Y-m-d') == $event->end->format('Y-m-d')){
						// single day
						$dateString = $event->start->format('M j, Y g:ia') . ' - ' . $event->end->format('g:ia') . ' ' . $event->timezone_abbr;
					}else{
						// multi-day
						$dateString = $event->start->format('M j, Y g:ia') . ' - ' . $event->end->format('M j, Y g:ia') . ' ' . $event->timezone_abbr;
					}

					echo $dateString;
				?></p>
				<p class="host-org">Host Organization: <!--<a href="javascript:void(0);">--><?php echo $event->org_name; ?><!-- <i class="icon-share"></i></a>--></p>
				<p><?php echo $event->summary; ?></p>	
				<a class="btn btn-sm btn-default view-event" data-event-id="<?php echo $event->id; ?>"><span class="icomoon-search"></span> Details</a>
				<?php if($event->org == $user_org): ?>
					&nbsp;<a class="btn btn-sm btn-primary edit-event" data-event-id="<?php echo $event->id; ?>"><span class="icomoon-edit"></span> Edit</a> <a class="btn btn-sm btn-danger delete-event" data-event-id="<?php echo $event->id; ?>"><span class="icomoon-remove"></span> Delete</a>
				<?php endif; ?>
			</div>
			<div style="clear:both;"></div>
		</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>