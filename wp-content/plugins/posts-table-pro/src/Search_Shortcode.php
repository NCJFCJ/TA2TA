<?php

namespace Barn2\Plugin\Posts_Table_Pro;

use Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Service,
	Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Conditional,
	Barn2\Plugin\Posts_Table_Pro\Dependencies\Lib\Util as Lib_Util;

defined( 'ABSPATH' ) || exit;

/**
 * This class handles the global doc search shortcode.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Search_Shortcode implements Service, Registerable, Conditional {

	const SHORTCODE = 'ptp_search';

	/**
	 * {@inheritdoc}
	 */
	public function is_required() {
		return Lib_Util::is_front_end();
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_shortcode( self::SHORTCODE, [ self::class, 'do_shortcode' ] );
	}

	/**
	 * Render the shortcode
	 *
	 * @param array $atts
	 * @param string $content
	 * @return string
	 */
	public static function do_shortcode( $atts, $content = '' ) {

        wp_enqueue_style( 'posts-table-pro-search-box' );

		$placeholder = isset( $atts['placeholder'] ) && ! empty( $atts['placeholder'] ) ? $atts['placeholder'] : esc_html__( 'Search posts...', 'posts-table-pro' );
		$button_text = isset( $atts['button_text'] ) && ! empty( $atts['button_text'] ) ? $atts['button_text'] : esc_html__( 'Search', 'posts-table-pro' );

		return Search_Handler::get_search_box_html( 'shortcode', $placeholder, $button_text );
	}

}
