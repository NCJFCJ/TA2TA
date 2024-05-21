<?php
/**
 * Webex details section for ticket emails.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-virtual/webex/email/ticket-email-webex-details.php
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

// Don't print anything when this event is not virtual. Or if we're missing both pieces.
if ( ! $event->virtual || ( empty( $event->webex_join_url ) && empty( $event->webex_password ) ) ) {
	return;
}
?>
<table class="tec-events-virtual-email-webex-details" style="width: 100%;">
	<tr>
		<?php if ( ! empty( $event->webex_join_url ) ) : ?>
			<?php $this->template( 'webex/email/details/join-header' ); ?>
		<?php endif; ?>
		<?php if ( ! empty( $event->webex_password ) ) : ?>
			<?php $this->template( 'webex/email/details/password-header' ); ?>
		<?php endif; ?>
	</tr>
	<tr>
		<?php if ( ! empty( $event->webex_join_url ) ) : ?>
			<?php $this->template( 'webex/email/details/join-content', [ 'event' => $event ] ); ?>
		<?php endif; ?>
		<?php if ( ! empty( $event->webex_password ) ) : ?>
			<?php $this->template( 'webex/email/details/password-content', [ 'event' => $event ] ); ?>
		<?php endif; ?>
	</tr>
</table>
