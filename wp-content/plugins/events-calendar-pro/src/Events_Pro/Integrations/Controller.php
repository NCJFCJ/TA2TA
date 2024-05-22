<?php
/**
 * Controller for Events Calendar Pro integrations.
 *
 * @since 6.4.0
 *
 * @package TEC\Events_Pro\Integrations
 */

namespace TEC\Events_Pro\Integrations;

use TEC\Common\Contracts\Provider\Controller as Controller_Base;

/**
 * Class Controller
 *
 * @since 6.4.0
 *
 * @package TEC\Events_Pro\Integrations
 */
class Controller extends Controller_Base {

	/**
	 * Determines if this controller will register.
	 * This is present due to how UOPZ works, it will fail we have the boolean living on the method.
	 *
	 * @since 6.4.0
	 *
	 * @var bool $is_active If the integration is active.
	 */
	protected bool $is_active = true;

	/**
	 * {@inheritDoc}
	 *
	 * @since 6.4.0
	 *
	 * @return bool
	 */
	public function is_active(): bool {
		return $this->is_active;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 6.4.0
	 */
	public function do_register(): void {
		$this->boot();

		// Load plugin integration providers once the TEC integration has loaded.
		$this->container->register_on_action( 'tec_events_elementor_loaded', Plugins\Elementor\Controller::class );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 6.4.0
	 */
	public function unregister(): void {}
}
