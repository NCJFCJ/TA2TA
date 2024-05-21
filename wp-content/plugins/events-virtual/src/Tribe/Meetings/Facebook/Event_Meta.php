<?php
/**
 * Handles the post meta related to Facebook Integration.
 *
 * @since   1.7.0
 *
 * @package Tribe\Events\Virtual\Meetings\Facebook
 */

namespace Tribe\Events\Virtual\Meetings\Facebook;

use Tribe\Events\Virtual\Event_Meta as Virtual_Event_Meta;
use Tribe\Events\Virtual\Meetings\Facebook\Event_Meta as Facebook_Meta;
use Tribe\Events\Virtual\Meetings\Facebook_Provider;
use Tribe__Utils__Array as Arr;
use WP_Post;

/**
 * Class Event_Meta
 *
 * @since   1.7.0
 *
 * @package Tribe\Events\Virtual\Meetings\Facebook
 */
class Event_Meta {

	/**
	 * The PAge API instance.
	 *
	 * @since 1.0.4
	 *
	 * @var Page_API
	 */
	public $api;

	/**
	 * {@inheritDoc}
	 */
	public static $key_source_id = 'facebook';

	/**
	 * The video source Facebook internal ID.
	 *
	 * @since 1.8.0
	 *
	 * @var string
	 */
	public static $video_source_fb_id = 'facebook';

	/**
	 * The Autodetect Facebook video internal ID.
	 *
	 * @since 1.8.0
	 *
	 * @var string
	 */
	public static $autodetect_fb_video_id = 'facebook-video';

	/**
	 * The array of Facebook fields.
	 *
	 * @since 1.7.0
	 *
	 * @var array<string>
	 */
	public static $fields = [
		'local_id',
	];

	/**
	 * The prefix, in the context of tribe_get_events, of each setting of Facebook.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	public static $prefix = 'facebook_';

	/**
	 * Event_Meta constructor.
	 *
	 * @since 1.7.0
	 *
	 * @param Page_API $api An instance of the Page_API handler.
	 */
	public function __construct( Page_API $api ) {
		$this->api = $api;
	}

	/**
	 * Get the prefix for the settings.
	 *
	 * @since 1.7.0
	 *
	 * @param string $key The meta key to add the prefix to.
	 *
	 * @return string The meta key with prefix added.
	 */
	protected static function get_prefix( $key ) {
		return static::$prefix . $key;
	}

	/**
	 * Returns an event post meta related to Facebook.
	 *
	 * @since 1.7.0
	 *
	 * @param int|\WP_Post $post The event post ID or object.
	 *
	 * @return array The Facebook post meta or an empty array if not found or not an event.
	 */
	public static function get_post_meta( $post ) {
		$event = tribe_get_event( $post );

		if ( ! $event instanceof \WP_Post ) {
			return [];
		}

		$all_meta = get_post_meta( $event->ID );

		$prefix = Virtual_Event_Meta::$prefix . 'facebook_';

		$flattened_array = Arr::flatten(
			array_filter(
				$all_meta,
				static function ( $meta_key ) use ( $prefix ) {
					return 0 === strpos( $meta_key, $prefix );
				},
				ARRAY_FILTER_USE_KEY
			)
		);

		return $flattened_array;
	}

	/**
	 * Add information about the Facebook live stream if available via the REST Api.
	 *
	 * @since 1.7.0
	 *
	 * @param array<string,mixed> $data  The current data of the event.
	 * @param \WP_Post            $event The event being updated.
	 *
	 * @return array<string,mixed> An array with the data of the event on the endpoint.
	 */
	public function attach_rest_properties( array $data, \WP_Post $event ) {
		$event = tribe_get_event( $event );

		if ( ! $event instanceof \WP_Post || ! current_user_can( 'read_private_posts' ) ) {
			return $data;
		}

		// Return when Facebook is not the source.
		if ( static::$video_source_fb_id !== $event->virtual_video_source ) {
			return $data;
		}

		if ( empty( $data['meetings'] ) ) {
			$data['meetings'] = [];
		}

		$data['meetings']['facebook'] = [
			'local_id' => $event->facebook_local_id,
			'is_live'  => $event->virtual_meeting_is_live,
		];

		return $data;
	}

	/**
	 * Adds Facebook related properties to an event post object.
	 *
	 * @since 1.7.0
	 *
	 * @param \WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
	 *
	 * @return \WP_Post The decorated event post object, with Facebook related properties added to it.
	 */
	public static function add_event_properties( \WP_Post $event ) {
		// Return when Facebook is not the source.
		if ( static::$video_source_fb_id !== $event->virtual_video_source ) {
			return $event;
		}

		// Get the saved values since the source is Facebook.
		foreach ( self::$fields as $field_name ) {
			$prefix_name = self::get_prefix( $field_name );
			$value = self::get_meta_field( $prefix_name, $event );
			$event->{$prefix_name} = $value;
		}

		// Enforce this is a virtual event.
		$event->virtual                  = true;
		$event->virtual_meeting          = true;
		$event->virtual_meeting_provider = tribe( Facebook_Provider::class )->get_slug();

		// Set virtual url to null if Facebook is connected to the event.
		$event->virtual_url = null;

		return $event;
	}

	/**
	 * Get the meta fields value.
	 *
	 * @since 1.7.0
	 *
	 * @param string $key     The option key to add the prefix to.
	 * @param \WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
	 *
	 * @return mixed
	 */
	protected static function get_meta_field( $key, \WP_Post $event ) {
		return get_post_meta( $event->ID, Virtual_Event_Meta::$prefix . $key, true );
	}

