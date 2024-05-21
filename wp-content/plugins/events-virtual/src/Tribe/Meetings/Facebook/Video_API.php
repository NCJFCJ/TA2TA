<?php
/**
 * Manages the Connection to Facebook Video API.
 *
 * @since   1.7.0
 *
 * @package Tribe\Events\Virtual\Meetings\Facebook
 */

namespace Tribe\Events\Virtual\Meetings\Facebook;

use Tribe\Events\Virtual\Event_Meta as Virtual_Events_Meta;
use Tribe\Events\Virtual\Integrations\Api_Response;
use Tribe\Events\Virtual\Meetings\Facebook\Event_Meta as Facebook_Meta;
use Tribe\Events\Virtual\Template;

/**
 * Class Video_API
 *
 * @since   1.7.0
 *
 * @package Tribe\Events\Virtual\Meetings\Facebook
 */
abstract class Video_API {

	/**
	 * Expected response code for GET requests.
	 *
	 * @since 1.7.0
	 *
	 * @var integer
	 */
	const GET_RESPONSE_CODE = 200;

	/**
	 * The Facebook page url with placeholder for page id.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	public static $page_url_with_placeholder = 'https://facebook.com/%%PAGEID%%';

	/**
	 * The url to connect to the Facebook API to get the live videos.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	public static $live_videos_url_with_placeholder = 'https://graph.facebook.com/v12.0/%%PAGEID%%/live_videos';

	/**
	 * The url to connect to the Facebook API to get the live videos.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	public static $live_video_permalink_with_placeholder = 'https://graph.facebook.com/%%VIDEOID%%?fields=permalink_url';

	/**
	 * The regex to detect a Facebook video url.
	 * Source - https://stackoverflow.com/a/65484777
	 *
	 * @since 1.8.0
	 *
	 * @var string
	 */
	public static $facebook_video_regex = '/(?:https?:\/\/)?(?:www.|web.|m.)?(facebook|fb).(com|watch)\/(?:video.php\?v=\d+|(\S+)|photo.php\?v=\d+|\?v=\d+)|\S+\/videos\/((\S+)\/(\d+)|(\d+))\/?/';

	/**
	 * The Facebook page id.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	public $page_id;

	/**
	 * The Facebook access token.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	public $access_token;

	/**
	 * Get the Facebook page url with the provided page id.
	 *
	 * @since 1.7.0
	 *
	 * @param string $page_id The Facebook Page ID.
	 *
	 * @return string The url with page id.
	 */
	protected static function get_page_url_with_page_id( $page_id ) {
		/**
		 * Allow filtering of the Facebook API url to get live videos.
		 *
		 * @since 1.7.0
		 *
		 * @param string The url to get a Facebook Page's live videos.
		 */
		$page_url = apply_filters( 'tribe_events_virtual_facebook_page_url_with_placeholder', static::$page_url_with_placeholder  );

		return str_replace( '%%PAGEID%%', $page_id, $page_url );
	}

	/**
	 * Get the Facebook page url with the provided page id.
	 *
	 * @since 1.7.3
	 *
	 * @param string $page_id The Facebook Page ID.
	 *
	 * @return string The url with page id.
	 */
	public static function get_facebook_page_url_with_page_id( $page_id ) {
		return esc_url( static::get_page_url_with_page_id( $page_id ) );
	}

	/**
	 * Get the Facebook API url with the provided page id.
	 *
	 * @since 1.7.0
	 *
	 * @param string $page_id The Facebook Page ID.
	 *
	 * @return string The url with page id.
	 */
	protected static function get_videos_url_with_page_id( $page_id ) {
		/**
		 * Allow filtering of the Facebook API url to get live videos.
		 *
		 * @since 1.7.0
		 *
		 * @param string The url to get a Facebook Page's live videos.
		 */
		$live_videos_url = apply_filters( 'tribe_events_virtual_facebook_live_videos_url_with_placeholder', static::$live_videos_url_with_placeholder  );

		return str_replace( '%%PAGEID%%', $page_id, $live_videos_url );
	}

