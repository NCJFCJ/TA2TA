<?php
/**
 * Implements the methods shared for Webex API.
 *
 * @since 1.9.0
 *
 * @package Tribe\Events\Virtual\Meetings\Webex
 */

namespace Tribe\Events\Virtual\Meetings\Webex;

use Tribe\Events\Virtual\Event_Meta as Virtual_Events_Meta;
use Tribe\Events\Virtual\Meetings\Webex\Event_Meta as Webex_Meta;
use Tribe\Events\Virtual\Traits\With_AJAX;
use Tribe__Date_Utils as Dates;

/**
 * Class Abstract_Meetings
 *
 * @since 1.9.0
 *
 * @package Tribe\Events\Virtual\Meetings\Webex
 */
abstract class Abstract_Meetings {
	use With_AJAX;

	/**
	 * The name of the action used to generate a meeting creation link.
	 * The property also provides a reasonable default for the abstract class.
	 *
	 * @since 1.9.0
	 * @deprecated 1.13.0 - Use Actions::$create_action.
	 *
	 * @var string
	 */
	public static $create_action = 'events-virtual-meetings-webex-meeting-create';

	/**
	 * The name of the action used to remove a meeting creation link.
	 * The property also provides a reasonable default for the abstract class.
	 *
	 * @since 1.9.0
	 * @deprecated 1.13.0 - Use Actions::$remove_action.
	 *
	 * @var string
	 */
	public static $remove_action = 'events-virtual-meetings-webex-meeting-remove';

	/**
	 * The type of the meeting handled by the class instance.
	 * Defaults to the Meetings one.
	 *
	 * @since 1.9.0
	 *
	 * @var string
	 */
	public static $meeting_type = 'meeting';

	/**
	 * The Webex API endpoint used to create and manage the meeting.
	 * Defaults to the one used for Meetings.
	 *
	 * @since 1.9.0
	 *
	 * @var string
	 */
	public static $api_endpoint = 'meetings';

	/**
	 * An instance of the Webex API handler.
	 *
	 * @since 1.9.0
	 *
	 * @var Api
	 */
	protected $api;

	/**
	 * An instance of the Actions name handler.
	 *
	 * @since 1.13.0
	 *
	 * @var Actions
	 */
	public $actions;

	/**
	 * The Classic Editor rendering handler.
	 *
	 * @since 1.9.0
	 *
	 * @var Classic_Editor
	 */
	protected $classic_editor;

	/**
	 * Meetings constructor.
	 *
	 * @since 1.9.0
	 * @since 1.13.0 - Add the Action class.
	 *
	 * @param Api            $api            An instance of the Webex API handler.
	 * @param Classic_Editor $classic_editor An instance of the Classic Editor rendering handler.
	 * @param Actions        $actions        An instance of the Actions name handler.
	 */
	public function __construct( Api $api, Classic_Editor $classic_editor, Actions $actions ) {
		$this->api            = $api;
		$this->classic_editor = $classic_editor;
		$this->actions        = $actions;
	}

	/**
	 * Filter the autodetect source to detect if a Webex link.
	 *
	 * @since 1.9.0
	 *
	 * @param array<string|mixed> $autodetect   An array of the autodetect defaults.
	 * @param string              $video_url    The url to use to autodetect the video source.
	 * @param string              $video_source The optional name of the video source to attempt to autodetect.
	 * @param \WP_Post|null       $event        The event post object, as decorated by the `tribe_get_event` function.
	 * @param array<string|mixed> $ajax_data    An array of extra values that were sent by the ajax script.
	 *
	 * @return array<string|mixed> An array of the autodetect results.
	 */
	abstract public function filter_virtual_autodetect_webex( $autodetect, $video_url, $video_source, $event, $ajax_data );

