<?php
/**
 * An abstract class to handle an API
 *
 * @since 1.9.0
 *
 * @package Tribe\Events\Virtual\Integrations
 */

namespace Tribe\Events\Virtual\Integrations\Editor;

use Tribe\Events\Virtual\Traits\With_AJAX;
use Tribe\Events\Virtual\Admin_Template;
use Tribe__Utils__Array as Arr;

/**
 * Class Api_Classic_Editor
 *
 * @since 1.9.0
 *
 * @package Tribe\Events\Virtual\Integrations\Editor
 */
Abstract class Abstract_Classic {
	use With_AJAX;

	/**
	 * The name of the API
	 *
	 * @since 1.9.0
	 *
	 * @var string
	 */
	public static $api_name;

	/**
	 * The id of the API
	 *
	 * @since 1.9.0
	 *
	 * @var string
	 */
	public static $api_id;

	/**
	 * An instance of a Api Handler.
	 *
	 * @since 1.9.0
	 *
	 * @var Api
	 */
	protected $api;

	/**
	 * An instance of a Settings Handler.
	 *
	 * @since 1.9.0
	 *
	 * @var Settings
	 */
	protected $settings;

	/**
	 * The template handler instance.
	 *
	 * @since 1.9.0
	 *
	 * @var Admin_Template
	 */
	protected $template;

	/**
	 * The Users handler for the Api.
	 *
	 * @since 1.9.0
	 *
	 * @var Users
	 */
	protected $users;

	/**
	 * The URLs handler for the Api.
	 *
	 * @since 1.9.0
	 *
	 * @var Url
	 */
	protected $url;

	/**
	 * The Actions name handler.
	 *
	 * @since 1.13.0
	 *
	 * @var Abstract_Actions
	 */
	protected $actions;

	/**
	 * Adds Event Properties to an event post object.
	 *
	 * @since 1.9.0
	 *
	 * @param \WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
	 *
	 * @return \WP_Post The decorated event post object, with the API related properties added to it.
	 */
	abstract function add_event_properties( $post = null );

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
	abstract public function render_meeting_link_generator( $post = null, $echo = true, $force_generator = false, $account_id = null );

	/**
	 * Handles the request to select an account.
	 *
	 * @since 1.9.0
	 *
	 * @param string|null $nonce The nonce that should accompany the request.
	 *
	 * @return bool Whether the request was handled or not.
	 */
	abstract public function ajax_selection( $nonce = null );

	/**
	 * Returns the remove link.
	 *
	 * @since 1.9.0
	 *
	 * @param int|\WP_Post $event      The event ID or object.
	 *
	 * @return string The remove link, unescaped.
	 */
	protected function get_remove_link( $event ) {
		return $this->url->to_remove_meeting_link( $event );
	}

	/**
	 * Returns the link for ajax account selection.
	 *
	 * @since 1.9.0
	 *
	 * @param \WP_Post $post                  The post object of the Event context of the link generation.
	 * @param bool     $include_generate_text Whether to include the "Generate" text in the labels or not.
	 *
	 * @return array<string,array<string>> A map (by meeting type) of unpackable arrays, each one containing the URL and
	 *                                     label for the generation link HTML code.
	 */
	protected function get_account_link_selection_url( \WP_Post $post, $include_generate_text = false ) {
		$link = $this->url->to_select_account_link( $post );
		$api_id = static::$api_id;

		/**
		 * Allows filtering the account selection link URL.
		 *
		 * @since 1.9.0
		 *
		 * @param string   $link   The url used to setup the account selection.
		 * @param \WP_Post $post   The post object of the Event context of the link generation.
		 * @param string   $api_id The id of the API.
		 */
		$link = apply_filters( 'tec_events_virtual_api_select_account_url', $link, $post, $api_id );

		/**
		 * Allows filtering the account selection link URL by the API.
		 *
		 * @since 1.9.0
		 *
		 * @param string   $link   The url used to setup the account selection.
		 * @param \WP_Post $post   The post object of the Event context of the link generation.
		 */
		$link = apply_filters( "tec_events_virtual_{$api_id}_select_account_url", $link, $post );

		return $link;
	}

	/**
	 * Renders, echoing to the page, the API initial setup options.
	 *
	 * @since 1.9.0
	 *
	 * @param null|\WP_Post|int $post            The post object or ID of the event to generate the controls for, or `null` to use
	 *                                           the global post object.
	 * @param bool              $echo            Whether to echo the template contents to the page (default) or to return it.
	 *
	 * @return string The template contents, if not rendered to the page.
	 */
	public function render_initial_setup_options( $post = null, $echo = true ) {
		$post = tribe_get_event( $post );

		if ( ! $post instanceof \WP_Post ) {
			return '';
		}

		// Make sure to apply the properties to the event.
		$post = $this->add_event_properties( $post );

		// If an account is found, load the meeting generation links or details.
		$account_id = $this->api->get_account_id_in_admin();
		if ( $account_id ) {
			return $this->render_meeting_link_generator( $post, true, false, $account_id );
		}

		// Get the list of accounts and if none show the link to setup an API integration.
		$accounts = $this->api->get_formatted_account_list( true );
		if ( empty( $accounts ) ) {
			return $this->render_setup_api_connection_link( $post, $echo );
		}

		return $this->render_account_selection( $post, $accounts );
	}

	/**
	 * Renders the link to setup the API connection.
	 *
	 * @since 1.9.0
	 *
	 * @param \WP_Post $post The post object of the Event to use the API with.
	 * @param bool     $echo Whether to print the rendered HTML to the page or not.
	 *
	 * @return string|false Either the final content HTML or `false` if the template could be found.
	 */
	protected function render_setup_api_connection_link( \WP_Post $post, $echo = true ) {
		return $this->template->template(
			'virtual-metabox/api/setup-link',
			[
				'api_id'           => static::$api_id,
				'attrs'                    => [
					'data-depends'    => "#tribe-events-virtual-video-source",
					'data-condition'  => static::$api_id,
					'data-api-id'     => static::$api_id,
				],
				'event'            => $post,
				'setup_link_label' => $this->get_connect_to_label(),
				'setup_link_url'   => $this->settings::admin_url(),
			],
			$echo
		);
	}

	/**
	 * Renders the dropdown to choose an API account.
	 *
	 * @since 1.9.0
	 *
	 * @param \WP_Post             $post             The post object of the Event context of the link generation.
	 * @param array<string|string> $list_of_accounts An array of API Accounts formatted for options dropdown.
	 * @param bool                 $echo             Whether to print the rendered HTML to the page or not.
	 *
	 * @return string|false Either the final content HTML or `false` if the template could be found.
	 */
	protected function render_account_selection( \WP_Post $post, array $accounts, $echo = true ) {
		$api_id = static::$api_id;

		/**
		 * Filters the account list used by an API to generate connections.
		 *
		 * @since 1.9.0
		 *
		 * @param array<string,mixed>  An array of API Accounts formatted for options dropdown.
		 */
		$accounts = apply_filters( 'tribe_events_virtual_meetings_api_accounts', $accounts );

		/**
		 * Filters a specific API account list used to generate connections.
		 *
		 * @since 1.9.0
		 *
		 * @param array<string,mixed>  An array of API Accounts formatted for options dropdown.
		 */
		$accounts = apply_filters( "tribe_events_virtual_meetings_{$api_id}_accounts", $accounts );

		return $this->template->template(
			'virtual-metabox/api/accounts',
			[
				'api_id'        => $api_id,
				'attrs'                    => [
					'data-depends'    => "#tribe-events-virtual-video-source",
					'data-condition'  => static::$api_id,
					'data-api-id'     => static::$api_id,
				],
				'event'         => $post,
				'title'         => $this->get_ui_title(),
				'select_url'    => $this->get_account_link_selection_url( $post ),
				'select_label'  => _x(
					'Next ',
					'The label used to designate the next step after selecting an API Account.',
					'events-virtual'
				),
				'accounts'      => [
					'label'       => _x(
						'Choose account:',
						'The label to choose an API account.',
						'events-virtual'
					),
					'id'          => "tec-events-virtual-{$api_id}-account",
					'class'       => "tec-events-virtual-meetings-api__account-dropdown tec-events-virtual-meetings-{$api_id}__account-dropdown",
					'name'        => "tribe-events-virtual-{$api_id}-account",
					'selected'    =>  '',
					'attrs'       => [
						'placeholder'        => _x(
						    'Select an Account',
						    'The placeholder for the dropdown to select an API account.',
						    'events-virtual'
						),
						'data-prevent-clear' => true,
						'data-force-search'  => true,
						'data-options'       => json_encode( $accounts ),
					],
				],
				'remove_link_url'          => $this->get_remove_link( $post ),
				'remove_link_label'        => $this->get_remove_link_label(),
				'remove_attrs'             => [
					'data-confirmation' => $this->get_remove_confirmation_text(),
				],
			],
			$echo
		);
	}

	/**
	 * Get an existing Meeting details.
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
	 * Add an API's accounts dropdown to autodetect fields.
	 *
	 * @since 1.9.0
	 *
	 * @param array<string|mixed> $autodetect   An array of the autodetect resukts.
	 * @param string              $video_url    The url to use to autodetect the video source.
	 * @param string              $video_source The optional name of the video source to attempt to autodetect.
	 * @param \WP_Post|null       $event        The event post object, as decorated by the `tribe_get_event` function.
	 * @param array<string|mixed> $ajax_data    An array of extra values that were sent by the ajax script.
	 *
	 * @return array<string|mixed> An array of the autodetect results.
	 */
	public function classic_autodetect_video_source_accounts( $autodetect_fields, $video_url, $video_source, $event, $ajax_data ) {
		if ( ! $event instanceof \WP_Post ) {
			return $autodetect_fields;
		}

		$api_key = static::$api_id;

		// All video sources are checked on the first autodetect run, only prevent checking of this source if it is set.
		if ( ! empty( $video_source ) && $api_key !== $video_source ) {
			return $autodetect_fields;
		}

		// Get optional chosen API account.
		$selected_account = Arr::get( $ajax_data, "{$api_key}-accounts", '' );

		$accounts = $this->api->get_formatted_account_list( true );

		if ( empty( $accounts ) ) {
			$autodetect_fields[] = [
				'path'  => 'virtual-metabox/api/autodetect-no-account',
				'field' => [
					'classes_wrap' => [ 'tribe-dependent', "tribe-events-virtual-meetings-autodetect-{$api_key}__message-wrap", 'error' ],
					'message'        => _x(
						'No accounts found. Use the link to authorize a new account or reauthorize an existing account:',
						'The message for smart url/autodetect when there are no valid api accounts.',
						'events-virtual'
					),
					'setup_link_label' => $this->get_connect_to_label(),
					'setup_link_url'   => $this->settings::admin_url(),
					'wrap_attrs'   => [
						'data-depends'   => '#tribe-events-virtual-autodetect-source',
						'data-condition' => static::$api_id,
					],
				]
			];
		} else {
			$autodetect_fields[] = [
				'path'  => 'components/dropdown',
				'field' => [
					'label'        => _x( 'Choose account:', 'The label of an api accounts dropdown.', 'events-virtual' ),
					'id'           => "tribe-events-virtual-autodetect-{$api_key}-account",
					'class'        => "tribe-events-virtual-meetings-autodetect-{$api_key}__account-dropdown",
					'classes_wrap' => [ 'tribe-dependent', "tribe-events-virtual-meetings-autodetect-{$api_key}__account-wrap" ],
					'name'         => "tribe-events-virtual-autodetect[{$api_key}-account]",
					'selected'     => $selected_account,
					'attrs'        => [
						'placeholder'        => _x(
							'Select an Account',
							'The placeholder for the dropdown to select an account.',
							'events-virtual'
						),
						'data-prevent-clear' => true,
						'data-hide-search'   => true,
						'data-options'       => json_encode( $accounts ),
					],
					'wrap_attrs'   => [
						'data-depends'   => '#tribe-events-virtual-autodetect-source',
						'data-condition' => static::$api_id,
					],
				]
			];
		}

		return $autodetect_fields;
	}

	/**
	 * Render the account disabled template.
	 *
	 * @since 1.9.0
	 *
	 * @param bool $is_loaded    Whether the account is loaded or not.
	 * @param bool $only_details Whether to echo only the details of the disabled template.
	 * @param bool $echo         Whether to echo the template to the page or not.
	 *
	 * @return string The rendered template contents.
	 */
	public function render_account_disabled_details( $is_loaded = true, $only_details = false, $echo = true ) {
		$disabled_title = $this->get_is_loaded_label( $is_loaded );
		$disabled_body = $this->get_is_loaded_body( $is_loaded );
		$link_label = $this->get_is_loaded_link_label( $is_loaded );

		if ( $only_details ) {
			return $this->template->template(
				'virtual-metabox/api/account-disabled-details',
				[
					'api_id'         => static::$api_id,
					'disabled_title' => $disabled_title,
					'disabled_body'  => $disabled_body,
					'link_url'       => $this->settings::admin_url(),
					'link_label'     => $link_label,
				],
				$echo
			);
		}

		return $this->template->template(
			'virtual-metabox/api/account-disabled',
			[
				'api_id'         => static::$api_id,
				'disabled_title' => $disabled_title,
				'disabled_body'  => $disabled_body,
				'link_url'       => $this->settings::admin_url(),
				'link_label'     => $link_label,
			],
			$echo
		);
	}

	/**
	 * Renders the error details shown to the user when an API connection fails.
	 *
	 * @since 1.9.0
	 *
	 * @param int|\WP_Post $event      The event ID or object.
	 * @param string       $error_body The error details in human-readable form. This can contain HTML
	 *                                 tags (e.g. links).
	 * @param bool         $echo       Whether to echo the template to the page or not.
	 *
	 * @return string The rendered template contents.
	 */
	public function render_meeting_generation_error_details( $event = null, $error_body = null, $echo = true ) {
		$event = tribe_get_event( $event );

		if ( ! $event instanceof \WP_Post ) {
			return '';
		}

		$is_authorized = $this->api->is_ready();

		$link_url   = $is_authorized
			? $this->url->to_generate_meeting_link( $event )
			: $this->settings::admin_url();
		$link_label = $is_authorized
			? _x(
				'Try again',
				'The label of the button to try and generate an connection again.',
				'events-virtual'
			)
			: $this->get_connect_to_label();

		if ( null === $error_body ) {
			$error_body = $this->get_unknown_error_message();
		}
		$error_body = wpautop( $error_body );

		return $this->template->template(
			'virtual-metabox/api/meeting-link-error-details',
			[
				'api_id'                   => static::$api_id,
				'attrs'                    => [
					'data-depends'    => "#tribe-events-virtual-video-source",
					'data-condition'  => static::$api_id,
					'data-api-id'     => static::$api_id,
				],
				'remove_link_url'          => $this->get_remove_link( $event ),
				'remove_link_label'        => $this->get_remove_link_label(),
				'remove_attrs'             => [
					'data-confirmation' => $this->get_remove_confirmation_text(),
				],
				'is_authorized'            => $is_authorized,
				'error_title'              => $this->get_the_error_message_title(),
				'error_message'            => $this->get_the_error_message(),
				'error_details_title'      => $this->get_the_error_message_details_title(),
				'error_body'               => $error_body,
				'link_url'                 => $link_url,
				'link_label'               => $link_label,
			],
			$echo
		);
	}

	/**
	 * Render no hosts found template.
	 *
	 * @since 1.9.0
	 *
	 * @param bool $echo Whether to echo the template to the page or not.
	 *
	 * @return string The rendered template contents.
	 */
	public function render_no_hosts_found( $echo = true ) {
		return $this->template->template(
			'virtual-metabox/api/account-disabled',
			[
				'api_id'         => static::$api_id,
				'disabled_title' => $this->get_no_hosts_found_title(),
				'disabled_body'  => $this->get_no_hosts_found_message(),
				'link_url'       => $this->settings::admin_url(),
				'link_label'     => _x(
						'Refresh your account on the settings page',
						'The label of the button to link back to the settings to refresh an API account.',
						'events-virtual'
					),
				'echo'           => true,
			],
			$echo
		);
	}
}
