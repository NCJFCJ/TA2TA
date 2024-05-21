<?php
/**
 * Handles OEmbed links.
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual
 */

namespace Tribe\Events\Virtual;

use Tribe\Events\Virtual\Autodetect\Template_Modifications;
use Tribe\Events\Virtual\Autodetect\Url;
use Tribe\Events\Virtual\Metabox as Virtual_Metabox;
use Tribe\Events\Virtual\Event_Meta as Virtual_Events_Meta;
use WP_Post;

/**
 * Class OEmbed.
 *
 * @since 1.0.0
 *
 * @package Tribe\Events\Virtual
 */
class OEmbed {

	/**
	 * An instance of the front-end template handler.
	 *
	 * @since 1.8.0
	 *
	 * @var Template
	 */
	protected $template;

	/**
	 * The URL handler instance.
	 *
	 * @since 1.8.0
	 *
	 * @var URL
	 */
	protected $url;

	/**
	 * OEmbed constructor.
	 *
	 * @since 1.8.0
	 *
	 * @param Template $template An instance of the front-end template handler.
	 * @param Url      $url      An instance of the Autodetect URL handler.
	 *
	 */
	public function __construct( Template $template, Url $url ) {
		$this->template = $template;
		$this->url      = $url;
	}

	/**
	 * Adds a wrapper to the oEmbed HTML.
	 *
	 * @param string $html  The returned oEmbed HTML.
	 * @param object $data  A data object result from an oEmbed provider.
	 * @param string $url   The URL of the content to be embedded.
	 *
	 * @return string  The modified oEmbed HTML.
	 */
	public function make_oembed_responsive( $html, $data, $url ) {
		// Verify oembed data (as done in the oEmbed data2html code).
		if ( ! is_object( $data ) || empty( $data->type ) ) {
			return $html;
		}

		if ( empty( $html ) ) {
			return $html;
		}

		/**
		 * Filters whether to make oembed responsive or not.
		 *
		 * @since 1.0.0
		 *
		 * @param boolean $make_oembed_responsive  Boolean of if oembed should be made responsive.
		 */
		$make_oembed_responsive = apply_filters( 'tribe_events_virtual_make_oembed_responsive', true );

		if ( empty( $make_oembed_responsive ) ) {
			return $html;
		}

		$attrs = [ 'class' => 'tribe-events-virtual-single-video-embed__wrapper' ];

		// Add padding if height and width data exists.
		if ( ! empty( $data->height ) && ! empty( $data->width ) ) {
			// Calculate aspect ratio.
			$aspect_ratio = $data->height / $data->width;
			$padding      = round( $aspect_ratio * 100, 2 ) . '%';

			$attrs['style'] = "padding-bottom:$padding";
		}

		// convert attributes to key value HTML.
		$attrs = array_map(
			static function ( $key ) use ( $attrs ) {
				if ( is_bool( $attrs[ $key ] ) ) {
					return $attrs[ $key ] ? $key : '';
				}

				return $key . '=\'' . esc_attr( $attrs[ $key ] ) . '\'';
			},
			array_keys( $attrs )
		);

		$attrs_string = implode( ' ', $attrs );

		// Strip width and height from HTML.
		$html = preg_replace( '/(width|height)="\d*"\s/', '', $html );
		$html = "<div $attrs_string>$html</div>";

		/**
		 * Filters the responsive oembed HTML.
		 *
		 * @since 1.0.0
		 *
		 * @param string $html  oEmbed HTML.
		 * @param object $data  data object result from an oEmbed provider.
		 * @param string $url   URL of the content to be embedded.
		 */
		return apply_filters( 'tribe_events_virtual_responsive_oembed_html', $html, $data, $url );
	}

	/**
	 * Tests if a link is embeddable.
	 *
	 * @since 1.0.0
	 * @since 1.6.1 - Use _wp_get_oembed_get_object() to get Oembed object with custom providers.
	 *
	 * @param string $url The URL to test.
	 * @return boolean
	 */
	public function is_embeddable( $url ) {
		if ( empty( $url ) ) {
			return false;
		}

		$oembed   = _wp_oembed_get_object();
		$provider = $oembed->get_provider( $url, [ 'discover' => false ] );

		return false !== $provider;
	}

