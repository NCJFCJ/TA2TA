<?php
/**
 * Handles the rendering of the Classic Editor controls.
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual\Meetings\Zoom
 */

namespace Tribe\Events\Virtual\Meetings\Zoom;

use Tribe\Events\Virtual\Admin_Template;
use Tribe\Events\Virtual\Event_Meta as Virtual_Events_Meta;
use Tribe\Events\Virtual\Event_Meta as Virtual_Meta;
use Tribe\Events\Virtual\Integrations\Editor\Abstract_Classic_Labels;
use Tribe\Events\Virtual\Meetings\Zoom\Event_Meta as Zoom_Meta;
use Tribe\Events\Virtual\Metabox;
use Tribe__Utils__Array as Arr;

/**
 * Class Classic_Editor
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual\Meetings\Zoom
 */
class Classic_Editor extends Abstract_Classic_Labels {

	/**
	 * {@inheritDoc}
	 */
	public static $api_name = 'Zoom';

	/**
	 * {@inheritDoc}
	 */
	public static $api_id = 'zoom';

	/**
	 * Classic_Editor constructor.
	 *
	 * @since 1.9.0 - Add Settings Dependency
	 * @since 1.13.0 - Add the Action class.
	 *
	 * @param Url                    $url                    The URLs handler for the integration.
	 * @param Api                    $api                    An instance of the Zoom API handler.
	 * @param Admin_Template         $template               An instance of the Template class to handle the rendering of admin views.
	 * @param Settings               $settings               An instance of the Webex Settings handler.
	 * @param Users                  $users                  The Users handler for the integration.
	 * @param Template_Modifications $template_modifications An instance of the Template_Modifications handler.
	 * @param Actions                $actions                An instance of the Actions name handler.
	 */
	public function __construct( Url $url, Api $api, Admin_Template $template, Settings $settings, Users $users, Actions $actions ) {
		$this->url      = $url;
		$this->api      = $api;
		$this->template = $template;
		$this->settings = $settings;
		$this->users    = $users;
		$this->actions  = $actions;
	}

	/**
	 * {@inheritDoc}
	 */
	public function add_event_properties( $post = null ) {
		return Zoom_Meta::add_event_properties( $post );
	}

	/**
	 * Renders, echoing to the page, the Zoom API meeting generator controls.
	 *
	 * @since 1.0.0
	 * @since 1.4.0 - Add ability to force the meeting and webinar generator.
	 * @since 1.5.0 - Support for multiple accounts.
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

		// Load the Zoom account.
		$account_loaded = $this->api->load_account_by_id( $account_id );
		$candidate_types = [
			// Always allow by default.
			'meeting' => true,
			// Allow the generation only if the account has the correct caps.
			'webinar' => $this->api->supports_webinars(),
		];
		$available_types = [];

		foreach ( $candidate_types as $type => $allow ) {
			/**
			 * Allow filtering whether to allow link generation and to show controls for a meeting type.
			 * This will continue to allow previously generated links to be seen and removed.
			 *
			 * @since 1.1.1
			 *
			 * @param boolean  $allow Whether to allow link generation.
			 * @param \WP_Post $post  The post object of the Event context of the link generation.
			 */
			$allow = apply_filters( "tribe_events_virtual_zoom_{$type}_link_allow_generation", $allow, $post );

