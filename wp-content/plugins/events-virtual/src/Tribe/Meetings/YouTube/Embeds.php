<?php
/**
 * Handles the YouTube Embeds.
 *
 * @since 1.6.0
 *
 * @package Tribe\Events\Virtual\Meetings\YouTube
 */

namespace Tribe\Events\Virtual\Meetings\YouTube;

/**
 * Class Embeds
 *
 * @since 1.6.0
 *
 * @package Tribe\Events\Virtual\Meetings\YouTube
 */
class Embeds {

	/**
	 * Regex to get the YouTube Video url.
	 *
	 * @since 1.6.0
	 *
	 * @var string
	 */
	protected $regex_video_url = '|src="(.+?)"|';

	/**
	 * Get the regex to get the YouTube Video url.
	 *
	 * @since 1.6.0
	 *
	 * @return string The regex to get YouTube video url from the filter if a string or the default.
	 */
	public function get_regex_video_url() {
		/**
		 * Allow filtering of the regex to get YouTube video url.
		 *
		 * @since 1.6.0
		 *
		 * @param string The regex to get YouTube video url.
		 */
		$regex_video_url = apply_filters( 'tribe_events_virtual_youtube_regex_video_url', $this->regex_video_url );

		return is_string( $regex_video_url ) ? $regex_video_url : $this->regex_video_url;
	}

	/**
	 * Get the live chat embed coding.
	 *
	 * @since 1.6.0
	 *
	 * @param \WP_Post $event The event post object with properties added by the `tribe_get_event` function.
	 *
	 * @return bool|string The string of the live chat or false if not live chat.
	 */
	public function get_live_chat( $event ) {
		if ( ! $event->youtube_live_chat ) {
			return false;
		}
		$parsed_site_url = parse_url( get_site_url() );
		$embed_domain    = $parsed_site_url["host"];

		return '<iframe
			class="tribe-events-virtual-single-youtube__chat-embed"
			width="580"
			height="400"
			src="https://www.youtube.com/live_chat?v=' . $event->virtual_meeting_is_live . '&embed_domain=' . $embed_domain . '"
			frameborder="0"
			scrolling="no"
		></iframe>';
	}

	/**
	 * Get the YouTube embed html with the saved parameters.
	 *
	 * @since 1.6.0
	 *
	 * @param \WP_Post $event The event post object with properties added by the `tribe_get_event` function.
	 *
	 * @return string The html for the YouTube Video embed with the saved parameters.
	 */
	public function get_embed( $event ) {
		if ( ! $event instanceof \WP_Post ) {
			return '';
		}

		if ( empty( $event->virtual_meeting_is_live ) || ! is_string( $event->virtual_meeting_is_live ) ) {
			return '';
		}

		$iframe = wp_oembed_get( 'http://www.youtube.com/watch?v=' . $event->virtual_meeting_is_live );

		// Find the video url and replace the parameters.
		// 1 & 0 used as false does not get added to the url.
		$parameters = [
			'autoplay'       => $event->youtube_autoplay ? 1 : 0,
			'mute'           => $event->youtube_mute_video ? 1 : 0,
			'modestbranding' => $event->youtube_modest_branding ? 1 : 0,
			'rel'            => $event->youtube_related_videos ? 0 : 1,
			'controls'       => $event->youtube_hide_controls ? 0 : 1,
		];
		preg_match( $this->get_regex_video_url(), $iframe, $matches );
		$src     = isset( $matches[1] ) ? $matches[1] : false;
		if ( ! $src ) {
			return false;
		}

		$new_src = add_query_arg( $parameters, $src );

		return str_replace( $src, $new_src, $iframe );
	}
}
