<?php
/**
 * Handles the modifications to the Event model returned by the `tribe_get_event` function.
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual\Models
 */

namespace Tribe\Events\Virtual\Models;

use Tribe\Events\Virtual\Event_Meta;
use Tribe__Date_Utils as Dates;
use Tribe__Timezones as Timezones;
use WP_Post;

/**
 * Class Event
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual\Models
 */
class Event {

	/**
	 * Filters the object returned by the `tribe_get_event` function to add to it properties related to virtual events.
	 *
	 * @since 1.0.0
	 * @since 1.4.0 Add hybrid field.
	 * @since 1.6.0 Add video source.
	 *
	 * @param WP_Post $event The event post object.
	 *
	 * @return WP_Post The original event object decorated with properties related to virtual events.
	 */
	public function add_properties( WP_Post $event ) {
		$event->virtual_event_type         = $this->get_virtual_event_type( $event );
		$event->virtual                    = self::is_virtual( $event );
		$event->virtual_video_source       = $this->get_video_source( $event );
		$event->virtual_autodetect_source  = $this->get_autodetect_source( $event );
		$event->virtual_url                = $this->get_virtual_url( $event );
		$event->virtual_embed_video        = $this->get_virtual_embed_video( $event );
		$event->virtual_linked_button      = $this->get_virtual_linked_button( $event );
		$event->virtual_linked_button_text = $this->get_virtual_linked_button_text( $event );
		$event->virtual_show_embed_at      = $this->get_virtual_show_embed_at( $event );
		$event->virtual_show_embed_to      = $this->get_virtual_show_embed_to( $event );
		$event->virtual_show_on_event      = $this->get_virtual_show_on_event( $event );
		$event->virtual_show_on_views      = $this->get_virtual_show_on_views( $event );
		$event->virtual_show_lead_up       = $this->get_virtual_show_lead_up( $event );

		/**
		 * Fires after the event object has been decorated with properties related to Virtual Events.
		 *
		 * @since 1.0.0
		 *
		 * @param \WP_Post $event The event post object as decorated by the `tribe_get_event` function, with Virtual
		 *                        Events related properties added.
		 */
		do_action( 'tribe_events_virtual_add_event_properties', $event );

		return $event;
	}

	/**
	 * Retrieves whether the event is marked as virtual or not.
	 *
	 * @since 1.0.0
	 * @since 1.0.4 Changed to static method.
	 *
	 * @param WP_Post $event Event post object.
	 *
	 * @return boolean Whether the event is virtual or not.
	 */
	protected static function is_virtual( WP_Post $event ) {
		$is_virtual = get_post_meta( $event->ID, Event_Meta::$key_virtual, true );

		/**
		 * Filters whether an event is considered virtual or not.
		 *
		 * @since 1.0.0
		 *
		 * @param boolean  $is_virtual Whether the event is considered virtual or not.
		 * @param \WP_Post $event      The event post object.
		 */
		return tribe_is_truthy( apply_filters( 'tribe_events_virtual_is_virtual_event', $is_virtual, $event ) );
	}

	/**
	 * Retrieves an event's type.
	 *
	 * @since 1.4.0
	 *
	 * @param WP_Post $event Event post object.
	 *
	 * @return null|string The event's type.
	 */
	protected function get_virtual_event_type( WP_Post $event ) {
		if ( ! self::is_new_virtual( $event ) ) {
			return Event_Meta::$value_virtual_event_type;
		}

		$value = get_post_meta( $event->ID, Event_Meta::$key_type, true );

		if ( ! empty( $value ) ) {
			return $value;
		}

		return Event_Meta::$value_virtual_event_type;
	}

	/**
	 * Retrieves an event's virtual video source.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_Post $event Event post object.
	 *
	 * @return string The event's video source or empty string if not a virtual event.
	 */
	protected static function get_video_source( WP_Post $event ) {
		if ( ! self::is_virtual( $event ) ) {
			return '';
		}

		return get_post_meta( $event->ID, Event_Meta::$key_video_source, true );
	}

