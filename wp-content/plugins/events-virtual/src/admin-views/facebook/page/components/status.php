<?php
/**
 * View: Virtual Events Metabox Facebook Live Page - Access Token Status.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/facebook/page/components/facebook-connect.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.7.0
 *
 * @version 1.7.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var int                 $local_id The unique id used to save the page data.
 * @var array<string|mixed> $page The page data.
 * @var Url                 $url  An instance of the URL handler.
 */

$connect_status = _x(
	'Not Connected',
	'The status of the Page\'s access token.',
	'events-virtual'
);
$connect_instructions = _x(
	'click "Continue with Facebook" to authorize it.',
	'The message to display if a Faceook Page is not connected.',
	'events-virtual'
);

$connected_message = sprintf(
	'<span class="warning">%1$s</span>, %2$s',
	esc_html( $connect_status ),
	esc_html( $connect_instructions )
);

if ( $page['access_token'] ) {
	$connect_status = _x(
		'Connected',
		'The status of the Page\'s access token.',
		'events-virtual'
		);
	$connect_instructions = _x(
		'token expires:',
		'The message to display if a Faceook Page is connected.',
		'events-virtual'
	);
	$clear_link_label = _x(
		'clear token',
		'The label of the link to clear the token.',
		'events-virtual'
	);

	$connected_message = sprintf(
		'<span class="success">%1$s</span>, %2$s  %3$s - <a class="tribe-settings-facebook-page-details__clear-access" href="%4$s">%5$s</a>',
		esc_html( $connect_status ),
		esc_html( $connect_instructions ),
		esc_html( $page['expiration'] ),
		$url->to_clear_access_page_link(),
		esc_html( $clear_link_label )
	);
}
$expiration = $page['expiration'];
?>
<div class="tribe-settings-facebook-page-details__page-expiration">
	<div class="tribe-settings-facebook-page-details__page-expiration-text">
	<strong>
		<?php echo esc_html_x( 'Status: ', 'The label of the status of the Page\'s access token.', 'events-virtual' ); ?>
	</strong>
	<?php echo $connected_message; ?>
	</div>
</div>