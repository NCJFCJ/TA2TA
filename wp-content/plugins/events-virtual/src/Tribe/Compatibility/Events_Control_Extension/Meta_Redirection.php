<?php
/**
 * Handles the redirection and migration of Online meta to Virtual events.
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual\Compatibility\Events_Control_Extension
 */

namespace Tribe\Events\Virtual\Compatibility\Events_Control_Extension;

use Tribe\Events\Virtual\Event_Meta as Virtual_Event_Meta;
use Tribe\Extensions\EventsControl\Event_Meta as Events_Control_Extension_Meta;
use Tribe__Events__Main as TEC;
use Tribe__Utils__Array as Arr;

/**
 * Class Meta_Redirection
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual\Compatibility\Events_Control_Extension
 */
class Meta_Redirection {

	/**
	 * Redirects requests for Online meta to virtual meta, if set, and vice-versa.
	 *
	 * The purpose of this redirection is to provide the extension with either Virtual Event data and, when not set,
	 * online event data.
	 * The method will perform an embedded migration converting Online event data to Virtual Event data if not yet
	 * migrated.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed|null  $meta_value The current meta value, initially `null`.
	 * @param int         $post_id    The post ID.
	 * @param string|null $meta_key   The meta key to fetch, or `null` to return all the post meta values.
	 * @param bool        $single     Whether the current request is for a single meta key entry or not.
	 *
	 * @return mixed The meta key value or values.
	 */
	public function redirect_online_meta_to_virtual_meta( $meta_value, $post_id, $meta_key = null, $single = false ) {
		if ( empty( $meta_key ) || TEC::POSTTYPE !== get_post_type( $post_id ) ) {
			return $meta_value;
		}

		$all_meta = Arr::flatten( get_post_meta( $post_id ) );

		$map = [
			// Online to Virtual.
			'_tribe_events_control_online'       => Virtual_Event_Meta::$key_virtual,
			'_tribe_events_control_online_url'   => Virtual_Event_Meta::$key_virtual_url,
			// Virtual to Online.
			Virtual_Event_Meta::$key_virtual     => '_tribe_events_control_online',
			Virtual_Event_Meta::$key_virtual_url => '_tribe_events_control_online_url',
		];

		if ( ! array_key_exists( $meta_key, $map ) ) {
			return $meta_value;
		}

		$direction = 0 === strpos( $meta_key, '_tribe_events_control_' )
			? 'online_to_virtual'
			: 'virtual_to_online';

		/*
		 * An event is migrated when it has no Online Event meta. If the event is new, then it's implicitly migrated.
		 */
		$migrated = ! isset( $all_meta['_tribe_events_control_online'], $all_meta['_tribe_events_control_online_url'] );

		if ( 'virtual_to_online' === $direction && $migrated ) {
			return $meta_value;
		}

		$redirected_meta_key = Arr::get( $map, $meta_key, false );

		if ( ! $migrated ) {
			$this->port_online_meta_to_virtual( $post_id );

			if ( $this->migrate_meta() ) {
				// Remove the Online meta.
				delete_post_meta( $post_id, '_tribe_events_control_online' );
				delete_post_meta( $post_id, '_tribe_events_control_online_url' );
			}
		}

		if ( 'online_to_virtual' === $direction ) {
			// No event Online attribute is ever true when the plugin is active.
			$value = '';
		} else {
			$value = Arr::get( $all_meta, $redirected_meta_key, '' );
		}

		return $single ? $value : [ $value ];
	}

