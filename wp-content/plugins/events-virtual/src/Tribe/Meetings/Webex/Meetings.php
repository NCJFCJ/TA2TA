<?php
/**
 * Handles the creation and updates of Webex Meetings via the Webex API.
 *
 * @since 1.9.0
 *
 * @package Tribe\Events\Virtual\Meetings\Webex
 */

namespace Tribe\Events\Virtual\Meetings\Webex;

use Tribe\Events\Virtual\Event_Meta as Virtual_Events_Meta;
use Tribe\Events\Virtual\Meetings\Webex\Event_Meta as Webex_Meta;

/**
 * Class Meetings
 *
 * @since 1.9.0
 *
 * @package Tribe\Events\Virtual\Meetings\Webex
 */
class Meetings extends Abstract_Meetings {

	/**
	 * Regex to determine if a Webex meeting join url.
	 *
	 * @since 1.9.0
	 *
	 * @var string
	 */
	protected $regex_meeting_join_url = '|(?<name>(\bwebex\b)).+?(MTID=(?<id>([a-zA-Z0-9]*)))|';

	/**
	 * Get the regex to get the Webex meeting/webinar id from a url.
	 *
	 * @since 1.9.0
	 *
	 * @return string The regex to get Webex meeting/webinar id from a url from the filter if a string or the default.
	 */
	public function get_regex_meeting_join_url() {
		/**
		 * Allow filtering of the regex to get Webex meeting join url.
		 *
		 * @since 1.9.0
		 *
		 * @param string The regex to verify a Webex meeting join url.
		 */
		$regex_meeting_join_url = apply_filters( 'tec_events_virtual_webex_regex_meeting_join_url', $this->regex_meeting_join_url );

		return is_string( $regex_meeting_join_url ) ? $regex_meeting_join_url : $this->regex_meeting_join_url;
	}

	/**
	 * {@inheritDoc}
	 */
	public function filter_virtual_autodetect_webex( $autodetect, $video_url, $video_source, $event, $ajax_data ) {
		if ( $autodetect['detected'] || $autodetect['guess'] ) {
			return $autodetect;
		}

		// All video sources are checked on the first autodetect run, only prevent checking of this source if it is set.
		if ( ! empty( $video_source ) && Webex_Meta::$key_source_id !== $video_source ) {
			return $autodetect;
		}

		// If virtual url, fail the request.
		if ( empty( $video_url ) ) {
			$autodetect['message'] = _x( 'No url found. Please enter a Webex meeting URL or change the selected source.', 'Webex autodetect missing video url error message.', 'events-virtual' );

			return $autodetect;
		}

		// Attempt to find the Webex meeting/webinar id from the url.
		preg_match( $this->get_regex_meeting_join_url(), $video_url, $matches );
		$web_link_found     = isset( $matches['name'] ) && isset( $matches['id'] ) ? true : false;
		if ( ! $web_link_found ) {
			$error_message = _x( 'No Webex Meeting web link found. Please check your web link URL.', 'No Webex Meeting web link  found for autodetect error message.', 'events-virtual' );
			$autodetect['message'] = $error_message;

			return $autodetect;
		}

		$autodetect['guess'] = Webex_Meta::$key_source_id;

		// Use the webex-account if available, otherwise try with the first account stored in the site.
		$accounts   = $this->api->get_list_of_accounts();
		$account_id = empty( $ajax_data['webex-account'] ) ? key( array_slice( $accounts, 0, 1 ) ) : esc_attr( $ajax_data['webex-account'] );

		if ( empty( $account_id ) ) {
			$autodetect['message'] = $this->api->get_no_account_message();

			return $autodetect;
		}

		$this->api->load_account_by_id( $account_id );
		if ( ! $this->api->is_ready() ) {
			$autodetect['message'] = $this->api->get_no_account_message();

			return $autodetect;
		}

		$video_url = esc_url( $video_url );
		$data = $this->api->fetch_meeting_data( $video_url, 'meeting' );

		// If no meeting or webinar found it is because the account is not authorized or does not exist.
		if ( empty( $data ) ) {
			$autodetect['message'] = _x( 'This Webex meeting could not be found in the selected account. Please select the associated account below and try again.', 'No Webex meeting or webinar found for autodetect error message.', 'events-virtual' );

			return $autodetect;
		}

		// Set as virtual event and video source to Webex.
		update_post_meta( $event->ID, Virtual_Events_Meta::$key_virtual, true );
		update_post_meta( $event->ID, Virtual_Events_Meta::$key_video_source, Webex_Meta::$key_source_id );
		$event->virtual_video_source = Webex_Meta::$key_source_id;

		// Save Webex data.
		$new_response['body'] = json_encode( $data );
		$this->process_meeting_connection_response( $new_response, $event->ID );

		// Set Webex as the autodetect source and set up success data and send back to smart url ui.
		update_post_meta( $event->ID, Virtual_Events_Meta::$key_autodetect_source, Webex_Meta::$key_source_id );
		$this->api->save_account_id_to_post( $event->ID, $account_id );
		$autodetect['detected']          = true;
		$autodetect['autodetect-source'] = Webex_Meta::$key_source_id;
		$autodetect['message']           = _x( 'Webex meeting successfully connected!', 'Webex meeting/webinar connected success message.', 'events-virtual' );
		$autodetect['html'] = $this->classic_editor->get_meeting_details( $event, false, $account_id, false );

		return $autodetect;
	}
}
