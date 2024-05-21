<?php
/**
 * View: Virtual Events Metabox Facebook Live Page - Add Button.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/facebook/page/components/add-button.php
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

$add_link  = $url->to_save_page_link();
$add_label = _x( 'Add', 'Add a facebook page from the list of Facebook live pages.', 'events-virtual' );
?>
<button
	class="tribe-settings-facebook-page-details__save-page button-primary"
	type="button"
	data-ajax-save-url="<?php echo $add_link; ?>"
	<?php echo empty( $page['name'] ) || empty( $page['page_id'] ) ? 'disabled' : ''; ?>
>
	<span>
		<?php echo esc_html( $add_label ); ?>
	</span>
</button>