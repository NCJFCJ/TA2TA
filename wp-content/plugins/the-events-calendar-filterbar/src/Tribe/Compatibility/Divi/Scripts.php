<?php
/**
 * Class that handles scripts for Divi compatibility.
 *
 * @since   5.3.1
 *
 * @package Tribe\Events\Filterbar\Compatibility\Divi
 */

namespace Tribe\Events\Filterbar\Compatibility\Divi;

/**
 * Class Scripts.
 *
 * @since 5.3.1
 *
 * @package Tribe\Events\Filterbar\Compatibility\Divi
 */
class Scripts {

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
		if ( ! is_singular( \Tribe__Events__Main::POSTTYPE ) ) {
			return $enabled;
		}

		return false;
	}
}
