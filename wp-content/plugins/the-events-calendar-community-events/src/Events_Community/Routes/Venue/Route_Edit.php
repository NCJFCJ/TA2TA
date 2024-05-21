<?php

namespace TEC\Events_Community\Routes\Venue;

use TEC\Events_Community\Routes\Abstract_Route;
use Tribe__Events__Community__Main;
use TEC\Events_Community\Callbacks\Venue\Callback_Edit;

/**
 * Class Edit Venue
 *
 * Adds a venue route to the WordPress router.
 *
 * @since 4.10.9
 *
 * @package TEC\Events_Community\Routes\venue
 */
class Route_Edit extends Abstract_Route {

	/**
	 * The route slug.
	 *
	 * @since 4.10.9
	 * @var string
	 */
	protected static string $slug = 'edit-venue-route';

	/**
	 * The route suffix.
	 *
	 * @since 4.10.9
	 * @var string
	 */
	protected string $suffix = '/(\d+)/?$';

	/**
	 * The query variables for the route.
	 *
	 * @since 4.10.9
	 * @var array
	 */
	protected static array $query_vars = [
		'tribe_event_id' => 1,
	];

	/**
	 * The page arguments for the route.
	 *
	 * @since 4.10.9
	 * @var array
	 */
	protected static array $page_args = [ 'tribe_event_id' ];


	/**
	 * Callback function for the route.
	 *
	 * @since 4.10.9
	 * @since 4.10.14 Refactored callback.
	 *
	 * @param int $event_id The ID of the event to edit.
	 *
	 * @return string The form HTML for editing the venue.
	 */
	public function callback( int $event_id ): string {

		$venue_edit = tribe( Callback_Edit::class );

		$venue_edit->setup( [ 'venue_id' => $event_id ] );

		return $venue_edit->callback();

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
		 * The path should follow these criteria:
		 *     events/community/edit/venue/123
		 *     events/community/edit/venue/456/
		 *     events/community/edit/venue/789
		 *     events/community/edit/venue/012/
		 */

		$community_events = tribe( 'community.main' );
		$community_rewrite_slug = $community_events->getCommunityRewriteSlug();
		$edit_slug = $community_events->rewriteSlugs[ 'edit' ];
		$venue_slug = $community_events->rewriteSlugs[ 'venue' ];
		$path = "{$community_rewrite_slug}/{$edit_slug}/{$venue_slug}{$suffix}";

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

		$title = __( 'Edit a Venue', 'tribe-events-community' );
		/**
		 * Filters the title of the community events edit venue page.
		 *
		 * @since 4.10.9
		 *
		 * @deprecated 4.10.9
		 *
		 * @param string $title The page title.
		 *
		 * @return string The filtered page title.
		 */
		$this->title = apply_filters_deprecated( 'tribe_events_community_venue_edit_page_title', [ $title ], '4.10.9', 'tec_events_community_edit-venue-route_page_title', 'Moved to new filter.' );
	}
}
