<?php
/**
 * View: Virtual Events Metabox API Single Host
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/api/single-host.php
 *
 * See more documentation about our views templating system.
 *
 * @since 1.9.0
 *
 * @version 1.9.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var string               $label     Label for the dropdown input.
 * @var array<string,string> $hosts_arr Associative array of the data for hosts dropdown.
 */

// Get first entry, which should be the only entry.
$host = reset( $hosts_arr );
?>
<div
	class="tec-events-virtual-meetings-api__host tec-events-virtual-meetings-api__host-dropdown"
	data-host-id="<?php echo esc_html( $host['id'] ); ?>"
>
	<?php echo esc_html( $label ); ?>: <?php echo esc_html( $host['text'] ); ?>
</div>
