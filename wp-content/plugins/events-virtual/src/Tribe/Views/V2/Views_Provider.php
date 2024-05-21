<?php
/**
 * The main service provider for Virtual support and additions to the Views V2 functions.
 *
 * @since   1.0.1
 * @package Tribe\Events\Virtual\Views\V2
 */

namespace Tribe\Events\Virtual\Views\V2;

use Tribe\Events\Views\V2\Kitchen_Sink;
use Tribe\Events\Views\V2\View;
use Tribe\Events\Views\V2\View_Interface;
use Tribe__Context as Context;
use TEC\Common\Contracts\Service_Provider;

/**
 * Class Views_Provider
 *
 * @since   1.0.1
 * @package Tribe\Events\Virtual\Views\V2
 */
class Views_Provider extends Service_Provider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 1.0.1
	 */
	public function register() {

		$this->container->singleton( 'events-virtual.views.v2.provider', $this );

		add_filter( 'query_vars', [ $this, 'filter_query_vars' ], 15 );
		add_filter( 'tribe_events_views_v2_url_query_args', [ $this, 'filter_view_url_query_args' ], 10, 2 );
		add_filter( 'tribe_events_views_v2_view_repository_args', [ $this, 'filter_events_views_v2_view_repository_args' ], 10, 2 );

		add_filter( 'tribe_events_filter_views_v2_wp_title_plural_events_label', [ $this, 'filter_views_v2_wp_title_plural_events_label' ], 10, 3 );
		add_filter( 'tribe_events_pro_filter_views_v2_wp_title_plural_events_label', [ $this, 'filter_views_v2_wp_title_plural_events_label' ], 10, 3 );
		add_filter( 'tribe_events_views_v2_view_breadcrumbs', [ $this, 'filter_v2_view_breadcrumbs' ], 10, 2 );
	}

	/**
	 * Filters the publicly available query variables to add the ones supported by Views v2.
	 *
	 * @since 1.0.1
	 *
	 * @param array $query_vars The list of publicly available query variables.
	 *
	 * @return array The filtered list of publicly available query variables.
	 */
	public function filter_query_vars( array $query_vars = [] ) {
		$query_vars[] = 'virtual';

		return $this->container->make( Kitchen_Sink::class )->filter_register_query_vars( $query_vars );
	}

	/**
	 * Filters the query arguments Views will use to build their own URL.
	 *
	 * Using the context we'll know what filter are applied and what keys and values to add to the query args.
	 *
	 * @since 1.0.1
	 *
	 * @param array          $query_args The current URL query arguments.
	 * @param View_Interface $view       The instance of the View the URL is being built for.
	 *
	 * @return array The filtered array of URL query arguments.
	 */
	public function filter_view_url_query_args( array $query_args, View_Interface $view ) {
		return $this->container->make( Query::class )->filter_view_query_args( $query_args, $view );
	}

	/**
	 * Hook to filter a view repository args to add the virtual ones.
	 *
	 * @since 1.0.1
	 *
	 * @param array        $repository_args The current repository args.
	 * @param Context|null $context         An instance of the context the View is using or `null` to use the
	 *                                      global Context.
	 *
	 * @return array The filtered repository args.
	 */
	public function filter_events_views_v2_view_repository_args( array $repository_args = [], Context $context = null ) {
		return $this->container->make( Repository::class )->filter_repository_args( $repository_args, $context );
	}

	/**
	 * Filter the plural events label for Virtual V2 Views.
	 *
	 * @since 1.0.1
	 *
	 * @param string  $label   The plural events label as it's been generated thus far.
	 * @param Context $context The context used to build the title, it could be the global one, or one externally
	 *                         set.
	 *
	 * @return string the original label or updated label for virtual archives.
	 */
	public function filter_views_v2_wp_title_plural_events_label( $label, Context $context ) {
		return $this->container->make( Title::class )->filter_views_v2_wp_title_plural_events_label( $label, $context );
	}

	/**
	 * Setup breadcrumbs for when it's virtual.
	 *
	 * @since 1.0.1
	 *
	 * @param array $breadcrumbs An array of breadcrumbs.
	 * @param View  $this        The current View instance being rendered.
	 *
	 * @return array An array of breadcrumb data the View will display on the front-end.
	 */
	public function filter_v2_view_breadcrumbs( $breadcrumbs, $view ) {
		return $this->container->make( Breadcrumbs::class )->filter_views_v2_breadcrumbs( $breadcrumbs, $view );
	}
}
