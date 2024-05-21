<?php
/**
 * PDF Pass: Virtual Event Link: Link
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-virtual/integrations/event-tickets-wallet-plus/pdf/pass/body/virtual-event/link/link.php
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
 */

if ( empty( $virtual_url ) ) {
	return;
}

?>
<table class="tec-tickets__wallet-plus-virtual-event-link-table">
	<tr>
		<td align="center">
			<a
				class="tec-tickets__wallet-plus-virtual-event-link"
				href="<?php echo esc_url( $virtual_url ); ?>"
			>
			<?php echo esc_html( $virtual_url ); ?></a>
		</td>
	</tr>
</table>