	/**
	 * Get the Facebook API video url to retrieve the permalink.
	 *
	 * @since 1.7.0
	 *
	 * @param string $video_id The Facebook video id.
	 *
	 * @return string The Facebook API video url with page id.
	 */
	protected static function get_video_url_permalink_with_video_id( $video_id ) {
		/**
		 * Allow filtering of Facebook video api url to get the video permalink
		 *
		 * @since 1.7.0
		 *
		 * @param string The url to get a Facebook video's permalink.
		 */
		$live_video_url = apply_filters( 'tribe_events_virtual_facebook_live_video_permalink_with_placeholder', static::$live_video_permalink_with_placeholder );

		return str_replace( '%%VIDEOID%%', $video_id, $live_video_url );
	}

	/**
	 * Get the Facebook video regex.
	 *
	 * @since 1.8.0
	 *
	 * @return string The regex to detect the Facebook video url.
	 */
	protected static function get_facebook_video_regex() {
		/**
		 * Allow filtering of Facebook video regex.
		 *
		 * @since TB
		 *
		 * @param string The regex to detect the Facebook video url.
		 */
		return apply_filters( 'tec_virtual_facebook_video_regex', static::$facebook_video_regex );
	}

	/**
	 * Get the Facebook video api url with a access token.
	 *
	 * @since 1.7.0
	 *
	 * @return string The url to access the Facebook video api with a access token.
	 */
	protected function get_video_api_url_with_access_token() {
		return add_query_arg( [
			'access_token' => $this->access_token,
		], $this->get_videos_url_with_page_id( $this->page_id ) );
	}

	/**
	 * Get the Facebook video api url for the permalink with a access token.
	 *
	 * @since 1.7.0
	 *
	 * @param int $video_id The Facebook video id.
	 *
	 * @return string The url to access the Facebook video api to get the video permalink.
	 */
	protected function get_video_url_permalink_with_access_token( $video_id ) {
		return add_query_arg( [
			'access_token' => $this->access_token,
		], $this->get_video_url_permalink_with_video_id( $video_id ) );
	}

	/**
	 * Makes a GET request to the Facebook API.
	 *
	 * @since 1.7.0
	 *
	 * @param string              $url         The URL to make the request to.
	 * @param array<string|mixed> $args        An array of arguments for the request.
	 * @param int                 $expect_code The expected response code, if not met, then the request will be considered a failure.
	 *                                         Set to `null` to avoid checking the status code.
	 *
	 * @return Api_Response An API response to act upon the response result.
	 */
	public function get( $url, array $args, $expect_code = self::GET_RESPONSE_CODE ) {
		$args['method'] = 'GET';

		return $this->request( $url, $args, $expect_code );
	}

	/**
	 * Makes a request to the Facebook API.
	 *
	 * @since 1.7.0
	 *
	 * @param string              $url         The URL to make the request to.
	 * @param array<string|mixed> $args        An array of arguments for the request. Should include 'method' (POST/GET/PATCH, etc).
	 * @param int                 $expect_code The expected response code, if not met, then the request will be considered a failure.
	 *                                         Set to `null` to avoid checking the status code.
	 *
	 * @return Api_Response An API response to act upon the response result.
	 */
	private function request( $url, array $args, $expect_code = self::GET_RESPONSE_CODE ) {
		/**
		 * Filters the response for a Facebook API request to prevent the response from actually happening.
		 *
		 * @since 1.7.0
		 *
		 * @param null|Api_Response|\WP_Error|mixed $response    The response that will be returned. A non `null` value
		 *                                                       here will short-circuit the response.
		 * @param string                            $url         The full URL this request is being made to.
		 * @param array<string,mixed>               $args        The request arguments.
		 * @param int                               $expect_code The HTTP response code expected for this request.
		 */
		$response = apply_filters( 'tribe_events_virtual_meetings_facebook_api_post_response', null, $url, $args, $expect_code );

		if ( null !== $response ) {
			return Api_Response::ensure_response( $response );
		}

		$response = wp_remote_request( $url, $args );

		if ( $response instanceof \WP_Error ) {
			$error_message = $response->get_error_message();

			do_action(
				'tribe_log',
				'error',
				__CLASS__,
				[
					'action'  => __METHOD__,
					'code'    => $response->get_error_code(),
					'message' => $error_message,
					'method'  => $args['method'],
				]
			);

			$user_message = sprintf(
				// translators: the placeholder is for the error as returned from Facebook API.
				_x(
					'Error while trying to communicate with Facebook API: %1$s. Please try again in a minute.',
					'The prefix of a message reporting a Facebook API communication error, the placeholder is for the error.',
					'events-virtual'
				),
				$error_message
			);
			tribe_transient_notice(
				'events-virtual-facebook-request-error',
				'<p>' . esc_html( $user_message ) . '</p>',
				[ 'type' => 'error' ],
				60
			);

			return new Api_Response( $response );
		}

		$response_code = wp_remote_retrieve_response_code( $response );

		if ( null !== $expect_code && $expect_code !== $response_code ) {
			$data = [
				'action'        => __METHOD__,
				'message'       => 'Response code is not the expected one.',
				'expected_code' => $expect_code,
				'response_code' => $response_code,
				'api_method'    => $args['method'],
			];
			do_action( 'tribe_log', 'error', __CLASS__, $data );

			$user_message = sprintf(
				// translators: the placeholders are, respectively, for the expected and actual response codes.
				_x(
					'Facebook API response is not the expected one, expected %1$s, received %2$s. Please, try again in a minute.',
					'The message reporting a Facebook API unexpected response code, placeholders are the codes.',
					'events-virtual'
				),
				$expect_code,
				$response_code
			);
			tribe_transient_notice(
				'events-virtual-facebook-response-error',
				'<p>' . esc_html( $user_message ) . '</p>',
				[ 'type' => 'error' ],
				60
			);

			return new Api_Response( new \WP_Error( $response_code, 'Response code is not the expected one.', $data ) );
		}

		return new Api_Response( $response );
	}

