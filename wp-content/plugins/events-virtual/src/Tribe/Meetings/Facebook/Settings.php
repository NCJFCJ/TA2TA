<?php
/**
 * Manages the Facebook Live settings.
 *
 * @since   1.7.0
 *
 * @package Tribe\Events\Virtual\Meetings\Facebook
 */

namespace Tribe\Events\Virtual\Meetings\Facebook;

use Tribe__Settings;
use Tribe\Events\Virtual\Traits\With_AJAX;
use Tribe__Settings_Manager as Manager;
use Tribe__Main as Common;
use \Tribe\Events\Admin\Settings as TEC_Settings;

/**
 * Class Settings
 *
 * @since   1.7.0
 *
 * @package Tribe\Events\Virtual\Meetings\Facebook
 */
class Settings {

	use With_AJAX;

	/**
	 * The name of the action used to save a Facebook app id and secret.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	public static $save_app_action = 'events-virtual-meetings-facebook-save-app';

	/**
	 * The name of the action used to add an account.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	public static $add_action = 'events-virtual-meetings-facebook-page-add';

	/**
	 * The name of the action used to delete a Facebook Page.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	public static $delete_action = 'events-virtual-meetings-facebook-page-delete';

	/**
	 * The name of the action used to save a Facebook Page.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	public static $save_action = 'events-virtual-meetings-facebook-page-save';

	/**
	 * The name of the action used to save a Facebook Page's access token.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	public static $save_access_action = 'events-virtual-meetings-facebook-page-access-save';

	/**
	 * The name of the action used to clear a Facebook Page's access token.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	public static $clear_access_action = 'events-virtual-meetings-facebook-page-access-clear';

	/**
	 * The prefix, in the context of tribe options, of each setting for this extension.
	 *
	 * @since 1.7.0
	 *
	 * @var string
	 */
	public static $option_prefix = 'tribe_facebook_';

	/**
	 * Returns the URL of the Settings URL page.
	 *
	 * @since 1.7.0
	 *
	 * @return string The URL of the Facebook Live API integration settings page.
	 */
	public static function admin_url() {
		$admin_page_url = tribe( TEC_Settings::class )->get_url( [ 'tab' => 'addons' ] );

		return $admin_page_url;
	}

	/**
	 * Adds the Facebook Live API fields to the ones in the Events > Settings > APIs tab.
	 *
	 * @since 1.7.0
	 * @since 1.8.0 - Add setting section for Facebook Video to disable the JS SDK.
	 *
	 * @param array<string,array> $fields The current fields.
	 *
	 * @return array<string,array> The fields, as updated by the settings.
	 */
	public function add_fields( array $fields = [] ) {
		$live_wrapper_classes = tribe_get_classes( [
			'tribe-settings-facebook-integration' => true,
			'tribe-common'                        => true,
		] );

		$facebook_fields = [
			// Facebook Live.
			$this->get_prefix( 'wrapper_open' )  => [
				'type' => 'html',
				'html' => '<div id="tribe-settings-facebook-integration" class="' . implode( ' ', $live_wrapper_classes ) . '">',
			],
			$this->get_prefix( 'header' )        => [
				'type' => 'html',
				'html' => $this->get_intro_text(),
			],
			$this->get_prefix( 'app_open' )  => [
				'type' => 'html',
				'html' => '<div class="tribe-settings-facebook-application__container">',
			],
			$this->get_prefix( 'app_id' )     => [
				'type'            => 'text',
				'label'           => esc_html__( 'Facebook App ID', 'events-virtual' ),
				'placeholder'     => esc_html_x( 'Enter your Facebook App ID.', 'The Facebook App ID to use for Facebook Live.', 'events-virtual' ),
				'validation_type' => 'html',
			],
			$this->get_prefix( 'app_secret' ) => [
				'type'            => 'text',
				'label'           => esc_html__( 'Facebook App Secret', 'events-virtual' ),
				'placeholder'     => esc_html_x( 'Enter your Facebook App Secret.', 'The Facebook App secret key to use for Facebook Live.', 'events-virtual' ),
				'validation_type' => 'html',
			],
			$this->get_prefix( 'app_close' ) => [
				'type' => 'html',
				'html' => '</div>',
			],
			$this->get_prefix( 'find_app_id' ) => [
				'type' => 'html',
				'html' => $this->get_find_app_id(),
			],
			$this->get_prefix( 'authorize' )     => [
				'type' => 'html',
				'html' => $this->get_authorize_fields(),
			],
			$this->get_prefix( 'wrapper_close' ) => [
				'type' => 'html',
				'html' => '<div class="clear"></div></div>',
			],
			// Facebook Video.
			$this->get_prefix( 'header-video' )      => [
				'type' => 'html',
				'html' => '<h3>' . esc_html_x( 'Facebook Video', 'The label for the Facebook Video settings.', 'events-virtual' ) . '</h3>',
			],
			$this->get_prefix( 'disable_fb_js_sdk' ) => [
				'type'            => 'checkbox_bool',
				'label'           => esc_html_x( 'Disable Facebook JS SDK for Facebook Video', 'The label to disable Facebook JS sdk.', 'events-virtual' ),
				'tooltip'         => esc_html_x( 'Disable the Facebook JS SDK script for single events on the frontend. This may be necessary to prevent conflicts with other Facebook plugins or scripts.', 'The tooltip for the option to disable the Facebook JS SDK.', 'events-virtual' ),
				'default'         => false,
				'validation_type' => 'boolean',
			],
		];

		/**
		 * Filters the Facebook Live API settings shown to the user in the Events > Settings > APIs screen.
		 *
		 * @since  1.7.0
		 *
		 * @param array<string,array> A map of the Facebook Live API fields that will be printed on the page.
		 * @param Settings $this This Settings instance.
		 */
		$facebook_fields = apply_filters( 'tribe_events_virtual_meetings_facebook_live_settings_fields', $facebook_fields, $this );

		// Insert the link after the other APIs and before the Google Maps API ones.
		$fields = Common::array_insert_before_key( 'gmaps-js-api-start', $fields, $facebook_fields );

		return $fields;
	}

