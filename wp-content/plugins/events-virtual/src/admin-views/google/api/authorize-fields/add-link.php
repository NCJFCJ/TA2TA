<?php
/**
 * View: Virtual Events Metabox Google API add account link.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/google/api/authorize-fields/add-link
 *
 * See more documentation about our views templating system.
 *
 * @since 1.9.0
 *
 * @version 1.9.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var Api $api An instance of the Google API handler.
 * @var Url $url An instance of the URL handler.
 */

$authorize_link = $url->to_authorize();
$connect_label  = _x( 'Add Google Account', 'Label to connect an account to the Google API.', 'events-virtual' );

$classes = [
	'tec-events-virtual-meetings-api-settings_add-account-button'   => true,
	'tec-events-virtual-meetings-google-settings_add-account-button' => true,
];
?>
<a
	href="<?php echo esc_url( $authorize_link ); ?>"
	<?php tribe_classes( $classes ); ?>
>
	<?php echo esc_html( $connect_label ); ?>
</a>
<div class="tec-events-virtual-meetings-api-settings_add-account-button-helper-text tec-events-virtual-meetings-google-settings_add-account-button-helper-text">
	<?php
	$url = 'https://evnt.is/1b8r';
	echo sprintf(
		'<a href="%1$s" target="_blank">%2$s</a>',
		esc_url( $url ),
		esc_html_x(
			'Using multiple Google Accounts',
			'Settings help text for multiple Google accounts button',
			'events-virtual'
		)
	);
	?>
</div>
