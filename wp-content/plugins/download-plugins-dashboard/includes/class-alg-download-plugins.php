<?php
/**
 * Download Plugins and Themes from Dashboard - Main Plugin Class
 *
 * @version 1.8.4
 * @since   1.0.0
 *
 * @author  WPFactory
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_Download_Plugins' ) ) :

final class Alg_Download_Plugins {

	/**
	 * Plugin version.
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	public $version = ALG_DOWNLOAD_PLUGINS_VERSION;

	/**
	 * @var   Alg_Download_Plugins The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Settings.
	 *
	 * @since 1.8.4
	 *
	 * @var Alg_Download_Plugins_Settings
	 */
	public $settings;

	/**
	 * Core.
	 *
	 * @since 1.8.4
	 *
	 * @var Alg_Download_Plugins_Core
	 */
	public $core;

	/**
	 * Main Alg_Download_Plugins Instance.
	 *
	 * Ensures only one instance of Alg_Download_Plugins is loaded or can be loaded.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @static
	 * @return  Alg_Download_Plugins - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Alg_Download_Plugins Constructor.
	 *
	 * @version 1.8.0
	 * @since   1.0.0
	 *
	 * @access  public
	 *
	 * @todo    [next] (dev) load everything on `is_admin()` only?
	 */
	function __construct() {

		// Translation file
		add_action( 'init', array( $this, 'localize' ) );

		// Pro
		if ( 'download-plugins-from-dashboard-pro.php' === basename( ALG_DOWNLOAD_PLUGINS_FILE ) ) {
			require_once( 'pro/class-alg-download-plugins-pro.php' );
		}

		// Includes
		$this->settings = require_once( 'settings/class-alg-download-plugins-settings.php' );
		$this->core     = require_once( 'class-alg-download-plugins-core.php' );

		// Action links
		if ( is_admin() ) {
			add_filter( 'plugin_action_links_' . plugin_basename( ALG_DOWNLOAD_PLUGINS_FILE ), array( $this, 'action_links' ) );
		}

	}

	/**
	 * localize.
	 *
	 * @version 1.8.0
	 * @since   1.7.1
	 */
	function localize() {
		load_plugin_textdomain( 'download-plugins-dashboard', false, dirname( plugin_basename( ALG_DOWNLOAD_PLUGINS_FILE ) ) . '/langs/' );
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @version 1.8.0
	 * @since   1.3.0
	 *
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
		$custom_links   = array();
		$custom_links[] = '<a href="' . admin_url( 'options-general.php?page=download-plugins-dashboard' ) . '">' . __( 'Settings', 'download-plugins-dashboard' ) . '</a>';
		if ( 'download-plugins-from-dashboard.php' === basename( ALG_DOWNLOAD_PLUGINS_FILE ) ) {
			$custom_links[] = '<a target="_blank" style="font-weight: bold; color: green;" href="https://wpfactory.com/item/download-plugins-and-themes-from-dashboard/">' .
				__( 'Go Pro', 'download-plugins-dashboard' ) . '</a>';
		}
		return array_merge( $custom_links, $links );
	}

	/**
	 * Get the plugin url.
	 *
	 * @version 1.8.0
	 * @since   1.0.0
	 *
	 * @return  string
	 */
	function plugin_url() {
		return untrailingslashit( plugin_dir_url( ALG_DOWNLOAD_PLUGINS_FILE ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @version 1.8.0
	 * @since   1.0.0
	 *
	 * @return  string
	 */
	function plugin_path() {
		return untrailingslashit( plugin_dir_path( ALG_DOWNLOAD_PLUGINS_FILE ) );
	}

}

endif;
