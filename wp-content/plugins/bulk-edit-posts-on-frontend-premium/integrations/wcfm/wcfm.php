<?php
if (!class_exists('WPSE_WCFM')) {

	class WPSE_WCFM {

		static private $instance = false;

		private function __construct() {
			
		}

		function init() {
			if (!class_exists('WCFM') || !class_exists('WP_Sheet_Editor_Frontend_Editor')) {
				return;
			}
			add_action('wcfm_products_quick_actions', array($this, 'add_bulk_edit_button'));

			add_filter('vg_sheet_editor/load_rows/wp_query_args', array($this, 'modify_query_parameters'));
			add_action('vg_sheet_editor/frontend/metabox/after_fields', array($this, 'add_settings'));
			add_action('vg_sheet_editor/frontend/metabox/after_fields_saved', array($this, 'save_settings'));
		}

		function modify_query_parameters($args) {
			$current_user = wp_get_current_user();

			if ($args['post_type'] === 'product' && !current_user_can('manage_options')) {
				$vendor = get_current_user_id();

				if (!isset($args['tax_query'])) {
					$args['tax_query'] = array();
				}

				$is_marketplace = wcfm_is_marketplace();
				if ($is_marketplace) {
					if (!wcfm_is_vendor()) {
						if ($is_marketplace == 'wcpvendors') {
							$args['tax_query'][] = array(
								'taxonomy' => WC_PRODUCT_VENDORS_TAXONOMY,
								'field' => 'term_id',
								'terms' => $vendor,
							);
						} elseif ($is_marketplace == 'wcvendors') {
							$args['author'] = $vendor;
						} elseif ($is_marketplace == 'wcmarketplace') {
							$vendor_term = absint(get_user_meta($vendor, '_vendor_term_id', true));
							$args['tax_query'][] = array(
								'taxonomy' => 'dc_vendor_shop',
								'field' => 'term_id',
								'terms' => $vendor_term,
							);
						} elseif ($is_marketplace == 'dokan') {
							$args['author'] = $vendor;
						}
					}
				} else {
					$args['author'] = $vendor;
				}

				$args['post_status'][] = 'publish';
			}

			return $args;
		}

		function save_settings($post_id) {
			if (isset($_POST['vgse_wcmp_menu_title'])) {
				update_post_meta($post_id, 'vgse_wcmp_menu_title', sanitize_text_field($_POST['vgse_wcmp_menu_title']));
			}
		}

		function add_settings($post) {
			include __DIR__ . '/views/metabox.php';
		}

		function add_bulk_edit_button() {
			$frontend_sheet_id = $this->get_sheet_id();

			if (!$frontend_sheet_id) {
				return;
			}
			if (!apply_filters('vg_sheet_editor/frontend/can_vendor_see_editor', true)) {

				return;
			}


			$frontend_url = $this->get_sheet_url($frontend_sheet_id);
			$title = esc_html(get_post_meta($frontend_sheet_id, 'vgse_wcmp_menu_title', true));
			?>
			<a href="<?php echo esc_url($frontend_url); ?>" class="add_new_wcfm_ele_dashboard  sheet-editor text_tip"><span class="text"><?php echo esc_html($title); ?><span></a>
						<?php
					}

					function get_sheet_id() {

						$frontend_sheet = new WP_Query(array(
							'post_type' => VGSE_EDITORS_POST_TYPE,
							'posts_per_page' => 1,
							'meta_key' => 'vgse_wcmp_menu_title',
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

					/**
					 * Creates or returns an instance of this class.
					 */
					static function get_instance() {
						if (null == WPSE_WCFM::$instance) {
							WPSE_WCFM::$instance = new WPSE_WCFM();
							WPSE_WCFM::$instance->init();
						}
						return WPSE_WCFM::$instance;
					}

					function __set($name, $value) {
						$this->$name = $value;
					}

					function __get($name) {
						return $this->$name;
					}

				}

			}

			if (!function_exists('WPSE_WCFM_Obj')) {

				function WPSE_WCFM_Obj() {
					return WPSE_WCFM::get_instance();
				}

			}


			add_action('after_setup_theme', 'WPSE_WCFM_Obj');
			