<?php
/**
 * Class RSVP.
 *
 * @since 1.15.0
 *
 * @package TEC\Events_Virtual\Integrations\Plugins\Event_Tickets\Emails
 */

namespace TEC\Events_Virtual\Integrations\Plugins\Event_Tickets\Emails\Email;

use TEC\Tickets\Emails\Email\RSVP as RSVP_Email;
use TEC\Events_Virtual\Integrations\Plugins\Event_Tickets\Emails\Template;
use Tribe\Events\Virtual\Event_Meta;

/**
 * Class RSVP.
 *
 * @since 1.15.0
 *
 * @package TEC\Events_Virtual\Integrations\Plugins\Event_Tickets\Emails
 */
class RSVP {

	/**
	 * Maybe include virtual event link.
	 *
	 * @since 1.15.0
	 * @since 1.15.2    Always show the email links in previews.
	 * @since 1.15.4    Added check for `virtual_meeting_url` property.
	 *
	 * @param \Tribe__Template $parent_template Event Tickets template object.
	 *
	 * @return void
	 */
	public function include_virtual_event_link( $parent_template ) {
		$args = $parent_template->get_local_values();

		if ( ! $args['email'] instanceof RSVP_Email ) {
			return;
		}

		if ( tribe( RSVP_Email::class )->is_using_ticket_email_settings() ) {
			tribe( Ticket::class )->render_virtual_event_link( $args );
			return;
		}

		// Check if event exists.
		if ( ! isset( $args['event'] ) ) {
			return;
		}

		// Check if user wants link in email.
		if ( empty( $args['preview'] ) && empty( $args['event']->virtual_rsvp_email_link ) ) {
			return;
		}

		if ( ! empty( $args['preview'] ) ) {
			$args['virtual_url']       = home_url();
			$args['virtual_link_text'] = Event_Meta::linked_button_default_text();
		}elseif( ! empty( $args['event']->virtual_url ) || ! empty( $args['event']->virtual_meeting_url ) ) {
			/**
			 * Allows filtering the url used in ticket and rsvp emails.
			 *
			 * @since 1.15.0
			 *
			 * @param string  $virtual_url The virtual url for the ticket and rsvp emails.
			 * @param WP_Post $event       The event post object with properties added by the `tribe_get_event` function.
			 */
			$virtual_url = empty( $args['event']->virtual_meeting_url ) ? $args['event']->virtual_url : $args['event']->virtual_meeting_url;
			$args['virtual_url']       = apply_filters( 'tec_events_virtual_ticket_email_url', $virtual_url, $args['event'] );
			$args['virtual_link_text'] = get_post_meta( $args['event']->ID, Event_Meta::$key_linked_button_text, true );
		}


		tribe( Template::class )->template( 'template-parts/body/virtual-event/link', $args, true );
	}

	/**
	 * Maybe include virtual event link styles.
	 *
	 * @since 1.15.0
	 *
	 * @param \Tribe__Template $parent_template Event Tickets template object.
	 *
	 * @return void
	 */
	public function include_virtual_event_link_styles( $parent_template ): void {
		$args = $parent_template->get_local_values();

		if ( ! $args['email'] instanceof RSVP_Email ) {
			return;
		}

		tribe( Template::class )->template( 'template-parts/header/head/ve-styles', $parent_template->get_local_values(), true );
	}
}
