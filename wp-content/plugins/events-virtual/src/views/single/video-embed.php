<?php
/**
 * Video embed for a virtual event.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-virtual/single/video-embed.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 1.0.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */

// Don't print anything when the event isn't embedding or is not ready.
if ( ! $event->virtual_embed_video || ! $event->virtual_should_show_embed ) {
	return;
}

$video_html = wp_oembed_get( $event->virtual_url );

if ( empty( $video_html ) ) {
	return;
}
?>
<div class="tribe-events-virtual-single-video-embed">
	<?php echo $video_html // phpcs:ignore ?>
</div>
