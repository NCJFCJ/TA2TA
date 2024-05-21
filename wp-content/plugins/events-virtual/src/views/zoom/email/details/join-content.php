<?php
/**
 * Zoom details join link content section for ticket emails.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-virtual/zoom/email/details/join-content.php
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

// Remove the query vars from the Zoom URL to avoid too long a URL in display.
if ( empty( $event->zoom_join_url ) ) {
	return;
}
// The default url might not contain the password - make sure we include it for emails.
$email_url      = tribe( \Tribe\Events\Virtual\Meetings\Zoom\Password::class )->get_zoom_meeting_link( $event, true );
$short_zoom_url = implode(
	'',
	array_intersect_key( wp_parse_url( $event->zoom_join_url ), array_flip( [ 'host', 'path' ] ) )
);

?>
<td valign="top">
	<a
		href="<?php echo esc_url( $email_url ); ?>"
		class="tribe-events-virtual-email-zoom-details__zoom-link"
		style="font-size:15px;line-height: 18px;"
	>
		<?php echo esc_html( $short_zoom_url ); ?>
	</a>
	<div class="tribe-events-virtual-email-zoom-details__zoom-id" style="color: #6F6F6F;font-size: 13px;line-height: 16px;">
		<?php
		echo esc_html(
			sprintf(
				// translators: %1$s: Zoom meeting ID.
				_x(
					'ID: %1$s',
					'The label for the Zoom Meeting ID, prefixed by ID label.',
					'events-virtual'
				),
				$event->zoom_meeting_id
			)
		);
		?>
	</div>
</td>
