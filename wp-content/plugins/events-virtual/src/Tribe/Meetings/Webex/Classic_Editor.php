<?php
/**
 * Handles the rendering of the Classic Editor controls.
 *
 * @since 1.9.0
 *
 * @package Tribe\Events\Virtual\Meetings\Webex
 */

namespace Tribe\Events\Virtual\Meetings\Webex;

use Tribe\Events\Virtual\Admin_Template;
use Tribe\Events\Virtual\Event_Meta as Virtual_Event_Meta;
use Tribe\Events\Virtual\Event_Meta as Virtual_Events_Meta;
use Tribe\Events\Virtual\Integrations\Editor\Abstract_Classic_Labels;
use Tribe\Events\Virtual\Meetings\Webex\Event_Meta as Webex_Meta;

/**
 * Class Classic_Editor
 *
 * @since 1.9.0
 *
 * @package Tribe\Events\Virtual\Meetings\Webex
 */
class Classic_Editor extends Abstract_Classic_Labels {

	/**
	 * {@inheritDoc}
	 */
	public static $api_name = 'Webex';

	/**
	 * {@inheritDoc}
	 */
	public static $api_id = 'webex';

	/**
	 * Classic_Editor constructor.
	 *
	 * @since 1.13.0 - Add the Action class.
	 *
	 * @param Api            $api      An instance of the Webex API handler.
	 * @param Settings       $settings An instance of the Webex Settings handler.
	 * @param Admin_Template $template An instance of the Template class to handle the rendering of admin views.
	 * @param Users          $users    The Users handler for the integration.
	 * @param Url            $url      The URLs handler for the integration.
	 * @param Actions        $actions  An instance of the Actions name handler.
	 */
	public function __construct( Api $api, Settings $settings, Admin_Template $template, Users $users, Url $url, Actions $actions ) {
		$this->api      = $api;
		$this->settings = $settings;
		$this->template = $template;
		$this->users    = $users;
		$this->url      = $url;
		$this->actions  = $actions;
	}

	/**
	 * {@inheritDoc}
	 */
	public function add_event_properties( $post = null ) {
		return Webex_Meta::add_event_properties( $post );
	}

	/**
	 * Renders, echoing to the page, the API generator controls.
	 *
	 * @since 1.9.0
	 *
	 * @param null|\WP_Post|int $post            The post object or ID of the event to generate the controls for, or `null` to use
	 *                                           the global post object.
	 * @param bool              $echo            Whether to echo the template contents to the page (default) or to return it.
	 * @param bool              $force_generator Whether to force to display the meeting and webinar generator.
	 * @param null|string       $account_id      The account id to use to load the link generators.
	 *
	 * @return string The template contents, if not rendered to the page.
	 */
	public function render_meeting_link_generator( $post = null, $echo = true, $force_generator = false, $account_id = null ) {
		$post = tribe_get_event( $post );

		if ( ! $post instanceof \WP_Post ) {
			return '';
		}

		// Make sure to apply the properties to the event.
		$post = $this->add_event_properties( $post );
		$meeting_link = $post->webex_join_url;

		// Load the account.
		$account_loaded = $this->api->load_account_by_id( $account_id );

		$candidate_types = [
			// Always allow by default.
			'meeting' => true,
		];
		$available_types = [];

		foreach ( $candidate_types as $type => $allow ) {
			/**
			 * Allow filtering whether to allow link generation and to show controls for a meeting type.
			 * This will continue to allow previously generated links to be seen and removed.
			 *
			 * @since 1.9.0
			 *
			 * @param boolean  $allow Whether to allow link generation.
			 * @param \WP_Post $post  The post object of the Event context of the link generation.
			 */
			$allow = apply_filters( "tec_events_virtual_webex_{$type}_link_allow_generation", $allow, $post );

			if ( tribe_is_truthy( $allow ) ) {
				$available_types[] = $type;
			}
		}

		$allow_link_gen = count( $available_types ) > 0;

		if ( ! empty( $meeting_link ) && ! $force_generator ) {
			// Meetings Details.
			return $this->render_meeting_details( $post, $echo, $account_id, $account_loaded );
		}

		// Do not show the link generation controls if not allowed for any type.
		if ( false === $allow_link_gen ) {
			return '';
		}

		if ( count( $available_types ) > 0 ) {
			// Meetings and Webinars.
			return $this->render_multiple_links_generator( $post, $echo, $account_id, $account_loaded );
		}

		if ( ! $account_loaded && 'disabled' !== $account_loaded ) {
			return $this->render_account_disabled_details( false );
		}

		// If the account is disabled, display the disabled details message.
		if ( 'disabled' === $account_loaded ) {
			return $this->render_account_disabled_details();
		}

		return $this->render_initial_setup_options( $post, $echo );
	}

