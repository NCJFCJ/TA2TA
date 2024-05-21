<?php
/**
 * Manages the Autodetect AJAX.
 *
 * @since   1.8.0
 *
 * @package Tribe\Events\Virtual\Autodetect
 */

namespace Tribe\Events\Virtual\Autodetect;

use Tribe\Events\Virtual\Admin_Template;
use Tribe\Events\Virtual\Traits\With_AJAX;
use Tribe__Utils__Array as Arr;

/**
 * Class AJAX
 *
 * @since   1.8.0
 *
 * @package Tribe\Events\Virtual\Autodetect
 */
class AJAX {

	use With_AJAX;

	/**
	 * The name of the action used to detect the video source.
	 *
	 * @since 1.8.0
	 *
	 * @var string
	 */
	public static $autodetect_action = 'events-virtual-autodetect-video-source';

	/**
	 * Stores the admin template class used.
	 *
	 * @since 1.8.0
	 *
	 * @var Admin_Template
	 */
	public $admin_template;

	/**
	 * Stores the Fields class used.
	 *
	 * @since 1.8.0
	 *
	 * @var Fields
	 */
	protected $fields;

	/**
	 * AJAX constructor.
	 *
	 * @since 1.8.0
	 *
	 * @param Admin_Template $admin_template An instance of the plugin template handler.
	 * @param Fields         $fields         An instance of the autodetect fields handler.
	 */
	public function __construct( Admin_Template $admin_template, Fields $fields ) {
		$this->admin_template = $admin_template;
		$this->fields         = $fields;
	}

	/**
	 * Main method to autodetect the video source called by AJAX.
	 *
	 * @since 1.8.0
	 *
	 * @param string $nonce The autodetect action nonce to check.
	 *
	 * @return false|string The html of the autodetect fields or false if not authorized.
	 */
	public function detect_source( $nonce ) {
		if ( ! $this->check_ajax_nonce( static::$autodetect_action, $nonce ) ) {
			return false;
		}

		$video_url     = tribe_get_request_var( 'video_url' );
		// If missing information fail the request.
		if ( empty( $video_url ) ) {
			$error_message = _x(
				'No URL found.',
				'The autodetect no url found message.',
				'events-virtual'
			);
			tribe( Template_Modifications::class )->get_settings_message_template( $error_message, 'error' );

			wp_die();
		}

		// Autodetect default results.
		$autodetect = [
			'detected'     => false,
			'guess'        => '',
			'message'      => '',
			'html'         => '',
			'preview-html' => '',
		];

		// Setup the event object.
		$post_id = tribe_get_request_var( 'post_id' );
		if ( empty( $post_id ) ) {
			$error_message = _x(
				'The post ID is missing from the request.',
				'An error raised in the context of the Autodetect ajax request.',
				'events-virtual'
			);
			tribe( Template_Modifications::class )->get_settings_message_template( $error_message, 'error' );

			wp_die();
		}

		$event   = tribe_get_event( $post_id );
		if ( ! $event instanceof \WP_Post ) {
			$error_message = _x(
				'The event could not be found.',
				'An error raised in the context of the Autodetect ajax request.',
				'events-virtual'
			);
			tribe( Template_Modifications::class )->get_settings_message_template( $error_message, 'error' );

			wp_die();
		}

		// Setup any additional fields that might be sent to narrow down detection.
		$ajax_data = tribe_get_request_var( 'ajax_data', [] );

		// Get optional video source.
		$autodetect_source = Arr::get( $ajax_data, 'autodetect-source', '' );

		/**
		 * Filter the autodetect source to attempt to find the source of the url.
		 *
		 * @since 1.8.0
		 *
		 * @param array<string|mixed> $autodetect        An array of the autodetect defaults.
		 * @param string              $video_url         The url to use to autodetect the video source.
		 * @param string              $autodetect_source The optional name of the video source to attempt to autodetect.
		 * @param \WP_Post|null       $event             The event post object, as decorated by the `tribe_get_event` function.
		 * @param array<string|mixed> $ajax_data         An array of extra values that were sent by the ajax script.
		 *
		 * @return array<string|mixed> An array of the autodetect results.
		 */
		$autodetect = apply_filters( 'tec_events_virtual_autodetect_source', $autodetect, $video_url, $autodetect_source, $event, $ajax_data );

		if (
			! $autodetect['detected'] &&
			! $autodetect_source &&
			! $autodetect['guess']
		) {
			$error_message = _x(
				'No video found. Please check the URL.',
				'The autodetect no video source found message.',
				'events-virtual'
			);

			tribe( Template_Modifications::class )->get_settings_message_template( $error_message, 'error' );
		} else {
			$message_type = $autodetect['detected'] ? 'updated' : 'error';
			tribe( Template_Modifications::class )->get_settings_message_template( $autodetect['message'], $message_type );
		}

		// If a source is detected and there is an autodetect source, replace the passed autodetect source.
		$autodetect_source = $autodetect['detected'] && $autodetect['autodetect-source'] ? $autodetect['autodetect-source'] : $autodetect_source;
		if ( ! $autodetect_source && $autodetect['guess'] ) {
			$autodetect_source = $autodetect['guess'];
		}

		// Get the autodetect fields to display that were added through the filter.
		$this->admin_template->template( 'virtual-metabox/autodetect/fields', [
			'autodetect_fields' => $this->fields->get_autodetect_fields(
				[ 'video', 'video-source', 'all' ],
				$video_url,
				$autodetect_source,
				$event,
				$ajax_data
			),
			'event'             => $event,
		] );

		echo $autodetect['html'];

		// If there is a preview video wrap in the expected div and class.
		if ( ! empty( $autodetect['preview-html'] ) ) {
			echo '<div class="tec-autodetect-video-preview__inner">' . $autodetect['preview-html'] . '</div>';
		}

		wp_die();
	}
}
