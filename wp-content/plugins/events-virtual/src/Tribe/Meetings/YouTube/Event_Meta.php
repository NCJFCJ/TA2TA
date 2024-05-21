<?php
/**
 * Handles the post meta related to YouTube Integration.
 *
 * @since   1.6.0
 *
 * @package Tribe\Events\Virtual\Meetings\YouTube
 */

namespace Tribe\Events\Virtual\Meetings\YouTube;

use Tribe\Events\Virtual\Event_Meta as Virtual_Event_Meta;
use Tribe\Events\Virtual\Meetings\YouTube_Provider;
use Tribe__Utils__Array as Arr;
use WP_Post;

/**
 * Class Event_Meta
 *
 * @since   1.6.0
 *
 * @package Tribe\Events\Virtual\Meetings\YouTube
 */
class Event_Meta {

	/**
	 * {@inheritDoc}
	 */
	public static $key_source_id = 'youtube';

	/**
	 * The array of YouTube fields.
	 *
	 * @since 1.6.0
	 *
	 * @var array<string>
	 */
	public static $fields = [
		'channel_id',
		'autoplay',
		'live_chat',
		'mute_video',
		'modest_branding',
		'related_videos',
		'hide_controls',
	];

	/**
	 * The prefix, in the context of tribe_get_events, of each setting of YouTube.
	 *
	 * @since 1.6.0
	 *
	 * @var string
	 */
	public static $prefix = 'youtube_';

	/**
	 * Get the prefix for the settings.
	 *
	 * @since 1.6.0
	 *
	 * @param string $key The meta key to add the prefix to.
	 *
	 * @return string The meta key with prefix added.
	 */
	protected static function get_prefix( $key ) {
		return static::$prefix . $key;
	}

	/**
	 * Returns an event post meta related to YouTube.
	 *
	 * @since 1.6.0
	 *
	 * @param int|\WP_Post $post The event post ID or object.
	 *
	 * @return array The YouTube post meta or an empty array if not found or not an event.
	 */
	public static function get_post_meta( $post ) {
		$event = tribe_get_event( $post );

		if ( ! $event instanceof \WP_Post ) {
			return [];
		}

		$all_meta = get_post_meta( $event->ID );

		$prefix = Virtual_Event_Meta::$prefix . 'youtube_';

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
	 * Add information about the YouTube live stream if available via the REST Api.
	 *
	 * @since 1.6.0
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

		// Return when YouTube is not the source.
		if ( 'youtube' !== $event->virtual_video_source ) {
			return $data;
		}

		if ( empty( $data['meetings'] ) ) {
			$data['meetings'] = [];
		}

		$data['meetings']['youtube'] = [
			'channel_id'      => $event->youtube_channel_id,
			'is_live'         => $event->virtual_meeting_is_live,
			'auto_play'       => $event->youtube_autoplay,
			'live_chat'       => $event->youtube_live_chat,
			'mute_video'      => $event->youtube_mute_video,
			'modest_branding' => $event->youtube_modest_branding,
			'related_videos'  => $event->youtube_related_videos,
			'hide_controls'   => $event->youtube_hide_controls,
		];

		return $data;
	}

	/**
	 * Adds YouTube related properties to an event post object.
	 *
	 * @since 1.6.0
	 *
	 * @param \WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
	 *
	 * @return \WP_Post The decorated event post object, with YouTube related properties added to it.
	 */
	public static function add_event_properties( \WP_Post $event ) {
		// Return when YouTube is not the source.
		if ( 'youtube' !== $event->virtual_video_source ) {
			return $event;
		}

		// Get the saved values since the source is YouTube.
		foreach ( self::$fields as $field_name ) {
			$prefix_name = self::get_prefix( $field_name );
			$value = self::get_meta_field( $prefix_name, $event );
			$event->{$prefix_name} = $value;
		}

		// Enforce this is a virtual event.
		$event->virtual                  = true;
		$event->virtual_meeting          = true;
		$event->virtual_meeting_provider = tribe( YouTube_Provider::class )->get_slug();

		// Set virtual url to null if YouTube is connected to the event.
		$event->virtual_url = null;

		return $event;
	}

