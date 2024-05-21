<?php

namespace Barn2\Plugin\Posts_Table_Pro\Admin;

use Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Service,
	Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Conditional,
	Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Util;

defined( 'ABSPATH' ) || exit;

/**
 * Handles functionality on the Pages list table screen
 *
 * @package   Barn2/posts-table-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Page_List implements Registerable, Service, Conditional {

	/**
	 * {@inheritdoc}
	 */
	public function is_required() {
		return Util::is_admin();
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_filter( 'display_post_states', [ $this, 'display_post_states' ], 10, 2 );
	}

	/**
	 * Add a post display state for the search page.
	 *
	 * @param array     $post_states An array of post display states.
	 * @param \WP_Post  $post        The current post object.
	 */
	public function display_post_states( $post_states, $post ) {
		if ( $this->get_page_id( 'search_page' ) === $post->ID ) {
			$post_states['ptp_search_page'] = __( 'Posts Table Pro Search Page', 'posts-table-pro' );
		}

		return $post_states;
	}

	/**
	 * Returns the document page ID
	 *
	 * @return int $page_id
	 */
	private function get_page_id( $page_key ) {
		$page_id = get_option( "ptp_$page_key" );

		return $page_id ? absint( $page_id ) : -1;
	}

}
