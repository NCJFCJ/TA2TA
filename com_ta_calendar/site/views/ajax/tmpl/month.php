<?php
/**
 * @version     1.3.0
 * @package     com_ta_calendar
 * @copyright   Copyright (C) 2013-2014 NCJFCJ. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */
 
// start
$micro_start = microtime();

// no direct access
defined('_JEXEC') or die; 

// require the helper file
require_once(JPATH_COMPONENT . '/helpers/ta_calendar.php');

// variables
$popovers = array();

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
<?php endif; ?>

<div class="calendar-view" id="cvMonth">
	<div class="cal-month">
		<div class="cal-head">
			<div>Sun</div>
			<div>Mon</div>
			<div>Tue</div>
			<div>Wed</div>
			<div>Thu</div>
			<div>Fri</div>
			<div>Sat</div>
		</div>
		<?php 
		$weekDayCount = 0;
		for($day = 1 - $firstFallsOn; $day <= $daysInMonth + (6 - $lastFallsOn); $day++): 
			// first, check if we need to open a row
			if($weekDayCount == 0):?>
				<div class="cal-week">
			<?php endif; ?>
					<div class="cal-day <?php echo ($today->format('n') == $curMonth && $today->format('j') == $day) ? ' today' : ''; ?>">
						<div class="day-num<?php echo ($day < 1 || $day > $daysInMonth ? ' notCurMonth' : ''); ?>">
							<?php
								// print the day of month
								if($day < 1){
									// day in past month
									echo $daysInLastMonth + $day;
								}elseif($day > $daysInMonth){
									// day in next month
									echo $day - $daysInMonth;
								}else{
									echo $day;
								}
							?>
						</div>
						<?php foreach($events as $event):
							if($event->start->format('j') == $day): ?>
							<div class="calendar-event <?php echo (array_key_exists($event->type, $eventTypes) ? strtolower($eventTypes[$event->type]['name']) : '') . ($event->end < $today ? ' past' : '') . ($event->approved ? '' : ' unapproved'); ?>" data-event-type="<?php echo (array_key_exists($event->type, $eventTypes) ? $eventTypes[$event->type]['name'] : ''); ?>" data-event-id="<?php echo $event->id; ?>" data-title="<?php echo (array_key_exists($event->type, $eventTypes) ? $eventTypes[$event->type]['name'] : ''); ?>">
								<span class="time"><?php echo $event->start->format('g:ia'); ?></span><span class="hidden-xs hidden-sm"><?php echo (array_key_exists($event->type, $eventTypes) ? $eventTypes[$event->type]['name'] : ''); ?></span>
							</div>
						<?php
								// configure the date string
								$dateString = '';
								if($event->start->format('Y-m-d') == $event->end->format('Y-m-d')){
									// single day
									$dateString = $event->start->format('M j, Y g:ia') . ' - ' . $event->end->format('g:ia');
								}else{
									// multi-day
									$dateString = $event->start->format('M j, Y g:ia') . ' - ' . $event->end->format('M j, Y g:ia');
								}
						
								// create the popover content
								$popovers[] = '<div class="popover-content-wrapper" style="display: none;" data-popover-event-id="' . $event->id . '"><b>' . $event->title . '</b><br>' . $dateString . '<br>' . $event->org_name . '<br><br> <a class="btn btn-sm btn-default view-event" data-event-id="' . $event->id . '"><span class="icomoon-search"></span> Details</a>' . ($event->org == $user_org ? ' <a class="btn btn-sm btn-primary edit-event" data-event-id="' . $event->id . '"><span class="icomoon-edit"></span> Edit</a> <a class="btn btn-sm btn-danger delete-event" data-event-id="' . $event->id . '"><span class="icomoon-remove"></span> Delete</a>' : '') . '</div>';
							endif;
						endforeach; ?>	
					</div>
			<?php
			// check if we need to close a row, if not, just increment the count
			if($weekDayCount == 6):?>
				</div>
			<?php $weekDayCount = 0;
			else:
				$weekDayCount++;
			endif;
		endfor; ?>
	</div>
	<?php 
		foreach($popovers as $popover){
			echo $popover;
		} ?>
</div>