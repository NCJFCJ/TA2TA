<?php
/**
 * Handles the rendering of the Classic Editor controls.
 *
 * @since   1.7.0
 *
 * @package Tribe\Events\Virtual\Meetings\Facebook
 */

namespace Tribe\Events\Virtual\Meetings\Facebook;

use Tribe\Events\Virtual\Admin_Template;
use Tribe\Events\Virtual\Meetings\Facebook\Event_Meta as Facebook_Meta;

/**
 * Class Classic_Editor
 *
 * @since   1.7.0
 *
 * @package Tribe\Events\Virtual\Meetings\Facebook
 */
class Classic_Editor {

	/**
	 * The template handler instance.
	 *
	 * @since 1.7.0
	 *
	 * @var Admin_Template
	 */
	protected $template;

	/**
	 * An instance of the Facebook Page API handler.
	 *
	 * @since 1.7.0
	 *
	 * @var Page_API
	 */
	protected $page_api;

	/**
	 * An instance of the Facebook Setting handler.
	 *
	 * @since 1.8.0
	 *
	 * @var Settings
	 */
	protected $settings;

	/**
	 * Classic_Editor constructor.
	 *
	 * @param Admin_Template $template An instance of the Template class to handle the rendering of admin views.
	 * @param Page_API       $page_api An instance of the Facebook Page API handler.
	 * @param Settings           $settings           An instance of the Settings handler.
	 */
	public function __construct( Admin_Template $template, Page_API $page_api, Settings $settings ) {
		$this->template = $template;
		$this->page_api = $page_api;
		$this->settings = $settings;
	}

	/**
	 * Renders, echoing to the page, the Facebook Integration fields.
	 *
	 * @since 1.7.0
	 *
	 * @param null|\WP_Post|int $post            The post object or ID of the event to generate the controls for, or `null` to use
	 *                                           the global post object.
	 * @param bool              $echo            Whether to echo the template contents to the page (default) or to return it.
	 *
	 * @return string The template contents, if not rendered to the page.
	 */
	public function render_setup_options( $post = null, $echo = true ) {
		$post = tribe_get_event( get_post( $post ) );

		if ( ! $post instanceof \WP_Post ) {
			return '';
		}

		// Make sure to apply the Facebook properties to the event.
		Facebook_Meta::add_event_properties( $post );

		// Get the current Facebook Pages
		$pages = $this->page_api->get_formatted_page_list( true, $post->facebook_local_id );

		if ( empty( $pages ) ) {
			return $this->render_incomplete_setup();
		}

		return $this->template->template(
			'virtual-metabox/facebook/controls',
			[
				'event' => $post,
				'pages' => [
					'label'    => _x(
						'Choose Page:',
						'The label of Facebook Page to choose.',
						'events-virtual'
					),
					'id'       => 'tribe-events-virtual-facebook-page',
					'class'    => 'tribe-events-virtual-meetings-facebook__page-dropdown',
					'name'     => 'tribe-events-virtual[facebook_local_id]',
					'selected' => $post->facebook_local_id,
					'attrs'    => [
						'placeholder'        => _x(
						    'Select a Facebook Page',
						    'The placeholder for the dropdown to select a Facebook Page.',
						    'events-virtual'
						),
						'data-prevent-clear' => '1',
						'data-options'       => json_encode( $pages ),
					],
				],
			],
			$echo
		);
	}

	/**
	 * Get the incomplete Facebook setup template.
	 *
	 * @since 1.7.0
	 *
	 * @param bool $echo Whether to echo the template contents to the page (default) or to return it.
	 *
	 * @return string The template contents, if not rendered to the page.
	 */
	public function render_incomplete_setup( $echo = true ) {

		return $this->template->template(
			'virtual-metabox/facebook/incomplete-setup',
			[
				'disabled_title' => _x(
					'Facebook Live',
					'The title of Facebook Live incomplete setup message.',
					'events-virtual'
				),
				'disabled_body'  => _x(
					'No connected Facebook Pages found. You must connect a Facebook App to your site before you can add Facebook Live videos to events.',
					'The message to complete the Facebook setup.',
					'events-virtual'
				),
				'link_url'       => Settings::admin_url(),
				'link_label'     => _x(
					'Set up Facebook Live',
					'The label of the link to setup Facebook Live.',
					'events-virtual'
				),
			],
			 $echo
		);
	}

	/**
	 * Add the Facebook video message to autodetect fields.
	 *
	 * @since 1.8.0
	 *
	 * @param array<string|mixed> $autodetect_fields An array of the autodetect results.
	 * @param string              $video_url         The url to use to autodetect the video source.
	 * @param string              $video_source      The optional name of the video source to attempt to autodetect.
	 * @param \WP_Post|null       $event             The event post object, as decorated by the `tribe_get_event` function.
	 * @param array<string|mixed> $ajax_data         An array of extra values that were sent by the ajax script.
	 *
	 * @return array<string|mixed> An array of the autodetect results.
	 */
	public function classic_autodetect_video_source_message( $autodetect_fields, $video_url, $video_source, $event, $ajax_data ) {
		if ( ! $event instanceof \WP_Post ) {
			return $autodetect_fields;
		}

		// All video sources are checked on the first autodetect run, only prevent checking of this source if it is set.
		if ( ! empty( $video_source ) && Facebook_Meta::$autodetect_fb_video_id !== $video_source ) {
			return $autodetect_fields;
		}

		// If app id return fields.
		if( tribe_get_option( $this->settings->get_prefix( 'app_id' ), '' ) ) {
			return $autodetect_fields;
		}

		// If no app id return the no Facebook App ID message.
		$autodetect_fields[] = $this->page_api->get_no_facebook_app_id_message_content();

		return $autodetect_fields;
	}
}
