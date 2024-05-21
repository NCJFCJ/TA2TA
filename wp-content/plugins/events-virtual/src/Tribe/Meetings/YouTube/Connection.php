<?php
/**
 * Handles the connection to the YouTube Channel
 *
 * @since   1.6.0
 *
 * @package Tribe\Events\Virtual\Meetings\YouTube
 */

namespace Tribe\Events\Virtual\Meetings\YouTube;

/**
 * Class Connection
 *
 * @since   1.6.0
 *
 * @package Tribe\Events\Virtual\Meetings\YouTube
 */
class Connection {

	/**
	 * The YouTube Channel url with placeholder.
	 *
	 * @since 1.6.0
	 *
	 * @var string
	 */
	protected static $url_with_placeholder = 'https://www.youtube.com/channel/%%CHANNELID%%/live';

	/**
	 * The YouTube Channel url with placeholder.
	 *
	 * @since 1.6.0
	 *
	 * @var string
	 */
	public static $offline_key = 'OFFLINE';

	/**
	 * Regex to get channel status.
	 *
	 * @since 1.6.0
	 *
	 * @var string
	 */
	protected $regex_status = '|status"\s*:\s*"([^"]*)"|';

	/**
	 * Regex to get if embed allowed.
	 *
	 * @since 1.6.0
	 *
	 * @var string
	 */
	protected $regex_embed_allowed = '|playableInEmbed"\s*:\s*true|';

	/**
	 * Regex to get live video id.
	 *
	 * @since 1.6.0
	 *
	 * @var string
	 */
	protected $regex_video_id = '|liveStreamabilityRenderer"\s*:\s*{"videoId"\s*:\s*"([^"]*)"|';

	/**
	 * Whether the channel is live or not.
	 *
	 * @since 1.6.0
	 *
	 * @var bool
	 */
	protected $status = '';

	/**
	 * Whether the live video can be embeded or not.
	 *
	 * @since 1.6.0
	 *
	 * @var bool
	 */
	protected $is_embeddable = '';

	/**
	 * The live video id or OFFLINE.
	 *
	 * @since 1.6.0
	 *
	 * @var string
	 */
	protected $video_id = '';

	/**
	 * The html of the request YouTube channel.
	 *
	 * @since 1.6.0
	 *
	 * @var string
	 */
	protected $data;

	/**
	 * Get the regex to get channel status.
	 *
	 * @since 1.6.0
	 *
	 * @return string The regex to detect video status from the filter if a string or the default.
	 */
	public function get_regex_status() {
		/**
		 * Allow filtering of the regex to get YouTube video status.
		 *
		 * @since 1.6.0
		 *
		 * @param string The regex to detect video status.
		 */
		$regex_status = apply_filters( 'tribe_events_virtual_youtube_regex_status', $this->regex_status );

		return is_string( $regex_status ) ? $regex_status : $this->regex_status;
	}

	/**
	 * Get the regex to to check if embed allowed.
	 *
	 * @since 1.6.0
	 *
	 * @return string The regex to detect embed status from the filter if a string or the default.
	 */
	public function get_regex_embed_allowed() {
		/**
		 * Allow filtering of the regex to get YouTube video status.
		 *
		 * @since 1.6.0
		 *
		 * @param string The regex to detect embed status.
		 */
		$regex_embed_allowed = apply_filters( 'tribe_events_virtual_youtube_regex_embed_allowed', $this->regex_embed_allowed );

		return is_string( $regex_embed_allowed ) ? $regex_embed_allowed : $this->regex_embed_allowed;
	}

	/**
	 * Get the regex to get the video id.
	 *
	 * @since 1.6.0
	 *
	 * @return string The regex to detect video id from the filter if a string or the default.
	 */
	public function get_regex_video_id() {
		/**
		 * Allow filtering of the regex to get the YouTube video id.
		 *
		 * @since 1.6.0
		 *
		 * @param string The regex to detect video id.
		 */
		$regex_video_id = apply_filters( 'tribe_events_virtual_youtube_regex_video_id', $this->regex_video_id );

		return is_string( $regex_video_id ) ? $regex_video_id : $this->regex_video_id;
	}

