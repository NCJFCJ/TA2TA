<?php
/**
 * View: Virtual Events Metabox Label section.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/container/label.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.0.0
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
	<td class='tribe-table-field-label'><?php esc_html_e( 'Virtual Event Label:', 'events-virtual' ); ?></td>
	<td>
		<ul>
			<li>
				<label for="<?php echo esc_attr( "{$metabox_id}-show-on-event" ); ?>">
					<input
						id="<?php echo esc_attr( "{$metabox_id}-show-on-event" ); ?>"
						name="<?php echo esc_attr( "{$metabox_id}[show-on-event]" ); ?>"
						type="checkbox"
						value="yes"
						<?php checked( $post->virtual_show_on_event ); ?>
					/>
					<?php
					echo esc_html(
						sprintf(
							/* Translators: single event term. */
							_x(
								'Show on %1$s page',
								'Show virtual events marker on single event page.',
								'events-virtual'
							),
							tribe_get_event_label_singular_lowercase()
						)
					);
					?>
				</label>
			</li>
			<li>
				<label for="<?php echo esc_attr( "{$metabox_id}-show-on-views" ); ?>">
					<input
						id="<?php echo esc_attr( "{$metabox_id}-show-on-views" ); ?>"
						name="<?php echo esc_attr( "{$metabox_id}[show-on-views]" ); ?>"
						type="checkbox"
						value="yes"
						<?php checked( tribe_is_truthy( $post->virtual_show_on_views ) ); ?>
					/>
					<?php
					echo esc_html_x(
						'Show on calendar views',
						'Show virtual events marker on calendar views.',
						'events-virtual'
					);
					?>
				</label>
			</li>
			<li>
				<p class="event-helper-text">
					<?php
					echo esc_html(
						sprintf(
							/* Translators: plural event term. */
							__(
								'Virtual %1$s will be indexed on Google as online %1$s.',
								'events-virtual'
							),
							tribe_get_event_label_plural_lowercase()
						)
					);
					?>
				</p>
			</li>
		</ul>
	</td>
</tr>
