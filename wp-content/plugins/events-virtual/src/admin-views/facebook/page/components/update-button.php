<?php
/**
 * View: Virtual Events Metabox Facebook Live Page - Update Button.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/facebook/page/components/update-button.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.7.0
 *
 * @version 1.7.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var array<string|mixed> $page The page data.
 * @var Url                 $url  An instance of the URL handler.
 */

$update_link  = $url->to_save_page_link();
$update_label = _x( 'Update Facebook Page.', 'Save a facebook page from the list of Facebook live pages.', 'events-virtual' );

?>
<button
	class="dashicons dashicons-update tribe-settings-facebook-page-details__save-page update-button"
	type="button"
	data-ajax-save-url="<?php echo $update_link; ?>"
	<?php echo empty( $page['name'] ) || empty( $page['page_id'] ) ? 'disabled' : ''; ?>
>
	<span class="screen-reader-text">
		<?php echo esc_html( $update_label ); ?>
	</span>
</button>