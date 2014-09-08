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
//$grantPrograms = Ta_calendarHelper::getGrantPrograms();
$grantPrograms;

// get the current date as of midnight
$today = new DateTime('now', $calTimezone);
$today->setTime(0,0,0);

// figure out some basic facts about the month to display
$tmpDate = new DateTime($calDate, $calTimezone);
$curDay = $tmpDate->format('j');
$curWeekDay = $tmpDate->format('w');
if($curWeekDay){
	$tmpDate->modify('last Sunday');
}
$firstDate = clone $tmpDate;
$tmpDate->modify('next Saturday');
$lastDate = $tmpDate;

// get the dates needed for the sql query, adjusted to the selected timezone
$firstDaySQL = $firstDate;
$firstDaySQL->setTimezone(new DateTimeZone('UTC'));
$firstDaySQL = $firstDaySQL->format('Y-m-d 00:00:00');
$lastDaySQL = $lastDate;
$lastDaySQL->setTimezone(new DateTimeZone('UTC'));
$lastDaySQL = $lastDaySQL->format('Y-m-d 00:00:00');

// get the calendar events from the database
$events = Ta_calendarHelper::getEvents($permission_level, $filters, $firstDaySQL, $lastDaySQL, $calTimezone);

// Check if there was no result, and if so, tell then user
if(empty($events)): ?>
	<div class="alert">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<h4>Warning!</h4>
		<p>There are no events which match your date and filter selections.</p>
	</div>
<?php endif; ?>

<div class="calendar-view" id="cv-week">
	<?php
	foreach($events as $key => $event):
		// determine if any events are 'all day'
		$interval = $event->start->diff($event->end);
		if($interval->format('h') > 6){
			echo '<div class="calendar-event all-day ' . strtolower($event->start->format('l')) . (array_key_exists($event->type, $eventTypes) ? strtolower($eventTypes[$event->type]['name']) : '') . ($event->end < $today ? ' past' : '') . '">
				<span class="time">' . $event->start->format('g:i') . substr($event->start->format('a'), 0 , -1) . '</span>' . (array_key_exists($event->type, $eventTypes) ? $eventTypes[$event->type]['name'] : '') . '
			</div>';			
			
			// remove this event so it isn't printed below
			unset($events[$key]);
		}	
	endforeach;
	?>
	<table style="margin-left: 2px;" class="cal-week">
		<thead>
			<tr>
				<td style="width:50px;">&nbsp;</td>
				<td style="width:125px;">Sun 3/3</td>
				<td style="width:125px;">Mon 3/4</td>
				<td style="width:125px;">Tue 3/5</td>
				<td style="width:125px;">Wed 3/6</td>
				<td style="width:125px;">Thu 3/7</td>
				<td style="width:125px;">Fri 3/8</td>
				<td style="width:125px;">Sat 3/9</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</thead>
	</table>
	<div id="calendar-week-pane" style="height: 461px; margin-top: 15px; position: relative; overflow-x: hidden; overflow-y: scroll; width: 100%">
		<?php
			foreach($events as $event){
				$interval = $event->start->diff($event->end);
				$hours = $interval->format('h');
				echo '<div class="calendar-event all-day ' . strtolower($event->start->format('l')) . ' ' . $event->start->format('ag') . 'hr' . ($hours >= 1 ? $hours : '0.5' ) . (array_key_exists($event->type, $eventTypes) ? strtolower($eventTypes[$event->type]['name']) : '') . ($event->end < $today ? ' past' : '') . '">
					<span class="time">' . $event->start->format('g:i') . substr($event->start->format('a'), 0 , -1) . '</span>' . (array_key_exists($event->type, $eventTypes) ? $eventTypes[$event->type]['name'] : '') . '
				</div>';
			}
		?>
		<table class="cal-week">
			<tbody>
				<tr class="first-half-hour">
					<td rowspan="2" style="width:50px;">12am</td>
					<td style="width:124px;">&nbsp;</td>
					<td style="width:124px;">&nbsp;</td>
					<td style="width:124px;">&nbsp;</td>
					<td style="width:124px;">&nbsp;</td>
					<td style="width:124px;">&nbsp;</td>
					<td style="width:124px;">&nbsp;</td>
					<td style="width:124px;">&nbsp;</td>
				</tr>
				<tr class="second-half-hour">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="first-half-hour">
					<td rowspan="2">1am</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="second-half-hour">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="first-half-hour">
					<td rowspan="2">2am</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="second-half-hour">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="first-half-hour">
					<td rowspan="2">3am</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="second-half-hour">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="first-half-hour">
					<td rowspan="2">4am</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="second-half-hour">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="first-half-hour">
					<td rowspan="2">5am</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="second-half-hour">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="first-half-hour">
					<td rowspan="2">6am</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="second-half-hour">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="first-half-hour">
					<td rowspan="2">7am</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="second-half-hour">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="first-half-hour">
					<td rowspan="2">8am</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="second-half-hour">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="first-half-hour">
					<td rowspan="2">9am</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="second-half-hour">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="first-half-hour">
					<td rowspan="2">10am</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="second-half-hour">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="first-half-hour">
					<td rowspan="2">11am</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="second-half-hour">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="first-half-hour">
					<td rowspan="2">12pm</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="second-half-hour">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="first-half-hour">
					<td rowspan="2">1pm</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="second-half-hour">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="first-half-hour">
					<td rowspan="2">2pm</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="second-half-hour">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="first-half-hour">
					<td rowspan="2">3pm</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="second-half-hour">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="first-half-hour">
					<td rowspan="2">4pm</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="second-half-hour">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="first-half-hour">
					<td rowspan="2">5pm</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="second-half-hour">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="first-half-hour">
					<td rowspan="2">6pm</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="second-half-hour">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="first-half-hour">
					<td rowspan="2">7pm</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="second-half-hour">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="first-half-hour">
					<td rowspan="2">8pm</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="second-half-hour">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="first-half-hour">
					<td rowspan="2">9pm</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="second-half-hour">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="first-half-hour">
					<td rowspan="2">10pm</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="second-half-hour">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="first-half-hour">
					<td rowspan="2">11pm</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="second-half-hour">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>