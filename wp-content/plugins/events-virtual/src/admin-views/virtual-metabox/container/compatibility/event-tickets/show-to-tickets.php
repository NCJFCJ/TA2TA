<?php
/**
 * View: Virtual Events Metabox Show section Ticket Attendee addition.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/compatibility/event-tickets/show-to.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.0.4
 * @since   1.10.0 Use new method to get the Tickets settings URL.
 *
 * @version 1.10.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var boolean  $disabled   Should the control be disabled?
 * @var string   $metabox_id The current metabox id.
 * @var \WP_Post $post       The current event post object, as decorated by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */

use Tribe\Events\Virtual\Compatibility\Event_Tickets\Event_Meta as Ticket_Meta;

$classes = $disabled ? 'tribe-disabled' : '';
?>
<li>
	<label
		for="<?php echo esc_attr( "{$metabox_id}-show-to-ticket-attendees" ); ?>"
		<?php tribe_classes( $classes ); ?>
	>
		<input
			id="<?php echo esc_attr( "{$metabox_id}-show-to-ticket-attendees" ); ?>"
			<?php tribe_classes( $classes ); ?>
			name="<?php echo esc_attr( "{$metabox_id}[show-embed-to][]" ); ?>"
			type="checkbox"
			data-dependency-manual-control
			value="<?php echo esc_attr( Ticket_Meta::$value_show_embed_to_ticket ); ?>"
			<?php checked( in_array( Ticket_Meta::$value_show_embed_to_ticket, $post->virtual_show_embed_to ) ); ?>
			<?php disabled( $disabled, true, true ); ?>
		/>
		<?php
		echo esc_html(
			sprintf(
				/* Translators: %1$s: singular ticket term. */
				_x(
					'%1$s Attendees only',
					'Only show virtual content to users with tickets.',
					'events-virtual'
				),
				tribe_get_ticket_label_singular()
			)
		);
		?>
	</label>
	<?php if ( $disabled ) : ?>
		<div class="tribe-tooltip event-helper-text tribe-events-virtual-show-to-ticket-attendees-helper-text" aria-expanded="false">
			<span class="dashicons dashicons-info"></span>
			<div class="down">
				<p>
				<?php
				echo wp_kses(
					sprintf(
						/* Translators: %1$s is the URL */
						_x(
							'Login requirements for tickets must be active to use this option. <a href="%1$s">Go to Tickets settings.</a>',
							'Explains why the radio button is disabled and how to enable it via ticket settings.',
							'events-virtual'
						),
						esc_url(
							tribe( 'events-virtual.compatibility.tribe-event-tickets' )->get_settings_url( [ 'tab' => 'event-tickets' ] )
						)
					),
					[ 'a' => [ 'href' => [] ] ]
				);
				?>
				</p>
			</div>
		</div>
	<?php endif; ?>
</li>
