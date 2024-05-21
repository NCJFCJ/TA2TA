<?php
/**
 * Handles the templates modifications required by the YouTube API integration.
 *
 * @since 1.6.0
 *
 * @package Tribe\Events\Virtual\Meetings\YouTube
 */

namespace Tribe\Events\Virtual\Meetings\YouTube;

use Tribe\Events\Virtual\Meetings\YouTube\Url;
use Tribe\Events\Virtual\Template;
use Tribe\Events\Virtual\Admin_Template;
use Tribe\Events\Virtual\Template_Modifications as Base_Modifications;

/**
 * Class Template_Modifications
 *
 * @since 1.6.0
 *
 * @package Tribe\Events\Virtual\Meetings\YouTube
 */
class Template_Modifications {

	/**
	 * An instance of the front-end template handler.
	 *
	 * @since 1.6.0
	 *
	 * @var Template
	 */
	protected $template;

	/**
	 * An instance of the admin template handler.
	 *
	 * @since 1.6.0
	 *
	 * @var Template
	 */
	protected $admin_template;

	/**
	 * The URL handler instance.
	 *
	 * @since 1.6.0
	 *
	 * @var Url
	 */
	protected $url;

	/**
	 * An instance of the Settings handler.
	 *
	 * @since 1.6.0
	 *
	 * @var Settings
	 */
	protected $settings;

	/**
	 * An instance of base Virtual Event template modifications service.
	 *
	 * @since 1.6.0
	 *
	 * @var Base_Modifications
	 */
	protected $base_modifications;

	/**
	 * Template_Modifications constructor.
	 *
	 * @since 1.6.0
	 *
	 * @param Template           $template           An instance of the front-end template handler.
	 * @param Admin_Template     $template           An instance of the backend template handler.
	 * @param Url                $url                An instance of the URL handler.
	 * @param Settings           $settings           An instance of the Settings handler.
	 * @param Base_Modifications $base_modifications An instance of base Virtual Event template modifications instance.
	 */
	public function __construct(
		Template $template,
		Admin_Template $admin_template,
		Url $url,
		Settings $settings,
		Base_Modifications $base_modifications
	) {
		$this->template           = $template;
		$this->admin_template     = $admin_template;
		$this->url                = $url;
		$this->settings           = $settings;
		$this->base_modifications = $base_modifications;
	}

	/**
	 * Get intro text for YouTube API UI
	 *
	 * @since 1.6.0
	 *
	 * @return string HTML for the intro text.
	 */
	public function get_intro_text() {
		$message = get_transient( $this->settings->get_prefix('account_message' ) );
		if ( $message ) {
			delete_transient( $this->settings->get_prefix( 'account_message' ) );
		}

		return $this->admin_template->template( 'youtube/intro-text', [ 'message' => $message, ], false );
	}

	/**
	 * Adds YouTube authorize fields to events->settings->api.
	 *
	 * @since 1.6.0
	 *
	 * @param array<string|mixed> $fields The array of values for switch fields.
	 *
	 * @return string HTML for the authorize fields.
	 */
	public function get_default_fields( $fields ) {
		$args = [
			'label' => _x( 'Default Settings', 'The default settings for YouTube Integration.', 'events-virtual' ),
			'id' => 'tribe-events-virtual-meetings-youtube-settings__defaults',
			'classes_wrap' => [ 'tribe-events-virtual-meetings-youtube-settings__accordion-wrapper' ],
			'panel' => $this->admin_template->template( 'youtube/components/panel', [ 'fields' => $fields ], false ),
			'expanded' => false,
		];

		return $this->admin_template->template( 'components/accordion', $args, false );
	}

	/**
	 * Add the channel trash icon with AJAX url.
	 *
	 * @since 1.6.0
	 *
	 * @param string              $html  The html for the end of the field.
	 * @param array<string|mixed> $field An array of the field attributes.
	 *
	 * @return string The html for the trash icon along with remaining field html.
	 */
	public function setup_channel_trash_icon( $html, $field ) {
		if ( $field->id !== $this->settings->get_prefix( 'channel_id' ) ) {
			return $html;
		}

		return $this->admin_template->template(
			'youtube/components/trash',
			[
				'disabled' =>  $field->value ? false : true,
				'url' => $this->url->to_delete_channel_id(),
			],
			false
		) . $html;
	}

	/**
	 * The message template to display on user account changes.
	 *
	 * @since 1.6.0
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
	 * Adds YouTube video embed to a single event.
	 *
	 * @since 1.6.0
	 *
	 * @return string The embed html or empty string when it should not display.
	 */
	public function add_youtube_video_embed() {
		// Don't show on password protected posts.
		if ( post_password_required() ) {
			return;
		}

		$event = tribe_get_event( get_the_ID() );
		if ( ! $event instanceof \WP_Post ) {
			return;
		}

		// Only embed when the source is video.
		if ( 'youtube' !== $event->virtual_video_source ) {
			return;
		}

		// Hide on a past event.
		if ( tribe_is_past_event( $event ) ) {
			return;
		}

		// Don't show if requires log in and user isn't logged in.
		if ( ! $this->base_modifications->should_show_virtual_content( $event ) ) {
			return;
		}

		if ( ! $event->virtual_embed_video ) {
			return;
		}

		if ( ! $event->virtual_should_show_embed ) {
			return;
		}

		if ( Connection::$offline_key === $event->virtual_meeting_is_live ) {
			$context = [
				'event'         => $event,
				'offline'           => esc_html_x(
					'The Live Stream is Offline.',
					'YouTube offline message',
					'events-virtual'
				),
			];
			$this->template->template( 'youtube/single/youtube-embed-offline', $context );

			return;
		}

		/** @var \Tribe\Events\Virtual\Meetings\YouTube\Embeds $embeds */
		$embeds    = tribe( Embeds::class );
		$live_chat = $embeds->get_live_chat( $event );
		$embed     = $embeds->get_embed( $event );

		$embed_classes = [];
		if ( $event->youtube_live_chat ) {
			$embed_classes[] = 'chat-enabled';
		}

		$context = [
			'event'         => $event,
			'embed_classes' => $embed_classes,
			'embed'         => $embed,
			'live_chat'     => $live_chat,
		];

		$this->template->template( 'youtube/single/youtube-embed', $context );
	}
}
