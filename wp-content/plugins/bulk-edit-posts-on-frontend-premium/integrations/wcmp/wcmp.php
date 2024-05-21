<?php
if (!class_exists('WPSE_WCMP')) {

	class WPSE_WCMP {

		static private $instance = false;

		private function __construct() {
			
		}

		function init() {
			if (!function_exists('get_wcmp_vendor') || !class_exists('WP_Sheet_Editor_Frontend_Editor')) {
				return;
			}
			add_action('before_wcmp_vendor_dash_product_list_page_header_action_btn', array($this, 'add_bulk_edit_button'));

			add_filter('vg_sheet_editor/load_rows/wp_query_args', array($this, 'modify_query_parameters'));
			add_action('vg_sheet_editor/frontend/metabox/after_fields', array($this, 'add_settings'));
			add_action('vg_sheet_editor/frontend/metabox/after_fields_saved', array($this, 'save_settings'));
		}

		function is_vendor_allowed_to_edit_products() {
			$user_id = get_current_user_id();
			$user = new WP_User($user_id);
			return $user_id && get_wcmp_vendor($user_id) && $user->has_cap('edit_products');
		}

		function modify_query_parameters($args) {
			global $WCMp;

			$current_user = wp_get_current_user();

			if ($args['post_type'] === 'product' && is_user_wcmp_vendor($current_user)) {
				$args['author'] = get_current_vendor_id();
				$term_id = get_user_meta($current_user->ID, '_vendor_term_id', true);
				$taxquery = array(
					array(
						'taxonomy' => $WCMp->taxonomy->taxonomy_name,
						'field' => 'term_id',
						'terms' => intval($term_id),
					)
				);

				if (!isset($args['tax_query'])) {
					$args['tax_query'] = array();
				}
				$args['tax_query'] = array_merge($args['tax_query'], $taxquery);

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
			<a href="<?php echo esc_url($frontend_url); ?>" class="btn btn-default"><i class="wcmp-font ico-import"></i><span><?php echo esc_html($title); ?><span></a>
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
						if (null == WPSE_WCMP::$instance) {
							WPSE_WCMP::$instance = new WPSE_WCMP();
							WPSE_WCMP::$instance->init();
						}
						return WPSE_WCMP::$instance;
					}

					function __set($name, $value) {
						$this->$name = $value;
					}

					function __get($name) {
						return $this->$name;
					}

				}

			}

			if (!function_exists('WPSE_WCMP_Obj')) {

				function WPSE_WCMP_Obj() {
					return WPSE_WCMP::get_instance();
				}

			}


			add_action('after_setup_theme', 'WPSE_WCMP_Obj');
			