<?php
/**
 * Implements the methods shared for Google API.
 *
 * @since 1.11.0
 *
 * @package Tribe\Events\Virtual\Meetings\Google
 */

namespace Tribe\Events\Virtual\Meetings\Google;

use Tribe\Events\Virtual\Event_Meta as Virtual_Events_Meta;
use Tribe\Events\Virtual\Meetings\Google\Event_Meta as Google_Meta;
use Tribe\Events\Virtual\Traits\With_AJAX;
use Tribe__Date_Utils as Dates;

/**
 * Class Abstract_Meetings
 *
 * @since 1.11.0
 *
 * @package Tribe\Events\Virtual\Meetings\Google
 */
abstract class Abstract_Meetings {
	use With_AJAX;

	/**
	 * The name of the action used to generate a meeting creation link.
	 * The property also provides a reasonable default for the abstract class.
	 *
	 * @since 1.11.0
	 * @deprecated 1.13.0 - Use Actions::$create_action.
	 *
	 * @var string
	 */
	public static $create_action = 'events-virtual-meetings-google-meeting-create';

	/**
	 * The name of the action used to remove a meeting creation link.
	 * The property also provides a reasonable default for the abstract class.
	 *
	 * @since 1.11.0
	 * @deprecated 1.13.0 - Use Actions::$remove_action.
	 *
	 * @var string
	 */
	public static $remove_action = 'events-virtual-meetings-google-meeting-remove';

	/**
	 * The type of the meeting handled by the class instance.
	 * Defaults to the Meetings one.
	 *
	 * @since 1.11.0
	 *
	 * @var string
	 */
	public static $meeting_type = 'meeting';

	/**
	 * The Google API endpoint used to create and manage the meeting.
	 * Defaults to the one used for Meetings.
	 *
	 * @since 1.11.0
	 *
	 * @var string
	 */
	public static $api_endpoint = 'meetings';

	/**
	 * An instance of the Google API handler.
	 *
	 * @since 1.11.0
	 *
	 * @var Api
	 */
	protected $api;

	/**
	 * The Classic Editor rendering handler.
	 *
	 * @since 1.11.0
	 *
	 * @var Classic_Editor
	 */
	protected $classic_editor;

	/**
	 * The Actions name handler.
	 *
	 * @since 1.13.0
	 *
	 * @var Actions
	 */
	public $actions;

	/**
	 * Meetings constructor.
	 *
	 * @since 1.11.0
	 * @since 1.13.0 - Add the Action class.
	 *
	 * @param Api            $api            An instance of the Google API handler.
	 * @param Classic_Editor $classic_editor An instance of the Classic Editor rendering handler.
	 * @param Actions        $actions        An instance of the Actions name handler.
	 */
	public function __construct( Api $api, Classic_Editor $classic_editor, Actions $actions ) {
		$this->api            = $api;
		$this->classic_editor = $classic_editor;
		$this->actions        = $actions;
	}

	/**
	 * Filter the autodetect source to detect if a Google link.
	 *
	 * @since 1.11.0
	 *
	 * @param array<string|mixed> $autodetect   An array of the autodetect defaults.
	 * @param string              $video_url    The url to use to autodetect the video source.
	 * @param string              $video_source The optional name of the video source to attempt to autodetect.
	 * @param \WP_Post|null       $event        The event post object, as decorated by the `tribe_get_event` function.
	 * @param array<string|mixed> $ajax_data    An array of extra values that were sent by the ajax script.
	 *
	 * @return array<string|mixed> An array of the autodetect results.
	 */
	abstract public function filter_virtual_autodetect_google( $autodetect, $video_url, $video_source, $event, $ajax_data );