	/**
	 * Renders an existing Meeting details.
	 *
	 * @since 1.9.0
	 *
	 * @param \WP_Post    $post           The post object of the Event context of the link generation.
	 * @param bool        $echo           Whether to print the rendered HTML to the page or not.
	 * @param null|string $account_id     The account id to use to load the link generators.
	 * @param bool        $account_loaded The account is loaded successfully into the API.
	 *
	 * @return string|false Either the final content HTML or `false` if the template could be found.
	 */
	protected function render_meeting_details( \WP_Post $post, $echo = true, $account_id = null, $account_loaded = false ) {
		// Make sure to apply the properties to the event.
		$post = $this->add_event_properties( $post );

		$details_title = _x(
				'Webex Meeting:',
				'Title of the details box shown for a generated Webex Meeting link in the backend.',
				'events-virtual'
			);

		$message = '';
		if ( 'not-found' === $account_loaded ) {
			$message = $this->render_account_disabled_details( false, true, false );
		} elseif ( 'disabled' === $account_loaded ) {
			$message = $this->render_account_disabled_details( true, true, false );
		}

		$connected_msg = '';
		$manual_connected = get_post_meta( $post->ID, Virtual_Events_Meta::$key_autodetect_source, true );
		if ( Webex_Meta::$key_source_id === $manual_connected ) {
			$connected_msg = _x(
					'This meeting is manually connected to the event and changes to the event will not alter the Webex meeting.',
					'Message for a manually connected Webex meeting or webinar.',
					'events-virtual'
				);
		}

		return $this->template->template(
			'virtual-metabox/webex/details',
			[
				'attrs'                    => [
					'data-depends'    => '#tribe-events-virtual-video-source',
					'data-condition'  => static::$api_id,
					'data-webex-id'   => $post->webex_meeting_id,
				],
				'connected'                => Webex_Meta::$key_source_id === $manual_connected,
				'connected_msg'            => $connected_msg,
				'event'                    => $post,
				'details_title'            => $details_title,
				'remove_link_url'          => $this->get_remove_link( $post ),
				'remove_link_label'        => $this->get_remove_link_label(),
				'remove_attrs'             => [
					'data-confirmation' => $this->get_remove_confirmation_text(),
				],
				'account_name'             => $this->api->loaded_account_name,
				'host_label'               => _x(
					'Host: ',
					'The label used to designate the host of a Webex Meeting.',
					'events-virtual'
				),
				'webex_id'               => $post->webex_meeting_id,
				'id_label'               => _x(
					'ID: ',
					'The label used to prefix a Webex Meeting in the backend.',
					'events-virtual'
				),
				'message'                => $message,
			],
			$echo
		);
	}

