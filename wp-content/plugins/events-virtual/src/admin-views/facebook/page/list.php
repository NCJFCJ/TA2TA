<?php
/**
 * View: Virtual Events Metabox Facebook Page list.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/facebook/page/list.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.7.0
 *
 * @version 1.7.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var Page_API            $api   An instance of the Page_API handler.
 * @var array<string|mixed> $pages An array of the Facebook pages authorized for the site.
 * @var URL                 $url   An instance of the URL handler.
 */

?>
<?php
if ( empty( $pages ) ) {
	$this->template( 'facebook/page/fields', [
		'local_id' => $api->get_unique_id(),
		'page'     => [
			'name'         => '',
			'page_id'      => '',
			'access_token' => '',
			'expiration'   => '',
		],
		'url'      => $url,
	] );

	return;
}
?>
<?php foreach ( $pages as $local_id => $page ) : ?>
	<?php
	$this->template( 'facebook/page/fields', [
		'local_id' => $local_id,
		'page'     => $page,
		'url'      => $url,
	] );
	?>
<?php endforeach; ?>
