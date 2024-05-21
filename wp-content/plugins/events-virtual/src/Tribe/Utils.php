<?php
/**
 * Provides utility methods used across the plugin.
 *
 * @since   1.0.4
 *
 * @package Tribe\Events\Virtual
 */

namespace Tribe\Events\Virtual;

use Tribe__Cache_Listener as Triggers;
use Tribe__Events__Main as TEC;

/**
 * Class Utils
 *
 * @since   1.0.4
 *
 * @package Tribe\Events\Virtual
 */
class Utils {

	/**
	 * Returns the total count of Virtual Events in any post status present on the site.
	 *
	 * @return int The count of total events on the site
	 */
	public static function get_virtual_events_count() {
		/** @var \Tribe__Cache $cache */
		$cache      = tribe( 'cache' );
		$expiration = DAY_IN_SECONDS;
		$trigger    = Triggers::TRIGGER_SAVE_POST;
		$count      = $cache->get( 'events_virtual_count', $trigger, false, $expiration );

		if ( false !== $count ) {
			return (int) $count;
		}

		/** @var \wpdb $wpdb */
		global $wpdb;

		$query = "SELECT COUNT( pm.post_id )
			FROM $wpdb->postmeta pm
			LEFT JOIN {$wpdb->posts} p
				ON p.ID = pm.post_id AND p.post_type = %s
			WHERE meta_key = '%s'
			  AND meta_value != ''";

		$count = $wpdb->get_var(
			$wpdb->prepare(
				$query,
				TEC::POSTTYPE,
				Event_Meta::$key_virtual
			)
		);

		if ( null === $count ) {
			// Query failure, do not store the result.
			return 0;
		}

		$cache->set( 'events_virtual_count', (int) $count, $expiration, $trigger );

		return (int) $count;
	}
}
