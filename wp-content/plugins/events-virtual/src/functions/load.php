<?php
/**
 * Provides functions to handle the loading operations of the plugin.
 *
 * The functions are defined in the global namespace to allow easier loading in the main plugin file.
 *
 * @since 1.0.0
 */

/**
 * Shows a message to indicate the plugin cannot be loaded due to missing requirements.
 *
 * @since 1.0.0
 * @since 1.14.0 Include message as a param.
 *
 * @param ?string $message The message to show. Defaults to null.
 */
function tribe_events_virtual_show_fail_message( string $message = null ) {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	tribe_events_virtual_load_text_domain();

	if ( null === $message ) {
		$url = 'plugin-install.php?tab=plugin-information&plugin=the-events-calendar&TB_iframe=true';

		$message = sprintf(
			'%1s <a href="%2s" class="thickbox" title="%3s">%3$s</a>.',
			esc_html__(
				'To begin using Virtual Events, please install the latest version of',
				'events-virtual'
			),
			esc_url( $url ),
			esc_html__( 'The Events Calendar', 'events-virtual' )
		);
	}

	// The message HTML is escaped in the line above.
	echo wp_kses_post( '<div class="error"><p>' . $message . '</p></div>' );
}

/**
 * Loads the plugin localization files.
 *
 * If the text domain loading functions provided by `common` (from The Events Calendar or Event Tickets) are not
 * available, then the function will use the `load_plugin_textdomain` function.
 *
 * @since 1.0.4
 */
function tribe_events_virtual_load_text_domain() {
	$domain          = 'events-virtual';
	$plugin_base_dir = dirname( plugin_basename( EVENTS_VIRTUAL_FILE ) );
	$plugin_rel_path = $plugin_base_dir . DIRECTORY_SEPARATOR . 'lang';

	if ( ! class_exists( 'Tribe__Main' ) ) {
		// If we don't have Common classes load the old-fashioned way.
		load_plugin_textdomain( $domain, false, $plugin_rel_path );
	} else {
		// This will load `wp-content/languages/plugins` files first.
		Tribe__Main::instance()->load_text_domain( $domain, $plugin_rel_path );
	}
}

/**
 * Determines if the plugin can be active based on the PUE and Plugin Register dependencies.
 * When the plugin cannot be active, it will show a message and soft-deactivate itself.
 *
 * Soft-deactivation is done by keeping the plugin active but preventing it from loading.
 *
 * @since 1.0.0
 *
 * @return bool Whether the plugin dependency manifest is satisfied or not.
 */
function tribe_events_virtual_preload() {
	/**
	 * This function is attached to two hooks:
	 * - `tribe_common_loaded` with a priority of 15
	 * - `plugins_loaded` with a priority of 25
	 *
	 * So this check here will ensure the plugin doesn't load twice.
	 *
	 */
	if ( did_action( 'tribe_common_loaded' ) && ! doing_action( 'tribe_common_loaded' ) ) {
		return false;
	}

	// We need these two to be true to even test for the rest.
	if ( ! (
		function_exists( 'tribe_register_provider' )
		&& class_exists( 'Tribe__Abstract_Plugin_Register', false )
	) ) {
		// Loaded in single site or not network-activated in a multisite installation.
		add_action( 'admin_notices', 'tribe_events_virtual_show_fail_message', 10, 0 );

		// Network-activated in a multisite installation.
		add_action( 'network_admin_notices', 'tribe_events_virtual_show_fail_message', 10, 0 );

		// Prevent loading of the plugin if common is loaded (better safe than sorry).
		remove_action( 'tribe_common_loaded', 'tribe_events_virtual_load', 50 );

		return false;
	}

	// Load the Plugin register which contains the dependency manifest.
	if ( ! class_exists( '\Tribe\Events\Virtual\Plugin_Register', false ) ) {
		require_once dirname( EVENTS_VIRTUAL_FILE ) . '/src/Tribe/Plugin_Register.php';
	}

	$plugin_register = new \Tribe\Events\Virtual\Plugin_Register();
	$plugin_register->set_base_dir( EVENTS_VIRTUAL_FILE );
	$plugin_register->register_plugin();

	if ( ! tribe_check_plugin( $plugin_register->get_plugin_class() ) ) {
		// Prevent loading of the plugin if common is loaded (better safe than sorry).
		remove_action( 'tribe_common_loaded', 'tribe_events_virtual_load', 50 );
		return false;
	}

	// After this point, it's safe to assume Common has been loaded.

	tribe_singleton( \Tribe\Events\Virtual\Plugin_Register::class, $plugin_register );

	return true;
}

/**
 * Register and load the service provider for loading the plugin.
 *
 * @since 1.0.0
 */
function tribe_events_virtual_load() {
	$plugin_register = tribe( \Tribe\Events\Virtual\Plugin_Register::class );

	// Determine if the main class exists, it really shouldn't, but we double-check.
	if ( class_exists( $plugin_register->get_plugin_class(), false ) ) {
		$notice_about_plugin_already_exists = static function() {
			$message = esc_html__(
				'The Virtual Events plugin is already loaded. Please check your site for conflicting plugins.',
				'events-virtual'
			);

			tribe_events_virtual_show_fail_message( $message );
		};

		// Loaded in single site or not network-activated in a multisite installation.
		add_action( 'admin_notices', $notice_about_plugin_already_exists );

		// Network-activated in a multisite installation.
		add_action( 'network_admin_notices', $notice_about_plugin_already_exists );
	}

	// Last file that needs to be loaded manually.
	require_once dirname( $plugin_register->get_base_dir() ) . '/src/Tribe/Plugin.php';

	// Load the plugin, autoloading happens here.
	\Tribe\Events\Virtual\Plugin::boot();
}

/**
 * Handles the removal of PUE-related options when the plugin is uninstalled.
 *
 * @since 1.0.0
 */
function tribe_events_virtual_uninstall() {
	$slug = 'events_virtual';

	delete_option( 'pue_install_key_' . $slug );
	delete_option( 'pu_dismissed_upgrade_' . $slug );
}
