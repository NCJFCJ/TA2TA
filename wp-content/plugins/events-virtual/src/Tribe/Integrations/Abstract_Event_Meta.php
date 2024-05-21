<?php
/**
 * Abstract Class to manage integration meta.
 *
 * @since   1.11.0
 *
 * @package Tribe\Events\Virtual\Integrations
 */

namespace Tribe\Events\Virtual\Integrations;

use Tribe\Events\Virtual\Encryption;
use Tribe\Events\Virtual\Event_Meta as Virtual_Event_Meta;
use Tribe__Utils__Array as Arr;
use WP_Post;

/**
 * Class Abstract_Event_Meta
 *
 * @since   1.11.0
 *
 * @package Tribe\Events\Virtual\Integrations
 */
abstract class Abstract_Event_Meta {

	/**
	 * Key for the API video and autodetect source.
	 *
	 * @since 1.11.0
	 *
	 * @var string
	 */
	public static $key_source_id = '';

	/**
	 * An array of fields to encrypt fields.
	 *
	 * @since 1.11.0
	 *
	 * @var array<string|boolean> An array of field names and whether the field is an array.
	 */
	public static $encrypted_fields = [];

	/**
	 * An array of action names used to create an API integration.
	 *
	 * @since 1.11.0
	 *
	 * @var array<string|string> An array of action names.
	 */
	protected static $create_actions = [];

	/**
	 * Gets the meeting id for an API.
	 *
	 * @since 1.11.0
	 *
	 * @param WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
	 *
	 * @return string The meeting id for an API.
	 */
	abstract protected static function get_meeting_id( WP_Post $event );

	/**
	 * Gets the meeting join url for an API.
	 *
	 * @since 1.11.0
	 *
	 * @param WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
	 *
	 * @return string The meeting url for an API.
	 */
	abstract protected static function get_meeting_join_url( WP_Post $event );

	/**
	 * Gets the meeting data of an API for the Events REST API.
	 *
	 * @since 1.11.0
	 *
	 * @param WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
	 *
	 * @return array<string|mixed> An array of data from an API for the Events REST API.
	 */
	abstract protected static function get_meeting_data_for_rest_api( WP_Post $event );

	/**
	 * Removes the API meta from a post.
	 *
	 * @since 1.11.0
	 *
	 * @param int|WP_Post $post The event post ID or object.
	 */
	public static function delete_meeting_meta( $post ) {
		$event = tribe_get_event( $post );

		if ( ! $event instanceof WP_Post ) {
			return false;
		}

		$api_meta = static::get_post_meta( $event );

		foreach ( array_keys( $api_meta ) as $meta_key ) {
			delete_post_meta( $event->ID, $meta_key );
		}

		return true;
	}

	/**
	 * Returns an event post meta related to API.
	 *
	 * @since 1.11.0
	 *
	 * @param int|WP_Post $post The event post ID or object.
	 *
	 * @return array The API post meta or an empty array if not found or not an event.
	 */
	public static function get_post_meta( $post ) {
		$event = tribe_get_event( $post );

		if ( ! $event instanceof WP_Post ) {
			return [];
		}

		$all_meta = get_post_meta( $event->ID );

		$prefix = Virtual_Event_Meta::$prefix . static::$key_source_id  . '_';

		$flattened_array = Arr::flatten(
			array_filter(
				$all_meta,
				static function ( $meta_key ) use ( $prefix ) {
					return 0 === strpos( $meta_key, $prefix );
				},
				ARRAY_FILTER_USE_KEY
			)
		);

		$encrypted_fields = static::$encrypted_fields;
		if ( empty( $encrypted_fields) ) {
			return $flattened_array;
		}

		// Decrypt the encrypted meta fields.
		$encryption       = tribe( Encryption::class );
		foreach ( $flattened_array as $meta_key => $meta_value ) {
			$encrypted_field_key = str_replace( $prefix, '', $meta_key );

			if ( ! array_key_exists( $encrypted_field_key, $encrypted_fields ) ) {
				continue;
			}

			$flattened_array[ $meta_key ] = $encryption->decrypt( $meta_value, $encrypted_fields[ $encrypted_field_key ] );
		}

		return $flattened_array;
	}