	/**
	 * Handles the request to generate a Google Meet.
	 *
	 * @since 1.11.0
	 *
	 * @param string|null $nonce The nonce that should accompany the request.
	 *
	 * @return bool Whether the request was handled or not.
	 */
	public function ajax_create( $nonce = null ) {
		if ( ! $this->check_ajax_nonce( $this->actions::$create_action, $nonce ) ) {
			return false;
		}

		$event = $this->check_ajax_post();

		if ( ! $event ) {
			return false;
		}

		$host_email = tribe_get_request_var( 'host_id' );
		// If no host id found, fail the request as account level apps do not support 'me'
		if ( empty( $host_email ) ) {
			$error_message = _x( 'The Google Host Email to access the API is missing.', 'Google Host Email is missing error message.', 'events-virtual' );
			$this->classic_editor->render_meeting_generation_error_details( $event, $error_message, true );

			wp_die();
		}

		// Load the account.
		$account_id = tribe_get_request_var( 'account_id' );
		// if no id, fail the request.
		if ( empty( $account_id ) ) {
			$error_message = _x( 'The Google Account ID to access the API is missing.', 'Account ID is missing error message.', 'events-virtual' );
			$this->classic_editor->render_meeting_generation_error_details( $event, $error_message, true );

			wp_die();
		}

		$this->api->load_account_by_id( $account_id );
		// If there is no token, then stop as the connection will fail.
		if ( ! $this->api->get_token_authorization_header() ) {
			$error_message = _x( 'The Google Account to access to API could not be loaded.', 'Google account loading error message.', 'events-virtual' );
			$this->classic_editor->render_meeting_generation_error_details( $event, $error_message, true );

			wp_die();
		}

		$post_id = $event->ID;
		$cached  = get_post_meta( $post_id, Virtual_Events_Meta::$prefix . 'google_meeting_data', true );

		/**
		 * Filters whether to force the recreation of the Google Meets link on each request or not.
		 *
		 * If the filters returns a truthy value, then each request, even for events that already had a Google Meet
		 * generated, will generate a new link, without re-using the previous one.
		 *
		 * @since 1.11.0
		 *
		 * @param bool $force   Whether to force the regeneration of Google Meet links or not.
		 * @param int  $post_id The post ID of the event the Meeting is being generated for.
		 */
		$force = apply_filters(
			"tec_events_virtual_meetings_google_{$this::$meeting_type}_force_recreate",
			true,
			$post_id
		);

		if ( ! $force && ! empty( $cached ) ) {
			$this->classic_editor->render_meeting_link_generator( $event, true, false, $account_id  );

			wp_die();
		}

		// Get the event times from the ajax script or fallback to the event object.
		$start_date = tribe_get_request_var( 'EventStartDate', $event->start_date );
		$start_time = tribe_get_request_var( 'EventStartTime', $event->start_time );
		$time_zone  = tribe_get_request_var( 'EventTimezone', $event->timezone );
		$end_date   = tribe_get_request_var( 'EventEndDate', $event->end_date );
		$end_time   = tribe_get_request_var( 'EventEndTime', $event->end_time );
		$all_day    = tribe_get_request_var( 'allDayCheckbox', $event->all_day );

		$start_datetime = $this->format_date_for_google( $start_date, $start_time, $time_zone, $all_day );
		$end_datetime   = $this->format_date_for_google( $end_date, $end_time, $time_zone, $all_day );

		$body = [
			'summary'        => $event->post_title,
			'start'          => $start_datetime,
			'end'            => $end_datetime,
			'source'         => [
				'title' => get_bloginfo( 'name' ),
				'url'   => get_site_url(),
			],
			'conferenceData' => [
				'createRequest' => [
					'requestId' => md5( $event->post_name . $post_id ),
					'conferenceSolutionKey' => [
						'type' => 'hangoutsMeet',
					]
				],
			],
		];

		/**
		 * Filters the contents of the request that will be made to the Google API to generate a meeting link.
		 *
		 * @since 1.11.0
		 *
		 * @param array<string,mixed> The current content of the request body.
		 * @param \WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
		 * @param Meetings $this  The current API handler object instance.
		 */
		$body = apply_filters(
			"tec_events_virtual_meetings_google_{$this::$meeting_type}_request_body",
			$body,
			$event,
			$this
		);

		$success = false;

		$this->api->post(
				Api::get_calendar_api_url( '', true ),
			[
				'headers' => [
					'authorization' => $this->api->get_token_authorization_header(),
					'content-type'  => 'application/json; charset=utf-8',
				],
				'body'    => wp_json_encode( $body ),
			],
			Api::POST_RESPONSE_CODE
		)->then(
			function ( array $response ) use ( $post_id, &$success, &$account_id ) {
				$event = tribe_get_event( $post_id, OBJECT, 'raw', true );

				$this->process_meeting_creation_response( $response, $post_id, $event, $account_id );

				$this->classic_editor->render_meeting_link_generator( $event, true, false, $account_id  );

				$success = true;

				wp_die();
			}
		)->or_catch(
			function ( \WP_Error $error ) use ( $event ) {
				do_action(
					'tribe_log',
					'error',
					__CLASS__,
					[
						'action'  => __METHOD__,
						'code'    => $error->get_error_code(),
						'message' => $error->get_error_message(),
					]
				);

				$error_data    = wp_json_encode( $error->get_error_data() );
				$decoded       = json_decode( $error_data, true );
				$error_message = null;
				if ( false !== $decoded && is_array( $decoded ) && isset( $decoded['message'] ) ) {
					$error_message = $decoded['message'];
				}

				$this->classic_editor->render_meeting_generation_error_details( $event, $error_message, true );

				wp_die();
			}
		);

		return $success;
	}

