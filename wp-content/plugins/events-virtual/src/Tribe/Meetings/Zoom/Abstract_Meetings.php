<?php
/**
 * Implements the methods shared by Meetings and Webinars.
 *
 * @since   1.1.1
 *
 * @package Tribe\Events\Virtual\Meetings\Zoom
 */

namespace Tribe\Events\Virtual\Meetings\Zoom;

use Tribe\Events\Virtual\Encryption;
use Tribe\Events\Virtual\Event_Meta as Virtual_Events_Meta;
use Tribe\Events\Virtual\Meetings\Zoom\Event_Meta as Zoom_Meta;
use Tribe\Events\Virtual\Traits\With_AJAX;
use Tribe__Date_Utils as Dates;
use Tribe__Timezones as Timezones;

/**
 * Class Abstract_Meetings
 *
 * @since   1.1.1
 *
 * @package Tribe\Events\Virtual\Meetings\Zoom
 */
class Abstract_Meetings {
	use With_AJAX;

	/**
	 * The  integer value associated to the Scheduled meeting type.
	 *
	 * @since 1.0.0
	 * @since 1.1.1 Moved to the Tribe\Events\Virtual\Meetings\Zoom\Abstract_Meetings class.
	 *
	 * @link  https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingcreate
	 */
	const TYPE_MEETING_SCHEDULED = 2;

	/**
	 * The name of the action used to generate a meeting creation link.
	 * The property also provides a reasonable default for the abstract class.
	 *
	 * @since 1.0.0
	 * @since 1.1.1 Moved to the Tribe\Events\Virtual\Meetings\Zoom\Abstract_Meetings class.
	 * @deprecated 1.13.0 - Use Actions::$create_action.
	 *
	 * @var string
	 */
	public static $create_action = 'events-virtual-meetings-zoom-meeting-create';

	/**
	 * The name of the action used to update a meeting.
	 * The property also provides a reasonable default for the abstract class.
	 *
	 * @since 1.4.0
	 * @deprecated 1.13.0 - No replacement.
	 *
	 * @var string
	 */
	public static $update_action = 'events-virtual-meetings-zoom-meeting-update';

	/**
	 * The name of the action used to remove a meeting creation link.
	 * The property also provides a reasonable default for the abstract class.
	 *
	 * @since 1.0.0
	 * @since 1.1.1 Moved to the Tribe\Events\Virtual\Meetings\Zoom\Abstract_Meetings class.
	 * @deprecated 1.13.0 - Use Actions::$remove_action.
	 *
	 * @var string
	 */
	public static $remove_action = 'events-virtual-meetings-zoom-meeting-remove';

	/**
	 * The type of the meeting handled by the class instance.
	 * Defaults to the Meetings one.
	 *
	 * @since 1.1.1
	 *
	 * @var string
	 */
	public static $meeting_type = 'meeting';

	/**
	 * The Zoom API endpoint used to create and manage the meeting.
	 * Defaults to the one used for Meetings.
	 *
	 * @since 1.1.1
	 *
	 * @var string
	 */
	public static $api_endpoint = 'meetings';

	/**
	 * The URL that will contain the meeting join instructions.
	 * Defaults to the one used for Meetings.
	 *
	 * @since 1.1.1
	 *
	 * @var string
	 */
	protected static $join_instructions_url = 'https://support.zoom.us/hc/en-us/articles/201362193-Joining-a-Meeting';

	/**
	 * An instance of the Zoom API handler.
	 *
	 * @since 1.0.0
	 * @since 1.1.1 Moved to the Tribe\Events\Virtual\Meetings\Zoom\Abstract_Meetings class.
	 *
	 * @var Api
	 */
	protected $api;

	/**
	 * The Classic Editor rendering handler.
	 *
	 * @since 1.0.0
	 * @since 1.1.1 Moved to the Tribe\Events\Virtual\Meetings\Zoom\Abstract_Meetings class.
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
	 * The Password handler.
	 *
	 * @since 1.0.2
	 *
	 * @var Password
	 */
	protected $password;