	/**
	 * Get the YouTube live stream video.
	 *
	 * @since 1.6.0
	 *
	 * @param string $channel_id The YouTube channel id.
	 *
	 * @return string OFFLINE or the video id if live.
	 */
	public function get_live_stream( $channel_id ) {
		$is_live = static::$offline_key;
		if ( empty( $channel_id ) ) {
			return $is_live;
		}

		/** @var \Tribe__Cache $cache */
		$cache    = tribe( 'cache' );
		$cache_id = 'events_virtual_meetings_youtube_' . md5( $channel_id );
		$video_id = $cache->get_transient( $cache_id );
		if ( ! empty( $video_id ) ) {
			return $video_id;
		}

		$data = $this->get_channel_html_by_id( $channel_id );
		if ( ! $data ) {
			return $is_live;
		}

		$this->is_embeddable = $this->get_is_embeddable( $data );
		if ( ! $this->is_embeddable ) {
			return $is_live;
		}

		$this->status = $this->get_live_stream_status( $data );
		if ( ! $this->status ) {
			return $is_live;
		}

		$this->video_id = $this->get_the_video_id( $data );
		if ( $this->video_id ) {
			$is_live = $this->video_id;
		}

		$expiration = MINUTE_IN_SECONDS * 3;
		$cache->set_transient( $cache_id, $is_live, $expiration );
		return $is_live;
	}

	/**
	 * Get the YouTube channel's html.
	 *
	 * @since 1.6.0
	 *
	 * @param string $channel_id The YouTube channel id.
	 *
	 * @return string The html of the request YouTube channel or empty string.
	 */
	protected function get_channel_html_by_id( $channel_id ) {
		$response = wp_remote_get( $this->get_url_with_id( $channel_id ) );
		$data = wp_remote_retrieve_body( $response );
		if ( is_wp_error( $data ) ) {
			return '';
		}

		$this->data = $data;

		return $data;
	}

	/**
	 * Get the current status of the live stream.
	 *
	 * @since 1.6.0
	 *
	 * @param string $data The html of the request YouTube channel.
	 *
	 * @return bool Whether the channel is live or not.
	 */
	protected function get_live_stream_status( $data ) {
		preg_match_all( $this->get_regex_status(), $data, $matches );
		if ( ! isset( $matches[1][1] ) ) {
			return false;
		}

		// If OK or LIVE_STREAM_OFFLINE we have a valid video
		if (
			'OK' === $matches[1][1]
			|| 'LIVE_STREAM_OFFLINE' === $matches[1][1]
		) {
			return true;
		}

		return false;
	}

	/**
	 * Get if a YouTube channel live video is embeddable.
	 *
	 * @since 1.6.0
	 *
	 * @param string $data The html of the request YouTube channel.
	 *
	 * @return bool Whether the live video can be embed or not.
	 */
	protected function get_is_embeddable( $data ) {
		preg_match_all( $this->get_regex_embed_allowed(), $data, $matches );
		if ( ! isset( $matches[0][0] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the video id if live.
	 *
	 * @since 1.6.0
	 *
	 * @param string $data The html of the request YouTube channel.
	 *
	 * @return string|boolean The live video id or false if not live.
	 */
	protected function get_the_video_id( $data ) {
		preg_match_all( $this->get_regex_video_id(), $data, $matches );
		if ( ! isset( $matches[1][0] ) ) {
			return false;
		}

		return esc_attr( $matches[1][0] );
	}

	/**
	 * Get the YouTube Channel url with the provided ID.
	 *
	 * @since 1.6.0
	 *
	 * @param string $channel_id The YouTube channel id.
	 *
	 * @return string The url with the channel id.
	 */
	public static function get_url_with_id( $channel_id = '' ) {
		return str_replace( '%%CHANNELID%%', $channel_id, self::$url_with_placeholder );
	}
}
