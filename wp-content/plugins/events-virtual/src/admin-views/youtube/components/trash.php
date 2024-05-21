<?php
/**
 * View: Virtual Events YouTube Settings Delete Channel ID.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/youtube/components/trash.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.6.0
 *
 * @version 1.6.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var bool   $disabled Whether the trash is disabled.
 * @var string $url      The url of the ajax call to delete the channel id.
 */

?>

<div class="tribe-settings-youtube-integration__channel-delete">
	<button
		class="dashicons dashicons-trash tribe-settings-youtube-integration__delete-channel"
		type="button"
		data-ajax-delete-url="<?php echo $url; ?>"
		<?php disabled( $disabled ); ?>
	>
		<span class="screen-reader-text">
			<?php echo esc_html_x( 'Delete YouTube Channel ID', 'Delete YouTube channel id by AJAX.', 'events-virtual' ); ?>
		</span>
	</button>
</div>
