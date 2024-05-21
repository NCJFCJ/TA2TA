<?php
/**
 * Class that handles interfacing with core Site Health.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Site_Health
 */

namespace TEC\Conference\Site_Health;

use TEC\Conference\Plugin;
use WP_Query;

/**
 * Class Site_Health
 *
 * @since   1.0.0
 * @package TEC\Conference\Site_Health
 */
class Info_Section extends Info_Section_Abstract {
	/**
	 * Slug for the section.
	 *
	 * @since 1.0.0
	 *
	 * @var string $slug
	 */
	protected static string $slug = Plugin::SLUG;

	/**
	 * Label for the section.
	 *
	 * @since 1.0.0
	 *
	 * @var string $label
	 */
	protected string $label;

	/**
	 * If we should show the count of fields in the site health info page.
	 *
	 * @since 1.0.0
	 *
	 * @var bool $show_count
	 */
	protected bool $show_count = false;

	/**
	 * If this section is private.
	 *
	 * @since 1.0.0
	 *
	 * @var bool $is_private
	 */
	protected bool $is_private = false;

	/**
	 * Description for the section.
	 *
	 * @since 1.0.0
	 *
	 * @var string $description
	 */
	protected string $description;

	/**
	 * Sets up the section and internally add the fields.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->label       = esc_html_x( 'Event Schedule Manager', 'Site Health Info Title.','event-schedule-manager' );
		$this->description = esc_html_x( 'This section contains information on the Event Schedule Manager Plugin.', 'Site Health Info Description.', 'event-schedule-manager' );
		$this->add_fields();
	}

	/**
	 * Generates and adds our fields to the section.
	 *
	 * @since 1.0.0
	 *
	 * @param array $info The debug information to be added to the core information page.
	 *
	 * @return array The debug information to be added to the core information page.
	 */
	public function add_fields(): void {
		$this->add_field(
			Factory::generate_post_status_count_field(
				'session_counts',
				Plugin::SESSION_POSTTYPE,
				10
			)
		);

		$this->add_field(
			Factory::generate_generic_field(
				'track_counts',
				esc_html_x( 'Track Terms', 'Site Health Info Title for Track terms.','event-schedule-manager' ),
				$this->get_total_terms( Plugin::TRACK_TAXONOMY ),
				11
			)
		);

		$this->add_field(
			Factory::generate_generic_field(
				'track_avg_per_cpt',
				esc_html_x( 'Average number of Tracks per Session', 'Site Health Info Title for average number of tracks per session.','event-schedule-manager' ),
				$this->average_terms_per_cpt( Plugin::SESSION_POSTTYPE, Plugin::TRACK_TAXONOMY ),
				12
			)
		);

		$this->add_field(
			Factory::generate_generic_field(
				'location_counts',
				esc_html_x( 'Location Terms', 'Site Health Info Title for Location terms.','event-schedule-manager' ),
				$this->get_total_terms( Plugin::LOCATION_TAXONOMY ),
				13
			)
		);

		$this->add_field(
			Factory::generate_generic_field(
				'location_avg_per_cpt',
				esc_html_x( 'Average number of Locations per Session', 'Site Health Info Title for average number of locations per session.','event-schedule-manager' ),
				$this->average_terms_per_cpt( Plugin::SESSION_POSTTYPE, Plugin::LOCATION_TAXONOMY ),
				14
			)
		);

		$this->add_field(
			Factory::generate_post_status_count_field(
				'speaker_counts',
				Plugin::SPEAKER_POSTTYPE,
				20
			)
		);

		$this->add_field(
			Factory::generate_generic_field(
				'group_counts',
				esc_html_x( 'Group Terms', 'Site Health Info Title for Group terms.','event-schedule-manager' ),
				$this->get_total_terms( Plugin::GROUP_TAXONOMY ),
				21
			)
		);

		$this->add_field(
			Factory::generate_generic_field(
				'group_avg_per_cpt',
				esc_html_x( 'Average number of Groups per Speaker', 'Site Health Info Title for average number of groups per speaker.','event-schedule-manager' ),
				$this->average_terms_per_cpt( Plugin::SPEAKER_POSTTYPE, Plugin::GROUP_TAXONOMY ),
				22
			)
		);

		$this->add_field(
			Factory::generate_generic_field(
				'speaker_uses_typed',
				esc_html_x( 'Sessions using Typed Speaker Names', 'Site Health Info Title for Sessions using Typed Speaker Names.','event-schedule-manager' ),
				$this->count_cpt_with_meta( Plugin::SESSION_POSTTYPE, 'tec_session_speaker_display', 'typed' ),
				23
			)
		);

		$this->add_field(
			Factory::generate_generic_field(
				'speaker_uses_cpt',
				esc_html_x( 'Sessions using Speaker Post Type', 'Site Health Info Title for Sessions using Speaker Post Type.','event-schedule-manager' ),
				$this->count_cpt_with_meta( Plugin::SESSION_POSTTYPE, 'tec_session_speaker_display', 'cpt' ),
				24
			)
		);

		$this->add_field(
			Factory::generate_post_status_count_field(
				'sponsor_counts',
				Plugin::SPONSOR_POSTTYPE,
				30
			)
		);

		$this->add_field(
			Factory::generate_generic_field(
				'sponsor_level_counts',
				esc_html_x( 'Sponsor Level Terms', 'Site Health Info Title for SponsorLevel terms.','event-schedule-manager' ),
				$this->get_total_terms( Plugin::SPONSOR_LEVEL_TAXONOMY ),
				31
			)
		);

		$this->add_field(
			Factory::generate_generic_field(
				'sponsor_level_avg_per_cpt',
				esc_html_x( 'Average number of Sponsor Levels per Sponsor', 'Site Health Info Title for average number of sponsor levels per sponsor.','event-schedule-manager' ),
				$this->average_terms_per_cpt( Plugin::SPONSOR_POSTTYPE, Plugin::SPONSOR_LEVEL_TAXONOMY ),
				32
			)
		);
	}

