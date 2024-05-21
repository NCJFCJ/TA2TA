<?php
/**
 * Implements the methods shared for Microsoft API.
 *
 * @since 1.13.0
 *
 * @package Tribe\Events\Virtual\Meetings\Microsoft
 */

namespace Tribe\Events\Virtual\Meetings\Microsoft;

use Tribe\Events\Virtual\Event_Meta as Virtual_Events_Meta;
use Tribe\Events\Virtual\Meetings\Microsoft\Event_Meta as Microsoft_Meta;
use Tribe\Events\Virtual\Traits\With_AJAX;
use Tribe__Date_Utils as Dates;
use Tribe__Utils__Array as Arr;

/**
 * Class Abstract_Meetings
 *
 * @since 1.13.0
 *
 * @package Tribe\Events\Virtual\Meetings\Microsoft
 */
abstract class Abstract_Meetings {
	use With_AJAX;

	/**
	 * The type of the meeting handled by the class instance.
	 * Defaults to the Meetings one.
	 *
	 * @since 1.13.0
	 *
	 * @var string
	 */
	public static $meeting_type = 'meeting';

	/**
	 * Regex to get the Microsoft Team ID.
	 *
	 * @since 1.13.0
	 *
	 * @var string
	 */
	protected $regex_team_meeting_id = '~((Meeting ID:\s)(?<id>([0-9\s]*)))~';

	/**
	 * An instance of the Microsoft API handler.
	 *
	 * @since 1.13.0
	 *
	 * @var Api
	 */
	protected $api;

	/**
	 * The Classic Editor rendering handler.
	 *
	 * @since 1.13.0
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
	protected $actions;

	/**
	 * Meetings constructor.
	 *
	 * @since 1.13.0
	 *
	 * @param Api            $api            An instance of the Microsoft API handler.
	 * @param Classic_Editor $classic_editor An instance of the Classic Editor rendering handler.
	 * @param Actions        $actions        An instance of the Actions name handler.
	 */
	public function __construct( Api $api, Classic_Editor $classic_editor, Actions $actions ) {
		$this->api            = $api;
		$this->classic_editor = $classic_editor;
		$this->actions        = $actions;
	}

	/**
	 * Filter the autodetect source to detect if a Microsoft link.
	 *
	 * @since 1.13.0
	 *
	 * @param array<string|mixed> $autodetect   An array of the autodetect defaults.
	 * @param string              $video_url    The url to use to autodetect the video source.
	 * @param string              $video_source The optional name of the video source to attempt to autodetect.
	 * @param \WP_Post|null       $event        The event post object, as decorated by the `tribe_get_event` function.
	 * @param array<string|mixed> $ajax_data    An array of extra values that were sent by the ajax script.
	 *
	 * @return array<string|mixed> An array of the autodetect results.
	 */
	abstract public function filter_virtual_autodetect_microsoft( $autodetect, $video_url, $video_source, $event, $ajax_data );

