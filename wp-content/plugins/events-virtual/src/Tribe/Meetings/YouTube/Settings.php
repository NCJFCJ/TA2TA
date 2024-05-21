<?php
/**
 * Manages the YouTube settings for the extension.
 *
 * @since 1.6.0
 *
 * @package Tribe\Events\Virtual\Meetings\YouTube
 */

namespace Tribe\Events\Virtual\Meetings\YouTube;

use Tribe__Settings;
use Tribe\Events\Virtual\Traits\With_AJAX;
use Tribe__Settings_Manager as Manager;
use Tribe__Main as Common;
use \Tribe\Events\Admin\Settings as TEC_Settings;

/**
 * Class Settings
 *
 * @since 1.6.0
 *
 * @package Tribe\Events\Virtual\Meetings\YouTube
 */
class Settings {
	use With_AJAX;

	/**
	 * The prefix, in the context of tribe options, of each setting for this extension.
	 *
	 * @since 1.6.0
	 *
	 * @var string
	 */
	public static $option_prefix = 'tribe_youtube_';

	/**
	 * The name of the action used to delete a channel id..
	 *
	 * @since 1.6.0
	 *
	 * @var string
	 */
	public static $delete_action = 'events-virtual-meetings-youtube-settings-channel-delete';

	/**
	 * Returns the URL of the Settings URL page.
	 *
	 * @since 1.6.0
	 *
	 * @return string The URL of the YouTube API integration settings page.
	 */
	public static function admin_url() {
		$admin_page_url = tribe( TEC_Settings::class )->get_url( [ 'tab' => 'addons' ] );

		return $admin_page_url;
	}

	/**
	 * Adds the YouTube API fields to the ones in the Events > Settings > APIs tab.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string,array> $fields The current fields.
	 *
	 * @return array<string,array> The fields, as updated by the settings.
	 */
	public function add_fields( array $fields = [] ) {
		$wrapper_classes = tribe_get_classes(
			[
				'tribe-settings-youtube-integration' => true,
				'tribe-common' => true,
			]
		);

		$youtube_fields = [
			$this->get_prefix( 'wrapper_open' )  => [
				'type' => 'html',
				'html' => '<div id="tribe-settings-youtube-integration" class="' . implode( ' ', $wrapper_classes ) . '">',
			],
			$this->get_prefix( 'header' )        => [
				'type' => 'html',
				'html' => $this->get_intro_text(),
			],
			$this->get_prefix( 'channel_id' )    => $this->get_channel_id_field(),
			$this->get_prefix( 'defaults' )      => [
				'type' => 'html',
				'html' => $this->get_default_fields(),
			],
			$this->get_prefix( 'wrapper_close' ) => [
				'type' => 'html',
				'html' => '<div class="clear"></div></div>',
			],
		];

		// Merge the fields displayed in the slide toggle.
		$youtube_fields = array_merge( $youtube_fields, $this->get_fields() );

		/**
		 * Filters the YouTube API settings shown to the user in the Events > Settings > APIs screen.
		 *
		 * @since  1.6.0
		 *
		 * @param array<string,array> A map of the YouTube API fields that will be printed on the page.
		 * @param Settings $this This Settings instance.
		 */
		$youtube_fields = apply_filters( 'tribe_events_virtual_meetings_youtube_settings_fields', $youtube_fields, $this );

		// Insert the link after the other APIs and before the Google Maps API ones.
		$fields = Common::array_insert_before_key(
			'gmaps-js-api-start',
			$fields,
			$youtube_fields
		);

		return $fields;
	}

	/**
	 * Provides the introductory text to the set up and configuration of the YouTube API integration.
	 *
	 * @since 1.0.0
	 *
	 * @return string The introductory text to the the set up and configuration of the YouTube API integration.
	 */
	protected function get_intro_text() {
		return tribe( Template_Modifications::class )->get_intro_text();
	}

	/**
	 * Get the API authorization fields.
	 *
	 * @since 1.6.0
	 *
	 * @return string The HTML fields.
	 */
	protected function get_default_fields() {
		$fields = $this->get_fields();

		return tribe( Template_Modifications::class )->get_default_fields( $fields );
	}

