<?php
/**
 * The Virtual Event Integration with Event Automator Event Mapping.
 *
 * @since 1.13.5
 * @package TEC\Events_Virtual\Compatibility\Event_Automator\Zapier\Maps
 */

namespace TEC\Events_Virtual\Compatibility\Event_Automator\Zapier\Maps;

use WP_Post;

/**
 * Class Event
 *
 * @since 1.13.5
 *
 * @package TEC\Events_Virtual\Compatibility\Event_Automator\Zapier\Maps
 */
class Event {

	/**
	 * The Power Automate service id.
	 *
	 * @since 1.15.3
	 *
	 * @var string
	 */
	public static $pa_service_id = 'power-automate';

	/**
	 * The Zapier service id.
	 *
	 * @since 1.15.3
	 *
	 * @var string
	 */
	public static $zapier_service_id = 'zapier';

	/**
	 * Filters the event details sent to a 3rd party.
	 *
	 * @since 1.13.5
	 * @since 1.15.3 - Add Service ID.
	 *
	 * @param array<string|mixed> $next_eventAn array of event details.
	 * @param WP_Post             $event        An instance of the event WP_Post object.
	 * @param string              $service_id   The service id used to modify the mapped event details.
	 *
	 * @return array<string|mixed> $next_event An array of event details.
	 */
	public function add_virtual_fields( array $next_event, WP_Post $event, $service_id ) {
		if ( ! $event->virtual ) {
			return $next_event;
		}

		// VE Fields.
		$ve_fields = [
			'virtual'                    => true,
			'virtual_video_source'       => $event->virtual_video_source,
			'virtual_event_type'         => $event->virtual_event_type,
			'virtual_autodetect_source'  => $event->virtual_autodetect_source,
			'virtual_url'                => $event->virtual_url,
			'virtual_meeting_provider'   => $event->virtual_meeting_provider,
			'virtual_provider_details'   => [],
			'virtual_embed_video'        => $event->virtual_embed_video,
			'virtual_linked_button'      => $event->virtual_linked_button,
			'virtual_linked_button_text' => $event->virtual_linked_button_text,
			'virtual_show_embed_at'      => $event->virtual_show_embed_at,
			'virtual_show_embed_to'      => $event->virtual_show_embed_to,
			'virtual_show_on_event'      => $event->virtual_show_on_event,
			'virtual_show_on_views'      => $event->virtual_show_on_views,
			'virtual_show_lead_up'       => $event->virtual_show_lead_up,
			'virtual_rsvp_email_link'    => $event->virtual_rsvp_email_link,
			'virtual_ticket_email_link'  => $event->virtual_ticket_email_link,
			'virtual_is_immediate'       => $event->virtual_is_immediate,
			'virtual_is_linkable'        => $event->virtual_is_linkable,
			'virtual_should_show_embed'  => $event->virtual_should_show_embed,
			'virtual_should_show_link'   => $event->virtual_should_show_link,
		];

		$next_event = array_merge( $next_event, $ve_fields );

		/**
		 * Filters the event details map sent to a 3rd party.
		 *
		 * @since 1.13.5
		 * @since 1.15.3 - Add Service ID.
		 *
		 * @param array<string|mixed> $next_event An array of event details.
		 * @param WP_Post             $event      An instance of the event WP_Post object.
		 * @param string              $service_id The service id used to modify the mapped event details.
		 */
		$next_event = apply_filters( 'tec_virtual_automator_map_event_details', $next_event, $event, $service_id );

		// Power Automate Expects an array of provider details and considers the standard formatting as an object.
		if ( $service_id === static::$pa_service_id && ! empty( $next_event['virtual_provider_details'] ) ) {
			$provider_details = $next_event['virtual_provider_details'];
			unset( $next_event['virtual_provider_details'] );
			$next_event['virtual_provider_details'][0] = $provider_details;
		}

		return $next_event;
	}
}
