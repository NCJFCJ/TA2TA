<?php
/**
 * Export functions abstract class.
 *
 * @since   1.7.3
 *
 * @package Tribe\Events\Virtual\Export;
 */

namespace Tribe\Events\Virtual\Export;

/**
 * Class Abstract_Export
 *
 * @since 1.7.3
 *
 * @package Tribe\Events\Virtual\Export;
 */
abstract class Abstract_Export {
	/**
	 * The internal id of the API integration.
	 *
	 * @since 1.13.0
	 *
	 * @var string
	 */
	public static $api_id = '';

	/**
	 * Format the exported value to conform to the export type's standard.
	 *
	 * @since 1.7.3
	 *
	 * @param string $value    The value being exported.
	 * @param string $key_name The key name to add to the value.
	 * @param string $type     The name of the export, ie ical, gcal, etc...
	 *
	 * @return string The value to add to the export.
	 */
	public function format_value( $value, $key_name, $type ) {

		if ( 'ical' === $type ) {
			/**
			 * With iCal we have to include the key name with the url
			 * or the export will only include the url without the defining tag.
			 * Example of expected output: - Location: https://tri.be?326t3425225
			 */
			$value = $key_name . ':' . $value;
		}

		return $value;
	}

	/**
	 * Checks if a string is found in another string.
	 *
	 * @since 1.11.0
	 *
	 * @param string $haystack The string to search in.
	 * @param string $needle   The string to search for.
	 *
	 * @return bool Whether or not the $needle was found.
	 */
	protected function str_contains( string $haystack, string $needle ) {
		return $needle !== '' && mb_strpos( $haystack, $needle ) !== false;
	}

	/**
	 * Modify the export parameters for an API source.
	 *
	 * @since 1.13.0
	 *
	 * @param array    $fields      The various file format components for this specific event.
	 * @param \WP_Post $event       The WP_Post of this event.
	 * @param string   $key_name    The name of the array key to modify.
	 * @param string   $type        The name of the export type.
	 * @param boolean  $should_show Whether to modify the export fields for the current user, default to false.
	 *
	 * @return array The various file format components for this specific event.
	 */
	public function modify_video_source_export_output( $fields, $event, $key_name, $type, $should_show ) {
		$api_id = static::$api_id;

		if ( $api_id !== $event->virtual_video_source ) {
			return $fields;
		}

		// If it should not show or no linked button and details, set the permalink and return.
		if (
			! $should_show ||
			(
				! $event->virtual_linked_button &&
				! $event->virtual_meeting_display_details
			)
		 ) {
			$fields[ $key_name ] = $this->format_value( get_the_permalink( $event->ID ), $key_name, $type );

			return $fields;
		}

		$url = empty( $event->virtual_meeting_url ) ? $event->virtual_url : $event->virtual_meeting_url;

		$fields[ $key_name ] = $this->format_value( $url, $key_name, $type );

		/**
		 * Allow filtering of the export fields for an API.
		 *
		 * @since 1.13.0
		 *
		 * @param array    $fields      The various file format components for this specific event.
		 * @param \WP_Post $event       The WP_Post of this event.
		 * @param string   $key_name    The name of the array key to modify.
		 * @param string   $type        The name of the export type.
		 * @param boolean  $should_show Whether to modify the export fields for the current user, default to false.
		 */
		$fields = apply_filters( "tec_events_virtual_{$api_id}_export_fields", $fields, $event, $key_name, $type, $should_show );

		return $fields;
	}

	/**
	 * Filter the Outlook single event export url for an API source event.
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
	public function filter_outlook_single_event_export_url_by_api( $url, $base_url, $params, $outlook_methods, $event, $should_show ) {
		$api_id = static::$api_id;

		if ( $api_id !== $event->virtual_video_source ) {
			return $url;
		}

		// If it should not show or no linked button and details, set the permalink and return.
		if (
			! $should_show ||
			(
				! $event->virtual_linked_button &&
				! $event->virtual_meeting_display_details
			)
		 ) {

			return $url;
		}

		$api_url        = empty( $event->virtual_meeting_url ) ? $event->virtual_url : $event->virtual_meeting_url;
		$params['body'] = trim( $params['body'] . ' ' . $api_url );
		$url            = add_query_arg( $params, $base_url );

		/**
		 * Allow filtering of the export fields for an API.
		 *
		 * @since 1.13.0
		 *
		 * @param string               $url             The url used to subscribe to a calendar in Outlook.
		 * @param string               $base_url        The base url used to subscribe in Outlook.
		 * @param array<string|string> $params          An array of parameters added to the base url.
		 * @param Outlook_Methods      $outlook_methods An instance of the link abstract.
		 * @param \WP_Post             $event           The WP_Post of this event.
		 * @param boolean              $should_show     Whether to modify the export fields for the current user, default to false.
		 */
		$url = apply_filters( "tec_events_virtual_outlook_{$api_id}_export_url", $url, $base_url, $params, $outlook_methods, $event, $should_show );

		return $url;
	}
}
