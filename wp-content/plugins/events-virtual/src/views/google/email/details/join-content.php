<?php
/**
 * Google details join link content section for ticket emails.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-virtual/google/email/details/join-content.php
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

// Remove the query vars from the Google URL to avoid too long a URL in display.
if ( empty( $event->google_join_url ) ) {
	return;
}

$short_google_url = implode(
	'',
	array_intersect_key( wp_parse_url( $event->google_join_url ), array_flip( [ 'host', 'path' ] ) )
);

?>
<td valign="top">
	<a
		href="<?php echo esc_url( $event->google_join_url ); ?>"
		class="tribe-events-virtual-email-google-details__google-link"
		style="font-size:15px;line-height: 18px;"
	>
		<?php echo esc_html( $short_google_url ); ?>
	</a>
	<div class="tribe-events-virtual-email-google-details__google-id" style="color: #6F6F6F;font-size: 13px;line-height: 16px;">
		<?php
		echo esc_html(
			sprintf(
				// translators: %1$s: Google Meet ID.
				_x(
					'ID: %1$s',
					'The label for the Google Meet ID, prefixed by ID label.',
					'events-virtual'
				),
				$event->google_conference_id
			)
		);
		?>
	</div>
</td>
