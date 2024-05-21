<?php
/**
 * Abstract template modifications class for API integrations.
 *
 * @since 1.13.0
 *
 * @package Tribe\Events\Virtual\Integrations
 */

namespace Tribe\Events\Virtual\Integrations;

use Tribe\Events\Virtual\Template;
use Tribe\Events\Virtual\Admin_Template;

/**
 * Class Template_Modifications
 *
 * @since 1.13.0
 *
 * @package Tribe\Events\Virtual\Integrations
 */
abstract class Abstract_Template_Modifications {

	/**
	 * The internal id of the API integration.
	 *
	 * @since 1.13.0
	 *
	 * @var string
	 */
	public static $api_id = '';

	/**
	 *  The prefix, in the context of tribe options, of each setting for an API.
	 *
	 * @since 1.13.0
	 *
	 * @var string
	 */
	public static $option_prefix = '';

	/**
	 * An instance of the front-end template handler.
	 *
	 * @since 1.13.0
	 *
	 * @var Template
	 */
	protected $template;

	/**
	 * An instance of the admin template handler.
	 *
	 * @since 1.13.0
	 *
	 * @var Admin_Template
	 */
	protected $admin_template;

	/**
	 * Template_Modifications constructor.
	 *
	 * @since 1.13.0
	 *
	 * @param Template       $template An instance of the front-end template handler.
	 * @param Admin_Template $template An instance of the backend template handler.
	 */
	public function __construct( Template $template, Admin_Template $admin_template ) {
		$this->template       = $template;
		$this->admin_template = $admin_template;
		$this->setup();
	}

	/**
	 * Setup the child class properties.
	 *
	 * @since 1.13.0
	 */
	abstract public function setup();

	/**
	 * Get intro text for an API Settings.
	 *
	 * @since 1.13.0
	 *
	 * @return string HTML for the intro text.
	 */
	public function get_intro_text() {
		$args = [
			'allowed_html' => [
				'a' => [
					'href'   => [],
					'target' => [],
				],
			],
		];

		return $this->admin_template->template( static::$api_id . '/api/intro-text', $args, false );
	}
	/**
	 * Adds an API authorize fields to events->settings->api.
	 *
	 * @since 1.13.0
	 *
	 * @param Request_Api $api An instance of an API handler.
	 * @param Abstract_Url $url The URLs handler for the integration.
	 *
	 * @return string HTML for the authorize fields.
	 */
	public function get_api_authorize_fields( Request_Api $api, Abstract_Url $url ) {
		/** @var \Tribe__Cache $cache */
		$cache   = tribe( 'cache' );
		$message = $cache->get_transient( static::$option_prefix . 'account_message' );
		if ( $message ) {
			$cache->delete_transient( static::$option_prefix . 'account_message' );
		}

		$args = [
			'api'     => $api,
			'url'     => $url,
			'message' => $message,
		];

		return $this->admin_template->template( static::$api_id . '/api/authorize-fields', $args, false );
	}

	/**
	 * Gets settings connect link.
	 *
	 * @since 1.13.0
	 *
	 * @param Request_Api $api An instance of an API handler.
	 * @param Abstract_Url $url The URLs handler for the integration.
	 *
	 * @return string HTML for API connect link.
	 */
	public function get_connect_link( Request_Api $api, Abstract_Url $url ) {
		$args = [
			'api' => $api,
			'url' => $url,
		];

		return $this->admin_template->template( static::$api_id . '/api/authorize-fields/add-link', $args, false );
	}

	/**
	 * The message template to display on user account changes for an API.
	 *
	 * @since 1.13.0
	 *
	 * @param string $message The message to display.
	 * @param string $type    The type of message, either updated or error.
	 *
	 * @return string The message with html to display
	 */
	public function get_settings_message_template( $message, $type = 'updated' ) {
		return $this->admin_template->template( 'components/message', [
			'message' => $message,
			'type'    => $type,
		] );
	}

	/**
	 * Adds an API's details to an event single template.
	 *
	 * @since 1.13.0
	 */
	public function add_event_single_api_details() {
		// Don't show on password protected posts.
		if ( post_password_required() ) {
			return;
		}

		$event  = tribe_get_event( get_the_ID() );
		$api_id = static::$api_id;

		if (
			empty( $event->virtual )
			|| empty( $event->virtual_meeting )
			|| empty( $event->virtual_should_show_embed )
			|| empty( $event->virtual_meeting_display_details )
			|| $api_id !== $event->virtual_meeting_provider
		) {
			return;
		}

		/**
		 * Filters whether the link button should open in a new window or not.
		 *
		 * @since 1.13.0
		 *
		 * @param boolean $link_button_new_window  Boolean of if link button should open in new window.
		 */
		$link_button_new_window = apply_filters( 'tec_events_virtual_link_button_new_window', false );

		$link_button_attrs = [];
		if ( ! empty( $link_button_new_window ) ) {
			$link_button_attrs['target'] = '_blank';
		}

		/**
		 * Filters whether an API link should open in a new window or not.
		 *
		 * @since 1.13.0
		 *
		 * @param boolean $api_link_new_window Boolean of if an API link should open in new window.
		 */
		$api_link_new_window = apply_filters( "tec_events_virtual_{$api_id}_link_new_window", false );

		$api_link_attrs = [];
		if ( ! empty( $api_link_new_window ) ) {
			$api_link_attrs['target'] = '_blank';
		}

		$context = [
			'event'                => $event,
			'link_button_attrs'    => $link_button_attrs,
			"{$api_id}_link_attrs" => $api_link_attrs,
		];

		$this->template->template( "{$api_id}/single/{$api_id}-details", $context );
	}
}
