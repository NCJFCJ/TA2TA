<?php
/**
 * Webex details join content section for ticket emails.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-virtual/webex/email/details/join-content.php
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

// Remove the query vars from the Webex URL to avoid too long a URL in display.
if ( empty( $event->webex_join_url ) ) {
	return;
}

$short_webex_url = implode(
	'',
	array_intersect_key( wp_parse_url( $event->webex_join_url ), array_flip( [ 'host', 'path' ] ) )
);

?>
<td valign="top">
	<a
		href="<?php echo esc_url( $event->webex_join_url ); ?>"
		class="tribe-events-virtual-email-webex-details__webex-link"
		style="font-size:15px;line-height: 18px;"
	>
		<?php echo esc_html( $short_webex_url ); ?>
	</a>
	<div class="tribe-events-virtual-email-webex-details__webex-id" style="color: #6F6F6F;font-size: 13px;line-height: 16px;">
		<?php
		echo esc_html(
			sprintf(
				// translators: %1$s: Webex meeting ID.
				_x(
					'ID: %1$s',
					'The label for the Webex Meeting ID, prefixed by ID label.',
					'events-virtual'
				),
				$event->webex_meeting_id
			)
		);
		?>
	</div>
</td>
