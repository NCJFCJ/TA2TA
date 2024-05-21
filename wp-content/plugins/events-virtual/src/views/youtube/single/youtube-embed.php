<?php
/**
 * YouTube Live embed for a virtual event.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-virtual/youtube/single/youtube-embed.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 1.6.0
 *
 * @var WP_Post              $event           The event post object with properties added by the `tribe_get_event` function.
 * @var array<string,string> $embed_classes An array of classes for the embed.
 * @var string               $embed           The html for the embed.
 * @var string               $live_chat       The html for the live chat embed.
 *
 * @see tribe_get_event() For the format of the event object.
 */

// Don't print anything when the event isn't embedding or is not ready.
use Tribe\Events\Virtual\Meetings\YouTube\Connection;

if ( Connection::$offline_key === $event->virtual_meeting_is_live || ! $event->virtual_should_show_embed ) {
	return;
}

$youtube_embed_classes = [ 'tribe-events-virtual-single-youtube__embed' ];
if ( ! empty( $embed_classes ) ) {
	$youtube_embed_classes = array_merge( $youtube_embed_classes, $embed_classes );
}
?>
<div class="tribe-events-virtual-single-youtube__embed-wrap">
	<figure
		<?php tribe_classes( $youtube_embed_classes ); ?>
	>
		<div class="tribe-events-virtual-single-youtube__wrapper">
			<?php echo $embed // phpcs:ignore ?>
		</div>
	</figure>

	<?php if ( $live_chat ) { ?>
		<figure class="tribe-events-virtual-single-youtube__chat-wrap">
			<div class="tribe-events-virtual-single-youtube__wrapper">
				<?php echo $live_chat // phpcs:ignore ?>
			</div>
		</figure>
	<?php } ?>
</div>
