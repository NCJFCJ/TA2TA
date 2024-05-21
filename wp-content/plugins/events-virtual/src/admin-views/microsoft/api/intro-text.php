<?php
/**
 * View: Virtual Events Metabox Microsoft API auth intro text.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/microsoft/api/intro-text.php
 *
 * See more documentation about our views templating system.
 *
 * @since 1.13.0
 *
 * @version 1.13.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var array $allowed_html Which HTML elements are used for wp_kses.
 */

?>
<h3 id="tec-microsoft-application-credentials" class="tec-settings-microsoft-application__title">
	<?php echo esc_html_x( 'Microsoft', 'API connection header', 'events-virtual' ); ?>
</h3>
<p>
	<?php
	$url = 'https://evnt.is/1b9c';
	echo sprintf(
		'%1$s %2$s. <a href="%3$s" target="_blank">%4$s</a>',
		esc_html_x(
			'You need to connect your site to a Microsoft account to be able to generate Microsoft Meeting links for your',
		'Settings help text for multiple Microsoft accounts.',
		'events-virtual'
		),
		tribe_get_virtual_event_label_plural_lowercase(),
		esc_url( $url ),
		esc_html_x(
			'Read more about adding and managing Microsoft Accounts.',
			'Settings link text for multiple Microsoft accounts.',
			'events-virtual'
		)
	);
	?>
</p>
