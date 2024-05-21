<?php
/**
 * Facebook Video embed for a virtual event.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-virtual/facebook/single/facebook-video-embed.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 1.8.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */

use Tribe\Events\Virtual\Meetings\Facebook\Event_Meta as Facebook_Meta;

// Don't print anything if the autodetect source is not facebook-video
if ( Facebook_Meta::$autodetect_fb_video_id !== $event->virtual_autodetect_source ) {
	return;
}

// Don't print anything when the event isn't embedding or is not ready.
if ( ! $event->virtual_embed_video || ! $event->virtual_should_show_embed ) {
	return;
}
?>
<div id="fb-root"></div>
<div
  class="fb-video tec-virtual-video-embed__facebook"
  data-href="<?php echo $event->virtual_url; ?>"
  data-width="1400"
  data-allowfullscreen="true"></div>
