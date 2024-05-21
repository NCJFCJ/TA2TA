<?php
/**
 * Provides the rewrite rules for virtual events archives.
 *
 * @since   1.0.1
 * @package Tribe\Events\Virtual\Rewrite
 */

namespace Tribe\Events\Virtual\Rewrite;

use TEC\Common\Contracts\Service_Provider;

/**
 * Class Rewrite_Provider
 *
 * @since   1.0.1
 * @package Tribe\Events\Virtual\Rewrite
 */
class Rewrite_Provider extends Service_Provider {

	/**
	 * Used when forming recurring events /all/ view permalinks.
	 *
	 * @since 1.0.1
	 *
	 * @var string
	 */
	public $virtual_slug = 'virtual';

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 1.0.1
	 */
	public function register() {

		$this->virtual_slug = sanitize_title( tribe_get_virtual_label_lowercase() );

		$this->add_filters();
	}

	/**
	 * Adds the filter required to provide the rewrite support.
	 *
	 * @since 1.0.1
	 */
	protected function add_filters() {
		add_action( 'tribe_events_pre_rewrite', [ $this, 'filter_add_routes' ], 5 );
		add_filter( 'tribe_events_rewrite_base_slugs', [ $this, 'filter_add_base_slugs' ], 12 );
		add_filter( 'tribe_events_rewrite_matchers_to_query_vars_map', [ $this, 'filter_add_matchers_to_query_vars_map' ], 11, 2 );
		add_filter( 'tribe_events_rewrite_i18n_domains', [ $this, 'add_virtual_text_domain' ], 10 );
	}

	/**
	 * Add rewrite routes for custom Virtual views.
	 *
	 * @since 1.0.1
	 *
	 * @param \Tribe__Events__Rewrite $rewrite The Tribe__Events__Rewrite object
	 */
	public function filter_add_routes( $rewrite ) {

		$this->add_core_routes( $rewrite );
		$this->add_pro_routes( $rewrite );
	}

