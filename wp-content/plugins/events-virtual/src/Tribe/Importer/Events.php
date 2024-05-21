<?php
/**
 * Handles the Virtual Events intergration into the CSV importer.
 *
 * @since   1.8.0
 *
 * @package Tribe\Events\Virtual\Importer
 */

namespace Tribe\Events\Virtual\Importer;

use Tribe\Events\Virtual\Event_Meta as Virtual_Events_Meta;
use Tribe\Events\Virtual\Compatibility\Event_Tickets\Event_Meta as Ticket_Events_Meta;
use Tribe\Events\Virtual\OEmbed;
use Tribe__Events__Importer__File_Importer_Events as CSV_Event_Importer;
use Tribe\Events\Virtual\Metabox;

/**
 * Class Events
 *
 * @since   1.8.0
 *
 * @package Tribe\Events\Virtual\Autodetect
 */
class Events {

	/**
	 * An instance of the plugin metabox handler.
	 *
	 * @var Metabox
	 */
	protected $metabox;

	/**
	 * An instance of the plugin ticket meta handler.
	 *
	 * @var Ticket_Events_Meta
	 */
	protected $ticket_meta;

	/**
	 * Events Importer constructor.
	 *
	 * @since 1.8.0
	 *
	 * @param Metabox            $metabox     An instance of the plugin metabox handler.
	 * @param Ticket_Events_Meta $ticket_meta An instance of the plugin ticket meta handler.
	 */
	public function __construct( Metabox $metabox, Ticket_Events_Meta $ticket_meta ) {
		$this->metabox     = $metabox;
		$this->ticket_meta = $ticket_meta;
	}

	/**
	 * Add Virtual Event column names to csv event import.
	 *
	 * @since 1.8.0
	 *
	 * @param array<string|string> $column_mapping An array of column names for csv event imports.
	 *
	 * @return array<string|string> An array of column names for csv event imports.
	 */
	public function importer_column_mapping( $column_mapping ) {
		$virtual_column_names = [
			'virtual'              => esc_html(
				sprintf(
					/* Translators: %1$s: Virtual Events label, %2$s: Events label singular */
					_x(
						'Configure %1$s %2$s',
						'Column name for importer of virtual events.',
						'events-virtual'
					),
					tribe_get_virtual_label(),
					tribe_get_event_label_singular()
				)
			),
			'event-type'              => esc_html(
				sprintf(
					_x(
						'Type of %1$s (Virtual, Hybrid)',
						'Column name for importer of virtual event type.',
						'events-virtual'
					),
					tribe_get_event_label_singular()
				)
			),
			'virtual-url'              => esc_html(
				_x(
					'Video Source URL',
					'Column name for importer of virtual event source url.',
					'events-virtual'
				)
			),
			'embed-video'              => esc_html(
				_x(
					'Embed Video',
					'Column name for importer of virtual event to embed the video.',
					'events-virtual'
				)
			),
			'linked-button'              => esc_html(
				_x(
					'Linked Button',
					'Column name for importer of virtual event linked button shows.',
					'events-virtual'
				)
			),
			'virtual-button-text'              => esc_html(
				_x(
					'Linked Button Text',
					'Column name for importer of virtual event linked button text.',
					'events-virtual'
				)
			),
			'show-embed-at'              => esc_html(
				_x(
					'Show Embed At',
					'Column name for importer of virtual event for when to show the emebed.',
					'events-virtual'
				)
			),
			'show-on-event'              => esc_html(
				_x(
					'Show Virtual Event Icon on Single',
					'Column name for importer of virtual event to show virtual icon on signle view.',
					'events-virtual'
				)
			),
			'show-on-views'              => esc_html(
				_x(
					'Show Virtual Event on all Views',
					'Column name for importer of virtual event to show virtual icon on all views.',
					'events-virtual'
				)
			),
			'show-embed-to'              => esc_html(
				_x(
					'Show Embed to',
					'Column name for importer of virtual event to show the embed to.',
					'events-virtual'
				)
			),
			'rsvp-email-link'              => esc_html(
				_x(
					'Include Link in RSVP Email',
					'Column name for importer of virtual event RSVP email link.',
					'events-virtual'
				)
			),
			'ticket-email-link'              => esc_html(
				_x(
					'Include Link in Ticket Email',
					'Column name for importer of virtual event ticket email link.',
					'events-virtual'
				)
			),
		];

		/**
		 * Filters the importer columns names for Virtual Events.
		 *
		 * @since  1.8.0
		 *
		 * @param array<string,string> A map of the Virtual Event column names.
		 */
		$virtual_column_names = apply_filters( 'tec_virtual_importer_event_column_names', $virtual_column_names );

		return array_merge( $column_mapping, $virtual_column_names );
	}