	/**
	 * The Encryption handler.
	 *
	 * @since 1.4.0
	 *
	 * @var Encryption
	 */
	public $encryption;

	/**
	 * Meetings constructor.
	 *
	 * @since 1.0.0
	 * @since 1.0.2 - Add the Password Class.
	 * @since 1.1.1 Moved to the Tribe\Events\Virtual\Meetings\Zoom\Abstract_Meetings class.
	 * @since 1.4.0 - Add Encryption Class.
	 * @since 1.4.0 - Add Encryption Class.
	 * @since 1.13.0 - Add the Action class.
	 *
	 * @param Api            $api            An instance of the Zoom API handler.
	 * @param Classic_Editor $classic_editor An instance of the Classic Editor rendering handler.
	 * @param Password       $password       An instance of the Password handler.
	 * @param Encryption     $encryption     An instance of the Encryption handler.
	 * @param Actions        $actions        An instance of the Actions name handler.
	 */
	public function __construct( Api $api, Classic_Editor $classic_editor, Password $password, Encryption $encryption = NULL, Actions $actions = NULL ) {
		$this->api            = $api;
		$this->classic_editor = $classic_editor;
		$this->password       = $password;
		$this->encryption     = ( ! empty( $encryption ) ? $encryption : tribe( Encryption::class ) );
		$this->actions        = $actions;
	}