	/**
	 * Add rewrite routes for The Events Calendar views.
	 *
	 * @since 1.0.1
	 *
	 * @param \Tribe__Events__Rewrite $rewrite The Tribe__Events__Rewrite object
	 */
	public function add_core_routes( $rewrite ) {

		// List
		$rewrite->archive( [ '{{ virtual }}', '{{ page }}', '(\d+)' ], [
					'virtual'      => true,
					'eventDisplay' => 'list',
					'paged'        => '%1'
				] )
				->archive( [ '{{ virtual }}', '(feed|rdf|rss|rss2|atom)' ], [
					'virtual'      => true,
					'eventDisplay' => 'list',
					'feed'         => '%1'
				] )
				->archive( [ '{{ list }}', '{{ virtual }}', '{{ page }}', '(\d+)' ], [
					'eventDisplay' => 'list',
					'virtual'      => true,
					'paged'        => '%1'
				] )
				->archive( [ '{{ list }}', '{{ virtual }}' ], [ 'eventDisplay' => 'list', 'virtual' => true ] )
				->tax( [ '{{ virtual }}', '{{ page }}', '(\d+)' ], [
					'virtual' => true,
					'eventDisplay' => 'list',
					'paged' => '%2'
				] )
				->tax( [ '{{ list }}', '{{ virtual }}', '{{ page }}', '(\d+)' ], [
					'eventDisplay' => 'list',
					'virtual' => true,
					'paged' => '%2'
				] )
				->tax( [ '{{ list }}', '{{ virtual }}' ], [ 'eventDisplay' => 'list', 'virtual' => true ] )
				->tag( [ '{{ virtual }}', '{{ page }}', '(\d+)' ], [
					'virtual' => true,
					'eventDisplay' => 'list',
					'paged' => '%2'
				] )
				->tag( [ '{{ list }}', '{{ virtual }}', '{{ page }}', '(\d+)' ], [
					'eventDisplay' => 'list',
					'virtual' => true,
					'paged' => '%2'
				] )
				->tag( [ '{{ list }}', '{{ virtual }}' ], [ 'eventDisplay' => 'list', 'virtual' => true ] )
				->tax( [ '{{ virtual }}', 'feed' ], [
					'virtual' => true,
					'eventDisplay' => 'list',
					'feed' => 'rss2'
				] )
				->tag( [ '{{ virtual }}', 'feed' ], [
					'eventDisplay' => 'list',
					'feed' => 'rss2',
					'virtual' => true
				] )

				// Month
				->archive( [ '{{ month }}', '{{ virtual }}' ], [
					'eventDisplay' => 'month',
					'virtual'      => true
				] )
				->archive( [ '(\d{4}-\d{2})', '{{ virtual }}' ], [
					'eventDisplay' => 'month',
					'eventDate'    => '%1',
					'virtual'      => true
				] )
				->tax( [ '{{ month }}', '{{ virtual }}' ], [ 'eventDisplay' => 'month', 'virtual' => true ] )
				->tax( [ '(\d{4}-\d{2})', '{{ virtual }}' ], [
					'eventDisplay' => 'month',
					'eventDate' => '%2',
					'virtual' => true
				] )
				->tag( [ '{{ month }}', '{{ virtual }}' ], [ 'eventDisplay' => 'month', 'virtual' => true ] )
				->tag( [ '(\d{4}-\d{2})', '{{ virtual }}' ], [
					'eventDisplay' => 'month',
					'eventDate' => '%2',
					'virtual' => true
				] )

				// Day
				->archive( [ '{{ today }}', '{{ virtual }}' ], [ 'eventDisplay' => 'day', 'virtual' => true ] )->archive( [ '(\d{4}-\d{2}-\d{2})', '{{ virtual }}' ], [
					'eventDisplay' => 'day',
					'eventDate'    => '%1',
					'virtual'      => true
				] )
				->archive( [ '(\d{4}-\d{2}-\d{2})', '{{ virtual }}' ], [
					'eventDisplay' => 'day',
					'eventDate' => '%1',
					'virtual' => true
				] )
				->archive( [ '{{ virtual }}', 'ical' ], [ 'ical' => 1, 'virtual' => true ] )->archive( [ '(\d{4}-\d{2}-\d{2})', 'ical' ], [
					'ical'         => 1,
					'eventDisplay' => 'day',
					'eventDate'    => '%1'
				] )
				->archive( [ '(\d{4}-\d{2}-\d{2})', 'ical', '{{ virtual }}' ], [
					'ical'         => 1,
					'eventDisplay' => 'day',
					'eventDate'    => '%1',
					'virtual'      => true
				] )
				->tax( [ '{{ today }}', '{{ virtual }}' ], [ 'eventDisplay' => 'day', 'virtual' => true ] )
				->tax( [ '{{ day }}', '(\d{4}-\d{2}-\d{2})', '{{ virtual }}' ], [
					'eventDisplay' => 'day',
					'eventDate' => '%2',
					'virtual' => true
				] )
				->tax( [ '(\d{4}-\d{2}-\d{2})', '{{ virtual }}' ], [
					'eventDisplay' => 'day',
					'eventDate' => '%2',
					'virtual' => true
				] )
				->tag( [ '{{ today }}', '{{ virtual }}' ], [ 'eventDisplay' => 'day', 'virtual' => true ] )
				->tag( [ '{{ day }}', '(\d{4}-\d{2}-\d{2})', '{{ virtual }}' ], [
					'eventDisplay' => 'day',
					'eventDate' => '%2',
					'virtual' => true
				] )
				->tag( [ '(\d{4}-\d{2}-\d{2})', '{{ virtual }}' ], [ 'eventDisplay' => 'day', 'eventDate' => '%2', 'virtual' => true ] )
				->archive( [ '{{ virtual }}' ], [ 'virtual' => true ] )
				->tax( [ '{{ virtual }}' ], [ 'virtual' => true, 'eventDisplay' => 'default' ] )
				->tag( [ '{{ virtual }}' ], [ 'virtual' => true ] )

				// iCal & Feeds
				->tax( [ '{{ virtual }}', 'ical' ], [ 'virtual' => true, 'ical' => 1 ] )
				->tax( [ '{{ virtual }}', 'feed', '(feed|rdf|rss|rss2|atom)' ], [ 'virtual' => true, 'feed' => '%2' ] )
				->tag( [ '{{ virtual }}', 'ical' ], [ 'virtual' => true, 'ical' => 1 ] )
				->tag( [ '{{ virtual }}', 'feed', '(feed|rdf|rss|rss2|atom)' ], [ 'virtual' => true, 'feed' => '%2' ] );
	}

