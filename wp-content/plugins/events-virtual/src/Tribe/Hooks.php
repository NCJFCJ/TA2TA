<?php
/**
 * Handles hooking all the actions and filters used by the module.
 *
 * To remove a filter:
 * ```php
 *  remove_filter( 'some_filter', [ tribe( Tribe\Events\Virtual\Hooks::class ), 'some_filtering_method' ] );
 *  remove_filter( 'some_filter', [ tribe( 'events-virtual.hooks' ), 'some_filtering_method' ] );
 * ```
 *
 * To remove an action:
 * ```php
 *  remove_action( 'some_action', [ tribe( Tribe\Events\Virtual\Hooks::class ), 'some_method' ] );
 *  remove_action( 'some_action', [ tribe( 'events-virtual.hooks' ), 'some_method' ] );
 * ```
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual;
 */

namespace Tribe\Events\Virtual;

use Tribe\Events\Virtual\Autodetect\Autodetect_Provider;
use Tribe\Events\Virtual\Context\Context_Provider;
use Tribe\Events\Virtual\Importer\Importer_Provider;
use Tribe\Events\Virtual\Event_Status\Compatibility\Filter_Bar\Service_Provider as Event_Status_Filter_Bar_Provider;
use Tribe\Events\Virtual\Event_Status\Status_Labels;
use Tribe\Events\Virtual\Meetings\Facebook_Provider;
use Tribe\Events\Virtual\Meetings\Google_Provider;
use Tribe\Events\Virtual\Meetings\Microsoft_Provider;
use Tribe\Events\Virtual\Meetings\Webex_Provider;
use Tribe\Events\Virtual\Meetings\YouTube_Provider;
use Tribe\Events\Virtual\Meetings\Zoom\Url;
use Tribe\Events\Virtual\Meetings\Zoom_Provider;
use Tribe\Events\Virtual\Views\V2\Widgets\Widget;
use Tribe\Events\Views\V2\Template_Bootstrap;
use Tribe__Context as Context;
use Tribe__Events__Main as Events_Plugin;
use Tribe__Template as Template;
use TEC\Common\Contracts\Service_Provider;
use WP_Post;

/**
 * Class Hooks.
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual;
 */
