<?php
/**
 * View: Virtual Events Metabox Google API auth intro text.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/google/api/intro-text.php
 *
 * See more documentation about our views templating system.
 *
 * @since 1.9.0
 *
 * @version 1.9.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var array $allowed_html Which HTML elements are used for wp_kses.
 */

?>
<h3 id="tec-google-application-credentials" class="tec-settings-google-application__title">
	<?php echo esc_html_x( 'Google', 'API connection header', 'events-virtual' ); ?>
</h3>
<p>
	<?php
	$url = 'https://evnt.is/1b8r';
	echo sprintf(
		'%1$s %2$s. <a href="%3$s" target="_blank">%4$s</a>',
		esc_html_x(
			'You need to connect your site to a Google account to be able to generate Google Meet links for your',
			'Settings help text for multiple Google accounts.',
			'events-virtual'
		),
		tribe_get_virtual_event_label_plural_lowercase(),
		esc_url( $url ),
		esc_html_x(
			'Read more about adding and managing Google Accounts.',
			'Settings link text for multiple Google accounts.',
			'events-virtual'
		)
	);
	?>
</p>
