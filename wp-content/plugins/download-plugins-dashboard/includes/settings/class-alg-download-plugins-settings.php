<?php
/**
 * Download Plugins and Themes from Dashboard - Settings Class
 *
 * @version 1.8.4
 * @since   1.2.0
 *
 * @author  WPFactory
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_Download_Plugins_Settings' ) ) :

class Alg_Download_Plugins_Settings {

	/**
	 * Id.
	 *
	 * @since 1.8.4
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Constructor.
	 *
	 * @version 1.4.0
	 * @since   1.2.0
	 */
	function __construct() {
		$this->id = 'alg_download_plugins_dashboard';
		add_action( 'admin_menu',      array( $this, 'add_plugin_menu' ) );
		add_action( 'admin_init',      array( $this, 'save_settings' ) );
		add_action( 'admin_notices',   array( $this, 'admin_notices' ) );
	}

	/**
	 * admin_notices.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 *
	 * @todo    [later] (dev) remove `$_GET` (i.e. hook to `admin_notices` directly)?
	 */
	function admin_notices() {
		if ( isset( $_GET['alg_download_plugin_bulk_finished'] ) ) {
			echo '<div class="notice notice-success is-dismissible"><p>' .
				sprintf( __( 'Plugins successfully downloaded to %s.', 'download-plugins-dashboard' ),
					'<code>' . get_option( 'alg_download_plugins_dashboard_plugins_bulk_dir', alg_download_plugins()->core->get_uploads_dir( 'plugins-archive' ) ) . '</code>' ) .
			'</p></div>';
		}
		if ( isset( $_GET['alg_download_theme_bulk_finished'] ) ) {
			echo '<div class="notice notice-success is-dismissible"><p>' .
				sprintf( __( 'Themes successfully downloaded to %s.', 'download-plugins-dashboard' ),
					'<code>' . get_option( 'alg_download_plugins_dashboard_themes_bulk_dir', alg_download_plugins()->core->get_uploads_dir( 'themes-archive' ) ) . '</code>' ) .
			'</p></div>';
		}
	}

	/**
	 * add_plugin_menu.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 */
	function add_plugin_menu() {
		add_options_page(
			__( 'Download Plugins and Themes from Dashboard', 'download-plugins-dashboard' ),
			__( 'Download Plugins and Themes', 'download-plugins-dashboard' ),
			'manage_options',
			'download-plugins-dashboard',
			array( $this, 'output_plugin_menu' )
		);
	}

	/**
	 * output_plugin_menu.
	 *
	 * @version 1.6.0
	 * @since   1.2.0
	 */
	function output_plugin_menu() {
		echo '<div class="wrap">' .
			'<h2>' . __( 'Download Plugins and Themes from Dashboard', 'download-plugins-dashboard' ) . '</h2>' .
			'<form action="" method="post">' .
				'<p>' .$this->get_fields_html() . '</p>' .
				'<p>' .
					'<input class="button-primary" type="submit" name="' . $this->id . '_save_settings" value="' .
						__( 'Save settings', 'download-plugins-dashboard' ) . '">' . ' ' .
					'<input class="button-primary" type="submit" name="' . $this->id . '_reset_settings" value="' .
						__( 'Reset settings', 'download-plugins-dashboard' ) . '"' .
						' style="color:yellow;"' .
						' onclick="return confirm(\'' . __( 'Are you sure?', 'download-plugins-dashboard' ) . '\')">' .
					wp_nonce_field( $this->id . '_save_settings_nonce', $this->id . '_save_settings_nonce', true, false ) .
				'</p>' .
			'</form>' .
		'</div>';
	}

	/**
	 * save_settings.
	 *
	 * @version 1.6.0
	 * @since   1.2.0
	 */
	function save_settings() {
		if ( isset( $_POST[ $this->id . '_save_settings' ] ) || isset( $_POST[ $this->id . '_reset_settings' ] ) ) {
			if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( $this->id . '_save_settings_nonce', $this->id . '_save_settings_nonce' ) ) {
				add_action( 'admin_notices', array( $this, 'admin_notice__error' ) );
				return false;
			}
			foreach ( $this->get_settings() as $field ) {
				$field_id    = $this->id . '_' . $field['id'];
				$field_value = null;
				if ( isset( $_POST[ $this->id . '_save_settings' ] ) && isset( $_POST[ $field_id ] ) ) {
					$field_value = sanitize_text_field( $_POST[ $field_id ] );
				} elseif ( isset( $_POST[ $this->id . '_reset_settings' ] ) && isset( $field['default'] ) ) {
					$field_value = $field['default'];
				}
				if ( 'plugins_bulk_period' === $field['id'] || 'themes_bulk_period' === $field['id'] ) {
					$prev_value = get_option( $field_id, '' );
				}
				if ( null !== $field_value ) {
					update_option( $field_id, stripslashes( $field_value ) );
				}
				if ( 'plugins_bulk_period' === $field['id'] && $field_value != $prev_value ) {
					alg_download_plugins()->core->cron_unschedule_plugins_event();
					alg_download_plugins()->core->cron_schedule_plugins_event();
				}
				if ( 'themes_bulk_period' === $field['id'] && $field_value != $prev_value ) {
					alg_download_plugins()->core->cron_unschedule_themes_event();
					alg_download_plugins()->core->cron_schedule_themes_event();
				}
			}
			add_action( 'admin_notices', array( $this, 'admin_notice__success' ) );
		}
	}

	/**
	 * admin_notice__error.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 */
	function admin_notice__error() {
		echo '<div class="notice notice-error">' . '<p>' . __( 'Something went wrong!', 'download-plugins-dashboard' ) . '</p>' . '</div>';
	}

	/**
	 * admin_notice__success.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 */
	function admin_notice__success() {
		echo '<div class="notice notice-success is-dismissible">' . '<p>' . __( 'Settings saved.', 'download-plugins-dashboard' ) . '</p>' . '</div>';
	}

	/**
	 * get_fields_html.
	 *
	 * @version 1.6.0
	 * @since   1.2.0
	 */
	function get_fields_html() {
		$table_data = array();
		foreach ( $this->get_settings() as $field ) {
			$field_id    = $this->id . '_' . $field['id'];
			$field_title = '<label for="' . $field_id . '">' . $field['title'] . '</label>';
			$field_html  = '';
			if ( 'title' != $field['type'] ) {
				$field_value = ( false != get_option( $field_id, false ) ? esc_html( get_option( $field_id, false ) ) : $field['default'] );
				$custom_atts = ( ! empty( $field['custom_attributes'] ) ? ' ' . $field['custom_attributes'] : '' );
				switch ( $field['type'] ) {
					case 'select_yes_no':
						$field_html = '<select name="' . $field_id . '" id="' . $field_id . '"' . $custom_atts . '>' .
							'<option value="yes" ' . selected( $field_value, 'yes', false ) . '>' . __( 'Yes', 'download-plugins-dashboard' ) . '</option>' .
							'<option value="no" '  . selected( $field_value, 'no',  false ) . '>' . __( 'No',  'download-plugins-dashboard' ) . '</option>' .
						'</select>';
						break;
					case 'select':
						$options = '';
						foreach ( $field['options'] as $id => $desc ) {
							$options .= '<option value="' . $id . '"' . selected( $field_value, $id, false ) . '>' . $desc . '</option>';
						}
						$field_html = '<select name="' . $field_id . '" id="' . $field_id . '"' . $custom_atts . '>' . $options . '</select>';
						break;
					case 'textarea':
						$field_html = '<textarea name="' . $field_id . '" id="' . $field_id . '" style="width:100%;"' . $custom_atts . '>' . $field_value . '</textarea>';
						break;
					case 'tool':
						$field_html = '';
						break;
					default:
						$field_html = '<input type="' . $field['type'] . '" name="' . $field_id . '" id="' . $field_id . '" value="' . $field_value . '" style="width:100%;"' .
							$custom_atts . '>';
						break;
				}
			}
			if ( isset( $field['desc'] ) ) {
				$field_html .= ' ' . $field['desc'];
			}
			$table_data[] = array( $field_title, $field_html );
		}
		return $this->get_table_html(
			$table_data,
			array( 'table_heading_type' => 'vertical', 'table_class' => 'widefat striped' , 'columns_styles' => array( 'width:25%;', 'width:75%;' ) )
		);
	}

	/**
	 * get_settings.
	 *
	 * @version 1.7.0
	 * @since   1.2.0
	 */
	function get_settings() {
		$period_options = array(
			''           => __( 'Do not download', 'download-plugins-dashboard' ),
			'minutely'   => __( 'Download once a minute', 'download-plugins-dashboard' ),
			'hourly'     => __( 'Download once hourly', 'download-plugins-dashboard' ),
			'twicedaily' => __( 'Download twice daily', 'download-plugins-dashboard' ),
			'daily'      => __( 'Download once daily', 'download-plugins-dashboard' ),
			'weekly'     => __( 'Download once weekly', 'download-plugins-dashboard' ),
			'four_weeks' => __( 'Download every 4 weeks', 'download-plugins-dashboard' ),
		);
		return array(
			array(
				'title'   => '<strong>' . __( 'General Settings', 'download-plugins-dashboard' ) . '</strong>',
				'id'      => 'general_settings_title',
				'type'    => 'title',
			),
			array(
				'title'   => __( 'Add main plugin directory to ZIP', 'download-plugins-dashboard' ),
				'id'      => 'plugins_add_main_dir',
				'type'    => 'select_yes_no',
				'default' => 'yes',
			),
			array(
				'title'   => __( 'Add main theme directory to ZIP', 'download-plugins-dashboard' ),
				'id'      => 'themes_add_main_dir',
				'type'    => 'select_yes_no',
				'default' => 'yes',
			),
			array(
				'title'   => __( 'Append plugin version number to ZIP filename', 'download-plugins-dashboard' ),
				'id'      => 'plugins_append_version',
				'type'    => 'select_yes_no',
				'default' => 'no',
			),
			array(
				'title'   => __( 'Append theme version number to ZIP filename', 'download-plugins-dashboard' ),
				'id'      => 'themes_append_version',
				'type'    => 'select_yes_no',
				'default' => 'no',
			),
			array(
				'title'   => '<strong>' . __( 'Tools', 'download-plugins-dashboard' ) . '</strong>',
				'id'      => 'tools_title',
				'type'    => 'title',
				'desc'    => '<em>' . sprintf(
					__( 'Please note that if you have large number of plugins or themes, you may need to <a href="%s" target="_blank">increase your WP memory limits</a> to use "Download all" tools. Your current memory limits are: %s (standard) and %s (admin).', 'download-plugins-dashboard' ),
						'https://wordpress.org/support/article/editing-wp-config-php/#increasing-memory-allocated-to-php', WP_MEMORY_LIMIT, WP_MAX_MEMORY_LIMIT ) . '</em>',
			),
			array(
				'title'   => __( 'Plugins', 'download-plugins-dashboard' ),
				'id'      => 'plugins_tools',
				'type'    => 'tool',
				'default' => '',
				'desc'    => '<a href="' . add_query_arg( 'alg_download_plugin_all', true ) . '" class="button">' . __( 'Download all', 'download-plugins-dashboard' ) . '</a>' . ' ' .
					'<em>' .
						__( 'Please note that this won\'t include "Must-Use" and "Drop-in" plugins.', 'download-plugins-dashboard' ) . ' ' .
						__( 'However, you can download them from "Plugins" page directly.', 'download-plugins-dashboard' ) .
					'</em>',
			),
			array(
				'title'   => __( 'Themes', 'download-plugins-dashboard' ),
				'id'      => 'themes_tools',
				'type'    => 'tool',
				'default' => '',
				'desc'    => '<a href="' . add_query_arg( 'alg_download_theme_all', true )  . '" class="button">' . __( 'Download all', 'download-plugins-dashboard' ) . '</a>',
			),
			array(
				'title'   => '<strong>' . __( 'Advanced Settings', 'download-plugins-dashboard' ) . '</strong>',
				'id'      => 'advanced_settings_title',
				'type'    => 'title',
			),
			array(
				'title'   => __( 'ZIP library', 'download-plugins-dashboard' ),
				'desc'    => '<em>' . __( 'Sets which ZIP library should be used.', 'download-plugins-dashboard' ) . ' ' .
					__( 'Leave the default value if not sure.', 'download-plugins-dashboard' ) . '</em>',
				'id'      => 'zip_library',
				'type'    => 'select',
				'default' => ( class_exists( 'ZipArchive' ) ? 'ziparchive' : 'pclzip' ),
				'options' => array(
					'ziparchive' => 'ZipArchive',
					'pclzip'     => 'PclZip',
				),
			),
			array(
				'title'   => __( 'Temporary directory', 'download-plugins-dashboard' ),
				'desc'    => '<em>' . sprintf( __( 'Leave blank to use the default system temporary directory: %s.', 'download-plugins-dashboard' ),
					'<code>' . alg_download_plugins()->core->get_sys_temp_dir() . '</code>' ) . '</em>',
				'id'      => 'temp_dir',
				'type'    => 'text',
				'default' => '',
			),
			array(
				'title'   => '<strong>' . __( 'Periodical Downloads', 'download-plugins-dashboard' ) . '</strong>',
				'id'      => 'periodical_downloads_title',
				'type'    => 'title',
				'desc'    => apply_filters( 'alg_download_plugins_settings', '<em>' . sprintf( __( 'Periodical downloads are available in %s only.', 'download-plugins-dashboard' ),
					'<a target="_blank" href="https://wpfactory.com/item/download-plugins-and-themes-from-dashboard/">' .
						__( 'Pro version', 'download-plugins-dashboard' ) . '</a>' ) . '</em>' ),
			),
			array(
				'title'   => __( 'Periodical plugins downloads', 'download-plugins-dashboard' ),
				'id'      => 'plugins_bulk_period',
				'type'    => 'select',
				'default' => '',
				'options' => $period_options,
				'desc'    => apply_filters( 'alg_download_plugins_settings', '<em>' . sprintf( __( 'Possible options: %s.', 'download-plugins-dashboard' ),
					implode( '; ', $period_options ) ) . '</em>', 'plugins_bulk_period' ) . ' ' .
					'<em>' .
						__( 'Please note that this won\'t include "Must-Use", "Drop-in" and "Single File" plugins.', 'download-plugins-dashboard' ) . ' ' .
						__( 'However, you can download them from "Plugins" page directly.', 'download-plugins-dashboard' ) .
					'</em>',
				'custom_attributes' => apply_filters( 'alg_download_plugins_settings', 'disabled' ),
			),
			array(
				'title'   => __( 'Periodical themes downloads', 'download-plugins-dashboard' ),
				'id'      => 'themes_bulk_period',
				'type'    => 'select',
				'default' => '',
				'options' => $period_options,
				'desc'    => apply_filters( 'alg_download_plugins_settings', '', 'themes_bulk_period' ),
				'custom_attributes' => apply_filters( 'alg_download_plugins_settings', 'disabled' ),
			),
			array(
				'title'   => __( 'Path for periodical plugins downloads', 'download-plugins-dashboard' ),
				'id'      => 'plugins_bulk_dir',
				'type'    => 'text',
				'default' => alg_download_plugins()->core->get_uploads_dir( 'plugins-archive' ),
				'custom_attributes' => apply_filters( 'alg_download_plugins_settings', 'disabled' ),
			),
			array(
				'title'   => __( 'Path for periodical themes downloads', 'download-plugins-dashboard' ),
				'id'      => 'themes_bulk_dir',
				'type'    => 'text',
				'default' => alg_download_plugins()->core->get_uploads_dir( 'themes-archive' ),
				'custom_attributes' => apply_filters( 'alg_download_plugins_settings', 'disabled' ),
			),
			array(
				'title'   => __( 'Append download date & time to plugin ZIP filename', 'download-plugins-dashboard' ),
				'id'      => 'plugins_append_date_time',
				'type'    => 'select_yes_no',
				'default' => 'no',
				'custom_attributes' => apply_filters( 'alg_download_plugins_settings', 'disabled' ),
			),
			array(
				'title'   => __( 'Append download date & time to theme ZIP filename', 'download-plugins-dashboard' ),
				'id'      => 'themes_append_date_time',
				'type'    => 'select_yes_no',
				'default' => 'no',
				'custom_attributes' => apply_filters( 'alg_download_plugins_settings', 'disabled' ),
			),
			array(
				'title'   => __( 'Plugins output file(s)', 'download-plugins-dashboard' ),
				'id'      => 'plugins_output_files',
				'type'    => 'select',
				'default' => 'each',
				'options' => array(
					'each' => __( 'Each plugin\'s zip', 'download-plugins-dashboard' ),
					'all'  => __( 'All plugins in single zip', 'download-plugins-dashboard' ),
					'both' => __( 'Each plugin\'s zip + All plugins in single zip', 'download-plugins-dashboard' ),
				),
				'desc'    => apply_filters( 'alg_download_plugins_settings', '<em>' . sprintf( __( 'Possible options: %s.', 'download-plugins-dashboard' ),
					implode( '; ', array(
						__( 'Each plugin\'s or theme\'s zip', 'download-plugins-dashboard' ),
						__( 'All plugins or themes in single zip', 'download-plugins-dashboard' ),
						__( 'Each plugin\'s or theme\'s zip + All plugins or themes in single zip', 'download-plugins-dashboard' ),
					) ) ) . '</em>' ),
				'custom_attributes' => apply_filters( 'alg_download_plugins_settings', 'disabled' ),
			),
			array(
				'title'   => __( 'Themes output file(s)', 'download-plugins-dashboard' ),
				'id'      => 'themes_output_files',
				'type'    => 'select',
				'default' => 'each',
				'options' => array(
					'each' => __( 'Each theme\'s zip', 'download-plugins-dashboard' ),
					'all'  => __( 'All themes in single zip', 'download-plugins-dashboard' ),
					'both' => __( 'Each theme\'s zip + All themes in single zip', 'download-plugins-dashboard' ),
				),
				'custom_attributes' => apply_filters( 'alg_download_plugins_settings', 'disabled' ),
			),
			array(
				'title'   => __( 'Plugins single ZIP file name', 'download-plugins-dashboard' ),
				'id'      => 'plugins_single_zip_file_name',
				'type'    => 'text',
				'default' => 'plugins',
				'custom_attributes' => apply_filters( 'alg_download_plugins_settings', 'disabled' ),
			),
			array(
				'title'   => __( 'Themes single ZIP file name', 'download-plugins-dashboard' ),
				'id'      => 'themes_single_zip_file_name',
				'type'    => 'text',
				'default' => 'themes',
				'custom_attributes' => apply_filters( 'alg_download_plugins_settings', 'disabled' ),
			),
		);
	}

	/**
	 * get_table_html.
	 *
	 * @version 1.8.0
	 * @since   1.2.0
	 */
	function get_table_html( $data, $args = array() ) {
		$defaults = array(
			'table_class'        => '',
			'table_style'        => '',
			'row_styles'         => '',
			'table_heading_type' => 'horizontal',
			'columns_classes'    => array(),
			'columns_styles'     => array(),
		);
		$args         = array_merge( $defaults, $args );
		$table_class  = ( '' == $args['table_class'] ? '' : ' class="' . $args['table_class'] . '"' );
		$table_style  = ( '' == $args['table_style'] ? '' : ' style="' . $args['table_style'] . '"' );
		$row_styles   = ( '' == $args['row_styles']  ? '' : ' style="' . $args['row_styles']  . '"' );
		$html         = '';
		$html        .= '<table' . $table_class . $table_style . '>';
		$html        .= '<tbody>';
		foreach( $data as $row_number => $row ) {
			$html .= '<tr' . $row_styles . '>';
			foreach( $row as $column_number => $value ) {
				$th_or_td      = ( ( 0 === $row_number && 'horizontal' === $args['table_heading_type'] ) || ( 0 === $column_number && 'vertical' === $args['table_heading_type'] ) ? 'th' : 'td' );
				$column_class  = ( ! empty( $args['columns_classes'][ $column_number ] ) ? ' class="' . $args['columns_classes'][ $column_number ] . '"' : '' );
				$column_style  = ( ! empty( $args['columns_styles'][ $column_number ] )  ? ' style="' . $args['columns_styles'][ $column_number ]  . '"' : '' );
				$html         .= '<' . $th_or_td . $column_class . $column_style . '>';
				$html         .= $value;
				$html         .= '</' . $th_or_td . '>';
			}
			$html .= '</tr>';
		}
		$html .= '</tbody>';
		$html .= '</table>';
		return $html;
	}

}

endif;

return new Alg_Download_Plugins_Settings();