	/**
	 * Retrieves an event's virtual autodetect source.
	 *
	 * @since 1.8.0
	 *
	 * @param WP_Post $event Event post object.
	 *
	 * @return string The event's autodetect source or empty string if none.
	 */
	protected static function get_autodetect_source( WP_Post $event ) {
		if ( ! self::is_virtual( $event ) ) {
			return '';
		}

		return get_post_meta( $event->ID, Event_Meta::$key_autodetect_source, true );
	}

	/**
	 * Retrieves an event's virtual URL.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $event Event post object.
	 *
	 * @return null|string The event's virtual URL.
	 */
	protected function get_virtual_url( WP_Post $event ) {
		if ( ! self::is_virtual( $event ) ) {
			return null;
		}

		return get_post_meta( $event->ID, Event_Meta::$key_virtual_url, true );
	}

	/**
	 * Get whether to show an event's video embed.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $event Event post object.
	 *
	 * @return boolean Whether the video should be embed.
	 */
	protected function get_virtual_embed_video( WP_Post $event ) {
		if ( ! self::is_virtual( $event ) ) {
			return true;
		}

		return tribe_is_truthy( get_post_meta( $event->ID, Event_Meta::$key_embed_video, true ) );
	}

	/**
	 * Get whether to show an event's linked button.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $event Event post object.
	 *
	 * @return boolean Whether to show linked button.
	 */
	protected function get_virtual_linked_button( WP_Post $event ) {
		if ( ! self::is_new_virtual( $event ) ) {
			return true;
		}

		// If the metadata hasn't been set yet we want to default to true.
		$button = tribe_context()->is_new_post() ? true : get_post_meta( $event->ID, Event_Meta::$key_linked_button, true );

		return tribe_is_truthy( $button );
	}

	/**
	 * Get the text of the linked button.
	 * Defaults to "Watch".
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $event Event post object.
	 *
	 * @return string The text to display in the linked button.
	 */
	protected function get_virtual_linked_button_text( WP_Post $event ) {
		$default_text = Event_Meta::linked_button_default_text();

		if ( ! self::is_virtual( $event ) ) {
			return $default_text;
		}

		$text = get_post_meta( $event->ID, Event_Meta::$key_linked_button_text, true );

		// Button won't display if there's no text. Set a default value.
		if ( empty( $text ) ) {
			return $default_text;
		}

		/**
		 * Filter the linked button label.
		 *
		 * @since 1.0.0
		 *
		 * @param string $text The default linked button text, 'Watch'.
		 * @param WP_Post $event Event post object.
		 */
		return apply_filters(
			'tribe_events_virtual_linked_button_label',
			$text,
			$event
		);
	}

	/**
	 * Get when to show a the video embed.
	 *
	 * @since 1.0.0
	 * @since 1.0.4 Alter to allow filtering of the default via get_default_show_at().
	 *
	 * @param WP_Post $event Event post object.
	 *
	 * @return string The time to start displaying the video embed.
	 */
	protected function get_virtual_show_embed_at( WP_Post $event ) {
		if ( ! self::is_new_virtual( $event ) ) {
			return $this->get_default_show_at();
		}

		$value = get_post_meta( $event->ID, Event_Meta::$key_show_embed_at, true );

		if ( ! empty( $value ) ) {
			return $value;
		}

		return $this->get_default_show_at();
	}

	/**
	 * Get default show when.
	 * Allows filtering via `get_default_virtual_show_embed_when`.
	 *
	 * @since 1.0.4
	 *
	 * @return string The time to start displaying the video embed.
	 */
	protected function get_default_show_at() {
		/**
		 * Allows filtering the default show when value for virtual events.
		 *
		 * @since 1.0.4
		 *
		 * @param string Event_Meta::$value_show_embed_now (immediately) When to show the content.
		 */
		return apply_filters( 'tribe_events_virtual_default_virtual_show_embed_at', Event_Meta::$value_show_embed_now );
	}

