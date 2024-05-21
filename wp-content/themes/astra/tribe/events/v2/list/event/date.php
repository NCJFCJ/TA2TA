<?php
/**
 * View: List View - Single Event Date
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/list/event/date.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @since 4.9.9
 * @since 5.1.1 Move icons into separate templates.
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 *
 * @version 5.1.1
 */
use Tribe__Date_Utils as Dates;

$event_id = get_the_ID();

$event_date_attr = $event->dates->start->format( Dates::DBDATEFORMAT );
$timezone = get_post_meta( $event_id, '_EventTimezoneAbbr', true  );
$pending = get_post_meta($event_id, '_ecp_custom_5', true);
$pending_string="";

if( $pending == 'Yes' ) {
	$pending_string = '<span style="color:darkgreen;">Approved</span>'; 
}
elseif( $pending == 'No' ) {
	$pending_string = '<span style="color:darkred;">Pending</span>'; 
}

?>
<div class="tribe-events-calendar-list__event-datetime-wrapper tribe-common-b2">
	<?php $this->template( 'list/event/date/featured' ); ?>
	<time class="tribe-events-calendar-list__event-datetime" datetime="<?php echo esc_attr( $event_date_attr ); ?>">
		<?php echo $event->schedule_details->value() ; ?>
	</time>
</div>
<?php echo $pending_string; ?>
