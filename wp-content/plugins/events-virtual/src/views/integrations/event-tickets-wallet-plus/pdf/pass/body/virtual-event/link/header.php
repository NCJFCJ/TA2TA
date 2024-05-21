<?php
/**
 * PDF Pass: Virtual Event Link: Header
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-virtual/integrations/event-tickets-wallet-plus/pdf/pass/body/virtual-event/link/header.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/event-tickets-wallet-plus-tpl Help article for Wallet Plus template files.
 *
 * @since 1.15.5
 *
 * @version 1.15.5
 *
 * @var string $virtual_url The virtual event URL.
 * @var string $virtual_event_icon_src The image source of the virtual event icon.
 */

if ( empty( $virtual_url ) ) {
	return;
}
?>
<table class="tec-tickets__wallet-plus-virtual-event-header-table">
	<tr>
		<td align="center">
			<img height="11" src="<?php echo esc_url( $virtual_event_icon_src ); ?>" />
			<?php echo esc_html_x( 'Virtual Event', 'Link to Virtual Event on the Ticket Email', 'events-virtual' ); ?>
		</td>
	</tr>
</table>
