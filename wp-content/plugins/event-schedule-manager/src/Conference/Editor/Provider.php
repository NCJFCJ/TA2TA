<?php
/**
 * Provider for Editor Related Functionality.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Editor
 */

namespace TEC\Conference\Editor;

use TEC\Conference\Contracts\Service_Provider;

/**
 * Class Provider
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Editor
 */
class Provider extends Service_Provider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		// Register the SP on the container.
		$this->container->singleton( 'tec.conference.editor.provider', $this );

		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Adds required actions for post types.
	 *
	 * @since 1.0.0
	 */
	protected function add_actions() {
		add_action( 'init', [ $this, 'register_block' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'register_editor_assets' ] );
	}

	/**
	 * Registers the Event Schedule Manager block.
	 *
	 * @since 1.0.0
	 */
	public function register_block() {
		$this->container->make( Block::class )->register_block();
	}

	/**
	 * Registers the editor assets.
	 *
	 * @since 1.0.0
	 */
	public function register_editor_assets() {
		$this->container->make( Assets::class )->register_editor_assets();
	}

	/**
	 * Adds required filters for editor.
	 *
	 * @since 1.0.0
	 */
	protected function add_filters() {}
}
