<?php
/**
 * View: List View - Single Event Title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/list/event/title.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */
// Get the event ID.
$event_id = get_the_ID();
 
$timezone = get_post_meta( $event_id, '_EventTimezoneAbbr ', true  );
?>

<div class="tribe-events-calendar-list__event-datetime-wrapper tribe-common-b2">
	<span class="tribe-events-calendar-list__event-datetime" ?>
		<?php echo $timezone; ?>
	</span>
</div>


