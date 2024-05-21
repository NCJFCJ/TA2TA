<?php
/**
 * Flexible Tickets compatibility class.
 *
 * @since 1.15.6
 *
 * @package Tribe\Events\Virtual
 */

namespace Tribe\Events\Virtual\Compatibility\Event_Tickets;

use TEC\Common\Contracts\Provider\Controller;
use TEC\Tickets\Flexible_Tickets\Series_Passes\Series_Passes;
use WP_Post;

/**
 * Class Flexible_Tickets handler.
 *
 * @since 1.15.6
 *
 * @package Tribe\Events\Virtual\Compatibility\Event_Tickets
 */
class Flexible_Tickets extends Controller {

	/**
	 * @inheritDoc
	 *
	 * @since 1.15.6
	 *
	 * @return void
	 */
	protected function do_register(): void {
		add_filter( 'tec_events_virtual_user_has_ticket', [ $this, 'filter_events_virtual_show_to_content' ], 10, 3 );
	}

	/**
	 * @inheritDoc
	 *
	 * @since 1.15.6
	 *
	 * @return void
	 */
	public function unregister(): void {
		remove_filter( 'tec_events_virtual_user_has_ticket', [ $this, 'filter_events_virtual_show_to_content' ], 10, 3 );
	}

	/**
	 * Filters the content of the virtual event show page.
	 *
	 * @since 1.15.6
	 *
	 * @param boolean $has_ticket Whether the current user has a ticket for the event.
	 * @param WP_Post $event      The post object or ID of the viewed event.
	 * @param int     $user_id    ID of the current user.
	 *
	 * @return bool Whether the current user can view the content.
	 */
	public function filter_events_virtual_show_to_content( bool $has_ticket, WP_Post $event, int $user_id ): bool {
		/**
		 * Filter whether to render the show to content for series passes.
		 *
		 * @since 1.15.6
		 *
		 * @param boolean $allowed Whether to render the show to content for series passes.
		 */
		$allowed = apply_filters( 'tec_events_virtual_render_show_to_content_for_series_passes', true );

		// If series passes are allowed or ticket access is already false, we don't need to do anything.
		if ( $allowed || ! $has_ticket ) {
			return $has_ticket;
		}

		if ( ! $this->user_has_non_series_pass_tickets( $user_id, $event->ID ) ) {
			return false;
		}

		return $has_ticket;
	}

	/**
	 * Checks if the user has tickets other than series passes for the event.
	 *
	 * @since 1.15.6
	 *
	 * @param int $user_id  ID of the current user.
	 * @param int $event_id ID of the event.
	 *
	 * @return bool Whether the user has tickets other than series passes for the event.
	 */
	public function user_has_non_series_pass_tickets( int $user_id, int $event_id ): bool {
		$args = [
			'provider__not_in'    => 'rsvp',
			'user'                => $user_id,
			'event'               => $event_id,
			'ticket_type__not_in' => Series_Passes::TICKET_TYPE,
		];

		$ticketed_attendees = tribe_attendees()->by_args( $args )->count();

		return (bool) $ticketed_attendees;
	}
}