	/**
	 * Get who to show a the video embed to.
	 *
	 * @since 1.0.4
	 *
	 * @param WP_Post $event Event post object.
	 *
	 * @return array<string|string> The array of user types (logged in or all) to display the video embed to.
	 */
	protected function get_virtual_show_embed_to( WP_Post $event ) {
		if ( ! self::is_new_virtual( $event ) ) {
			return $this->get_default_show_to();
		}

		$value = get_post_meta( $event->ID, Event_Meta::$key_show_embed_to, true );

		if ( ! empty( $value ) ) {
			return (array) $value;
		}

		// If a meta field with the given key exists, return an empty array.
		$meta_exists = metadata_exists('post', $event->ID, Event_Meta::$key_show_embed_to);
		if ( $meta_exists ) {
			return [];
		}

		// If the metadata hasn't been set yet we grab the default.
		return $this->get_default_show_to();
	}

	/**
	 * Get default show to.
	 * Allows filtering via `get_default_virtual_show_embed_to`.
	 *
	 * @since 1.0.4
	 *
	 * @return array<string|string> The array of user types (logged in or all) to display the video embed to.
	 */
	protected function get_default_show_to() {
		/**
		 * Allows filtering the default show to value for virtual events.
		 *
		 * @since 1.0.4
		 *
		 * @param true Based on whether this is a new post.
		 */
		return (array) apply_filters( 'tribe_events_virtual_default_virtual_show_embed_to', Event_Meta::$value_show_embed_to_all );
	}

	/**
	 * Get whether to virtual indicator on single view.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $event Event post object.
	 *
	 * @return boolean Whether to show indicators on Single View.
	 */
	protected function get_virtual_show_on_event( WP_Post $event ) {
		if ( ! self::is_virtual( $event ) ) {
			return true;
		}

		return tribe_is_truthy( get_post_meta( $event->ID, Event_Meta::$key_show_on_event, true ) );
	}

	/**
	 * Get whether to show the virtual indicator on v2 views.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $event Event post object.
	 *
	 * @return boolean Whether to show indicators on V2 Views.
	 */
	protected function get_virtual_show_on_views( WP_Post $event ) {
		if ( ! self::is_virtual( $event ) ) {
			return true;
		}

		return tribe_is_truthy( get_post_meta( $event->ID, Event_Meta::$key_show_on_views, true ) );
	}

	/**
	 * Add the virtual event meta to rest meta.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $event Event post object.
	 *
	 * @return array virtual meta.
	 */
	public function get_rest_properties( $event ) {
		$event = tribe_get_event( $event );

		if ( ! $event ) {
			return [];
		}

		$meta_virtual = [
			'is_virtual'           => $event->virtual,
			'virtual_url'          => $event->virtual_url,
			'virtual_video_source' => $event->virtual_video_source,
		];

		return $meta_virtual;
	}

	/**
	 * Retrieves if the event is set to show the embed immediately.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $event Event post object.
	 *
	 * @return boolean
	 */
	protected function get_virtual_is_immediate( $event ) {
		if ( ! self::is_new_virtual( $event ) ) {
			return false;
		}

		// If the metadata hasn't been set yet we want to default to true.
		$immediate = tribe_context()->is_new_post() || Event_Meta::$value_show_embed_now === $event->virtual_show_embed_at;

		return tribe_is_truthy( $immediate );
	}

	/**
	 * Retrieves the filtered lead-up for the event embed/link button in minutes.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $event Event post object.
	 *
	 * @return int The filtered lead-up. Returns 0 for events flagged to show "immediately".
	 */
	protected function get_virtual_show_lead_up( WP_Post $event ) {
		$lead_up = 15;

		/**
		 * Filters the time to show embeds and links before the event start time.
		 *
		 * @since 1.0.0
		 *
		 * @param int      $lead_up The time in seconds to show embeds and links before the event start time.
		 * @param \WP_Post $event The event post object.
		 */
		return apply_filters( 'tribe_events_virtual_show_lead_up', $lead_up, $event );
	}

	/**
	 * Retrieves whether the event has a URL to show (link/embed).
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $event Event post object.
	 *
	 * @return boolean Whether the event has a URL to show.
	 */
	protected function get_is_linkable( $event ) {
		$linkable = $event->virtual && ! empty( $event->virtual_url );

		/**
		 * Filters whether an event is considered linkable (is virtual and has a virtual URL) or not.
		 *
		 * @since 1.0.0
		 *
		 * @param boolean  $linkable Whether the event is considered linkable or not.
		 * @param \WP_Post $event    The event post object.
		 */
		return tribe_is_truthy( apply_filters( 'tribe_events_virtual_is_linkable', $linkable, $event ) );
	}

