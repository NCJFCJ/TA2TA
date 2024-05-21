<?php
/**
 * View: Virtual Events Metabox Facebook Live add page link.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/facebook/page/add-link.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.7.0
 *
 * @version 1.7.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var Url $url An instance of the URL handler.
 */

$add_link = $url->add_link();
$connect_label  = _x( 'Add Facebook Page', 'Label to connect an page to the Facebook Live API.', 'events-virtual' );

$classes = [
	'button'                                                          => true,
	'tribe-events-virtual-meetings-facebook-settings__add-page-button' => true,
];
?>
<a
	href="<?php echo esc_url( $add_link ); ?>"
	<?php tribe_classes( $classes ); ?>
>
	<span class="dashicons dashicons-plus tribe-events-virtual-meetings-facebook-settings__add-page-span"></span>
	<?php echo esc_html( $connect_label ); ?>
</a>
