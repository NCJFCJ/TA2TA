<?php
/**
 * Handles the compatibility with the Event Tickets plugin.
 *
 * @since   1.0.4
 *
 * @package Tribe\Events\Virtual\Compatibility\Event_Tickets
 */

namespace Tribe\Events\Virtual\Compatibility\Event_Tickets;

use Tribe\Events\Virtual\Compatibility\Event_Tickets\Template_Modifications;
use Tribe\Events\Virtual\Compatibility\Event_Tickets\Email as Email;
use Tribe\Events\Virtual\Compatibility\Event_Tickets\Event_Meta as Ticket_Meta;
use Tribe__Tickets__Tickets;
use TEC\Common\Contracts\Service_Provider as Provider_Contract;
use TEC\Tickets\Flexible_Tickets\Series_Passes\Series_Passes;

/**
 * Class Service_Provider
 *
 * @since   1.0.4
 *
 * @package Tribe\Events\Virtual\Compatibility\Event_Tickets
 */
class Service_Provider extends Provider_Contract {
	/**
	 * Register the bindings and filters required to ensure compatibility w/Event Tickets.
	 *
	 * @since 1.0.4
	 */
	public function register() {
		$this->container->singleton( self::class, $this );
		$this->container->singleton( 'events-virtual.compatibility.tribe-event-tickets', $this );

		if ( ! class_exists( 'Tribe__Tickets__Main' ) ) {
			// If for whatever reason, the plugin is not active but we still got here - bail.
			return;
		}
		
		// Handle Flexible Tickets compatibility.
		$this->container->register_on_action( 'tec_container_registered_provider_' . Series_Passes::class, Flexible_Tickets::class );

		add_action( 'tribe_events_virtual_add_event_properties', [ $this, 'add_event_properties' ] );

		add_action(
			'tribe_tickets_ticket_email_after_details',
			[ $this, 'action_ticket_email_after_details' ],
			10,
			2
		);

		add_action(
			'tribe_template_before_include:events-virtual/admin-views/virtual-metabox/container/label',
			[ $this, 'share_rsvp_controls' ],
			10,
			3
		);

		add_filter(
			'tribe_template_pre_html:events-virtual/admin-views/virtual-metabox/container/show-to',
			[ $this, 'show_to_controls' ],
			10,
			4
		);

		add_action(
			'tribe_template_entry_point:events-virtual/admin-views/virtual-metabox/container/compatibility/event-tickets/share:before_share_list_end',
			[ $this, 'share_ticket_controls' ],
			10,
			3
		);

		add_action(
			'tribe_template_entry_point:events-virtual/admin-views/virtual-metabox/container/compatibility/event-tickets/show-to:before_show_to_list_end',
			[ $this, 'show_to_ticket_controls' ],
			10,
			3
		);

		add_action( 'tribe_events_virtual_update_post_meta', [ $this, 'action_update_post_meta' ], 10, 2 );

		add_filter( 'tribe_events_virtual_event_meta_keys', [ $this, 'filter_virtual_event_meta_keys' ] );

		add_filter( 'tribe_events_virtual_show_virtual_content', [ $this, 'filter_show_virtual_content' ], 10, 2 );

		add_filter( 'tec_events_virtual_export_should_show', [ $this, 'filter_export_should_show' ], 10, 2 );
	}

	/**
	 * Filters the object returned by the `tribe_get_event` function to add to it properties related to Zoom meetings.
	 *
	 * @since 1.0.4
	 *
	 * @param \WP_Post $event The event post object to be modified.
	 *
	 * @return \WP_Post The original event object decorated with properties related to virtual events.
	 */
	public function add_event_properties( \WP_Post $event ) {
		// Skip non-events.
		if ( ! tribe_is_event( $event ) ) {
			return $event;
		}

		return $this->container->make( Ticket_Meta::class )->add_event_properties( $event );
	}

	/**
	 * Add the share controls to the metabox.
	 *
	 * @since 1.0.4
	 *
	 * @param string           $unused_file Complete path to include the PHP File.
	 * @param array            $unused_name Template name.
	 * @param \Tribe__Template $template    Current instance of the Tribe__Template.
	 */
	public function share_rsvp_controls( $unused_file, $unused_name, \Tribe__Template $template ) {
		$this->container->make( Template_Modifications::class )
				->render_share_rsvp_controls( $template->get( 'post' ) );
	}

	/**
	 * Add the share controls to the metabox.
	 *
	 * @since 1.0.4
	 *
	 * @param string           $unused_file Complete path to include the PHP File.
	 * @param array            $unused_name Template name.
	 * @param \Tribe__Template $template    Current instance of the Tribe__Template.
	 */
	public function share_ticket_controls( $unused_file, $unused_name, \Tribe__Template $template ) {
		$this->container->make( Template_Modifications::class )
				->render_share_ticket_controls( $template->get( 'post' ) );
	}

	/**
	 * Add the Event Tickets "show to" controls to the metabox.
	 *
	 * @since 1.0.4
	 *
	 * @param string           $html        The initial HTML.
	 * @param string           $unused_file Complete path to include the PHP File.
	 * @param array            $unused_name Template name.
	 * @param \Tribe__Template $template    Current instance of the Tribe__Template.
	 * @return string|boolean               The original template HTML or the new.
	 */
	public function show_to_controls( $html, $unused_file, $unused_name, \Tribe__Template $template ) {
		$event = tribe_get_event( $template->get( 'post' ) );

		if ( ! $event instanceof \WP_Post ) {
			return $html;
		}

		return $this->container->make( Template_Modifications::class )->render_show_to_controls( $event, $html, false );
	}