	/**
	 * Get the prefix for the settings.
	 *
	 * @since 1.7.0
	 *
	 * @param string $key The option key to add the prefix to.
	 *
	 * @return string The option key with prefix added.
	 */
	public static function get_prefix( $key ) {
		return static::$option_prefix . $key;
	}

	/**
	 * Get the prefix for the settings.
	 *
	 * @since 1.7.0
	 *
	 * @param string $key     The option key to add the prefix to.
	 * @param mixed  $default The default option for the key.
	 *
	 * @return mixed The options value or default value.
	 */
	public static function get_option( $key, $default = '' ) {
		return Manager::get_option( static::get_prefix( $key ), $default );
	}

	/**
	 * Provides the introductory text to the set up and configuration of the Facebook API integration.
	 *
	 * @since 1.7.0
	 *
	 * @return string The introductory text to the the set up and configuration of the Facebook API integration.
	 */
	protected function get_intro_text() {
		return tribe( Template_Modifications::class )->get_intro_text();
	}

	/**
	 * The information message to help get the Facebook app id and secret.
	 *
	 * @since 1.7.0
	 *
	 * @return string  The information text to help get the Facebook app id and secret.
	 */
	protected function get_find_app_id() {
		return tribe( Template_Modifications::class )->get_find_app_id();
	}

	/**
	 * Get the Page authorization fields.
	 *
	 * @since 1.7.0
	 *
	 * @return string The HTML fields.
	 */
	protected function get_authorize_fields() {
		$app_id     = tribe_get_option( $this->get_prefix( 'app_id' ), '' );
		$app_secret = tribe_get_option( $this->get_prefix( 'app_secret' ), '' );

		// If no app id or app secret add a button so they can be saved.
		if( ! $app_id || ! $app_secret ) {
			return tribe( Template_Modifications::class )->get_save_button();
		}

		return tribe( Template_Modifications::class )->get_page_authorize_fields();
	}

	/**
	 * The message template to display on the integrations.
	 *
	 * @since 1.7.0
	 *
	 * @param string $message The message to display.
	 * @param string $type    The type of message, either standard or error.
	 *
	 * @return string The message with html to display
	 */
	public function get_settings_message_template( $message, $type = 'standard' ) {
		return tribe( Template_Modifications::class )->get_settings_message_template( $message, $type );
	}

	/**
	 * Handles the request to save the Facebook app id and secret.
	 *
	 * @since 1.7.0
	 *
	 * @param string|null $nonce The nonce that should accompany the request.
	 *
	 * @return bool Whether the request was handled or not.
	 */
	public function save_app( $nonce = null ) {
		if ( ! $this->check_ajax_nonce( static::$save_app_action, $nonce ) ) {
			return false;
		}

		$facebook_app_id          = tribe_get_request_var( 'facebook_app_id' );
		$existing_facebook_app_id = tribe_get_option( $this->get_prefix( 'app_id' ) );
		// If app id found, fail the request.
		if ( empty( $facebook_app_id ) ) {
			$error_message = _x(
				'The Facebook App ID field is missing.',
				'Facebook App ID is missing error message.',
				'events-virtual'
			);
			$this->get_settings_message_template( $error_message, 'error' );

			wp_die();
		}

		$facebook_app_secret          = tribe_get_request_var( 'facebook_app_secret' );
		$existing_facebook_app_secret = tribe_get_option( $this->get_prefix( 'app_secret' ) );
		// If no app secret found, fail the request.
		if ( empty( $facebook_app_secret ) ) {
			$error_message = _x(
				'The Facebook App Secret field is missing.',
				'Facebook App Secret is missing error message.',
				'events-virtual'
			);
			$this->get_settings_message_template( $error_message, 'error' );

			wp_die();
		}

		// Save the App ID and Secret if different.
		// Set to true by default and if the save fails it will be false.
		$success_id = $success_secret = true;
		if ( $existing_facebook_app_id !== $facebook_app_id ) {
			$success_id = tribe_update_option( $this->get_prefix( 'app_id' ), $facebook_app_id );
		}
		if ( $existing_facebook_app_secret !== $facebook_app_secret ) {
			$success_secret = tribe_update_option( $this->get_prefix( 'app_secret' ), $facebook_app_secret );
		}

		if ( $success_id && $success_secret ){
			$message = _x(
				'The Facebook App was successfully saved.',
				'The message after a Facebook app id and secret have been saved.',
				'events-virtual'
			);

			// Send back the success message and the page authorize fields.
			$this->get_settings_message_template( $message );
			echo tribe( Template_Modifications::class )->get_page_authorize_fields();

			wp_die();
		}

		$error_message = _x(
			'The Facebook App was not saved.',
			'The message after a Facebook app id and secret did not save successfully.',
			'events-virtual'
		);
		$this->get_settings_message_template( $error_message, 'error' );

		wp_die();
	}
}
