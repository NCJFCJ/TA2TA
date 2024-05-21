<?php

namespace Barn2\Plugin\Posts_Table_Pro\Integration;

use Barn2\Plugin\Posts_Table_Pro\Table_Args;
use Barn2\Plugin\Posts_Table_Pro\Util\Util;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Service;

defined( 'ABSPATH' ) || exit;

/**
 * Handles integration with FacetWP
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class FacetWP implements Registerable, Service {

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		if ( ! class_exists( 'FacetWP' ) ) {
			return;
		}

		add_filter( 'facetwp_template_html', [ $this, 'render_posts_table' ], 10, 2 );
	}

	/**
	 * Load the posts table if template begins with 'ptp_'.
	 *
	 * @param string           $output
	 * @param FacetWP_Renderer $renderer
	 * @return string
	 */
	public function render_posts_table( $output, $renderer ) {
		// check if template name starts with ptp_
		if ( substr( $renderer->template['name'], 0, 4 ) !== 'ptp_' ) {
			return $output;
		}

		$custom_args = apply_filters( 'posts_table_facetwp_custom_args', [], $renderer->template['name'], $renderer );

		// check facetwp version to determine which version of `get_filtered_post_ids` to use.
		$filtered_post_ids = version_compare( FACETWP_VERSION, '4.0.6', '>=' ) ? $renderer->get_filtered_post_ids( $renderer->query_args ) : $renderer->get_filtered_post_ids();

		// add posts table init script.
		$output = $this->get_posts_table_script();

		if ( $renderer->query->found_posts < 1 ) {
			$output .= $this->get_no_posts_message(
				array_merge(
					$custom_args,
					[
						'post_type' => $renderer->query_args['post_type'],
					]
				)
			);
		} else {
			$output .= ptp_get_posts_table(
				array_merge(
					$custom_args,
					[
						'post_type'       => $renderer->query_args['post_type'],
						'filters'         => false,
						'search_box'      => false,
						'reset_button'    => false,
						'cache'           => false,
						'search_on_click' => false,
						'rows_per_page'   => $renderer->query_args['posts_per_page'],
						'sort_by'         => 'post__in',
						'include'         => implode( ',', $filtered_post_ids ),
					]
				)
			);

		}

		return $output;
	}

	/**
	 * Retrieve the string for the no posts message.
	 *
	 * @param array $custom_args
	 * @return string
	 */
	private function get_no_posts_message( $custom_args ) {
		if ( isset( $custom_args['no_posts_filtered_message'] ) && ! empty( $custom_args['no_posts_filtered_message'] ) ) {
			return $custom_args['no_posts_filtered_message'];
		}

		$post_type = $custom_args['post_type'] ?: Table_Args::get_site_defaults()['post_type'];
		$strings   = Util::get_language_strings( $post_type );

		return $strings['zeroRecords'];
	}

	/**
	 * Generate script tag to re-init posts table after facet loading.
	 *
	 * @return string
	 */
	private function get_posts_table_script() {
		$script = "<script>
        ( function( $ ) {
            document.addEventListener( 'facetwp-loaded', function() {
                $( '.facetwp-template table' ).first().postsTable();
             } );
        } )( jQuery );
        </script>";

		return $script;
	}

}
