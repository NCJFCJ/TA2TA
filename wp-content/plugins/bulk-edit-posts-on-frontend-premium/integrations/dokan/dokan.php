<?php
if (!class_exists('WPSE_Dokan')) {

	class WPSE_Dokan {

		static private $instance = false;

		private function __construct() {
			
		}

		function init() {
			if (!class_exists('WeDevs_Dokan') || !class_exists('WP_Sheet_Editor_Frontend_Editor')) {
				return;
			}
			add_filter('dokan_after_add_product_btn', array($this, 'add_bulk_edit_button'));
			add_filter('vg_sheet_editor/load_rows/wp_query_args', array($this, 'modify_query_parameters'));
			add_action('vg_sheet_editor/frontend/metabox/after_fields', array($this, 'add_dokan_settings'));
			add_action('vg_sheet_editor/frontend/metabox/after_fields_saved', array($this, 'save_settings'));
		}

		function modify_query_parameters($args) {
			$user_id = get_current_user_id();
			if ($args['post_type'] === 'product' && dokan_is_seller_enabled($user_id)) {
				$args['author'] = $user_id;
			}

			return $args;
		}

		function save_settings($post_id) {
			if (isset($_POST['vgse_dokan_menu_title'])) {
				update_post_meta($post_id, 'vgse_dokan_menu_title', sanitize_text_field($_POST['vgse_dokan_menu_title']));
			}
			if (isset($_POST['vgse_dokan_menu_position'])) {
				update_post_meta($post_id, 'vgse_dokan_menu_position', (int) $_POST['vgse_dokan_menu_position']);
			}
			if (isset($_POST['vgse_dokan_menu_icon'])) {
				update_post_meta($post_id, 'vgse_dokan_menu_icon', sanitize_text_field($_POST['vgse_dokan_menu_icon']));
			}
		}

		function add_dokan_settings($post) {
			include __DIR__ . '/views/metabox.php';
		}

		function add_bulk_edit_button() {

			$frontend_sheet_id = $this->get_sheet_id();
			if (!$frontend_sheet_id) {
				return;
			}
			$frontend_url = $this->get_sheet_url($frontend_sheet_id);
			$title = get_post_meta($frontend_sheet_id, 'vgse_dokan_menu_title', true);
			?>
			<a href="<?php echo esc_url($frontend_url); ?>" class="dokan-btn dokan-btn-theme">
				<i class="fa fa-<?php echo esc_attr(get_post_meta($frontend_sheet_id, 'vgse_dokan_menu_icon', true)); ?>">&nbsp;</i>
				<?php echo esc_html($title); ?>
			</a>
			<?php
		}

		function get_sheet_id() {

			$frontend_sheet = new WP_Query(array(
				'post_type' => VGSE_EDITORS_POST_TYPE,
				'posts_per_page' => 1,
				'meta_key' => 'vgse_dokan_menu_title',
				'meta_value' => '0',
				'meta_compare' => '>',
				'fields' => 'ids'
			));

			if (!$frontend_sheet->posts) {
				return;
			}
			$frontend_sheet_id = current($frontend_sheet->posts);
			return $frontend_sheet_id;
		}

		function get_sheet_url($frontend_sheet_id) {
			$frontend_url = get_permalink(vgse_frontend_editor()->get_frontend_page_id(array(
						'spreadsheet_id' => $frontend_sheet_id,
			)));
			return $frontend_url;
		}

		function add_help_menu($urls) {
			$frontend_sheet_id = $this->get_sheet_id();
			if (!$frontend_sheet_id) {
				return;
			}
			$frontend_url = $this->get_sheet_url($frontend_sheet_id);
			$position = (int) get_post_meta($frontend_sheet_id, 'vgse_dokan_menu_position', true);
			if (!$position) {
				return;
			}
			$urls['vgse-bulk-edit'] = array(
				'title' => get_post_meta($frontend_sheet_id, 'vgse_dokan_menu_title', true),
				'icon' => '<i class="fa fa-' . get_post_meta($frontend_sheet_id, 'vgse_dokan_menu_icon', true) . '"></i>',
				'url' => $frontend_url,
				'pos' => $position,
			);
			return $urls;
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if (null == WPSE_Dokan::$instance) {
				WPSE_Dokan::$instance = new WPSE_Dokan();
				WPSE_Dokan::$instance->init();
			}
			return WPSE_Dokan::$instance;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

}

if (!function_exists('WPSE_Dokan_Obj')) {

	function WPSE_Dokan_Obj() {
		return WPSE_Dokan::get_instance();
	}

}


add_action('after_setup_theme', 'WPSE_Dokan_Obj');