	/**
	 * Parses and Saves the data from a metabox update request.
	 *
	 * @since 1.11.0
	 *
	 * @param int                 $post_id The post ID of the post the date is being saved for.
	 * @param array<string,mixed> $data    The data to save, directly from the metabox.
	 */
	public function save_metabox_data( $post_id, array $data ) {
		$prefix = Virtual_Event_Meta::$prefix;

		$join_url = get_post_meta( $post_id, $prefix . static::$key_source_id . '_join_url', true );

		// An event that has an API integration should always be considered virtual, let's ensure that.
		if ( ! empty( $join_url ) ) {
			update_post_meta( $post_id, Virtual_Event_Meta::$key_virtual, true );
		}

		$map = [
			'meetings-api-display-details' => $prefix . static::$key_source_id . '_display_details',
		];
		foreach ( $map as $data_key => $meta_key ) {
			$value = Arr::get( $data, 'meetings-api-display-details', false );
			if ( ! empty( $value ) ) {
				update_post_meta( $post_id, $meta_key, $value );
			} else {
				delete_post_meta( $post_id, $meta_key );
			}
		}
	}

	/**
	 * Add information about the API if available, only if the user has permission to read_private_posts via the REST Api.
	 *
	 * @since 1.11.0
	 *
	 * @param array<string,mixed> $data  The current data of the event.
	 * @param WP_Post            $event The event being updated.
	 *
	 * @return array<string,mixed> An array with the data of the event on the endpoint.
	 */
	public function attach_rest_properties( array $data, WP_Post $event ) {
		$event = tribe_get_event( $event );

		if ( ! $event instanceof WP_Post || ! current_user_can( 'read_private_posts' ) ) {
			return $data;
		}

		// Return when API is not the source.
		if ( static::$key_source_id !== $event->virtual_video_source ) {
			return $data;
		}

		if ( empty( $data['meetings'] ) ) {
			$data['meetings'] = [];
		}

		if ( ! $event->virtual || empty( $this->get_meeting_id( $event ) ) ) {
			return $data;
		}

		$data['meetings'][static::$key_source_id] = $this->get_meeting_data_for_rest_api( $event );

		return $data;
	}

	/**
	 * Adds API related properties to an event post object.
	 *
	 * @since 1.11.0
	 *
	 * @param WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
	 *
	 * @return WP_Post The decorated event post object, with the API related properties added to it.
	 */
	public static function add_event_properties( WP_Post $event ) {
		// Get the current actions
		$current_action = tribe_get_request_var( 'action' );

		// Return when the API is not the source and not running the create actions.
		if ( static::$key_source_id !== $event->virtual_video_source && ! in_array( $current_action, static::$create_actions ) ) {
			return $event;
		}

		$prefix       = Virtual_Event_Meta::$prefix;
		$is_new_event = empty( $event->ID );

		$event    = static::get_api_properties( $event, $prefix, $is_new_event );
		$join_url = static::get_meeting_join_url( $event );

		if ( ! empty( $join_url ) ) {
			// An event that has an API integration assigned should be considered virtual.
			$event->virtual                  = true;
			$event->virtual_meeting          = true;
			$event->virtual_meeting_url      = $join_url;
			$event->virtual_meeting_provider = static::$key_source_id;

			// Override the virtual url if no API details and linked button is checked.
			if (
				empty( $event->virtual_meeting_display_details )
				&& ! empty( $event->virtual_linked_button )
			) {
				$event->virtual_url = $event->virtual_meeting_url;
			} else {
				// Set virtual url to null if an API is connected to the event.
				$event->virtual_url = null;
			}
		}

		return $event;
	}

	/**
	 * Gets the API properties for a specific API.
	 *
	 * @since 1.11.0
	 *
	 * @param WP_Post $event        The event post object, as decorated by the `tribe_get_event` function.
	 * @param string  $prefix       The Virtual Event meta field prefix..
	 * @param boolean $is_new_event Whether the event is new.
	 *
	 * @return WP_Post The decorated event post object, with the API related properties added to it.
	 */
	abstract protected static function get_api_properties( WP_Post $event, $prefix, $is_new_event );
}
