<?php
/**
 * Download Plugins and Themes from Dashboard - Core Class
 *
 * @version 1.8.0
 * @since   1.2.0
 *
 * @author  WPFactory
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_Download_Plugins_Core' ) ) :

class Alg_Download_Plugins_Core {

	/**
	 * Constructor.
	 *
	 * @version 1.4.0
	 * @since   1.2.0
	 *
	 * @todo    [later] (dev) add nonces
	 * @todo    [later] (feature) add "Download plugin" to "Bulk Actions" select box on "Plugins" page
	 * @todo    [later] (feature) add "Download active / inactive / recently active plugins" (to browser and periodically)
	 * @todo    [later] (feature) add "Download active theme only" (periodically)
	 */
	function __construct() {
		// Links
		add_filter( 'plugin_action_links',                       array( $this, 'add_plugin_download_action_links' ), PHP_INT_MAX, 4 );
		add_action( 'admin_enqueue_scripts',                     array( $this, 'add_theme_download_links' ) );
		// Core
		add_action( 'admin_init',                                array( $this, 'download_plugin' ) );
		add_action( 'admin_init',                                array( $this, 'download_theme' ) );
		add_action( 'admin_init',                                array( $this, 'download_plugin_bulk' ) );
		add_action( 'admin_init',                                array( $this, 'download_theme_bulk' ) );
		// Tools
		add_action( 'admin_init',                                array( $this, 'download_plugin_all' ) );
		add_action( 'admin_init',                                array( $this, 'download_theme_all' ) );
		// Crons
		add_filter( 'cron_schedules',                            array( $this, 'cron_add_custom_intervals' ) );
		add_action( 'alg_download_plugins_cron',                 array( $this, 'cron_alg_download_plugins' ) );
		add_action( 'alg_download_themes_cron',                  array( $this, 'cron_alg_download_themes' ) );
		register_activation_hook(   ALG_DOWNLOAD_PLUGINS_FILE,   array( $this, 'cron_schedule_plugins_event' ) );
		register_deactivation_hook( ALG_DOWNLOAD_PLUGINS_FILE,   array( $this, 'cron_unschedule_plugins_event' ) );
		register_activation_hook(   ALG_DOWNLOAD_PLUGINS_FILE,   array( $this, 'cron_schedule_themes_event' ) );
		register_deactivation_hook( ALG_DOWNLOAD_PLUGINS_FILE,   array( $this, 'cron_unschedule_themes_event' ) );
	}

	/**
	 * add_theme_download_links.
	 *
	 * @version 1.7.1
	 * @since   1.1.0
	 *
	 * @todo    [later] (dev) add download links to each theme's "Theme Details"
	 */
	function add_theme_download_links() {
		wp_enqueue_script(  'alg-theme-download-links',
			alg_download_plugins()->plugin_url() . '/includes/js/theme_download_link' . ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ? '' : '.min' ) . '.js',
			array( 'jquery' ),
			alg_download_plugins()->version,
			true
		);
		wp_localize_script( 'alg-theme-download-links', 'alg_object', array(
			'download_link_text' => __( 'Download ZIP', 'download-plugins-dashboard' ),
		) );
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @version 1.5.0
	 * @since   1.0.0
	 */
	function add_plugin_download_action_links( $actions, $plugin_file, $plugin_data, $context ) {
		$plugin_file = explode( '/', $plugin_file );
		if ( isset( $plugin_file[0] ) ) {
			$extra_params = ( isset( $_GET['plugin_status'] ) && in_array( $_GET['plugin_status'], array( 'mustuse', 'dropins' ) ) ?
				'&alg_download_plugin_status=' . $_GET['plugin_status'] : '' );
			$actions = array_merge( $actions, array(
				'<a href="' . admin_url( 'plugins.php?alg_download_plugin=' . $plugin_file[0] . $extra_params ) . '">' .
					__( 'Download ZIP', 'download-plugins-dashboard' ) . '</a>' )
			);
		}
		return $actions;
	}

	/**
	 * get_sys_temp_dir.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 *
	 * @todo    [maybe] (dev) check `open_basedir` for `is_writable()`
	 */
	function get_sys_temp_dir() {
		$dir = sys_get_temp_dir();
		if ( ! empty( $dir ) && is_writable( $dir ) ) {
			return $dir;
		} else {
			$dir = ini_get( 'upload_tmp_dir' );
			if ( ! empty( $dir ) && is_writable( $dir ) ) {
				return $dir;
			} else {
				$dir = wp_upload_dir();
				if ( ! empty( $dir['path'] ) && is_writable( $dir['path'] ) ) {
					return $dir['path'];
				} elseif ( ! empty( $dir['basedir'] ) && is_writable( $dir['basedir'] ) ) {
					return $dir['basedir'];
				} else {
					$dir = ini_get( 'open_basedir' );
					return trailingslashit( $dir );
				}
			}
		}
	}

	/**
	 * get_temp_dir.
	 *
	 * @version 1.7.0
	 * @since   1.4.3
	 */
	function get_temp_dir() {
		return ( '' !== ( $temp_dir = get_option( 'alg_download_plugins_dashboard_temp_dir', '' ) ) ? $temp_dir : $this->get_sys_temp_dir() );
	}

	/**
	 * download_theme_all.
	 *
	 * @version 1.4.3
	 * @since   1.4.0
	 */
	function download_theme_all() {
		if ( isset( $_GET['alg_download_theme_all'] ) && is_user_logged_in() && current_user_can( 'switch_themes' ) ) {
			if ( ! $this->check_system_requirements() ) {
				return false;
			}
			$zip_file_name        = 'themes' . '.zip';
			$zip_file_path        = $this->get_temp_dir() . '/' . $zip_file_name;
			$plugin_or_theme_path = get_theme_root();
			$exclude_path         = $plugin_or_theme_path;
			$args                 = array( 'zip_file_path' => $zip_file_path, 'exclude_path' => $exclude_path );
			$files                = $this->get_files( $plugin_or_theme_path );
			if ( $this->create_zip( $args, $files ) ) {
				$this->send_file( $zip_file_name, $zip_file_path );
			} else {
				add_action( 'admin_notices', array( $this, 'create_zip_error_message' ) );
				return false;
			}
		}
	}

	/**
	 * download_plugin_all.
	 *
	 * @version 1.5.0
	 * @since   1.4.0
	 *
	 * @todo    [later] (dev) `mustuse` and `dropins`
	 * @todo    [later] (dev) `$is_cron` is not used?
	 */
	function download_plugin_all( $is_cron = false ) {
		if ( isset( $_GET['alg_download_plugin_all'] ) && is_user_logged_in() && current_user_can( 'activate_plugins' )  ) {
			if ( ! $this->check_system_requirements() ) {
				return false;
			}
			$zip_file_name        = 'plugins' . '.zip';
			$zip_file_path        = $this->get_temp_dir() . '/' . $zip_file_name;
			$plugin_or_theme_path = $this->get_plugin_dir( 'regular' );
			$exclude_path         = $plugin_or_theme_path;
			$args                 = array( 'zip_file_path' => $zip_file_path, 'exclude_path' => $exclude_path );
			$files                = $this->get_files( $plugin_or_theme_path );
			if ( $this->create_zip( $args, $files ) ) {
				$this->send_file( $zip_file_name, $zip_file_path );
			} else {
				add_action( 'admin_notices', array( $this, 'create_zip_error_message' ) );
				return false;
			}
		}
	}

	/**
	 * cron_unschedule_themes_event.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function cron_unschedule_themes_event() {
		wp_clear_scheduled_hook( 'alg_download_themes_cron' );
	}

	/**
	 * cron_schedule_themes_event.
	 *
	 * @version 1.7.0
	 * @since   1.4.0
	 */
	function cron_schedule_themes_event() {
		if ( '' != ( $interval = apply_filters( 'alg_download_plugins_themes_bulk_period', '' ) ) && ! wp_next_scheduled( 'alg_download_themes_cron' ) ) {
			wp_schedule_event( time(), $interval, 'alg_download_themes_cron' );
		}
	}

	/**
	 * cron_unschedule_plugins_event.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function cron_unschedule_plugins_event() {
		wp_clear_scheduled_hook( 'alg_download_plugins_cron' );
	}

	/**
	 * cron_schedule_plugins_event.
	 *
	 * @version 1.7.0
	 * @since   1.4.0
	 */
	function cron_schedule_plugins_event() {
		if ( '' != ( $interval = apply_filters( 'alg_download_plugins_plugins_bulk_period', '' ) ) && ! wp_next_scheduled( 'alg_download_plugins_cron' ) ) {
			wp_schedule_event( time(), $interval, 'alg_download_plugins_cron' );
		}
	}

	/**
	 * cron_alg_download_themes.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function cron_alg_download_themes() {
		$this->download_theme_bulk( true );
	}

	/**
	 * cron_alg_download_plugins.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function cron_alg_download_plugins() {
		$this->download_plugin_bulk( true );
	}

	/**
	 * cron_add_custom_intervals.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function cron_add_custom_intervals( $schedules ) {
		$schedules['four_weeks'] = array(
			'interval' => 4 * 604800,
			'display'  => __( 'Every 4 Weeks', 'download-plugins-dashboard' ),
		);
		$schedules['weekly'] = array(
			'interval' => 604800,
			'display'  => __( 'Once Weekly', 'download-plugins-dashboard' ),
		);
		$schedules['minutely'] = array(
			'interval' => 60,
			'display'  => __( 'Once a Minute', 'download-plugins-dashboard' ),
		);
		return $schedules;
	}

	/**
	 * get_uploads_dir.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function get_uploads_dir( $subdir ) {
		$upload_dir = wp_upload_dir();
		$upload_dir = $upload_dir['basedir'] . '/' . $subdir;
		return str_replace( '\\', '/', $upload_dir );
	}

	/**
	 * download_plugin_or_theme_bulk.
	 *
	 * @version 1.4.1
	 * @since   1.4.0
	 *
	 * @todo    [later] (dev) rethink `if ( ! $this->create_zip() )`
	 * @todo    [later] (dev) maybe use temp dir for `'all' === $output_files`
	 * @todo    [later] (dev) maybe just download `$plugin_or_theme_dir` for `in_array( $output_files, array( 'all', 'both' ) )` (instead of using `$final_zip_files`)
	 */
	function download_plugin_or_theme_bulk( $plugin_or_theme_dir, $plugins_or_themes, $add_main_dir, $do_add_download_time, $output_files, $all_file_name, $destination_path ) {
		if ( ! $this->check_system_requirements() ) {
			return false;
		}
		if ( ! file_exists( $destination_path ) ) {
			mkdir( $destination_path, 0755, true );
		}
		$final_zip_files = array();
		foreach ( $plugins_or_themes as $plugin_or_theme_name => $version ) {
			$zip_file_name        = $plugin_or_theme_name . ( '' != $version ? '.' : '' ) . $version . ( $do_add_download_time ? '-' . date( 'Y-m-d-H-i-s' ) : '' ) . '.zip';
			$zip_file_path        = $destination_path    . '/' . $zip_file_name;
			$plugin_or_theme_path = $plugin_or_theme_dir . '/' . $plugin_or_theme_name;
			$exclude_path         = ( $add_main_dir ? $plugin_or_theme_dir : $plugin_or_theme_path );
			$args                 = array( 'zip_file_path' => $zip_file_path, 'exclude_path' => $exclude_path );
			$files                = $this->get_files( $plugin_or_theme_path );
			$final_zip_files[]    = $zip_file_path;
			if ( ! $this->create_zip( $args, $files ) ) {
				add_action( 'admin_notices', array( $this, 'create_zip_error_message' ) );
			}
		}
		if ( in_array( $output_files, array( 'all', 'both' ) ) && ! empty( $final_zip_files ) ) {
			$zip_file_path        = $destination_path    . '/' . $all_file_name . ( $do_add_download_time ? '-' . date( 'Y-m-d-H-i-s' ) : '' ) . '.zip';
			$exclude_path         = $destination_path;
			$args                 = array( 'zip_file_path' => $zip_file_path, 'exclude_path' => $exclude_path );
			if ( ! $this->create_zip( $args, $final_zip_files ) ) {
				add_action( 'admin_notices', array( $this, 'create_zip_error_message' ) );
			}
		}
		if ( 'all' === $output_files ) {
			foreach ( $final_zip_files as $file ) {
				if ( file_exists( $file ) ) {
					unlink( $file );
				}
			}
		}
	}

	/**
	 * download_theme_bulk.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function download_theme_bulk( $is_cron = false ) {
		if ( $is_cron || ( isset( $_GET['alg_download_theme_bulk'] ) && is_user_logged_in() && current_user_can( 'switch_themes' ) ) ) {
			$themes            = array();
			$do_append_version = ( 'yes' === get_option( 'alg_download_plugins_dashboard_themes_append_version', 'no' ) );
			foreach ( wp_get_themes() as $theme => $theme_object ) {
				$themes[ $theme ] = ( $do_append_version ? $theme_object->get( 'Version' ) : '' );
			}
			if ( ! empty( $themes ) ) {
				$this->download_plugin_or_theme_bulk(
					get_theme_root(),
					$themes,
					( 'yes' === get_option( 'alg_download_plugins_dashboard_themes_add_main_dir', 'yes' ) ),
					( 'yes' === get_option( 'alg_download_plugins_dashboard_themes_append_date_time', 'no' ) ),
					get_option( 'alg_download_plugins_dashboard_themes_output_files', 'each' ),
					get_option( 'alg_download_plugins_dashboard_themes_single_zip_file_name', 'themes' ),
					get_option( 'alg_download_plugins_dashboard_themes_bulk_dir', $this->get_uploads_dir( 'themes-archive' ) )
				);
			}
			if ( ! $is_cron ) {
				wp_safe_redirect( add_query_arg( 'alg_download_theme_bulk_finished', true, remove_query_arg( 'alg_download_theme_bulk' ) ) );
				exit;
			}
		}
	}

	/**
	 * download_plugin_bulk.
	 *
	 * @version 1.5.0
	 * @since   1.4.0
	 *
	 * @todo    [later] (dev) `mustuse` and `dropins`
	 * @todo    [later] (dev) single file plugins
	 */
	function download_plugin_bulk( $is_cron = false ) {
		if ( $is_cron || ( isset( $_GET['alg_download_plugin_bulk'] ) && is_user_logged_in() && current_user_can( 'activate_plugins' ) ) ) {
			$plugins           = array();
			$do_append_version = ( 'yes' === get_option( 'alg_download_plugins_dashboard_plugins_append_version', 'no' ) );
			foreach ( $this->get_plugins( 'regular' ) as $plugin_file => $plugin_data ) {
				$plugin = explode( '/', $plugin_file );
				if ( isset( $plugin[1] ) ) {
					$plugin = $plugin[0];
					$plugins[ $plugin ] = ( $do_append_version ? $plugin_data['Version'] : '' );
				}
			}
			if ( ! empty( $plugins ) ) {
				$this->download_plugin_or_theme_bulk(
					$this->get_plugin_dir( 'regular' ),
					$plugins,
					( 'yes' === get_option( 'alg_download_plugins_dashboard_plugins_add_main_dir', 'yes' ) ),
					( 'yes' === get_option( 'alg_download_plugins_dashboard_plugins_append_date_time', 'no' ) ),
					get_option( 'alg_download_plugins_dashboard_plugins_output_files', 'each' ),
					get_option( 'alg_download_plugins_dashboard_plugins_single_zip_file_name', 'plugins' ),
					get_option( 'alg_download_plugins_dashboard_plugins_bulk_dir', $this->get_uploads_dir( 'plugins-archive' ) )
				);
			}
			if ( ! $is_cron ) {
				wp_safe_redirect( add_query_arg( 'alg_download_plugin_bulk_finished', true, remove_query_arg( 'alg_download_plugin_bulk' ) ) );
				exit;
			}
		}
	}

	/**
	 * download_theme.
	 *
	 * @version 1.4.0
	 * @since   1.1.0
	 *
	 * @todo    [later] (dev) extra validation (i.e. check for `$theme_name` in `wp_get_themes()`)
	 */
	function download_theme() {
		if ( isset( $_GET['alg_download_theme'] ) && is_user_logged_in() && current_user_can( 'switch_themes' ) ) {
			if ( '' != ( $theme_name = sanitize_text_field( $_GET['alg_download_theme'] ) ) ) {
				// Validated successfully
				$theme_root = get_theme_root();
				if ( 'yes' === get_option( 'alg_download_plugins_dashboard_themes_append_version', 'no' ) ) {
					$_theme  = wp_get_theme( $theme_name, $theme_root );
					$version = ( is_object( $_theme ) ? $_theme->get( 'Version' ) : '' );
				} else {
					$version = '';
				}
				$add_main_dir = ( 'yes' === get_option( 'alg_download_plugins_dashboard_themes_add_main_dir', 'yes' ) );
				$this->download_plugin_or_theme( $theme_root, $theme_name, $version, $add_main_dir );
			}
		}
	}

	/**
	 * get_plugins.
	 *
	 * @version 1.5.0
	 * @since   1.5.0
	 *
	 * @todo    [later] (dev) recheck if we really need `require_once( ABSPATH . 'wp-admin/includes/plugin.php' )`
	 */
	function get_plugins( $status = false ) {
		if ( ! $status ) {
			$status = ( isset( $_GET['alg_download_plugin_status'] ) ? $_GET['alg_download_plugin_status'] : 'regular' );
		}
		if ( ! function_exists( 'get_plugins' ) || ! function_exists( 'get_dropins' ) || ! function_exists( 'get_mu_plugins' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		switch ( $status ) {
			case 'mustuse':
				return get_mu_plugins();
			case 'dropins':
				return get_dropins();
			default: // 'regular'
				return get_plugins();
		}
	}

	/**
	 * get_plugin_dir.
	 *
	 * @version 1.5.0
	 * @since   1.5.0
	 */
	function get_plugin_dir( $status = false ) {
		if ( ! $status ) {
			$status = ( isset( $_GET['alg_download_plugin_status'] ) ? $_GET['alg_download_plugin_status'] : 'regular' );
		}
		switch ( $status ) {
			case 'mustuse':
				return WPMU_PLUGIN_DIR;
			case 'dropins':
				return WP_CONTENT_DIR;
			default: // 'regular'
				return WP_PLUGIN_DIR;
		}
	}

	/**
	 * download_plugin.
	 *
	 * @version 1.5.0
	 * @since   1.0.0
	 */
	function download_plugin() {
		if ( isset( $_GET['alg_download_plugin'] ) && is_user_logged_in() && current_user_can( 'activate_plugins' ) ) {
			if ( '' != ( $plugin_name = sanitize_text_field( $_GET['alg_download_plugin'] ) ) ) {
				$all_plugins = $this->get_plugins();
				foreach ( $all_plugins as $plugin_file => $plugin_data ) {
					$plugin_file = explode( '/', $plugin_file );
					if ( isset( $plugin_file[0] ) && $plugin_name === $plugin_file[0] ) {
						// Validated successfully
						$version      = ( 'yes' === get_option( 'alg_download_plugins_dashboard_plugins_append_version', 'no' ) ) ? $plugin_data['Version'] : '';
						$add_main_dir = ( 'yes' === get_option( 'alg_download_plugins_dashboard_plugins_add_main_dir', 'yes' ) );
						$this->download_plugin_or_theme( $this->get_plugin_dir(), $plugin_name, $version, $add_main_dir, ( isset( $plugin_file[1] ) ) );
						break;
					}
				}
			}
		}
	}

	/**
	 * check_system_requirements.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function check_system_requirements() {
		if ( ! isset( $this->system_requirements_check ) ) {
			$this->system_requirements_check = ( class_exists( 'RecursiveIteratorIterator' ) && function_exists( 'gzopen' ) );
		}
		if ( ! $this->system_requirements_check ) {
			add_action( 'admin_notices', array( $this, 'system_requirements_error_message' ) );
		}
		return $this->system_requirements_check;
	}

	/**
	 * system_requirements_error_message.
	 *
	 * @version 1.4.0
	 * @since   1.1.0
	 */
	function system_requirements_error_message() {
		$message     = __( 'To use %s plugin, %s must be available on your server.', 'download-plugins-dashboard' );
		$plugin_name = '<strong>' . __( 'Download Plugins and Themes from Dashboard', 'download-plugins-dashboard' ) . '</strong>';
		if ( ! class_exists( 'RecursiveIteratorIterator' ) ) {
			$required = '<code>RecursiveIteratorIterator</code>';
			echo '<div class="notice notice-error"><p>' . sprintf( $message, $plugin_name, $required ) . '</p></div>';
		}
		if ( ! function_exists( 'gzopen' ) ) {
			$required = '<code>zlib</code>';
			echo '<div class="notice notice-error"><p>' . sprintf( $message, $plugin_name, $required ) . '</p></div>';
		}
	}

	/**
	 * create_zip_error_message.
	 *
	 * @version 1.4.1
	 * @since   1.4.0
	 */
	function create_zip_error_message() {
		echo '<div class="notice notice-error"><p>' .
			( ! empty( $this->last_error ) ?
				sprintf(  __( 'Error: %s', 'download-plugins-dashboard' ), $this->last_error ) :
				__( 'Something went wrong...', 'download-plugins-dashboard' )
			) .
		'</p></div>';
	}

	/**
	 * download_plugin_or_theme.
	 *
	 * @version 1.5.0
	 * @since   1.1.0
	 *
	 * @todo    [later] (dev) recheck if themes can be single file (i.e. `$is_dir = false`)
	 */
	function download_plugin_or_theme( $plugin_or_theme_dir, $plugin_or_theme_name, $version, $add_main_dir, $is_dir = true ) {
		if ( ! $this->check_system_requirements() ) {
			return false;
		}
		$zip_file_name        = $plugin_or_theme_name . ( '' != $version ? '.' : '' ) . $version . '.zip';
		$zip_file_path        = $this->get_temp_dir() . '/' . $zip_file_name;
		$plugin_or_theme_path = $plugin_or_theme_dir . '/' . $plugin_or_theme_name;
		$exclude_path         = ( ! $is_dir || $add_main_dir ? $plugin_or_theme_dir : $plugin_or_theme_path );
		$args                 = array( 'zip_file_path' => $zip_file_path, 'exclude_path' => $exclude_path );
		$files                = ( $is_dir ? $this->get_files( $plugin_or_theme_path ) : array( $plugin_or_theme_path ) );
		if ( $this->create_zip( $args, $files ) ) {
			$this->send_file( $zip_file_name, $zip_file_path );
		} else {
			add_action( 'admin_notices', array( $this, 'create_zip_error_message' ) );
			return false;
		}
	}

	/**
	 * get_files.
	 *
	 * @version 1.3.0
	 * @since   1.3.0
	 */
	function get_files( $plugin_or_theme_path ) {
		$files       = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $plugin_or_theme_path ), RecursiveIteratorIterator::LEAVES_ONLY );
		$files_paths = array();
		foreach ( $files as $name => $file ) {
			if ( ! $file->isDir() ) {
				$file_path = str_replace( '\\', '/', $file->getRealPath() );
				$files_paths[] = $file_path;
			}
		}
		return $files_paths;
	}

	/**
	 * create_zip.
	 *
	 * @version 1.4.1
	 * @since   1.3.0
	 */
	function create_zip( $args, $files ) {
		if ( file_exists( $args['zip_file_path'] ) ) {
			unlink( $args['zip_file_path'] );
		}
		$zip_library = get_option( 'alg_download_plugins_dashboard_zip_library', ( class_exists( 'ZipArchive' ) ? 'ziparchive' : 'pclzip' ) );
		switch ( $zip_library ) {
			case 'pclzip':
				return $this->create_zip_pclzip( $args, $files );
			default: // 'ziparchive':
				return $this->create_zip_ziparchive( $args, $files );
		}
	}

	/**
	 * create_zip_ziparchive.
	 *
	 * @version 1.4.1
	 * @since   1.3.0
	 *
	 * @todo    [maybe] (dev) check `new ZipArchive`, `$zip->addFile`, `$zip->close` for errors
	 */
	function create_zip_ziparchive( $args, $files ) {
		$zip = new ZipArchive();
		if ( true !== ( $result = $zip->open( $args['zip_file_path'], ZipArchive::CREATE | ZipArchive::OVERWRITE ) ) ) {
			$this->last_error = sprintf( __( '%s can not open a new zip archive (error code %s).', 'download-plugins-dashboard' ),
				'<code>ZipArchive</code>', '<code>' . $result . '</code>' );
			return false;
		}
		$exclude_from_relative_path = strlen( $args['exclude_path'] ) + 1;
		foreach ( $files as $file_path ) {
			$zip->addFile( $file_path, substr( $file_path, $exclude_from_relative_path ) );
		}
		$zip->close();
		return true;
	}

	/**
	 * create_zip_pclzip.
	 *
	 * @version 1.4.1
	 * @since   1.3.0
	 *
	 * @see     http://www.phpconcept.net/pclzip
	 *
	 * @todo    [maybe] (dev) check `new PclZip` for errors
	 */
	function create_zip_pclzip( $args, $files ) {
		require_once( ABSPATH . 'wp-admin/includes/class-pclzip.php' );
		$zip = new PclZip( $args['zip_file_path'] );
		if ( 0 == $zip->create( $files, PCLZIP_OPT_REMOVE_PATH, $args['exclude_path'] ) ) {
			$this->last_error = sprintf( '%s %s.', '<code>PclZip</code>', $zip->errorInfo( true ) );
			return false;
		}
		return true;
	}

	/**
	 * send_file.
	 *
	 * @version 1.8.0
	 * @since   1.3.0
	 *
	 * @see     https://stackoverflow.com/questions/11315951/using-the-browser-prompt-to-download-a-file
	 */
	function send_file( $zip_file_name, $zip_file_path ) {
		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename=' . urlencode( $zip_file_name ) );
		header( 'Content-Description: File Transfer' );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . filesize( $zip_file_path ) );
		flush();
		if ( false !== ( $fp = fopen( $zip_file_path, 'r' ) ) ) {
			while ( ! feof( $fp ) ) {
				echo fread( $fp, 65536 );
				flush();
			}
			fclose( $fp );
			unlink( $zip_file_path );
			die();
		} else {
			die( __( 'Unexpected error', 'download-plugins-dashboard' ) );
		}
	}

}

endif;

return new Alg_Download_Plugins_Core();
