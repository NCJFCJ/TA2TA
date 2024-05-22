<?php
/**
 * Block Controller
 */

namespace TEC\Events_Pro\Blocks;

use TEC\Common\Contracts\Provider\Controller as Controller_Contract;
use TEC\Events_Pro\Blocks\Single_Venue\Block as Single_Venue_Block;

/**
 * Class Controller
 *
 * @since 6.3.2
 *
 * @package TEC\Events_Pro\Blocks
 */
class Controller extends Controller_Contract {
	/**
	 * Register the provider.
	 *
	 * @since 6.3.2
	 */
	public function do_register(): void {
		$this->add_actions();

		// Register the service provider itself on the container.
		$this->container->singleton( static::class, $this );
	}

	/**
	 * Unhooks actions and filters.
	 *
	 * @since 6.3.2
	 */
	public function unregister(): void {
		$this->remove_actions();
	}

	/**
	 * Adds the actions required by the Blocks components.
	 *
	 * @since 6.3.2
	 */
	protected function add_actions() {
		add_action( 'tribe_editor_register_blocks', [ $this, 'register_single_venue_block' ] );
	}

	/**
	 * Removes registered actions.
	 *
	 * @since 6.3.2
	 */
	public function remove_actions() {
		remove_action( 'tribe_editor_register_blocks', [ $this, 'register_single_venue_block' ] );
	}

	/**
	 * Registers the Single Venue block.
	 *
	 * @since 6.3.2
	 */
	public function register_single_venue_block() {
		return $this->container->make( Single_Venue_Block::class )->register();
	}
}
