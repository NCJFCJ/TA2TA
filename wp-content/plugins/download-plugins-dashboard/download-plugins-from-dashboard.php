<?php
/*
Plugin Name: Download Plugins and Themes from Dashboard
Plugin URI: https://wpfactory.com/item/download-plugins-and-themes-from-dashboard/
Description: Download installed plugins and themes ZIP files directly from your admin dashboard without using FTP.
Version: 1.8.7
Author: WPFactory
Copyright: © 2024 WPFactory
Author URI: https://wpfactory.com
Text Domain: download-plugins-dashboard
Domain Path: /langs
*/

defined( 'ABSPATH' ) || exit;

if ( 'download-plugins-from-dashboard.php' === basename( __FILE__ ) ) {
	/**
	 * Check if Pro plugin version is activated.
	 *
	 * @version 1.8.0
	 * @since   1.8.0
	 *
	 * @see     https://developer.wordpress.org/reference/functions/is_plugin_active/
	 * @see     https://developer.wordpress.org/reference/functions/is_plugin_active_for_network/
	 */
	$plugin = 'download-plugins-from-dashboard-pro/download-plugins-from-dashboard-pro.php';
	if (
		in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) ||
		( is_multisite() && array_key_exists( $plugin, (array) get_site_option( 'active_sitewide_plugins', array() ) ) )
	) {
		return;
	}
}

// Plugin version constant
if ( ! defined( 'ALG_DOWNLOAD_PLUGINS_VERSION' ) ) {
	define( 'ALG_DOWNLOAD_PLUGINS_VERSION', '1.8.7' );
}

// Plugin file constant
if ( ! defined( 'ALG_DOWNLOAD_PLUGINS_FILE' ) ) {
	define( 'ALG_DOWNLOAD_PLUGINS_FILE', __FILE__ );
}

// Load main plugin class
require_once( 'includes/class-alg-download-plugins.php' );

if ( ! function_exists( 'alg_download_plugins' ) ) {
	/**
	 * Returns the main instance of Alg_Download_Plugins to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @return  Alg_Download_Plugins
	 *
	 * @todo    [next] (dev) run on `plugins_loaded`?
	 */
	function alg_download_plugins() {
		return Alg_Download_Plugins::instance();
	}
}

alg_download_plugins();