	/**
	 * Handles the request to generate a Microsoft Meet.
	 *
	 * @since 1.13.0
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
			$error_message = _x( 'The Microsoft Host Email to access the API is missing.', 'Microsoft Host Email is missing error message.', 'events-virtual' );
			$this->classic_editor->render_meeting_generation_error_details( $event, $error_message, true );

			wp_die();
		}

		// Meeting provider options: teamsForBusiness, skypeForBusiness, and skypeForConsumer.
		$meeting_type = tribe_get_request_var( 'meeting_type' );
		// If meeting type checked, fail the request.
		if ( empty( $meeting_type ) ) {
			$error_message = _x( 'No Microsoft meeting provider selected, please choose an available meeting provider.', 'The no Microsoft meeting provider is selected error message.', 'events-virtual' );
			$this->classic_editor->render_meeting_generation_error_details( $event, $error_message, true );

			wp_die();
		}

		// Load the account.
		$account_id = tribe_get_request_var( 'account_id' );
		// if no id, fail the request.
		if ( empty( $account_id ) ) {
			$error_message = _x( 'The Microsoft Account ID to access the API is missing.', 'Account ID is missing error message.', 'events-virtual' );
			$this->classic_editor->render_meeting_generation_error_details( $event, $error_message, true );

			wp_die();
		}

		$this->api->load_account_by_id( $account_id );
		// If there is no token, then stop as the connection will fail.
		if ( ! $this->api->get_token_authorization_header() ) {
			$error_message = _x( 'The Microsoft Account to access to API could not be loaded.', 'Microsoft account loading error message.', 'events-virtual' );
			$this->classic_editor->render_meeting_generation_error_details( $event, $error_message, true );

			wp_die();
		}

		$post_id = $event->ID;
		$cached  = get_post_meta( $post_id, Virtual_Events_Meta::$prefix . 'microsoft_meeting_data', true );

		/**
		 * Filters whether to force the recreation of the Microsoft Meets link on each request or not.
		 *
		 * If the filters returns a truthy value, then each request, even for events that already had a Microsoft Meet
		 * generated, will generate a new link, without re-using the previous one.
		 *
		 * @since 1.13.0
		 *
		 * @param bool $force   Whether to force the regeneration of Microsoft Meet links or not.
		 * @param int  $post_id The post ID of the event the Meeting is being generated for.
		 */
		$force = apply_filters(
			"tec_events_virtual_meetings_microsoft_{$this::$meeting_type}_force_recreate",
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

		$start_datetime = $this->format_date_for_microsoft( $start_date, $start_time, $time_zone, $all_day );
		$end_datetime   = $this->format_date_for_microsoft( $end_date, $end_time, $time_zone, $all_day );

		if ( $start_datetime === $end_datetime && $all_day ) {
			$end_datetime   = $this->format_date_for_microsoft( $end_date, $end_time, $time_zone, $all_day, true );
		}

		$body = [
			'subject'               => $event->post_title,
			'start'                 => $start_datetime,
			'end'                   => $end_datetime,
			'isOnlineMeeting'       => true,
			'onlineMeetingProvider' => $meeting_type
		];

		// Include isAllDay if is set.
		if( $all_day ) {
			$body['isAllDay'] = true;
		}

		/**
		 * Filters the contents of the request that will be made to the Microsoft API to generate a meeting link.
		 *
		 * @since 1.13.0
		 *
		 * @param array<string,mixed> The current content of the request body.
		 * @param \WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
		 * @param Meetings $this  The current API handler object instance.
		 */
		$body = apply_filters(
			"tec_events_virtual_meetings_microsoft_{$this::$meeting_type}_request_body",
			$body,
			$event,
			$this
		);

		$success = false;

		$this->api->post(
			Api::$api_base . "me/events",
			[
				'headers' => [
					'authorization' => $this->api->get_token_authorization_header(),
					'content-type'  => 'application/json',
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
	 * Handles the AJAX request to remove the Microsoft Meet information from an event.
	 *
	 * @since 1.13.0
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
		Microsoft_Meta::delete_meeting_meta( $event->ID );

		// Send the HTML for the meeting creation.
		$this->classic_editor->render_initial_setup_options( $event, true );

		wp_die();
	}

	/**
	 * Handles update of Microsoft Meet when Event details change.
	 *
	 * @since 1.13.0
	 *
	 * @param \WP_Post|int $event The event (or event ID) we're updating the meeting for.
	 */
	public function update( $event ) {
		// Get event if not an object.
		if ( ! ( $event instanceof \WP_Post ) ) {
			$event = tribe_get_event( $event );
		}

		// There is no meeting to update.
		if ( ! ( $event instanceof \WP_Post ) || empty( $event->microsoft_meeting_id ) ) {
			return;
		}

		// If manually connected, do not update Microsoft Meet when event details change.
		$manual_connected = get_post_meta( $event->ID, Virtual_Events_Meta::$key_autodetect_source, true );
		if ( Microsoft_Meta::$key_source_id === $manual_connected ) {
			return;
		}

		$start_date = tribe_get_request_var( 'EventStartDate', $event->start_date );
		$start_time = tribe_get_request_var( 'EventStartTime', $event->start_time );
		$time_zone  = tribe_get_request_var( 'EventTimezone', $event->timezone );
		$end_date   = tribe_get_request_var( 'EventEndDate', $event->end_date );
		$end_time   = tribe_get_request_var( 'EventEndTime', $event->end_time );
		$all_day    = tribe_get_request_var( 'allDayCheckbox', $event->all_day );

		$start_datetime = $this->format_date_for_microsoft( $start_date, $start_time, $time_zone, $all_day );
		$end_datetime   = $this->format_date_for_microsoft( $end_date, $end_time, $time_zone, $all_day );

		if ( $start_datetime === $end_datetime && $all_day ) {
			$end_datetime   = $this->format_date_for_microsoft( $end_date, $end_time, $time_zone, $all_day, true );
		}

		$event_body = [
			'subject' => $event->post_title,
			'start'   => $start_datetime,
			'end'     => $end_datetime,
		];

		$meeting_data = get_post_meta( $event->ID, Virtual_Events_Meta::$prefix . 'microsoft_meeting_data', true );
		$meeting_body = [
			'subject' => $meeting_data['subject'],
			'start'   => str_replace( '.0000000', '', $meeting_data['start'] ),
			'end'     => str_replace( '.0000000', '', $meeting_data['end'] ),
		];

		$diff_summary = $event_body['subject'] !== $meeting_body['subject'];
		$diff_start   = array_diff_assoc( $event_body['start'], $meeting_body['start'] );
		$diff_end     = array_diff_assoc( $event_body['end'], $meeting_body['end'] );

		// Nothing to update.
		if ( empty( $diff_summary ) && empty( $diff_start ) && empty( $diff_end ) ) {
			return;
		}

		$post_id = $event->ID;

		/**
		 * Filters the contents of the request that will be made to the Microsoft API to update a meeting link.
		 *
		 * @since 1.13.0
		 *
		 * @param array<string,mixed> The current content of the request body.
		 * @param \WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
		 * @param Meetings $this  The current API handler object instance.
		 */
		$body = apply_filters(
			"tec_events_virtual_meetings_microsoft_{$this::$meeting_type}_update_request_body",
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
			Api::$api_base . "me/events/{$event->microsoft_meeting_id}",
			[
				'headers' => [
					'Authorization' => $this->api->get_token_authorization_header(),
					'Content-Type'  => 'application/json',
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
	 * Processes the Microsoft API Meeting update response to massage, filter and save the data.
	 *
	 * @since 1.13.0
	 *
	 * @param array<string,mixed> $response The entire Microsoft API response.
	 * @param \WP_Post            $event    The event post object.
	 * @param int                 $post_id  The event post ID.
	 *
	 * @return array<string,mixed>|false The Microsoft Meet data or `false` on error.
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
			Api::$api_base . "/me/events/{$event->microsoft_meeting_id}",
			[
				'headers' => [
					'Authorization' => $this->api->get_token_authorization_header(),
					'Content-Type'  => 'application/json',
				],
			],
			Api::GET_RESPONSE_CODE
		)->then(
			function ( array $response ) use ( $post_id, &$success ) {
				$body     = json_decode( wp_remote_retrieve_body( $response ), true );
				$body_set = $this->api->has_proper_response_body( $body );

				// If the response is empty, then do not update the post.
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
	 * Get the regex to get the Microsoft Team ID from event content.
	 *
	 * @since 1.13.0
	 *
	 * @return string The regex to get Microsoft Team ID from event content, from the filter if a string or the default property.
	 */
	public function get_regex_microsoft_team_meeting_id() {
		/**
		 * Allow filtering of the regex to get Microsoft Team ID.
		 *
		 * @since 1.13.0
		 *
		 * @param string The regex to verify a Microsoft Team ID.
		 */
		$regex_team_meeting_id = apply_filters( 'tec_events_virtual_microsoft_regex_team_meeting_id', $this->regex_team_meeting_id );

		return is_string( $regex_team_meeting_id ) ? $regex_team_meeting_id : $this->regex_team_meeting_id;
	}

	/**
	 * Filters and massages the meeting data to prepare it to be saved in the post meta.
	 *
	 * @since 1.13.0
	 *
	 * @param array<string,mixed> $body The response body, in raw format.
	 *
	 * @return array<string,mixed> The meeting data, massaged and filtered.
	 */
	protected function prepare_meeting_data( $body ) {
		$event_body              = Arr::get( $body, 'body', [] );
		$event_content           = Arr::get( $event_body, 'content', '' );
		$online_meeting          = Arr::get( $body, 'onlineMeeting', [] );
		$online_meeting_provider = Arr::get( $body, 'onlineMeetingProvider', '' );
		$organizer               = Arr::get( $body, 'organizer', [] );
		$email_address           = Arr::get( $organizer, 'emailAddress', [] );
		$weblink                 = Arr::get( $body, 'webLink', '' );
		$conference_id           = str_replace( 'https://join.skype.com/', '', Arr::get( $online_meeting, 'joinUrl', '' ) );

		if ( $online_meeting_provider === 'teamsForBusiness' ) {
			preg_match( $this->get_regex_microsoft_team_meeting_id(), strip_tags( $event_content, '<div><br><a><head><body>' ), $matches );
			if ( isset( $matches['id'] ) ) {
				$conference_id = $matches['id'];
			}
		}

		$data = [
			'id'            => Arr::get( $body, 'id', '' ),
			'join_url'      => Arr::get( $online_meeting, 'joinUrl', $weblink ),
			'provider'      => $online_meeting_provider,
			'conference_id' => $conference_id,
			'host_email'    => Arr::get( $email_address, 'address', '' ),
		];

		/**
		 * Filters the Microsoft API meeting data after a successful meeting creation.
		 *
		 * @since 1.13.0
		 *
		 * @param array<string,mixed> $data The data that will be returned in the AJAX response.
		 * @param array<string,mixed> $body The raw data returned from the Microsoft API for the request.
		 */
		$data = apply_filters( "tec_events_virtual_meetings_microsoft_{$this::$meeting_type}_data", $data, $body );

		return $data;
	}

	/**
	 * Processes the Microsoft API Meeting connection response.
	 *
	 * @since 1.13.0
	 *
	 * @param array<string,mixed> $response The entire Microsoft API response.
	 * @param int                 $post_id  The event post ID.
	 *
	 * @return array<string,mixed> The Microsoft Meet data.
	 */
	public function process_meeting_connection_response( array $response, $post_id, $event, $account_id ) {
		return $this->process_meeting_creation_response( $response, $post_id, $event, $account_id );
	}

	/**
	 * Processes the Microsoft API Meeting creation response to massage, filter and save the data.
	 *
	 * @since 1.13.0
	 *
	 * @param array<string,mixed> $response The entire Microsoft API response.
	 * @param int                 $post_id  The event post ID.
	 *
	 * @return array<string,mixed> The Microsoft Meet data.
	 */
	protected function process_meeting_creation_response( array $response, $post_id, $event = '', $account_id = '' ) {
		$body     = json_decode( wp_remote_retrieve_body( $response ), true );
		$body_set = $this->api->has_proper_response_body( $body, ['webLink'] );

		if ( ! $body_set ) {
			do_action(
				'tribe_log',
				'error',
				__CLASS__,
				[
					'action'   => __METHOD__,
					'message'  => "Microsoft API {$this::$meeting_type} creation response is malformed.",
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
	 * @since 1.13.0
	 *
	 * @param int                 $post_id       The post ID of the event to update the Microsoft Meet related meta for.
	 * @param array<string,mixed> $response_body The Microsoft API response body, as received from it.
	 * @param array<string,mixed> $meeting_data  The Microsoft Meet data, as returned from the Microsoft API request.
	 */
	protected function update_post_meta( $post_id, array $response_body, array $meeting_data ) {
		$prefix = Virtual_Events_Meta::$prefix;

		// Cache the raw meeting data for future use.
		update_post_meta( $post_id, $prefix . 'microsoft_meeting_data', $response_body, true );

		// Set the video source to prevent issues with loading the information later.
		update_post_meta( $post_id, Virtual_Events_Meta::$key_video_source, Microsoft_Meta::$key_source_id );

		$map = [
			$prefix . 'microsoft_meeting_id'    => 'id',
			$prefix . 'microsoft_join_url'      => 'join_url',
			$prefix . 'microsoft_host_email'    => 'host_email',
			$prefix . 'microsoft_provider'      => 'provider',
			$prefix . 'microsoft_conference_id' => 'conference_id',
		];

		foreach ( $map as $meta_key => $data_key ) {
			if ( isset( $meeting_data[ $data_key ] ) ) {
				update_post_meta( $post_id, $meta_key, $meeting_data[ $data_key ] );
			} else {
				delete_post_meta( $post_id, $meta_key );
			}
		}

		// Add the meeting type, it's not part of the data coming from Microsoft.
		update_post_meta( $post_id, $prefix . 'microsoft_meeting_type', static::$meeting_type );
	}

	/**
	 * Format the event date for Microsoft.
	 *
	 * @since 1.13.0
	 *
	 * @param string  $date      The start date of the event.
	 * @param string  $time      The start time of the event.
	 * @param string  $time_zone The timezone of the event.
	 * @param boolean $all_day   Whether an event is all day.
	 * @param boolean $tomorrow  Whether to add a day to the datetime, used for all day events to get midnight to midnight
	 *
	 * @return array<string|string> An array with dateTime and timezone formatted for Microsoft using DateTime::RFC3339 - 'Y-m-d\TH:i:sP'.
	 */
	public function format_date_for_microsoft( $date, $time, $time_zone, $all_day = false, $tomorrow = false ) {
		// Utilize the datepicker format when parse the Event Date to prevent the wrong date in Microsoft.
		$datepicker_format = Dates::datepicker_formats( tribe_get_option( 'datepickerFormat' ) );
		$date_time         = Dates::datetime_from_format( $datepicker_format, $date ) . ' ' . $time;
		$date              = Dates::build_date_object( $date_time, $time_zone )->format( 'Y-m-d\TH:i:s' );

		// Support all day events as the Microsoft API expects the end date to be midnight of the next day.
		if ( $all_day && $tomorrow ) {
			$date = Dates::build_date_object( $date_time, $time_zone )->add( new \DateInterval( 'P1D' ) )->format( 'Y-m-d' );
		} else if ( $all_day ) {
			$date = Dates::build_date_object( $date_time, $time_zone )->format( 'Y-m-d' );
		}

		return [
			'dateTime' => $date,
			'timeZone' => $time_zone,
		];
	}
}
