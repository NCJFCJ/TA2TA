<?php
/**
 * Handles the `post__not_recurring` query var to exclude Occurrences of Recurring Events from the results
 * when the filtered query is a `WP_Query` instance, not a Custom Tables query.
 *
 * If the query is not a WP_Query, the modifier that should be used is the
 * `Events_Not_Recurring_Custom_Tables_Query_Modifier`.
 *
 * @since   6.2.2
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\WP_Query\Modifiers;
 */

namespace TEC\Events_Pro\Custom_Tables\V1\WP_Query\Modifiers;

use TEC\Events\Custom_Tables\V1\WP_Query\Modifiers\Base_Modifier;
use Tribe__Events__Main as TEC;
use Tribe__Events__Pro__Main as ECP;
use WP_Query;

/**
 * Class Events_Not_Recurring_Query_Modifier.
 *
 * @since   6.2.2
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\WP_Query\Modifiers;
 */
class Events_Not_Recurring_Query_Modifier extends Base_Modifier {
	/**
	 * The name of the query var that will trigger this modifier.
	 *
	 * @var string
	 */
	public const POST_NOT_RECURRING_QUERY_VAR = 'post__not_recurring';

	/**
	 * {@inheritDoc}
	 *
	 * @since 6.2.2
	 *
	 * @return void
	 */
	public function hook() {
		add_filter( 'posts_where', [ $this, 'exclude_recurring_events' ], 10, 2 );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 6.2.2
	 *
	 * @return void
	 */
	public function unhook() {
		remove_filter( 'posts_where', [ $this, 'exclude_recurring_events' ], 10, 2 );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 6.2.2
	 *
	 * @param WP_Query|null $query
	 *
	 * @return void
	 */
	public function applies_to( WP_Query $query = null ): bool {
		return $query && ! empty( $query->query_vars[ self::POST_NOT_RECURRING_QUERY_VAR ] );
	}

	/**
	 * Excludes, by post ID, Recurring Events from the query.
	 *
	 * Recurring Events are either:
	 * - Events that have a non-empty `_EventRecurrence` meta value.
	 * - Events that have a non-empty `post_parent` value; these might be left-over Events from a migration.
	 *
	 * @since 6.2.2
	 *
	 * @param string   $where The WHERE clause of the query.
	 * @param WP_Query $query The WP_Query instance.
	 *
	 * @return string The modified WHERE clause.
	 */
	public function exclude_recurring_events( string $where, WP_Query $query ): string {
		if ( $query !== $this->query ) {
			return $where;
		}

		remove_filter( 'posts_where', [ $this, 'exclude_recurring_events' ], 10, 2 );

		global $wpdb;

		/*
		 * Use a sub-query to exclude from the results:
		 * - Events that have a non-empty '_EventRecurrence' meta value.
		 * - Events that have a non-empty 'post_parent' value.
		 * The sub-query is pretty fast building on the indexed `wp_posts.ID`, `wp_posts.post_type`,
		 * `wp_posts.post_parent` and `wp_postmeta.meta_key` columns; the last comparison happens on
		 * `wp_postmeta.meta_value`, not indexed, but on the smallest possible set of rows.
		 */
		$where .= $wpdb->prepare( " AND $wpdb->posts.ID NOT IN (
				SELECT DISTINCT(p.ID) FROM $wpdb->posts p
				LEFT JOIN $wpdb->postmeta pm ON p.ID = pm.post_id AND pm.meta_key = '_EventRecurrence'
				WHERE p.post_type = %s
				AND ((p.post_parent IS NOT NULL AND p.post_parent != 0)
				OR (pm.meta_value IS NOT NULL AND pm.meta_value != ''))
			)", TEC::POSTTYPE );

		return $where;
	}
}