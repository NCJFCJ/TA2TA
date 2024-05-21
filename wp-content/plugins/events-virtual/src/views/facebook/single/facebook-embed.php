<?php
/**
 * Facebook Live embed for a virtual event.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-virtual/facebook/single/facebook-embed.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 1.7.0
 *
 * @var WP_Post              $event           The event post object with properties added by the `tribe_get_event` function.
 * @var array<string,string> $embed_classes An array of classes for the embed.
 * @var string               $embed           The html for the embed.
 *
 * @see tribe_get_event() For the format of the event object.
 */

if ( ! $event->virtual_meeting_is_live || ! $event->virtual_should_show_embed ) {
	return;
}

$facebook_embed_classes = [ 'tribe-events-virtual-single-facebook__embed' ];
if ( ! empty( $embed_classes ) ) {
	$facebook_embed_classes = array_merge( $facebook_embed_classes, $embed_classes );
}
?>
<div class="tribe-events-virtual-single-facebook__embed-wrap">
	<figure
		<?php tribe_classes( $facebook_embed_classes ); ?>
	>
		<div class="tribe-events-virtual-single-facebook__wrapper">
			<?php echo $embed // phpcs:ignore ?>
		</div>
	</figure>
</div>
