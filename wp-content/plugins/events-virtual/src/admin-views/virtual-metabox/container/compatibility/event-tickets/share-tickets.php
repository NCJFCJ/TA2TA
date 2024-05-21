<?php
/**
 * View: Virtual Events Metabox Share on Ticket Emails section.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/compatibility/event-tickets/share-tickets.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.0.0
 * @since   1.0.2 Add check for ticket provider as return of get_event_ticket_provider has changed.
 * @since   1.0.4 Moved to Compatibility.
 *
 * @version 1.0.2
 *
 * @link    http://evnt.is/1aiy
 *
 * @var string   $metabox_id The current metabox id.
 * @var \WP_Post $post       The current event post object, as decorated by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */
?>
<li>
	<label for="<?php echo esc_attr( "{$metabox_id}-ticket-email-link" ); ?>">
		<input
			id="<?php echo esc_attr( "{$metabox_id}-ticket-email-link" ); ?>"
			name="<?php echo esc_attr( "{$metabox_id}[ticket-email-link]" ); ?>"
			type="checkbox"
			value="yes"
			<?php checked( $post->virtual_ticket_email_link ); ?>
		/>
		<?php
		echo esc_html(
			sprintf(
				/* Translators: %1$s: singular ticket term. */
				_x(
					'Include link in %1$s emails',
					'Include virtual link in ticket emails.',
					'events-virtual'
				),
				tribe_get_ticket_label_singular()
			)
		);
		?>
	</label>
</li>