	/**
	 * Handles the AJAX request to remove the Google Meet information from an event.
	 *
	 * @since 1.11.0
	 *
	 * @param string|null $nonce The nonce that should accompany the request.
	 *
	 * @return bool|string Whether the request was handled or a string with html for meeting creation.
	 */
	public function ajax_remove( $nonce = null ) {
		if ( ! $this->check_ajax_nonce( $this->actions::$remove_action, $nonce ) ) {
			return false;
		}

		// phpcs:ignore
		if ( ! $event = $this->check_ajax_post() ) {
			return false;
		}

		// Remove the meta, but not the data.
		Google_Meta::delete_meeting_meta( $event->ID );

		// Send the HTML for the meeting creation.
		$this->classic_editor->render_initial_setup_options( $event, true );

		wp_die();
	}

	/**
	 * Handles update of Google Meet when Event details change.
	 *
	 * @since 1.11.0
	 *
	 * @param \WP_Post|int $event The event (or event ID) we're updating the meeting for.
	 */
	public function update( $event ) {
		// Get event if not an object.
		if ( ! ( $event instanceof \WP_Post ) ) {
			$event = tribe_get_event( $event );
		}

		// There is no meeting to update.
		if ( ! ( $event instanceof \WP_Post ) || empty( $event->google_meeting_id ) ) {
			return;
		}

		// If manually connected, do not update Google Meet when event details change.
		$manual_connected = get_post_meta( $event->ID, Virtual_Events_Meta::$key_autodetect_source, true );
		if ( Google_Meta::$key_source_id === $manual_connected ) {
			return;
		}

		$start_date = tribe_get_request_var( 'EventStartDate', $event->start_date );
		$start_time = tribe_get_request_var( 'EventStartTime', $event->start_time );
		$time_zone  = tribe_get_request_var( 'EventTimezone', $event->timezone );
		$end_date   = tribe_get_request_var( 'EventEndDate', $event->end_date );
		$end_time   = tribe_get_request_var( 'EventEndTime', $event->end_time );
		$all_day    = tribe_get_request_var( 'allDayCheckbox', $event->all_day );

		$event_body = [
			'summary' => $event->post_title,
			'start'   => $this->format_date_for_google( $start_date, $start_time, $time_zone, $all_day ),
			'end'     => $this->format_date_for_google( $end_date, $end_time, $time_zone, $all_day ),
		];

		$meeting_data = get_post_meta( $event->ID, Virtual_Events_Meta::$prefix . 'google_meeting_data', true );
		$meeting_body = [
			'summary' => $meeting_data['summary'],
			'start'   => $meeting_data['start'],
			'end'     => $meeting_data['end'],
		];

		$diff_summary = $event_body['summary'] !== $meeting_body['summary'];
		$diff_start   = array_diff_assoc( $event_body['start'], $meeting_body['start'] );
		$diff_end     = array_diff_assoc( $event_body['end'], $meeting_body['end'] );

		// Nothing to update.
		if ( empty( $diff_summary ) && empty( $diff_start ) && empty( $diff_end ) ) {
			return;
		}

		$post_id = $event->ID;

		/**
		 * Filters the contents of the request that will be made to the Google API to update a meeting link.
		 *
		 * @since 1.11.0
		 *
		 * @param array<string,mixed> The current content of the request body.
		 * @param \WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
		 * @param Meetings $this  The current API handler object instance.
		 */
		$body = apply_filters(
			"tec_events_virtual_meetings_google_{$this::$meeting_type}_update_request_body",
			$event_body,
			$event,
			$this
		);

		// Load the account.
		$account_id = $this->api->get_account_id_in_admin( $post_id );
		if ( empty( $account_id ) ) {
			return;
		}

		$this->api->load_account_by_id( $account_id );
		if ( ! $this->api->get_token_authorization_header() ) {
			return;
		}

		// Update.
		$this->api->patch(
				Api::get_calendar_api_url( $event->google_meeting_id ),
			[
				'headers' => [
					'Authorization' => $this->api->get_token_authorization_header(),
					'Content-Type'  => 'application/json; charset=utf-8',
				],
				'body'    => wp_json_encode( $body ),
			],
			Api::PUT_RESPONSE_CODE
		)->then(
			function ( array $response ) use ( $post_id, $event ) {
				$this->process_meeting_update_response( $response, $event, $post_id );
			}
		)->or_catch(
			function ( \WP_Error $error ) use ( $event ) {
				do_action(
					'tribe_log',
					'error',
					__CLASS__,
					[
						'action'  => __METHOD__,
						'code'    => $error->get_error_code(),
						'message' => $error->get_error_message(),
					]
				);

				$error_data    = wp_json_encode( $error->get_error_data() );
				$decoded       = json_decode( $error_data, true );
				$error_message = null;
				if ( false !== $decoded && is_array( $decoded ) && isset( $decoded['message'] ) ) {
					$error_message = $decoded['message'];
				}

				// Do something to indicate failure with $error_message?
				$this->classic_editor->render_meeting_generation_error_details( $event, $error_message, true );
			}
		);
	}

