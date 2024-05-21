<?php

namespace TEC\Events_Community\Callbacks\Listing;

use TEC\Events_Community\Callbacks\Abstract_Callback;
use Tribe__Events__Community__Main;
use Tribe__Events__Main;

/**
 * Class Callback_Listing
 *
 * @4.10.14
 *
 * @package TEC\Events_Community\Callbacks\Listing
 */
class Callback_Listing extends Abstract_Callback {

	/**
	 * @inheritdoc
	 */
	protected static string $slug = 'listing';

	/**
	 * @inheritdoc
	 */
	protected string $logout_page_tagline = 'Please log in to view your events.';

	/**
	 * Get the events to display on the listing page.
	 *
	 * @since 4.10.14
	 *
	 * @param $paged int Page to display.
	 *
	 * @return array|\WP_Query
	 */
	public function get_events( $paged ) {
		$current_user = wp_get_current_user();
		add_filter( 'tribe_query_can_inject_date_field', '__return_false' );

		/**
		 * Allow filtering the "my events" query 'orderby' param directly.
		 *
		 * @since 4.6.2
		 *
		 * @param string $orderby 'event_date'    defaults to event_date now for orderby
		 */
		$orderby = apply_filters( 'tribe_events_community_my_events_query_orderby', 'event_date' );

		/**
		 * Allow filtering the "my events" query 'order' param directly.
		 *
		 * @since 4.6.2
		 *
		 * @param string $order 'ASC'    defaults to ASC now for order
		 */
		$order = apply_filters( 'tribe_events_community_my_events_query_order', 'ASC' );

		$main            = tribe( 'community.main' );
		$events_per_page = $main->eventsPerPage;

		$event_display = empty( $_GET['eventDisplay'] ) ? 'list' : $_GET['eventDisplay'];

		$args = [
			'posts_per_page'      => $events_per_page,
			'paged'               => $paged,
			'author'              => $current_user->ID,
			'post_type'           => Tribe__Events__Main::POSTTYPE,
			'post_status'         => [ 'pending', 'draft', 'future', 'publish' ],
			'eventDisplay'        => $event_display,
			'tribeHideRecurrence' => false,
			'orderby'             => sanitize_text_field( $orderby ),
			'order'               => sanitize_text_field( $order ),
			's'                   => isset( $_GET['event-search'] ) ? esc_html( $_GET['event-search'] ) : '',
		];

		if ( 'list' === $event_display ) {
			// Anything that ends after now is considered "Upcoming".
			$args['ends_after'] = 'now';
		} elseif ( 'past' === $_GET['eventDisplay'] ) {
			// Anything that ends before now is considered "Past".
			$args['ends_before'] = 'now';
		}

		/**
		 * Allow filtering the "my events" query args.
		 * Note that 'order' and 'orderby can be filtered directly -
		 *     removing the need to sift through the array to change them
		 *     via: `tribe_events_community_my_events_query_order` and `tribe_events_community_my_events_query_orderby`
		 *
		 * @since 4.6.1.2
		 *
		 * @param array $args array of query args
		 */
		$args   = apply_filters( 'tribe_events_community_my_events_query', $args );
		$events = tribe_get_events( $args, true );

		remove_filter( 'tribe_query_can_inject_date_field', '__return_false' );

		return $events;
	}

	/**
	 * Return the HTML that will be displayed when the callback() is called.
	 *
	 * @return string
	 */
	public function display_events() {

		global $paged;

		if ( empty( $paged ) && ! empty( $page ) ) {
			$paged = $this->get_page_args( 'listPage' );
		}

		$paged = empty( $paged ) ? 1 : $paged;

		$events = $this->get_events( $paged );

		$shortcode = $this->get_page_args( 'shortcode' );

		$template = 'community/event-list';

		// If shortcode, load our shortcode template.
		if ( $shortcode ) {
			$template = 'community/event-list-shortcode';
		}

		$args = [
			'events' => $events,
			'paged'  => $paged
		];

		return $this->display_template( $template, $args );

	}

	/**
	 * @inheritDoc
	 *
	 * @return string
	 */
	public function callback(): string {
		$this->pre_filters();

		// Make sure the user has access to the page.
		$access_message = $this->get_access_message();

		if ( $access_message ) {
			return $access_message;
		}

		$this->default_template_compatibility();


		return $this->display_events();
	}

}
