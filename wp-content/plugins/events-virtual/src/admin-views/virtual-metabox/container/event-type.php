<?php
/**
 * View: Virtual Events Metabox Event Type section.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/container/event-type.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.4.0
 * @since 1.6.0 - Move the trash button for virtual events to this templat from video-source.php.
 *
 * @version 1.6.0
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

<tr class="tribe-events-virtual-type-of-event">
	<td class='tribe-table-field-label'><?php esc_html_e( 'Type of Event:', 'events-virtual' ); ?></td>
	<td>
		<button
			class="dashicons dashicons-trash tribe-remove-virtual-event tribe-dependent"
			type="button"
			data-depends="#<?php echo esc_attr( "{$metabox_id}-setup" ); ?>"
			data-condition-checked
		>
			<span class="screen-reader-text">
				<?php echo esc_html_x( 'Remove Virtual Settings', 'Resets the virtual settings', 'events-virtual' ); ?>
			</span>
		</button>
		<ul>
			<li>
				<label for="<?php echo esc_attr( "{$metabox_id}-type-virtual" ); ?>">
					<input
						id="<?php echo esc_attr( "{$metabox_id}-type-virtual" ); ?>"
						name="<?php echo esc_attr( "{$metabox_id}[event-type]" ); ?>"
						type="radio"
						value="<?php echo esc_attr( Event_Meta::$value_virtual_event_type ); ?>"
						<?php checked( Event_Meta::$value_virtual_event_type, $post->virtual_event_type ); ?>
					/>
					<?php
						echo tribe_get_virtual_event_label_singular();
					?>
				</label>
			</li>
			<li>
				<label for="<?php echo esc_attr( "{$metabox_id}-type-hybrid" ); ?>">
					<input
						id="<?php echo esc_attr( "{$metabox_id}-type-hybrid" ); ?>"
						name="<?php echo esc_attr( "{$metabox_id}[event-type]" ); ?>"
						type="radio"
						value="<?php echo esc_attr( Event_Meta::$value_hybrid_event_type ); ?>"
						<?php checked( Event_Meta::$value_hybrid_event_type, $post->virtual_event_type ); ?>
					/>
					<?php
						echo tribe_get_hybrid_event_label_singular();
					?>
				</label>
			</li>
		</ul>
	</td>
</tr>
