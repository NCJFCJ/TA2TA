<?php
/**
 * The base implementation of a meetings and conference provider.
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual\Meetings
 */

namespace Tribe\Events\Virtual\Meetings;

use TEC\Common\Contracts\Service_Provider;

/**
 * Class Meeting_Provider
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual\Meetings
 */
abstract class Meeting_Provider extends Service_Provider {

	/**
	 * Returns whether the provider is enable or not via filters.
	 *
	 * If the whole meetings support is not enabled, then all providers will not be enabled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether the provider is enabled or not.
	 */
	public function is_enabled() {
		$slug = $this->get_slug();

		/**
		 * Filters whether a specific meetings and conference provider is enabled or not.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $enabled Whether the specific meetings and conference provider is enabled or not.
		 */
		return (bool) apply_filters( "tribe_events_virtual_meetings_{$slug}_enabled", true );
	}

	/**
	 * Returns the meetings and conference provider slug.
	 *
	 * The value is used to build the filter names.
	 *
	 * @since 1.0.0
	 *
	 * @return string The provider slug.
	 */
	abstract public function get_slug();
}