	/**
	 * Saves the meta fields for Facebook.
	 *
	 * @since 1.7.0
	 *
	 * @param int                 $post_id The post ID of the post the date is being saved for.
	 * @param array<string,mixed> $data    The data to save, directly from the metabox.
	 */
	public function save_metabox_data( $post_id, array $data ) {
		$event = tribe_get_event( $post_id );
		if ( static::$video_source_fb_id !== $event->virtual_video_source ) {
			return;
		}

		$prefix = Virtual_Event_Meta::$prefix;
		// An event that has a Facebook Page is always considered virtual, let's ensure that.
		update_post_meta( $post_id, Virtual_Event_Meta::$key_virtual, true );

		// Update meta fields.
		foreach ( self::$fields as $field_name ) {
			$name     = self::get_prefix( $field_name );
			$value    = Arr::get( $data, $name, false );
			$meta_key = $prefix . $name;

			if ( ! empty( $value ) ) {
				update_post_meta( $post_id, $meta_key, $value );
			} else {
				delete_post_meta( $post_id, $meta_key );
			}
		}
	}

	/**
	 * Removes the Facebook meta from a post.
	 *
	 * @since 1.7.0
	 *
	 * @param int|\WP_Post $post The event post ID or object.
	 */
	public static function delete_meta( $post ) {
		$event = tribe_get_event( $post );

		if ( ! $event instanceof \WP_Post ) {
			return false;
		}

		$facebook_meta = static::get_post_meta( $event );

		foreach ( array_keys( $facebook_meta ) as $meta_key ) {
			delete_post_meta( $event->ID, $meta_key );
		}

		return true;
	}

	/**
	 * Adds dynamic, time-related, properties to the event object.
	 *
	 * This method deals with properties we set, for convenience, on the event object that should not
	 * be cached as they are time-dependent; i.e. the time the properties are computed  at matters and
	 * caching their values would be incorrect.
	 *
	 * @since 1.7.0
	 *
	 * @param WP_Post $event The event post object, as read from the cache, if any.
	 *
	 * @return WP_Post The decorated event post object; its dynamic and time-dependent properties correctly set up.
	 */
	public function add_dynamic_properties( WP_Post $event ) {

		// Return the event post object in the admin as these properties are for the front end only.
		if ( is_admin() ) {
			return $event;
		}

		if (
			! isset( $event->virtual_video_source ) ||
			static::$video_source_fb_id !== $event->virtual_video_source
		) {
			return $event;
		}

		// Hide on a past event.
		if ( tribe_is_past_event( $event ) ) {
			return $event;
		}

		if (
			! isset(
				$event->virtual_embed_video,
				$event->virtual_should_show_embed,
				$event->facebook_local_id
			)
		) {
			return $event;
		}

		// Setup Facebook Live Stream.
		$live_stream = $this->api->get_live_stream( $event->facebook_local_id );

		// Set the status.
		$event->virtual_meeting_is_live = empty( $live_stream['status'] ) ? false : $live_stream['status'];

		// Set the meeting url, to the live stream or default to the Facebook page url.
		$video_permalink = empty( $live_stream['page_url'] ) ? '' : $live_stream['page_url'];
		if ( ! empty( $live_stream['permalink_url'] ) ) {
			$video_permalink = $live_stream['permalink_url'];
		}

		$event->virtual_meeting_url = esc_url( $video_permalink );
		// Override the virtual url if linked button is checked and stream is online.
		if ( ! empty( $event->virtual_linked_button ) ) {
			$event->virtual_url = $event->virtual_meeting_url;
		}

		if ( 'LIVE' !== $event->virtual_meeting_is_live ) {
			return $event;
		}

		$video_embed                  = is_string( $live_stream['embed_code'] ) ? json_decode( $live_stream['embed_code'] ) : '';
		$event->virtual_meeting_embed = $video_embed;

		return $event;
	}

	/**
	 * Filter the ticket email url.
	 *
	 * @since 1.7.2
	 *
	 * @param string  $virtual_url The virtual url for the ticket and rsvp emails.
	 * @param WP_Post $event       The event post object with properties added by the `tribe_get_event` function.
	 *
	 * @return string The Facebook Live virtual url for the ticket and rsvp emails.
	 */
	public function filter_ticket_email_url( $virtual_url, WP_Post $event ) {

		if ( static::$video_source_fb_id !== $event->virtual_video_source ) {
			return $virtual_url;
		}

		if ( ! isset( $event->facebook_local_id ) ) {
			return $virtual_url;
		}

		// Setup Facebook Live Stream.
		$live_stream = $this->api->get_live_stream( $event->facebook_local_id );

		return ! empty( $live_stream['page_url'] ) ? $live_stream['page_url'] : $virtual_url;
	}

	/**
	 * Adds related properties to an Event Automator event details map.
	 *
	 * @since 1.13.5
	 *
	 * @param array<string|mixed> $next_event An array of event details.
	 * @param WP_Post             $event      An instance of the event WP_Post object.
	 *
	 * @return array<string|mixed> An array of event details.
	 */
	public static function add_event_automator_properties( $next_event,  WP_Post $event ) {
		if ( $event->virtual_video_source !== static::$key_source_id ) {
			return $next_event;
		}

		$next_event['virtual_url']              = $event->virtual_meeting_url;
		$next_event['virtual_provider_details'] = [
			'facebook_local_id' => $event->facebook_local_id,
			'facebook_is_live'  => $event->virtual_meeting_is_live,
			'facebook_embed'    => $event->virtual_meeting_embed,
		];

		return $next_event;
	}
}
