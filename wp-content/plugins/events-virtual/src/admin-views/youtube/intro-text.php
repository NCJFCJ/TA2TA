<?php
/**
 * View: YouTube Settings API auth intro text.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/youtube/intro-text.php
 *
 * See more documentation about our views templating system.
 *
 * @since 1.6.0
 *
 * @version 1.6.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var string $message A message to display above the account list on loading.
 */

?>
<h3 id="tribe-events-virtual-youtube-credentials" class="tribe-settings-youtube-application__title">
	<?php echo esc_html_x( 'YouTube Live', 'API connection header', 'events-virtual' ); ?>
</h3>
<div class="tec-youtube-accounts-messages">
	<?php
		$this->template( 'components/message', [
		'message' => $message,
		'type'    => 'standard',
	] );
	?>
</div>
