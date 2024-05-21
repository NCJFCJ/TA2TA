<?php
/**
 * Handles the creation and updates of Google Meets via the Google API.
 *
 * @since 1.11.0
 *
 * @package Tribe\Events\Virtual\Meetings\Google
 */

namespace Tribe\Events\Virtual\Meetings\Google;

use Tribe\Events\Virtual\Event_Meta as Virtual_Events_Meta;
use Tribe\Events\Virtual\Meetings\Google\Event_Meta as Google_Meta;

/**
 * Class Meetings
 *
 * @since 1.11.0
 *
 * @package Tribe\Events\Virtual\Meetings\Google
 */
class Meetings extends Abstract_Meetings {

	/**
	 * Regex to determine if a Google Meet join url.
	 *
	 * @since 1.11.0
	 *
	 * @var string
	 */
	protected $regex_event_hash_url = '~(?<name>(\bgoogle\b)).+?((eventedit\/|tmeid=)(?<id>([a-zA-Z0-9]*)))~';

	/**
	 * Get the regex to get the Google Event hash id from a url.
	 *
	 * @since 1.11.0
	 *
	 * @return string The regex to get Google event hash id from a url, from the filter if a string or the default property.
	 */
	public function get_regex_google_event_hash_url() {
		/**
		 * Allow filtering of the regex to get Google event hash url.
		 *
		 * @since 1.11.0
		 *
		 * @param string The regex to verify a Google event hash url.
		 */
		$regex_meeting_join_url = apply_filters( 'tec_events_virtual_google_regex_event_hash_url', $this->regex_event_hash_url );

		return is_string( $regex_meeting_join_url ) ? $regex_meeting_join_url : $this->regex_event_hash_url;
	}

	/**
	 * {@inheritDoc}
	 */
	public function filter_virtual_autodetect_google( $autodetect, $video_url, $video_source, $event, $ajax_data ) {
		if ( $autodetect['detected'] || $autodetect['guess'] ) {
			return $autodetect;
		}

		// All video sources are checked on the first autodetect run, only prevent checking of this source if it is set.
		if ( ! empty( $video_source ) && Google_Meta::$key_source_id !== $video_source ) {
			return $autodetect;
		}

		// If virtual url, fail the request.
		if ( empty( $video_url ) ) {
			$autodetect['message'] = _x( 'No url found. Please enter a Google Meet URL or change the selected source.', 'Google autodetect missing video url error message.', 'events-virtual' );

			return $autodetect;
		}

		// Attempt to find the Google event id from the url.
		preg_match( $this->get_regex_google_event_hash_url(), $video_url, $matches );
		$event_link_found     = isset( $matches['name'] ) && isset( $matches['id'] ) ? true : false;
		if ( ! $event_link_found ) {
			$error_message = _x( 'No Google Event link found. Please check your URL.', 'No Google Event link found for autodetect error message.', 'events-virtual' );
			$autodetect['message'] = $error_message;

			return $autodetect;
		}

		$decoded_string    = base64_decode( $matches['id'] );
		$decoded_array = explode( ' ', $decoded_string );
		if ( ! isset( $decoded_array[0], $decoded_array[1] ) ) {
			$error_message = _x( 'No Google Event ID found. Please check your URL.', 'No Google Event ID found for autodetect error message.', 'events-virtual' );
			$autodetect['message'] = $error_message;

			return $autodetect;
		}

		$autodetect['guess'] = Google_Meta::$key_source_id;

		// Use the google-account if available, otherwise try with the first account stored in the site.
		$accounts   = $this->api->get_list_of_accounts( true );
		$filtered_account = wp_list_filter($accounts, [
		    'email' => $decoded_array[1]
		]);
		$account_id = empty( $ajax_data['google-account'] ) ? key( $filtered_account ) : esc_attr( $ajax_data['google-account'] );

		if ( empty( $account_id ) ) {
			$autodetect['message'] = $this->api->get_no_account_message();

			return $autodetect;
		}

		$this->api->load_account_by_id( $account_id );
		if ( ! $this->api->is_ready() ) {
			$autodetect['message'] = $this->api->get_no_account_message();

			return $autodetect;
		}

		$google_event_id = esc_attr( $decoded_array[0] );
		$data = $this->api->fetch_event_data( $google_event_id );

		// If no Google Meet found it is because the account is not authorized or does not exist.
		if ( empty( $data ) ) {
			$autodetect['message'] = _x( 'This Google Meet could not be found in the selected account. Please select the associated account below and try again.', 'No Google Meet found for autodetect error message.', 'events-virtual' );

			return $autodetect;
		}

		// Set as virtual event and video source to Google.
		update_post_meta( $event->ID, Virtual_Events_Meta::$key_virtual, true );
		update_post_meta( $event->ID, Virtual_Events_Meta::$key_video_source, Google_Meta::$key_source_id );
		$event->virtual_video_source = Google_Meta::$key_source_id;

		// Save Google data.
		$new_response['body'] = json_encode( $data );
		$this->process_meeting_connection_response( $new_response, $event->ID, $event, $account_id );

		// Set Google as the autodetect source and set up success data and send back to smart url ui.
		update_post_meta( $event->ID, Virtual_Events_Meta::$key_autodetect_source, Google_Meta::$key_source_id );
		$this->api->save_account_id_to_post( $event->ID, $account_id );
		$autodetect['detected']          = true;
		$autodetect['autodetect-source'] = Google_Meta::$key_source_id;
		$autodetect['message']           = _x( 'Google Meet successfully connected!', 'Google Meet connected success message.', 'events-virtual' );
		$autodetect['html'] = $this->classic_editor->get_meeting_details( $event, false, $account_id, false );

		return $autodetect;
	}
}