	/**
	 * Get the Facebook live stream information by local id.
	 *
	 * @since 1.7.0
	 *
	 * @param string $local_id The local Facebook Page id to get and load for use with the API.
	 *
	 * @return array<string|mixed> An array of live stream information.
	 */
	public function get_live_stream( $local_id ) {
		$video = [
			'status'        => false,
			'video_id'      => '',
			'page_url'      => '',
			'permalink_url' => '',
			'embed_code'    => '',
		];

		$loaded = $this->load_page_by_id( $local_id );
		if ( true !== $loaded ) {
			return $video;
		}

		// Set the page url.
		$video['page_url'] = esc_url( $this->get_page_url_with_page_id( $this->page_id ) );

		$cache    = tribe( 'cache' );
		$cache_id = 'events_virtual_meetings_facebook_' . $local_id;
		$save_video = $cache->get( $cache_id );
		if ( ! empty( $save_video ) ) {
			return $save_video;
		}

		$this->get(
			$this->get_video_api_url_with_access_token(),
			[]
		)->then(
			function ( array $response ) use ( &$video ) {

				$body = json_decode( wp_remote_retrieve_body( $response ) );
				if ( empty( $body ) ) {
					do_action( 'tribe_log', 'error', __CLASS__, [
						'action'   => __METHOD__,
						'message'  => 'Facebook API response is missing the required information to display the live stream.',
						'response' => $body,
					] );

					return false;
				}

				if ( ! isset( $body->data[0]->id ) ) {
					return false;
				}

				$live_video   = $body->data[0];
				$fb_permalink = $this->get_live_stream_permalink( $live_video->id );

				$video = [
					'status'        => esc_attr( $live_video->status ),
					'video_id'      => esc_attr( $live_video->id ),
					'page_url'      => esc_url( $this->get_page_url_with_page_id( $this->page_id ) ),
					'permalink_url' => esc_url( $fb_permalink ),
					// Encode the embed to store in the cache.
					'embed_code'    => wp_json_encode( $live_video->embed_html ),
				];

				return true;
			}
		)->or_catch(
			function ( \WP_Error $error ) {
				do_action( 'tribe_log', 'error', __CLASS__, [
					'action'  => __METHOD__,
					'code'    => $error->get_error_code(),
					'message' => $error->get_error_message(),
				] );
			}
		);

		// Cache for 30 seconds, due to Facebook not having a preview video state.
		$expiration = MINUTE_IN_SECONDS * .5;
		$cache->set( $cache_id, $video, $expiration );

		return $video;
	}

