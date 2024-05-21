<?php
/**
 * Class that handles template modifications for Event Tickets emails.
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual
 */

namespace Tribe\Events\Virtual\Compatibility\Event_Tickets;

use \Tribe\Events\Virtual\Template;

/**
 * Class Email.
 *
 * @since   1.0.0
 * @since 1.0.4 Moved to Compatibility.
 *
 * @package Tribe\Events\Virtual
 */
class Email {
	/**
	 * An instance of the front-end template rendering handler.
	 *
	 * @since 1.0.0
	 *
	 * @var Template
	 */
	protected $template;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Template $template An instance of the front-end template rendering handler.
	 */
	public function __construct( Template $template ) {
		$this->template = $template;
	}

	/**
	 * Insert virtual details into ticket email template.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string,mixed> $ticket Ticket data.
	 * @param WP_Post             $event  Event post object.
	 */
	public function insert_virtual_info_into_ticket_email( $ticket, $event ) {
		if ( ! class_exists( 'Tribe__Tickets__Main' ) ) {
			// If for whatever reason, the plugin is not active but we still got here - bail.
			return;
		}

		// Bail if we're missing info.
		if ( empty( $ticket ) || empty( $event ) ) {
			return;
		}

		// Make sure we have the event object.
		$event_obj = tribe_get_event( $event );

		if ( ! $event_obj instanceof \WP_Post ) {
			return;
		}

		// Bail if we don't have a provider.
		if ( empty( $ticket['provider'] ) ) {
			return;
		}

		if ( 'Tribe__Tickets__RSVP' === $ticket['provider'] ) {
			if ( empty( $event_obj->virtual_rsvp_email_link ) ) {

				// Or we've toggled off injection for RSVP.
				return;
			}
		} elseif ( empty( $event_obj->virtual_ticket_email_link ) ) {
			// Or if we've toggled off injection for tickets.
			return;
		}

		/**
		 * Allows filtering the url used in ticket and rsvp emails.
		 *
		 * @since 1.7.2
		 *
		 * @param string  $virtual_url The virtual url for the ticket and rsvp emails.
		 * @param WP_Post $event       The event post object with properties added by the `tribe_get_event` function.
		 */
		$virtual_url = apply_filters( 'tribe_events_virtual_ticket_email_url', $event_obj->virtual_url, $event_obj );

		$args = [
			'event'       => $event_obj,
			'virtual_url' => $virtual_url,
		];

		/**
		 * Filter the template used for the email injection.
		 *
		 * @since 1.0.0
		 *
		 * @param string              $template_name The template path, relative to src/views.
		 * @param array<string,mixed> $args	         The template arguments.
		 */
		$template_name = apply_filters(
			'tribe_events_virtual_ticket_email_template',
			'compatibility/event-tickets/email/ticket-email-link',
			$args
		);

		$this->template->template( $template_name, $args, true );
	}
}
