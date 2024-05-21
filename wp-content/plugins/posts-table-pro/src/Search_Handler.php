<?php

namespace Barn2\Plugin\Posts_Table_Pro;

use Barn2\Plugin\Posts_Table_Pro\Util\Options;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Service;

defined( 'ABSPATH' ) || exit;

/**
 * Handle the Global Search page.
 *
 * @package   Barn2/posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Search_Handler implements Registerable, Service {

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_action( 'template_redirect', [ $this, 'handle_search_redirect' ] );
		add_filter( 'the_content', [ $this, 'filter_search_page_content' ], 999, 1 );

		add_filter( 'wp_get_nav_menu_items', [ $this, 'protect_nav_menus' ], 10, 1 );
		add_filter( 'wp_list_pages_excludes', [ $this, 'protect_list_pages' ], 10, 1 );
	}

	/**
	 * Redirect a search from the widget or shortcode to the search results page.
	 */
	public function handle_search_redirect() {
		$search_query = filter_input( INPUT_GET, 'ptp_search', FILTER_SANITIZE_SPECIAL_CHARS );

		if ( is_null( $search_query ) || $search_query === false ) {
			return;
		}

		$search_results_page_id = Options::get_search_page_option();

		if ( $search_results_page_id && ! is_page( $search_results_page_id ) ) {
			wp_safe_redirect( add_query_arg( 'ptp_search', $search_query, get_permalink( $search_results_page_id ) ), 301 );
			exit;
		}
	}

	/**
	 * Filter in the search box and posts table to the search results page.
	 *
	 * @param string $content
	 * @return string $content
	 */
	public function filter_search_page_content( $content ) {
		$search_results_page_id = Options::get_search_page_option();
		$search_query           = filter_input( INPUT_GET, 'ptp_search', FILTER_SANITIZE_SPECIAL_CHARS );

		if ( ! $search_results_page_id || ! is_page( $search_results_page_id ) || ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}

		wp_enqueue_style( 'posts-table-pro-search-box' );

		remove_filter( 'the_content', [ $this, 'filter_search_page_content' ], 999, 1 );

		$content .= self::get_search_box_html( 'single-content', esc_html__( 'Search posts...', 'posts-table-pro' ), esc_html__( 'Search', 'posts-table-pro' ) );

		if ( $search_query ) {
			$content .= '<hr>' . ptp_get_posts_table(
					apply_filters(
						'posts_table_search_results_page_args',
						[
							'search_term' => $search_query,
							'search_box'  => false
						],
						$search_query
					)
				);
		} elseif ( ! is_null( $search_query ) ) {
			$content .= esc_html__( 'No matching results for your search term.', 'posts-table-pro' );
		}

		return $content;
	}

	/**
	 * Remove the search results page from list_pages
	 *
	 * @param array $excludes
	 * @return array
	 */
	public function protect_list_pages( $excludes ) {
		$search_results_page_id = Options::get_search_page_option();

		if ( $search_results_page_id === false ) {
			return $excludes;
		}

		return array_unique( array_merge( $excludes, [ (int) $search_results_page_id ] ) );
	}

	/**
	 * Remove the search results page from the navigation menus.
	 *
	 * @param array $menu_items
	 * @return array
	 */
	public function protect_nav_menus( $menu_items ) {
		$search_results_page_id = Options::get_search_page_option();

		if ( $search_results_page_id === false ) {
			return $menu_items;
		}

		$filtered_menu_items = array_filter(
			$menu_items,
			function ( $menu_item ) use ( $search_results_page_id ) {
				return $menu_item->object_id !== $search_results_page_id;
			}
		);

		return array_values( $filtered_menu_items );
	}

	/**
	 * Outputs a global search box.
	 *
	 * @param string $view
	 * @param string $placeholder
	 * @param string $button_text
	 */
	public static function get_search_box_html( $view, $placeholder, $button_text ) {
		$search_query = filter_input( INPUT_GET, 'ptp_search', FILTER_SANITIZE_SPECIAL_CHARS ) ?? '';

		ob_start();
		?>
		<div class="ptp-posts-search-container">
			<form role="search" method="get" class="ptp-posts-search ptp-<?php echo esc_attr( $view ); ?>" action="<?php echo esc_attr( site_url() ); ?>">
				<label class="screen-reader-text" for="ptp-posts-search-field"><?php esc_html_e( 'Search', 'posts-table-pro' ); ?></label>
				<input type="search" id="ptp-posts-search-field" class="ptp-posts-search-field" placeholder="<?php echo esc_attr( $placeholder ); ?>" value="<?php echo esc_attr( $search_query ); ?>" name="ptp_search">
				<button type="submit" class="button" value="Search"><?php echo esc_html( $button_text ); ?></button>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}

}
