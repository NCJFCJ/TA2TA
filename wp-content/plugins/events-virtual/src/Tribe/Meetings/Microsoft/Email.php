<?php
/**
 * Manages the Microsoft Meet Email
 *
 * @since 1.13.0
 *
 * @package Tribe\Events\Virtual\Meetings\Microsoft
 */

namespace Tribe\Events\Virtual\Meetings\Microsoft;

use Tribe\Events\Virtual\Meetings\Microsoft\Event_Meta as Microsoft_Event_Meta;

/**
 * Class Email
 *
 * @since 1.13.0
 *
 * @package Tribe\Events\Virtual\Meetings\Microsoft
 */
class Email {

	/**
	 * Conditionally inject content into ticket email templates.
	 *
	 * @since 1.13.0
	 *
	 * @param string $template The template path, relative to src/views.
	 * @param array  $args     The template arguments.
	 *
	 * @return string
	 */
	public function maybe_change_email_template( $template, $args ) {
		$event = $args['event'];

		// Get event if not an object and an integer.
		if (
			is_integer( $args['event']  )
		) {
			$event = tribe_get_event( $args['event'] );
		}

		if ( empty( $event ) ) {
			return $template;
		}

		if (
			empty( $event->virtual )
			|| empty( $event->virtual_meeting )
			|| Microsoft_Event_Meta::$key_source_id !== $event->virtual_meeting_provider
		) {
			return $template;
		}

		$template = 'microsoft/email/ticket-email-microsoft-details';

		return $template;
	}
}
