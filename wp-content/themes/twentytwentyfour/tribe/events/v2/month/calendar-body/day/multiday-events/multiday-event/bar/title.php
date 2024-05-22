<?php
/**
 * View: Month View - Single Multiday Event Title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/month/calendar-body/day/multiday-events/multiday-event/bar/title.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @since 5.1.1
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 *
 * @version 5.1.1
 */

?>
<!-- <h3 class="tribe-events-calendar-month__multiday-event-bar-title tribe-common-h8">
	<?php //echo $event->title; // phpcs:ignore  ?> 
</h3> -->


<?php
/**
 * View: Month View - Calendar Event Title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/month/calendar-body/day/calendar-events/calendar-event/title.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTICLE_LINK_HERE}
 *
 * @version 5.0.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */

$pending_string='';

$pending = tribe_get_custom_fields($event->ID);
				
if( $pending["OVW Approved"] == 'Yes' || $pending["OVW Approved"] == '1' ) {
	$pending_string = '<span style="color:darkgreen; background:white; border:2px solid darkgreen; border-radius:50%; padding:0 2px;">&#x2714;</span>'; 
}
elseif( $pending["OVW Approved"] == 'No' || $pending["OVW Approved"] == '0' ) {
	//Do nothing
	//$pending_string = '<span style="color:darkred; background:white; border:2px solid darkred; border-radius:50%; padding:0 5px; font-weight:bolder">?</span>'; 
}


?>
<h3 class="tribe-events-calendar-month__calendar-event-title tribe-common-h8 tribe-common-h--alt">
	<a
		href="<?php echo esc_url( $event->permalink ); ?>"
		title="<?php echo esc_attr( $event->title ); ?>"
		rel="bookmark"
		class="tribe-events-calendar-month__calendar-event-title-link tribe-common-anchor-thin"
		data-js="tribe-events-tooltip"
		data-tooltip-content="#tribe-events-tooltip-content-<?php echo esc_attr( $event->ID ); ?>"
		aria-describedby="tribe-events-tooltip-content-<?php echo esc_attr( $event->ID ); ?>"
	>
		<?php
		// phpcs:ignore
		echo substr($event->title, 0, 30) . " [...] " . $pending_string;
		?>
	</a>
</h3>
