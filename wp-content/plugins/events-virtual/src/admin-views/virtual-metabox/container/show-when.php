<?php
/**
 * View: Virtual Events Metabox Show When section.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/container/show-when.php
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

use Tribe\Events\Virtual\Event_Meta;
?>

<tr class="tribe-events-virtual-show">
	<td class='tribe-table-field-label'><?php esc_html_e( 'Show when:', 'events-virtual' ); ?></td>
	<td>
		<ul>
			<li>
				<label for="<?php echo esc_attr( "{$metabox_id}-show-immediately" ); ?>">
					<input
						id="<?php echo esc_attr( "{$metabox_id}-show-immediately" ); ?>"
						name="<?php echo esc_attr( "{$metabox_id}[show-embed-at]" ); ?>"
						type="radio"
						value="<?php echo esc_attr( Event_Meta::$value_show_embed_now ); ?>"
						<?php checked( Event_Meta::$value_show_embed_now, $post->virtual_show_embed_at ); ?>
					/>
					<?php
					echo esc_html(
						sprintf(
							/* Translators: single event term. */
							_x(
								'%1$s is published',
								'Show watch button or embed when the event is published.',
								'events-virtual'
							),
							tribe_get_event_label_singular()
						)
					);
					?>
				</label>
			</li>
			<li>
				<label for="<?php echo esc_attr( "{$metabox_id}-show-at-start" ); ?>">
					<input
						id="<?php echo esc_attr( "{$metabox_id}-show-at-start" ); ?>"
						name="<?php echo esc_attr( "{$metabox_id}[show-embed-at]" ); ?>"
						type="radio"
						value="<?php echo esc_attr( Event_Meta::$value_show_embed_start ); ?>"
						<?php checked( Event_Meta::$value_show_embed_start, $post->virtual_show_embed_at ); ?>
					/>
					<?php
					echo esc_html(
						sprintf(
							/* Translators: single event term. */
							_x(
								'%1$s starts',
								'Show watch button or embed only at the start of the event.',
								'events-virtual'
							),
							tribe_get_event_label_singular()
						)
					);
					?>
				</label>


				<div
					class="tribe-tooltip event-helper-text tribe-events-virtual-show-helper-text"
					aria-expanded="false"
				>
					<span class="dashicons dashicons-info"></span>
					<div class="down">
						<p>
							<?php
							echo esc_html(
								sprintf(
									/* Translators: %1$d: number of minutes of lead-up before event %2$s: single event term. */
									_x(
										'Link and/or embed will appear on the page %1$d minutes before the %2$s start time.',
										'Explains when the link will show.',
										'events-virtual'
									),
									absint( $post->virtual_show_lead_up ),
									tribe_get_event_label_singular_lowercase()
								)
							);
							?>
						</p>
					</div>
				</div>
			</li>
		</ul>
	</td>
</tr>
