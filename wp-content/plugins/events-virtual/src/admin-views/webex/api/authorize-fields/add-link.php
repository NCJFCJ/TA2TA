<?php
/**
 * View: Virtual Events Metabox Webex API add account link.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/webex/api/authorize-fields/add-link
 *
 * See more documentation about our views templating system.
 *
 * @since 1.9.0
 *
 * @version 1.9.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var Api $api An instance of the Webex API handler.
 * @var Url $url An instance of the URL handler.
 */

$authorize_link      = $url->to_authorize();
$connect_label       = _x( 'Add Webex Account', 'Label to connect an account to the Webex API.', 'events-virtual' );

$classes = [
	'button'                                                        => true,
	'tec-events-virtual-meetings-api-settings_add-account-button'   => true,
	'tec-events-virtual-meetings-webex-settings_add-account-button' => true,
];
?>
<a
	href="<?php echo esc_url( $authorize_link ); ?>"
	<?php tribe_classes( $classes ); ?>
>
	<span class="dashicons dashicons-plus"></span>
	<?php echo esc_html( $connect_label ); ?>
</a>
<div class="tec-events-virtual-meetings-api-settings_add-account-button-helper-text tec-events-virtual-meetings-webex-settings_add-account-button-helper-text">
	<?php
	$url = 'https://evnt.is/1b53';
	echo sprintf(
		'<a href="%1$s" target="_blank">%2$s</a>',
		esc_url( $url ),
		esc_html_x(
		'Using multiple Webex Accounts',
		'Settings help text for multiple Webex accounts button',
		'events-virtual'
		)
	);
	?>
</div>