	/**
	 * Copies the data from the Virtual event data that is saved from the metabox to the Online event one to
	 * ensure the extension will correctly handle the save.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The post ID of the event currently being saved.
	 */
	public function sync_online_meta( $post_id ) {
		if ( $this->migrate_meta() ) {
			// Don't keep anything in sync if we've decided to migrate meta.
			return;
		}

		if ( ! class_exists( '\Tribe\Extensions\EventsControl\Event_Meta' ) ) {
			// This should really not happen, but being this method public it might be called out of scope.
			return;
		}

		// Use all-meta to avoid this class redirection mechanism.
		$all_meta = Arr::flatten( get_post_meta( $post_id ) );

		if ( tribe_is_truthy( Arr::get( $all_meta, Virtual_Event_Meta::$key_virtual, false ) ) ) {
			$online_url = Arr::get( $all_meta, Virtual_Event_Meta::$key_virtual_url, '' );
			update_post_meta( $post_id, Events_Control_Extension_Meta::$key_online, true );
			update_post_meta( $post_id, Events_Control_Extension_Meta::$key_online_url, $online_url );
		} else {
			delete_post_meta( $post_id, Events_Control_Extension_Meta::$key_online );
			delete_post_meta( $post_id, Events_Control_Extension_Meta::$key_online_url );
		}
	}

	/**
	 * Returns the filtered value that will decide if post meta should be migrated from the extension to the
	 * plugin destructively or not.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether post meta should be migrated from the extension to the plugin destructively or not.
	 */
	protected function migrate_meta() {
		/**
		 * Filters whether the plugin should migrate the event meta, thus irreversibly changing it, from the extension
		 * Online status to the Virtual one or not.
		 *
		 * If this filter returns a `falsy` value, then no event meta will be altered and virtual-related meta
		 * will be redirected to the extension.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $migrate Whether the plugin should migrate the event meta, thus irreversibly changing it,
		 *                      from the extension Online status to the Virtual one or not.
		 */
		$migrate = apply_filters( 'tribe_events_virtual_compatibility_migrate_from_events_control_extension', false );

		return (bool) $migrate;
	}

	/**
	 * Sets some default meta on events when first migrating them from the extension to the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The event post ID.
	 *
	 * @see   \Tribe\Events\Virtual\Models\Event for the defaults in the getters.
	 */
	protected function set_default_meta( $post_id ) {
		update_post_meta( $post_id, Virtual_Event_Meta::$key_embed_video, true );
		update_post_meta( $post_id, Virtual_Event_Meta::$key_linked_button, true );
		update_post_meta(
			$post_id,
			Virtual_Event_Meta::$key_linked_button_text,
			Virtual_Event_Meta::linked_button_default_text()
		);
		update_post_meta( $post_id, Virtual_Event_Meta::$key_show_on_event, true );
		update_post_meta( $post_id, Virtual_Event_Meta::$key_show_on_views, true );
	}

	/**
	 * Ports the Online meta over to Virtual.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The ID of the post to perform the port for.
	 */
	protected function port_online_meta_to_virtual( $post_id ) {
		$all_meta = Arr::flatten( get_post_meta( $post_id ) );

		// Fetch the current values.
		$virtual_value = Arr::get( $all_meta, '_tribe_events_control_online', false );
		$virtual_url   = Arr::get( $all_meta, '_tribe_events_control_online_url', '' );

		// Un-hook the provider method, that will proxy to this one, before setting the values to avoid loops.
		$provider = tribe( Service_Provider::class );
		remove_filter( 'get_post_metadata', [ $provider, 'redirect_online_meta_to_virtual_meta' ], 20 );

		// Set the Virtual meta.
		$migration_map = [
			Virtual_Event_Meta::$key_virtual     => $virtual_value,
			Virtual_Event_Meta::$key_virtual_url => $virtual_url,
		];

		foreach ( $migration_map as $key => $value ) {
			if ( empty( $value ) ) {
				delete_post_meta( $post_id, $key );
			} else {
				update_post_meta( $post_id, $key, $value );
			}
		}

		// Set some defaults on the event.
		$this->set_default_meta( $post_id );

		// Re-hook after the migration completed.
		add_filter( 'get_post_metadata', [ $provider, 'redirect_online_meta_to_virtual_meta' ], 20, 4 );
	}
}
