<?php
/**
 * Handles hooking all the actions and filters used by Tickets Emails.
 *
 * To remove a filter:
 * remove_filter( 'some_filter', [ tribe( TEC\Events_Virtual\Integrations\Plugins\Event_Tickets\Emails\Hooks::class ), 'some_filtering_method' ] );
 *
 * To remove an action:
 * remove_action( 'some_action', [ tribe( TEC\Events_Virtual\Integrations\Plugins\Event_Tickets\Emails\Hooks::class ), 'some_method' ] );
 *
 * @since   1.15.0
 *
 * @package TEC\Events_Virtual\Integrations\Plugins\Event_Tickets\Emails
 */

namespace TEC\Events_Virtual\Integrations\Plugins\Event_Tickets\Emails;

use TEC\Events_Virtual\Integrations\Plugins\Event_Tickets\Emails\Email\RSVP;
use TEC\Events_Virtual\Integrations\Plugins\Event_Tickets\Emails\Email\Ticket;
use \Tribe__Template as Common_Template;
use TEC\Common\Contracts\Service_Provider;

/**
 * Class Hooks.
 *
 * @since   1.15.0
 *
 * @package TEC\Events_Virtual\Integrations\Plugins\Event_Tickets\Emails
 */
class Hooks extends Service_Provider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 1.15.0
	 */
	public function register(): void {
		$this->add_actions();
	}

	/**
	 * Adds the actions required by each Tickets Emails component.
	 *
	 * @since 1.15.0
	 */
	protected function add_actions(): void {
		add_action( 'tribe_template_before_include:tickets/emails/template-parts/header/head/styles', [ $this, 'include_virtual_event_styles' ], 10, 3 );
		add_action( 'tribe_template_after_include:tickets/emails/template-parts/body/tickets', [ $this, 'include_virtual_event_link' ], 9, 3 );
	}

	/**
	 * Include the Virtual Event link in the ticket and RSVP emails.
	 *
	 * @since 1.15.0
	 *
	 * @param string          $file     Template file.
	 * @param string          $name     Template name.
	 * @param Common_Template $template Event Tickets template object.
	 *
	 * @return void
	 */
	public function include_virtual_event_link( $file, $name, $template ) {
		if ( ! $template instanceof Common_Template ) {
			return;
		}

		$this->container->make( RSVP::class )->include_virtual_event_link( $template );
		$this->container->make( Ticket::class )->include_virtual_event_link( $template );
	}

	/**
	 * Include the Virtual Event link styles in the ticket and RSVP emails.
	 *
	 * @since 1.15.0
	 *
	 * @param string          $file     Template file.
	 * @param string          $name     Template name.
	 * @param Common_Template $template Event Tickets template object.
	 *
	 * @return void
	 */
	public function include_virtual_event_styles( $file, $name, $template ) {
		if ( ! $template instanceof Common_Template ) {
			return;
		}

		$this->container->make( RSVP::class )->include_virtual_event_link_styles( $template );
		$this->container->make( Ticket::class )->include_virtual_event_link_styles( $template );
	}

}
