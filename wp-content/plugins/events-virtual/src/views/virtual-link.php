<?php
/**
 * Link for a virtual event.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-virtual/virtual-link.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 1.0.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */

// Don't print anything when this event is not virtual.
if ( ! $event->virtual_is_linkable ) {
	return;
}

?>
<div class="tribe-events-c-small-cta tribe-common-b2">
	<a href="<?php echo esc_url( $event->virtual_url ); ?>" target="_blank" class="tribe-events-c-small-cta__link tribe-common-cta tribe-common-cta--thin-alt">
		<?php echo esc_html( $event->virtual_linked_button_text ); ?>
	</a>
</div>