	/**
	 * Does the logic on whether a button, link, and/or embed should show.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $event Event post object.
	 *
	 * @return boolean Whether the event should show a URL not.
	 */
	protected function get_should_show( $event ) {
		// If not a virtual event, bail.
		if ( ! $event->virtual ) {
			return false;
		}

		$should_show = tribe_is_truthy( $event->virtual_is_immediate );

		if ( ! $should_show ) {
			$should_show = $this->handle_should_show( $event );
		}

		return $should_show;
	}

	/**
	 * Calculate if the embed should show.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $event Event post object.
	 *
	 * @return boolean Whether the event embed shows.
	 */
	public function handle_should_show( $event ) {

		// Set start time.
		$start_time = $event->dates->start;

		// Set interval in seconds and add to now.
		$interval      = $this->get_virtual_show_lead_up( $event ) * MINUTE_IN_SECONDS;
		$interval_spec = "PT{$interval}S";
		$lead_interval = Dates::interval( $interval_spec );
		$timezone      = Timezones::build_timezone_object( $event->timezone );
		$lead_time     = Dates::build_date_object( 'now', $timezone )->add( $lead_interval );

		if ( $lead_time >= $start_time ) {
			return true;
		}

		return false;
	}

	/**
	 * Retrieves whether the event should show a link now.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $event Event post object.
	 *
	 * @return boolean Whether the event should show a link now or not.
	 */
	protected function get_should_show_link( $event ) {
		/**
		 * Filters whether an event is ready to show the link or not.
		 *
		 * @since 1.0.0
		 *
		 * @param boolean  $should_show Whether the event is ready to show the link or not.
		 * @param \WP_Post $event      The event post object.
		 */
		return tribe_is_truthy(
			apply_filters(
				'tribe_events_virtual_should_show_link',
				$this->get_should_show( $event ),
				$event
			)
		);
	}

	/**
	 * Retrieves whether the event should show an embed now.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $event Event post object.
	 *
	 * @return boolean Whether the event should show an embed now or not.
	 */
	protected function get_should_show_embed( $event ) {
		/**
		 * Filters whether an event is ready to show the embed or not.
		 *
		 * @since 1.0.0
		 *
		 * @param boolean  $should_show Whether the event is ready to show the embed or not.
		 * @param \WP_Post $event      The event post object.
		 */
		return tribe_is_truthy(
			apply_filters(
				'tribe_events_virtual_should_show_embed',
				$this->get_should_show( $event ),
				$event
			)
		);
	}

	/**
	 * Testing if the event is virtual - taking into account new posts aren't automatically virtual events.
	 * Useful for when we need to set default values.
	 *
	 * @since 1.0.0
	 * @since 1.0.4 Changed to static method.
	 *
	 * @param WP_Post $event Event post object.
	 * @return bool Whether an event is a new virtual event or not.
	 */
	public static function is_new_virtual( $event ) {
		return self::is_virtual( $event ) || tribe_context()->is_new_post();
	}

	/**
	 * Adds dynamic, time-related, properties to the event object.
	 *
	 * This method deals with properties we set, for convenience, on the event object that should not
	 * be cached as they are time-dependent; i.e. the time the properties are computed  at matters and
	 * caching their values would be incorrect.
	 *
	 * @since 1.4.1
	 *
	 * @param WP_Post $event The event post object, as read from the cache, if any.
	 *
	 * @return WP_Post The decorated event post object; its dynamic and time-dependent properties correctly set up.
	 */
	public function add_dynamic_properties( WP_Post $event ) {
		// Note: order matters below, as these function depend on the ones added in the `add_properties` method!
		$event->virtual_is_immediate      = $this->get_virtual_is_immediate( $event );
		$event->virtual_is_linkable       = $this->get_is_linkable( $event );
		$event->virtual_should_show_embed = $this->get_should_show_embed( $event );
		$event->virtual_should_show_link  = $this->get_should_show_link( $event );

		return $event;
	}
}
