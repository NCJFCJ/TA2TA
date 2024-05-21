<?php
/**
 * Event Tickets Emails: Main template > Body > Virtual Event > Link.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/integrations/event-tickets/emails/template-parts/body/virtual-event/link.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/tickets-emails-tpl Help article for Tickets Emails template files.
 *
 * @version 1.15.0
 *
 * @since 1.15.0
 *
 * @var WP_Post $event       The event post object with properties added by the `tribe_get_event` function.
 * @var string  $virtual_url URL to Virtual Event.
 *
 * @see tribe_get_event() For the format of the event object.
 */

use Tribe\Events\Virtual\Plugin;

if ( empty( $event ) || empty( $virtual_url ) ) {
	return;
}

?>
<tr>
	<td class="tec-tickets__email-table-content-virtual-event-title-container" align="center">
		<h3 class="tec-tickets__email-table-content-virtual-event-title">
			<img
				class="tec-tickets__email-table-content-virtual-event-alert"
				width="21"
				height="15"
				src="<?php echo esc_url( tribe_resource_url( 'images/alert.png', false, null, tribe( Plugin::class ) ) ); ?>"
			/>
			<?php echo esc_html_x( 'Virtual Event', 'Link to Virtual Event on the Ticket Email', 'events-virtual' ); ?>
		</h3>
	</td>
</tr>
<tr>
	<td class="tec-tickets__email-table-content-virtual-event-link-container" align="center">
		<a class="tec-tickets__email-table-content-virtual-event-link" href="<?php echo esc_url( $virtual_url ); ?>">
			<?php esc_html_e( $virtual_url ); ?>
		</a>
	</td>
</tr>
<tr>
	<td class="tec-tickets__email-table-content-virtual-event-button-container" align="center">
		<a class="tec-tickets__email-table-content-virtual-event-button" href="<?php echo esc_url( $virtual_url ); ?>">
			<?php esc_html_e( $virtual_link_text ); ?>
		</a>
	</td>
</tr>
