<?php
/**
 * Microsoft details section for ticket emails.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-virtual/microsoft/email/ticket-email-microsoft-details.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 1.13.1
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */

// Don't print anything when this event is not virtual. Or if we're missing both pieces.
if ( ! $event->virtual || ( empty( $event->microsoft_join_url ) ) ) {
	return;
}
?>
<table class="tribe-events-virtual-email-microsoft-details" style="width: 100%;">
	<tr>
		<?php if ( ! empty( $event->microsoft_join_url ) ) : ?>
			<?php $this->template( 'microsoft/email/details/join-header', $event ); ?>
		<?php endif; ?>
	</tr>
	<tr>
		<?php if ( ! empty( $event->microsoft_join_url ) ) : ?>
			<?php $this->template( 'microsoft/email/details/join-content', [ 'event' => $event ] ); ?>
		<?php endif; ?>
	</tr>
</table>
