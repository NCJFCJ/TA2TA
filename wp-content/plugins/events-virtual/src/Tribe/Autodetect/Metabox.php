<?php
/**
 * Handles the rendering of the metabox for autodetect.
 *
 * @since   1.8.0
 *
 * @package Tribe\Events\Virtual
 */

namespace Tribe\Events\Virtual\Autodetect;

use Tribe\Events\Virtual\Admin_Template;
use Tribe\Events\Virtual\Metabox as Virtual_Metabox;
use Tribe\Events\Virtual\OEmbed;

/**
 * Class Metabox.
 *
 * @since   1.8.0
 *
 * @package Tribe\Events\Virtual\Autodetect
 */
class Metabox {

	/**
	 * Stores the admin template class used.
	 *
	 * @since 1.8.0
	 *
	 * @var Admin_Template
	 */
	public $admin_template;

	/**
	 * The URL handler instance.
	 *
	 * @since 1.8.0
	 *
	 * @var URL
	 */
	protected $url;

	/**
	 * The OEmbed handler instance.
	 *
	 * @since 1.8.0
	 *
	 * @var URL
	 */
	protected $oembed;

	/**
	 * Stores the Fields class used.
	 *
	 * @since 1.8.0
	 *
	 * @var Fields
	 */
	protected $fields;

	/**
	 * Metabox constructor.
	 *
	 * @since 1.8.0
	 *
	 * @param Admin_Template $admin_template An instance of the plugin admin_template handler.
	 * @param Url            $url            An instance of the Autodetect URL handler.
	 * @param OEmbed         $oembed         An instance of the OEmbed handler.
	 * @param Fields         $fields         An instance of the autodetect fields handler.
	 */
	public function __construct( Admin_Template $admin_template, Url $url, OEmbed $oembed, Fields $fields ) {
		$this->admin_template = $admin_template;
		$this->url            = $url;
		$this->oembed         = $oembed;
		$this->fields         = $fields;
	}

	/**
	 * Renders the autodetect fields.
	 *
	 * @since 1.8.0
	 *
	 * @param null|\WP_Post|int $post            The post object or ID of the event to generate the controls for, or `null` to use
	 *                                           the global post object.
	 * @param bool              $echo            Whether to echo the template contents to the page (default) or to return it.
	 *
	 * @return string The template contents, if not rendered to the page or empty string if no post object.
	 */
	public function classic_autodetect_video_source_ui( $post = null, $echo = true ) {
		$event = tribe_get_event( $post );

		if ( ! $event instanceof \WP_Post ) {
			return '';
		}

		$metabox_id = Virtual_Metabox::$id;

		// Get the saved autodetect source and display the autodetect source dropdown if a value found.
		$autodetect_fields = [ 'video' ];
		if ( $event->virtual_autodetect_source ) {
			$autodetect_fields = [ 'video', 'video-source' ];
		}

		return $this->admin_template->template( 'virtual-metabox/autodetect/controls', [
			'autodetect_fields'          => $this->fields->get_autodetect_fields(
				$autodetect_fields,
				$event->virtual_url,
				$event->virtual_autodetect_source,
				$event,
				[]
			),
			'autodetect_message_classes' => [ 'tribe-events-virtual-video-source-autodetect__messages-wrap' ],
			'event'                      => $post,
			'metabox_id'                 => $metabox_id,
			'url'                        => $this->url,
		], $echo );
	}

	/**
	 * Filter the autodetect source to detect the source.
	 *
	 * @since 1.8.0
	 *
	 * @param array<string|mixed> $autodetect An array of the autodetect results.
	 * @param string              $video_url  The url to use to autodetect the video source.
	 * @param \WP_Post|null       $event      The event post object, as decorated by the `tribe_get_event` function.
	 * @param array<string|mixed> $ajax_data  An array of extra values that were sent by the ajax script.
	 *
	 * @return array<string|mixed> An array of the autodetect results.
	 */
	public function classic_autodetect_video_source_dropdown( $autodetect_fields, $video_url, $autodetect_source, $event, $ajax_data ) {
		if ( ! $event instanceof \WP_Post ) {
			return $autodetect_fields;
		}

		$metabox_id = Virtual_Metabox::$id;

		/**
		 * Allow filtering of the virtual autodetect video sources.
		 *
		 * @since 1.8.0
		 *
		 * @param array<string|string> An array of autodetect video sources.
		 * @param string $autodetect_source The ID of the current selected video source.
		 * @param \WP_Post $event The current event post object, as decorated by the `tribe_get_event` function.
		 */
		$autodetect_sources =  (array) apply_filters( 'tec_events_virtual_autodetect_video_sources', [], $autodetect_source, $event );

		$autodetect_fields[] = [
			'path' => 'components/dropdown',
			'field' => [
				'label'        => _x(
					'Video source:',
					'The label to choose the Smart URL/autodetect source.',
					'events-virtual'
				),
				'id'           => "{$metabox_id}-autodetect-source",
				'class'        => 'tribe-events-virtual-meetings-autodetect-source__dropdown',
				'classes_wrap' => ['tribe-events-virtual-meetings-autodetect-source__dropdown--wrap'],
				'name'         => "tribe-events-virtual-autodetect[autodetect-source]",
				'selected'     => $autodetect_source,
				'attrs'        => [
					'placeholder' 	   => _x(
						'Choose a video source:',
						'The placeholder for the dropdown to choose the virtual Smart URL/autodetect source.',
						'events-virtual'
					),
					'data-selected'      => $autodetect_source,
					'data-prevent-clear' => true,
					'data-hide-search'   => '1',
					'data-options'       => json_encode( $autodetect_sources ),
				],
				'tooltip'      => [
					'classes_wrap'  => [ 'tec-events-virtual-meetings-autodetect-source__dropdown--tooltip' ],
					'message'   => _x(
						'Video source is detected based on the URL you’ve entered. If no video was found, you may need to manually select a different video source from the dropdown below and click Find.',
						'Explains what the smart url/autodetect video source is used for.',
						'events-virtual'
					),
				],
			]
		];

		return $autodetect_fields;
	}
}