	/**
	 * Handles the request to generate a Zoom meeting.
	 *
	 * @since 1.0.0
	 * @since 1.1.1 Moved to the Tribe\Events\Virtual\Meetings\Zoom\Abstract_Meetings class.
	 *
	 * @param string|null $nonce The nonce that should accompany the request.
	 *
	 * @return bool Whether the request was handled or not.
	 */
	public function ajax_create( $nonce = null ) {
		$action = $this->actions::$create_action;
		if ( static::$meeting_type === 'webinar' ) {
			$action = $this->actions::$webinar_create_action;
		}
		if ( ! $this->check_ajax_nonce( $action, $nonce ) ) {
			return false;
		}

		$event = $this->check_ajax_post();

		if ( ! $event ) {
			return false;
		}

		$zoom_host_id = tribe_get_request_var( 'host_id' );
		// If no host id found, fail the request as account level apps do not support 'me'
		if ( empty( $zoom_host_id ) ) {
			$error_message = _x( 'The Zoom Host ID is missing to access the API.', 'Host ID is missing error message.', 'events-virtual' );
			$this->classic_editor->render_meeting_generation_error_details( $event, $error_message, true );

			wp_die();

			return false;
		}

		// Load the account.
		$zoom_account_id = tribe_get_request_var( 'account_id' );
		// if no id, fail the request.
		if ( empty( $zoom_account_id ) ) {
			$error_message = _x( 'The Zoom Account ID is missing to access the API.', 'Account ID is missing error message.', 'events-virtual' );
			$this->classic_editor->render_meeting_generation_error_details( $event, $error_message, true );

			wp_die();

			return false;
		}

		$this->api->load_account_by_id( $zoom_account_id );
		// If there is no token, then stop as the connection will fail.
		if ( ! $this->api->get_token_authorization_header() ) {
			$error_message = _x( 'The Zoom Account could not be loaded to access to API.', 'Zoom account loading error message.', 'events-virtual' );
			$this->classic_editor->render_meeting_generation_error_details( $event, $error_message, true );

			wp_die();

			return false;
		}

		$post_id = $event->ID;
		$cached  = $this->encryption->decrypt( get_post_meta( $post_id, Virtual_Events_Meta::$prefix . 'zoom_meeting_data', true ), true );

		/**
		 * Filters whether to force the recreation of the Zoom meetings link on each request or not.
		 *
		 * If the filters returns a truthy value, then each request, even for events that already had a Zoom meeting
		 * generated, will generate a new link, without re-using the previous one.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $force   Whether to force the regeneration of Zoom Meeting links or not.
		 * @param int  $post_id The post ID of the event the Meeting is being generated for.
		 */
		$force = apply_filters(
			"tribe_events_virtual_meetings_zoom_{$this::$meeting_type}_force_recreate",
			true,
			$post_id
		);

		if ( ! $force && ! empty( $cached ) ) {
			$this->classic_editor->render_meeting_link_generator( $event, true, false, $zoom_account_id  );

			wp_die();

			return true;
		}

		$password_requirements = tribe_get_request_var( 'password_requirements', [] );

		// Get the password requirements for Meetings.
		$password_requirements = $this->password->get_password_requirements( $password_requirements );

		/**
		 * Filters the password for the Zoom Meeting.
		 *
		 * @since 1.0.2
		 *
		 * @param null|string|int   The password for the Zoom Meeting.
		 * @param array    $password_requirements An array of password requirements from Zoom.
		 * @param \WP_Post $event                 The event post object, as decorated by the `tribe_get_event` function.
		 */
		$password = apply_filters(
			"tribe_events_virtual_meetings_zoom_{$this::$meeting_type}_password",
			null,
			$password_requirements,
			$event
		);

		/**
		 * If this is a new post, then the duration will not be available.
		 * Since meetings that have a duration of 0 will not be editable after their creation,
		 * let's ensure a default 60 minutes duration to come back and edit the meeting later.
		 */
		$duration = (int) ceil( (int) $event->duration / 60 );
		$duration = $duration ? (int) $duration : 60;

		$body = [
			'topic'      => $event->post_title,
			'type'       => self::TYPE_MEETING_SCHEDULED,
			'start_time' => $event->dates->start->format( 'Y-m-d\TH:i:s' ),
			'timezone'   => $event->timezone,
			'duration'   => $duration,
			'password'   => $password,
		];

		/**
		 * Filters the contents of the request that will be made to the Zoom API to generate a meeting link.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string,mixed> The current content of the request body.
		 * @param \WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
		 * @param Meetings $this  The current API handler object instance.
		 */
		$body = apply_filters(
			"tribe_events_virtual_meetings_zoom_{$this::$meeting_type}_request_body",
			$body,
			$event,
			$this
		);

		$success = false;

		$this->api->post(
			Api::$api_base . "users/{$zoom_host_id}/{$this::$api_endpoint}",
			[
				'headers' => [
					'authorization' => $this->api->get_token_authorization_header(),
					'content-type'  => 'application/json; charset=utf-8',
					'accept'        => 'application/json;',
				],
				'body'    => wp_json_encode( $body ),
			],
			Api::POST_RESPONSE_CODE
		)->then(
			function ( array $response ) use ( $post_id, &$success, &$zoom_account_id ) {
				$this->process_meeting_creation_response( $response, $post_id );

				$event = tribe_get_event( $post_id, OBJECT, 'raw', true );
				$this->classic_editor->render_meeting_link_generator( $event, true, false, $zoom_account_id  );

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
	 * Processes the Zoom API Meeting connection response.
	 *
	 * @since 1.8.0
	 *
	 * @param array<string,mixed> $response The entire Zoom API response.
	 * @param int                 $post_id  The event post ID.
	 *
	 * @return array<string,mixed> The Zoom Meeting data.
	 */
	public function process_meeting_connection_response( array $response, $post_id ) {
		return $this->process_meeting_creation_response( $response, $post_id );
	}

	/**
	 * Processes the Zoom API Meeting creation response to massage, filter and save the data.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string,mixed> $response The entire Zoom API response.
	 * @param int                 $post_id  The event post ID.
	 *
	 * @return array<string,mixed> The Zoom Meeting data.
	 */
	protected function process_meeting_creation_response( array $response, $post_id ) {

		$body     = json_decode( wp_remote_retrieve_body( $response ), true );
		$body_set = $this->api->has_proper_response_body( $body, [ 'join_url', 'id' ] );
		if ( ! $body_set ) {
			do_action(
				'tribe_log',
				'error',
				__CLASS__,
				[
					'action'   => __METHOD__,
					'message'  => "Zoom API {$this::$meeting_type} creation response is malformed.",
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
	 * Filters and massages the meeting data to prepare it to be saved in the post meta.
	 *
	 * @since 1.0.0
	 * @since 1.0.2 - Added Password and Password Hash to $data.
	 *
	 * @param array<string,mixed> $body The response body, in raw format.
	 *
	 * @return array<string,mixed> The meeting data, massaged and filtered.
	 */
	protected function prepare_meeting_data( $body ) {

		$hash_pwd = tribe( Password::class )->get_hash_pwd_from_join_url( $body['join_url'] );

		$data = [
			'id'                => $body['id'],
			'join_url'          => $body['join_url'],
			'join_instructions' => static::$join_instructions_url,
			'password_hash'     => $hash_pwd,
			'password'          => $body['password'],
			'host_email'        => $body['host_email'],
			'alternative_hosts' => $body['settings']['alternative_hosts'],
		];

		// Dial-in numbers are NOT a given and should not be assumed.
		if ( ! empty( $body['settings']['global_dial_in_numbers'] ) ) {
			// If there are dial-in numbers, there might be more than one.
			$dial_in_data                        = (array) $body['settings']['global_dial_in_numbers'];
			$data['global_dial_in_numbers_data'] = $dial_in_data;
			$data['global_dial_in_numbers']      = array_combine(
				array_column( $dial_in_data, 'number' ),
				array_column( $dial_in_data, 'country' )
			);
		}

		/**
		 * Filters the Zoom API meeting data after a successful meeting creation.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string,mixed> $data The data that will be returned in the AJAX response.
		 * @param array<string,mixed> $body The raw data returned from the Zoom API for the request.
		 */
		$data = apply_filters( "tribe_events_virtual_meetings_zoom_{$this::$meeting_type}_data", $data, $body );

		return $data;
	}

	/**
	 * Updates the event post meta depending on the meeting data provided.
	 *
	 * @since 1.0.0
	 *
	 * @param int                 $post_id       The post ID of the event to update the Zoom Meeting related meta for.
	 * @param array<string,mixed> $response_body The Zoom API response body, as received from it.
	 * @param array<string,mixed> $meeting_data  The Zoom Meeting data, as returned from the Zoom API request.
	 */
	protected function update_post_meta( $post_id, array $response_body, array $meeting_data ) {
		$prefix = Virtual_Events_Meta::$prefix;

		// Cache the raw meeting data for future use.
		update_post_meta( $post_id, $prefix . 'zoom_meeting_data', $this->encryption->encrypt( $response_body, true ) );

		// Set the video source to prevent issues with loading the information later.
		update_post_meta( $post_id, Virtual_Events_Meta::$key_video_source, Zoom_Meta::$key_source_id );

		$map = [
			$prefix . 'zoom_meeting_id'             => 'id',
			$prefix . 'zoom_join_url'               => 'join_url',
			$prefix . 'zoom_join_instructions'      => 'join_instructions',
			$prefix . 'zoom_global_dial_in_numbers' => 'global_dial_in_numbers',
			$prefix . 'zoom_password_hash'          => 'password_hash',
			$prefix . 'zoom_password'               => 'password',
			$prefix . 'zoom_host_email'             => 'host_email',
			$prefix . 'zoom_alternative_hosts'      => 'alternative_hosts',
		];

		$encrypted_fields = Zoom_Meta::$encrypted_fields;

		foreach ( $map as $meta_key => $data_key ) {
			if ( isset( $meeting_data[ $data_key ] ) ) {
				// Encrypt data that matches the encrypted fields.
				if ( isset( $encrypted_fields[$data_key] ) ) {
					update_post_meta( $post_id, $meta_key, $this->encryption->encrypt( $meeting_data[ $data_key ], $encrypted_fields[$data_key] ) );
					continue;
				}
				update_post_meta( $post_id, $meta_key, $meeting_data[ $data_key ] );
			} else {
				delete_post_meta( $post_id, $meta_key );
			}
		}

		// Add the meeting type, it's not part of the data coming from Zoom.
		update_post_meta( $post_id, $prefix . 'zoom_meeting_type', static::$meeting_type );
	}

	/**
	 * Handles the AJAX request to remove the Zoom Meeting information from an event.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null $nonce The nonce that should accompany the request.
	 *
	 * @return bool|string Whether the request was handled or a string with html for meeting creation.
	 */
	public function ajax_remove( $nonce = null ) {
		$action = $this->actions::$remove_action;
		if ( static::$meeting_type === 'webinar' ) {
			$action = $this->actions::$webinar_remove_action;
		}
		if ( ! $this->check_ajax_nonce( $action, $nonce ) ) {
			return false;
		}

		if ( ! $this->check_ajax_nonce( $action, $nonce ) ) {
			return false;
		}

		// phpcs:ignore
		if ( ! $event = $this->check_ajax_post() ) {
			return false;
		}

		// Remove the meta, but not the data.
		Zoom_Meta::delete_meeting_meta( $event->ID );

		// Send the HTML for the meeting creation.
		$this->classic_editor->render_initial_setup_options( $event, true );

		wp_die();

		return true;
	}

	/**
	 * Handles update of Zoom meeting when Event details change.
	 *
	 * @since 1.0.2
	 * @since 1.2.0 Utilize the datepicker format when parse the Event Date to prevent the wrong date in Zoom.
	 *
	 * @param \WP_Post|int $event The event (or event ID) we're updating the meeting for.
	 */
	public function update( $event ) {
		// Get event if not an object.
		if ( ! ( $event instanceof \WP_Post ) ) {
			$event = tribe_get_event( $event );
		}

		// There is no meeting to update.
		if ( ! ( $event instanceof \WP_Post ) || empty( $event->zoom_meeting_id ) ) {
			return;
		}

		// If manually connected, do not update Zoom meeting or webinar when event details change.
		$manual_connected = get_post_meta( $event->ID, Virtual_Events_Meta::$key_autodetect_source, true );
		if ( Zoom_Meta::$key_source_id === $manual_connected ) {
			return;
		}

		$start_date = tribe_get_request_var( 'EventStartDate', $event->start_date );
		$start_time = tribe_get_request_var( 'EventStartTime', $event->start_time );
		$time_zone  = tribe_get_request_var( 'EventTimezone', $event->timezone );
		$end_date   = tribe_get_request_var( 'EventEndDate', $event->end_date );
		$end_time   = tribe_get_request_var( 'EventEndTime', $event->end_time );

		// Get the duration of the event from the field values instead of the event object, which has previously saved values.
		$duration = $this->calculate_duration( $start_date, $start_time, $end_date, $end_time, $time_zone );
		if ( empty( $duration ) ) {
			$duration = $event->duration;
		}

		$zoom_date         = $this->format_date_for_zoom( $start_date, $start_time, $time_zone );
		$alternative_hosts = (array) tribe_get_request_var( 'tribe-events-virtual-zoom-alt-host' );

		// Note the time format - because Zoom stores all dates as UTC with the trailing 'Z'.
		$event_body = [
			'topic'             => $event->post_title,
			'start_time'        => $zoom_date,
			'timezone'          => $time_zone,
			'duration'          => (int) ceil( (int) $duration / 60 ),
			'alternative_hosts' => esc_attr( implode( ";", $alternative_hosts ) ),
		];

		$meeting_data = $this->encryption->decrypt( get_post_meta( $event->ID, Virtual_Events_Meta::$prefix . 'zoom_meeting_data', true ), true );
		if ( ! is_array( $meeting_data ) || empty( $meeting_data ) ) {
			return;
		}

		$meeting_body = [
			'topic'             => $meeting_data['topic'] ?? null,
			'start_time'        => $meeting_data['start_time'] ?? null,
			'timezone'          => $meeting_data['timezone'] ?? null,
			'duration'          => $meeting_data['duration'] ?? null,
			'alternative_hosts' => $meeting_data['settings']['alternative_hosts'] ?? null,
		];

		$diff = array_diff_assoc( $event_body, $meeting_body );

		// Nothing to update.
		if ( empty( $diff ) ) {
			return;
		}

		$post_id = $event->ID;

		// Unset the alternative hosts and set in expected location for the API.
		unset( $event_body['alternative_hosts'] );
		$event_body['settings']['alternative_hosts'] = esc_attr( implode( ";", $alternative_hosts ) );

		/**
		 * Filters the contents of the request that will be made to the Zoom API to update a meeting link.
		 *
		 * @since 1.0.2
		 *
		 * @param array<string,mixed> The current content of the request body.
		 * @param \WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
		 * @param Meetings $this  The current API handler object instance.
		 */
		$body = apply_filters(
			"tribe_events_virtual_meetings_zoom_{$this::$meeting_type}_update_request_body",
			$event_body,
			$event,
			$this
		);

		// Load the account.
		$zoom_account_id = $this->api->get_account_id_in_admin( $post_id );
		if ( empty( $zoom_account_id ) ) {
			return;
		}

		$this->api->load_account_by_id( $zoom_account_id );
		if ( ! $this->api->get_token_authorization_header() ) {
			return;
		}

		// Update.
		$this->api->patch(
			Api::$api_base . "{$this::$api_endpoint}/{$event->zoom_meeting_id}",
			[
				'headers' => [
					'Authorization' => $this->api->get_token_authorization_header(),
					'Content-Type'  => 'application/json; charset=utf-8',
					'accept'        => 'application/json;',
				],
				'body'    => wp_json_encode( $body ),
			],
			Api::PATCH_RESPONSE_CODE
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
	 * Processes the Zoom API Meeting update response to massage, filter and save the data.
	 *
	 * @since 1.0.2
	 *
	 * @param array<string,mixed> $response The entire Zoom API response.
	 * @param \WP_Post            $event    The event post object.
	 * @param int                 $post_id  The event post ID.
	 *
	 * @return array<string,mixed>|false The Zoom Meeting data or `false` on error.
	 */
	protected function process_meeting_update_response( $response, $event, $post_id ) {
		if ( empty( $response['response']['code'] ) || 204 !== $response['response']['code'] ) {
			return false;
		}

		$event = tribe_get_event( $event );
		if ( ! $event instanceof \WP_Post ) {
			return false;
		}

		$success = false;

		// Load the account.
		$zoom_account_id = $this->api->get_account_id_in_admin( $post_id );
		if ( empty( $zoom_account_id ) ) {
			return false;
		}

		$this->api->load_account_by_id( $zoom_account_id );
		if ( ! $this->api->get_token_authorization_header() ) {
			return false;
		}

		$this->api->get(
			Api::$api_base . "{$this::$api_endpoint}/{$event->zoom_meeting_id}",
			[
				'headers' => [
					'Authorization' => $this->api->get_token_authorization_header(),
					'Content-Type'  => 'application/json; charset=utf-8',
					'accept'        => 'application/json;',
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
	 * Format the event start date for zoom.
	 *
	 * @since 1.2.0
	 *
	 * @param string $start_date The start date of the event.
	 * @param string $start_time The start time of the event.
	 * @param string $time_zone  The timezone of the event.
	 *
	 * @return string The time formatted for Zoom using 'Y-m-d\TH:i:s\Z'.
	 */
	public function format_date_for_zoom( $start_date, $start_time, $time_zone ) {
		$timezone_object = Timezones::build_timezone_object( 'UTC' );
		// Utilize the datepicker format when parse the Event Date to prevent the wrong date in Zoom.
		$datepicker_format = Dates::datepicker_formats( tribe_get_option( 'datepickerFormat' ) );
		$start_date_time   = Dates::datetime_from_format( $datepicker_format, $start_date ) . ' ' . $start_time;

		return Dates::build_date_object( $start_date_time, $time_zone )->setTimezone( $timezone_object )->format( 'Y-m-d\TH:i:s\Z' );
	}

	/**
	 * Get the duration of an event.
	 *
	 * @since 1.7.0
	 *
	 * @param string $start_date The start date of the event.
	 * @param string $start_time The start time of the event.
	 * @param string $end_date   The end date of the event.
	 * @param string $end_time   The end time of the event.
	 * @param string $time_zone  The timezone of the event.
	 *
	 * @return string The duration in seconds.
	 */
	private function calculate_duration( $start_date, $start_time, $end_date, $end_time, $time_zone ) {
		$timezone_object   = Timezones::build_timezone_object( 'UTC' );
		$datepicker_format = Dates::datepicker_formats( tribe_get_option( 'datepickerFormat' ) );

		$start_date_time = Dates::datetime_from_format( $datepicker_format, $start_date ) . ' ' . $start_time;
		$start_timestamp = Dates::build_date_object( $start_date_time, $time_zone )->setTimezone( $timezone_object )->getTimestamp();

		$end_date_time = Dates::datetime_from_format( $datepicker_format, $end_date ) . ' ' . $end_time;
		$end_timestamp = Dates::build_date_object( $end_date_time, $time_zone )->setTimezone( $timezone_object )->getTimestamp();

		return absint( $end_timestamp - $start_timestamp );
	}

	/**
	 * Handles the request to update a Zoom meeting or webinar.
	 *
	 * @since 1.4.0
	 * @deprecated 1.13.0 - No replacement.
	 *
	 * @param string|null $nonce The nonce that should accompany the request.
	 *
	 * @return bool Whether the request was handled or not.
	 */
	public function ajax_update( $nonce = null ) {
		_deprecated_function( __METHOD__, '1.13.1', 'No replacement.' );

		if ( ! $this->check_ajax_nonce( static::$update_action, $nonce ) ) {
			return false;
		}

		$event = $this->check_ajax_post();

		if ( ! $event ) {
			return false;
		}

		$post_id = $event->ID;

		$zoom_id = tribe_get_request_var( 'zoom_id' );
		if ( empty( $zoom_id ) ) {
			return false;
		}

		$alternative_hosts = tribe_get_request_var( 'zoom_alternative_hosts' );

		$body = [
			'settings' => [
				'alternative_hosts' => esc_attr( implode( ";", $alternative_hosts ) ),
			]
		];

		/**
		 * Filters the contents of the request that will be made to the Zoom API to update a meeting or webinar.
		 *
		 * @since 1.4.0
		 *
		 * @param array<string,mixed> The current content of the request body.
		 * @param \WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
		 * @param Meetings $this  The current API handler object instance.
		 */
		$body = apply_filters(
			"tribe_events_virtual_ajax_update_meetings_zoom_{$this::$meeting_type}_request_body",
			$body,
			$event,
			$this
		);

		$success = false;

		// Update.
		$this->api->patch(
			Api::$api_base . "{$this::$api_endpoint}/{$zoom_id}",
			[
				'headers' => [
					'Authorization' => $this->api->token_authorization_header(),
					'Content-Type'  => 'application/json; charset=utf-8',
					'accept'        => 'application/json;',
				],
				'body'    => wp_json_encode( $body ),
			],
			Api::PATCH_RESPONSE_CODE
		)->then(
			function ( array $response ) use ( $post_id, $event, &$success ) {
				$this->process_meeting_update_response( $response, $event, $post_id );

				$success = true;

				wp_die();
			}

		)->or_catch(
			static function ( \WP_Error $error ) use ( $event ) {
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

		return $success;
	}
}
