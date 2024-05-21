<?php
/**
 * Handles the post meta related to Event Tickets.
 *
 * @since   1.0.4
 *
 * @package Tribe\Events\Virtual\Compatibility\Event_Tickets
 */

namespace Tribe\Events\Virtual\Compatibility\Event_Tickets;

use Tribe\Events\Virtual\Models\Event as Model;
use Tribe__Tickets__Tickets_View as Tickets_View;
use Tribe__Utils__Array as Arr;
use WP_Post;

/**
 * Class Event_Meta
 *
 * @since   1.0.4
 *
 * @package Tribe\Events\Virtual\Compatibility\Event_Tickets
 */
class Event_Meta {
	/**
	 * Meta key for showing virtual info in RSVP emails.
	 *
	 * @since 1.0.0
	 * @since 1.0.4 Moved to Compatibility.
	 *
	 * @var string
	 */
	public static $key_rsvp_email_link = '_tribe_events_virtual_rsvp_email_link';

	/**
	 * Meta key for showing virtual info in Event Ticket emails.
	 *
	 * @since 1.0.0
	 * @since 1.0.4 Moved to Compatibility.
	 *
	 * @var string
	 */
	public static $key_ticket_email_link = '_tribe_events_virtual_ticket_email_link';

	/**
	 * Meta value to show the embed to all users.
	 *
	 * @since 1.0.4
	 *
	 * @var string
	 */
	public static $value_show_embed_to_rsvp = 'rsvp';

	/**
	 * Meta value to show the embed to all users.
	 *
	 * @since 1.0.4
	 *
	 * @var string
	 */
	public static $value_show_embed_to_ticket = 'ticket';

	/**
	 * Adds Event Ticket related properties to an event post object.
	 *
	 * @since 1.0.4
	 *
	 * @param \WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
	 */
	public function add_event_properties( \WP_Post $event ) {
		$event->virtual_rsvp_email_link   = $this->get_virtual_rsvp_email_link( $event );
		$event->virtual_ticket_email_link = $this->get_virtual_ticket_email_link( $event );
	}

	/**
	 * Update the post meta for our link injection.
	 *
	 * @since 1.0.4
	 *
	 * @param int                 $post_id The post ID of the post the date is being saved for.
	 * @param array<string,mixed> $data    The data to save, directly from the metabox.
	 */
	public function update_post_meta( $post_id, $data ) {
		update_post_meta( $post_id, self::$key_rsvp_email_link, Arr::get( $data, 'rsvp-email-link', false ) );
		update_post_meta( $post_id, self::$key_ticket_email_link, Arr::get( $data, 'ticket-email-link', false ) );
	}

	/**
	 * Should we show the virtual link in RSVP emails?
	 *
	 * @since 1.0.0
	 * @since 1.0.4 Moved to Compatibility.
	 *
	 * @param WP_Post $event Event post object.
	 *
	 * @return bool Whether the virtual event link should be included in RSVP emails or not.
	 */
	protected function get_virtual_rsvp_email_link( $event ) {
		if ( ! Model::is_new_virtual( $event ) ) {
			return true;
		}

		/**
		 * Allows filtering the default checked/unchecked for RSVP email injection on new virtual events.
		 *
		 * @since 1.0.0
		 * @since 1.0.4 Moved to Compatibility.
		 *
		 * @param true Based on whether this is a new post
		 */
		$default = apply_filters( 'tribe_events_virtual_rsvp_email_link_default_value', tribe_context()->is_new_post() );

		// If the metadata hasn't been set yet we want to default to true.
		$link = $default || get_post_meta( $event->ID, self::$key_rsvp_email_link, true );

		/**
		 * Allows filtering of the response by third-party sources.
		 *
		 * @since 1.0.0
		 * @since 1.0.4 Moved to Compatibility.
		 *
		 * @param boolean $link Boolean should we add the link
		 * @param string  $type Ticket or RSVP.
		 * @param WP_Post $event Event post object.
		 */
		$link = apply_filters( 'tribe_events_virtual_email_link', $link, 'rsvp', $event );

		return tribe_is_truthy( $link );
	}

