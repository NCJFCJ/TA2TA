<?php
/**
 * View: Facebook Settings Save Facebook App.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/facebook/save.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.7.0
 *
 * @version 1.7.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var URL $url An instance of the URL handler.
 */

?>
<div class="tribe-settings-facebook-application__connect-container">
	<button
		class="tribe-settings-facebook-application__connect-button button-primary"
		type="button"
		data-ajax-save-url="<?php echo $url->to_save_facebook_app_link(); ?>"
	>
		<span>
			<?php echo esc_html_x( 'Save Facebook App', 'Save a Facebook App ID and App Secret.', 'events-virtual' ); ?>
		</span>
	</button>
</div>
