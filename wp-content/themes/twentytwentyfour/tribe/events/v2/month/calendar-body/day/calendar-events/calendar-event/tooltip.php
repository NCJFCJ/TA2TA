<?php
/**
 * View: Month View - Calendar Event Tooltip
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/month/calendar-body/day/calendar-events/calendar-event/tooltip.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 4.9.13
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */

?>

<?php
//Check if the logged in user is part the event organization
$user = wp_get_current_user();
$user_id = get_current_user_id();
$cf = get_field('organization_for_user', "user_{$user_id}");
$ef = tribe_get_custom_fields( $event->ID )['Organization'];

?>
<div class="tribe-events-calendar-month__calendar-event-tooltip-template tribe-common-a11y-hidden">
	<div
		class="tribe-events-calendar-month__calendar-event-tooltip"
		id="tribe-events-tooltip-content-<?php echo esc_attr( $event->ID ); ?>"
		role="tooltip"
	>
		<?php $this->template( 'month/calendar-body/day/calendar-events/calendar-event/tooltip/featured-image', [ 'event' => $event ] ); ?>
		<?php $this->template( 'month/calendar-body/day/calendar-events/calendar-event/type', [ 'event' => $event ] ); ?>
		
		<?php $this->template( 'month/calendar-body/day/calendar-events/calendar-event/tooltip/date', [ 'event' => $event ] ); ?>
		<?php $this->template( 'month/calendar-body/day/calendar-events/calendar-event/tooltip/title', [ 'event' => $event ] ); ?>
		<?php $this->template( 'month/calendar-body/day/calendar-events/calendar-event/tooltip/organization', [ 'event' => $event ] ); ?>
		<?php $this->template( 'month/calendar-body/day/calendar-events/calendar-event/tooltip/description', [ 'event' => $event ] ); ?>
		<?php $this->template( 'month/calendar-body/day/calendar-events/calendar-event/tooltip/cost', [ 'event' => $event ] ); ?>
		
		<?php if(! current_user_can('administrator') ){
			?>
			<?php $this->template( 'month/calendar-body/day/calendar-events/calendar-event/tooltip/detailsbutton', [ 'event' => $event ] ); ?>
			<?php if ( is_user_logged_in() && current_user_can( 'edit_posts', $event->ID ) && ($cf == $ef) ) { //Current logged in user can only edit events of his organization ?>
				<?php $this->template( 'month/calendar-body/day/calendar-events/calendar-event/tooltip/editor-box', [ 'event' => $event ] ); ?>
			<?php } 
			}; 
		?>
	</div>
</div>
