<?php
/**
 * View: Facebook Settings API auth intro text.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/facebook/intro-text.php
 *
 * See more documentation about our views templating system.
 *
 * @since 1.7.0
 *
 * @version 1.7.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var string $message A message to display above the account list on loading.
 */

?>
<h3 id="tribe-events-virtual-facebook-credentials" class="tribe-settings-facebook-application__title">
	<?php echo esc_html_x( 'Facebook Live Video', 'API connection header', 'events-virtual' ); ?>
</h3>
<div class="tec-facebook-app-messages">
	<?php
	$this->template( 'components/message', [
		'message' => $message,
		'type'    => 'standard',
	] );
	?>
</div>
