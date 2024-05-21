<?php
/**
 * Handles the compatibility with Divi theme.
 *
 * @since   5.3.1
 *
 * @package Tribe\Events\Filterbar\Compatibility\Divi
 */

namespace Tribe\Events\Filterbar\Compatibility\Divi;

use TEC\Common\Contracts\Service_Provider as Provider_Contract;

/**
 * Class Service_Provider
 *
 * @since   5.3.1
 *
 * @package Tribe\Events\Filterbar\Compatibility\Divi
 */
class Service_Provider extends Provider_Contract {
	/**
	 * Register the bindings and filters required to ensure compatibility Divi theme.
	 *
	 * @since 5.3.1
	 */
	public function register() {
		$this->container->singleton( self::class, $this );
		$this->container->singleton( 'filterbar.compatibility.divi-theme', $this );

		add_filter( 'et_builder_enable_jquery_body', array( $this, 'disable_jquery_body' ), 10, 1 );
	}

	/**
	 * Disable Divi jQuery Body on Single Events.
	 *
	 * @since 5.3.1
	 *
	 * @param bool $enabled Whether to disable the jQuery body.
	 *
	 * @return bool Whether to disable the jQuery body.
	 */
	public function disable_jquery_body( $enabled ) {
		return $this->container->make( Scripts::class )->disable_jquery_body( $enabled );
	}
}