	/**
	 * Get the saved or default values.
	 *
	 * @since 1.6.0
	 *
	 * @param \WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
	 *
	 * @return array<string|mixed> The array of values for switch fields.
	 */
	public static function get_current_fields( \WP_Post $event ) {
		/** @var \Tribe\Events\Virtual\Meetings\YouTube\Settings */
		$settings         = tribe( Settings::class );
		$setting_defaults = $settings->get_fields( true );
		$fields           = [];
		$use_defaults     = false;

		// Use defaults when YouTube is not the source.
		if ( 'youtube' !== $event->virtual_video_source ) {
			$use_defaults = true;
		}

		foreach ( $setting_defaults as $setting_id => $field ) {
			// Remove the settings prefix to get matching fields.
			$meta_id = str_replace( 'tribe_', '', $setting_id );
			$name    = "tribe-events-virtual[{$meta_id}]";

			// Setup the field.
			$fields[ $name ] = [
				'label'   => $field['label'],
				'tooltip' => $field['tooltip'],
				'value'   => $use_defaults ? $field['value'] : self::get_meta_field( $meta_id, $event ),
			];
		}

		/**
		 * Filters the YouTube fields values for an event.
		 *
		 * @since  1.6.0
		 *
		 * @param array<string,array> A map of the YouTube Live fields that will be used for an event.
		 * @param bool $use_defaults Whether the default values are being used.
		 */
		return apply_filters( 'tribe_events_virtual_meetings_youtube_settings_event_fields', $fields, $use_defaults );
	}

	/**
	 * Get the meta fields value.
	 *
	 * @since 1.6.0
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
	 * Saves the meta fields for YouTube.
	 *
	 * @since 1.6.0
	 *
	 * @param int                 $post_id The post ID of the post the date is being saved for.
	 * @param array<string,mixed> $data    The data to save, directly from the metabox.
	 */
	public function save_metabox_data( $post_id, array $data ) {
		$event = tribe_get_event( $post_id );
		if ( 'youtube' !== $event->virtual_video_source ) {
			return;
		}

		$prefix = Virtual_Event_Meta::$prefix;
		$channel_id = get_post_meta( $post_id, $prefix . 'youtube_channel_id', true );

		// An event that has a YouTube Channel ID Meeting link should always be considered virtual, let's ensure that.
		if ( ! empty( $channel_id ) ) {
			update_post_meta( $post_id, Virtual_Event_Meta::$key_virtual, true );
		}

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
	 * Removes the YouTube meta from a post.
	 *
	 * @since 1.6.0
	 *
	 * @param int|\WP_Post $post The event post ID or object.
	 */
	public static function delete_meta( $post ) {
		$event = tribe_get_event( $post );

		if ( ! $event instanceof \WP_Post ) {
			return false;
		}

		$youtube_meta = static::get_post_meta( $event );

		foreach ( array_keys( $youtube_meta ) as $meta_key ) {
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
	 * @since 1.6.0
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

		if ( 'youtube' !== $event->virtual_video_source ) {
			return $event;
		}

		// Hide on a past event.
		if ( tribe_is_past_event( $event ) ) {
			return $event;
		}

		if ( ! $event->virtual_should_show_embed ) {
			return $event;
		}

		// Setup YouTube Live Stream.
		/** @var \Tribe\Events\Virtual\Meetings\YouTube\Connection $connection */
		$connection                     = tribe( Connection::class );
		$event->virtual_meeting_url     = $connection->get_url_with_id( $event->youtube_channel_id );
		$event->virtual_meeting_is_live = $connection->get_live_stream( $event->youtube_channel_id );

		// If offline there is no linked button to add.
		if ( Connection::$offline_key === $event->virtual_meeting_is_live ) {
			return $event;
		}

		// Override the virtual url if linked button is checked.
		if ( ! empty( $event->virtual_linked_button ) ) {
			$event->virtual_url = $event->virtual_meeting_url;
		}

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
	 * @return string The YouTube virtual url for the ticket and rsvp emails.
	 */
	public function filter_ticket_email_url( $virtual_url, WP_Post $event ) {

		if ( 'youtube' !== $event->virtual_video_source ) {
			return $virtual_url;
		}

		// Get Live Stream URL
		/** @var \Tribe\Events\Virtual\Meetings\YouTube\Connection $connection */
		$connection = tribe( Connection::class );

		return $connection->get_url_with_id( $event->youtube_channel_id );
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
	public static function add_event_automator_properties( array $next_event, WP_Post $event ) {
		if ( $event->virtual_video_source !== static::$key_source_id ) {
			return $next_event;
		}

		$next_event['virtual_url']              = $event->virtual_meeting_url;
		$next_event['virtual_provider_details'] = [
			'youtube_channel_id'      => $event->youtube_channel_id,
			'youtube_url'             => $event->virtual_meeting_url,
			'youtube_is_live'         => $event->virtual_meeting_is_live,
			'youtube_autoplay'        => $event->youtube_autoplay,
			'youtube_live_chat'       => $event->youtube_live_chat,
			'youtube_mute_video'      => $event->youtube_mute_video,
			'youtube_modest_branding' => $event->youtube_modest_branding,
			'youtube_related_videos'  => $event->youtube_related_videos,
			'youtube_hide_controls'   => $event->youtube_hide_controls,
		];

		return $next_event;
	}
}
