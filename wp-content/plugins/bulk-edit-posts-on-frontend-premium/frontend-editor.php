<?php defined( 'ABSPATH' ) || exit;
/*
  Plugin Name: WP Sheet Editor - Editable Frontend Tables (Premium)
  Description: Display spreadsheet editor on the frontend or custom admin pages, create custom spreadsheets as dashboards for apps.
  Version: 2.4.33
  Update URI: https://api.freemius.com
  Author:      WP Sheet Editor
  Author URI:  https://wpsheeteditor.com/?utm_source=wp-admin&utm_medium=plugins-list&utm_campaign=frontend
  Plugin URI: https://wpsheeteditor.com/extensions/frontend-spreadsheet-editor/?utm_source=wp-admin&utm_medium=plugins-list&utm_campaign=frontend
  License:     GPL2
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
  WC requires at least: 3.0
  WC tested up to: 8.4
  Text Domain: vg_sheet_editor_frontend
  Domain Path: /lang
  @fs_premium_only /modules/user-path/send-user-path.php, /modules/acf/, /modules/advanced-filters/, /modules/formulas/, /modules/custom-columns/, /modules/custom-post-types/, /modules/woocommerce/, /modules/universal-sheet/, /modules/yoast-seo/, /modules/wpml/, /modules/posts-templates/, /modules/columns-manager/,  /modules/wp-sheet-editor/inc/integrations/notifier.php,/modules/wp-sheet-editor/inc/integrations/extensions.json,  /plugins/, /inc/freemius-init-addon.php
 */

if (isset($_GET['wpse_troubleshoot8987'])) {
	return;
}
if (!defined('ABSPATH')) {
	exit;
}
if (function_exists('bepof_fs')) {
	bepof_fs()->set_basename(true, __FILE__);
}
if (!defined('VGSE_FRONTEND_EDITOR_DIR')) {
	define('VGSE_FRONTEND_EDITOR_DIR', __DIR__);
}
if (!defined('VGSE_FRONTEND_EDITOR_FILE')) {
	define('VGSE_FRONTEND_EDITOR_FILE', __FILE__);
}
if (!defined('VGSE_EDITORS_POST_TYPE')) {
	define('VGSE_EDITORS_POST_TYPE', 'vgse_editors');
}

require 'vendor/vg-plugin-sdk/index.php';
require 'vendor/freemius/start.php';
require 'inc/freemius-init.php';

if (bepof_fs()->can_use_premium_code__premium_only()) {
	if (!defined('VGSE_FRONTEND_IS_PREMIUM')) {
		define('VGSE_FRONTEND_IS_PREMIUM', true);
	}
}

