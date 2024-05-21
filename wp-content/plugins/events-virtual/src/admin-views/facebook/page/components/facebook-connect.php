<?php
/**
 * View: Virtual Events Metabox Facebook Live Page - Facebook Connect Button.
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
 */

if ( ! empty( $page['access_token'] ) ) {
	return;
}
?>
<div class="tribe-settings-facebook-page-details__page-authorize">
	<div
		class="fb-login-button"
		data-scope="pages_manage_metadata,pages_read_engagement,pages_read_user_content"
		onlogin="tribe.events.facebookSettingsAdmin.facebookAuthorization( '<?php echo esc_html( $local_id ); ?>' );"
		data-size="medium"
		data-button-type="continue_with"
		data-layout="rounded"
		data-auto-logout-link="false"
		data-use-continue-as="false"
		data-width=""
	>
	</div>
</div>
