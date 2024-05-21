<?php

namespace TEC\Events_Community\Routes;

use Tribe__Events__Templates;

/**
 * Abstract class for adding custom routes to the WordPress router.
 *
 * Extend this class to create custom routes for your plugin or theme.
 *
 * @since 4.10.9
 */
abstract class Abstract_Route implements Route_Interface {

	/**
	 * The router object.
	 *
	 * @since 4.10.9
	 * @var object
	 */
	protected $router;

	/**
	 * The base URL slug for the route.
	 *
	 * @since 4.10.9
	 * @var string
	 */
	protected static string $slug;

	/**
	 * A suffix to add to the route URL.
	 *
	 * @since 4.10.9
	 * @var string
	 */
	protected string $suffix;

	/**
	 * An array of query variables for the route.
	 *
	 * @since 4.10.9
	 * @var array
	 */
	protected static array $query_vars = [ 'eventDisplay' => 'community_event_page', ];


	/**
	 * An array of page arguments for the route.
	 *
	 * @since 4.10.9
	 * @var array
	 */
	protected static array $page_args;

	/**
	 * The title for the route.
	 *
	 * @since 4.10.9
	 * @var string
	 */
	protected string $title;

	/**
	 * The template to use for the route.
	 *
	 * @since 4.10.9
	 * @var string
	 */
	protected string $template;

	/**
	 * The slug prefix four all of our routes.
	 *
	 * @since 4.10.9
	 * @var string
	 */
	protected string $slug_prefix = 'ce';

	/**
	 * Set the router to use later.
	 *
	 * @since 4.10.9
	 *
	 * @param $router
	 *
	 * @return void
	 */
	public function set_router( $router ): void {
		$this->router = $router;
	}

	/**
	 * @inheritDoc
	 *
	 * @since 4.10.9
	 *
	 * @return void
	 */
	public function setup(): void{
		$this->set_title();
		// Add the route after setup.
		$this->add();
	}

	/**
	 * @inheritDoc
	 *
	 * @since 4.10.9
	 */
	public function add(): void {
		$this->router->add_route( $this->prefix_slug( self::get_slug() ), [
			'path'            => '^' . $this->get_path( $this->suffix ),
			'query_vars'      => static::$query_vars,
			'page_callback'   => [ $this, 'callback' ],
			'page_arguments'  => static::$page_args,
			'access_callback' => true,
			'title'           => $this->filter_title(),
			'template'        => $this->get_template(),
		] );
	}

	/**
	 * Get the template for the route.
	 *
	 * @since 4.10.9
	 *
	 * @return string The name of the template file to use.
	 */
	public function get_template(): string {

		// Get the template hierarchy for the default community template or the user-selected template
		// based on the value of the tribeEventsTemplate option.
		$tec_template = tribe_get_option( 'tribeEventsTemplate' );
		switch ( $tec_template ) {
			case '':
				$template_name = Tribe__Events__Templates::getTemplateHierarchy( 'community/default-template' );
				break;
			case 'default':
				$template_name = 'page.php';
				break;
			default:
				$template_name = $tec_template;
		}

		/**
		 * Filters the template name for the Events Community feature.
		 *
		 * @since 4.10.9
		 *
		 * @param string $template_name The name of the template file to use.
		 * @param string $slug Slug used by the route.
		 *
		 * @return string The filtered name of the template file to use.
		 */
		return apply_filters( 'tribe_events_community_template', $template_name, self::get_slug() );
	}

	/**
	 * Returns the route slug.
	 *
	 * @since 4.10.9
	 *
	 * @return string The route slug.
	 */
	public static function get_slug(): string {
		return static::$slug;
	}

	/**
	 * Adds a prefix to a given slug.
	 *
	 * @since 4.10.9
	 *
	 * @param string $slug The original slug.
	 *
	 * @return string The slug with the prefix.
	 */
	public function prefix_slug( string $slug ): string {
		return "{$this->slug_prefix}-{$slug}";
	}

	/**
	 * @inheritDoc
	 *
	 * @since 4.10.9
	 *
	 * @return void
	 */
	abstract public function set_title(): void;

	/**
	 * Returns the title for the route.
	 *
	 * @since 4.10.9
	 *
	 * @return string The title.
	 */
	public function get_title(): string {
		return $this->title;
	}

	/**
	 * Filters the title of the route.
	 *
	 * Applies the "tribe_events_community_{slug}_page_title" and "tribe_events_community_route_page_title" filters to the title.
	 *
	 * @since 4.10.9
	 *
	 * @return string The filtered title.
	 */
	public function filter_title(): string {
		/**
		 * Filters the title of a specific Community Events route page.
		 *
		 * @since 4.10.9
		 *
		 * @param string $title The current page title.
		 *
		 * @return string The filtered page title.
		 */
		$title = apply_filters( "tec_events_community_{$this->get_slug()}_page_title", $this->get_title() );

		/**
		 * Filters the title of all Community Events route pages.
		 *
		 * @since 4.10.9
		 *
		 * @param string $title The current page title.
		 *
		 * @return string The filtered page title.
		 */
		$title = apply_filters( 'tec_events_community_route_page_title', $title );

		return $title;
	}

}
