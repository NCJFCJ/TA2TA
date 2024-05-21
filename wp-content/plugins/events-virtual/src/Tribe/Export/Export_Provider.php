<?php
/**
 * Handles the export-related functions of the plugin.
 *
 * @since   1.0.4
 * @package Tribe\Events\Virtual\Service_Providers;
 */

namespace Tribe\Events\Virtual\Export;

use TEC\Common\Contracts\Service_Provider;

/**
 * Class Export_Provider
 *
 * @since   1.0.4
 * @package Tribe\Events\Virtual\Export;
 */
class Export_Provider extends Service_Provider {

	/**
	 * Binds and sets up implementations and registers the required filters.
	 *
	 * @since 1.0.4
	 * @since 1.8.2 Change use of `tribe_google_calendar_parameters` to `tec_views_v2_single_event_gcal_link_parameters`
	 */
	public function register() {
		$this->container->singleton( 'events-virtual.export', $this );
		$this->container->singleton( static::class, $this );

		add_filter( 'tec_views_v2_single_event_gcal_link_parameters', [ $this, 'filter_google_calendar_parameters' ], 10, 2 );
		add_filter( 'tribe_ical_feed_item', [ $this, 'filter_ical_feed_items' ], 10, 2 );

		add_filter( 'tec_events_virtual_export_fields', [ $this, 'filter_video_source_google_calendar_parameters' ], 10, 5 );
		add_filter( 'tec_events_virtual_export_fields', [ $this, 'filter_video_source_ical_feed_items' ], 10, 5 );
		add_filter( 'tec_events_ical_outlook_single_event_import_url', [ $this, 'filter_outlook_single_event_export_url' ], 10, 4 );

		add_filter( 'tec_events_virtual_export_should_show', [ $this, 'filter_export_should_show' ], 5, 2 );
	}

	/**
	 * Filter the Google Calendar export parameters for an exporting event.
	 *
	 * @since 1.0.4
	 *
	 * @param array<string|string> $output   Google Calendar Link params.
	 * @param int                  $event_id The event id.
	 *
	 * @return  array<string|string> Google Calendar Link params.
	 */
	public function filter_google_calendar_parameters( $output, $event_id ) {

		return $this->container->make( Event_Export::class )->modify_export_output( $output, $event_id, 'location', 'gcal' );
	}

	/**
	 * Filter the iCal export parameters for an exporting event.
	 *
	 * @since 1.0.4
	 *
	 * @param array<string|string> $item       The various iCal file format components of this specific event item.
	 * @param \WP_Post             $event_post The WP_Post of this event.
	 *
	 * @return array<string|string>  The various iCal file format components of this specific event item.
	 */
	public function filter_ical_feed_items( $item, $event_post ) {
		return $this->container->make( Event_Export::class )->modify_export_output( $item, $event_post->ID, 'LOCATION', 'ical' );
	}

	/**
	 * Filter the Google Calendar export fields for a video source event.
	 *
	 * @since 1.7.3
	 * @since 1.8.0 add should_show parameter.
	 *
	 * @param array<string|string> $fields      The various file format components for this specific event.
	 * @param \WP_Post             $event       The WP_Post of this event.
	 * @param string               $key_name    The name of the array key to modify.
	 * @param string               $type        The name of the export type.
	 * @param boolean              $should_show Whether to modify the export fields for the current user, default to false.
	 *
	 * @return  array<string|string> Google Calendar Link params.
	 */
	public function filter_video_source_google_calendar_parameters( $fields, $event, $key_name, $type, $should_show ) {

		return $this->container->make( Event_Export::class )->modify_video_source_export_output( $fields, $event, $key_name, $type, $should_show );
	}

	/**
	 * Filter the iCal export fields for a video source event.
	 *
	 * @since 1.7.3
	 * @since 1.8.0 add should_show parameter.
	 *
	 * @param array<string|string> $fields      The various file format components for this specific event.
	 * @param \WP_Post             $event       The WP_Post of this event.
	 * @param string               $key_name    The name of the array key to modify.
	 * @param string               $type        The name of the export type.
	 * @param boolean              $should_show Whether to modify the export fields for the current user, default to false.
	 *
	 * @return array<string|string>  The various iCal file format components of this specific event item.
	 */
	public function filter_video_source_ical_feed_items( $fields, $event, $key_name, $type, $should_show ) {
		return $this->container->make( Event_Export::class )->modify_video_source_export_output( $fields, $event, $key_name, $type, $should_show );
	}

	/**
	 * Filter the Outlook single event export url for a Zoom source event.
	 *
	 * @since 1.13.0
	 *
	 * @param string               $url             The url used to subscribe to a calendar in Outlook.
	 * @param string               $base_url        The base url used to subscribe in Outlook.
	 * @param array<string|string> $params          An array of parameters added to the base url.
	 * @param Outlook_Methods      $outlook_methods An instance of the link abstract.
	 *
	 * @return string The export url used to generate an Outlook event for the single event.
	 */
	public function filter_outlook_single_event_export_url( $url, $base_url, $params, $outlook_methods ) {
		return $this->container->make( Event_Export::class )->filter_outlook_single_event_export_url( $url, $base_url, $params, $outlook_methods );
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
		return $this->container->make( Event_Export::class )->filter_export_should_show( $should_show, $event );
	}
}