	/**
	 * Processes the Google API Meeting update response to massage, filter and save the data.
	 *
	 * @since 1.11.0
	 *
	 * @param array<string,mixed> $response The entire Google API response.
	 * @param \WP_Post            $event    The event post object.
	 * @param int                 $post_id  The event post ID.
	 *
	 * @return array<string,mixed>|false The Google Meet data or `false` on error.
	 */
	protected function process_meeting_update_response( $response, $event, $post_id ) {
		if ( empty( $response['response']['code'] ) || 200 !== $response['response']['code'] ) {
			return false;
		}

		$event = tribe_get_event( $event );
		if ( ! $event instanceof \WP_Post ) {
			return false;
		}

		$success = false;

		// Load the account.
		$account_id = $this->api->get_account_id_in_admin( $post_id );
		if ( empty( $account_id ) ) {
			return false;
		}

		$this->api->load_account_by_id( $account_id );
		if ( ! $this->api->get_token_authorization_header() ) {
			return false;
		}

		$this->api->get(
			Api::get_calendar_api_url( $event->google_meeting_id ),
			[
				'headers' => [
					'Authorization' => $this->api->get_token_authorization_header(),
					'Content-Type'  => 'application/json; charset=utf-8',
				],
			],
			Api::GET_RESPONSE_CODE
		)->then(
			function ( array $response ) use ( $post_id, &$success ) {
				$body     = json_decode( wp_remote_retrieve_body( $response ), true );
				$body_set = $this->api->has_proper_response_body( $body, ['webLink'] );

				if ( $body_set ) {
					$data = $this->prepare_meeting_data( $body );
					$this->update_post_meta( $post_id, $body, $data );
				}

				$success = true;
			}
		)->or_catch(
			function ( \WP_Error $error ) use ( $event ) {
				do_action(
					'tribe_log',
					'error',
					__CLASS__,
					[
						'action'  => __METHOD__,
						'code'    => $error->get_error_code(),
						'message' => $error->get_error_message(),
					]
				);

				$error_data    = wp_json_encode( $error->get_error_data() );
				$decoded       = json_decode( $error_data, true );
				$error_message = null;
				if ( false !== $decoded && is_array( $decoded ) && isset( $decoded['message'] ) ) {
					$error_message = $decoded['message'];
				}

				$this->classic_editor->render_meeting_generation_error_details( $event, $error_message, true );
			}
		);

		return $success;
	}

	/**
	 * Filters and massages the meeting data to prepare it to be saved in the post meta.
	 *
	 * @since 1.11.0
	 *
	 * @param array<string,mixed> $body The response body, in raw format.
	 *
	 * @return array<string,mixed> The meeting data, massaged and filtered.
	 */
	protected function prepare_meeting_data( $body ) {
		$data = [
			'id'            => $body['id'],
			'join_url'      => $body['hangoutLink'],
			'entry_points'  => $body['conferenceData']['entryPoints'],
			'conference_id' => $body['conferenceData']['conferenceId'],
			'host_email'    => $body['organizer']['email'],
		];

		/**
		 * Filters the Google API meeting data after a successful meeting creation.
		 *
		 * @since 1.11.0
		 *
		 * @param array<string,mixed> $data The data that will be returned in the AJAX response.
		 * @param array<string,mixed> $body The raw data returned from the Google API for the request.
		 */
		$data = apply_filters( "tec_events_virtual_meetings_google_{$this::$meeting_type}_data", $data, $body );

		return $data;
	}