	/**
	 * Renders the link generator HTML for Webex Meetings.
	 *
	 * @since 1.9.0
	 *
	 * @param \WP_Post    $post           The post object of the Event context of the link generation.
	 * @param bool        $echo           Whether to print the rendered HTML to the page or not.
	 * @param null|string $account_id     The account id to use to load the link generators.
	 * @param bool        $account_loaded The account is loaded successfully into the API.
	 *
	 * @return string|false Either the final content HTML or `false` if the template could be found.
	 */
	public function render_multiple_links_generator( \WP_Post $post, $echo = true, $account_id = null, $account_loaded = false ) {
		$hosts = [];
		if ( $account_id ) {
			$hosts = $this->users->get_formatted_hosts_list( $account_id );
		}

		/**
		 * Filters the host list to use to assign to Webex Meetings.
		 *
		 * @since 1.9.0
		 *
		 * @param array<string,mixed>  An array of Webex Users to use as the host.
		 */
		$hosts = apply_filters( 'tec_events_virtual_meetings_webex_hosts', $hosts );

		// Display no hosts found error template.
		if ( empty( $hosts ) ) {
			return $this->render_no_hosts_found();
		}

		$message = '';
		if ( 'not-found' === $account_loaded ) {
			$message = $this->render_account_disabled_details( false, true, false );
		} elseif ( 'disabled' === $account_loaded ) {
			$message = $this->render_account_disabled_details( true, true, false );
		}
		$api_id = static::$api_id;

		return $this->template->template(
			'virtual-metabox/webex/setup',
			[
				'api_id'                  => $api_id,
				'event'                   => $post,
				'attrs'                   => [
					'data-depends'    => "#tribe-events-virtual-video-source",
					'data-condition'  => $api_id,
					'data-account-id' => $account_id,
					'data-api-id'     => $api_id,
				],
				'account_label'            => _x(
					'Account: ',
					'The label used to designate the account of a Webex Meeting.',
					'events-virtual'
				),
				'account_name'            => $this->api->loaded_account_name,
				'generation_toggle_label' => _x(
					'Generate Webex Link',
					'The label of the toggle to show the links to generate Webex Meeting.',
					'events-virtual'
				),
				'generation_urls'         => $this->get_link_creation_urls( $post ),
				'generate_label'        => _x(
					'Create ',
					'The label used to designate the next step in generation of a Webex Meeting.',
					'events-virtual'
				),
				'hosts' => [
					'label'       => _x(
						'Meeting Host',
						'The label of the meeting or webinar host.',
						'events-virtual'
					),
					'id'          => 'tribe-events-virtual-webex-host',
					'class'       => 'tec-events-virtual-meetings-api__host-dropdown tribe-events-virtual-meetings-webex__host-dropdown',
					'name'        => 'tribe-events-virtual-webex-host',
					'selected'    =>  '',
					'hosts_count' => count( $hosts ),
					'hosts_arr'   => $hosts,
					'attrs'       => [
						'placeholder'       => _x(
						    'Select a Host',
						    'The placeholder for the dropdown to select a host.',
						    'events-virtual'
						),
						'data-prevent-clear' => true,
						'data-force-search'  => false,
						'data-options'       => json_encode( $hosts ),
					],
				],
				'remove_link_url'          => $this->get_remove_link( $post ),
				'remove_link_label'        => $this->get_remove_link_label(),
				'remove_attrs'             => [
					'data-confirmation' => $this->get_remove_confirmation_text(),
				],
				'message'                  => $message,
				'message_classes'          => [ 'tec-events-virtual-video-source-api-setup__messages-wrap' ],
			],
			$echo
		);
	}

	/**
	 * Returns the meeting creation links and labels.
	 *
	 * @since 1.9.0
	 *
	 * @param \WP_Post $post            The post object of the Event context of the link creation.
	 * @param bool     $webinar_support Whether to add the webinar create link.
	 *
	 * @return array<string,array<string>> A map (by meeting type) of unpackable arrays, each one containing the URL and
	 *                                     label for the creation link HTML code.
	 */
	public function get_link_creation_urls( \WP_Post $post ) {
		$meeting_create_label = _x(
			'Meeting',
			'Label for the control to generate a Webex meeting link in the event classic editor UI.',
			'events-virtual'
		);

		$data = [
			Meetings::$meeting_type => [
				$this->url->to_generate_meeting_link( $post ),
				$meeting_create_label,
				false,
				[],
			]
		];

		/**
		 * Allows filtering the creation links URL and label before rendering them on the admin UI.
		 *
		 * @since 1.9.0
		 *
		 * @param array<string,array<string>> A map (by meeting type) of unpackable arrays, each one containing the URL and
		 *                                    label for the creation link HTML code.
		 * @param \WP_Post $post              The post object of the Event context of the link creation.
		 */
		return apply_filters( 'tec_events_virtual_webex_meeting_link_creation_urls', $data, $post );
	}

	/**
	 * {@inheritDoc}
	 */
	public function ajax_selection( $nonce = null ) {
		if ( ! $this->check_ajax_nonce( $this->actions::$select_action, $nonce ) ) {
			return false;
		}

		$event = $this->check_ajax_post();

		if ( ! $event ) {
			return false;
		}

		$account_id = tribe_get_request_var( 'account_id' );

		// If no account id found, fail the request.
		if ( empty( $account_id ) ) {
			$error_message = _x( 'The Webex Account ID is missing to access the API.', 'Account ID is missing error message.', 'events-virtual' );
			$this->render_meeting_generation_error_details( $event, $error_message, true );

			wp_die();
		}

		$account_loaded = $this->api->load_account_by_id( $account_id );
		// If there is no token, then stop as the connection will fail.
		if ( ! $account_loaded ) {
			$error_message = _x( 'The Webex Account could not be loaded to access the API. Please try refreshing the account in the Events API Settings.', 'Webex account loading error message.', 'events-virtual' );

			$this->render_meeting_generation_error_details( $event, $error_message, true );

			wp_die();
		}

		$post_id = $event->ID;

		// Set the video source to Webex.
		update_post_meta( $post_id, Virtual_Event_Meta::$key_video_source, Webex_Meta::$key_source_id );

		// get the setup
		$this->render_meeting_link_generator( $event, true, false, $account_id );
		$this->api->save_account_id_to_post( $post_id, $account_id );

		wp_die();
	}
}
