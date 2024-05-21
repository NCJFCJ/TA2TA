<?php
/**
 * Webex details password content section for ticket emails.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-virtual/webex/email/details/password-content.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @since 1.9.0
 *
 * @version 1.9.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */

?>
<td>
	<ul class="tec-events-virtual-email-webex-details__passwordt" style="list-style: none; margin-top: 0;padding-left: 0;">
		<?php
		echo esc_html(
			sprintf(
				// translators: %1$s: Webex meeting password.
				_x(
					'Password: %1$s',
					'The Webex Meeting password, prefixed by password label.',
					'events-virtual'
				),
				$event->webex_password
			)
		);
		?>
	</ul>
</td>
