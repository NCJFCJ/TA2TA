<?php

namespace TEC\Events_Community\Routes\Listing;

use TEC\Events_Community\Callbacks\Listing\Callback_Listing;
use TEC\Events_Community\Routes\Abstract_Route;
use Tribe__Events__Community__Main;

/**
 * Class Listing Page
 *
 * Adds an event route to the WordPress router.
 *
 * @since 4.10.9
 *
 * @package TEC\Events_Community\Routes\listing
 */
class Route_Listing extends Abstract_Route {

	/**
	 * The route slug.
	 *
	 * @since 4.10.9
	 * @var string
	 */
	protected static string $slug = 'list-route';

	/**
	 * The route suffix.
	 *
	 * @since 4.10.9
	 * @var string
	 */
	protected string $suffix = '(/page/(\d+))?/?$';

	/**
	 * The query variables for the route.
	 *
	 * @since 4.10.9
	 * @var array
	 */
	protected static array $query_vars = [
		'listPage' => 2,
	];

	/**
	 * The page arguments for the route.
	 *
	 * @since 4.10.9
	 * @var array
	 */
	protected static array $page_args = [ 'listPage' ];


	/**
	 * Callback function for the route.
	 *
	 * @since 4.10.9
	 * @since 4.10.14 Refactored callback.
	 *
	 * @param int $listPage The current page number.
	 */
	public function callback( $listPage = 1 ) {

		$listing = tribe( Callback_Listing::class );


		$listing->setup( [ 'listPage' => $listPage, 'print_before_after_override' => 'false', 'shortcode' => false ] );
		return $listing->callback();

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
		 *     events/community/list
		 *     events/community/list/
		 *     events/community/list/page/1
		 *     events/community/list/page/12/
		 */

		$community_events = Tribe__Events__Community__Main::instance();
		$community_rewrite_slug = $community_events->getCommunityRewriteSlug();
		$list_slug = $community_events->rewriteSlugs[ 'list' ];
		$path = "{$community_rewrite_slug}/{$list_slug}{$suffix}";

		return $path;
	}

	/**
	 * Returns a custom title based on the current event display.
	 *
	 * @since 4.10.9
	 *
	 * @return string The custom page title.
	 */
	public function create_custom_title() {
		$event_display = tribe_get_request_var( 'eventDisplay' );

		if ( $event_display === 'past' ) {
			$title = __( 'My Past Events', 'tribe-events-community' );
		} else {
			$title = __( 'My Upcoming Events', 'tribe-events-community' );
		}

		return $title;
	}

	/**
	 * @inheritDoc
	 *
	 * @since 4.10.9
	 *
	 * @return void
	 */
	public function set_title(): void {

		$title = $this->create_custom_title();
		/**
		 * Filters the title of the community events listing page.
		 *
		 * @since 4.10.9
		 *
		 * @deprecated 4.10.9
		 *
		 * @param string $title The page title.
		 *
		 * @return string The filtered page title.
		 */
		$this->title = apply_filters_deprecated( 'tribe_events_community_event_list_page_title', [ $title ], '4.10.9', 'tec_events_community_list-route_page_title', 'Moved to new filter.' );
	}

}