	/**
	 * Get the video live stream permalink.
	 *
	 * @since 1.7.0
	 *
	 * @param int $video_id The Facebook video id.
	 *
	 * @return string The video permalink or empty string if not found.
	 */
	public function get_live_stream_permalink( $video_id ) {
		$fb_permalink = '';

		$this->get(
			$this->get_video_url_permalink_with_access_token( $video_id ),
			[]
	    )->then(
			function ( array $response ) use ( &$fb_permalink ) {

				$body = json_decode( wp_remote_retrieve_body( $response ) );
				if ( empty( $body ) ) {
				   do_action( 'tribe_log', 'error', __CLASS__, [
				       'action'   => __METHOD__,
				       'message'  => 'Facebook API response is missing the required information to get the video permalink.',
				       'response' => $body,
				   ] );

				   return false;
				}

				  if ( empty( $body->permalink_url ) ) {
				      return false;
				  }

				  $fb_permalink = 'https://www.facebook.com' . $body->permalink_url;

				  return true;
			}
		)->or_catch(
			function ( \WP_Error $error ) {
			  do_action( 'tribe_log', 'error', __CLASS__, [
			      'action'  => __METHOD__,
			      'code'    => $error->get_error_code(),
			      'message' => $error->get_error_message(),
			  ] );
			}
		);

		return $fb_permalink;
	}

	/**
	 * Load a specific page by the id.
	 *
	 * @since 1.7.0
	 *
	 * @param string $local_id The local Facebook Page id to get and load for use with the API.
	 *
	 * @return bool|string Whether the page is loaded or an error code. False or code means the page did not load.
	 */
	abstract function load_page_by_id( $local_id );

	/**
	 * Filter the autodetect source to detect if a Facebook video link.
	 *
	 * @since 1.8.0
	 *
	 * @param array<string|mixed> $autodetect   An array of the autodetect defaults.
	 * @param string              $video_url    The url to use to autodetect the video source.
	 * @param string              $video_source The optional name of the video source to attempt to autodetect.
	 * @param \WP_Post|null       $event        The event post object, as decorated by the `tribe_get_event` function.
	 * @param array<string|mixed> $ajax_data    An array of extra values that were sent by the ajax script.
	 *
	 * @return array<string|mixed> An array of the autodetect results.
	 */
	public function filter_virtual_autodetect_facebook_video( $autodetect, $video_url, $video_source, $event, $ajax_data ) {
		if ( $autodetect['detected'] || $autodetect['guess'] ) {
			return $autodetect;
		}

		// All video sources are checked on the first autodetect run, only prevent checking of this source if it is set.
		if ( ! empty( $video_source ) && Facebook_Meta::$autodetect_fb_video_id !== $video_source ) {
			return $autodetect;
		}

		// If no video url found, fail the request.
		if ( empty( $video_url ) ) {
			$autodetect['message'] = _x( 'No video found. Please enter a Facebook video URL or change the selected source.', 'Facebook video autodetect missing video url error message.', 'events-virtual' );

			return $autodetect;
		}

		// Detect if the url is a Facebook video url.
		preg_match( $this->get_facebook_video_regex(), $video_url, $matches );
		$facebook_url     = isset( $matches[1] ) ? $matches[1] : false;
		if ( ! $facebook_url ) {
			$error_message = _x( 'This is not a valid Facebook video URL. Please recheck the URL and try again.', 'Invalid Facebook video url for autodetect error message.', 'events-virtual' );
			$autodetect['message'] = $error_message;

			return $autodetect;
		}

		$autodetect['guess'] = Facebook_Meta::$autodetect_fb_video_id;

		// Set Facebook video as the autodetect source and setup success data and send back to smart url ui.
		update_post_meta( $event->ID, Virtual_Events_Meta::$key_autodetect_source, Facebook_Meta::$autodetect_fb_video_id );
		$autodetect['detected']          = true;
		$autodetect['autodetect-source'] = Facebook_Meta::$autodetect_fb_video_id;
		$autodetect['message']           = _x( "Success! Save your event to add this video.", 'Facebook video valid success message.', 'events-virtual' );

		// Preview video setup.
		$event->virtual_url = filter_var( $video_url, FILTER_VALIDATE_URL );
		// Set for the preview video to always show.
		$event->virtual_embed_video       = $event->virtual_should_show_embed = true;
		$event->virtual_autodetect_source = Facebook_Meta::$autodetect_fb_video_id;
		$autodetect['preview-html'] = tribe( Template::class )->template( 'facebook/single/facebook-video-embed', [ 'event' => $event ] );

		return $autodetect;
	}
}
