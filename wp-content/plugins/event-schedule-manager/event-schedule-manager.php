<?php
/**
 * Plugin Name:       Event Schedule Manager
 * Plugin URI:        https://theeventscalendar.com
 * Description:       Creates session post types for conference websites. Includes a shortcode and custom block for fully-responsive conference schedules in table format.
 * Version:           1.1.0
 * Author:            The Events Calendar
 * Author URI:        https://theeventscalendar.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       event-schedule-manager
 * Domain Path:       /languages
 * Requires at least: 6.2
 * Requires PHP:      7.4
 */

define( 'CONFERENCE_SCHEDULE_PRO_FILE', __FILE__ );

// Load the required php min version functions.
require_once dirname( CONFERENCE_SCHEDULE_PRO_FILE ) . '/src/functions/php-min-version.php';

/**
 * Verifies if we need to warn the user about min PHP version and bail to avoid fatal errors.
 */
if ( tribe_is_not_min_php_version() ) {
	tribe_not_php_version_textdomain( 'event-schedule-manager', CONFERENCE_SCHEDULE_PRO_FILE );

	/**
	 * Include the plugin name into the correct place.
	 *
	 * @since  TBD
	 *
	 * @param  array $names current list of names.
	 *
	 * @return array List of names after adding Event Schedule Manager.
	 */
	function tec_conference_schedule_not_php_version_plugin_name( $names ) {
		$names['event-schedule-manager'] = esc_html__( 'Event Schedule Manager', 'event-schedule-manager' );
		return $names;
	}

	add_filter( 'tribe_not_php_version_names', 'tec_conference_schedule_not_php_version_plugin_name' );

	if ( ! has_filter( 'admin_notices', 'tribe_not_php_version_notice' ) ) {
		add_action( 'admin_notices', 'tribe_not_php_version_notice' );
	}

	return false;
}

// Include the file that defines the functions handling the plugin load operations.
require_once __DIR__ . '/src/functions/load.php';

add_action( 'plugins_loaded', 'conference_schedule_load', 0 );
