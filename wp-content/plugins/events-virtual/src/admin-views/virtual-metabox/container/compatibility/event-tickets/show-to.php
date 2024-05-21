<?php
/**
 * View: Virtual Events Metabox Show section addition.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/compatibility/event-tickets/show-to.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.0.4
 *
 * @version 1.0.4
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
	<td class='tribe-table-field-label'><?php esc_html_e( 'Show to:', 'events-virtual' ); ?></td>
	<td>
		<ul>
			<?php
			/**
			 * Adds an entry point to inject items before the default items.
			 */
			$this->do_entry_point( 'before_show_to_list_start' );
			?>
			<li>
				<label for="<?php echo esc_attr( "{$metabox_id}-show-to-all" ); ?>">
					<input
						id="<?php echo esc_attr( "{$metabox_id}-show-to-all" ); ?>"
						name="<?php echo esc_attr( "{$metabox_id}[show-embed-to][]" ); ?>"
						type="checkbox"
						value="<?php echo esc_attr( Event_Meta::$value_show_embed_to_all ); ?>"
						<?php checked( in_array( Event_Meta::$value_show_embed_to_all, $post->virtual_show_embed_to ) ); ?>
					/>
					<?php
					echo esc_html(
						_x(
							'Everyone',
							'Show virtual content to all users.',
							'events-virtual'
						)
					);
					?>
				</label>
			</li>
			<li>
				<label for="<?php echo esc_attr( "{$metabox_id}-show-to-logged-in" ); ?>">
					<input
						id="<?php echo esc_attr( "{$metabox_id}-show-to-logged-in" ); ?>"
						name="<?php echo esc_attr( "{$metabox_id}[show-embed-to][]" ); ?>"
						type="checkbox"
						value="<?php echo esc_attr( Event_Meta::$value_show_embed_to_logged_in ); ?>"
						<?php checked( in_array( Event_Meta::$value_show_embed_to_logged_in, $post->virtual_show_embed_to ) ); ?>
					/>
					<?php
					echo esc_html(
						_x(
							'Logged in users',
							'Show virtual content to logged-in users only.',
							'events-virtual'
						)
					);
					?>
				</label>
			</li>
			<?php
			/**
			 * Adds an entry point to inject items after the default items.
			 */
			$this->do_entry_point( 'before_show_to_list_end' );
			?>
		</ul>
	</td>
</tr>
