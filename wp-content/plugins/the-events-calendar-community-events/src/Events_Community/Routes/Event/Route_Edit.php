<?php

namespace TEC\Events_Community\Routes\Event;

use TEC\Events_Community\Routes\Abstract_Route;
use Tribe__Events__Community__Main;
use TEC\Events_Community\Callbacks\Event\Callback_Add_Edit;

/**
 * Class Edit Event
 *
 * Adds an event route to the WordPress router.
 *
 * @since 4.10.9
 *
 * @package TEC\Events_Community\Routes\event
 */
class Route_Edit extends Abstract_Route {

	/**
	 * The route slug.
	 *
	 * @since 4.10.9
	 * @var string
	 */
	protected static string $slug = 'edit-route';

	/**
	 * The route suffix.
	 *
	 * @since 4.10.9
	 * @var string
	 */
	protected string $suffix = '(\d+)/?$';

	/**
	 * The query variables for the route.
	 *
	 * @since 4.10.9
	 * @var array
	 */
	protected static array $query_vars = [
		'tribe_community_event_id' => 2,
		'eventDisplay'             => 'edit_community_event',
	];

	/**
	 * The page arguments for the route.
	 *
	 * @since 4.10.9
	 * @var array
	 */
	protected static array $page_args = [ 'tribe_community_event_id' ];


	/**
	 * Callback function for the route.
	 *
	 * @since 4.10.9
	 * @since 4.10.14 refactored Callback.
	 *
	 * @param int $event_id The ID of the event being edited.
	 *
	 * @return string|void The event form or a "Not found" message.
	 */
	public function callback( int $event_id ) {

		$edit = tribe( Callback_Add_Edit::class );

		$edit->setup( [ 'event_id' => $event_id ] );
		return $edit->callback();

	}

	/**
	 * Get the path for the route.
	 *
	 * @since 4.10.9
	 *
	 * @param string $suffix Optional. The suffix to add to the path. Default is an empty string.
	 *
	 * @return string The path for the route.
	 */
	public function get_path( string $suffix = '' ): string {

		/*
		 * The path should follow these criteria -
		 *     /events/community/edit/event/123/
		 *     /events/community/edit/456/
		 *     /events/community/edit/event/789
		 *     /events/community/edit/987
		 */
		$community_events = Tribe__Events__Community__Main::instance();
		$community_rewrite_slug = $community_events->getCommunityRewriteSlug();
		$edit_slug = $community_events->rewriteSlugs[ 'edit' ];
		$event_slug = $community_events->rewriteSlugs[ 'event' ];
		$path = "{$community_rewrite_slug}/{$edit_slug}/(?:(?={$event_slug}/)({$event_slug}/))?{$suffix}";

		return $path;
	}

	/**
	 * @inheritDoc
	 *
	 * @since 4.10.9
	 *
	 * @return void
	 */
	public function set_title(): void {

		$title = __( 'Edit an Event', 'tribe-events-community' );
		/**
		 * Filters the title of the community events edit submission page.
		 *
		 * @since 4.10.9
		 *
		 * @deprecated 4.10.9
		 *
		 * @param string $title The page title.
		 *
		 * @return string The filtered page title.
		 */
		$this->title = apply_filters_deprecated( 'tribe_events_community_edit_event_page_title', [ $title ], '4.10.9', 'tec_events_community_edit-route_page_title', 'Moved to new filter.' );
	}
}