	/**
	 * Save virtual event meta of import of an event.
	 *
	 * @since 1.8.0
	 *
	 * @param integer             $event_id        The event ID to update.
	 * @param array<string|mixed> $record         An event record from the import.
	 * @param CSV_Event_Importer  $csv_events_obj An instance of the Tribe__Events__Importer__File_Importer_Events class.
	 */
	public function import_save_event_meta( $event_id, $record, $csv_events_obj ) {
		if ( ! $csv_events_obj instanceof CSV_Event_Importer ) {
			return;
		}

		$is_virtual = tribe_is_truthy( $csv_events_obj->get_value_by_key( $record, 'virtual' ) );
		if ( ! $is_virtual ) {
			return;
		}

		$can_embed_video = $video_source = '';
		$virtual_url = $csv_events_obj->get_value_by_key( $record, 'virtual-url' );
		$embed_video = $csv_events_obj->get_value_by_key( $record, 'embed-video' );
		// If the link is embeddable set the video source, autodetect and embed video.
		if ( $virtual_url ) {
			$video_source = 'video';
		}
		if ( $embed_video && tribe( OEmbed::class )->is_embeddable( $virtual_url ) ) {
			$can_embed_video = true;
		}

		$data = [
			'virtual'             => true,
			'event-type'          => esc_html( $this->get_event_type_value( $csv_events_obj->get_value_by_key( $record, 'event-type' ) ) ),
			// If there is a virtual url set the video source to video. ( Facebook Live, YouTube Live, and Zoom are not supported )
			'video-source'        => $video_source ? 'video' : '',
			'autodetect-source'   => $can_embed_video && $video_source ? 'oembed' : '',
			'embed-video'         => $can_embed_video && $video_source ? true : false,
			'virtual-url'         => esc_url( $virtual_url ),
			'virtual-button-text' => esc_html( $csv_events_obj->get_value_by_key( $record, 'virtual-button-text' ) ),
			'linked-button'       => tribe_is_truthy( $csv_events_obj->get_value_by_key( $record, 'linked-button' ) ),
			// Values of immediately - at-start.
			'show-embed-at'       => esc_html( $this->get_show_embed_at_value( $csv_events_obj->get_value_by_key( $record, 'show-embed-at' ) ) ),
			'show-on-event'       => tribe_is_truthy( $csv_events_obj->get_value_by_key( $record, 'show-on-event' ) ),
			'show-on-views'       => tribe_is_truthy( $csv_events_obj->get_value_by_key( $record, 'show-on-views' ) ),
			// Multiple values allowed ( all - logged-in - rsvp - ticket ). If it includes logged-in, then all is removed )
			'show-embed-to'       => $this->get_show_embed_to_value( $csv_events_obj->get_value_by_key( $record, 'show-embed-to' ) ),
			// Tickets related fields.
			'rsvp-email-link'     => tribe_is_truthy( $csv_events_obj->get_value_by_key( $record, 'rsvp-email-link' ) ),
			'ticket-email-link'   => tribe_is_truthy( $csv_events_obj->get_value_by_key( $record, 'ticket-email-link' ) ),
		];

		$this->metabox->update_fields( $event_id, $data );
		$this->ticket_meta->update_post_meta( $event_id, $data );

		// Set the autodetect source if there is a video source.
		if ( $can_embed_video && $video_source ) {
			update_post_meta( $event_id, Virtual_Events_Meta::$key_autodetect_source, Virtual_Events_Meta::$key_oembed_source_id );
		}
	}

	/**
	 * Get the value formatted to lowercase and whitespace trimed.
	 *
	 * @since 1.8.0
	 *
	 * @param string $value The value to format.
	 *
	 * @return string The formatted value.
	 */
	protected function get_formatted_value( $value ) {
		return strtolower( trim( $value ) );
	}

	/**
	 * Get the event type value.
	 *
	 * @since 1.8.0
	 *
	 * @param string $value The imported value for event type.
	 *
	 * @return string An empty string if it is not the event type of virtual or hybrid.
	 */
	protected function get_event_type_value( $value ) {
		$value = $this->get_formatted_value( $value );

		if (
			$value === Virtual_Events_Meta::$value_virtual_event_type ||
			$value === Virtual_Events_Meta::$value_hybrid_event_type
		) {
			return $value;
		}

		return '';
	}

	/**
	 * Get the show embed value.
	 *
	 * @since 1.8.0
	 *
	 * @param string $value The imported value for show embed at.
	 *
	 * @return string An empty string if it is not the show embed at value of at-start or immediately.
	 */
	protected function get_show_embed_at_value( $value ) {
		$value = $this->get_formatted_value( $value );

		if (
			$value === Virtual_Events_Meta::$value_show_embed_now ||
			$value === Virtual_Events_Meta::$value_show_embed_start
		) {
			return $value;
		}

		return '';
	}

	/**
	 * Get the value for the show embed to field.
	 *
	 * @since 1.8.0
	 *
	 * @param string $value The string of values for show embed to that are comma separated.
	 *
	 * @return string|array<string|string> The string or array of valus to save for the show embed to field.
	 */
	protected function get_show_embed_to_value( $value ) {
		$show_embed = explode( ',', $value );

		if ( ! is_array( $show_embed ) ) {
			return esc_html( $this->get_formatted_value( $show_embed ) );
		}

		$accepted_values = [
			Virtual_Events_Meta::$value_show_embed_to_all,
			Virtual_Events_Meta::$value_show_embed_to_logged_in,
			Ticket_Events_Meta::$value_show_embed_to_rsvp,
			Ticket_Events_Meta::$value_show_embed_to_ticket,
		];

		$cleaned_show_embed = array_filter( $show_embed, function ( $arr_value ) {
			return esc_html( $this->get_formatted_value( $arr_value ) );
		} );

		$only_accepted_values = array_intersect( $accepted_values, $cleaned_show_embed );

		// If both all and logged-in are found, remove all.
		if (
			( $key = array_search('all', $only_accepted_values ) ) !== false &&
			array_search('logged-in', $only_accepted_values ) !== false
		) {
		    unset($only_accepted_values[$key]);
		}

		return $only_accepted_values;
	}
}