			if ( tribe_is_truthy( $allow ) ) {
				$available_types[] = $type;
			}
		}

		$allow_link_gen = count( $available_types ) > 0;
		$meeting_link = tribe( Password::class )->get_zoom_meeting_link( $post );

		if ( ! empty( $meeting_link ) && ! $force_generator ) {
			// Meetings and Webinars Details.
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
	 * Returns the remove link.
	 *
	 * @since 1.5.0
	 *
	 * @param int|\WP_Post $event      The event ID or object.
	 *
	 * @return string The remove link, unescaped.
	 */
	protected function get_remove_link( $event ) {
		return empty( $event->zoom_meeting_type ) || Webinars::$meeting_type !== $event->zoom_meeting_type ?
			$this->url->to_remove_meeting_link( $event )
			: $this->url->to_remove_webinar_link( $event );
	}

	/**
	 * Returns the remove link label.
	 *
	 * @since 1.5.0
	 *
	 * @return string The remove link label, unescaped.
	 */
	protected function get_remove_link_label() {
		return _x(
			'Remove Zoom link',
			'The label for the admin UI control that allows removing the Zoom Meeting or Webinar link from the event.',
			'events-virtual'
		);
	}

	/**
	 * Returns the account link selection URL.
	 *
	 * @since 1.5.0
	 *
	 * @param \WP_Post $post                  The post object of the Event context of the link generation.
	 * @param bool     $include_generate_text Whether to include the "Generate" text in the labels or not.
	 *
	 * @return array<string,array<string>> A map (by meeting type) of unpackable arrays, each one containing the URL and
	 *                                     label for the generation link HTML code.
	 */
	protected function get_account_link_selection_url( \WP_Post $post, $include_generate_text = false ) {
		$link = $this->url->to_select_account_link( $post );

		/**
		 * Allows filtering the account selection link URL.
		 *
		 * @since 1.5.0
		 * @since 1.8.2 - Renamed as the filter name was used in another context.
		 *
		 * @param string   $link The url used to setup the account selection.
		 * @param \WP_Post $post The post object of the Event context of the link generation.
		 */
		$link = apply_filters( 'tec_events_virtual_zoom_select_account_url', $link, $post );

		return $link;
	}

	/**
	 * Renders the link generator HTML for 2+ Zoom Meeting types (e.g. Webinars and Meetings).
	 *
	 * Currently the available types are, at the most, 2: Meetings and Webinars. This method might need to be
	 * updated in the future if that assumption changes. If this method runs, then it means that we should render
	 * generation links for both type of meetings.
	 *
	 * @since 1.1.1
	 * @since 1.4.0 - Add support to choose a host before meeting or webinar creation.
	 * @since 1.5.0 - Add support for multiple accounts.
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
		 * Filters the host list to use to assign to Zoom Meetings and Webinars.
		 *
		 * @since 1.4.0
		 *
		 * @param array<string,mixed>  An array of Zoom Users to use as the host.
		 */
		$hosts = apply_filters( 'tribe_events_virtual_meetings_zoom_hosts', $hosts );

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

		$api_id                = $this->api::$api_id;
		$settings              = $this->api->fetch_user( $account_id, true );
		$password_requirements = $this->api->get_password_requirements( $settings );

		return $this->template->template(
			'virtual-metabox/zoom/setup',
			[
				'api_id'                  => $api_id,
				'event'                   => $post,
				'attrs'                   => [
					'data-account-id' => $account_id,
					'data-api-id'     => $api_id,
				],
				'offer_or_label'          => _x(
					'or',
					'The lowercase "or" label used to offer the creation of a Zoom Meetings or Webinars API link.',
					'events-virtual'
				),
				'account_label'           => _x(
					'Account: ',
					'The label used to designate the account of a Zoom Meeting or Webinar.',
					'events-virtual'
				),
				'account_name'            => $this->api->loaded_account_name,
				'generation_toggle_label' => _x(
					'Generate Zoom Link',
					'The label of the toggle to show the links to generate Zoom Meetings or Webinars.',
					'events-virtual'
				),
				'generation_urls'         => $this->get_link_creation_urls( $post ),
				'generate_label'          => _x(
					'Create ',
					'The label used to designate the next step in generation of a Zoom Meeting or Webinar.',
					'events-virtual'
				),
				'hosts'                   => [
					'label'       => _x(
						'Meeting Host',
						'The label of the meeting or webinar host.',
						'events-virtual'
					),
					'id'          => 'tribe-events-virtual-zoom-host',
					'class'       => 'tec-events-virtual-meetings-api__host-dropdown tribe-events-virtual-meetings-zoom__host-dropdown',
					'name'        => 'tribe-events-virtual-zoom-host',
					'selected'    =>  $post->zoom_host_id,
					'attrs'       => [
						'placeholder'       => _x(
						    'Select a Host',
						    'The placeholder for the dropdown to select a host.',
						    'events-virtual'
						),
						'data-selected'      => $post->zoom_host_id,
						'data-prevent-clear' => true,
						'data-force-search'  => true,
						'data-options'       => json_encode( $hosts ),
						'data-validate-url'  => $this->url->to_validate_user_type( $post ),
					],
				],
				'remove_link_url'          => $this->get_remove_link( $post ),
				'remove_link_label'        => $this->get_remove_link_label(),
				'remove_attrs'             => [
					'data-confirmation' => $this->get_remove_confirmation_text(),
				],
				'message'                  => $message,
				'zoom_message_classes'     => [ 'tec-events-virtual-video-source-api-setup__messages-wrap' ],
				'password_requirements'    => $password_requirements,
			],
			$echo
		);
	}

	/**
	 * Returns the meeting/webinar creation links and labels.
	 *
	 * @since 1.8.2
	 *
	 * @param \WP_Post $post            The post object of the Event context of the link creation.
	 * @param bool     $webinar_support Whether to add the webinar create link.
	 *
	 * @return array<string,array<string>> A map (by meeting type) of unpackable arrays, each one containing the URL and
	 *                                     label for the creation link HTML code.
	 */
	public function get_link_creation_urls( \WP_Post $post, $webinar_support = false ) {
		$meeting_create_label = _x(
			'Meeting',
			'Label for the control to generate a Zoom meeting link in the event classic editor UI.',
			'events-virtual'
		);
		$webinar_create_label = _x(
			'Webinar',
			'Label for the control to generate a Zoom webinar link in the event classic editor UI.',
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

		$webinar_tooltip = [
			'classes_wrap'  => [ 'tec-events-virtual-meetings-zoom__type-options--tooltip' ],
			'message'   => _x(
				'Webinars are not enabled for this host. Webinar support is enabled by the account plan in Zoom.',
				'Explains why the webinar field is disabled when creating a meeting/webinar for an event.',
				'events-virtual'
			),
		];

		// Add webinar, but disable by default
		$data[ Webinars::$meeting_type ] = [
			$this->url->to_generate_webinar_link( $post ),
			$webinar_create_label,
			// Webinar Disabled, set to true if disabled.
			$webinar_support || $this->api->supports_webinars() ? false : true,
			// Add Tooltip.
			$webinar_support || $this->api->supports_webinars() ? [] : $webinar_tooltip,
		];

		/**
		 * Allows filtering the creation links URL and label before rendering them on the admin UI.
		 *
		 * @since 1.8.2
		 *
		 * @param array<string,array<string>> A map (by meeting type) of unpackable arrays, each one containing the URL and
		 *                                    label for the creation link HTML code.
		 * @param \WP_Post $post              The post object of the Event context of the link creation.
		 */
		return apply_filters( 'tec_events_virtual_zoom_meeting_link_creation_urls', $data, $post );
	}

	/**
	 * Get an existing Meeting details.
	 *
	 * @since 1.8.0
	 *
	 * @param \WP_Post    $post           The post object of the Event context of the link generation.
	 * @param bool        $echo           Whether to print the rendered HTML to the page or not.
	 * @param null|string $account_id     The account id to use to load the link generators.
	 * @param bool        $account_loaded The account is loaded successfully into the API.
	 *
	 * @return string|false Either the final content HTML or `false` if the template could be found.
	 */
	public function get_meeting_details( \WP_Post $post, $echo = true, $account_id = null, $account_loaded = false ) {

		// Make sure to apply the properties to the event.
		$post = $this->add_event_properties( $post );

		// Load the account for the API instance.
		if ( ! $account_loaded ) {
			$account_loaded = $this->api->load_account_by_id( $account_id );
		}

		return $this->render_meeting_details( $post, $echo, $account_id, $account_loaded );
	}

	/**
	 * Renders an existing Meeting details.
	 *
	 * @since 1.1.1
	 * @since 1.5.0 - Add multiple accounts support.
	 *
	 * @param \WP_Post    $post           The post object of the Event context of the link generation.
	 * @param bool        $echo           Whether to print the rendered HTML to the page or not.
	 * @param null|string $account_id     The account id to use to load the link generators.
	 * @param bool        $account_loaded The account is loaded successfully into the API.
	 *
	 * @return string|false Either the final content HTML or `false` if the template could be found.
	 */
	protected function render_meeting_details( \WP_Post $post, $echo = true, $account_id = null, $account_loaded = false ) {
		// Display a different details title depending on the type of meeting.
		$meeting_type = empty( $post->zoom_meeting_type ) || Webinars::$meeting_type !== $post->zoom_meeting_type
			? Meetings::$meeting_type
			: Webinars::$meeting_type;

		$details_title = Webinars::$meeting_type === $meeting_type
			? _x(
				'Zoom Webinar:',
				'Title of the details box shown for a generated Zoom Webinar link in the backend.',
				'events-virtual'
			)
			: _x(
				'Zoom Meeting:',
				'Title of the details box shown for a generated Zoom Meeting link in the backend.',
				'events-virtual'
			);

		$alt_hosts = [];
		if ( $account_id ) {
			$alt_hosts = $this->users->get_alternative_users( [], $post->zoom_alternative_hosts, $post->zoom_host_email, $account_id );
		}

		/**
		 * Filters the host list to use to assign to Zoom Meetings and Webinars.
		 *
		 * @since 1.4.0
		 *
		 * @param array<string,mixed>   An array of Zoom Users to use as the alternative hosts.
		 * @param string $selected_alt_hosts The list of alternative host emails.
		 * @param string $current_host       The email of the current host.
		 */
		$alt_hosts = apply_filters( 'tribe_events_virtual_meetings_zoom_alternative_hosts', $alt_hosts, $post->zoom_alternative_hosts, $post->zoom_host_email );

		$message = '';
		if ( 'not-found' === $account_loaded ) {
			$message = $this->render_account_disabled_details( false, true, false );
		} elseif ( 'disabled' === $account_loaded ) {
			$message = $this->render_account_disabled_details( true, true, false );
		}

		$connected_msg = '';
		$manual_connected = get_post_meta( $post->ID, Virtual_Events_Meta::$key_autodetect_source, true );
		if ( Zoom_Meta::$key_source_id === $manual_connected ) {
			$connected_msg = Webinars::$meeting_type === $meeting_type
				? _x(
					'This webinar is manually connected to the event and changes to the event will not alter the Zoom webinar.',
					'Message for a manually connected Zoom meeting or webinar.',
					'events-virtual'
				)
				: _x(
					'This meeting is manually connected to the event and changes to the event will not alter the Zoom meeting.',
					'Message for a manually connected Zoom meeting or webinar.',
					'events-virtual'
				);
		}

		return $this->template->template(
			'virtual-metabox/zoom/details',
			[
				'attrs'                    => [
					'data-depends'            => '#tribe-events-virtual-video-source',
					'data-condition'          => static::$api_id,
					'data-zoom-id'            => $post->zoom_meeting_id,
					'data-selected-alt-hosts' => $post->zoom_alternative_hosts,
				],
				'connected'                => Zoom_Meta::$key_source_id === $manual_connected,
				'connected_msg'            => $connected_msg,
				'event'                    => $post,
				'details_title'            => $details_title,
				'remove_link_url'          => $this->get_remove_link( $post ),
				'remove_link_label'        => $this->get_remove_link_label(),
				'remove_attrs'             => [
					'data-confirmation' => $this->get_remove_confirmation_text(),
				],
				'account_name'             => $this->api->loaded_account_name,
				'host_label'            => _x(
					'Host: ',
					'The label used to designate the host of a Zoom Meeting or Webinar.',
					'events-virtual'
				),
				'zoom_id'               => $post->zoom_meeting_id,
				'id_label'              => _x(
					'ID: ',
					'The label used to prefix a Zoom Meeting or Webinar ID in the backend.',
					'events-virtual'
				),
				'phone_numbers'         => array_filter(
					(array) get_post_meta( $post->ID, Virtual_Meta::$prefix . 'zoom_global_dial_in_numbers', true )
				),
				'selected_alt_hosts'    => $post->zoom_alternative_hosts,
				'alt_hosts'             => [
					'label'       => _x(
						'Alternative Hosts',
						'The label of the alternative host multiselect',
						'events-virtual'
					),
					'id'          => 'tribe-events-virtual-zoom-alt-host',
					'name'        => 'tribe-events-virtual-zoom-alt-host[]',
					'class'       => 'tribe-events-virtual-meetings-zoom__alt-host-multiselect',
					'selected'    => $post->zoom_alternative_hosts,
					'attrs'       => [
						'data-placeholder' => _x(
							'Add Alternative hosts',
							'The placeholder for the multiselect to select alternative hosts.',
							'events-virtual'
						),
						'data-force-search' => true,
						'data-selected'     => $post->zoom_host_id,
						'data-options'      => json_encode( $alt_hosts ),
					],
				],
				'message'               => $message,
			],
			$echo
		);
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

		$zoom_account_id = tribe_get_request_var( 'zoom_account_id' );
		if ( empty( $zoom_account_id ) ) {
			$zoom_account_id = tribe_get_request_var( 'account_id' );
		}
		// If no account id found, fail the request.
		if ( empty( $zoom_account_id ) ) {
			$error_message = _x( 'The Zoom Account ID is missing to access the API.', 'Account ID is missing error message.', 'events-virtual' );
			$this->render_meeting_generation_error_details( $event, $error_message, true );

			wp_die();
		}

		$account_loaded = $this->api->load_account_by_id( $zoom_account_id );
		// If there is no token, then stop as the connection will fail.
		if ( ! $account_loaded ) {
			$error_message = _x( 'The Zoom Account could not be loaded to access the API. Please try refreshing the account in the Events API Settings.', 'Zoom account loading error message.', 'events-virtual' );

			$this->render_meeting_generation_error_details( $event, $error_message, true );

			wp_die();
		}

		$post_id = $event->ID;

		// Set the video source to zoo.
		update_post_meta( $post_id, Virtual_Events_Meta::$key_video_source, Zoom_Meta::$key_source_id );

		// get the setup
		$this->render_meeting_link_generator( $event, true, false, $zoom_account_id );
		$this->api->save_account_id_to_post( $post_id, $zoom_account_id );

		wp_die();
	}
}
