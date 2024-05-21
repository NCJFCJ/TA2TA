<?php
/**
 * YouTube Live embed offline template for a virtual event.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-virtual/youtube/single/youtube-embed-offline.php
 *
 * See more documentation about our views templating system.
 *
 * @link    http://evnt.is/1aiy
 *
 * @version 1.6.0
 *
 * @var WP_Post $event   The event post object with properties added by the `tribe_get_event` function.
 * @var string  $offline The offline message.
 *
 * @see     tribe_get_event() For the format of the event object.
 */

?>
<div class="tribe-events-virtual-single-youtube__embed-wrap">
	<div class="ribe-events-virtual-single-youtube__embed-offline-title">
		<?php echo esc_html( $offline ); ?>
	</div>
</div>