	/**
	 * Processes the Google API Meeting connection response.
	 *
	 * @since 1.11.0
	 *
	 * @param array<string,mixed> $response The entire Google API response.
	 * @param int                 $post_id  The event post ID.
	 *
	 * @return array<string,mixed> The Google Meet data.
	 */
	public function process_meeting_connection_response( array $response, $post_id, $event, $account_id ) {
		return $this->process_meeting_creation_response( $response, $post_id, $event, $account_id );
	}

	/**
	 * Processes the Google API Meeting creation response to massage, filter and save the data.
	 *
	 * @since 1.11.0
	 *
	 * @param array<string,mixed> $response The entire Google API response.
	 * @param int                 $post_id  The event post ID.
	 *
	 * @return array<string,mixed> The Google Meet data.
	 */
	protected function process_meeting_creation_response( array $response, $post_id, $event = '', $account_id = '' ) {
		$body     = json_decode( wp_remote_retrieve_body( $response ), true );
		$body_set = $this->api->has_proper_response_body( $body, [ 'hangoutLink' ] );

		if ( ! $body_set ) {
			do_action(
				'tribe_log',
				'error',
				__CLASS__,
				[
					'action'   => __METHOD__,
					'message'  => "Google API {$this::$meeting_type} creation response is malformed.",
					'response' => $response,
				]
			);

			return [];
		}

		$data = $this->prepare_meeting_data( $body );
		$this->update_post_meta( $post_id, $body, $data );

		return $data;
	}

	/**
	 * Updates the event post meta depending on the meeting data provided.
	 *
	 * @since 1.11.0
	 *
	 * @param int                 $post_id       The post ID of the event to update the Google Meet related meta for.
	 * @param array<string,mixed> $response_body The Google API response body, as received from it.
	 * @param array<string,mixed> $meeting_data  The Google Meet data, as returned from the Google API request.
	 */
	protected function update_post_meta( $post_id, array $response_body, array $meeting_data ) {
		$prefix = Virtual_Events_Meta::$prefix;

		// Cache the raw meeting data for future use.
		update_post_meta( $post_id, $prefix . 'google_meeting_data', $response_body, true );

		// Set the video source to prevent issues with loading the information later.
		update_post_meta( $post_id, Virtual_Events_Meta::$key_video_source, Google_Meta::$key_source_id );

		$map = [
			$prefix . 'google_meeting_id'    => 'id',
			$prefix . 'google_join_url'      => 'join_url',
			$prefix . 'google_host_email'    => 'host_email',
			$prefix . 'google_entry_points'  => 'entry_points',
			$prefix . 'google_conference_id' => 'conference_id',
		];

		foreach ( $map as $meta_key => $data_key ) {
			if ( isset( $meeting_data[ $data_key ] ) ) {
				update_post_meta( $post_id, $meta_key, $meeting_data[ $data_key ] );
			} else {
				delete_post_meta( $post_id, $meta_key );
			}
		}

		// Add the meeting type, it's not part of the data coming from Google.
		update_post_meta( $post_id, $prefix . 'google_meeting_type', static::$meeting_type );
	}

	/**
	 * Format the event date for Google.
	 *
	 * @since 1.11.0
	 *
	 * @param string  $date      The start date of the event.
	 * @param string  $time      The start time of the event.
	 * @param string  $time_zone The timezone of the event.
	 * @param boolean $all_day   Whether an event is all day.
	 *
	 * @return array<string|string> An array with dateTime and timezone formatted for Google using DateTime::RFC3339 - 'Y-m-d\TH:i:sP'.
	 */
	public function format_date_for_google( $date, $time, $time_zone, $all_day = false ) {
		// Utilize the datepicker format when parse the Event Date to prevent the wrong date in Google.
		$datepicker_format = Dates::datepicker_formats( tribe_get_option( 'datepickerFormat' ) );
		$date_time         = Dates::datetime_from_format( $datepicker_format, $date ) . ' ' . $time;

		if ( $all_day ) {
			return [
				'date' => Dates::build_date_object( $date_time, $time_zone )->format( 'Y-m-d' ),
			];
		}

		return [
			'dateTime' => Dates::build_date_object( $date_time, $time_zone )->format( \DateTime::RFC3339 ),
			'timeZone' => $time_zone,
		];
	}
}
