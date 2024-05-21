<?php defined( 'ABSPATH' ) || exit;
if (!class_exists('WP_Sheet_Editor_Universal_Sheet')) {

	/**
	 * Filter rows in the spreadsheet editor.
	 */
	class WP_Sheet_Editor_Universal_Sheet {

		static private $instance = false;
		public $plugin_url = null;
		public $plugin_dir = null;
		public $buy_link = null;
		var $settings = null;
		public $args = null;
		var $vg_plugin_sdk = null;

		private function __construct() {
			
		}

		function init() {
			$this->plugin_url = plugins_url('/', __FILE__);
			$this->plugin_dir = __DIR__;

			require __DIR__ . '/inc/csv-api.php';
			add_action('vg_sheet_editor/initialized', array($this, 'late_init'));
		}

		function late_init() {
//			require __DIR__ . '/inc/google-sheets.php';

			add_action('vg_sheet_editor/editor/before_init', array($this, 'register_toolbar_items'));

			// Enqueue metabox css and js
			add_action('vg_sheet_editor/after_enqueue_assets', array($this, 'enqueue_assets'));
			add_action('wp_ajax_vgse_delete_saved_export', array($this, 'delete_saved_export'));
		}

		/**
		 * Render modal for exporting csv
		 * @param str $post_type
		 * @return null
		 */
		function render_export_csv_modal($post_type) {
			$nonce = wp_create_nonce('bep-nonce');
			include __DIR__ . '/views/export-modal.php';
		}

		/**
		 * Render modal for importing csv
		 * @param str $post_type
		 * @return null
		 */
		function render_import_csv_modal($post_type) {
			$nonce = wp_create_nonce('bep-nonce');
			include __DIR__ . '/views/import-modal.php';
		}

		function get_import_sources_options(){
			$out = array(
					'--' => __( '--', 'vg_sheet_editor' ),
					'csv_upload' => __( 'CSV file from my computer', 'vg_sheet_editor' ),
					'csv_url' => __( 'CSV file from url', 'vg_sheet_editor' ),
					'paste' => __( 'Copy & paste from another spreadsheet or table', 'vg_sheet_editor' ),
					'server_file' => __( 'CSV file in the server', 'vg_sheet_editor' ),
			);
			return apply_filters('vg_sheet_editor/universal_sheet/import_sources_options', $out);
		}
		function get_target_software_options(){
			$target = array(
				''    => array(
					'default' => __( '--', 'vg_sheet_editor' ),
					'excel' => __( 'Microsoft Excel (Office 365)', 'vg_sheet_editor' ),
					'google_sheets' => __( 'Google Sheets', 'vg_sheet_editor' ),
					'other' => __( 'Other', 'vg_sheet_editor' ),
				),
				'old' => array(
					'old' => __( 'Older versions of Microsoft Excel', 'vg_sheet_editor' ),
				),
			);
			return apply_filters('vg_sheet_editor/universal_sheet/target_software_options', $target);
		}

		function get_export_options($post_type) {
			$columns = VGSE()->helpers->get_post_type_columns_options($post_type, array(
				'conditions' => array(
					'allow_plain_text' => true
				),
					), false, false, true);
			return apply_filters('vg_sheet_editor/export/columns', $columns, $post_type);
		}

		function render_wp_fields_export_options($post_type) {
			$columns = $this->get_export_options($post_type);
			do_action('vg_sheet_editor/export/before_available_columns_options', $post_type);
			foreach ($columns as $key => $column) {
				echo '<option value="' . esc_attr($key) . '">' . esc_html($column['title']) . '</option>';
			}
			do_action('vg_sheet_editor/export/after_available_columns_options', $post_type);
		}

		function get_import_options($post_type) {
			// we don't use allow_to_save => true here because we need 
			// readonly columns like the ID to find items to update		
			$columns = VGSE()->helpers->get_post_type_columns_options($post_type, array(
				'conditions' => array(
					'allow_plain_text' => true,
				),
					), false, false, true);
			$not_allowed_columns = VGSE()->helpers->get_post_type_columns_options($post_type, array(
				'conditions' => array(
					'allow_to_import' => false,
				),
					), false, false, true);
			if ($not_allowed_columns) {
				$columns = array_diff_key($columns, $not_allowed_columns);
			}
			return apply_filters('vg_sheet_editor/import/columns', $columns, $post_type);
		}

		function render_wp_fields_import_options($post_type) {
			$columns = $this->get_import_options($post_type);
			?>
			<option value=""><?php _e('Ignore this column', 'vg_sheet_editor' ); ?></option>
			<?php
			do_action('vg_sheet_editor/import/before_available_columns_options', $post_type);
			foreach ($columns as $key => $column) {
				echo '<option value="' . esc_attr($key) . '">' . esc_html($column['title']) . '</option>';
			}

			if (post_type_exists($post_type)) {
				echo '<option value="post_name__in">' . __('Full URL', 'vg_sheet_editor' ) . '</option>';
			}
			do_action('vg_sheet_editor/import/after_available_columns_options', $post_type);
		}

		function delete_saved_export() {
			if (empty($_REQUEST['post_type']) || empty($_REQUEST['search_name'])) {
				wp_send_json_error(array('message' => __('Missing parameters.', 'vg_sheet_editor' )));
			}


			if (!VGSE()->helpers->verify_nonce_from_request() || !VGSE()->helpers->user_can_manage_options()) {
				wp_send_json_error(array('message' => __('You dont have enough permissions to view this page.', 'vg_sheet_editor' )));
			}

			$post_type = VGSE()->helpers->sanitize_table_key($_REQUEST['post_type']);
			$name = sanitize_text_field($_REQUEST['search_name']);

			$saved_items = get_option('vgse_saved_exports');
			if (empty($saved_items)) {
				wp_send_json_success();
			}

			if (!isset($saved_items[$post_type])) {
				wp_send_json_success();
			}

			$same_name = wp_list_filter($saved_items[$post_type], array('name' => $name));
			foreach ($same_name as $index => $same_name_search) {
				unset($saved_items[$post_type][$index]);
			}
			update_option('vgse_saved_exports', $saved_items);
			wp_send_json_success();
		}

		function register_toolbar_items($editor) {
			$post_types = $editor->args['enabled_post_types'];
			foreach ($post_types as $post_type) {
				$editor->args['toolbars']->register_item('export_csv', array(
					'type' => 'button', // html | switch | button
					'content' => __('Export', 'vg_sheet_editor' ),
					'id' => 'export_csv',
					'allow_in_frontend' => true,
					'toolbar_key' => 'secondary',
					'extra_html_attributes' => 'data-remodal-target="export-csv-modal"',
					'footer_callback' => array($this, 'render_export_csv_modal')
						), $post_type);

				if (VGSE()->helpers->user_can_manage_options()) {
					$saved_exports = WPSE_CSV_API_Obj()->get_saved_exports($post_type);
					foreach ($saved_exports as $index => $saved_export) {
						$editor->args['toolbars']->register_item('save_export' . $index, array(
							'type' => 'button',
							'content' => esc_html($saved_export['name']),
							'toolbar_key' => 'secondary',
							'allow_in_frontend' => true,
							'parent' => 'export_csv',
							'extra_html_attributes' => 'data-saved-type="export" data-saved-item data-item-name="' . esc_attr($saved_export['name']) . '" data-start-saved-export="' . esc_attr(json_encode($saved_export)) . '"',
								), $post_type);
					}
				}


				$editor->args['toolbars']->register_item('share_export', array(
					'type' => 'button',
					'content' => __('Download CSV file', 'vg_sheet_editor' ),
					'extra_html_attributes' => 'data-remodal-target="export-csv-modal"',
					'toolbar_key' => 'primary',
					'allow_in_frontend' => false,
					'parent' => 'share'
						), $post_type);
				$editor->args['toolbars']->register_item('import_csv', array(
					'type' => 'button', // html | switch | button
					'content' => __('Import', 'vg_sheet_editor' ),
					'id' => 'import_csv',
					'allow_in_frontend' => true,
					'toolbar_key' => 'secondary',
					'extra_html_attributes' => 'data-remodal-target="import-csv-modal"',
					'footer_callback' => array($this, 'render_import_csv_modal')
						), $post_type);
			}
		}

		/**
		 * Enqueue metabox assets
		 * @global obj $post
		 * @param str $hook
		 */
		function enqueue_assets($hook = null) {
			wp_enqueue_script('vgse-universal-sheet-init', $this->plugin_url . 'assets/js/init.js', array('jquery'), VGSE()->version, false);
			wp_localize_script('vgse-universal-sheet-init', 'vgse_universal_sheet_data', array(
				'rest_nonce' => wp_create_nonce('wp_rest'),
				'rest_base_url' => rest_url(),
				'post_type' => VGSE()->helpers->get_provider_from_query_string(),
			));
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if (null == WP_Sheet_Editor_Universal_Sheet::$instance) {
				WP_Sheet_Editor_Universal_Sheet::$instance = new WP_Sheet_Editor_Universal_Sheet();
				WP_Sheet_Editor_Universal_Sheet::$instance->init();
			}
			return WP_Sheet_Editor_Universal_Sheet::$instance;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

}

add_action('plugins_loaded', 'vgse_universal_sheet', 99);

if (!function_exists('vgse_universal_sheet')) {

	/**
	 * @return WP_Sheet_Editor_Universal_Sheet
	 */
	function vgse_universal_sheet() {
		return WP_Sheet_Editor_Universal_Sheet::get_instance();
	}

}