class Hooks extends Service_Provider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 1.0.0
	 * @since 1.8.0 - add call to method for additional providers.
	 */
	public function register() {
		$this->container->singleton( static::class, $this );
		$this->container->singleton( 'events-virtual.hooks', $this );

		$this->add_actions();
		$this->add_filters();
		$this->add_providers();
		$this->add_meetings_support();
		$this->container->register( Event_Status_Filter_Bar_Provider::class );
	}

	/**
	 * Adds the actions required by the plugin.
	 *
	 * @since 1.0.0
	 * @since 1.8.0 - remove embed ajax call and move video metabox template to autodetect.
	 */
	protected function add_actions() {
		add_action( 'init', [ $this, 'on_init' ] );
		add_action( 'admin_init', [ $this, 'run_updates' ], 10, 0 );
		if (  tribe( 'editor' )->should_load_blocks() ) {
			add_action( 'add_meta_boxes', [ $this, 'on_add_meta_boxes' ], 15 );
		} else {
			add_action( 'tribe_after_location_details', [ $this, 'render_metabox' ], 5 );
		}

		// Shared API Display Details.
		add_action(
			'tribe_template_entry_point:events-virtual/admin-views/virtual-metabox/container/display:before_ul_close',
			[ $this, 'render_classic_display_controls' ],
			10,
			3
		);

		add_action( 'save_post_' . Events_Plugin::POSTTYPE, [ $this, 'on_save_post' ], 15, 3 );

		// Latest Past View.
		add_action(
			'tribe_template_after_include:events/v2/latest-past/event/venue',
			[ $this, 'action_add_virtual_event_marker' ],
			15,
			3
		);

		add_action(
			'tribe_template_after_include:events/v2/latest-past/event/venue',
			[ $this, 'action_add_hybrid_event_marker' ],
			15,
			3
		);

		// List View.
		add_action(
			'tribe_template_after_include:events/v2/list/event/venue',
			[ $this, 'action_add_virtual_event_marker' ],
			15,
			3
		);

		add_action(
			'tribe_template_after_include:events/v2/list/event/venue',
			[ $this, 'action_add_hybrid_event_marker' ],
			15,
			3
		);

		// Day View.
		add_action(
			'tribe_template_after_include:events/v2/day/event/venue',
			[ $this, 'action_add_virtual_event_marker' ],
			15,
			3
		);

		add_action(
			'tribe_template_after_include:events/v2/day/event/venue',
			[ $this, 'action_add_hybrid_event_marker' ],
			15,
			3
		);

		// Month View.
		add_action(
			'tribe_template_after_include:events/v2/month/calendar-body/day/calendar-events/calendar-event/date/featured',
			[ $this, 'action_add_virtual_event_marker' ],
			15,
			3
		);

		add_action(
			'tribe_template_after_include:events/v2/month/calendar-body/day/calendar-events/calendar-event/tooltip/date/featured',
			[ $this, 'action_add_virtual_event_marker' ],
			15,
			3
		);

		add_action(
			'tribe_template_after_include:events/v2/month/calendar-body/day/multiday-events/multiday-event/bar/featured',
			[ $this, 'action_add_virtual_event_marker' ],
			15,
			3
		);

		add_action(
			'tribe_template_after_include:events/v2/month/mobile-events/mobile-day/mobile-event/title',
			[ $this, 'action_add_virtual_event_marker' ],
			15,
			3
		);

		add_action(
			'tribe_template_after_include:events/v2/month/calendar-body/day/calendar-events/calendar-event/date/featured',
			[ $this, 'action_add_hybrid_event_marker' ],
			15,
			3
		);

		add_action(
			'tribe_template_after_include:events/v2/month/calendar-body/day/calendar-events/calendar-event/tooltip/date/featured',
			[ $this, 'action_add_hybrid_event_marker' ],
			15,
			3
		);

		add_action(
			'tribe_template_after_include:events/v2/month/calendar-body/day/multiday-events/multiday-event/bar/featured',
			[ $this, 'action_add_hybrid_event_marker' ],
			15,
			3
		);

		add_action(
			'tribe_template_after_include:events/v2/month/mobile-events/mobile-day/mobile-event/title',
			[ $this, 'action_add_hybrid_event_marker' ],
			15,
			3
		);

		// Summary View.
		add_action(
			'tribe_template_before_include:events-pro/v2/summary/date-group/event/title/featured',
			[ $this, 'action_add_virtual_event_marker' ],
			20,
			3
		);

		add_action(
			'tribe_template_before_include:events-pro/v2/summary/date-group/event/title/featured',
			[ $this, 'action_add_hybrid_event_marker' ],
			20,
			3
		);

		// Photo View.
		add_action(
			'tribe_template_before_include:events-pro/v2/photo/event/date-time',
			[ $this, 'action_add_virtual_event_marker' ],
			20,
			3
		);

		add_action(
			'tribe_template_before_include:events-pro/v2/photo/event/date-time',
			[ $this, 'action_add_hybrid_event_marker' ],
			20,
			3
		);

		// Map View.
		add_action(
			'tribe_template_after_include:events-pro/v2/map/event-cards/event-card/event/venue',
			[ $this, 'action_add_virtual_event_marker' ],
			15,
			3
		);

		add_action(
			'tribe_template_after_include:events-pro/v2/map/event-cards/event-card/tooltip/venue',
			[ $this, 'action_add_virtual_event_marker' ],
			15,
			3
		);

		add_action(
			'tribe_template_after_include:events-pro/v2/map/event-cards/event-card/event/venue',
			[ $this, 'action_add_hybrid_event_marker' ],
			15,
			3
		);

		add_action(
			'tribe_template_after_include:events-pro/v2/map/event-cards/event-card/tooltip/venue',
			[ $this, 'action_add_hybrid_event_marker' ],
			15,
			3
		);

		// Week View.
		add_action(
			'tribe_template_after_include:events-pro/v2/week/mobile-events/day/event/venue',
			[ $this, 'action_add_virtual_event_marker' ],
			15,
			3
		);

		add_action(
			'tribe_template_after_include:events-pro/v2/week/grid-body/events-day/event/date/featured',
			[ $this, 'action_add_virtual_event_marker' ],
			15,
			3
		);

		add_action(
			'tribe_template_after_include:events-pro/v2/week/grid-body/events-day/event/tooltip/date/featured',
			[ $this, 'action_add_virtual_event_marker' ],
			15,
			3
		);

		add_action(
			'tribe_template_after_include:events-pro/v2/week/grid-body/multiday-events-day/multiday-event/bar/featured',
			[ $this, 'action_add_virtual_event_marker' ],
			15,
			3
		);

		add_action(
			'tribe_template_after_include:events-pro/v2/week/mobile-events/day/event/venue',
			[ $this, 'action_add_hybrid_event_marker' ],
			15,
			3
		);

		add_action(
			'tribe_template_after_include:events-pro/v2/week/grid-body/events-day/event/date/featured',
			[ $this, 'action_add_hybrid_event_marker' ],
			15,
			3
		);

		add_action(
			'tribe_template_after_include:events-pro/v2/week/grid-body/events-day/event/tooltip/date/featured',
			[ $this, 'action_add_hybrid_event_marker' ],
			15,
			3
		);

		add_action(
			'tribe_template_after_include:events-pro/v2/week/grid-body/multiday-events-day/multiday-event/bar/featured',
			[ $this, 'action_add_hybrid_event_marker' ],
			15,
			3
		);

		// "Classic" Event Single.
		add_action(
			'tribe_events_single_event_after_the_content',
			[ $this, 'action_add_event_single_video_embed' ],
			15,
			0
		);

		add_action(
			'tribe_events_single_meta_details_section_end',
			[ $this, 'action_add_event_single_details_block_link_button' ],
			15,
			0
		);

		add_action(
			'tribe_template_before_include:events-virtual/single/video-embed',
			[ $this, 'action_add_oembed_filter' ],
			15,
			3
		);

		add_action(
			'tribe_template_after_include:events-virtual/single/video-embed',
			[ $this, 'action_remove_oembed_filter' ],
			15,
			3
		);

		// Event Single Blocks.

		// We need to be sure that some hooks fire after global $post is available for checks.
		add_action( 'wp', [ $this, 'hook_block_template' ] );

		add_action(
			'tribe_template_before_include:events/blocks/event-datetime',
			[ $this, 'action_add_block_virtual_event_marker' ]
		);

		add_action(
			'tribe_template_before_include:events/blocks/event-datetime',
			[ $this, 'action_add_block_hybrid_event_marker' ]
		);

		add_action(
			'tribe_events_pro_shortcode_tribe_events_before_assets',
			[ $this, 'action_include_assets' ]
		);
		// Generic Widgets.

		add_action(
			'tribe_events_views_v2_widget_after_enqueue_assets',
			[ $this, 'action_widget_after_enqueue_assets' ],
			10,
			3
		);

		// Widget Events List.

		add_action(
			'tribe_template_after_include:events/v2/widgets/widget-events-list/event/date/featured',
			[ $this, 'action_add_virtual_event_marker' ],
			10,
			3
		);

		add_action(
			'tribe_template_after_include:events/v2/widgets/widget-events-list/event/date/featured',
			[ $this, 'action_add_hybrid_event_marker' ],
			10,
			3
		);

		// Widget Featured Venue.

		add_action(
			'tribe_template_after_include:events-pro/v2/widgets/widget-featured-venue/events-list/event/date/featured',
			[ $this, 'action_add_virtual_event_marker' ],
			10,
			3
		);

		add_action(
			'tribe_template_after_include:events-pro/v2/widgets/widget-featured-venue/events-list/event/date/featured',
			[ $this, 'action_add_hybrid_event_marker' ],
			10,
			3
		);

		/* Events by Week Widget */
		add_action(
			'tribe_template_after_include:events-pro/v2/widget-week/mobile-events/day/event/date/featured',
			[ $this, 'action_add_virtual_event_marker' ],
			15,
			3
		);

		/* Event Calendar Widget */
		add_action(
			'tribe_events_pro_shortcode_month_widget_add_hooks',
			[ $this, 'action_pro_shortcode_month_widget_add_hooks' ]
		);

		add_action(
			'tribe_events_pro_shortcode_month_widget_remove_hooks',
			[ $this, 'action_pro_shortcode_month_widget_remove_hooks' ]
		);
	}

	/**
	 * Adds the filters required by the plugin.
	 *
	 * @since 1.0.0
	 */
	protected function add_filters() {
		add_filter( 'tribe_template_origin_namespace_map', [ $this, 'filter_add_template_origin_namespace' ], 15 );
		add_filter( 'tribe_template_path_list', [ $this, 'filter_template_path_list' ], 15, 2 );
		add_filter( 'tribe_the_notices', [ $this, 'filter_include_single_control_mobile_markers' ], 15 );
		add_filter( 'tribe_the_notices', [ $this, 'filter_include_single_hybrid_control_mobile_markers' ], 15 );
		add_filter( 'tribe_events_event_schedule_details', [ $this, 'include_single_control_desktop_markers' ], 10, 2 );
		add_filter( 'tribe_events_event_schedule_details', [ $this, 'include_single_hybrid_control_desktop_markers' ], 10, 2 );
		add_filter( 'tribe_json_ld_event_object', [ $this, 'filter_json_ld_modifiers' ], 15, 3 );
		add_filter( 'tribe_rest_event_data', [ $this, 'filter_rest_event_data' ], 10, 2 );
		add_filter( 'post_class', [ $this, 'filter_add_post_class' ], 15, 3 );
		add_filter( 'body_class', [ $this, 'filter_add_body_class' ], 10 );

		// Filter event object properties to add the ones related to virtual events.
		add_filter( 'tribe_get_event', [ $this, 'filter_tribe_get_event' ] );
		add_filter( 'tribe_get_event_after', [ $this, 'add_dynamic_properties' ] );

		// Add the plugin locations to the Context.
		add_filter( 'tribe_context_locations', [ $this, 'filter_context_locations' ] );

		// Add Video Source.
		add_filter( 'tribe_events_virtual_video_sources', [ $this, 'add_video_source' ], 10, 2 );
		// Add autodetect source.
		add_filter( 'tec_events_virtual_autodetect_video_sources', [ $this, 'add_autodetect_source' ], 10, 3 );
		// Add Event status.
		add_filter( 'tec_event_statuses', [ $this, 'filter_event_status' ], 10, 2 );
	}

	/**
	 * Register providers.
	 *
	 * @since 1.8.0
	 */
	protected function add_providers() {
		$this->container->register( Autodetect_Provider::class );
		$this->container->register( Importer_Provider::class );
	}

	/**
	 * Registers the meeting providers.
	 *
	 * @since 1.0.0
	 */
	protected function add_meetings_support() {
		if ( ! Plugin::meetings_enabled() ) {
			return;
		}

		$this->container->register( Facebook_Provider::class );
		$this->container->register( Google_Provider::class );
		$this->container->register( Microsoft_Provider::class );
		$this->container->register( Webex_Provider::class );
		$this->container->register( YouTube_Provider::class );
		$this->container->register( Zoom_Provider::class );
	}

	/**
	 * Add the control classes for the views v2 elements
	 *
	 * @since 1.0.0
	 *
	 * @param string|array<string> $classes Space-separated string or array of class names to add to the class list.
	 * @param array<string>        $class   An array of additional class names added to the post.
	 * @param int|\WP_Post         $post    Post ID or post object.
	 *
	 * @return array<string> The filtered post classes.
	 */
	public function filter_add_post_class( $classes, $class, $post ) {
		$new_classes = $this->container->make( Template_Modifications::class )->get_post_classes( $post );

		return array_merge( $classes, $new_classes );
	}

	/**
	 * Add the control classes for the views v2 elements
	 *
	 * @since 1.0.0
	 *
	 * @param string|array<string> $classes Space-separated string or array of class names to add to the class list.
	 *
	 * @return array<string> The filtered post body classes.
	 */
	public function filter_add_body_class( $classes ) {
		global $post;
		$new_classes = $this->container->make( Template_Modifications::class )->get_body_classes( $post );

		return array_merge( $classes, $new_classes );
	}

	/**
	 * Includes Pro into the path namespace mapping, allowing for a better namespacing when loading files.
	 *
	 * @since 1.0.0
	 *
	 * @param array $namespace_map Indexed array containing the namespace as the key and path to `strpos`.
	 *
	 * @return array  Namespace map after adding Pro to the list.
	 */
	public function filter_add_template_origin_namespace( $namespace_map ) {
		/* @var $plugin Plugin */
		$plugin                        = tribe( Plugin::class );
		$namespace_map[ Plugin::SLUG ] = $plugin->plugin_path;

		return $namespace_map;
	}

	/**
	 * Filters the list of folders TEC will look up to find templates to add the ones defined by PRO.
	 *
	 * @since 1.0.0
	 *
	 * @param array    $folders  The current list of folders that will be searched template files.
	 * @param Template $template Which template instance we are dealing with.
	 *
	 * @return array The filtered list of folders that will be searched for the templates.
	 */
	public function filter_template_path_list( array $folders, Template $template ) {
		/* @var $plugin Plugin */
		$plugin = tribe( Plugin::class );
		$path   = (array) rtrim( $plugin->plugin_path, '/' );

		// Pick up if the folder needs to be added to the public template path.
		$folder = [ 'src/views' ];

		if ( ! empty( $folder ) ) {
			$path = array_merge( $path, $folder );
		}

		$folders[ Plugin::SLUG ] = [
			'id'        => Plugin::SLUG,
			'namespace' => Plugin::SLUG,
			'priority'  => 10,
			'path'      => implode( DIRECTORY_SEPARATOR, $path ),
		];

		return $folders;
	}

	/**
	 * Modifiers to the JSON LD object we use.
	 *
	 * @since 1.0.0
	 *
	 * @param object  $data The JSON-LD object.
	 * @param array   $args The arguments used to get data.
	 * @param WP_Post $post The post object.
	 *
	 * @return object JSON LD object after modifications.
	 */
	public function filter_json_ld_modifiers( $data, $args, $post ) {
		return $this->container->make( JSON_LD::class )->modify_virtual_event( $data, $args, $post );
	}

	/**
	 * Filters event REST data to include new fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array   $data  The event data array.
	 * @param WP_Post $event The post object.
	 *
	 * @return array The event data array after modification.
	 */
	public function filter_rest_event_data( $data, $event ) {
		return array_merge(
			$data,
			$this->container->make( Models\Event::class )->get_rest_properties( $event )
		);
	}

	/**
	 * Renders the metabox template.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id  The post ID of the event we are interested in.
	 */
	public function render_metabox( $post_id ) {
		echo $this->container->make( Metabox::class )->render_template( $post_id ); /* phpcs:ignore */
	}

	/**
	 * Renders the API controls related to the display of the API detais.
	 * I.E. Webex or Zoom Meeting Links and numbers.
	 *
	 * @since 1.9.0
	 *
	 * @param string           $file        The path to the template file, unused.
	 * @param string           $entry_point The name of the template entry point, unused.
	 * @param \Tribe__Template $template    The current template instance.
	 */
	public function render_classic_display_controls( $file, $entry_point, \Tribe__Template $template ) {
		$this->container->make( Metabox::class )
			        ->render_classic_display_controls( $template->get( 'post' ) );
	}

	/**
	 * Register the metabox fields in the correct action.
	 *
	 * @since 1.0.0
	 */
	public function on_init() {
		$this->container->make( Metabox::class )->register_fields();
	}

	/**
	 * Registers the plugin meta box for Blocks Editor support.
	 *
	 * @since 1.0.0
	 */
	public function on_add_meta_boxes() {
		$this->container->make( Metabox::class )->register_blocks_editor_legacy();
	}

	/**
	 * Register the metabox fields in the correct action.
	 *
	 * @since 1.0.0
	 *
	 * @param int     $post_id Which post ID we are dealing with when saving.
	 * @param WP_Post $post    WP Post instance we are saving.
	 * @param boolean $update  If we are updating the post or not.
	 */
	public function on_save_post( $post_id, $post, $update ) {
		$this->container->make( Metabox::class )->save( $post_id, $post, $update );
	}

	/**
	 * Include the Virtual Events URL anchor for the archive pages.
	 *
	 * @since 1.0.0
	 *
	 * @param string   $file     Complete path to include the PHP File.
	 * @param array    $name     Template name.
	 * @param Template $template Current instance of the Template.
	 */
	public function action_add_virtual_event_marker( $file, $name, $template ) {
		$this->container->make( Template_Modifications::class )
						->add_virtual_event_marker( $file, $name, $template );
	}

	/**
	 * Include the Hybrid Events event marker for the archive pages.
	 *
	 * @since 1.4.0
	 *
	 * @param string   $file     Complete path to include the PHP File.
	 * @param array    $name     Template name.
	 * @param Template $template Current instance of the Template.
	 */
	public function action_add_hybrid_event_marker( $file, $name, $template ) {
		$this->container->make( Template_Modifications::class )
						->add_hybrid_event_marker( $file, $name, $template );
	}

	/**
	 * Include the Virtual Events URL anchor for the single event block.
	 *
	 * @since 1.0.1
	 */
	public function action_add_block_virtual_event_marker() {
		$this->container->make( Template_Modifications::class )
						->add_single_block_virtual_event_marker();
	}

	/**
	 * Include the Hybrid Events URL anchor for the single event block.
	 *
	 * @since 1.4.0
	 */
	public function action_add_block_hybrid_event_marker() {
		$this->container->make( Template_Modifications::class )
						->add_single_block_hybrid_event_marker();
	}

	/**
	 * Include the video embed for event single.
	 *
	 * @since 1.0.0
	 */
	public function action_add_event_single_video_embed() {
		$this->container->make( Template_Modifications::class )
						->add_event_single_video_embed();
	}

	/**
	 * Include the link button for event single.
	 *
	 * @since 1.0.0
	 */
	public function action_add_event_single_link_button() {
		$this->container->make( Template_Modifications::class )
						->add_event_single_non_block_link_button();
	}

	/**
	 * Include the link button for event single details block.
	 *
	 * @since 1.0.0
	 */
	public function action_add_event_single_details_block_link_button() {
		$this->container->make( Template_Modifications::class )
						->add_event_single_link_button();
	}

	/**
	 * Add the oEmbed filter before the video embed template.
	 *
	 * @since 1.0.0
	 *
	 * @param string   $file     Complete path to include the PHP File.
	 * @param array    $name     Template name.
	 * @param Template $template Current instance of the Template.
	 */
	public function action_add_oembed_filter( $file, $name, $template ) {
		add_filter( 'oembed_dataparse', [ $this, 'filter_make_oembed_responsive' ], 10, 3 );
	}

	/**
	 * Remove the oEmbed filter after the video embed template.
	 *
	 * @since 1.0.0
	 *
	 * @param string   $file     Complete path to include the PHP File.
	 * @param array    $name     Template name.
	 * @param Template $template Current instance of the Template.
	 */
	public function action_remove_oembed_filter( $file, $name, $template ) {
		remove_filter( 'oembed_dataparse', [ $this, 'filter_make_oembed_responsive' ], 10, 3 );
	}

	/**
	 * Include the control markers for the single pages.
	 *
	 * @since 1.0.0
	 * @since 1.6.0 - Always return notice html back to the filter.
	 *
	 * @param string $notices_html Previously set HTML.
	 *
	 * @return string  Before event html with the new markers.
	 */
	public function filter_include_single_control_mobile_markers( $notices_html ) {
		$event = tribe_get_event( get_the_ID() );

		if ( ! $event instanceof \WP_Post) {
			return $notices_html;
		}

		if ( ! $event->virtual ) {
			return $notices_html;
		}

		$template_modifications = $this->container->make( Template_Modifications::class );

		return $template_modifications->add_single_control_mobile_markers( $notices_html );
	}

	/**
	 * Include the hybrid control markers for the single pages.
	 *
	 * @since 1.4.0
	 * @since 1.6.0 - Always return notice html back to the filter.
	 *
	 * @param string $notices_html Previously set HTML.
	 *
	 * @return string  Before event html with the new markers.
	 */
	public function filter_include_single_hybrid_control_mobile_markers( $notices_html ) {
		$event = tribe_get_event( get_the_ID() );

		if ( ! $event instanceof \WP_Post ) {
			return $notices_html;
		}

		if ( ! $event->virtual ) {
			return $notices_html;
		}

		$template_modifications = $this->container->make( Template_Modifications::class );

		return $template_modifications->add_single_hybrid_control_mobile_markers( $notices_html );
	}

	/**
	 * Include the control markers for the single pages.
	 *
	 * @since 1.0.0
	 *
	 * @param string $schedule  The output HTML.
	 * @param int    $event_id  The post ID of the event we are interested in.
	 *
	 * @return string The output HTML.
	 */
	public function include_single_control_desktop_markers( $schedule, $event_id ) {
		// Avoid infinite loops with serialization.
		return tribe_suspending_filter(
			current_filter(),
			[ $this, __FUNCTION__ ],
			function () use ( $schedule, $event_id ) {
				$template_modifications = $this->container->make( Template_Modifications::class );

				return $template_modifications->add_single_control_markers( $schedule, $event_id );
			},
			2
		);
	}

	/**
	 * Include the hybrid control markers for the single pages.
	 *
	 * @since 1.4.0
	 *
	 * @param string $schedule  The output HTML.
	 * @param int    $event_id  The post ID of the event we are interested in.
	 *
	 * @return string The output HTML.
	 */
	public function include_single_hybrid_control_desktop_markers( $schedule, $event_id ) {
		// Avoid infinite loops with serialization.
		return tribe_suspending_filter(
			current_filter(),
			[ $this, __FUNCTION__ ],
			function () use ( $schedule, $event_id ) {
				$template_modifications = $this->container->make( Template_Modifications::class );

				return $template_modifications->add_single_hybrid_control_markers( $schedule, $event_id );
			},
			2
		);
	}

	/**
	 * Filters the object returned by the `tribe_get_event` function to add to it properties related to virtual events.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post The events post object to be modified.
	 *
	 * @return \WP_Post The original event object decorated with properties related to virtual events.
	 */
	public function filter_tribe_get_event( $post ) {
		if ( ! $post instanceof WP_Post ) {
			// We should only act on event posts, else bail.
			return $post;
		}

		return $this->container->make( Models\Event::class )->add_properties( $post );
	}

	/**
	 * Add, to the Context, the locations used by the plugin.
	 *
	 * @since 1.0.0
	 * @since 1.14.0 - Use a constant for 'state' query variable.
	 *
	 * @param array<string,array> $context_locations The current Context locations.
	 *
	 * @return array<string,array> The updated Context locations.
	 */
	public function filter_context_locations( array $context_locations ) {
		$context_locations['events_virtual_data'] = [
			'read' => [
				Context::REQUEST_VAR => [ Metabox::$id ],
			],
		];

		$context_locations['events_virtual_request'] = [
			'read' => [
				Context::REQUEST_VAR => [ Plugin::$request_slug, Context_Provider::AUTH_STATE_QUERY_VAR ],
			],
		];

		return $context_locations;
	}

	/**
	 * Filters the oEmbed HTML to make it responsive.
	 *
	 * @since 1.0.0
	 *
	 * @param string $html The returned oEmbed HTML.
	 * @param object $data A data object result from an oEmbed provider.
	 * @param string $url  The URL of the content to be embedded.
	 *
	 * @return string  The filtered oEmbed HTML.
	 */
	public function filter_make_oembed_responsive( $html, $data, $url ) {
		return $this->container->make( OEmbed::class )->make_oembed_responsive( $html, $data, $url );
	}

	/**
	 * Enqueue assets when we call a PRO shortcode.
	 *
	 * @since 1.0.0
	 */
	public function action_include_assets() {
		return $this->container->make( Assets::class )->load_on_shortcode();
	}

	/**
	 * Action to enqueue assets for virtual events for events list widget.
	 *
	 * @since 1.1.2
	 *
	 * @param boolean         $should_enqueue Whether assets are enqueued or not.
	 * @param \Tribe__Context $context        Context we are using to build the view.
	 * @param View_Interface  $view           Which view we are using the template on.
	 */
	public function action_widget_after_enqueue_assets( $should_enqueue, $context, $view ) {
		$this->container->make( Widget::class )->action_enqueue_assets( $should_enqueue, $context, $view );
	}

	/**
	 * Adds dynamic, time-related, properties to the event object.
	 *
	 * This method deals with properties we set, for convenience, on the event object that should not
	 * be cached as they are time-dependent; i.e. the time the properties are computed at matters and
	 * caching their values would be incorrect.
	 *
	 * @since 1.4.1
	 *
	 * @param mixed|\WP_Post $post The event post object, as read from the cache, if any.
	 *
	 * @return WP_Post The decorated event post object; its dynamic and time-dependent properties correctly set up.
	 */
	public function add_dynamic_properties( $post ) {
		if ( ! $post instanceof WP_Post ) {
			// We should only act on event posts, else bail.
			return $post;
		}


		return $this->container->make( Models\Event::class )->add_dynamic_properties( $post );
	}

	/**
	 * Triggers on the ECP month widget add_hooks() to add/remove icons strategically
	 *
	 * @since 1.4.0
	 */
	public function action_pro_shortcode_month_widget_add_hooks() {
		remove_action(
			'tribe_template_after_include:events/v2/month/mobile-events/mobile-day/mobile-event/title',
			[ $this, 'action_add_virtual_event_marker' ],
			15
		);

		add_action(
			'tribe_template_after_include:events/v2/month/mobile-events/mobile-day/mobile-event/date/featured',
			[ $this, 'action_add_virtual_event_marker' ],
			15,
			3
		);
	}

	/**
	 * Triggers on the ECP month widget remove_hooks() to add/remove icons strategically
	 *
	 * @since 1.4.0
	 */
	public function action_pro_shortcode_month_widget_remove_hooks() {
		add_action(
			'tribe_template_after_include:events/v2/month/mobile-events/mobile-day/mobile-event/title',
			[ $this, 'action_add_virtual_event_marker' ],
			15,
			3
		);

		remove_action(
			'tribe_template_after_include:events/v2/month/mobile-events/mobile-day/mobile-event/date/featured',
			[ $this, 'action_add_virtual_event_marker' ],
			15
		);
	}

	/**
	 * Run Updates on Plugin Upgrades.
	 *
	 * @since 1.5.0
	 */
	public function run_updates() {
		if ( ! class_exists( 'Tribe__Events__Updater' ) ) {
			return; // core needs to be updated for compatibility
		}

		$updater = new Updater( Plugin::VERSION );
		if ( $updater->update_required() ) {
			$updater->do_updates();
		}
	}

	/**
	 * Add the Video Source.
	 *
	 * @since 1.6.0
	 *
	 * @param array<string|string> An array of video sources.
	 * @param \WP_Post $post       The current event post object, as decorated by the `tribe_get_event` function.
	 *
	 * @return array<string|string> An array of video sources.
	 */
	public function add_video_source( $video_sources, $post ) {

		$video_sources[] = [
			'text'     => _x( 'Search for video or meeting link', 'The label of the video source option.', 'events-virtual' ),
			'id'       => Event_Meta::$key_video_source_id,
			'value'    => Event_Meta::$key_video_source_id,
			'selected' => Event_Meta::$key_video_source_id === $post->virtual_video_source,
		];

		return $video_sources;
	}

	/**
	 * Add the moved online event status.
	 *
	 * @since 1.7.3
	 *
	 * @param array<string|mixed> $statuses       The event status options for an event.
	 * @param string              $current_status The current event status for the event or empty string if none.
	 *
	 * @return array<string|mixed> The event status options for an event.
	 */
	public function filter_event_status( $statuses, $current_status ) {
		return $this->container->make( Status_Labels::class )->filter_event_statuses( $statuses, $current_status );
	}

	/**
	 * Add OEmbed to Autodetect Source.
	 *
	 * @since 1.8.0
	 *
	 * @param array<string|string>        An array of autodetect sources.
	 * @param string   $autodetect_source The ID of the selected autodetect video source.
	 * @param \WP_Post $post              The current event post object, as decorated by the `tribe_get_event` function.
	 *
	 * @return array<string|string> An array of video sources.
	 */
	public function add_autodetect_source( $autodetect_sources, $autodetect_source, $post ) {

		$autodetect_sources[] = [
			'text'     => _x( 'OEmbed', 'The name of the autodetect source.', 'events-virtual' ),
			'id'       => 'oembed',
			'value'    => 'oembed',
			'selected' => 'oembed' === $autodetect_source,
		];

		return $autodetect_sources;
	}

	/**
	 * Hook block templates - legacy or new VE block.
	 * Has to be postponed to `wp` action or later so global $post is available for should_inject_new_block().
	 *
	 * @since 1.7.1
	 *
	 * @todo: Should we move this to an abstract base provider?
	 */
	public function hook_block_template() {
		/* The action/location which the template is injected depends on whether or not V2 is enabled
		 * and whether the virtual event block is present in the post content.
		 */
		$embed_inject_action = $this->get_virtual_embed_action();

		add_action(
			$embed_inject_action,
			[
				$this,
				'action_add_event_single_video_embed',
			],
			10
		);

		add_action(
			$embed_inject_action,
			[
				$this,
				'action_add_event_single_link_button',
			],
			15
		);
	}

	/**
	 * Get the action we embed the virtual event content on.
	 *
	 * @since 1.7.1
	 *
	 * @return string The name of the action we embed on.
	 */
	public function get_virtual_embed_action() {
		return $this->should_inject_new_block()
			? 'tribe_events_virtual_block_content'
			: 'tribe_template_after_include:events/blocks/event-datetime';
	}

	/**
	 * Determine whether we show the legacy virtual info HTML or the new block.
	 *
	 * @since 1.7.1
	 *
	 * @return boolean Show the new block if true, the legacy info if false.
	 */
	public function should_inject_new_block() {
		global $post;

		// If we can't get the post - we probably shouldn't be here. Bail.
		if ( empty( $post ) ) {
			return false;
		}

		// This is required to ensure virtual event details are embedded properly in the Elementor Event widget
		$is_single_event = tribe( Template_Bootstrap::class )->is_single_event();

		$no_blocks = ! function_exists( 'has_block' ) || has_block( 'tribe/virtual-event', $post )  || ! $is_single_event;

		// If the block is missing, show the legacy info HTML.
		$new_block = ! $no_blocks ? false : true;

		/**
		 * Allows filtering whether to load the legacy virtual info HTML over the new block.
		 * Note if the new block is not present, returning true here can have unforeseen consequences!
		 *
		 * @since 1.7.1
		 *
		 * @param boolean $new_block Whether to load the new block or not
		 * @param WP_Post $post      The current global post object.
		 */
		return apply_filters( 'tec_events_virtual_should_inject_new_block', $new_block, $post );
	}
}
