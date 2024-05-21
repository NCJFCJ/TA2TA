<?php
/**
 * View: Virtual Events Metabox Share on RSVP Emails section.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/compatibility/event-tickets/share.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.0.0
 * @since   1.0.4 Moved to Compatibility
 *
 * @version 1.0.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var string   $metabox_id The current metabox id.
 * @var \WP_Post $post       The current event post object, as decorated by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */
?>

<tr>
	<td class='tribe-table-field-label'><?php esc_html_e( 'Share:', 'events-virtual' ); ?></td>
	<td>
		<ul>
			<?php
			/**
			 * Adds an entry point to inject items before the default items.
			 */
			$this->do_entry_point( 'before_share_list_start' );
			?>
			<li>
				<label for="<?php echo esc_attr( "{$metabox_id}-rsvp-email-link" ); ?>">
					<input
						id="<?php echo esc_attr( "{$metabox_id}-rsvp-email-link" ); ?>"
						name="<?php echo esc_attr( "{$metabox_id}[rsvp-email-link]" ); ?>"
						type="checkbox"
						value="yes"
						<?php checked( $post->virtual_rsvp_email_link ); ?>
					/>
					<?php
					echo esc_html(
						sprintf(
							/* Translators: %1$s: singular RSVP term. */
							_x(
								'Include link in %1$s emails',
								'Include virtual link in RSVP emails.',
								'events-virtual'
							),
							tribe_get_rsvp_label_singular()
						)
					);
					?>
				</label>
			</li>
			<?php
			/**
			 * Adds an entry point to inject items after the default items.
			 */
			$this->do_entry_point( 'before_share_list_end' );
			?>
		</ul>
	</td>
</tr>
