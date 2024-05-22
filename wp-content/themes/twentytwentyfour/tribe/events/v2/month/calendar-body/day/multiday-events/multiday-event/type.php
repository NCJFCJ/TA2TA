<?php
/**
 * View: Month View - Day cell
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/month/calendar-body/day/cell.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 5.3.0
 *
 * @var string $today_date Today's date in the `Y-m-d` format.
 * @var string $day_date The current day date, in the `Y-m-d` format.
 * @var array $day The current day data.{
 *          @type string $date The day date, in the `Y-m-d` format.
 *          @type bool $is_start_of_week Whether the current day is the first day of the week or not.
 *          @type string $year_number The day year number, e.g. `2019`.
 *          @type string $month_number The day year number, e.g. `6` for June.
 *          @type string $day_number The day number in the month with leading 0, e.g. `11` for June 11th.
 *          @type string $day_url The day url, e.g. `http://yoursite.com/events/2019-06-11/`.
 *          @type int $found_events The total number of events in the day including the ones not fetched due to the per
 *                                  page limit, including the multi-day ones.
 *          @type int $more_events The number of events not showing in the day.
 *          @type array $events The non multi-day events on this day. The format of each event is the one returned by
 *                    the `tribe_get_event` function. Does not include the below events.
 *          @type array $featured_events The featured events on this day. The format of each event is the one returned
 *                    by the `tribe_get_event` function.
 *          @type array $multiday_events The stack of multi-day events on this day. The stack is a mix of event post
 *                              objects, the format is the one returned from the `tribe_get_event` function, and
 *                              spacers. Spacers are falsy values indicating an empty space in the multi-day stack for
 *                              the day
 *      }
 */

$event_type = get_the_terms( $event->ID, 'tribe_events_cat' );
$event_type_options = get_field( 'event_type', 'options', true );
$colors=[];
foreach($event_type_options as $event_color){
	$colors += [
		$event_color['name'] => [
			'background_color' => $event_color['background_color'], 
			'color' => $event_color['color']
			]
		];
}
?>

<h3 class="tribe-events-calendar-month__calendar-event-title tribe-common-h8 tribe-common-h--alt" style="font-style:initial; font-weight:600; border-radius:5px; text-align:center; color:<?php echo $colors[$event_type[0]->name]['color'];?>; background:<?php echo $colors[$event_type[0]->name]['background_color']?>; ">
	<?php
	echo $event_type[0]->name;
	?>
</h3>