	/**
	 * Get the default YouTube fields.
	 *
	 * @since 1.6.0
	 *
	 * @param bool $include_channel_id Whether to include the channel id, default false.
	 *
	 * @return array<string|mixed> The array of values for switch fields.
	 */
	public function get_fields( $include_channel_id = false ) {
		$fields = [
			$this->get_prefix( 'autoplay' )  => [
				'type'            => 'hidden',
				'default'         => false,
				'validation_type' => 'boolean',
				'label'           => esc_html_x(
					'Autoplay Video',
					'YouTube default setting',
					'events-virtual'
				),
				'tooltip'         => esc_html_x(
					'Autoplay is being deprecated by most browsers and is prevented in Chrome and Safari, it maybe removed in all the browsers soon.',
					'YouTube default setting tooltip',
					'events-virtual'
				),
				'value'           => $this->get_option( 'autoplay', false ),
			],
			$this->get_prefix( 'live_chat' )  => [
				'type'            => 'hidden',
				'default'         => false,
				'validation_type' => 'boolean',
				'label'           => esc_html_x(
					'Include live chat',
					'YouTube default setting',
					'events-virtual'
				),
				'tooltip'         => '',
				'value'           => $this->get_option( 'live_chat', false ),
			],
			$this->get_prefix( 'mute_video' ) => [
				'type'            => 'hidden',
				'default'         => false,
				'validation_type' => 'boolean',
				'label'           => esc_html_x(
					'Mute Video',
					'YouTube default setting',
					'events-virtual'
				),
				'tooltip'           => esc_html_x(
					'Best used with autoplay.',
					'YouTube default setting tooltip',
					'events-virtual'
				),
				'value'           => $this->get_option( 'mute_video', false ),
			],
			$this->get_prefix( 'modest_branding' ) => [
				'type'            => 'hidden',
				'default'         => false,
				'validation_type' => 'boolean',
				'label'           => esc_html_x(
					'Modest Branding',
					'YouTube default setting',
					'events-virtual'
				),
				'tooltip'           => esc_html_x(
					'Hides the YouTube logo in the control bar.',
					'YouTube default setting tooltip',
					'events-virtual'
				),
				'value'           => $this->get_option( 'modest_branding', false ),
			],
			$this->get_prefix( 'related_videos' ) => [
				'type'            => 'hidden',
				'default'         => false,
				'validation_type' => 'boolean',
				'label'           => esc_html_x(
					'Restrict Related Videos',
					'YouTube default setting',
					'events-virtual'
				),
				'tooltip'           => esc_html_x(
					'Restricts related videos to only come from your channel.',
					'YouTube default setting tooltip',
					'events-virtual'
				),
				'value'           => $this->get_option( 'related_videos', false ),
			],
			$this->get_prefix( 'hide_controls' ) => [
				'type'            => 'hidden',
				'default'         => false,
				'validation_type' => 'boolean',
				'label'           => esc_html_x(
					'Hide Controls',
					'YouTube default setting',
					'events-virtual'
				),
				'tooltip'           => esc_html_x(
					'Hides the video control bar.',
					'YouTube default setting tooltip',
					'events-virtual'
				),
				'value'           => $this->get_option( 'hide_controls', false ),
			],
		];

		if ( $include_channel_id ) {
			$fields[ $this->get_prefix( 'channel_id' ) ] = $this->get_channel_id_field();
		}

		return $fields;
	}

	/**
	 * Get the prefix for the settings.
	 *
	 * @since 1.6.0
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
	 * @since 1.6.0
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
	 * Get the channel id field.
	 *
	 * @since 1.6.0
	 *
	 * @return array<string|mixed> The channel id field settings.
	 */
	public function get_channel_id_field() {
		$url = 'https://evnt.is/1ap7';
		$channel_tooltip = sprintf(
			'<a href="%1$s" target="_blank">%2$s</a>',
			esc_url( $url ),
			esc_html_x(
				'Click here to find channel ID',
			'Settings help text for finding a YouTube channel id.',
			'events-virtual'
			)
		);

		return [
			'type'            => 'text',
			'label'           => esc_html_x( 'Default YouTube Live Channel ID', 'The field label for the default YouTube channel ID', 'events-virtual' ),
			'placeholder'     => esc_html_x( 'Enter your YouTube Live ID', 'The field placeholder for the default YouTube channel ID', 'events-virtual' ),
			'tooltip'         => $channel_tooltip,
			'validation_type' => 'html',
			'class'           => 'tribe-settings-youtube-integration__channel-id',
			'value'           => $this->get_option( 'channel_id', '' ),
		];
	}

	/**
	 * The message template to display on user account changes.
	 *
	 * @since 1.6.0
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
	 * Handles the request to delete a channel ID.
	 *
	 * @since 1.6.0
	 *
	 * @param string|null $nonce The nonce that should accompany the request.
	 *
	 * @return bool Whether the request was handled or not.
	 */
	public function ajax_delete( $nonce = null ) {

		if ( ! $this->check_ajax_nonce( static::$delete_action, $nonce ) ) {
			return false;
		}

		$youtube_field_id = tribe_get_request_var( 'channel_field_id' );
		// If no field id found, fail the request.
		if ( empty( $youtube_field_id ) ) {
			$error_message = _x( 'The YouTube channel ID field is missing.', 'YouTube channel ID is missing on delete error message.', 'events-virtual' );
			$this->get_settings_message_template( $error_message, 'error' );

			wp_die();
		}

		// Remove the channel id.
		$success =  tribe_update_option( $youtube_field_id, '' );
		if ( $success ){
			$message = _x(
				'The YouTube channel ID was successfully deleted.',
				'The message after a YouTube channel ID has been deleted from the Website.',
				'events-virtual'
			);
			$this->get_settings_message_template( $message );

			wp_die();
		}

		$error_message = _x(
			'The YouTube channel ID could not be deleted.',
			'The message to display if a YouTube channel ID could not be deleted.',
			'events-virtual'
		);
		$this->get_settings_message_template( $error_message, 'error' );

		wp_die();
	}
}
