<?php
/**
 * Organizes Event Schedule Manager Custom Columns in the admin list.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Admin
 */

namespace TEC\Conference\Admin;

use TEC\Conference\Plugin;
use WP_Query;

/**
 * Class Conference_Schedule
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Admin
 */
class Columns {

	/**
	 * Event Schedule Manager session screen ID.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public static $screen_id = 'edit-tec_session';

	/**
	 * Runs during pre_get_posts in admin.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query $query The WP_Query object.
	 */
	public function admin_sessions_pre_get_posts( $query ) {
		if ( ! is_admin() ) {
			return;
		}

		if ( ! $query->is_main_query() ) {
			return;
		}

		$current_screen = get_current_screen();
		// Order by session time.
		if (
			empty( $current_screen->id )
			|| $current_screen->id !== static::$screen_id
		) {
			return;
		}
		$orderby = $query->get( 'orderby' );

		if ( $orderby === '_tec_session_time' ) {
			$query->set( 'meta_key', '_tec_session_time' );
			$query->set( 'orderby', 'meta_value_num' );
		} elseif ( $orderby === '_tec_session_type' ) {
			$query->set( 'meta_key', '_tec_session_type' );
			$query->set( 'orderby', 'meta_value' );
		}
	}

	/**
	 * Output for custom columns in the admin screen.
	 *
	 * @since 1.0.0
	 *
	 * @param string $column  The name of the current column.
	 * @param int    $post_id The ID of the current post.
	 */
	public function manage_post_types_columns_output( string $column, int $post_id ) {
		switch ( $column ) {
			case 'conference_session_datetime':
				$session_time = absint( get_post_meta( $post_id, '_tec_session_time', true ) );
				if ( $session_time ) {
					$date_time_str = date( get_option( 'date_format' ), $session_time ) . ', ' . date( get_option( 'time_format' ), $session_time );
					echo esc_html( $date_time_str );
				} else {
					echo '&mdash;';
				}
				break;
			case 'conference_session_speakers':
				$speaker_ids = get_post_meta( $post_id, 'tec_session_speakers', true );
				if ( ! empty( $speaker_ids ) && is_array( $speaker_ids ) ) {
					$speaker_names = array_map( function ( $id ) {
						$speaker_post = get_post( $id );

						return $speaker_post ? $speaker_post->post_title : '';
					}, $speaker_ids );
					echo esc_html( implode( ', ', $speaker_names ) );
				} else {
					echo '&mdash;';
				}
				break;
			case 'conference_session_sponsors':
				$sponsor_ids = get_post_meta( $post_id, 'tec_session_sponsors', true );
				if ( ! empty( $sponsor_ids ) && is_array( $sponsor_ids ) ) {
					$sponsor_names = array_map( function ( $id ) {
						$sponsor_post = get_post( $id );

						return $sponsor_post ? $sponsor_post->post_title : '';
					}, $sponsor_ids );
					echo esc_html( implode( ', ', $sponsor_names ) );
				} else {
					echo '&mdash;';
				}
				break;
			case 'conference_session_type':
				$session_type = get_post_meta( $post_id, '_tec_session_type', true );
				$types        = [
					'session'   => 'Regular Session',
					'mainstage' => 'Mainstage',
					'custom'    => 'Break, Lunch, etc.'
				];
				echo array_key_exists( $session_type, $types ) ? esc_html( $types[ $session_type ] ) : '&mdash;';
				break;
			default:
		}
	}

	/**
	 * Adds or modifies the columns in the admin screen for custom post types.
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns The existing columns.
	 *
	 * @return array The modified columns.
	 */
	public function manage_post_types_columns( array $columns ): array {
		$current_filter = current_filter();

		switch ( $current_filter ) {
			case 'manage_tec_session_posts_columns':
				$new_columns = [
					'cb'                          => $columns['cb'],  // Checkbox (usually the first column)
					'conference_session_datetime' => __( 'Date & Time', 'event-schedule-manager' ),
					'title'                       => $columns['title'],  // Title
					'conference_session_type'     => __( 'Session Type', 'event-schedule-manager' ),
					'conference_session_speakers' => __( 'Speakers', 'event-schedule-manager' ),
					'conference_session_sponsors' => __( 'Sponsors', 'event-schedule-manager' )
				];
				$columns     = array_merge( $new_columns, $columns );
				unset( $columns['conference_session_time'], $columns['conference_session_date'] );  // Remove old date and time columns
				break;
			default:
		}

		return $columns;
	}

	/**
	 * Defines sortable columns in the admin screen.
	 *
	 * @since 1.0.0
	 *
	 * @param array $sortable The existing sortable columns.
	 *
	 * @return array The modified sortable columns.
	 */
	public function manage_sortable_columns( array $sortable ): array {
		$current_filter = current_filter();

		if ( $current_filter !== 'manage_edit-tec_session_sortable_columns' ) {
			return $sortable;
		}

		$sortable['conference_session_datetime'] = '_tec_session_time';
		$sortable['conference_session_type']     = '_tec_session_type';
		foreach ( [ Plugin::TRACK_TAXONOMY, Plugin::LOCATION_TAXONOMY ] as $column ) {
			$sortable[ 'taxonomy-' . $column ] = $column;
		}

		return $sortable;
	}

	/**
	 * Sorts posts by taxonomy terms in the admin list.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string|mixed> $clauses  SQL clauses for fetching posts.
	 * @param WP_Query            $wp_query The WP_Query object.
	 *
	 * @return array<string|mixed> Modified SQL clauses.
	 */
	public function sort_by_tax( array $clauses, WP_Query $wp_query ): array {
		if ( ! is_admin() ) {
			return $clauses;
		}

		$orderby = $wp_query->query['orderby'] ?? null;

		$taxonomy_map = [
			Plugin::TRACK_TAXONOMY    => Plugin::TRACK_TAXONOMY,
			Plugin::LOCATION_TAXONOMY => Plugin::LOCATION_TAXONOMY,
		];

		$taxonomy = null;
		if ( is_string( $orderby ) || is_int( $orderby ) ) {
			$taxonomy = $taxonomy_map[ $orderby ] ?? null;
		}

		if ( $taxonomy === null ) {
			return $clauses;
		}

		global $wpdb;

		$smashed_terms_sql = $wpdb->prepare( "SELECT GROUP_CONCAT( {$wpdb->terms}.name ORDER BY name ASC ) AS smashed_terms
			FROM {$wpdb->term_relationships}
			LEFT JOIN {$wpdb->term_taxonomy} ON {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id AND taxonomy = %s
			LEFT JOIN {$wpdb->terms} ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id
			WHERE {$wpdb->term_relationships}.object_id = {$wpdb->posts}.ID", $taxonomy );

		$clauses['fields']  .= ",( $smashed_terms_sql ) AS smashed_terms";
		$clauses['orderby'] = 'smashed_terms ' . $this->get_sort_direction( $wp_query );

		return $clauses;
	}

	/**
	 * Determines the sort direction for the WP_Query.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query $wp_query The WP_Query object.
	 *
	 * @return string The sort direction ('ASC' or 'DESC').
	 */
	public function get_sort_direction( WP_Query $wp_query ): string {
		return strtoupper( $wp_query->get( 'order', 'ASC' ) ) === 'ASC' ? 'ASC' : 'DESC';
	}
}
