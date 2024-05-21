<?php

namespace TEC\Events_Community\Routes\Organizer;

use TEC\Events_Community\Routes\Abstract_Route;
use Tribe__Events__Community__Main;
use TEC\Events_Community\Callbacks\Organizer\Callback_Edit;

/**
 * Class Edit Organizer
 *
 * Adds an organizer route to the WordPress router.
 *
 * @since 4.10.9
 *
 * @package TEC\Events_Community\Routes\organizer
 */
class Route_Edit extends Abstract_Route {

	/**
	 * The route slug.
	 *
	 * @since 4.10.9
	 * @var string
	 */
	protected static string $slug = 'edit-organizer-route';

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
	 * @since 4.10.14 refactored Callback.
	 *
	 * @param int $event_id The organizer ID.
	 *
	 * @return string The HTML content for the organizer form.
	 */
	public function callback( int $event_id ): string {


		$organizer_edit = tribe( Callback_Edit::class );

		$organizer_edit->setup( [ 'organizer_id' => $event_id ] );

		return $organizer_edit->callback();

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
		 *
		 *     events/community/edit/organizer/123
		 *     events/community/edit/organizer/456/
		 */

		$community_events = Tribe__Events__Community__Main::instance();
		$community_rewrite_slug = $community_events->getCommunityRewriteSlug();
		$edit_slug = $community_events->rewriteSlugs[ 'edit' ];
		$organizer_slug = $community_events->rewriteSlugs[ 'organizer' ];
		$path = "{$community_rewrite_slug}/{$edit_slug}/{$organizer_slug}{$suffix}";

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

		$title = __( 'Edit an Organizer', 'tribe-events-community' );
		/**
		 * Filters the title of the community events edit organizer page.
		 *
		 * @since 4.10.9
		 *
		 * @deprecated 4.10.9
		 *
		 * @param string $title The page title.
		 *
		 * @return string The filtered page title.
		 */
		$this->title = apply_filters_deprecated( 'tribe_events_community_organizer_edit_page_title', [ $title ], '4.10.9', 'tec_events_community_edit-organizer-route_page_title', 'Moved to new filter.' );
	}
}
