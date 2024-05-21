<?php
/**
 * Google details section for ticket emails.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-virtual/google/email/ticket-email-google-details.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 1.11.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */

// Don't print anything when this event is not virtual. Or if we're missing both pieces.
if ( ! $event->virtual || ( empty( $event->google_join_url ) && empty( $event->google_global_dial_in_numbers ) ) ) {
	return;
}
?>
<table class="tribe-events-virtual-email-google-details" style="width: 100%;">
	<tr>
		<?php if ( ! empty( $event->google_join_url ) ) : ?>
			<?php $this->template( 'google/email/details/join-header' ); ?>
		<?php endif; ?>
		<?php if ( ! empty( $event->google_global_dial_in_numbers ) ) : ?>
			<?php $this->template( 'google/email/details/dial-in-header' ); ?>
		<?php endif; ?>
	</tr>
	<tr>
		<?php if ( ! empty( $event->google_join_url ) ) : ?>
			<?php $this->template( 'google/email/details/join-content', [ 'event' => $event ] ); ?>
		<?php endif; ?>
		<?php if ( ! empty( $event->google_global_dial_in_numbers ) ) : ?>
			<?php $this->template( 'google/email/details/dial-in-content', [ 'event' => $event ] ); ?>
		<?php endif; ?>
	</tr>
</table>