	/**
	 * Add RSVP and Ticket controls.
	 *
	 * @since 1.0.4
	 *
	 * @param string           $unused_file Complete path to include the PHP File.
	 * @param array            $unused_name Template name.
	 * @param \Tribe__Template $template    Current instance of the Tribe__Template.
	 */
	public function show_to_ticket_controls( $unused_file, $unused_name, \Tribe__Template $template ) {
		$event = $template->get( 'post' );
		$rsvp_disabled = tribe_is_truthy( ! tribe( 'tickets.rsvp' )->login_required() );

		$this->container->make( Template_Modifications::class )->render_show_to_rsvp_controls( $event, $rsvp_disabled );

		$provider = Tribe__Tickets__Tickets::get_event_ticket_provider_object( $event->ID );

		// Returns false if no provider.
		if ( empty( $provider ) ) {
			return;
		}

		// Defaults to RSVP, but we deal with that above.
		if ( 'Tribe__Tickets__RSVP' !== get_class( $provider ) ) {
			$ticket_disabled = tribe_is_truthy( ! $provider->login_required() );
			$this->container->make( Template_Modifications::class )->render_show_to_ticket_controls( $event, $ticket_disabled );
		}
	}

	/**
	 * Include the video embed for event single.
	 *
	 * @since 1.0.0
	 * @since 1.0.4 Moved to Compatibility.
	 *
	 * @param array   $ticket The ticket data.
	 * @param \WP_Post $event  The post object.
	 */
	public function action_ticket_email_after_details( $ticket, \WP_Post $event ) {
		$this->container->make( Email::class )
			->insert_virtual_info_into_ticket_email( $ticket, $event );
	}

	/**
	 * Add Event Ticket related meta keys.
	 *
	 * @since 1.0.4
	 *
	 * @param array<string> $keys Existing keys.
	 * @return array<string>      Modified keys.
	 */
	public function filter_virtual_event_meta_keys( $keys ) {
		$keys[] = '_tribe_events_virtual_rsvp_email_link';
		$keys[] = '_tribe_events_virtual_ticket_email_link';

		return $keys;
	}

	/**
	 * Update Virtual Event post meta associated with Event Tickets.
	 *
	 * @since 1.0.4
	 *
	 * @param int   $event_id ID of the event post we're saving.
	 * @param array $data     The meta data we're trying to save.
	 */
	public function action_update_post_meta( $event_id, $data ) {
		$this->container->make( Ticket_Meta::class )->update_post_meta( $event_id, $data );
	}

	/**
	 * Filter wether the virtual event info should show or not on the front end.
	 *
	 * @since 1.0.4
	 * @since 1.1.2 Bail earlier if the user isn't logged in.
	 * @since 1.9.0 - Only use tribe_get_event if an integer.
	 *
	 * @param boolean     $show  If the virtual content should show or not.
	 * @param WP_Post|int $event The post object or ID of the viewed event.
	 * @return boolean
	 */
	public function filter_show_virtual_content( $show, $event ) {
		if ( is_integer( $event ) ) {
			$event = tribe_get_event( $event );
		}

		if ( ! $event instanceof \WP_Post ) {
			return $show;
		}

		$show_to = (array) $event->virtual_show_embed_to;

		if ( empty( $show_to ) ) {
			return $show;
		}

		if ( in_array( 'all', $show_to, true ) ) {
			return true;
		}

		// Everything after this depends on the user being logged in.
		if ( ! is_user_logged_in() ) {
			return false;
		}

		// Do we need to be logged in?
		if ( in_array( 'logged-in', $show_to ) ) {
			return true;
		}

		$user_id = get_current_user_id();

		// Check RSVP first.
		if ( in_array( 'rsvp', $show_to ) ) {
			$rsvp = $this->container->make( Ticket_Meta::class )->user_is_rsvp_attendee( $event, $user_id );

			if ( tribe_is_truthy( $rsvp ) ) {
				return true;
			}
		}

		// Then tickets.
		if ( in_array( 'ticket', $show_to ) ) {
			$ticket = $this->container->make( Ticket_Meta::class )->user_is_ticket_attendee( $event, $user_id );

			if ( tribe_is_truthy( $ticket ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Filter whether the current user should see the video source in the export.
	 *
	 * @since 1.8.0
	 *
	 * @param boolean  $should_show Whether to modify the export fields for the current user, default to false.
	 * @param \WP_Post $event       The WP_Post of this event.
	 *
	 * @return boolean Whether to modify the export fields for the current user.
	 */
	public function filter_export_should_show( $should_show, $event ) {
		return $this->filter_show_virtual_content( $should_show, $event );
	}

	/**
	 * Get Tickets settings URL.
	 *
	 * @since 1.10.0
	 *
	 * @param array $args array of args to add to the url.
	 * @return string The settings URL.
	 */
	public function get_settings_url( array $args = [] ) {
		if ( ! tribe()->isBound( 'tickets.main' ) ) {
			return tribe( 'settings' )->get_url( $args );
		}

		return tribe( 'tickets.main' )->settings()->get_url( $args );

	}
}
