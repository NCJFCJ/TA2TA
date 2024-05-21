<?php
/**
 * Handles the compatibility with the Events Control extension.
 *
 * @since   1.0.0
 *
 * @package Tribe\Extensions\Events_Control_Extension
 */

namespace Tribe\Events\Virtual\Compatibility\Events_Control_Extension;

use Tribe\Extensions\EventsControl\Hooks as Events_Control_Extension_Hooks;
use TEC\Common\Contracts\Service_Provider as Provider_Contract;
/**
 * Class Service_Provider
 *
 * @since   1.0.0
 *
 * @package Tribe\Extensions\Events_Control_Extension
 */
class Service_Provider extends Provider_Contract {

	/**
	 * Registers the bindings and filters used to ensure compatibility with the Events Control extension.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		$this->container->singleton( self::class, $this );
		$this->container->singleton( 'events-virtual.compatibility.tribe-ext-events-control', $this );

		$this->container->singleton( Meta_Redirection::class, Meta_Redirection::class );

		add_action( 'tribe_plugins_loaded', [ $this, 'handle_actions' ], 20 );
		add_action( 'tribe_plugins_loaded', [ $this, 'handle_filters' ], 20 );
		add_filter( 'tribe_template_done', [ $this, 'short_circuit_templates' ], 10, 2 );
		add_filter( 'tribe_template_file', [ $this, 'replace_metabox_template' ], 20, 3 );
		add_filter( 'get_post_metadata', [ $this, 'redirect_online_meta_to_virtual_meta' ], 20, 4 );
	}

	/**
	 * Short-circuits the templates the extension would load for Online events.
	 *
	 * @since 1.0.0
	 *
	 * @param bool|null    $done A flag to indicate whether the template request has been handled or not.
	 * @param string|array $name The name, or name fragments, of the requested template.
	 *
	 * @return bool|null Either the original `$done` value if the template is not one of the target ones, or `true` if
	 *                   the template is one of the target ones and should not be printed.
	 */
	public function short_circuit_templates( $done, $name ) {
		$targets = [
			'online-link',
			'online-marker',
			'single/online-link',
			'single/online-marker',
		];

		return in_array( $name, $targets, true ) ? true : $done;
	}

	/**
	 * Un-hooks the extension actions that deal with online events.
	 *
	 * @since 1.0.0
	 */
	public function handle_actions() {
		$extension_hooks = tribe( Events_Control_Extension_Hooks::class );

		/**
		 * Fires after the Metabox saved the data from the current request.
		 *
		 * @since 1.0.0
		 *
		 * @param int $post_id The post ID of the event currently being saved.
		 * @param array<string,mixed> The whole data received by the metabox.
		 */
		add_action( 'tribe_events_virtual_metabox_save', [ $this, 'sync_online_meta' ], 100 );

		// List View.
		remove_action(
			'tribe_template_after_include:events/v2/list/event/venue',
			[ $extension_hooks, 'action_add_online_event' ],
			15,
			3
		);

		// Day View.
		remove_action(
			'tribe_template_after_include:events/v2/day/event/description',
			[ $extension_hooks, 'action_add_archive_online_link' ],
			15,
			3
		);
		remove_action(
			'tribe_template_after_include:events/v2/day/event/venue',
			[ $extension_hooks, 'action_add_online_event' ],
			15,
			3
		);

		// Photo View.
		remove_action(
			'tribe_template_before_include:events-pro/v2/photo/event/date-time',
			[ $extension_hooks, 'action_add_online_event' ],
			20,
			3
		);

		// Map View.
		remove_action(
			'tribe_template_after_include:events-pro/v2/map/event-cards/event-card/event/venue',
			[ $extension_hooks, 'action_add_online_event' ],
			15,
			3
		);
		remove_action(
			'tribe_template_after_include:events-pro/v2/map/event-cards/event-card/tooltip/venue',
			[ $extension_hooks, 'action_add_online_event' ],
			15,
			3
		);

		// Week View.
		remove_action(
			'tribe_template_after_include:events-pro/v2/week/mobile-events/day/event/venue',
			[ $extension_hooks, 'action_add_online_event' ],
			15,
			3
		);
		remove_action(
			'tribe_template_after_include:events-pro/v2/week/grid-body/events-day/event/tooltip/description',
			[ $extension_hooks, 'action_add_archive_online_link' ],
			15,
			3
		);
	}

