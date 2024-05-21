<?php
/**
 * Handles the editor functionality of the Events Virtual plugin.
 *
 * @since 1.7.1
 *
 * @package Tribe\Events\Virtual\Editor
 */

namespace Tribe\Events\Virtual\Editor;

use \Tribe__Events__Main as TEC;
use TEC\Common\Contracts\Service_Provider;

/**
 * Class Provider
 *
 * Initialize Gutenberg editor blocks and styles.
 *
 * @since 1.7.1
 *
 * @package Tribe\Events\Virtual\Editor
 */
class Provider extends Service_Provider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 1.7.1
	 */
	public function register() {
		$this->container->singleton( static::class, $this );

		$this->container->register( Assets::class );
		$this->container->singleton( 'events-virtual.editor.blocks.virtual', Blocks\Virtual_Event::class, [ 'load' ] );
		$this->container->singleton( Template\Frontend::class, Template\Frontend::class );
		$this->container->singleton( Template\Admin::class, Template\Admin::class );

		$this->hook();
	}

	/**
	 * Hooks actions from the editor into the correct places.
	 *
	 * @since 1.7.1
	 *
	 * @return bool
	 */
	public function hook() {
		add_action( 'tribe_editor_register_blocks', [ $this, 'register_blocks' ] );
		add_filter( 'tribe_events_editor_default_template', [ $this, 'add_event_template_blocks' ], 20, 3 );
	}

	/**
	 * Register the Virtual Event Block.
	 *
	 * Instead of using `init` to register our own blocks, this allows us to add
	 * it when the proper place shows up
	 *
	 * @since 1.7.1
	 */
	public function register_blocks() {
		tribe( 'events-virtual.editor.blocks.virtual' )->register();
	}

	/**
	 * Adds the required blocks into the Events Post Type default templates.
	 *
	 * @since 1.7.1
	 * @since 1.12.0 - Removed early return that made the filter unreachable.
	 *
	 * @param  array               $template   Array of all the templates used by default.
	 * @param  string              $post_type  Which post type we are filtering.
	 * @param  array<string|mixed> $args       Array of configurations for the post type.
	 *
	 * @return array $template Modified arguments used to setup the CPT template.
	 */
	public function add_event_template_blocks( $template, $post_type, $args ) {
		$post = tribe_get_request_var( 'post' );

		// Basically sets up up a different template if this is a classic event.
		if ( ! has_blocks( $post ) ) {
			return $template;
		}

		// To be safe, ensure we have an array.
		if ( ! is_array( $template ) ) {
			$template = (array) $template;
		}

		/**
		 * Allow modifying the default block template for Virtual Events.
		 *
		 * @since 1.7.1
		 *
		 * @param  array               $template   Array of the templates we're adding.
		 * @param  string              $post_type  Which post type we are filtering.
		 * @param  array<string|mixed> $args       Array of configurations for the post type.
		 */
		$virtual_template = apply_filters( 'tribe_events_editor_default_virtual_template', [ 'tribe/virtual-event' ], TEC::POSTTYPE, $args );

		// Add our new template to the mix.
		$template[] = $virtual_template;

 		return $template;
	}
}