	/**
	 * Get the error message for an unembeddable link.
	 *
	 * @since 1.0.0
	 * @since 1.0.4 Changed message to include link to the WordPress oEmbed page.
	 *
	 * @param string $url The unembeddable URL.
	 * @return string Failure message.
	 */
	public function get_unembeddable_message( $url ) {
		$message = sprintf(
			/* Translators: %1$s: opening anchor tag, %2$s: closing anchor tag */
			_x(
				'This video cannot be embedded because the source is not supported by %1$sWordPress oEmbed%2$s.',
				'Tells user that URL cannot be embedded, and links to the WordPress oEmbed page for a list of embeddable sites.',
				'events-virtual'
			),
			'<a href="https://wordpress.org/support/article/embeds/" target="_blank" rel="noopener noreferrer">',
			'</a>'
		);

		/**
		 * Allows filtering of the error message by external objects.
		 *
		 * @since 1.0.0
		 *
		 * @param string $message The error message.
		 * @param string $url     The URL that failed.
		 */
		return apply_filters( 'tribe_events_virtual_get_unembeddable_message', $message, $url );
	}

	/**
	 * Get the success message for an embeddable link.
	 *
	 * @since 1.8.0
	 *
	 * @return string success message.
	 */
	public function get_success_message() {
		$message = _x(
			'Success! Save your event to add this video.',
			'Tells user the URL is embedded and to save to use that url.',
			'events-virtual'
		);

		/**
		 * Allows filtering of the success message for oembed success message.
		 *
		 * @since 1.8.0
		 *
		 * @param string $message The error message.
		 */
		return apply_filters( 'tribe_events_virtual_autodetect_oembed_success_message', $message );
	}

	/**
	 * Get the message for using unsupported oembed as a link button.
	 *
	 * @since 1.8.0
	 *
	 * @param string $url The unembeddable URL.
	 *
	 * @return string  The unsupported oembed as a link button message.
	 */
	public function get_using_as_link_button_message( $url ) {
		$message = sprintf(
			/* Translators: %1$s: opening anchor tag, %2$s: closing anchor tag */
			_x(
				'This video cannot be embedded because the source is not supported by %1$sWordPress oEmbed%2$s. You can link to the video source with a button instead.',
				'Tells user the URL is used in the linked button and that it cannot be embedded, and links to the WordPress oEmbed page for a list of embeddable sites.',
				'events-virtual'
			),
			'<a href="https://wordpress.org/support/article/embeds/" target="_blank" rel="noopener noreferrer">',
			'</a>'
		);

		/**
		 * Allows filtering of the unsupported oembed as a link button message.
		 *
		 * @since 1.8.0
		 *
		 * @param string $message TThe unsupported oembed as a link button message.
		 * @param string $url     The URL that failed.
		 */
		return apply_filters( 'tec_virtual_get_using_as_link_button_message', $message, $url );
	}

	/**
	 * Filter the autodetect source to detect if a WordPress oembed.
	 *
	 * @since 1.8.0
	 *
	 * @param array<string|mixed> $autodetect   An array of the autodetect defaults.
	 * @param string              $video_url    The url to use to autodetect the video source.
	 * @param string              $video_source The optional name of the video source to attempt to autodetect.
	 * @param \WP_Post|null       $event        The event post object, as decorated by the `tribe_get_event` function.
	 * @param array<string|mixed> $ajax_data    An array of extra values that were sent by the ajax script.
	 *
	 * @return array<string|mixed> An array of the autodetect results.
	 */
	public function filter_virtual_autodetect_oembed( $autodetect, $video_url, $video_source, $event, $ajax_data ) {
		if ( $autodetect['detected'] || $autodetect['guess'] ) {
			return $autodetect;
		}

		// All video sources are checked on the first autodetect run, only prevent checking of this source if it is set.
		if ( ! empty( $video_source ) && 'oembed' !== $video_source ) {
			return $autodetect;
		}

		$url  = filter_var( $video_url, FILTER_VALIDATE_URL );
		$test = $this->is_embeddable( $url );

		if ( false === $test || is_wp_error( $test ) ) {
			$autodetect['detected'] = false;
			$autodetect['message'] = $this->get_unembeddable_message( $video_url );

			return $autodetect;
		}

		// Set Oembed as the autodetect source and setup success data and send back to smart url ui.
		update_post_meta( $event->ID, Virtual_Events_Meta::$key_autodetect_source, Virtual_Events_Meta::$key_oembed_source_id );
		$autodetect['detected']          = true;
		$autodetect['autodetect-source'] = Virtual_Events_Meta::$key_oembed_source_id;
		$autodetect['message']           = $this->get_success_message();

		// Preview video setup.
		$event->virtual_url = filter_var( $video_url, FILTER_VALIDATE_URL );
		// Set for the preview video to always show.
		$event->virtual_embed_video       = $event->virtual_should_show_embed = true;
		$event->virtual_autodetect_source = Virtual_Events_Meta::$key_oembed_source_id;
		$autodetect['preview-html'] = $this->template->template( 'single/video-embed', [ 'event' => $event ] );

		return $autodetect;
	}