	/**
	 * Get the total number of terms in a custom taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @param string $taxonomy The taxonomy slug.
	 *
	 * @return int The total number of terms.
	 */
	public function get_total_terms( string $taxonomy ): int {
		$term_count = wp_count_terms( $taxonomy );

		return is_wp_error( $term_count ) ? 0 : intval( $term_count );
	}

	/**
	 * Get the count of custom post type entries with specific meta value.
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_type  The custom post type.
	 * @param string $meta_key   The meta key to look for.
	 * @param string $meta_value The meta value to look for.
	 *
	 * @return int The total number of posts that match.
	 */
	public function count_cpt_with_meta( string $post_type, string $meta_key, string $meta_value ): int {
		$args = [
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'meta_key'       => $meta_key,
			'meta_value'     => $meta_value,
			'fields'         => 'ids',
			'posts_per_page' => 3000,
		];

		$query = new WP_Query( $args );

		return $query->post_count;
	}

	/**
	 * Calculate the average number of custom taxonomy terms per custom post type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_type The custom post type.
	 * @param string $taxonomy  The custom taxonomy.
	 *
	 * @return float The average number of terms per post.
	 */
	public function average_terms_per_cpt( string $post_type, string $taxonomy ): float {
		// Count total number of posts in the custom post type.
		$post_count_args = [
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'posts_per_page' => 3000,
		];
		$post_query      = new WP_Query( $post_count_args );
		$total_posts     = $post_query->post_count;

		// If there are no posts, return 0
		if ( $total_posts === 0 ) {
			return 0.0;
		}

		// Loop through each post to get total number of terms.
		$total_terms = 0;
		foreach ( $post_query->posts as $post_id ) {
			$terms = get_the_terms( $post_id, $taxonomy );
			if ( is_array( $terms ) ) {
				$total_terms += count( $terms );
			}
		}

		// Calculate the average.
		$average = round( $total_terms / $total_posts, 2 );

		return (float) $average;
	}
}
