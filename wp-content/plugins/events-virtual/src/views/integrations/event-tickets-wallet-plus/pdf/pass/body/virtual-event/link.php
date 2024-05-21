<?php
/**
 * PDF Pass: Virtual Event Link
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-virtual/integrations/event-tickets-wallet-plus/pdf/pass/body/virtual-event/link.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/event-tickets-wallet-plus-tpl Help article for Wallet Plus template files.
 *
 * @since 1.15.5
 *
 * @version 1.15.5
 */

if ( empty( $virtual_url ) ) {
	return;
}

$this->template( 'pass/body/virtual-event/link/header' );
$this->template( 'pass/body/virtual-event/link/link' );