	/**
	 * Handles the request to generate a Webex meeting.
	 *
	 * @since 1.9.0
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
			$error_message = _x( 'The Webex Host Email to access the API is missing.', 'Webex Host Email is missing error message.', 'events-virtual' );
			$this->classic_editor->render_meeting_generation_error_details( $event, $error_message, true );

			wp_die();
		}

		// Load the account.
		$account_id = tribe_get_request_var( 'account_id' );
		// if no id, fail the request.
		if ( empty( $account_id ) ) {
			$error_message = _x( 'The Webex Account ID to access the API is missing.', 'Account ID is missing error message.', 'events-virtual' );
			$this->classic_editor->render_meeting_generation_error_details( $event, $error_message, true );

			wp_die();
		}

		$this->api->load_account_by_id( $account_id );
		// If there is no token, then stop as the connection will fail.
		if ( ! $this->api->get_token_authorization_header() ) {
			$error_message = _x( 'The Webex Account to access to API could not be loaded.', 'Webex account loading error message.', 'events-virtual' );
			$this->classic_editor->render_meeting_generation_error_details( $event, $error_message, true );

			wp_die();
		}

		$all_day = tribe_get_request_var( 'allDayCheckbox', $event->all_day );
		if ( $all_day ) {
			$error_message = _x( 'The Webex API does not support all day events, please set a start date and end date with time for both.', 'Webex all day error message.', 'events-virtual' );
			$this->classic_editor->render_meeting_generation_error_details( $event, $error_message, true );

			wp_die();
		}

		$post_id = $event->ID;
		$cached  = get_post_meta( $post_id, Virtual_Events_Meta::$prefix . 'webex_meeting_data', true );

		/**
		 * Filters whether to force the recreation of the Webex meetings link on each request or not.
		 *
		 * If the filters returns a truthy value, then each request, even for events that already had a Webex meeting
		 * generated, will generate a new link, without re-using the previous one.
		 *
		 * @since 1.9.0
		 *
		 * @param bool $force   Whether to force the regeneration of Webex Meeting links or not.
		 * @param int  $post_id The post ID of the event the Meeting is being generated for.
		 */
		$force = apply_filters(
			"tec_events_virtual_meetings_webex_{$this::$meeting_type}_force_recreate",
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

		$start_datetime = $this->format_date_for_webex( $start_date, $start_time, $time_zone );
		$end_datetime   = $this->format_date_for_webex( $end_date, $end_time, $time_zone );
		if ( $start_datetime === $end_datetime ) {
			$error_message = _x( 'To create an event please set the event end time at least 10 minutes from the start time.', 'Webex no duration error message.', 'events-virtual' );
			$this->classic_editor->render_meeting_generation_error_details( $event, $error_message, true );

			wp_die();
		}

		/**
		 * Password is not included as a random password conforming to the site's password rules will be generated automatically.
		 * @see: https://developer.webex.com/docs/api/v1/meetings/create-a-meeting
		 */
		$body = [
			'title'     => $event->post_title,
			'start'     => $start_datetime,
			'end'       => $end_datetime,
			'timezone'  => $time_zone,
			'hostEmail' => $host_email,
		];

		/**
		 * Filters the contents of the request that will be made to the Webex API to generate a meeting link.
		 *
		 * @since 1.9.0
		 *
		 * @param array<string,mixed> The current content of the request body.
		 * @param \WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
		 * @param Meetings $this  The current API handler object instance.
		 */
		$body = apply_filters(
			"tec_events_virtual_meetings_webex_{$this::$meeting_type}_request_body",
			$body,
			$event,
			$this
		);

		$success = false;

		$this->api->post(
			Api::$api_base . 'meetings',
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
				$this->process_meeting_creation_response( $response, $post_id );

				$event = tribe_get_event( $post_id, OBJECT, 'raw', true );
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
	 * Handles the AJAX request to remove the Webex Meeting information from an event.
	 *
	 * @since 1.9.0
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
		Webex_Meta::delete_meeting_meta( $event->ID );

		// Send the HTML for the meeting creation.
		$this->classic_editor->render_initial_setup_options( $event, true );

		wp_die();
	}

	/**
	 * Handles update of Webex meeting when Event details change.
	 *
	 * @since 1.9.0
	 *
	 * @param \WP_Post|int $event The event (or event ID) we're updating the meeting for.
	 */
	public function update( $event ) {
		// Get event if not an object.
		if ( ! ( $event instanceof \WP_Post ) ) {
			$event = tribe_get_event( $event );
		}

		// There is no meeting to update.
		if ( ! ( $event instanceof \WP_Post ) || empty( $event->webex_meeting_id ) ) {
			return;
		}

		// If manually connected, do not update Webex meeting or webinar when event details change.
		$manual_connected = get_post_meta( $event->ID, Virtual_Events_Meta::$key_autodetect_source, true );
		if ( Webex_Meta::$key_source_id === $manual_connected ) {
			return;
		}

		$start_date = tribe_get_request_var( 'EventStartDate', $event->start_date );
		$start_time = tribe_get_request_var( 'EventStartTime', $event->start_time );
		$time_zone  = tribe_get_request_var( 'EventTimezone', $event->timezone );
		$end_date   = tribe_get_request_var( 'EventEndDate', $event->end_date );
		$end_time   = tribe_get_request_var( 'EventEndTime', $event->end_time );

		$event_body = [
			'title'    => $event->post_title,
			'start'    => $this->format_date_for_webex( $start_date, $start_time, $time_zone ),
			'end'      => $this->format_date_for_webex( $end_date, $end_time, $time_zone ),
			'timezone' => $event->timezone,
			'password' => $event->webex_password,
		];

		$meeting_data = get_post_meta( $event->ID, Virtual_Events_Meta::$prefix . 'webex_meeting_data', true );
		$meeting_body = [
			'title'    => $meeting_data['title'],
			'start'    => $meeting_data['start'],
			'end'      => $meeting_data['end'],
			'timezone' => $meeting_data['timezone'],
			'password' => $meeting_data['password'],
		];

		$diff = array_diff_assoc( $event_body, $meeting_body );

		// Nothing to update.
		if ( empty( $diff ) ) {
			return;
		}

		$post_id = $event->ID;

		/**
		 * Filters the contents of the request that will be made to the Webex API to update a meeting link.
		 *
		 * @since 1.9.0
		 *
		 * @param array<string,mixed> The current content of the request body.
		 * @param \WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
		 * @param Meetings $this  The current API handler object instance.
		 */
		$body = apply_filters(
			"tec_events_virtual_meetings_webex_{$this::$meeting_type}_update_request_body",
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
		$this->api->put(
			Api::$api_base . "{$this::$api_endpoint}/{$event->webex_meeting_id}",
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
	 * Processes the Webex API Meeting update response to massage, filter and save the data.
	 *
	 * @since 1.9.0
	 *
	 * @param array<string,mixed> $response The entire Webex API response.
	 * @param \WP_Post            $event    The event post object.
	 * @param int                 $post_id  The event post ID.
	 *
	 * @return array<string,mixed>|false The Webex Meeting data or `false` on error.
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
			Api::$api_base . "{$this::$api_endpoint}/{$event->webex_meeting_id}",
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
				$body_set = $this->api->has_proper_response_body( $body );
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
	 * @since 1.9.0
	 *
	 * @param array<string,mixed> $body The response body, in raw format.
	 *
	 * @return array<string,mixed> The meeting data, massaged and filtered.
	 */
	protected function prepare_meeting_data( $body ) {
		$data = [
			'id'                => $body['id'],
			'join_url'          => $body['webLink'],
			'password'          => $body['password'],
			'host_email'        => $body['hostEmail'],
		];

		/**
		 * Filters the Webex API meeting data after a successful meeting creation.
		 *
		 * @since 1.9.0
		 *
		 * @param array<string,mixed> $data The data that will be returned in the AJAX response.
		 * @param array<string,mixed> $body The raw data returned from the Webex API for the request.
		 */
		$data = apply_filters( "tec_events_virtual_meetings_webex_{$this::$meeting_type}_data", $data, $body );

		return $data;
	}

	/**
	 * Processes the Webex API Meeting connection response.
	 *
	 * @since 1.9.0
	 *
	 * @param array<string,mixed> $response The entire Webex API response.
	 * @param int                 $post_id  The event post ID.
	 *
	 * @return array<string,mixed> The Webex Meeting data.
	 */
	public function process_meeting_connection_response( array $response, $post_id ) {
		return $this->process_meeting_creation_response( $response, $post_id );
	}

	/**
	 * Processes the Webex API Meeting creation response to massage, filter and save the data.
	 *
	 * @since 1.9.0
	 *
	 * @param array<string,mixed> $response The entire Webex API response.
	 * @param int                 $post_id  The event post ID.
	 *
	 * @return array<string,mixed> The Webex Meeting data.
	 */
	protected function process_meeting_creation_response( array $response, $post_id ) {

		$body     = json_decode( wp_remote_retrieve_body( $response ), true );
		$body_set = $this->api->has_proper_response_body( $body, [ 'webLink', 'id' ] );
		if ( ! $body_set ) {
			do_action(
				'tribe_log',
				'error',
				__CLASS__,
				[
					'action'   => __METHOD__,
					'message'  => "Webex API {$this::$meeting_type} creation response is malformed.",
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
	 * @since 1.9.0
	 *
	 * @param int                 $post_id       The post ID of the event to update the Webex Meeting related meta for.
	 * @param array<string,mixed> $response_body The Webex API response body, as received from it.
	 * @param array<string,mixed> $meeting_data  The Webex Meeting data, as returned from the Webex API request.
	 */
	protected function update_post_meta( $post_id, array $response_body, array $meeting_data ) {
		$prefix = Virtual_Events_Meta::$prefix;

		// Cache the raw meeting data for future use.
		update_post_meta( $post_id, $prefix . 'webex_meeting_data', $response_body, true );

		// Set the video source to prevent issues with loading the information later.
		update_post_meta( $post_id, Virtual_Events_Meta::$key_video_source, Webex_Meta::$key_source_id );

		$map = [
			$prefix . 'webex_meeting_id'             => 'id',
			$prefix . 'webex_join_url'               => 'join_url',
			$prefix . 'webex_password'               => 'password',
			$prefix . 'webex_host_email'             => 'host_email',
		];

		foreach ( $map as $meta_key => $data_key ) {
			if ( isset( $meeting_data[ $data_key ] ) ) {
				update_post_meta( $post_id, $meta_key, $meeting_data[ $data_key ] );
			} else {
				delete_post_meta( $post_id, $meta_key );
			}
		}

		// Add the meeting type, it's not part of the data coming from Webex.
		update_post_meta( $post_id, $prefix . 'webex_meeting_type', static::$meeting_type );
	}

	/**
	 * Format the event date for Webex.
	 *
	 * @since 1.9.0
	 *
	 * @param string $date The start date of the event.
	 * @param string $time The start time of the event.
	 * @param string $time_zone  The timezone of the event.
	 *
	 * @return string The time formatted for Webex using 'Y-m-d\TH:i:sO'.
	 */
	public function format_date_for_webex( $date, $time, $time_zone ) {
		// Utilize the datepicker format when parse the Event Date to prevent the wrong date in Webex.
		$datepicker_format = Dates::datepicker_formats( tribe_get_option( 'datepickerFormat' ) );
		$date_time   = Dates::datetime_from_format( $datepicker_format, $date ) . ' ' . $time;

		return Dates::build_date_object( $date_time, $time_zone )->format( 'c' );
	}
}