	/**
	 * Add the video url field for autodetect.
	 *
	 * @since 1.8.0
	 *
	 * @param array<string|mixed> $autodetect   An array of the autodetect defaults.
	 * @param string              $video_url    The url to use to autodetect the video source.
	 * @param string              $video_source The optional name of the video source to attempt to autodetect.
	 * @param \WP_Post|null       $event        The event post object, as decorated by the `tribe_get_event` function.
	 * @param array<string|mixed> $ajax_data    An array of extra values that were sent by the ajax script.
	 *
	 * @return string The html for the video url field.
	 */
	public function add_video_url_autodetect_field( $autodetect_fields, $video_url, $video_source, $event, $ajax_data ) {
		if ( ! $event instanceof \WP_Post ) {
			return '';
		}

		$metabox_id = Virtual_Metabox::$id;

		/**
		 * Allow filtering of the virtual event video url.
		 *
		 * @since 1.8.0
		 *
		 * @param string The virtual url string.
		 * @param \WP_Post $event The current event post object, as decorated by the `tribe_get_event` function.
		 */
		$video_url = apply_filters( 'tec_events_virtual_video_source_virtual_url', $video_url, $event );

		/**
		 * Allow filtering` to disable the video url field.
		 *
		 * @since 1.8.0
		 *
		 * @param bool Whether to disable the video url field or not.
		 * @param \WP_Post $event The current event post object, as decorated by the `tribe_get_event` function.
		 */
		$virtual_url_disabled = apply_filters( 'tec_events_virtual_video_source_virtual_url_disabled', false, $event );

		$autodetect_fields[] = [
			'path' => 'components/text',
			'field' => [
				'classes_wrap'  => [ 'tec-events-virtual-meetings-control', 'tribe-events-virtual-video-source__virtual-url-wrap' ],
				'label'         => _x( 'Video', 'The label for the autodetect video source input.', 'events-virtual' ),
				'id'            => "{$metabox_id}-virtual-url",
				'classes_input' => [ 'tribe-events-virtual-video-source__virtual-url-input' ],
				'name'          => "{$metabox_id}[virtual-url]",
				'placeholder'   => Event_Meta::get_video_source_text( $event ),
				'value'         => $video_url,
				'attrs'         => [
					'data-autodetect-ajax-url' => $this->url->to_autodetect_video_source( $event ),
					'data-dependency-manual-control',
					disabled( $virtual_url_disabled ),
				],
			]
		];

		return $autodetect_fields;
	}

	/**
	 * Add a preview video of Oembed video to autodetect.
	 *
	 * @since 1.8.0
	 *
	 * @param WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
	 *
	 * @return string The video player html, not embedable message, or an empty string.
	 */
	public function autodetect_oembed_preview( $event ) {
		if ( Virtual_Events_Meta::$key_oembed_source_id !== $event->virtual_autodetect_source ) {
			return '';
		}

		$url  = filter_var( $event->virtual_url, FILTER_VALIDATE_URL );
		$test = $this->is_embeddable( $url );
		if ( false === $test || is_wp_error( $test ) ) {
			$linked_button_msg = $this->get_using_as_link_button_message( $event->virtual_url );

			return tribe( Template_Modifications::class )->get_settings_message_template( $linked_button_msg, '' );
		}

		// Set for the preview video to always show in the admin.
		$event->virtual_embed_video = $event->virtual_should_show_embed = true;
		return $this->template->template( 'single/video-embed', [ 'event' => $event ] );
	}
}
