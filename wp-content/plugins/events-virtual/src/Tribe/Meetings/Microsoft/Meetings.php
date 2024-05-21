<?php
/**
 * Handles the creation and updates of Microsoft Meets via the Microsoft API.
 *
 * @since 1.13.0
 *
 * @package Tribe\Events\Virtual\Meetings\Microsoft
 */

namespace Tribe\Events\Virtual\Meetings\Microsoft;

use Tribe\Events\Virtual\Event_Meta as Virtual_Events_Meta;
use Tribe\Events\Virtual\Meetings\Microsoft\Event_Meta as Microsoft_Meta;

/**
 * Class Meetings
 *
 * @since 1.13.0
 *
 * @package Tribe\Events\Virtual\Meetings\Microsoft
 */
class Meetings extends Abstract_Meetings {

	/**
	 * Regex to determine if a Microsoft Meet join url.
	 *
	 * @since 1.13.0
	 *
	 * @var string
	 */
	protected $regex_event_hash_url = '~(?<name>(\boutlook\b)).+?((read\/|item\/|itemid=)(?<id>([a-zA-Z0-9%]*)))~';

	/**
	 * Get the regex to get the Microsoft Event hash id from a url.
	 *
	 * @since 1.13.0
	 *
	 * @return string The regex to get Microsoft event hash id from a url, from the filter if a string or the default property.
	 */
	public function get_regex_microsoft_event_hash_url() {
		/**
		 * Allow filtering of the regex to get Microsoft event hash url.
		 *
		 * @since 1.13.0
		 *
		 * @param string The regex to verify a Microsoft event hash url.
		 */
		$regex_meeting_join_url = apply_filters( 'tec_events_virtual_microsoft_regex_event_hash_url', $this->regex_event_hash_url );

		return is_string( $regex_meeting_join_url ) ? $regex_meeting_join_url : $this->regex_event_hash_url;
	}

	/**
	 * {@inheritDoc}
	 */
	public function filter_virtual_autodetect_microsoft( $autodetect, $video_url, $video_source, $event, $ajax_data ) {
		if ( $autodetect['detected'] || $autodetect['guess'] ) {
			return $autodetect;
		}

		// All video sources are checked on the first autodetect run, only prevent checking of this source if it is set.
		if ( ! empty( $video_source ) && Microsoft_Meta::$key_source_id !== $video_source ) {
			return $autodetect;
		}

		// If virtual url, fail the request.
		if ( empty( $video_url ) ) {
			$autodetect['message'] = _x( 'No url found. Please enter a Outlook Event URL or change the selected source.', 'Microsoft autodetect missing video url error message.', 'events-virtual' );

			return $autodetect;
		}

		// Attempt to find the Microsoft event id from the url.
		preg_match( $this->get_regex_microsoft_event_hash_url(), $video_url, $matches );
		$event_link_found     = isset( $matches['name'] ) && isset( $matches['id'] ) ? true : false;
		if ( ! $event_link_found ) {
			$error_message = _x( 'No Microsoft Event link found. Please check your URL.', 'No Microsoft Event link found for autodetect error message.', 'events-virtual' );
			$autodetect['message'] = $error_message;

			return $autodetect;
		}

		$autodetect['guess'] = Microsoft_Meta::$key_source_id;

		// Use the microsoft-account if available, otherwise try with the first account stored in the site.
		$accounts   = $this->api->get_list_of_accounts();
		$account_id = empty( $ajax_data['microsoft-account'] ) ? key( array_slice( $accounts, 0, 1 ) ) : esc_attr( $ajax_data['microsoft-account'] );

		if ( empty( $account_id ) ) {
			$autodetect['message'] = $this->api->get_no_account_message();

			return $autodetect;
		}

		$this->api->load_account_by_id( $account_id );
		if ( ! $this->api->is_ready() ) {
			$autodetect['message'] = $this->api->get_no_account_message();

			return $autodetect;
		}

		// Decode url and change %2F to %2D as Outlook encodes dashes wrong.
		$microsoft_event_id = urldecode( str_replace( '%2F', '%2D', $matches['id'] ) );
		$data = $this->api->fetch_event_data( $microsoft_event_id );

		// If no Microsoft Meet found it is because the account is not authorized or does not exist.
		if ( empty( $data ) ) {
			$autodetect['message'] = _x( 'This Microsoft Meet could not be found in the selected account. Please select the associated account below and try again.', 'No Microsoft Meet found for autodetect error message.', 'events-virtual' );

			return $autodetect;
		}

		// Set as virtual event and video source to Microsoft.
		update_post_meta( $event->ID, Virtual_Events_Meta::$key_virtual, true );
		update_post_meta( $event->ID, Virtual_Events_Meta::$key_video_source, Microsoft_Meta::$key_source_id );
		$event->virtual_video_source = Microsoft_Meta::$key_source_id;

		// Save Microsoft data.
		$new_response['body'] = json_encode( $data );
		$this->process_meeting_connection_response( $new_response, $event->ID, $event, $account_id );

		// Set Microsoft as the autodetect source and set up success data and send back to smart url ui.
		update_post_meta( $event->ID, Virtual_Events_Meta::$key_autodetect_source, Microsoft_Meta::$key_source_id );
		$this->api->save_account_id_to_post( $event->ID, $account_id );
		$autodetect['detected']          = true;
		$autodetect['autodetect-source'] = Microsoft_Meta::$key_source_id;
		$autodetect['message']           = _x( 'Microsoft Meet successfully connected!', 'Microsoft Meet connected success message.', 'events-virtual' );
		$autodetect['html'] = $this->classic_editor->get_meeting_details( $event, false, $account_id, false );

		return $autodetect;
	}
}
