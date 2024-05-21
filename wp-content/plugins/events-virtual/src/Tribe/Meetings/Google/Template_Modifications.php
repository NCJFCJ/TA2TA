<?php
/**
 * Handles the templates modifications required by the Google API integration.
 *
 * @since 1.11.0
 *
 * @package Tribe\Events\Virtual\Meetings\Google
 */

namespace Tribe\Events\Virtual\Meetings\Google;

use Tribe\Events\Virtual\Integrations\Abstract_Template_Modifications;
use Tribe\Events\Virtual\Meetings\Google\Event_Meta as Google_Event_Meta;

/**
 * Class Template_Modifications
 *
 * @since 1.11.0
 * @since 1.13.0 - Utilize an abstract class.
 *
 * @package Tribe\Events\Virtual\Meetings\Google
 */
class Template_Modifications extends Abstract_Template_Modifications {

	/**
	 * {@inheritDoc}
	 */
	public function setup() {
		self::$api_id        = Google_Event_Meta::$key_source_id;
		self::$option_prefix = Settings::$option_prefix;
	}

	/**
	 * Adds Google details to event single.
	 *
	 * @since 1.11.0
	 * @deprecated 1.13.0 - Replaced with $this->add_event_single_api_details, see Abstract_Template_Modifications class.
	 */
	public function add_event_single_google_details() {
		_deprecated_function( __METHOD__, '1.13.1', 'Use $this->add_event_single_api_details() instead.' );

		// Don't show on password protected posts.
		if ( post_password_required() ) {
			return;
		}

		$event = tribe_get_event( get_the_ID() );

		if (
			empty( $event->virtual )
			|| empty( $event->virtual_meeting )
			|| empty( $event->virtual_should_show_embed )
			|| empty( $event->virtual_meeting_display_details )
			|| Google_Event_Meta::$key_source_id !== $event->virtual_meeting_provider
		) {
			return;
		}

		/**
		 * Filters whether the link button should open in a new window or not.
		 *
		 * @since 1.11.0
		 *
		 * @param boolean $link_button_new_window  Boolean of if link button should open in new window.
		 */
		$link_button_new_window = apply_filters( 'tec_events_virtual_link_button_new_window', false );

		$link_button_attrs = [];
		if ( ! empty( $link_button_new_window ) ) {
			$link_button_attrs['target'] = '_blank';
		}

		/**
		 * Filters whether the Google link should open in a new window or not.
		 *
		 * @since 1.11.0
		 *
		 * @param boolean $google_link_new_window  Boolean of if the Google link should open in new window.
		 */
		$google_link_new_window = apply_filters( 'tec_events_virtual_google_link_new_window', false );

		$google_link_attrs = [];
		if ( ! empty( $google_link_new_window ) ) {
			$google_link_attrs['target'] = '_blank';
		}

		$context = [
			'event'             => $event,
			'link_button_attrs' => $link_button_attrs,
			'google_link_attrs'  => $google_link_attrs,
		];

		$this->template->template( 'google/single/google-details', $context );
	}
}