	/**
	 * Handles the filters hooked by the extension by short-circuiting or removing them.
	 *
	 * @since 1.0.0
	 */
	public function handle_filters() {
		$extension_hooks = tribe( Events_Control_Extension_Hooks::class );

		// To avoid the JSON_LD from being modified from the extension mark any event as not Online.
		add_filter( 'tribe_events_single_event_online_status', '__return_false' );

		// Remove Online meta from any event.
		add_filter( 'tribe_ext_events_control_meta_data', [ $this, 'filter_meta' ] );

		// Month View.
		remove_filter(
			'tribe_template_html:events/v2/month/calendar-body/day/calendar-events/calendar-event/date',
			[ $extension_hooks, 'filter_insert_online_event' ],
			15,
			4
		);
		remove_filter(
			'tribe_template_html:events/v2/month/calendar-body/day/calendar-events/calendar-event/tooltip/date',
			[ $extension_hooks, 'filter_insert_online_event' ],
			15,
			4
		);
		remove_filter(
			'tribe_template_html:events/v2/month/calendar-body/day/multiday-events/multiday-event',
			[ $extension_hooks, 'filter_insert_online_event' ],
			15,
			4
		);
		remove_filter(
			'tribe_template_html:events/v2/month/mobile-events/mobile-day/mobile-event/date',
			[ $extension_hooks, 'filter_insert_online_event' ],
			15,
			4
		);

		// Week View.
		remove_filter(
			'tribe_template_html:events-pro/v2/week/grid-body/events-day/event/title',
			[ $extension_hooks, 'filter_insert_status_label' ],
			15,
			4
		);
		remove_filter(
			'tribe_template_html:events-pro/v2/week/grid-body/events-day/event/tooltip/title',
			[ $extension_hooks, 'filter_insert_status_label' ],
			15,
			4
		);
		remove_filter(
			'tribe_template_html:events-pro/v2/week/grid-body/multiday-events-day/multiday-event',
			[ $extension_hooks, 'filter_insert_status_label' ],
			15,
			4
		);
		remove_filter(
			'tribe_template_html:events-pro/v2/week/mobile-events/day/event/title',
			[ $extension_hooks, 'filter_insert_status_label' ],
			15,
			4
		);
	}

	/**
	 * This method will replace the "The Events Calendar Extension: Events Control" metabox template
	 * with one that will not include the management of Online Events.
	 *
	 * @since 1.0.0
	 *
	 * @param string               $found_file The template file found for the template name.
	 * @param array<string>|string $name       The name, or name fragments, of the requested template.
	 * @param \Tribe__Template     $template   The template instance that is currently handling the template location
	 *                                                                                                     request.
	 *
	 * @return string The path to the template to load; this will be modified to the "doctored" metabox
	 *                template if required.
	 */
	public function replace_metabox_template( $found_file, $name, \Tribe__Template $template ) {
		if (
			is_object( $template->origin )
			&& is_array( $name ) && [ 'metabox', 'container' ] === $name
			&& $template->origin instanceof \Tribe\Extensions\EventsControl\Main
		) {
			return __DIR__ . '/metabox-container.php';
		}

		return $found_file;
	}

	/**
	 * Redirects requests for Online meta to virtual meta, if set, and vice versa.
	 *
	 * The purpose of this redirection is to provide the extension with either Virtual Event data and, when not set,
	 * online event data.
	 * The method will perform an embedded migration converting Online event data to Virtual Event data if not yet
	 * migrated.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed|null  $meta_value The current meta value, initially `null`.
	 * @param int         $id         The post ID.
	 * @param string|null $meta_key   The meta key to fetch, or `null` to return all the post meta values.
	 * @param bool        $single     Whether the current request is for a single meta key entry or not.
	 *
	 * @return mixed The meta key value or values.
	 */
	public function redirect_online_meta_to_virtual_meta( $meta_value, $id, $meta_key = null, $single = false ) {
		$meta_redirection = $this->container->make( Meta_Redirection::class );

		return $meta_redirection->redirect_online_meta_to_virtual_meta( $meta_value, $id, $meta_key, $single );
	}

	/**
	 * Filters the meta data to remove the data the extension would add for Online events.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string,mixed> $data The REST data for the event.
	 *
	 * @return array<string,mixed> The modified REST data for the event.
	 */
	public function filter_meta( array $data = [] ) {
		unset( $data['is_online'], $data['online_url'] );

		return $data;
	}

	/**
	 * Ensures Online data is correctly hydrated following the Virtual one when saving data from the metabox.
	 *
	 * If the migration is not on, then copying the Virtual event data where the extension expects to find it its the
	 * only way to ensure data will not be lost or deleted during the save process.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The post ID of the event currently being saved.
	 */
	public function sync_online_meta( $post_id ) {
		$meta_redirection = $this->container->make( Meta_Redirection::class );

		return $meta_redirection->sync_online_meta( $post_id );
	}
}