	/**
	 * Should we show the virtual link in ticket emails?
	 *
	 * @since 1.0.0
	 * @since 1.0.4 Moved to Compatibility.
	 *
	 * @param WP_Post $event Event post object.
	 *
	 * @return bool Whether the virtual event link should be included in ticket emails or not.
	 */
	protected function get_virtual_ticket_email_link( $event ) {
		if ( ! Model::is_new_virtual( $event ) ) {
			return true;
		}

		/**
		 * Allows filtering the default checked/unchecked for ticket email injection on new virtual events.
		 *
		 * @since 1.0.0
		 * @since 1.0.4 Moved to Compatibility.
		 *
		 * @param bool $default Based on whether this is a new post.
		 */
		$default = apply_filters( 'tribe_events_virtual_ticket_email_link_default_value', tribe_context()->is_new_post() );

		// If the metadata hasn't been set yet we want to default to true.
		$link = $default || get_post_meta( $event->ID, self::$key_ticket_email_link, true );

		/**
		 * Allows filtering of the response by third-party sources.
		 *
		 * @since 1.0.0
		 * @since 1.0.4 Moved to Compatibility.
		 *
		 * @param boolean $link Boolean should we add the link
		 * @param string  $type Ticket or RSVP.
		 * @param WP_Post $event Event post object.
		 */
		$link = apply_filters( 'tribe_events_virtual_email_link', $link, 'ticket', $event );

		return tribe_is_truthy( $link );
	}

	/**
	 * Determine if the current user has a ticket for the event.
	 *
	 * @since 1.0.4
	 * @since 1.9.0 - Only use tribe_get_event if an integer.
	 *
	 * @param WP_Post|int $event The post object or ID of the viewed event.
	 * @param int         $user_id ID of the current user.
	 *
	 * @return boolean
	 */
	public function user_is_ticket_attendee( $event, $user_id = 0 ) {
		if ( is_integer( $event ) ) {
			$event = tribe_get_event( $event );
		}

		if ( ! $event instanceof \WP_Post ) {
			return false;
		}

		$user_id = empty( $user_id ) ? get_current_user_id() : $user_id;

		// Check if we have a cached value.
		$cache_key = 'tec_events_virtual_user_has_ticket-' . $event->ID . '-' . $user_id;
		$cached    = tribe_cache()[ $cache_key ] ?? null;

		if ( is_bool( $cached ) ) {
			return $cached;
		}

		/** @var Tickets_View $tickets_view */
		$tickets_view = Tickets_View::instance();

		$has_ticket = $tickets_view->has_ticket_attendees( $event->ID, $user_id );

		/**
		 * Filter whether the user has a ticket for the event.
		 *
		 * @since 1.15.6
		 *
		 * @param boolean $has_ticket Whether the current user has a ticket for the event.
		 * @param WP_Post $event      The post object or ID of the viewed event.
		 * @param int     $user_id    ID of the current user.
		 */
		$has_ticket = apply_filters( 'tec_events_virtual_user_has_ticket', $has_ticket, $event, $user_id );

		tribe_cache()[ $cache_key ] = $has_ticket;

		return $has_ticket;
	}

	/**
	 * Determine if the current user has an RSVP for the event.
	 *
	 * @since 1.0.4
	 * @since 1.1.2 Simplify attendee check.
	 * @since 1.9.0 - Only use tribe_get_event if an integer.
	 *
	 * @param WP_Post|int $event The post object or ID of the viewed event.
	 * @param int         $user_id ID of the current user.
	 * @return boolean
	 */
	public function user_is_rsvp_attendee( $event, $user_id = 0 ) {
		if ( is_integer( $event ) ) {
			$event = tribe_get_event( $event );
		}

		if ( ! $event instanceof \WP_Post ) {
			return false;
		}

		$user_id = empty( $user_id ) ? get_current_user_id() : $user_id;

		/** @var Tribe__Tickets__Tickets_View $tickets_view */
		$tickets_view = Tickets_View::instance();

		return $tickets_view->has_rsvp_attendees( $event->ID, $user_id );
	}
}
