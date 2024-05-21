<?php
/**
 * Handles the rendering of the Classic Editor controls.
 *
 * @since 1.13.0
 *
 * @package Tribe\Events\Virtual\Meetings\Microsoft
 */

namespace Tribe\Events\Virtual\Meetings\Microsoft;

use Tribe\Events\Virtual\Admin_Template;
use Tribe\Events\Virtual\Event_Meta as Virtual_Event_Meta;
use Tribe\Events\Virtual\Event_Meta as Virtual_Events_Meta;
use Tribe\Events\Virtual\Integrations\Editor\Abstract_Classic_Labels;
use Tribe\Events\Virtual\Meetings\Microsoft\Event_Meta as Microsoft_Meta;

/**
 * Class Classic_Editor
 *
 * @since 1.13.0
 *
 * @package Tribe\Events\Virtual\Meetings\Microsoft
 */
class Classic_Editor extends Abstract_Classic_Labels {

	/**
	 * {@inheritDoc}
	 */
	public static $api_name = 'Microsoft';

	/**
	 * {@inheritDoc}
	 */
	public static $api_id = 'microsoft';

	/**
	 * Classic_Editor constructor.
	 *
	 * @param Api            $api      An instance of the Microsoft API handler.
	 * @param Settings       $settings An instance of the Microsoft Settings handler.
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
		$this->actions      = $actions;
	}

	/**
	 * {@inheritDoc}
	 */
	public function add_event_properties( $post = null ) {
		return Microsoft_Meta::add_event_properties( $post );
	}

	/**
	 * Renders, echoing to the page, the API generator controls.
	 *
	 * @since 1.13.0
	 *
	 * @param null|\WP_Post|int $post            The post object or ID of the event to generate the controls for, or `null` to use
	 *                                           the global post object.
	 * @param bool              $echo            Whether to echo the template contents to the page (default) or to return it.
	 * @param bool              $force_generator Whether to force to display the Meet generator.
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
		$meeting_link = $post->microsoft_join_url;

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
			 * @since 1.13.0
			 *
			 * @param boolean  $allow Whether to allow link generation.
			 * @param \WP_Post $post  The post object of the Event context of the link generation.
			 */
			$allow = apply_filters( "tec_events_virtual_microsoft_{$type}_link_allow_generation", $allow, $post );

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
			// Microsoft Meets.
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
	 * @since 1.13.0
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
				'Microsoft Meet:',
				'Title of the details box shown for a generated Microsoft Meet link in the backend.',
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
		if ( Microsoft_Meta::$key_source_id === $manual_connected ) {
			$connected_msg = _x(
					'This meeting is manually connected to the event and changes to the event will not alter the Microsoft Meet.',
					'Message for a manually connected Microsoft Meet.',
					'events-virtual'
				);
		}

		return $this->template->template(
			'virtual-metabox/microsoft/details',
			[
				'attrs'                    => [
					'data-depends'    => '#tribe-events-virtual-video-source',
					'data-condition'  => static::$api_id,
					'data-microsoft-id'   => $post->microsoft_meeting_id,
				],
				'connected'                => Microsoft_Meta::$key_source_id === $manual_connected,
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
					'The label used to designate the host of a Microsoft Meet.',
					'events-virtual'
				),
				'microsoft_id'               => $post->microsoft_meeting_id,
				'id_label'               => _x(
					'ID: ',
					'The label used to prefix a Microsoft Meet in the backend.',
					'events-virtual'
				),
				'message'                => $message,
			],
			$echo
		);
	}

	/**
	 * Renders the link generator HTML for Microsoft Meet.
	 *
	 * @since 1.13.0
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
		 * Filters the host list to use to assign to Microsoft Meet.
		 *
		 * @since 1.13.0
		 *
		 * @param array<string,mixed>  An array of Microsoft Users to use as the host.
		 */
		$hosts = apply_filters( 'tec_events_virtual_meetings_microsoft_hosts', $hosts );

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
		$api_id             = static::$api_id;
		$settings           = $this->api->get_calendar_settings( $account_id, true );
		$available_meetings = $this->api->get_available_meeting_providers( $settings );
		$checked            = $this->api->get_default_meeting_provider( $available_meetings );

		return $this->template->template(
			'virtual-metabox/microsoft/setup',
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
					'The label used to designate the account of a Microsoft Meet.',
					'events-virtual'
				),
				'account_name'            => $this->api->loaded_account_name,
				'checked'                 => $checked,
				'generation_toggle_label' => _x(
					'Generate Microsoft Link',
					'The label of the toggle to show the links to generate Microsoft Meet.',
					'events-virtual'
				),
				'generation_urls'         => $this->get_link_creation_urls( $post, $available_meetings ),
				'generate_label'        => _x(
					'Create ',
					'The label used to designate the next step in generation of a Microsoft Meet.',
					'events-virtual'
				),
				'hosts' => [
					'label'       => _x(
						'Meeting Host',
						'The label of the Microsoft Meet host.',
						'events-virtual'
					),
					'id'          => 'tribe-events-virtual-microsoft-host',
					'class'       => 'tec-events-virtual-meetings-api__host-dropdown tribe-events-virtual-meetings-microsoft__host-dropdown',
					'name'        => 'tribe-events-virtual-microsoft-host',
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
	 * @since 1.13.0
	 *
	 * @param \WP_Post             $post            The post object of the Event context of the link creation.
	 * @param array<string|string> $online_meetings An array of allowed online meetings for the Microsoft API.
	 *
	 * @return array<string,array<string>> A map (by meeting type) of unpackable arrays, each one containing the URL and
	 *                                     label for the creation link HTML code.
	 */
	public function get_link_creation_urls( \WP_Post $post, $available_meetings = [] ) {
		$data = [];

		foreach ( $available_meetings as $meeting_key => $meeting_available ) {
			$data[ $meeting_key ] = [
				$this->url->to_generate_meeting_link( $post ),
				$meeting_available['label'],
				! $meeting_available['available'],
				$meeting_available['tooltip'],
			];
		}

		/**
		 * Allows filtering the creation links URL and label before rendering them on the admin UI.
		 *
		 * @since 1.13.0
		 *
		 * @param array<string,array<string>> A map (by meeting type) of unpackable arrays, each one containing the URL and
		 *                                    label for the creation link HTML code.
		 * @param \WP_Post $post              The post object of the Event context of the link creation.
		 */
		return apply_filters( 'tec_events_virtual_microsoft_meeting_link_creation_urls', $data, $post );
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
			$error_message = _x( 'The Microsoft Account ID is missing to access the API.', 'Account ID is missing error message.', 'events-virtual' );
			$this->render_meeting_generation_error_details( $event, $error_message, true );

			wp_die();
		}

		$account_loaded = $this->api->load_account_by_id( $account_id );
		// If there is no token, then stop as the connection will fail.
		if ( ! $account_loaded ) {
			$error_message = _x( 'The Microsoft Account could not be loaded to access the API. Please try refreshing the account in the Events API Settings.', 'Microsoft account loading error message.', 'events-virtual' );

			$this->render_meeting_generation_error_details( $event, $error_message, true );

			wp_die();
		}

		$post_id = $event->ID;

		// Set the video source to Microsoft.
		update_post_meta( $post_id, Virtual_Event_Meta::$key_video_source, Microsoft_Meta::$key_source_id );

		// get the setup
		$this->render_meeting_link_generator( $event, true, false, $account_id );
		$this->api->save_account_id_to_post( $post_id, $account_id );

		wp_die();
	}
}
