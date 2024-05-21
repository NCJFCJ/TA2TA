<?php
/**
 * Handles the rendering of the Classic Editor controls.
 *
 * @since   1.6.0
 *
 * @package Tribe\Events\Virtual\Meetings\YouTube
 */

namespace Tribe\Events\Virtual\Meetings\YouTube;

use Tribe\Events\Virtual\Admin_Template;
use Tribe\Events\Virtual\Meetings\YouTube\Event_Meta as YouTube_Meta;

/**
 * Class Classic_Editor
 *
 * @since   1.6.0
 *
 * @package Tribe\Events\Virtual\Meetings\YouTube
 */
class Classic_Editor {

	/**
	 * The template handler instance.
	 *
	 * @since 1.6.0
	 *
	 * @var Admin_Template
	 */
	protected $template;

	/**
	 * Classic_Editor constructor.
	 *
	 * @param Admin_Template $template An instance of the Template class to handle the rendering of admin views.
	 */
	public function __construct( Admin_Template $template ) {
		$this->template = $template;
	}

	/**
	 * Renders, echoing to the page, the YouTube Integration fields.
	 *
	 * @since 1.6.0
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

		// Make sure to apply the YouTube properties to the event.
		YouTube_Meta::add_event_properties( $post );

		// Get the current YouTube Fields, it will return an array of saved values or the defaults.
		$fields = YouTube_Meta::get_current_fields( $post );

		return $this->template->template(
			'virtual-metabox/youtube/controls',
			[
				'event'  => $post,
				'fields' => $fields,
			],
			$echo
		);
	}
}
