<?php
/**
 * Handles Event Schedule Manager Block,
 *
 * @since 1.0.0
 *
 * @package TEC\Conference\Editor
 */

namespace TEC\Conference\Editor;

use TEC\Conference\Plugin;
use TEC\Conference\Views\Shortcode\Schedule;
use WP_Query;

/**
 * Class Block
 *
 * @since 1.0.0
 *
 * @package TEC\Conference\Editor
 */
class Block {

	/**
	 * Registers the conference schedule block.
	 *
	 * @since 1.0.0
	 */
	public function register_block() {
		register_block_type( 'tec/schedule-block', [
			'editor_script'   => 'event-schedule-manager-schedule-block-js',
			'attributes'      => [
				'date'         => [ 'type' => 'string' ],
				'color_scheme' => [ 'type' => 'string' ],
				'layout'       => [ 'type' => 'string' ],
				'row_height'   => [ 'type' => 'string' ],
				'session_link' => [ 'type' => 'string' ],
				'tracks'       => [ 'type' => 'string' ],
				'align'        => [ 'type' => 'string' ],
				'content'      => [ 'type' => 'string' ],
			],
			'render_callback' => [ $this, 'schedule_block_output' ],
		] );
	}

	/**
	 * Schedule Block Dynamic content Output.
	 *
	 * @since 1.0.0
	 *
	 * @param array $props An array of attributes from shortcode.
	 *
	 * @return string The HTML output the shortcode.
	 */
	public function schedule_block_output( $props ) {
		$has_sessions = $this->check_for_sessions( $props );
		if ( ! $has_sessions ) {
			return $this->event_schedule_block_message();
		}

		$schedule = new Schedule();

		return $schedule->render_shortcode( $props );
	}

	/**
	 * Check for sessions in 'tec_session' post type with '_tec_session_time' post meta.
	 *
	 * Utilizes WordPress caching to store results for 1 minute for performance optimization.
	 *
	 * @since 1.1.0
	 *
	 * @param array $props An array of attributes from shortcode.
	 *
	 * @return array The IDs of sessions if found, empty array otherwise.
	 */
	public function check_for_sessions( $props ) {
		if ( ! isset( $props['date'] ) || empty( $props['date'] ) ) {
			return [];
		}

		$cache_key = 'sessions_' . md5( wp_json_encode( $props ) );
		$cached    = wp_cache_get( $cache_key );

		if ( $cached !== false ) {
			return $cached;
		}

		$dates       = explode( ',', $props['date'] );
		$session_ids = [];

		// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		foreach ( $dates as $date ) {
			$args = [
				'post_type'      => Plugin::SESSION_POSTTYPE,
				'posts_per_page' => 10,
				'fields'         => 'ids',
				'meta_query'     => [
					'relation' => 'AND',
					[
						'key'     => '_tec_session_time',
						'compare' => 'EXISTS',
					],
					[
						'key'     => '_tec_session_time',
						'value'   => [
							strtotime( $date ),
							strtotime( $date . ' +1 day' ),
						],
						'compare' => 'BETWEEN',
						'type'    => 'NUMERIC',
					],
				],
			];

			$query = new WP_Query( $args );

			if ( $query->have_posts() ) {
				$session_ids = array_merge( $session_ids, $query->posts );
			}
		}
		// phpcs:enable WordPress.DB.SlowDBQuery.slow_db_query_meta_query

		wp_cache_set( $cache_key, $session_ids, '', 60 );

