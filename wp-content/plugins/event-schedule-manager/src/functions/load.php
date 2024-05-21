<?php
/**
 * Provides functions to handle the loading operations of the plugin.
 *
 * The functions are defined in the global namespace to allow easier loading in the main plugin file.
 *
 * @since 1.0.0
 */

use TEC\Conference\Plugin;

/**
 * Shows a message to indicate the plugin cannot be loaded due to missing requirements.
 *
 * @since 1.0.0
 *
 * @param string $message The message to show.
 */
function conference_schedule_show_fail_message( $message ) {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	conference_schedule_load_text_domain();

	echo wp_kses_post( '<div class="error"><p>' . $message . '</p></div>' );
}

/**
 * Loads the plugin localization files.
 *
 * If the text domain loading functions provided by `common` (from The Events Calendar or Event Tickets) are not
 * available, then the function will use the `load_plugin_textdomain` function.
 *
 * @since 1.0.0
 */
function conference_schedule_load_text_domain() {
	$domain          = 'event-schedule-manager';
	$plugin_base_dir = dirname( plugin_basename( CONFERENCE_SCHEDULE_PRO_FILE ) );
	$plugin_rel_path = $plugin_base_dir . DIRECTORY_SEPARATOR . 'lang';

	// Load textdomain.
	load_plugin_textdomain( $domain, false, $plugin_rel_path );
}

/**
 * Register and load the service provider for loading the plugin.
 *
 * @since 1.0.0
 */
function conference_schedule_load() {
	// Last file that needs to be loaded manually.
	require_once dirname( CONFERENCE_SCHEDULE_PRO_FILE ) . '/src/Conference/Plugin.php';

	// Load the plugin, autoloading happens here.
	$plugin = new Plugin();
	$plugin->boot();
}

/**
 * Handles the removal of PUE-related options when the plugin is uninstalled.
 *
 * @since 1.0.0
 */
function conference_schedule_uninstall() {
	$slug = Plugin::SLUG;

	delete_option( 'pue_install_key_' . $slug );
	delete_option( 'pu_dismissed_upgrade_' . $slug );
}
