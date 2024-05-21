<?php
/**
 * Keyholder class that provides keys and some default values related to Virtual Events custom fields.
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual
 */

namespace Tribe\Events\Virtual;

use WP_Post;

/**
 * Class Event_Meta.
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual
 */
class Event_Meta {

	/**
	 * Meta key for event type field.
	 *
	 * @since 1.4.0
	 *
	 * @var string
	 */
	public static $key_type = '_tribe_virtual_events_type';

	/**
	 * Meta key for virtual field.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public static $key_virtual = '_tribe_events_is_virtual';

	/**
	 * Meta key for video source field.
	 *
	 * @since 1.6.0
	 *
	 * @var string
	 */
	public static $key_video_source = '_tribe_events_virtual_video_source';

	/**
	 * Meta key for autodetect source field.
	 *
	 * @since 1.8.0
	 *
	 * @var string
	 */
	public static $key_autodetect_source = '_tribe_events_virtual_autodetect_source';

	/**
	 * Meta key for virtual url field.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public static $key_virtual_url = '_tribe_events_virtual_url';

	/**
	 * Meta key to enable display embed video.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public static $key_embed_video = '_tribe_events_virtual_embed_video';

	/**
	 * Meta key to enable display of linked button.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public static $key_linked_button = '_tribe_events_virtual_linked_button';

	/**
	 * Meta key for linked button text field.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public static $key_linked_button_text = '_tribe_events_virtual_linked_button_text';

	/**
	 * Meta key for when to show the embed.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public static $key_show_embed_at = '_tribe_events_virtual_show_embed_at';

	/**
	 * Meta key for who to show the embed to.
	 *
	 * @since 1.0.4
	 *
	 * @var string
	 */
	public static $key_show_embed_to = '_tribe_events_virtual_show_embed_to';

	/**
	 * Meta value to set as virtual.
	 *
	 * @since 1.4.0
	 *
	 * @var string
	 */
	public static $value_virtual_event_type = 'virtual';

	/**
	 * Meta value to set as hybrid.
	 *
	 * @since 1.4.0
	 *
	 * @var string
	 */
	public static $value_hybrid_event_type = 'hybrid';

	/**
	 * Meta value to show the embed immediately.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public static $value_show_embed_now = 'immediately';

	/**
	 * Meta value to show the embed on event start.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public static $value_show_embed_start = 'at-start';

	/**
	 * Meta value to show the embed to all users.
	 *
	 * @since 1.0.4
	 *
	 * @var string
	 */
	public static $value_show_embed_to_all = 'all';

	/**
	 * Meta value to show the embed to logged in users.
	 *
	 * @since 1.0.4
	 *
	 * @var string
	 */
	public static $value_show_embed_to_logged_in = 'logged-in';

	/**
	 * Meta key for showing virtual indicators on single events.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public static $key_show_on_event = '_tribe_events_virtual_show_on_event';

	/**
	 * Meta key for showing virtual indicator on v2 Views.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public static $key_show_on_views = '_tribe_events_virtual_show_on_views';

	/**
	 * Key for video/smart url video source.
	 *
	 * @since 1.8.0
	 *
	 * @var string
	 */
	public static $key_video_source_id = 'video';

	/**
	 * Key for Oembed autodetect source.
	 *
	 * @since 1.8.0
	 *
	 * @var string
	 */
	public static $key_oembed_source_id = 'oembed';

	/**
	 * All the meta keys, in a set.
	 *
	 * @since 1.0.0
	 * @since 1.0.4 moved ticket links to Compatibility.
	 * @since 1.4.0 Add hybrid key.
	 * @since 1.6.0 Add video source.
	 *
	 * @var array<string>
	 */
	public static $virtual_event_keys = [
		'_tribe_events_is_hybrid',
		'_tribe_events_is_virtual',
		'_tribe_events_virtual_video_source',
		'_tribe_events_virtual_embed_video',
		'_tribe_events_virtual_linked_button_text',
		'_tribe_events_virtual_linked_button',
		'_tribe_events_virtual_show_embed_at',
		'_tribe_events_virtual_show_embed_to',
		'_tribe_events_virtual_show_on_event',
		'_tribe_events_virtual_show_on_views',
		'_tribe_events_virtual_url',
	];

	/**
	 * Key value map of the meta field to the schema type, for registering meta fields.
	 *
	 * @since 1.15.2
	 *
	 * @var array<string,string>
	 */
	public static $meta_field_types = [
		'_tribe_events_is_hybrid'                  => 'string',
		'_tribe_events_is_virtual'                 => 'string',
		'_tribe_events_virtual_video_source'       => 'string',
		'_tribe_events_virtual_embed_video'        => 'string',
		'_tribe_events_virtual_linked_button_text' => 'string',
		'_tribe_events_virtual_linked_button'      => 'string',
		'_tribe_events_virtual_show_embed_at'      => 'string',
		'_tribe_events_virtual_show_embed_to'      => 'array',
		'_tribe_events_virtual_show_on_event'      => 'string',
		'_tribe_events_virtual_show_on_views'      => 'string',
		'_tribe_events_virtual_url'                => 'string',
	];

	/**
	 * The prefix used to mark the meta saved by the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public static $prefix = '_tribe_events_';

	/**
	 * Get the virtual event meta keys.
	 * Allows additional modules/plugins to add themselves via a filter.
	 *
	 * @since 1.0.4
	 *
	 * @return array
	 */
	public static function get_virtual_event_meta_keys() {
		return apply_filters( 'tribe_events_virtual_event_meta_keys', self::$virtual_event_keys );
	}

	/**
	 * Returns the default text to be used for the linked button.
	 *
	 * @since 1.0.0
	 *
	 * @return string The default, localized, text to be used from the linked button text.
	 */
	public static function linked_button_default_text() {
		return _x(
			'Watch',
			'Default label of the virtual event URL call-to-action link.',
			'events-virtual'
		);
	}

	/**
	 * Returns the default text to be used for the linked button.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post|null $event The event we're editing.
	 *
	 * @return string The localized, placeholder text to be used for the video source input.
	 */
	public static function get_video_source_text( $event = null ) {
		$event = tribe_get_event( $event );

		$text = _x(
			'Enter URL (YouTube, Zoom, Outlook Event, Webex, etc.)',
			'Default placeholder text for the virtual event smart URL input.',
			'events-virtual'
		);

		/**
		 * Allows filtering of the default placeholder text for the URL field.
		 *
		 * @since 1.0.0
		 *
		 * @param string $text The current placeholder text.
		 */
		return apply_filters(
			'tribe_events_virtual_video_source_placeholder_text',
			$text,
			$event
		);
	}
}