if (!class_exists('WP_Sheet_Editor_Frontend_Editor')) {

	/**
	 * Filter rows in the spreadsheet editor.
	 */
	class WP_Sheet_Editor_Frontend_Editor {

		static private $instance = false;
		public $plugin_url = null;
		public $plugin_dir = null;
		var $current_editor_settings = null;
		var $shortcode_key = 'vg_sheet_editor';
		public $textname = 'vg_sheet_editor_frontend';
		public $buy_link = null;
		public $version = '2.3.0';
		var $settings = null;
		public $args = null;
		var $vg_plugin_sdk = null;
		var $sheets_bootstrap = null;
		var $main_admin_page_slug = null;
		var $frontend_template_key = 'vg-sheet-editor-frontend.php';

		private function __construct() {
			
		}

		function init_plugin_sdk() {

			$this->vg_plugin_sdk = new VG_Freemium_Plugin_SDK($this->args);
		}

		function auto_setup() {
			$flag_key = 'vg_sheet_editor_frontend_auto_setup';
			$already_setup = get_option($flag_key, 'no');

			if ($already_setup === 'yes') {
				return;
			}

			update_option($flag_key, 'yes');

			$default_post_type = 'post';

			wp_insert_post(array(
				'post_type' => VGSE_EDITORS_POST_TYPE,
				'post_title' => __('Edit posts', $this->textname),
				'post_status' => 'publish',
				'meta_input' => array(
					'vgse_post_type' => $default_post_type,
				),
			));
		}

		function _get_first_post() {
			$editors = new WP_Query(array(
				'post_type' => VGSE_EDITORS_POST_TYPE,
				'posts_per_page' => 1,
				'fields' => 'ids'
			));

			return ($editors->have_posts()) ? current($editors->posts) : false;
		}

		function get_upgrade_url() {
			$url = ( function_exists('bepof_fs') ) ? bepof_fs()->pricing_url(WP_FS__PERIOD_ANNUALLY, true, array(
						'licenses' => is_multisite() ? 'unlimited' : 1,
						'billing_cycle' => is_multisite() ? 'monthly' : WP_FS__PERIOD_ANNUALLY,
					)) : 'https://wpsheeteditor.com/buy-frontend-editor-wporg';
			return $url;
		}

		function notify_wrong_core_version() {
			$plugin_data = get_plugin_data(__FILE__, false, false);
			?>
			<div class="notice notice-error">
				<p><?php _e('Please update the WP Sheet Editor plugin and all its extensions to the latest version. The features of the plugin "' . $plugin_data['Name'] . '" will be disabled temporarily because it is the newest version and it conflicts with old versions of other WP Sheet Editor plugins. The features will be enabled automatically after you install the updates.', vgse_frontend_editor()->textname); ?></p>
			</div>
			<?php
		}

		function init() {

			require __DIR__ . '/modules/init.php';
			$this->modules_controller = new WP_Sheet_Editor_CORE_Modules_Init(__DIR__, bepof_fs());
			// We initialize the modules directly because the modules_init class uses the plugins_loaded hook
			// but this plugin initializes too late with the after_setup_theme
			$this->modules_controller->init();

			$this->plugin_url = plugins_url('/', __FILE__);
			$this->plugin_dir = __DIR__;
			$this->buy_link = $this->get_upgrade_url();

			$this->args = array(
				'main_plugin_file' => __FILE__,
				'show_welcome_page' => true,
				'welcome_page_file' => $this->plugin_dir . '/views/welcome-page-content.php',
				'website' => 'https://wpsheeteditor.com',
				'logo_width' => 180,
				'logo' => plugins_url('/assets/imgs/logo.svg', __FILE__),
				'buy_link' => $this->buy_link,
				'buy_link_text' => __('Try premium plugin for FREE', $this->textname),
				'plugin_name' => 'Frontend Sheet',
				'plugin_prefix' => 'vgsefe_',
				'show_whatsnew_page' => true,
				'whatsnew_pages_directory' => $this->plugin_dir . '/views/whats-new/',
				'plugin_version' => $this->version,
				'plugin_options' => $this->settings,
			);
			$this->main_admin_page_slug = $this->args['plugin_prefix'] . 'welcome_page';

			$this->init_plugin_sdk();

			$this->register_post_type();

			// Allow core editor on frontend
			add_filter('vg_sheet_editor/allowed_on_frontend', '__return_true');

			// After core has initialized
			add_action('vg_sheet_editor/initialized', array($this, 'after_core_init'));
			add_action('vg_sheet_editor/after_extensions_registered', array($this, 'after_full_core_init'));
			add_action('vg_sheet_editor/after_extensions_registered', array($this, 'bootstrap'), 20);

			// Dont register the quick setup and other subpages, we'll
			// register them manually under the frontend sheets parent menu
			add_filter('vg_sheet_editor/register_admin_pages', '__return_false');

			// Fix. When we load the metabox settings, it used "post" as current provider showing post 
			// columns instead of the custom post type columns. We use this to set the provider 
			// from the post meta as current provider.
			add_filter('vg_sheet_editor/bootstrap/get_current_provider', array($this, 'set_provider_from_post_meta'));
			load_plugin_textdomain($this->textname, false, basename(dirname(__FILE__)) . '/lang/');

			add_action('admin_init', array($this, 'set_current_editor_settings_for_ajax_calls'));
			add_filter('vg_sheet_editor/column_groups_feature_allowed', array($this, 'disable_column_groups_frontend'));
			
			add_action(
				'before_woocommerce_init',
				function() {
					if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
						$main_file  = __FILE__;
						$parent_dir = dirname( dirname( $main_file ) );
						$new_path   = str_replace( $parent_dir, '', $main_file );
						$new_path   = wp_normalize_path( ltrim( $new_path, '\\/' ) );
						\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', $new_path, true );
					}
				}
			);
		}

		function disable_column_groups_frontend($allowed) {
			if (strpos($_SERVER['REQUEST_URI'], '/post.php') !== false) {
				$allowed = false;
			}
			return $allowed;
		}

		function set_current_editor_settings_for_ajax_calls() {
			if (!wp_doing_ajax() || empty($_REQUEST['wpse_source_suffix'])) {
				return;
			}
			$editor_id = str_replace('_frontend_sheet', '', $_REQUEST['wpse_source_suffix']);
			if (!is_numeric($editor_id)) {
				return;
			}

			$this->set_current_editor_settings((int) $editor_id, 'shortcode');
		}

		function modify_js_data($args) {
			if (empty($this->current_editor_settings)) {
				return $args;
			}

			$args['wpse_source_suffix'] = '_frontend_sheet' . $this->current_editor_settings['editor_id'];
			if (!empty($args['last_session_filters'])) {
				$args['last_session_filters'] = array();
			}

			if( ! empty(VGSE()->options['frontend_hide_id_column']) && !is_admin()){
				if( isset($args['columnsUnformat']['ID'])){
					unset($args['columnsUnformat']['ID']);
				}
				if( isset($args['columnsFormat']['ID'])){
					unset($args['columnsFormat']['ID']);
				}
				if( isset($args['colWidths']['ID'])){
					unset($args['colWidths']['ID']);
				}
				if( isset($args['colHeaders']['ID'])){
					unset($args['colHeaders']['ID']);
				}
				if (!empty($args['custom_handsontable_args'])) {
					$args['custom_handsontable_args'] = json_decode($args['custom_handsontable_args'], true);
				}
				if (is_array($args['custom_handsontable_args'])) {
					if (!empty($args['custom_handsontable_args']['fixedColumnsLeft'])) {
						$args['custom_handsontable_args']['fixedColumnsLeft']--;
					}
					$args['custom_handsontable_args'] = json_encode($args['custom_handsontable_args']);
				}
				$args['startCols']--;
			}
			return $args;
		}

		function allow_builtin_post_types($post_types) {
			// The frontend plugin allows to edit the same post types as the posts plugin
			// so we will remove those post types from the list of post types with own sheet
			// If we dont remove them here, the algorithm will exclude them and ask for an upgrade to edit those post types
			if (isset(VGSE()->bundles['custom_post_types']['post_types'])) {
				$post_types = array_diff($post_types, VGSE()->bundles['custom_post_types']['post_types']);
			}

			return $post_types;
		}

		function set_provider_from_post_meta($current_provider) {
			$post_id = null;
			$post = get_queried_object();
			if (is_admin() && !empty($_GET['post']) && get_post_type($_GET['post']) === VGSE_EDITORS_POST_TYPE) {
				$post_id = (int) $_GET['post'];
			} elseif (!is_admin() && $post && !empty($post->post_content) && strpos($post->post_content, '[vg_sheet_editor editor_id=') !== false) {
				$post_id = (int) preg_replace('/.*vg_sheet_editor editor_id="?(\d+)"?.*/s', '$1', $post->post_content);
			}
			if ($post_id) {
				$raw_current_provider = get_post_meta($post_id, 'vgse_post_type', true);
				if ($raw_current_provider) {
					$current_provider = $raw_current_provider;
				}
			}
			return $current_provider;
		}

		function remove_conflicting_css() {
			global $wp_styles, $wp_scripts;
			$post = get_queried_object();
			if (!empty($post) && isset($post->post_type) && $post->post_type === 'page' && $this->frontend_template_key == basename(get_post_meta($post->ID, '_wp_page_template', true))) {
				foreach ($wp_styles->registered as $index => $style) {
					if (!empty($style->src) && strpos($style->src, 'themes/') !== false) {
						unset($wp_styles->registered[$index]);
					}
				}
				foreach ($wp_scripts->registered as $index => $script) {
					if (!empty($script->src) && strpos($script->src, 'themes/') !== false) {
						unset($wp_scripts->registered[$index]);
					}
				}
			}
		}

		function render_page_template($template) {
			$post = get_post();
			$page_template = get_post_meta($post->ID, '_wp_page_template', true);
			if ($this->frontend_template_key == basename($page_template)) {
				$template = __DIR__ . '/views/frontend/page-template.php';
				wp_enqueue_style('vg-sheet-editor-frontend-styles', plugins_url('/assets/frontend/css/style.css', __FILE__));
			}

			if (!empty($_GET['wpse_frontend_sheet_iframe'])) {
				$template = __DIR__ . '/views/frontend/page-template-iframe.php';
			}

			return $template;
		}

		function register_page_template($templates) {
			$templates[$this->frontend_template_key] = 'Frontend Spreadsheet';
			return $templates;
		}

		function after_full_core_init() {

			if (!empty(VGSE()->options['hide_admin_bar_frontend']) && !is_admin() && !VGSE()->helpers->user_can_manage_options()) {
				add_filter('show_admin_bar', '__return_false');
			}
			add_filter('vg_sheet_editor/custom_post_types/get_post_types_with_own_sheet', array($this, 'allow_builtin_post_types'));

			add_filter('vg_sheet_editor/options_page/options', array($this, 'add_settings'));
		}

		function bootstrap() {
			// Don't initialize the frontend sheet when viewing the backend sheet to avoid conflicts
			if (VGSE()->helpers->is_editor_page() && is_admin()) {
				return;
			}

			// Initialize core sheets if the CORE plugin is not installed
			// If the CORE plugin is installed, the sheets are already initialized at this point
			if (!class_exists('WP_Sheet_Editor_Dist')) {
				$post_types_to_init = $this->get_post_types();
				if (isset($post_types_to_init['user'])) {
					unset($post_types_to_init['user']);
				}
				$this->sheets_bootstrap = new WP_Sheet_Editor_Bootstrap(array(
					'enabled_post_types' => array_filter(array_keys($post_types_to_init), 'post_type_exists'),
					'register_admin_menus' => false,
				));
			}
		}

		function add_settings($settings) {
			$settings['frontend'] = array(
				'icon' => 'el-icon-cogs',
				'title' => __('Frontend Spreadsheets', vgse_frontend_editor()->textname),
				'fields' => array(
					array(
						'id' => 'frontend_hide_id_column',
						'type' => 'switch',
						'title' => __('Hide the id column in the front end?', vgse_frontend_editor()->textname),
						'desc' => __('This is a beta feature that could cause some unexpected bugs, so this is opt-in only for now.', vgse_frontend_editor()->textname),
					),
					array(
						'id' => 'frontend_table_height',
						'type' => 'text',
						'validate' => 'numeric',
						'title' => __('Table height', 'vg_sheet_editor' ),
						'desc' => __('Enter the height in px (just a number). By default, we use 90% of the window height', 'vg_sheet_editor' ),
						'default' => null,
					),
					array(
						'id' => 'frontend_login_message',
						'type' => 'editor',
						'title' => __('Login message', vgse_frontend_editor()->textname),
						'default' => __('You need to login to view this page.', vgse_frontend_editor()->textname),
						'desc' => __('This will be displayed when the current user is not logged in and tries to see a spreadsheet page. We will display a login form after your message.', vgse_frontend_editor()->textname),
					),
					array(
						'id' => 'hide_admin_bar_frontend',
						'type' => 'switch',
						'title' => __('Hide admin bar on the frontend', vgse_frontend_editor()->textname),
						'desc' => __('By default WordPress shows a black bar at the top of the page when a logged in user views a frontend page. The bar lets you access the wp-admin, log out, edit the current page, etc. If you enable this option we will hide that bar and you can use the shortcode: [vg_display_logout_link] to display the logout link.', vgse_frontend_editor()->textname),
						'default' => true,
					),
					array(
						'id' => 'frontend_logo',
						'type' => 'media',
						'url' => true,
						'title' => __('Logo', vgse_frontend_editor()->textname),
						'desc' => __('This logo will be displayed above the spreadsheet in the frontend', vgse_frontend_editor()->textname),
					),
					array(
						'id' => 'frontend_menu',
						'type' => 'select',
						'title' => __('Menu', vgse_frontend_editor()->textname),
						'desc' => __('This menu will be displayed at the top right section above the spreadsheet.', vgse_frontend_editor()->textname),
						'data' => 'menus'
					),
					array(
						'id' => 'frontend_main_color',
						'type' => 'color',
						'title' => __('Main Color', vgse_frontend_editor()->textname),
						'subtitle' => __('This color will be used as background for the header and footer.', vgse_frontend_editor()->textname),
						'default' => '#FFFFFF',
						'validate' => 'color',
					),
					array(
						'id' => 'frontend_links_color',
						'type' => 'color',
						'title' => __('Links Color', vgse_frontend_editor()->textname),
						'subtitle' => __('This color will be used for the menu links, it should be the opposite of the background color. i.e. dark background with light text, or light background with dark text', vgse_frontend_editor()->textname),
						'default' => '#000',
						'validate' => 'color',
					),
					array(
						'id' => 'frontend_allow_to_open_full_screen',
						'type' => 'switch',
						'title' => __('Allow to open the sheet as full screen?', vgse_frontend_editor()->textname),
						'desc' => __('People will be able to activate/deactivate the full screen mode in the front end.', vgse_frontend_editor()->textname),
						'default' => false,
					),
				)
			);
			return $settings;
		}

		function maybe_show_full_screen_toggle($post_type, $toolbar_type) {
			if (empty(VGSE()->options['frontend_allow_to_open_full_screen']) || $toolbar_type !== 'secondary') {
				return;
			}
			?>
			<div class="wpse-full-screen-notice button-container right-toolbar-item" data-status="1">
				<div class="wpse-full-screen-notice-content notice-on">
					<?php _e('Full screen mode is active', 'vg_sheet_editor' ); ?> 
					<a href="#" class="wpse-full-screen-toggle" ><?php _e('Exit', 'vg_sheet_editor' ); ?></a> 
				</div>

				<div class="wpse-full-screen-notice-content notice-off">
					<a href="#" class="wpse-full-screen-toggle"><?php _e('Activate full screen', 'vg_sheet_editor' ); ?></a>
				</div>
			</div>
			<?php
		}

		function register_menu_page() {
			add_menu_page(
					$this->args['plugin_name'], $this->args['plugin_name'], 'manage_options', $this->main_admin_page_slug, array($this->vg_plugin_sdk, 'render_welcome_page'), plugins_url('/assets/imgs/icon.svg', __FILE__)
			);
			if (bepof_fs()->can_use_premium_code__premium_only()) {
				add_submenu_page($this->main_admin_page_slug, __('All spreadsheets', vgse_frontend_editor()->textname), __('All spreadsheets', vgse_frontend_editor()->textname), 'manage_options', 'edit.php?post_type=vgse_editors');

				if (function_exists('vgse_custom_columns_init')) {
					add_submenu_page($this->main_admin_page_slug, __('Custom columns', vgse_frontend_editor()->textname), __('Custom columns', vgse_frontend_editor()->textname), 'manage_options', vgse_custom_columns_init()->key, array(vgse_custom_columns_init(), 'render_settings_page'));
				}
			}
		}

		function after_core_init() {


			if (version_compare(VGSE()->version, '2.25.9-beta.1') < 0) {
				add_action('admin_notices', array($this, 'notify_wrong_core_version'));
				return;
			}


			add_action('admin_menu', array($this, 'register_menu_page'));

			add_filter('theme_page_templates', array($this, 'register_page_template'));
			add_filter('page_template', array($this, 'render_page_template'));
			add_action('wp_print_styles', array($this, 'remove_conflicting_css'), 99999999);

			// Register shortcode
			add_shortcode($this->shortcode_key, array($this, 'get_frontend_editor_html'));

			// Register metaboxes
			add_action('add_meta_boxes', array($this, 'register_meta_boxes'));
			add_action('save_post', array($this, 'save_meta_box'));

			// Enqueue metabox css and js
			add_action('admin_enqueue_scripts', array($this, 'enqueue_metabox_assets'), 10, 1);

			// Override core buy link with this plugin´s
			VGSE()->buy_link = $this->buy_link;

			// Disable columns visibility filter, we will set up our own filter
			if (class_exists('WP_Sheet_Editor_Columns_Visibility')) {
				remove_filter('vg_sheet_editor/columns/all_items', array('WP_Sheet_Editor_Columns_Visibility', 'filter_columns_for_visibility'), 9999);
				add_filter('vg_sheet_editor/columns/all_items', array($this, 'filter_columns_for_visibility'), 9999);
			}
			if (bepof_fs()->can_use_premium_code__premium_only()) {
				add_filter('vg_sheet_editor/load_rows/raw_incoming_data', array($this, 'filter_rows__premium_only'));
			}
			if (class_exists('VGSE_Columns_Resizing')) {
				add_filter('vg_sheet_editor/columns/provider_items', array($this, 'filter_columns_sizes'), 19, 2);
			}
			add_filter('manage_' . VGSE_EDITORS_POST_TYPE . '_posts_columns', array($this, 'register_columns_for_admin_table'));
			add_action('manage_' . VGSE_EDITORS_POST_TYPE . '_posts_custom_column', array($this, 'render_column_for_admin_table'), 10, 2);
			add_filter( 'post_row_actions', array( $this, 'add_view_link_to_admin_table' ), 10, 2 );
		}
		public function add_view_link_to_admin_table( $actions, $post ) {
			
			$frontend_page_id = $this->get_frontend_page_id(array(
				'spreadsheet_id' => $post->ID,
				'search_statuses' => array('publish', 'draft', 'pending')
			));
			$frontend_url = get_permalink($frontend_page_id);
			if( $frontend_page_id && $frontend_url ){

				$actions['wpse_view'] = '<a target="_blank" href="' . esc_url( $frontend_url ) . '">' . esc_html__( 'View in the front end', $this->textname ) . '</a>';
			}

			return $actions;
		}
		function render_column_for_admin_table($column_key, $post_id) {
			if ($column_key == 'wpse_shortcode') {
				echo '[vg_sheet_editor editor_id="' . (int) $post_id . '"]';
			}
		}

		function register_columns_for_admin_table( $columns ){
			$columns['wpse_shortcode'] = __('Shortcode', $this->textname);
			return $columns;
		}

		// Filter column sizes to use the sizes defined by the admin who created the table
		function filter_columns_sizes($spreadsheet_columns, $post_type) {

			if (empty($this->current_editor_settings) || $this->current_editor_settings['context'] !== 'shortcode' || empty($this->current_editor_settings['editor_id'])) {
				return $spreadsheet_columns;
			}

			$post = get_post($this->current_editor_settings['editor_id']);

			$option = get_user_meta($post->post_author, 'vgse_column_sizes', true);

			if (empty($option) || empty($option[$post_type])) {
				return $spreadsheet_columns;
			}

			foreach ($option[$post_type] as $column_key => $column_width) {
				if (!isset($spreadsheet_columns[$column_key])) {
					continue;
				}
				$spreadsheet_columns[$column_key]['column_width'] = (int) $column_width;
			}

			return $spreadsheet_columns;
		}

		function filter_rows__premium_only($clean_data) {

			if (empty($this->current_editor_settings) || $this->current_editor_settings['context'] !== 'shortcode' || empty($this->current_editor_settings['editor_id'])) {
				return $clean_data;
			}


			$advanced_filters = get_post_meta($this->current_editor_settings['editor_id'], 'vgse_filters', true);
			if (empty($advanced_filters) || !is_array($advanced_filters)) {
				return $clean_data;
			}

			$session_filters = vgse_filters_init()->get_raw_filters();
			$advanced_filters_json = json_encode($advanced_filters);
			$variables = array(
				'{user_id}' => get_current_user_id()
			);
			$referrer = null;
			if (wp_get_referer()) {
				$referrer = wp_get_referer();
			} elseif (!empty($_SERVER['HTTP_REFERER'])) {
				$referrer = $_SERVER['HTTP_REFERER'];
			}
			if( strpos($advanced_filters_json, '{url:') !== false && ! empty($referrer) && parse_url($referrer, PHP_URL_QUERY)){
				parse_str(parse_url($referrer, PHP_URL_QUERY), $referrer_params);
				foreach ($referrer_params as $key => $value ) {
					if( is_string($value)){
						$variables['{url:' . sanitize_text_field($key) . '}'] = sanitize_text_field($value);
					}
				}
			}
			$meta_query = json_decode(str_replace(array_keys($variables), array_values($variables), $advanced_filters_json), true);
			$advanced_filters = array(
				'meta_query' => $meta_query
			);

			foreach ($advanced_filters['meta_query'] as $index => $meta_query) {
				if (empty($meta_query) || empty($meta_query['key'])) {
					unset($advanced_filters['meta_query'][$index]);
				}
			}

			$advanced_filters = vgse_filters_init()->_sanitize_filters($advanced_filters);
			if( ! isset($session_filters['meta_query'])){
				$session_filters['meta_query'] = array();
			}
			// We append the filters set by the admin on top of the filters set by the sheet user
			// This ensures that the sheet user doesn't see/remove those filters and the admin filters override any matching filter set by the user
			$session_filters['meta_query'] = array_merge($session_filters['meta_query'], $advanced_filters['meta_query']);
			$clean_data['filters'] = $session_filters;
			$_REQUEST['filters'] = $clean_data['filters'];
			return $clean_data;
		}

		function get_post_types() {

			$allowed_post_types = array(
				'post' => __('Posts', $this->textname),
				'page' => __('Pages', $this->textname),
			);
			if (bepof_fs()->can_use_premium_code__premium_only()) {
				$allowed_post_types = VGSE()->helpers->get_allowed_post_types();
			}
			return $allowed_post_types;
		}

		function get_allowed_post_types() {
			$allowed_post_types = $this->get_post_types();
			return apply_filters('vg_sheet_editor/frontend/allowed_post_types', $allowed_post_types);
		}

		/**
		 * Enqueue metabox assets
		 * @global obj $post
		 * @param str $hook
		 */
		function enqueue_metabox_assets($hook) {

			global $post;

			if (($hook == 'post-new.php' || $hook == 'post.php') && VGSE_EDITORS_POST_TYPE === $post->post_type) {

				VGSE()->_register_styles();
				VGSE()->_register_scripts('post');

				if (class_exists('WP_Sheet_Editor_Columns_Visibility')) {
					$columns_visibility_module = WP_Sheet_Editor_Columns_Visibility::get_instance();
					$columns_visibility_module->enqueue_assets();
				}
			}
		}

		/**
		 * Register meta box(es).
		 */
		function register_meta_boxes() {
			add_meta_box('vgse-columns-visibility-metabox', __('Quick settings', $this->textname), array($this, 'render_settings_metabox'), VGSE_EDITORS_POST_TYPE);
		}

		/**
		 * Meta box display callback.
		 *
		 * @param WP_Post $post Current post object.
		 */
		function render_settings_metabox($post) {
			$allowed_post_types = $this->get_allowed_post_types();
			add_filter('vg_sheet_editor/columns_groups_enabled', '__return_false');

			$post_type = get_post_meta($post->ID, 'vgse_post_type', true);

			if (empty($post_type) || !is_string($post_type) || !isset($allowed_post_types[$post_type])) {
				$post_type = '';
			}

			$sanitized_post_type = sanitize_text_field($post_type);
			$all_post_types = VGSE()->helpers->get_all_post_types();

			// Prepare post type selectors
			$post_type_selectors = array();
			if (!empty($all_post_types)) {
				foreach ($allowed_post_types as $post_type_key => $post_type_label) {
					$post_type_selectors[] = array(
						'key' => $post_type_key,
						'label' => $post_type_label,
						'allowed' => true,
					);
				}


				foreach ($all_post_types as $post_type_obj) {
					if (isset($allowed_post_types[$post_type_obj->name])) {
						continue;
					}

					$post_type_field = array(
						'key' => $post_type_obj->name,
						'label' => $post_type_obj->label,
						'allowed' => false,
					);
					if (bepof_fs()->can_use_premium_code__premium_only()) {
						$post_type_field['allowed'] = true;
					}

					$post_type_selectors[] = $post_type_field;
				}
			}
			if ($post_type) {
				$editor = VGSE()->helpers->get_provider_editor($post_type);
				if (empty($editor)) {
					$post_type = '';
				}
			}

			if ($post_type) {
				$frontend_page_id = $this->get_frontend_page_id(array(
					'spreadsheet_id' => $post->ID,
					'search_statuses' => array('publish', 'draft', 'pending')
				));
				$frontend_url = get_permalink($frontend_page_id);
				$all_toolbars = $editor->args['toolbars']->get_items();
				if (empty($all_toolbars) || !is_array($all_toolbars)) {
					$all_toolbars = array();
				}

				if (isset($all_toolbars[$post_type])) {
					$post_type_toolbars = $all_toolbars[$post_type];
				} else {
					$post_type_toolbars = array();
				}


				foreach ($post_type_toolbars as $toolbar_key => $toolbar_items) {
					if (empty($toolbar_items) || !is_string($toolbar_key) || !is_array($toolbar_items)) {
						unset($post_type_toolbars[$toolbar_key]);
					}

					$filtered_toolbar_items = wp_list_filter($toolbar_items, array(
						'allow_to_hide' => true,
						'allow_in_frontend' => true
					));
					$post_type_toolbars[$toolbar_key] = $filtered_toolbar_items;

					foreach ($post_type_toolbars[$toolbar_key] as $toolbar_item_key => $toolbar_item) {

						if (empty($toolbar_item) || !is_array($toolbar_item) || !isset($toolbar_item['key']) || empty($toolbar_item['label'])) {
							unset($post_type_toolbars[$toolbar_key][$toolbar_item_key]);
						}
					}
				}

				$current_toolbars = maybe_unserialize(get_post_meta($post->ID, 'vgse_toolbars', true));

				if (empty($current_toolbars) || !is_array($current_toolbars)) {
					$current_toolbars = array();
				}
				// Render the editor settings because some JS requires the texts and other info
				$editor_settings = $editor->get_editor_settings($post_type);
				?>
				<script>
					var vgse_editor_settings = <?php echo json_encode($editor_settings); ?>
				</script>
				<?php
			}
			$upgrade_label_suffix = sprintf(__(' <small>(Premium. <a href="%s" target="_blank">Try for Free for 7 Days</a>)</small>', $this->textname), VGSE()->get_buy_link('frontend-post-type-selector', $this->buy_link));

			// Columns visibility section
			if (class_exists('WP_Sheet_Editor_Columns_Visibility')) {
				$columns_visibility_module = WP_Sheet_Editor_Columns_Visibility::get_instance();
				$current_columns = maybe_unserialize(get_post_meta($post->ID, 'vgse_columns', true));
				if (!$current_columns) {
					$current_columns = array();
				}
				$column_visibility_options = null;
				if (!empty($current_columns)) {
					$column_visibility_options = array(
						$post_type => $current_columns
					);
				}
			}

			$this->set_current_editor_settings($post->ID, 'metabox');
			include __DIR__ . '/views/backend/metabox.php';
		}

		function set_current_editor_settings($editor_id, $context) {

			$post_type = get_post_meta($editor_id, 'vgse_post_type', true);
			$columns = maybe_unserialize(get_post_meta($editor_id, 'vgse_columns', true));
			$toolbars = maybe_unserialize(get_post_meta($editor_id, 'vgse_toolbars', true));

			$raw_toolbars = serialize($toolbars);
			if (strpos($raw_toolbars, "run_formula") === false) {
				add_filter('vg_sheet_editor/formulas/is_bulk_selector_column_allowed', '__return_false');
			}

			// Cache editor settings for later
			$this->current_editor_settings = array(
				'toolbars' => $toolbars,
				'columns' => $columns,
				'post_type' => $post_type,
				'editor_id' => $editor_id,
				'context' => $context
			);
		}

		function get_frontend_page_id($args = array()) {
			extract(wp_parse_args($args, array(
				'spreadsheet_id' => null,
				'auto_create' => false,
				'search_statuses' => array('publish')
			)));
			global $wpdb;

			$shortcode = '[vg_sheet_editor editor_id="' . $spreadsheet_id . '"]';
			$post_type = get_post_meta($spreadsheet_id, 'vgse_post_type', true);
			$statuses_in_query_placeholders = implode(', ', array_fill(0, count($search_statuses), '%s'));
			$page_id = (int) $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type = 'page' AND post_status IN ($statuses_in_query_placeholders) AND post_content LIKE %s", array_merge($search_statuses, array('%' . $wpdb->esc_like($shortcode) . '%'))));
			if (!$page_id && $auto_create) {
				$page_id = wp_insert_post(array(
					'post_type' => 'page',
					'post_status' => 'publish',
					'post_title' => 'Edit ' . $post_type,
					'post_content' => $shortcode,
				));
				update_post_meta($page_id, '_wp_page_template', $this->frontend_template_key);
			}
			return $page_id;
		}

		/**
		 * Save meta box content.
		 *
		 * @param int $post_id Post ID
		 */
		function save_meta_box($post_id) {

			if (!isset($_POST['bep-nonce']) || !wp_verify_nonce($_POST['bep-nonce'], 'bep-nonce')) {
				return $post_id;
			}
			// cleanup data
			if (empty($_POST['vgse_post_type']) || !is_string($_POST['vgse_post_type'])) {
				return;
			}

			// Verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
			// to do anything
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return $post_id;
			}

			$post = get_post($post_id);

			if ($post->post_type !== VGSE_EDITORS_POST_TYPE) {
				return $post_id;
			}

			$post_type = VGSE()->helpers->sanitize_table_key($_POST['vgse_post_type']);
			$allowed_post_types = $this->get_allowed_post_types();
			if (!isset($allowed_post_types[$post_type])) {
				return;
			}
			// If the sheet changed, remove the settings that depend on the sheet, otherwise it would continue
			// saving columns and filters of the old sheet
			$current_post_type = get_post_meta( $post_id, 'vgse_post_type', true );
			if( $current_post_type !== $post_type ){
				$_POST['disallowed_columns_names'] = array();
				$_POST['disallowed_columns'] = array();
				$_POST['columns'] = array();
				$_POST['columns_names'] = array();
				$_POST['vgse_columns_enabled_all_keys'] = '';
				$_POST['meta_query'] = array();
			}
			update_post_meta($post_id, 'vgse_post_type', $post_type);

			// Automatically enable the spreadsheet if the admin selected a sheet that's available but not enabled yet
			$enabled_post_types = VGSE()->helpers->get_enabled_post_types();
			if( VGSE()->helpers->user_can_manage_options() && ! in_array( $post_type, $enabled_post_types, true ) ) {
				VGSE()->options['be_post_types'][] = $post_type;
				VGSE()->update_option( 'be_post_types', VGSE()->options['be_post_types'] );
			}

			if (isset($_POST['vgse_columns_enabled_all_keys']) && class_exists('WP_Sheet_Editor_Columns_Visibility')) {
				// It's possible that zero columns are disabled, so we need to define these 
				// variables because they wont' come from the form
				if (empty($_POST['disallowed_columns_names'])) {
					$_POST['disallowed_columns_names'] = array();
				}
				if (empty($_POST['disallowed_columns'])) {
					$_POST['disallowed_columns'] = array();
				}
				if (empty($_POST['columns'])) {
					$_POST['columns'] = array();
				}
				if (empty($_POST['columns_names'])) {
					$_POST['columns_names'] = array();
				}
				update_post_meta($post_id, 'vgse_columns', array(
					'enabled' => array_combine(VGSE()->helpers->safe_text_only($_POST['columns']), VGSE()->helpers->safe_text_only($_POST['columns_names'])),
					'disabled' => array_combine(VGSE()->helpers->safe_text_only($_POST['disallowed_columns']), VGSE()->helpers->safe_text_only($_POST['disallowed_columns_names'])),
				));
			}

			if (bepof_fs()->can_use_premium_code__premium_only()) {
				if (isset($_POST['meta_query'])) {
					$filters = vgse_filters_init()->_sanitize_filters(array('meta_query' => $_POST['meta_query']));
					update_post_meta($post_id, 'vgse_filters', isset($filters['meta_query']) ? $filters['meta_query'] : array());
				}
			}
			if (isset($_POST['vgse_toolbar_item'])) {
				update_post_meta($post_id, 'vgse_toolbars', VGSE()->helpers->safe_text_only($_POST['vgse_toolbar_item']));
			}

			do_action('vg_sheet_editor/frontend/metabox/after_fields_saved', $post_id, $allowed_post_types);
		}

		// Register Custom Post Type
		function register_post_type() {

			$labels = array(
				'name' => _x('Spreadsheets', 'Post Type General Name', $this->textname),
				'singular_name' => _x('Spreadsheet', 'Post Type Singular Name', $this->textname),
				'menu_name' => $this->args['plugin_name'],
				'name_admin_bar' => __('Post Type', $this->textname),
				'archives' => __('Spreadsheet Archives', $this->textname),
				'attributes' => __('Spreadsheet Attributes', $this->textname),
				'parent_item_colon' => __('Parent Spreadsheet:', $this->textname),
				'all_items' => __('All Spreadsheets', $this->textname),
				'add_new_item' => __('Add New Spreadsheet', $this->textname),
				'add_new' => __('Add New', $this->textname),
				'new_item' => __('New Spreadsheet', $this->textname),
				'edit_item' => __('Edit settings', $this->textname),
				'update_item' => __('Update settings', $this->textname),
				'view_item' => __('View Spreadsheet', $this->textname),
				'view_items' => __('View Spreadsheets', $this->textname),
				'search_items' => __('Search Spreadsheet', $this->textname),
				'not_found' => __('Not found', $this->textname),
				'not_found_in_trash' => __('Not found in Trash', $this->textname),
				'featured_image' => __('Featured Image', $this->textname),
				'set_featured_image' => __('Set featured image', $this->textname),
				'remove_featured_image' => __('Remove featured image', $this->textname),
				'use_featured_image' => __('Use as featured image', $this->textname),
				'insert_into_item' => __('Insert into item', $this->textname),
				'uploaded_to_this_item' => __('Uploaded to this item', $this->textname),
				'items_list' => __('Spreadsheets list', $this->textname),
				'items_list_navigation' => __('Spreadsheets list navigation', $this->textname),
				'filter_items_list' => __('Filter items list', $this->textname),
			);
			$args = array(
				'label' => $this->args['plugin_name'],
				'labels' => $labels,
				'supports' => array('title'),
				'hierarchical' => false,
				'public' => false,
				'show_ui' => true,
				'show_in_menu' => false,
				'menu_position' => 99,
				'show_in_admin_bar' => false,
				'show_in_nav_menus' => false,
				'can_export' => true,
				'has_archive' => false,
				'exclude_from_search' => true,
				'publicly_queryable' => false,
				'rewrite' => false,
				'capability_type' => 'page',
			);
			register_post_type(VGSE_EDITORS_POST_TYPE, $args);
		}

		/**
		 * Get frontend editor html
		 * @param array $atts
		 * @param str $content
		 * @return str
		 */
		function get_frontend_editor_html($atts = array(), $content = '') {

			$a = shortcode_atts(array(
				'editor_id' => '',
				'iframe' => false
					), $atts);

			if (empty($a['editor_id']) || !function_exists('VGSE')) {
				return;
			}

			if (!is_user_logged_in()) {
				$login_message = (!empty(VGSE()->options['frontend_login_message']) ) ? wp_kses_post(wpautop(VGSE()->options['frontend_login_message'])) : '';
				$login_form = wp_login_form(array('echo' => false, 'redirect' => sanitize_text_field($_SERVER['REQUEST_URI'])));

				ob_start();
				include 'views/frontend/log-in-message.php';
				return ob_get_clean();
			}

			// Allow plugins to do custom validation before showing the form and show custom error messages
			$error_message = apply_filters('vg_sheet_editor/frontend/get_editor_html_error', null, $a, $this);
			if (!empty($error_message)) {
				return $error_message;
			}


			$editor_id = (int) $a['editor_id'];
			$post_type = get_post_meta($editor_id, 'vgse_post_type', true);

			$allowed_post_types = $this->get_allowed_post_types();

			if (empty($post_type) || !is_string($post_type) || (!isset($allowed_post_types[$post_type]) && !in_array($post_type, $allowed_post_types) )) {
				return;
			}

			if (!empty($a['iframe'])) {
				$url = esc_url(add_query_arg(array(
					'wpse_frontend_sheet_iframe' => $editor_id
				)));
				ob_start();
				include __DIR__ . '/views/frontend/iframe.php';
				return ob_get_clean();
			}

			$this->set_current_editor_settings($editor_id, 'shortcode');

			// Only show columns that were explicitly enabled in the metabox, don't show new columns automatically
			if (!defined('WPSE_ONLY_EXPLICITLY_ENABLED_COLUMNS')) {
				define('WPSE_ONLY_EXPLICITLY_ENABLED_COLUMNS', true);
			}

			// Filter is_editor_page
			VGSE()->options['exclude_non_visible_columns_from_tools'] = true;
			add_filter('vg_sheet_editor/columns_groups_enabled', '__return_false');
			add_action('vg_sheet_editor/is_editor_page', '__return_true');
			add_filter('vg_sheet_editor/js_data', array($this, 'modify_js_data'), 99);

			do_action('vg_sheet_editor/render_editor_js_settings');

			// Hide editor logo on frontend
			add_filter('vg_sheet_editor/editor_page/allow_display_logo', '__return_false');

			// Filter toolbar items based on shortcode settings
			add_filter('vg_sheet_editor/toolbar/get_items', array($this, 'filter_toolbar_items'));
			add_filter('vg_sheet_editor/import/woocommerce/special_product_mapping_options', array($this, 'filter_wc_import_columns'), 10, 2);
			add_action('vg_sheet_editor/toolbar/after_buttons', array($this, 'maybe_show_full_screen_toggle'), 10, 2);

			// Enqueue css and js on frontend
			VGSE()->_register_styles();
			wp_enqueue_media();
			VGSE()->_register_scripts($post_type);

			$editor = VGSE()->helpers->get_provider_editor($post_type);
			if (is_object($editor)) {
				$editor->remove_conflicting_assets();
			}

			if ( ! $editor || ! VGSE()->helpers->user_can_edit_post_type( $post_type ) ) {
				return '<p>' . __( 'You don\'t have enough permissions to view the spreadsheet editor.', 'vg_sheet_editor' ) . '</p>';
			}

			// Get editor page
			$current_post_type = $post_type;
			ob_start();
			require VGSE_DIR . '/views/editor-page.php';

			// Enable the infinite scroll
			echo '<input type="checkbox" id="infinito" style="display: none;" checked/>';
			$content = ob_get_clean();

			add_action('wp_footer', array($this, 'enqueue_assets'));
			return $content;
		}

		function enqueue_assets() {
			wp_enqueue_style('vg-sheet-editor-frontend-css', plugins_url('/assets/frontend/css/general.css', __FILE__));
			wp_enqueue_script('vg-sheet-editor-frontend-js', plugins_url('/assets/frontend/js/init.js', __FILE__), array('jquery'), filemtime(__DIR__ . '/assets/frontend/js/init.js'), true);
			wp_localize_script('vg-sheet-editor-frontend-js', 'vgse_frontend_data', array(
				'frontend_table_height' => !empty(VGSE()->options['frontend_table_height']) ? (int) VGSE()->options['frontend_table_height'] : null
			));
		}

		function filter_wc_import_columns($mapping_options) {
			$sheet_to_wc_keys = array_flip(VGSE()->WC->core_to_woo_importer_columns_list);

			if (empty($this->current_editor_settings) || $this->current_editor_settings['context'] !== 'shortcode' || !is_array($this->current_editor_settings['columns']) || empty($this->current_editor_settings['columns']['enabled'])) {
				return $mapping_options;
			}
			foreach ($mapping_options as $key => $value) {
				if ($key === 'id') {
					continue;
				}
				if (is_array($value)) {
					foreach ($value['options'] as $sub_key => $sub_value) {
						$sheet_key = isset($sheet_to_wc_keys[$sub_key]) ? $sheet_to_wc_keys[$sub_key] : null;
						if (!$sheet_key || !isset($this->current_editor_settings['columns']['enabled'][$sheet_key])) {
							unset($mapping_options[$key]['options'][$sub_key]);
						}
					}
					if (empty($mapping_options[$key]['options'])) {
						unset($mapping_options[$key]);
					}
				} else {
					$sheet_key = isset($sheet_to_wc_keys[$key]) ? $sheet_to_wc_keys[$key] : null;
					if (!$sheet_key || !isset($this->current_editor_settings['columns']['enabled'][$sheet_key])) {
						unset($mapping_options[$key]);
					}
				}
			}
			return $mapping_options;
		}


		/**
		 * Filter toolbar items based on shortcode settings
		 * @param array $items
		 * @return array
		 */
		function filter_toolbar_items($items) {

			if (empty($this->current_editor_settings) || $this->current_editor_settings['context'] !== 'shortcode' || is_string($this->current_editor_settings['toolbars']) && $this->current_editor_settings['toolbars'] === 'all') {
				return $items;
			}
			if (empty($this->current_editor_settings['toolbars'])) {
				$this->current_editor_settings['toolbars'] = array();
			}
			foreach ($items[$this->current_editor_settings['post_type']] as $toolbar => $toolbar_items) {

				if (isset($this->current_editor_settings['toolbars'][$toolbar]) && is_string($this->current_editor_settings['toolbars'][$toolbar]) && $this->current_editor_settings['toolbars'][$toolbar] === 'all') {
					continue;
				}

				if (!isset($this->current_editor_settings['toolbars'][$toolbar])) {
					$this->current_editor_settings['toolbars'][$toolbar] = array();
				}

				foreach ($toolbar_items as $index => $item) {
					// Display If this is a child item and the parent is enabled
					if (!empty($item['parent']) && in_array($item['parent'], $this->current_editor_settings['toolbars'][$toolbar], true)) {
						continue;
					}
					if (!in_array($item['key'], $this->current_editor_settings['toolbars'][$toolbar], true) && $item['allow_to_hide']) {

						unset($items[$this->current_editor_settings['post_type']][$toolbar][$index]);
					}
				}
			}

			return $items;
		}

		/**
		 * Filter column items based on shortcode settings
		 * @param array $columns
		 * @return array
		 */
		function filter_columns_for_visibility($columns) {

			if (empty($this->current_editor_settings)) {
				return WP_Sheet_Editor_Columns_Visibility::filter_columns_for_visibility($columns);
			}
			$options = null;
			if (!empty($this->current_editor_settings['columns'])) {
				$options = array(
					$this->current_editor_settings['post_type'] => $this->current_editor_settings['columns']
				);

				// Use the meta columns backup as default. So if the frontend table uses columns that were 
				// not detected automatically, we add them here to avoid problems
				$meta_columns_backup = get_post_meta($this->current_editor_settings['editor_id'], 'vgse_enabled_meta_columns', true);
				if (is_array($meta_columns_backup)) {
					$columns[$this->current_editor_settings['post_type']] = wp_parse_args($columns[$this->current_editor_settings['post_type']], $meta_columns_backup);
				}
			}

			$filtered = WP_Sheet_Editor_Columns_Visibility::filter_columns_for_visibility(array(
						$this->current_editor_settings['post_type'] => $columns[$this->current_editor_settings['post_type']]
							), $options);
			return $filtered;
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if (null == WP_Sheet_Editor_Frontend_Editor::$instance) {
				WP_Sheet_Editor_Frontend_Editor::$instance = new WP_Sheet_Editor_Frontend_Editor();
				WP_Sheet_Editor_Frontend_Editor::$instance->init();
			}
			return WP_Sheet_Editor_Frontend_Editor::$instance;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

}

add_action('after_setup_theme', 'vgse_frontend_editor', 99);

if (!function_exists('vgse_frontend_editor')) {

	function vgse_frontend_editor() {
		return WP_Sheet_Editor_Frontend_Editor::get_instance();
	}

}

$directories = glob(__DIR__ . '/integrations/*', GLOB_ONLYDIR);

if (!empty($directories)) {
	$directories = array_map('basename', $directories);
	foreach ($directories as $directory) {
		$file = __DIR__ . "/integrations/$directory/$directory.php";
		if (file_exists($file)) {
			require_once $file;
		}
	}
}