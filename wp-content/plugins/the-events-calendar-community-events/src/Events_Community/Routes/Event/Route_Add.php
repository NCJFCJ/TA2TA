<?php

namespace TEC\Events_Community\Routes\Event;

use TEC\Events_Community\Routes\Abstract_Route;
use Tribe__Events__Community__Main;
use TEC\Events_Community\Callbacks\Event\Callback_Add_Edit;

/**
 * Class Add Event
 *
 * Adds an event route to the WordPress router.
 *
 * @since 4.10.9
 *
 * @package TEC\Events_Community\Routes\event
 */
class Route_Add extends Abstract_Route {

	/**
	 * The route slug.
	 *
	 * @since 4.10.9
	 * @var string
	 */
	protected static string $slug = 'add-route';

	/**
	 * The route suffix.
	 *
	 * @since 4.10.9
	 * @var string
	 */
	protected string $suffix = '$';

	/**
	 * The page arguments for the route.
	 *
	 * @since 4.10.9
	 * @var array
	 */
	protected static array $page_args = [];


	/**
	 * Callback function for the route.
	 *
	 * @since 4.10.9
	 * @since 4.10.14 refactored Callback.
	 */
	public function callback() {

		$add = tribe( Callback_Add_Edit::class );


		$add->setup( [ 'event_id' => null ] );
		return $add->callback();

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

		/**
		 * The path should follow these criteria:
		 *     /events/community/add
		 *     /events/community/add/
		 */

		$community_events = tribe( 'community.main' );
		$community_rewrite_slug = $community_events->getCommunityRewriteSlug();
		$add_slug = $community_events->rewriteSlugs[ 'add' ];
		$path = "{$community_rewrite_slug}/{$add_slug}{$suffix}";

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

		$title = __( 'Submit an Event', 'tribe-events-community' );
		/**
		 * Filters the title of the community events submission page.
		 *
		 * @since 4.10.9
		 *
		 * @deprecated 4.10.9
		 *
		 * @param string $title The page title.
		 *
		 * @return string The filtered page title.
		 */
		$this->title = apply_filters_deprecated( 'tribe_events_community_submit_event_page_title', [ $title ], '4.10.9', 'tec_events_community_add-route_page_title', 'Moved to new filter.' );
	}

}
