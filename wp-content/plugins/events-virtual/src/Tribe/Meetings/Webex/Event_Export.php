<?php
/**
 * Export functions for Webex.
 *
 * @since 1.9.0
 * @package Tribe\Events\Virtual\Meetings\Webex;
 */

namespace Tribe\Events\Virtual\Meetings\Webex;

use Tribe\Events\Virtual\Export\Abstract_Export;
use Tribe\Events\Virtual\Meetings\Webex\Event_Meta as Webex_Event_Meta;

/**
 * Class Event_Export
 *
 * @since 1.9.0
 * @since 1.13.0 - Moved modify_video_source_export_output() to the abstract class.
 *
 * @package Tribe\Events\Virtual\Meetings\Webex;
 */
class Event_Export extends Abstract_Export {

	/**
	 * Event_Export constructor.
	 *
	 * @since 1.13.0
	 */
	public function __construct() {
		self::$api_id = Webex_Event_Meta::$key_source_id;
	}

	/**
	 * Modify the gCal details component to add the password.
	 *
	 * @since 1.9.0
	 *
	 * @param array    $fields      The various file format components for this specific event.
	 * @param \WP_Post $event       The WP_Post of this event.
	 * @param string   $key_name    The name of the array key to modify.
	 * @param string   $type        The name of the export type.
	 * @param boolean  $should_show Whether to modify the export fields for the current user, default to false.
	 *
	 * @return array The various file format components for this specific event with the added password.
	 */
	public function add_password_to_gcal_details( $fields, $event, $key_name, $type, $should_show ) {
		if ( ! $should_show ) {
			return $fields;
		}

		if ( 'gcal' !== $type ) {
			return $fields;
		}

		if ( ! isset( $fields['details'] ) ) {
			return $fields;
		}

		if ( $this->str_contains( (string) $fields['details'], (string) $event->webex_password ) ) {
			return $fields;
		}

		if ( isset( $fields['details'] ) ) {
			$fields['details'] .= ' - ' . $this->get_password_label_with_password( $event );
		}

		return $fields;
	}

	/**
	 * Modify the iCal description component to add the password.
	 *
	 * @since 1.9.0
	 *
	 * @param array    $fields      The various file format components for this specific event.
	 * @param \WP_Post $event       The WP_Post of this event.
	 * @param string   $key_name    The name of the array key to modify.
	 * @param string   $type        The name of the export type.
	 * @param boolean  $should_show Whether to modify the export fields for the current user, default to false.
	 *
	 * @return array The various file format components for this specific event with the added password.
	 */
	public function add_password_to_ical_description( $fields, $event, $key_name, $type, $should_show ) {
		if ( ! $should_show ) {
			return $fields;
		}

		if ( 'ical' !== $type ) {
			return $fields;
		}

		if ( ! isset( $fields['DESCRIPTION'] ) ) {
			return $fields;
		}

		if ( $this->str_contains( (string) $fields['DESCRIPTION'], (string) $event->webex_password ) ) {
			return $fields;
		}

		if ( isset( $fields['DESCRIPTION'] ) ) {
			$fields['DESCRIPTION'] .= ' - ' . $this->get_password_label_with_password( $event );
		}

		return $fields;
	}

	/**
	 * Filter the Outlook body component to add the password.
	 *
	 * @since 1.13.0
	 *
	 * @param string               $url             The url used to subscribe to a calendar in Outlook.
	 * @param string               $base_url        The base url used to subscribe in Outlook.
	 * @param array<string|string> $params          An array of parameters added to the base url.
	 * @param Outlook_Methods      $outlook_methods An instance of the link abstract.
	 * @param \WP_Post             $event           The WP_Post of this event.
	 * @param boolean              $should_show     Whether to modify the export fields for the current user, default to false.
	 *
	 * @return string The export url used to generate an Outlook event for the single event.
	 */
	public function add_password_to_outlook_description( $url, $base_url, $params, $outlook_methods, $event, $should_show ) {
		if ( ! $should_show ) {
			return $url;
		}

		if ( ! isset( $params['body'] ) ) {
			return $url;
		}

		if ( $this->str_contains( (string) $params['body'], (string) $event->webex_password ) ) {
			return $url;
		}

		if ( isset( $params['body'] ) ) {
			$params['body'] .= ' - ' . $this->get_password_label_with_password( $event );
		}

		$url = add_query_arg( $params, $base_url );

		return $url;
	}

	/**
	 * Get the password label with included password.
	 *
	 * @since 1.9.0
	 *
	 * @param \WP_Post $event The WP_Post of this event.
	 *
	 * @return string the password label with included password.
	 */
	public function get_password_label_with_password( $event ) {
		return esc_html(
			sprintf(
				// translators: %1$s:  Webex meeting password.
				_x(
					'Webex Password: %1$s',
					'The label for the Webex Meeting password, followed by the password for an exported event.',
					'events-virtual'
				),
				$event->webex_password
			)
		);
	}
}
