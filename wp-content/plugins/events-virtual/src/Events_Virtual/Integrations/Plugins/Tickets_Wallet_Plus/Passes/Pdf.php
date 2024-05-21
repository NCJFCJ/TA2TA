<?php

namespace TEC\Events_Virtual\Integrations\Plugins\Tickets_Wallet_Plus\Passes;

use Tribe__Template;
use Tribe\Events\Virtual\Plugin as Virtual_Events;
use WP_Post;

/**
 * Class Pdf
 *
 * @since 1.15.5
 *
 * @package TEC\Events_Virtual\Integrations\Plugins\Tickets_Wallet_Plus\Passes
 */
class Pdf {

	/**
	 * Template instance.
	 *
	 * @since 1.15.5
	 *
	 * @var \Tribe__Template
	 */
	private $template;

	/**
	 * Get the template.
	 *
	 * @since 1.15.5
	 *
	 * @return \Tribe__Template
	 */
	public function get_template(): Tribe__Template {
		if ( empty( $this->template ) ) {
			$template = new Tribe__Template();
			$template->set_template_origin( tribe( Virtual_Events::class ) );
			$template->set_template_folder( 'src/views/integrations/event-tickets-wallet-plus/pdf' );
			$template->set_template_folder_lookup( true );
			$template->set_template_context_extract( true );
			$this->template = $template;
		}
		return $this->template;
	}

	/**
	 * Add styles.
	 *
	 * @since 1.15.5
	 *
	 * @param string           $file     Path to the file.
	 * @param string           $name     Name of the file.
	 * @param \Tribe__Template $template Template instance.
	 *
	 * @return void
	 */
	public function add_styles( $file, $name, $template ): void {
		if ( ! $template instanceof \Tribe__Template ) {
			return;
		}

		$this->get_template()->template( 'pass/tec-events-virtual-styles', $template->get_local_values(), true );
	}

	/**
	 * Add link.
	 *
	 * @since 1.15.5
	 *
	 * @param string           $file     Path to the file.
	 * @param string           $name     Name of the file.
	 * @param \Tribe__Template $template Template instance.
	 *
	 * @return void
	 */
	public function add_link( $file, $name, $template ): void {
		if ( ! $template instanceof \Tribe__Template ) {
			return;
		}

		$args = $template->get_local_values();

		// Check if event exists.
		if ( ! isset( $args['event'] ) ) {
			return;
		}

		// Check if user wants link in email.
		if ( empty( $args['event']->virtual_ticket_email_link ) ) {
			return;
		}

		$virtual_url = empty( $args['event']->virtual_meeting_url ) ? $args['event']->virtual_url : $args['event']->virtual_meeting_url;

		// Convert event to WP_Post before filtering.
		$event = new WP_Post( $args['event'] );

		/**
		 * Allows filtering the url used in ticket and rsvp emails.
		 *
		 * @since 1.15.0
		 *
		 * @param string  $virtual_url The virtual url for the ticket and rsvp emails.
		 * @param WP_Post $event       The event post object with properties added by the `tribe_get_event` function.
		 */
		$virtual_url = apply_filters( 'tec_events_virtual_ticket_email_url', $virtual_url, $event );

		$args = [
			'virtual_url'            => $virtual_url,
			'virtual_event_icon_src' => tribe( Virtual_Events::class )->plugin_path . 'src/resources/images/alert.png',
		];

		$this->get_template()->template( 'pass/body/virtual-event/link', $args, true );
	}

	/**
	 * Add link to sample.
	 *
	 * @since 1.15.5
	 *
	 * @param array $context Sample template context.
	 *
	 * @return array Modified template context.
	 */
	public function add_link_to_sample( $context ): array {
		// Check if event exists.
		if ( ! isset( $context['event'] ) ) {
			return $context;
		}

		$context['event']->virtual_ticket_email_link = true;
		$context['event']->virtual_url               = home_url();

		return $context;
	}
}