	/**
	 * Add rewrite routes for Events Calendar PRO views.
	 *
	 * @since 1.0.1
	 *
	 * @param \Tribe__Events__Rewrite $rewrite The Tribe__Events__Rewrite object
	 */
	public function add_pro_routes( $rewrite ) {

		// If PRO is not active then do not add routes.
		if ( ! class_exists( 'Tribe__Events__Pro__Main' ) ) {
			return;
		}

		// Week
		$rewrite->archive( [ '{{ week }}', '{{ virtual }}' ], [ 'eventDisplay' => 'week', 'virtual' => true ] )
		        ->archive( [ '{{ week }}', '(\d{4}-\d{2}-\d{2})', '{{ virtual }}' ], [
			        'eventDisplay' => 'week',
			        'eventDate'    => '%1',
			        'virtual'      => true
		        ] )
		        ->archive( [ '{{ week }}', '(\d{2})', '{{ virtual }}' ], [
			        'eventDisplay' => 'week',
			        'eventDate'    => '%1',
			        'virtual'      => true
		        ] )
		        ->tax( [ '{{ week }}', '{{ virtual }}' ], [ 'eventDisplay' => 'week', 'virtual' => true ] )
		        ->tax( [ '{{ week }}', '(\d{4}-\d{2}-\d{2})', '{{ virtual }}' ], [
			        'eventDisplay' => 'week',
			        'eventDate'    => '%2',
			        'virtual'      => true
		        ] )
		        ->tag( [ '{{ week }}', '{{ virtual }}' ], [ 'eventDisplay' => 'week', 'virtual' => true ] )
		        ->tag( [ '{{ week }}', '(\d{4}-\d{2}-\d{2})', '{{ virtual }}' ], [
			        'eventDisplay' => 'week',
			        'eventDate'    => '%2',
			        'virtual'      => true
		        ] )

				// Photo
			    ->archive( [ '{{ photo }}', '{{ virtual }}' ], [ 'eventDisplay' => 'photo', 'virtual' => true ] )
		        ->archive( [ '{{ photo }}', '(\d{4}-\d{2}-\d{2})', '{{ virtual }}' ], [
			        'eventDisplay' => 'photo',
			        'eventDate'    => '%1',
			        'virtual'      => true
		        ] )
		        ->tax( [ '{{ photo }}', '{{ virtual }}' ], [ 'eventDisplay' => 'photo', 'virtual' => true ] )
		        ->tag( [ '{{ photo }}', '{{ virtual }}' ], [ 'eventDisplay' => 'photo', 'virtual' => true ] );
	}

	/**
	 * Add the required bases for Virtual Events.
	 *
	 * @since 1.0.1
	 *
	 * @param array $bases Bases that are already set
	 *
	 * @return array         The modified version of the array of bases
	 */
	public function filter_add_base_slugs( $bases = [] ) {
		// Support original and translated versions.
		$bases['virtual'] = [ 'virtual', sanitize_title( tribe_get_virtual_label_lowercase() ) ];

		return $bases;
	}

	/**
	 * Add the required bases for the Virtual Archive.
	 *
	 * @since 1.0.1
	 *
	 * @param array $bases Bases that are already set.
	 *
	 * @return array         The modified version of the array of bases.
	 */
	public function filter_add_matchers_to_query_vars_map( $matchers = [], $rewrite = null ) {

		$matchers['virtual'] = 'virtual';

		return $matchers;
	}

	/**
	 * Adds the plugin text domain to the list of domains that should be used to generate rewrite
	 * rules.
	 *
	 * @since 1.13.4
	 *
	 * @param array<string,string> $domains A map of domains to the directories containing the translations.
	 *
	 * @return array<string,string>  The modified map of domains.
	 */
	public function add_virtual_text_domain( $domains ) {
		if ( is_array( $domains ) ) {
			$plugin_base_dir = dirname( plugin_basename( EVENTS_VIRTUAL_FILE ) );
			$domains['events-virtual'] = $plugin_base_dir . DIRECTORY_SEPARATOR . 'lang';
		}

		return $domains;
	}
}
