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
 * @var int                 $local_id The unique id used to save the page data.
 * @var array<string|mixed> $page The page data.
 * @var Url                 $url  An instance of the URL handler.
 */

$delete_link  = $url->to_delete_page_link( $local_id );
$delete_label = _x( 'Remove facebook page', 'Removes a facebook page from the list of Facebook live pages.', 'events-virtual' )
?>
<div class="tribe-settings-facebook-page-details__actions tribe-settings-facebook-page-details__page-delete">
	<button
		class="dashicons dashicons-trash tribe-settings-facebook-page-details__delete-page"
		type="button"
		data-ajax-delete-url="<?php echo $delete_link; ?>"
		<?php echo empty( $page['name'] ) || empty( $page['page_id'] ) ? 'disabled' : ''; ?>
	>
		<span class="screen-reader-text">
			<?php echo esc_html( $delete_label ); ?>
		</span>
	</button>
</div>