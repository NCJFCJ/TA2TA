<?php
/**
 * View: Virtual Events Metabox Display section.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/container/display.php
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

/**
 * Filters if the embed video checkbox is hidden.
 *
 * @since 1.0.0
 *
 * @param boolean $is_hidden Whether the embed video control is hidden.
 * @param WP_Post $post      The post object.
 */
$is_hidden = apply_filters( 'tribe_events_virtual_display_embed_video_hidden', false, $post );

$embed_video_classes = [
	'tec-events-virtual-display__list-item',
	'tribe-events-virtual-hidden' => $is_hidden,
];
?>
<tr class="tribe-events-virtual-display">
	<td class="tribe-table-field-label tribe-events-virtual-display__label">
		<?php esc_html_e( 'Display:', 'events-virtual' ); ?>
	</td>
	<td class="tribe-table-field--top tribe-events-virtual-display__content">
		<ul class="tribe-events-virtual-display__list">
			<li <?php tribe_classes( $embed_video_classes ); ?>>
				<label for="<?php echo esc_attr( "{$metabox_id}-embed-video" ); ?>">
					<input
						id="<?php echo esc_attr( "{$metabox_id}-embed-video" ); ?>"
						name="<?php echo esc_attr( "{$metabox_id}[embed-video]" ); ?>"
						type="checkbox"
						value="yes"
						<?php checked( tribe_is_truthy( $post->virtual_embed_video ) ); ?>
					/>
					<?php
					echo esc_html_x(
						'Embed Video',
						'Option to embed video in event.',
						'events-virtual'
					);
					?>
				</label>
			</li>
			<li class="tec-events-virtual-display__list-item tec-events-virtual-display__list-item--linked-button">
				<label
					for="<?php echo esc_attr( "{$metabox_id}-linked-button" ); ?>"
					class="tribe-events-virtual-display__linked-button-label"
				>
					<input
						id="<?php echo esc_attr( "{$metabox_id}-linked-button" ); ?>"
						name="<?php echo esc_attr( "{$metabox_id}[linked-button]" ); ?>"
						type="checkbox"
						value="yes"
						<?php checked( tribe_is_truthy( $post->virtual_linked_button ) ); ?>
					/>
					<?php
					echo esc_html_x(
						'Linked Button',
						'Show watch button or embed only at the start of the event.',
						'events-virtual'
					);
					?>
				</label>
				<span
					class="tribe-dependent tribe-events-virtual-display__linked-button-text-wrapper"
					data-depends="#<?php echo esc_attr( "{$metabox_id}-linked-button" ); ?>"
					data-condition-checked
				>
					<label
						class="tribe-events-virtual-display__linked-button-text-label"
						for="<?php echo esc_attr( "{$metabox_id}-virtual-button-text" ); ?>"
					>
						<?php
						echo esc_html_x(
							'Label',
							'Label for virtual events watch button, defaults to watch',
							'events-virtual'
						);
						?>
					</label>
					<input
						id="<?php echo esc_attr( "{$metabox_id}-virtual-button-text" ); ?>"
						name="<?php echo esc_attr( "{$metabox_id}[virtual-button-text]" ); ?>"
						value="<?php echo esc_attr( $post->virtual_linked_button_text ); ?>"
						type="text"
						class="tribe-events-virtual-display__linked-button-text-input components-text-control__input"
					/>
				</span>
			</li>

			<?php $this->do_entry_point( 'before_ul_close' ); ?>

		</ul>
	</td>
</tr>