		return $session_ids;
	}

	/**
	 * Outputs a message for the Event Schedule Block.
	 *
	 * Displays an informative message about the necessity of choosing a date with sessions.
	 * The message is localized and includes a link to a KB article.
	 *
	 * @since 1.1.0
	 *
	 * @return string The HTML output with the message.
	 */
	public function event_schedule_block_message() {
		$kb_link = 'https://evnt.is/1bd6';

		ob_start();
		?>
		<div class="tec-event-schedule-manager__schedule-block--message-wrap">
			<div class="tec-event-schedule-manager__schedule-block-message-title-wrap">
				<?php
					// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
					// phpcs:disable TEC.XSS.EscapeOutput.OutputNotEscaped
					echo $this->get_icon();
					// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
					// phpcs:enable TEC.XSS.EscapeOutput.OutputNotEscaped
				?>
				<h2>
					<?php echo esc_html_x( 'Event Schedule', 'The heading for the Event Schedule block.', 'event-schedule-manager' ); ?>
				</h2>
			</div>
			<?php
			printf(
				/* Translators: %1$s - open link tag, %2$s - close link tag. */
				esc_html_x( 'Select a date(s) with at least one session to display the schedule. %1$sLearn more%2$s.', 'No sessions found message for Event Schedule block.', 'event-schedule-manager' ),
				'<a href="' . esc_url( $kb_link ) . '" class="tec-event-schedule-manager__schedule-block-message--link" target="_blank">',
				'</a>'
			);
			?>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Get Icon.
	 *
	 * @since 1.1.0
	 */
	public function get_icon() {
		return '<svg width="32" height="32" viewBox="0 0 89 76" fill="none" xmlns="http://www.w3.org/2000/svg">
		    <path d="M73.7927 1.99344V6.22951H78.2801C84.2005 6.22951 89 11.0267 89 16.9443V65.2852C89 71.2028 84.2005 76 78.2801 76H23.89C22.9262 76 22.1449 75.2191 22.1449 74.2557C22.1449 73.2924 22.9262 72.5115 23.89 72.5115H78.2801C82.273 72.5115 85.5098 69.2762 85.5098 65.2852V24.4158H19.6947V27.5571C19.6947 28.5204 18.9134 29.3013 17.9496 29.3013C16.9858 29.3013 16.2045 28.5204 16.2045 27.5571V16.9443C16.2045 11.0267 21.0039 6.22951 26.9244 6.22951H32.9076V1.99344C32.9076 1.16583 33.4121 0.456018 34.1306 0.154576C34.3178 0.0558732 34.5312 0 34.7576 0C34.7794 0 34.8011 0.000518999 34.8227 0.00154559C34.849 0.000518999 34.8754 0 34.902 0C36.0034 0 36.8964 0.892494 36.8964 1.99344V6.22951H69.8039V1.99344C69.8039 1.12822 70.3554 0.391736 71.1262 0.116028C71.2935 0.0414534 71.4789 0 71.6739 0C71.6927 0 71.7114 0.000384021 71.73 0.00114826C71.7527 0.000384021 71.7754 0 71.7983 0C72.8998 0 73.7927 0.892494 73.7927 1.99344Z"/>
		    <path d="M57.8347 39.4682C57.8347 40.5691 56.9418 41.4615 55.8404 41.4615H43.8746C42.7732 41.4615 41.8803 40.5691 41.8803 39.4682C41.8803 38.3673 42.7732 37.4748 43.8746 37.4748H55.8404C56.9418 37.4748 57.8347 38.3673 57.8347 39.4682Z"/>
		    <path d="M76.98 49.0362C76.98 50.1371 76.0871 51.0296 74.9857 51.0296H47.4644C46.363 51.0296 45.4701 50.1371 45.4701 49.0362C45.4701 47.9353 46.363 47.0428 47.4644 47.0428H74.9857C76.0871 47.0428 76.98 47.9353 76.98 49.0362Z"/>
		    <path d="M70.9971 58.6043C70.9971 59.7052 70.1042 60.5976 69.0028 60.5976H45.0712C43.9698 60.5976 43.0769 59.7052 43.0769 58.6043C43.0769 57.5034 43.9698 56.6109 45.0712 56.6109H69.0028C70.1042 56.6109 70.9971 57.5034 70.9971 58.6043Z"/>
		    <path fill-rule="evenodd" clip-rule="evenodd"
		          d="M36.8964 51.3312C36.8964 61.5149 28.6368 69.7705 18.4482 69.7705C8.25953 69.7705 0 61.5149 0 51.3312C0 41.1474 8.25953 32.8918 18.4482 32.8918C28.6368 32.8918 36.8964 41.1474 36.8964 51.3312ZM18.2736 40.8656C17.5274 40.8656 16.9524 41.5591 16.9524 42.375V52.7614C16.9524 53.5772 17.5274 54.2708 18.2736 54.2708H25.4531C26.1993 54.2708 26.7743 53.5772 26.7743 52.7614C26.7743 51.9455 26.1993 51.2519 25.4531 51.2519H19.5948V42.375C19.5948 41.5591 19.0198 40.8656 18.2736 40.8656Z"
		          />
		</svg>';
	}
}
