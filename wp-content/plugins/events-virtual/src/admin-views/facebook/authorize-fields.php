<?php
/**
 * View: Virtual Events Metabox Facebook Live Page Authorization Setup.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/facebook/authorize-fields.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.7.0
 *
 * @version 1.7.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var Page_API $api     An instance of the Page_API handler.
 * @var URL      $url     An instance of the URL handler.
 * @var string   $message A message to display above the page list on loading.
 */
$pages = $api->get_list_of_pages( true );
?>
<div class="tribe-settings-facebook-application-pages__container">
	<div class="tec-facebook-page-messages"></div>
	<div class="tec-facebook-pages-wrap <?php echo is_array( $pages ) && count( $pages ) > 4 ? 'long-list' : ''; ?>">
		<ul class="tribe-settings-facebook-page-list">
			<?php
			$this->template( 'facebook/page/list', [
				'api'   => $api,
				'url'   => $url,
				'pages' => $pages,
			] );
			?>
		</ul>
	</div>
	<div class="tec-facebook-add-wrap">
		<?php
		$this->template( 'facebook/find-page-id', [] );
		?>
		<?php
		$this->template( 'facebook/page/add-link', [
			'url' => $url,
		] );
		?>
	</div>
</div>
