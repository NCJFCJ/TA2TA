<?php
/**
 * View: Find Facebook Page ID Text.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/facebook/find-page-id.php
 *
 * See more documentation about our views templating system.
 *
 * @since 1.7.0
 *
 * @version 1.7.0
 *
 * @link    http://evnt.is/1aiy
 *
 */

?>
<div class="tribe-settings-facebook-application__find-page-id">
	<?php
	$url = 'https://evnt.is/1avk';
	echo sprintf(
		'<a href="%1$s" target="_blank">%2$s</a>',
		esc_url( $url ),
		esc_html_x(
			'How to find your Facebook page/group ID.',
			'The link label to get the Facebook page/group ID.',
			'events-virtual'
		)
	);
	?>
</div>
