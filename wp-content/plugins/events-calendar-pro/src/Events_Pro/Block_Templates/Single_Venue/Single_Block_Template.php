<?php
/**
 * Single Block Template
 */

namespace TEC\Events_Pro\Block_Templates\Single_Venue;

use TEC\Events_Pro\Blocks\Single_Venue\Block;
use Tribe__Events__Pro__Main;
use Tribe__Template as Template_Engine;
use TEC\Common\Editor\Full_Site\Template_Utils;
use WP_Block_Template;
use TEC\Events\Block_Templates\Block_Template_Contract;

/**
 * Class Single_Block_Template
 *
 * @since   6.3.2
 *
 * @package TEC\Events_Pro\Block_Templates\Single_Venue
 */
class Single_Block_Template implements Block_Template_Contract {

	/**
	 * @since 6.3.2
	 *
	 * @var Block The registered block for this template.
	 */
	protected Block $block;

	/**
	 * Stores the template object.
	 *
	 * @since 6.3.2
	 *
	 * @var Template_Engine $template_engine The template object.
	 */
	protected Template_Engine $template_engine;

	/**
	 * Constructor for Single Venue Block Template.
	 *
	 * @since 6.3.2
	 *
	 * @param Block $block The registered Block for Single Venue.
	 */
	public function __construct( Block $block ) {
		$this->block = $block;
	}

	/**
	 * The ID of this block.
	 *
	 * @since 6.3.2
	 *
	 * @return string The WP Block Template ID.
	 */
	public function id(): string {
		return $this->block->get_namespace() . '//' . $this->block->slug();
	}

	/**
	 * Which is the name/slug of this template block.
	 *
	 * @since 6.3.2
	 *
	 * @return string
	 */
	public function slug(): string {
		return $this->block->slug();
	}

	/**
	 * Creates then returns the WP_Block_Template object for single venue.
	 *
	 * @since 6.3.2
	 *
	 * @return null|WP_Block_Template The hydrated single event template object.
	 */
	protected function create_wp_block_template(): ?WP_Block_Template {
		$post_title = sprintf(
			/* translators: %1$s: Event (singular) */
			esc_html_x( 'Single %1$s', 'The Full Site editor venue block navigation title', 'the-events-calendar' ),
			tribe_get_venue_label_singular()
		);

		$post_excerpt = sprintf(
			/* translators: %1$s: event (singular) */
			esc_html_x( 'Displays a single %1$s.', 'The Full Site editor venue block navigation description', 'the-events-calendar' ),
			tribe_get_venue_label_singular_lowercase()
		);

		$insert = [
			'post_name'    => $this->block->slug(),
			'post_title'   => $post_title,
			'post_excerpt' => $post_excerpt,
			'post_type'    => 'wp_template',
			'post_status'  => 'publish',
			'post_content' => Template_Utils::inject_theme_attribute_in_content( $this->get_template_engine()->template( 'single-venue', [], false ) ),
			'tax_input'    => [
				'wp_theme' => $this->block->get_namespace(),
			],
		];

		// Create this template.
		return Template_Utils::save_block_template( $insert );
	}

	/**
	 * Returns the template engine for the single venue block.
	 *
	 * @since 6.3.2
	 *
	 * @return Template_Engine
	 */
	protected function get_template_engine(): Template_Engine {
		if ( ! isset( $this->template_engine ) ) {
			$this->template_engine = new Template_Engine();
			$this->template_engine->set_template_origin( Tribe__Events__Pro__Main::instance() );
			$this->template_engine->set_template_folder( 'src/Events_Pro/Block_Templates/Single_Venue/templates' );
			$this->template_engine->set_template_context_extract( true );
			$this->template_engine->set_template_folder_lookup( true );
		}

		return $this->template_engine;
	}

	/**
	 * Creates if non-existent theme post, then returns the WP_Block_Template object for single events.
	 *
	 * @since 6.3.2
	 *
	 * @return null|WP_Block_Template The hydrated single events template object.
	 */
	public function get_block_template(): ?WP_Block_Template {
		$wp_block_template = Template_Utils::find_block_template_by_post( $this->block->slug(), $this->block->get_namespace() );

		// If empty, this is our first time loading our Block Template. Let's create it.
		if ( ! $wp_block_template ) {
			$wp_block_template = $this->create_wp_block_template();
		}

		// Validate we did stuff correctly.
		if ( ! $wp_block_template instanceof WP_Block_Template ) {
			do_action(
				'tribe_log',
				'error',
				'Failed locating our WP_Block_Template for the Single Venue Block',
				[
					'method'    => __METHOD__,
					'slug'      => $this->block->slug(),
					'namespace' => $this->block->get_namespace(),
				]
			);
		}

		return $wp_block_template;
	}
}
