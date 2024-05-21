<?php defined( 'ABSPATH' ) || exit;
if (!class_exists('WP_Sheet_Editor_WooCommerce_Variations')) {

	/**
	 * Display woocommerce item in the toolbar to tease users of the free 
	 * version into purchasing the premium plugin.
	 */
	class WP_Sheet_Editor_WooCommerce_Variations {

		static private $instance = false;
		var $post_type = null;
		var $variation_post_type = 'product_variation';
		var $wc_variation_columns = null;
		var $posts_to_inject_query = null;
		var $parent_attribute_terms = array();

		private function __construct() {
			
		}

		/**
		 * Register toolbar item
		 */
		function register_toolbar_items($editor) {

			$editor->args['toolbars']->register_item('create_variation', array(
				'type' => 'button', // html | switch | button
				'content' => __('Create variations', 'vg_sheet_editor' ),
				'id' => 'create-variation',
				'help_tooltip' => __('Create and copy variations for variable products.', 'vg_sheet_editor' ),
				'extra_html_attributes' => 'data-remodal-target="create-variation-modal"',
				'css_class' => 'wpse-disable-if-unsaved-changes',
				'footer_callback' => array($this, 'render_create_variation_modal')
					), $this->post_type);

			$editor->args['toolbars']->register_item('display_variations', array(
				'type' => 'switch', // html | switch | button
				'content' => __('Display variations', 'vg_sheet_editor' ),
				'id' => 'display-variations',
				'help_tooltip' => __('When this is enabled the products variations will be displayed and you will be able to edit them.', 'vg_sheet_editor' ),
				'default_value' => false
					), $this->post_type);
		}

		/**
		 * Add a lock icon to the cells enabled for variations or products.
		 * 
		 * @param array $posts Rows for display in spreadsheet
		 * @param array $wp_query Arguments used to query the posts.
		 * @param array $spreadsheet_columns
		 * @param array $request_data Data received in the ajax request
		 * @return array
		 */
		function maybe_lock_general_columns_in_variations($posts, $wp_query, $spreadsheet_columns) {
			if (VGSE()->helpers->get_provider_from_query_string() !== $this->post_type || empty($posts) || !is_array($posts) || VGSE()->helpers->is_plain_text_request()) {
				return $posts;
			}
			if (function_exists('WPSE_Profiler_Obj')) {
				WPSE_Profiler_Obj()->record('Before ' . __FUNCTION__);
			}

			$products = wp_list_filter($posts, array(
				'post_type' => $this->post_type
			));
			// We need at least one parent product to detect the parent vs variations columns and lock them
			if (empty($products)) {
				return $posts;
			}
			$first_product_keys = array_keys(current($products));

			$whitelist_variations = $this->get_variation_whitelisted_columns();
			$columns_with_visibility = array_keys($spreadsheet_columns);

			// Lock keys on variation rows for fields used in parent products that are not used in variations
			$locked_keys_in_variations = array_intersect(array_diff($first_product_keys, $whitelist_variations), $columns_with_visibility);

			// Lock keys on parent rows for fields used in variations that are not used by parent products
			$locked_keys_in_general = array_intersect(array_diff($whitelist_variations, $first_product_keys), $columns_with_visibility);

			$locked_keys_in_variations = apply_filters('vg_sheet_editor/woocommerce/locked_keys_in_variations', $locked_keys_in_variations, $whitelist_variations);
			$lock_icon = '<i class="fa fa-lock vg-cell-blocked vg-variation-lock"></i>';

			foreach ($posts as $index => $post) {


				if ($post['post_type'] === $this->post_type) {
					$locked_keys = $locked_keys_in_general;
				} else {
					$locked_keys = $locked_keys_in_variations;
				}
				if (isset($posts[$index]['_stock'])) {
					$posts[$index]['_stock'] = (int) $posts[$index]['_stock'];
				}
				$product_type = !empty($post['product_type']) ? $post['product_type'] : VGSE()->WC->get_product_type($post['ID']);
				// We are locking keys here because the automatic locking works with fields 
				// used by all parent products or all variations, not fields used by some parents only.
				// That's why in this case, we need to check the product type and disable them manually
				if ($product_type === 'variable') {
					$locked_keys[] = '_regular_price';
					$locked_keys[] = '_sale_price';
					$locked_keys[] = '_sale_price_dates_from';
					$locked_keys[] = '_sale_price_dates_to';
				}
				$posts[$index] = array_merge($posts[$index], array_fill_keys(array_diff($locked_keys, array_keys($post)), ''));
				foreach ($locked_keys as $locked_key) {

					if (strpos($posts[$index][$locked_key], 'vg-cell-blocked') !== false) {
						continue;
					}
					if (in_array($locked_key, array('title', 'post_title', 'post_name'))) {
						$posts[$index][$locked_key] = $lock_icon . ' ' . $posts[$index][$locked_key];
					} else {
						$posts[$index][$locked_key] = $lock_icon;
					}
				}
			}

			if (function_exists('WPSE_Profiler_Obj')) {
				WPSE_Profiler_Obj()->record('After ' . __FUNCTION__);
			}
			return $posts;
		}

		/**
		 * Are variations enabled in the spreadsheet according to the request data?
		 * @param str $post_type
		 * @param array $request_data Data received in the ajax request
		 * @return boolean
		 */
		function variations_enabled($post_type = null, $request_data = null) {
			if (!$post_type) {
				$post_type = VGSE()->helpers->get_provider_from_query_string();
			}
			if ($post_type !== $this->post_type) {
				return false;
			}
			if (empty($request_data) || empty($request_data['filters'])) {
				$filters = WP_Sheet_Editor_Filters::get_instance()->get_raw_filters();
			} elseif (!empty($request_data['filters'])) {
				$filters = $request_data['filters'];
			} else {
				return false;
			}

			if (empty($filters['wc_display_variations'])) {
				return false;
			}

			return true;
		}

		/**
		 * Include variations posts to the posts list before processing.
		 * 
		 * Note. The search variations logic is very good because it allows pagination by variations
		 * but we can't use it without searching because it would exclude the non-variable products.
		 * 
		 * @param type $posts
		 * @param type $wp_query
		 * @param array $request_data Data received in the ajax request
		 * @return array
		 */
		function maybe_include_variations_posts($posts, $wp_query, $request_data) {

			if (!$this->variations_enabled(null, $request_data) || empty($posts) || !is_array($posts)) {
				return $posts;
			}

			// If this is a variations search, the main query contains variations 
			// and we need to include the parent products
			if (!empty($wp_query['wpse_search_variations'])) {
				unset($wp_query['wpse_search_variations']);
				// We merge args with $wp_query to use the special search parameters
				$posts_to_inject_query = new WP_Query(array(
					'post_type' => $this->post_type,
					'nopaging' => true,
					'post_status' => 'any',
					'post__in' => array_unique(wp_list_pluck($posts, 'post_parent')),
				));

				if (!$posts_to_inject_query->have_posts()) {
					return $posts;
				}

				// Cache list of variations for future use
				$this->posts_to_inject_query = $posts_to_inject_query;

				$new_posts = array();

				foreach ($posts_to_inject_query->posts as $post) {
					$new_posts[] = $post;

					$product_variations = wp_list_filter($posts, array(
						'post_parent' => $post->ID
					));

					$new_posts = array_merge($new_posts, $product_variations);
				}
			} else {
				// We merge args with $wp_query to use the special search parameters
				$posts_to_inject_query = new WP_Query(array(
					'post_type' => 'product_variation',
					'nopaging' => true,
					'post_parent__in' => wp_list_pluck($posts, 'ID'),
					'orderby' => array('menu_order' => 'ASC', 'ID' => 'ASC'),
				));

				if (!$posts_to_inject_query->have_posts()) {
					return $posts;
				}

				// Cache list of variations for future use
				$this->posts_to_inject_query = $posts_to_inject_query;

				$new_posts = array();
				$wc_default_non_variable_types = array('simple', 'grouped', 'external');

				foreach ($posts as $post) {
					$new_posts[] = $post;

					if (in_array(VGSE()->WC->get_product_type($post->ID), $wc_default_non_variable_types, true)) {
						continue;
					}

					$product_variations = wp_list_filter($posts_to_inject_query->posts, array(
						'post_parent' => $post->ID
					));

					$new_posts = array_merge($new_posts, $product_variations);
				}
			}
			return $new_posts;
		}

		function init() {
			$this->post_type = apply_filters('vg_sheet_editor/woocommerce/product_post_type_key', 'product');

			$this->wc_variation_only_columns = array(
				'_vgse_variation_enabled',
				'_variation_description',
			);

			// We need to set the properties
			$this->get_variation_whitelisted_columns();

			// Register toolbar button to enable the display of variations and create variations
			add_action('vg_sheet_editor/editor/before_init', array(
				$this,
				'register_toolbar_items'
			));

			// Filter load_rows to include variations if toolbar item is enabled.
			// The general fields will contain the same info as the parent post.
			add_action('vg_sheet_editor/load_rows/output', array(
				$this,
				'maybe_modify_variations_output'
					), 10, 3);

			// Filter load_rows to preload variations custom data
			add_action('vg_sheet_editor/load_rows/found_posts', array(
				$this,
				'maybe_include_variations_posts'
					), 10, 3);

			// Filter load_rows output to remove data in general columns and display a lock icon instead, also modify some columns values
			add_action('vg_sheet_editor/load_rows/output', array(
				$this,
				'maybe_lock_general_columns_in_variations'
					), 10, 3);

			// Exclude variations from the saving list
			add_action('vg_sheet_editor/save_rows/incoming_data', array(
				$this,
				'exclude_variations_from_saving_list'
					), 10, 2);

			// Save variations
			add_action('vg_sheet_editor/save_rows/response', array(
				$this,
				'maybe_save_variations'
					), 10, 5);

			// Create variations via ajax
			add_action('wp_ajax_vgse_create_variations', array(
				$this,
				'create_variations_rows'
			));
			add_action('wp_ajax_vgse_process_copy_variations_queue', array(
				$this,
				'process_copy_variations_queue'
			));

			// Save default attributes via ajax
			add_action('wp_ajax_vgse_save_default_attributes', array(
				$this,
				'update_default_attributes'
			));

			// When loading posts, disable product columns in variations
			add_action('vg_sheet_editor/load_rows/allowed_post_columns', array(
				$this,
				'disable_general_columns_for_variations'
					), 10, 2);

			// When we create the products in the spreadsheet, the variations will be enabled automatically
			// so we display the new rows with the right cells disabled
			add_action('vg_sheet_editor/add_new_posts/get_rows_args', array(
				$this,
				'enable_variations_when_fetching_created_rows'
					), 10, 2);

			add_action('woocommerce_rest_insert_product_variation_object', array($this, 'add_variation_meta_after_copy'), 10, 2);
			add_filter('vg_sheet_editor/provider/post/get_items_terms', array($this, 'get_variation_attributes'), 10, 3);

			add_filter('vg_sheet_editor/filters/after_fields', array($this, 'add_search_on_variations_field'), 10, 2);
			add_filter('vg_sheet_editor/load_rows/wp_query_args', array($this, 'search_on_variations_query'), 20, 2);
			add_filter('posts_clauses', array($this, 'search_on_variations_query_sql'), 10, 2);
			add_filter('vg_sheet_editor/handsontable_cell_content/existing_value', array($this, 'get_default_attributes_for_cell'), 10, 3);
			add_filter('vg_sheet_editor/save_rows/row_data_before_save', array($this, 'save_default_attributes_from_cell'), 10, 3);
			add_filter('vg_sheet_editor/custom_columns/all_meta_keys', array($this, 'disable_default_attributes_from_custom_columns'), 10, 2);
			add_filter('vg_sheet_editor/custom_columns/all_meta_keys', array($this, 'register_custom_meta_columns'), 10, 3);
			add_action('woocommerce_variable_product_before_variations', array($this, 'render_variations_metabox_quick_access'));

			add_filter('vg_sheet_editor/bootstrap/post_type_column_dropdown_options', array($this, 'add_variation_to_post_types_dropdown'), 10, 2);
			add_action('wp_ajax_vgse_get_product_variations', array($this, 'get_product_variations'));
			// Force WC to generate variation titles with all attributes, even when having a lot of attributes
			// because the spreadsheet needs it for "delete duplicates" based on title and some search functionality
			add_filter('woocommerce_product_variation_title_include_attributes', '__return_true', 99999);
			add_action('vg_sheet_editor/editor/register_columns', array($this, 'register_columns'));
			add_filter('vg_sheet_editor/advanced_filters/all_meta_keys', array($this, 'add_variation_attribute_fields_to_search'), 10, 2);
			add_filter('vg_sheet_editor/custom_columns/columns_detected_settings_before_cache', array($this, 'maybe_allow_serialized_columns_for_variations'), 10, 2);
			add_filter('vg_sheet_editor/columns_manager/column_options', array($this, 'register_column_option_allow_for_variations'), 10, 3);
			add_action('vg_sheet_editor/columns_visibility/after_options_saved', array($this, 'save_columns_manager_options'), 11);
			add_action('vg_sheet_editor/formulas/after_form_fields', array($this, 'allow_to_run_formula_on_variations_directly'));
			add_action('vg_sheet_editor/save_rows/before_saving_cell', array($this, 'save_bulk_edit_of_global_attribute_with_proper_format'), 10, 6);
			add_filter('vg_sheet_editor/custom_columns/post_type_for_sample_values', array($this, 'include_variation_post_type_for_custom_column_samples'), 10, 3);
			add_filter('vg_sheet_editor/filters/sanitize_request_filters', array($this, 'register_custom_filters'), 10, 2);
		}

		function include_variation_post_type_for_custom_column_samples($post_types_for_sample_values, $meta_keys, $editor) {
			$post_type = $editor->args['provider'];
			if ($post_type === 'product' && array_intersect($meta_keys, $this->get_variation_meta_keys())) {
				$post_types_for_sample_values[] = $this->variation_post_type;
			}
			return $post_types_for_sample_values;
		}

		function save_bulk_edit_of_global_attribute_with_proper_format($row, $post_type, $cell_args, $cell_key, $spreadsheet_columns, $post_id) {
			if (!doing_action('wp_ajax_vgse_bulk_edit_formula_big') || $post_type !== 'product' || strpos($cell_key, 'pa_') === false || get_post_type($post_id) !== 'product_variation') {
				return;
			}
			$this->_save_variation_global_attribute($post_id, $cell_key, $row[$cell_key]);
		}

		function allow_to_run_formula_on_variations_directly($post_type) {
			if ($post_type !== $this->post_type) {
				return;
			}
			?>
			<li class="run-on-variations-field">
				<select name="wpse_run_on_variations">
					<option value=""><?php _e('Edit only parent products', 'vg_sheet_editor' ); ?></option>
					<option value="on"><?php _e('Edit only variations', 'vg_sheet_editor' ); ?></option>
				</select>		
			</li>
			<?php
		}

		function _get_manually_whitelisted_columns() {
			$existing_settings = get_option(VGSE()->options_key);
			if (!isset($existing_settings['wc_variation_whitelisted_columns'])) {
				$existing_settings['wc_variation_whitelisted_columns'] = '';
			}

			$whitelisted_columns = array_map(array(VGSE()->helpers, 'sanitize_table_key'), array_map('trim', explode(',', $existing_settings['wc_variation_whitelisted_columns'])));
			return $whitelisted_columns;
		}

		function save_columns_manager_options($post_type) {
			if (!isset($_POST['column_settings']) || !VGSE()->helpers->user_can_manage_options() || $post_type !== $this->post_type) {
				return;
			}
			$whitelisted_columns = $this->_get_manually_whitelisted_columns();

			foreach ($_POST['column_settings'] as $column_key => $column_settings) {
				if (isset($column_settings['allow_for_variations']) && $column_settings['allow_for_variations'] === 'yes') {
					$whitelisted_columns[] = sanitize_text_field($column_key);
				} else {
					$index = array_search(sanitize_text_field($column_key), $whitelisted_columns);
					if ($index !== false && isset($whitelisted_columns[$index])) {
						unset($whitelisted_columns[$index]);
					}
				}
			}
			VGSE()->update_option('wc_variation_whitelisted_columns', implode(',', array_unique(array_filter($whitelisted_columns))));
		}

		function register_column_option_allow_for_variations($column_options, $column, $post_type) {
			if ($post_type == $this->post_type) {
				$column_options['allow_for_variations'] = array($this, 'render_field_to_allow_columns_for_variations');
			}
			return $column_options;
		}

		function render_field_to_allow_columns_for_variations($option_key, $column, $post_type, $column_settings) {
			$whitelisted_columns = $this->_get_manually_whitelisted_columns();
			?>

			<div class="column-settings-field">					
				<label><input value="yes" <?php checked(in_array($column['key'], $whitelisted_columns)); ?> type="checkbox" name="column_settings[<?php echo esc_attr($column['key']); ?>][allow_for_variations]"> <?php _e('Allow to edit this column on variation rows?', 'vg_sheet_editor' ); ?></label>
				<p><?php _e('We automatically allow columns for variations if at least one variation is using that field.', 'vg_sheet_editor' ); ?></p>				
			</div>
			<?php
		}

		function maybe_allow_serialized_columns_for_variations($columns_detected, $post_type) {

			if ($post_type === $this->post_type) {
				foreach ($columns_detected as $group => $columns) {
					if ($group === 'normal') {
						continue;
					}
					foreach ($columns as $column_key => $column_settings) {
						if (in_array($column_key, $this->get_variation_meta_keys(), true)) {
							$columns_detected[$group][$column_key]['allow_in_wc_product_variations'] = true;
						}
					}
				}
			}

			return $columns_detected;
		}

		function add_variation_attribute_fields_to_search($keys, $post_type) {
			global $wpdb;
			if ($post_type !== $this->post_type) {
				return $keys;
			}

			$keys = array_merge($keys, $wpdb->get_col("SELECT meta_key FROM $wpdb->postmeta WHERE meta_key LIKE 'attribute_%' AND meta_value <> '' GROUP BY meta_key LIMIT 100"));

			return $keys;
		}

		/**
		 * Register spreadsheet columns
		 */
		function register_columns($editor) {
			$post_type = $this->post_type;

			if (!in_array($post_type, $editor->args['enabled_post_types'])) {
				return;
			}
			$editor->args['columns']->register_item('_variation_description', $post_type, array(
				'key' => '_variation_description',
				'data_type' => 'post_meta',
				'column_width' => 175,
				'title' => __('Variation description', 'woocommerce'),
				'supports_formulas' => true,
				'formatted' => array(
					'data' => '_variation_description',
				),
				'default_value' => '',
			));
			$editor->args['columns']->register_item('_vgse_variation_enabled', $post_type, array(
				'key' => '_vgse_variation_enabled',
				'data_type' => 'post_data',
				'column_width' => 140,
				'title' => __('Variation enabled?', 'woocommerce'),
				'supports_formulas' => true,
				'supports_sql_formulas' => false,
				'formatted' => array(
					'data' => '_vgse_variation_enabled',
					'type' => 'checkbox',
					'checkedTemplate' => 'on',
					'uncheckedTemplate' => 'off'
				),
				'default_value' => 'on',
				'save_value_callback' => array($this, 'save_variation_enabled_from_cell'),
			));

			$editor->args['columns']->register_item('_default_attributes', $post_type, array(
				'data_type' => 'post_meta',
				'unformatted' => array('data' => '_default_attributes', 'renderer' => 'html'),
				'column_width' => 210,
				'title' => __('Default attributes', 'woocommerce'),
				'type' => 'handsontable',
				'edit_button_label' => __('Default attributes', 'woocommerce'),
				'edit_modal_id' => 'vgse-default-attributes',
				'edit_modal_title' => __('Default attributes', 'woocommerce'),
				'edit_modal_description' => sprintf(__('Attributes appear as dropdowns in the product page where the user can select the variation colors, sizes, and any attribute. Here you can define the default options selected in the dropdowns.<br>Separate values with the character %s<br/>This only works for Variable Products and it must have variations, otherwise the default attributes won\'t be saved.</span>', 'woocommerce'), WC_DELIMITER),
				'edit_modal_save_action' => 'vgse_save_default_attributes',
				'edit_modal_get_action' => 'vgse_save_default_attributes',
				'edit_modal_local_cache' => false,
				'handsontable_columns' => array(
					$this->post_type => array(
						array(
							'data' => 'name'
						),
						array(
							'data' => 'option'
						),
					)),
				'handsontable_column_names' => array(
					$this->post_type => array(
						__('Name', 'woocommerce'),
						__('Value', 'woocommerce')
					)
				),
				'handsontable_column_widths' => array(
					$this->post_type => array(160, 300),
				),
				'supports_formulas' => true,
				'forced_supports_formulas' => true,
				'supported_formula_types' => array('clear_value', 'wc_default_attribute_replace' ),
				'key_for_formulas' => '_default_attributes',
				'formatted' => array('data' => '_default_attributes', 'renderer' => 'html'),
				'use_new_handsontable_renderer' => true
			));
		}

		function save_variation_enabled_from_cell($post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns) {
			$post = get_post($post_id);
			$api_response = VGSE()->helpers->create_rest_request('POST', '/wc/v3/products/' . $post->post_parent . '/variations/' . $post_id, array(
				'status' => $data_to_save === 'on' ? 'publish' : 'private',
				'wpse_source' => 'save_variation_enabled_cell'
			));
		}

		function add_variation_to_post_types_dropdown($post_types, $post_type) {
			if ($post_type === $this->post_type && !isset($post_types[$this->variation_post_type])) {
				$post_types[$this->variation_post_type] = $this->variation_post_type;
			}
			return $post_types;
		}

		function register_custom_filters($sanitized_filters, $dirty_filters) {

			if (isset($dirty_filters['search_variations'])) {
				$sanitized_filters['search_variations'] = sanitize_text_field($dirty_filters['search_variations']);
			}
			if (isset($dirty_filters['wc_display_variations'])) {
				$sanitized_filters['wc_display_variations'] = sanitize_text_field($dirty_filters['wc_display_variations']);
			}
			return $sanitized_filters;
		}

		function render_variations_metabox_quick_access() {
			global $post;
			$spreadsheet_url = esc_url(add_query_arg(array(
				'wpse_custom_filters' => array(
					'keyword' => $post->ID,
					'search_variations' => 'yes',
					'wc_display_variations' => 'yes',
				)
							), VGSE()->helpers->get_editor_url('product')));
			include VGSE_WC_DIR . '/views/variation-metabox-shortcut.php';
		}

		function register_custom_meta_columns($meta_keys, $post_type, $editor) {

			if ($post_type !== $this->post_type) {
				return $meta_keys;
			}
			$variation_meta_keys = $this->get_variation_meta_keys();
			$meta_keys = array_unique(array_merge($meta_keys, $variation_meta_keys));
			return $meta_keys;
		}

		function disable_default_attributes_from_custom_columns($meta_keys, $post_type) {

			if ($post_type !== $this->post_type) {
				return $meta_keys;
			}

			$position = array_search('_default_attributes', $meta_keys);
			if ($position !== false) {
				unset($meta_keys[$position]);
			}
			return $meta_keys;
		}

		function save_default_attributes_from_cell($item, $post_id, $post_type) {
			if (is_wp_error($item) || !isset($item['_default_attributes']) || $post_type !== $this->post_type) {
				return $item;
			}
			$this->save_default_attributes(json_decode(wp_unslash($item['_default_attributes']), true), $post_id);
			unset($item['_default_attributes']);
			return $item;
		}

		function get_default_attributes_for_cell($value, $post, $column_key) {
			if ($column_key !== '_default_attributes' || empty($post->post_type) || $post->post_type !== $this->post_type) {
				return $value;
			}

			// Previously we used the WC REST API to get the default attributes but it was too slow

			$raw_default = maybe_unserialize(get_post_meta($post->ID, '_default_attributes', true));
			if (empty($raw_default) || !is_array($raw_default)) {
				$raw_default = array();
			}

			$raw_default = (array) $raw_default;
			$default = array();
			foreach ($raw_default as $key => $value) {
				if (empty($value) || !is_string($value)) {
					continue;
				}
				if (0 === strpos($key, 'pa_')) {
					$default[] = array(
						'id' => wc_attribute_taxonomy_id_by_name($key),
						'name' => $this->get_attribute_taxonomy_label($key),
						'option' => $value,
					);
				} else {
					$default[] = array(
						'id' => 0,
						'name' => wc_attribute_taxonomy_slug($key),
						'option' => $value,
					);
				}
			}

			return $default;
		}

		function get_attribute_taxonomy_label($name) {
			$tax = get_taxonomy($name);
			if( $tax ){
				$labels = get_taxonomy_labels($tax);

				$out = $labels->singular_name;
			} else {
				$out = $name;
			}
			return $out;
		}

		function add_variation_meta_after_copy($object, $data) {
			$variation_id = $object->get_id();
			if (empty($data['wpse_custom_meta'])) {
				return;
			}

			$existing_meta_keys = get_post_meta($variation_id);
			$overwrite_meta_keys = array('_variation_description');
			foreach ($data['wpse_custom_meta'] as $meta_key => $meta_values) {
				if (isset($existing_meta_keys[$meta_key]) && !in_array($meta_key, $overwrite_meta_keys)) {
					continue;
				}
				foreach ($meta_values as $value) {
					// Unserialize before saving to prevent double serialization done by WP
					$value = maybe_unserialize($value);
					if (in_array($meta_key, $overwrite_meta_keys)) {
						update_post_meta($variation_id, $meta_key, $value);
					} else {
						add_post_meta($variation_id, $meta_key, $value);
					}
				}
			}
		}

		function search_on_variations_query_sql($clauses, $wp_query) {
			global $wpdb;

			if (!empty($wp_query->query['wpse_search_variations'])) {

				$wp_query->query['wpse_product_query_vars']['post_parent__in'] = array(PHP_INT_MAX);
				$parent_query = new WP_Query($wp_query->query['wpse_product_query_vars']);
				// Fix. WP 6.0 added new lines to SQL queries to make them more readable, which breaks our str_replace
				$parent_sql_query = preg_replace("/\t/", ' ', preg_replace("/\n+/", '', preg_replace("/(\t)+/", "$1", $parent_query->request)));
				$parent_sql_query = str_replace(array("AND $wpdb->posts.post_parent IN (" . PHP_INT_MAX . ")", "$wpdb->posts.ID FROM"), array('', "$wpdb->posts.ID, $wpdb->posts.post_date FROM"), $parent_sql_query);

				$clauses['orderby'] = " parent.post_date DESC, " . $clauses['orderby'];
				$clauses['join'] .= " INNER JOIN ($parent_sql_query) as parent  ON parent.ID = $wpdb->posts.post_parent ";
			}
			return $clauses;
		}

		/**
		 * Apply filters to wp-query args
		 * @param array $query
		 * @param array $data
		 * @return array
		 */
		function search_on_variations_query($query, $data) {
			$filters = WP_Sheet_Editor_Filters::get_instance()->get_raw_filters($data);
			if (empty($data['filters']) ||
					empty($filters['search_variations']) ||
					!isset($query['wpse_source']) ||
					!in_array($query['wpse_source'], array('load_rows', 'formulas'))) {
				return $query;
			}


			$query['wpse_search_variations'] = 1;
			$query['wpse_product_query_vars'] = $query;
			$query['post_type'] = $this->variation_post_type;
			$query['orderby'] = array('menu_order' => 'ASC', 'ID' => 'ASC');

			$query['wpse_product_query_vars']['fields'] = 'ids';
			$query['wpse_product_query_vars']['nopaging'] = true;

			$product_vars = array('post_status', 'tax_query', 'date_query', 'wpse_contains_keyword', 'wpse_not_contains_keyword');
			$variation_vars = array('post__in', 'meta_query', 'wpse_search_variations');

			if (!empty($filters['search_variations'])) {
				if (isset($query['tax_query'])) {
					if (!isset($query['meta_query'])) {
						$query['meta_query'] = array(
							'relation' => 'AND'
						);
					}

					foreach ($query['tax_query'] as $tax_query) {
						if (!isset($tax_query['taxonomy']) || strpos($tax_query['taxonomy'], 'pa_') !== 0) {
							continue;
						}

						$query['meta_query'][] = array(
							'key' => 'attribute_' . $tax_query['taxonomy'],
							'value' => $tax_query['terms'],
						);
					}
				}

				foreach ($product_vars as $product_var) {
					if (!empty($query[$product_var])) {
						unset($query[$product_var]);
					}
				}
				foreach ($variation_vars as $variation_var) {
					if (!empty($query['wpse_product_query_vars'][$variation_var])) {
						unset($query['wpse_product_query_vars'][$variation_var]);
					}
					if (!empty($query['wpse_product_query_vars']['wpse_original_filters']) && !empty($query['wpse_product_query_vars']['wpse_original_filters'][$variation_var])) {
						unset($query['wpse_product_query_vars']['wpse_original_filters'][$variation_var]);
					}
				}

				// We removed the meta_query from the product query and original filters above
				// Here we take the original_filters from the variation query and we 
				// will go through each meta query
				// The meta queries for attribute taxonomies will be added to the $query 
				// of variations and converted to meta search
				// and the meta queries for taxonomies that are not attributes are added 
				// to the $query of products and removed from the $query of variations
				// This way we can use the advanced filters freely. All meta filters applied to variations,
				// all taxonomies (except attributes) to parents, and attributes to variations

				if (!empty($query['wpse_original_filters']) && !empty($query['wpse_original_filters']['meta_query'])) {
					if (!isset($query['wpse_product_query_vars']['wpse_original_filters']['meta_query'])) {
						$query['wpse_product_query_vars']['wpse_original_filters']['meta_query'] = array();
					}
					foreach ($query['wpse_original_filters']['meta_query'] as $index => $meta_query) {
						$add_to_product_query = false;
						if ($meta_query['source'] === 'taxonomy_keys') {
							if (strpos($meta_query['key'], 'pa_') === 0) {
								$query['wpse_original_filters']['meta_query'][$index]['source'] = 'meta';
								$query['wpse_original_filters']['meta_query'][$index]['key'] = 'attribute_' . $meta_query['key'];
							} else {
								$add_to_product_query = true;
							}
						} elseif ($meta_query['source'] === 'post_data' && in_array($meta_query['key'], array('post_title'), true)) {
							$add_to_product_query = true;
						}
						if ($add_to_product_query) {
							$query['wpse_product_query_vars']['wpse_original_filters']['meta_query'][] = $meta_query;
							unset($query['wpse_original_filters']['meta_query'][$index]);
						}
					}
					$query['meta_query'] = vgse_advanced_filters_init()->_parse_meta_query_args($query['wpse_original_filters']['meta_query']);
				}
			}

			return $query;
		}

		function add_search_on_variations_field($post_type) {
			if ($post_type !== $this->post_type) {
				return;
			}
			include VGSE_WC_DIR . '/views/spreadsheet-search-on-variation.php';
		}

		function get_variation_whitelisted_columns() {
			$this->wc_variation_columns = array(
				'_vgse_variation_enabled',
				'ID',
				'post_type',
				'post_status',
				'_sku',
				'_regular_price',
				'_sale_price',
				'_sale_price_dates_from',
				'_sale_price_dates_to',
				'_downloadable',
				'_virtual',
				'_downloadable_files',
				'wpse_downloadable_file_names',
				'wpse_downloadable_file_urls',
				'_download_expiry',
				'_download_limit',
				'_tax_status',
				'_tax_class',
				'_manage_stock',
				'_stock_status',
				'_stock',
				'_backorders',
				'product_shipping_class',
				'_variation_description',
				'_thumbnail_id',
				'_vgse_create_attribute',
				'_weight',
				'_width',
				'_height',
				'_length',
				'post_parent',
				'menu_order'
			);
			$this->wc_core_variation_columns = array_diff($this->wc_variation_columns, array('post_type', 'post_parent', 'wpse_downloadable_file_names', 'wpse_downloadable_file_urls'));

			// We enable the global attribute and custom meta columns for variations too
			$this->wc_variation_columns = array_unique(array_merge($this->wc_variation_columns, wc_get_attribute_taxonomy_names(), $this->get_variation_meta_keys()));

			// Allow columns automatically when the column was registered with allow_for_variations=true
			$post_type = VGSE()->helpers->get_provider_from_query_string();
			if ($post_type === $this->post_type) {
				$spreadsheet_columns = wp_list_filter(VGSE()->helpers->get_provider_columns($post_type), array('allow_for_variations' => true));
				$this->wc_variation_columns = array_unique(array_merge($this->wc_variation_columns, array_keys($spreadsheet_columns)));
			}
			$manually_whitelisted = $this->_get_manually_whitelisted_columns();
			if ($manually_whitelisted) {
				$this->wc_variation_columns = array_unique(array_merge($this->wc_variation_columns, $manually_whitelisted));
			}

			return apply_filters('vg_sheet_editor/woocommerce/variation_columns', $this->wc_variation_columns);
		}

		function get_variation_meta_keys() {

			$transient_key = 'vgse_variation_meta_keys';
			$meta_keys = get_transient($transient_key);

			if (method_exists(VGSE()->helpers, 'can_rescan_db_fields') && VGSE()->helpers->can_rescan_db_fields($this->post_type)) {
				$meta_keys = false;
			}

			if (empty($meta_keys)) {
				$provider = VGSE()->helpers->get_current_provider();
				if (!is_object($provider) || $provider->key !== 'post') {
					return array();
				}
				$variation_meta_keys = array_diff(VGSE()->helpers->get_all_meta_keys($this->variation_post_type, 1000), array_keys(WP_Sheet_Editor_WooCommerce::get_instance()->core_to_woo_importer_columns_list));

				foreach ($variation_meta_keys as $index => $meta_key) {
					if (strpos($meta_key, 'attribute_') === 0) {
						unset($variation_meta_keys[$index]);
					}
				}
				$total_rows = (int) $provider->get_total($this->post_type);
				$meta_keys = $variation_meta_keys;
				set_transient($transient_key, $meta_keys, VGSE()->helpers->columns_cache_expiration($total_rows));
			}
			return apply_filters('vg_sheet_editor/woocommerce/variations/custom_meta_keys', $meta_keys);
		}

		function get_variation_attributes($terms, $id, $taxonomy) {
			if (get_post_type($id) !== $this->variation_post_type || strpos($taxonomy, 'pa_') === false) {
				return $terms;
			}


			$term_slug = VGSE()->helpers->get_current_provider()->get_item_meta($id, 'attribute_' . $taxonomy, true);

			if (!empty($term_slug) && $term = get_term_by('slug', $term_slug, $taxonomy)) {
				$terms = VGSE()->data_helpers->prepare_post_terms_for_display(array($term));
			}
			return $terms;
		}

		function enable_variations_when_fetching_created_rows($args) {
			if ($args['post_type'] !== $this->post_type) {
				return $args;
			}

			if (!isset($args['filters'])) {
				$args['filters'] = '';
			}
			$args['filters'] .= '&wc_display_variations=yes';
			return $args;
		}

		/**
		 * Modify variations fields before returning the spreadsheet rows.
		 * @param type $rows
		 * @param array $wp_query
		 * @param array $spreadsheet_columns
		 * @return array
		 */
		function maybe_modify_variations_output($rows, $wp_query, $spreadsheet_columns) {

			if (empty($rows) || !is_array($rows) || VGSE()->helpers->get_provider_from_query_string() !== $this->post_type) {
				return $rows;
			}

			if (function_exists('WPSE_Profiler_Obj')) {
				WPSE_Profiler_Obj()->record('before ' . __FUNCTION__);
			}

			$args = apply_filters('vg_sheet_editor/woocommerce/variations/modify_variation_output_args', array(
				'add_variation_title_prefix' => true,
					), $rows, $wp_query, $spreadsheet_columns);

			$parent_titles = array();
			foreach ($rows as $row_index => $post) {

				if (isset($post['_download_expiry']) && $post['_download_expiry'] === '-1') {
					$rows[$row_index]['_download_expiry'] = '';
				}
				if (isset($post['_download_limit']) && $post['_download_limit'] === '-1') {
					$rows[$row_index]['_download_limit'] = '';
				}

				if ($post['post_type'] !== $this->variation_post_type) {
					continue;
				}
				$post_obj = get_post($post['ID']);
				$rows[$row_index]['_vgse_variation_enabled'] = ($post_obj->post_status !== 'publish') ? 'off' : 'on';
				$rows[$row_index]['post_status'] = 'publish';

				// Set variation titles
				if ($args['add_variation_title_prefix']) {
					$rows[$row_index]['post_title'] = sprintf(__('Variation: %s', 'vg_sheet_editor' ), esc_html($post_obj->post_title));

					// WC doesn't add the attribute names to some variation titles, so we'll add them ourselves when loading the rows
					if (!isset($parent_titles[$post_obj->post_parent])) {
						$parent_titles[$post_obj->post_parent] = get_post_field('post_title', $post_obj->post_parent);
					}
					if ($post_obj->post_title === $parent_titles[$post_obj->post_parent]) {
						$rows[$row_index]['post_title'] .= ' - ' . wc_get_formatted_variation(wc_get_product($post['ID']), true, false);
					}
				} else {
					$rows[$row_index]['post_title'] = $post_obj->post_title;
				}
				if (!empty(VGSE()->options['allow_to_see_variation_url_slug'])) {
					$rows[$row_index]['post_name'] = $post_obj->post_name;
				}
			}

			if (function_exists('WPSE_Profiler_Obj')) {
				WPSE_Profiler_Obj()->record('After ' . __FUNCTION__);
			}
			return $rows;
		}

		/**
		 * Make sure that product variations dont have the columns exclusive to general products.
		 * @param array $columns
		 * @param obj $post
		 * @return array
		 */
		function disable_general_columns_for_variations($columns, $post) {

			if ($post->post_type !== $this->variation_post_type && $post->post_type !== $this->post_type) {
				return $columns;
			}

			if ($post->post_type === $this->variation_post_type) {
				$disallowed = array_diff(array_keys($columns), $this->get_variation_whitelisted_columns());
			} else {
				$disallowed = $this->wc_variation_only_columns;
			}

			$new_columns = array();

			foreach ($columns as $key => $column) {
				if (!in_array($key, $disallowed)) {
					$new_columns[$key] = $column;
				}
			}

			return $new_columns;
		}

		function _prepare_variations_data_for_copy($product_variations, $variations_to_copy, $data) {
			$variations = array();
			$placeholder_image_url = wc_placeholder_img_src();

			foreach ($product_variations as $variation) {
				if (!empty($variations_to_copy) && !in_array((int) $variation['id'], $variations_to_copy, true)) {
					continue;
				}
				// Save all meta data of the variation, we'll save it later using another hook
				// This allow copying meta data added by other plugins
				$variation['wpse_custom_meta'] = get_post_meta($variation['id']);
				$variation['wpse_custom_meta']['wpse_copied_from'] = array($variation['id']);

				// These fields should be auto generated by WC
				$fields_to_remove = array('id', 'date_created', 'date_modified', 'permalink', 'sku', 'price', 'meta_data');
				foreach ($fields_to_remove as $field_to_remove) {
					if (isset($variation[$field_to_remove])) {
						unset($variation[$field_to_remove]);
					}
				}

				// Remove all fields that inherit value from the parent to avoid error 400s
				// when the parent doesn't have the field value or has it with wrong format,
				// Let WC use the default.
				foreach ($variation as $field_key => $value) {
					if (is_string($value) && $value === 'parent') {
						unset($variation[$field_key]);
					}
				}

				// Prepare variation attributes for saving
				if (!empty($variation['attributes'])) {
					$variation['wpse_original_attributes'] = $variation['attributes'];
					foreach ($variation['attributes'] as $variation_attribute_index => $variation_attribute) {

						$attribute_name = wc_attribute_taxonomy_name_by_id($variation['attributes'][$variation_attribute_index]['id']);

						if ($variation['attributes'][$variation_attribute_index]['id']) {
							$variation['attributes'][$variation_attribute_index]['name'] = $attribute_name;
						}
						// If we remove the attribute ID, sometimes the variation attribute is not copied when the site is RTL
//						unset($variation['attributes'][$variation_attribute_index]['id']);
					}
				}
				if (!empty($variation['image'])) {
					$first_image = $variation['image'];

					// Ignore the variation image if the image is the blank placeholder, 
					// or if we selected the option ignore_variation_image
					if ($first_image['src'] === $placeholder_image_url || !empty($data['ignore_variation_image'])) {
						unset($variation['image']);

						if (isset($variation['wpse_custom_meta']['_thumbnail_id'])) {
							unset($variation['wpse_custom_meta']['_thumbnail_id']);
						}
					}
				}
				$variations[] = array_filter($variation);
			}
			return $variations;
		}

		function _get_parent_prices($product_id) {

			$target_product_type = VGSE()->WC->get_product_type($product_id);
			if ($target_product_type === 'simple') {
				$parent_regular_price = VGSE()->helpers->get_current_provider()->get_item_meta($product_id, '_regular_price', true);
				$parent_sale_price = VGSE()->helpers->get_current_provider()->get_item_meta($product_id, '_sale_price', true);
				VGSE()->helpers->get_current_provider()->update_item_meta($product_id, 'wpse_simple_regular_price', $parent_regular_price);
				VGSE()->helpers->get_current_provider()->update_item_meta($product_id, 'wpse_simple_sale_price', $parent_sale_price);
			} else {
				$parent_regular_price = VGSE()->helpers->get_current_provider()->get_item_meta($product_id, 'wpse_simple_regular_price', true);
				$parent_sale_price = VGSE()->helpers->get_current_provider()->get_item_meta($product_id, 'wpse_simple_sale_price', true);
			}
			$out = array(
				'regular_price' => $parent_regular_price,
				'sale_price' => $parent_sale_price,
			);
			return $out;
		}

		function _delete_product_variations($product_id) {
			global $wpdb;
			// Delete existing variations if we're copying all variations
			$existing_variations = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type = %s AND post_parent = %d", $this->variation_post_type, (int) $product_id));
			VGSE()->deleted_rows_ids = array_merge(VGSE()->deleted_rows_ids, $existing_variations);
			foreach ($existing_variations as $variation_id) {
				$variation = wc_get_product($variation_id);
				$variation->delete(true);
			}
		}

		function _index_attributes($attributes) {
			$out = array();
			foreach ($attributes as $attribute) {
				$attribute_key = $attribute['id'] > 0 ? wc_attribute_taxonomy_name_by_id($attribute['id']) : sanitize_title($attribute['name']);
				$out[$attribute_key] = $attribute;
			}
			return $out;
		}

		function copy_variations($data, $product_ids) {
			if (!function_exists('WPSE_Queues_Obj')) {
				wp_send_json_error(array('message' => __('Please update all the WP Sheet Editor plugins. This feature requires the latest version of all our plugins.', 'vg_sheet_editor' )));
			}

			$copy_from_product = VGSE()->helpers->_get_post_id_from_search($data['copy_from_product']);

			// We let the user select variation rows in the dropdown,
			// so we automatically switch to the parent
			$post = get_post($copy_from_product);
			if ($post->post_parent > 0) {
				$copy_from_product = $post->post_parent;
			}
			$api_response = VGSE()->helpers->create_rest_request('GET', '/wc/v3/products/' . $copy_from_product);
			$product_data = $api_response->get_data();

			if (empty($product_data['variations'])) {
				wp_send_json_error(array('message' => __('The source product doesn\'t have variations.', 'vg_sheet_editor' )));
			}
			$source_product_type = VGSE()->WC->get_product_type($copy_from_product);

			$variations_per_page = 100;
			$number_of_variation_pages = ceil((int) count($product_data['variations']) / $variations_per_page);
			$product_variations = array();

			for ($i = 1; $i < ($number_of_variation_pages + 1); $i++) {
				$product_variations_response = VGSE()->helpers->create_rest_request('GET', '/wc/v3/products/' . $copy_from_product . '/variations', array('per_page' => $variations_per_page, 'page' => $i));
				$product_variations = array_merge($product_variations, $product_variations_response->get_data());
			}

			// Reduce memory usage
			$product_variations_response = null;
			$api_response = null;

			$variations_to_copy = (!empty($data['copy_individual_variations']) && is_array($data['individual_variations']) ) ? array_filter(array_map('intval', $data['individual_variations'])) : array();
			$variations = $this->_prepare_variations_data_for_copy($product_variations, $variations_to_copy, $data);
			$only_copy_new_variations = !empty($data['only_copy_new_variations']);

			// Add index to source attributes so we can access them easily later
			$source_attributes = $this->_index_attributes($product_data['attributes']);
			$variations_count = 0;

			foreach ($product_ids as $product_id) {
				if ((int) $copy_from_product === (int) $product_id) {
					continue;
				}
				$variations_for_current_product = $variations;
				if (!empty($data['use_parent_product_price'])) {
					$parent_prices = $this->_get_parent_prices($product_id);

					foreach ($variations_for_current_product as $variation_index => $variation) {
						$variations_for_current_product[$variation_index]['regular_price'] = $parent_prices['regular_price'];
						$variations_for_current_product[$variation_index]['sale_price'] = $parent_prices['sale_price'];
					}
				}

				if (empty($variations_to_copy) && empty($only_copy_new_variations)) {
					// Delete existing variations if we're copying all variations
					$this->_delete_product_variations($product_id);
					$new_attributes = $source_attributes;
				} else {

					$target_product_response = VGSE()->helpers->create_rest_request('GET', '/wc/v1/products/' . $product_id);
					$target_product_data = $target_product_response->get_data();

					// Add index to existing attributes so we can update them easily later
					$new_attributes = $this->_index_attributes($target_product_data['attributes']);

					// Iterate over new variations to find missing attributes and add them to the target product
					foreach ($variations_for_current_product as $variation_index => $variation) {

						if ($only_copy_new_variations) {
							// Remove new variations matching the same attributes in the old variations
							foreach ($target_product_data['variations'] as $target_variation) {
								if ($target_variation['attributes'] == $variation['wpse_original_attributes']) {
									unset($variations_for_current_product[$variation_index]);
								}
							}
						} else {
							// Update existing variations with same attributes to avoid duplicating variations
							foreach ($target_product_data['variations'] as $target_variation) {
								if ($target_variation['attributes'] == $variation['wpse_original_attributes']) {
									$variations_for_current_product[$variation_index]['id'] = $target_variation['id'];
								}
							}
						}

						// Add missing attributes to the target product
						foreach ($variation['attributes'] as $variation_attribute) {
							$attribute_key = $variation_attribute['name'];
							$attribute_option = $variation_attribute['option'];
							if (isset($new_attributes[$attribute_key]) && !in_array($attribute_option, $new_attributes[$attribute_key]['options'])) {
								$new_attributes[$attribute_key]['options'][] = $attribute_option;
							} elseif (!isset($new_attributes[$attribute_key])) {
								$new_attributes[$attribute_key] = $source_attributes[$attribute_key];
								$new_attributes[$attribute_key]['options'] = array($attribute_option);
							}
						}
					}
				}


				$new_product_data = array(
					'ID' => $product_id,
					'default_attributes' => $product_data['default_attributes'],
					'attributes' => array_values($new_attributes),
					'type' => $source_product_type,
					'wpse_source' => 'copy_variations'
				);
				$modified_product_api_response = VGSE()->WC->update_products_with_api($new_product_data, 3);
				$modified_product_data = $modified_product_api_response->get_data();

				foreach ($variations_for_current_product as $variation_index => $variation) {
					$variations_for_current_product[$variation_index]['wpse_parent_id'] = $product_id;
					$variations_for_current_product[$variation_index]['wpse_source_id'] = $copy_from_product;
					$variations_for_current_product[$variation_index]['wpse_source'] = 'copy_variations';
				}

				WPSE_Queues_Obj()->bulk_entry($variations_for_current_product, sanitize_text_field(VGSE()->helpers->get_job_id_from_request()));
				$variations_count += count($variations_for_current_product);

				// Reduce memory usage
				$modified_product_api_response = null;
				$modified_product_data = null;
			}

			$out = array(
				'created' => $variations_count,
				'deleted' => array_unique(VGSE()->deleted_rows_ids)
			);
			return $out;
		}

		function _process_copy_variations_queue($job_id) {
			$batch_size = (!empty(VGSE()->options['wc_products_variation_copy_batch_size'])) ? (int) VGSE()->options['wc_products_variation_copy_batch_size'] : 50;
			$variations = WPSE_Queues_Obj()->get_tasks_for_processing($job_id, $batch_size);
			if (empty($variations)) {
				return false;
			}
			$grouped_variations_by_parent_id = array();
			$first_variation = current($variations);
			$copy_from_product = $first_variation['wpse_source_id'];
			foreach ($variations as $variation) {
				if (!isset($grouped_variations_by_parent_id[$variation['wpse_parent_id']])) {
					$grouped_variations_by_parent_id[$variation['wpse_parent_id']] = array();
				}
				$grouped_variations_by_parent_id[$variation['wpse_parent_id']][] = $variation;
			}
			foreach ($grouped_variations_by_parent_id as $product_id => $variations_to_insert) {
				$modified_variations_api_response = VGSE()->helpers->create_rest_request('POST', '/wc/v3/products/' . $product_id . '/variations/batch', array(
					'create' => $variations_to_insert
				));
			}
			$product_ids = array_keys($grouped_variations_by_parent_id);
			do_action('vg_sheet_editor/woocommerce/after_all_variations_copied', $copy_from_product, $product_ids);
			WPSE_Queues_Obj()->mark_tasks_as_processed($job_id, $batch_size);
			return count($variations);
		}

		/**
		 * Create variations rows
		 */
		function process_copy_variations_queue() {
			if (empty(VGSE()->helpers->get_job_id_from_request())) {
				wp_send_json_error(array('message' => __('Missing parameters.', 'vg_sheet_editor' )));
			}

			if (!VGSE()->helpers->verify_nonce_from_request() || !VGSE()->helpers->user_can_edit_post_type($this->post_type)) {
				wp_send_json_error(array('message' => __('Request not allowed. Try again later.', 'vg_sheet_editor' )));
			}

			if (!function_exists('WPSE_Queues_Obj')) {
				wp_send_json_error(array('message' => __('Please update all the WP Sheet Editor plugins. This feature requires the latest version of all our plugins.', 'vg_sheet_editor' )));
			}

			// Disable post actions to prevent conflicts with other plugins
			VGSE()->helpers->remove_all_post_actions($this->post_type);
			$job_id = sanitize_file_name(VGSE()->helpers->get_job_id_from_request());
			if (!WPSE_Queues_Obj()->get_tasks_for_processing($job_id)) {
				wp_send_json_error(array('message' => __('We don\'t have variations to copy.', 'vg_sheet_editor' )));
			}

			$processed = $this->_process_copy_variations_queue($job_id);
			wp_send_json_success(array(
				'message' => __('{total_created} of {total} variations created.', 'vg_sheet_editor' ),
				'created' => $processed,
			));
		}

		function create_variations_rows() {
			if (empty($_REQUEST['vgse_variation_tool']) || !isset($_REQUEST['vgse_variation_manager_source'])) {
				wp_send_json_error(array('message' => __('Missing parameters.', 'vg_sheet_editor' )));
			}

			if (!VGSE()->helpers->verify_nonce_from_request() || !VGSE()->helpers->user_can_edit_post_type($this->post_type)) {
				wp_send_json_error(array('message' => __('Request not allowed. Try again later.', 'vg_sheet_editor' )));
			}
			$variation_manager_source = sanitize_text_field($_REQUEST['vgse_variation_manager_source']);
			$variation_tool = sanitize_text_field($_REQUEST['vgse_variation_tool']);
			$page = isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : null;
			$job_id = sanitize_text_field(VGSE()->helpers->get_job_id_from_request());
			$selected_product = null;
			if (isset($_REQUEST['product'])) {
				$selected_product = is_string($_REQUEST['product']) ? sanitize_text_field($_REQUEST['product']) : array_map('sanitize_text_field', $_REQUEST['product']);
			}
			if ($variation_tool === 'copy') {
				$data_from_request = array(
					'nonce' => sanitize_text_field(VGSE()->helpers->get_nonce_from_request()),
					'copy_from_product' => sanitize_text_field($_REQUEST['copy_from_product']),
					'copy_individual_variations' => !empty($_REQUEST['copy_individual_variations']),
					'only_copy_new_variations' => !empty($_REQUEST['only_copy_new_variations']),
					'use_parent_product_price' => !empty($_REQUEST['use_parent_product_price']),
					'ignore_variation_image' => !empty($_REQUEST['ignore_variation_image']),
					'individual_variations' => isset($_REQUEST['individual_variations']) ? array_filter(array_map('intval', $_REQUEST['individual_variations'])) : array(),
				);
			} else {
				$data_from_request = array(
					'nonce' => sanitize_text_field(VGSE()->helpers->get_nonce_from_request()),
					'link_attributes' => $_REQUEST['link_attributes'] === 'on',
					'number' => isset($_REQUEST['number']) ? (int) $_REQUEST['number'] : 0
				);
			}

			// Disable post actions to prevent conflicts with other plugins
			VGSE()->helpers->remove_all_post_actions($this->post_type);

			if ($variation_manager_source === 'individual') {

				if (empty($selected_product)) {
					wp_send_json_error(array('message' => __('Please select a product.', 'vg_sheet_editor' )));
				}
				$product_ids = array();
				if (is_string($selected_product)) {
					$product_ids[] = VGSE()->helpers->_get_post_id_from_search($selected_product);
				} elseif (is_array($selected_product)) {
					foreach ($selected_product as $product) {
						$product_ids[] = VGSE()->helpers->_get_post_id_from_search($product);
					}
				}
			} elseif (in_array($variation_manager_source, array('search', 'all'), true)) {


				$bulk_edit_id = 'wpsebe' . $job_id;
				if ($page > 1 && function_exists('vgse_formulas_init')) {
					$_REQUEST['filters'] = array(
						'meta_query' => array (
							array (
							'source' => 'meta',
							'key' => $bulk_edit_id,
							'compare' => '=',
							'value' => '1',
						  ),
						),
					);
				}

				$get_rows_args = apply_filters('vg_sheet_editor/woocommerce/copy_variations/search_query/get_rows_args', array(
					'nonce' => wp_create_nonce('bep-nonce'),
					'post_type' => $this->post_type,
					'filters' => $_REQUEST['filters'],
					'paged' => $page,
					'wpse_source' => 'create_variations'
				));
				$base_query = VGSE()->helpers->prepare_query_params_for_retrieving_rows($get_rows_args);
				$base_query = apply_filters('vg_sheet_editor/woocommerce/copy_variations/posts_query', $base_query);

				$base_query['fields'] = 'ids';
				$per_page = (!empty(VGSE()->options) && !empty(VGSE()->options['be_posts_per_page_save']) ) ? (int) VGSE()->options['be_posts_per_page_save'] / 2 : 2;
				$base_query['posts_per_page'] = ( $per_page < 1 ) ? 1 : $per_page;
				$editor = VGSE()->helpers->get_provider_editor($this->post_type);
				VGSE()->current_provider = $editor->provider;
				$query = $editor->provider->get_items($base_query);
				$total = $query->found_posts;
				$product_ids = $query->posts;

				if ($page === 1 && function_exists('vgse_formulas_init')) {
					vgse_formulas_init()->_mark_all_items_for_bulk_edit_session($total, $editor, $base_query, $bulk_edit_id, 'product');
				}

				if ($page > 1 && empty($product_ids)) {

					if (!empty($bulk_edit_id)) {
						$editor->provider->delete_meta_key($bulk_edit_id, 'product');
					}
					wp_send_json_success(array(
						'message' => sprintf(__('%s variations created.', 'vg_sheet_editor' ), 0),
						'force_complete' => true,
						'deleted' => array(),
						'data' => array(),
						'processed_products' => array()
					));
				}
			}
			if (empty($product_ids)) {
				if (!empty($bulk_edit_id)) {
					$editor->provider->delete_meta_key($bulk_edit_id, 'product');
				}
				wp_send_json_error(array('message' => __('Target products not found.', 'vg_sheet_editor' )));
			}

			if ($variation_tool === 'copy') {
				$copy_result = $this->copy_variations($data_from_request, $product_ids);
				$variations_count = $copy_result['created'];
			} else {
				foreach ($product_ids as $product_id) {

					// We let the user select variation rows in the dropdown,
					// so we automatically switch to the parent
					$post = get_post($product_id);
					if ($post->post_parent > 0) {
						$product_id = $post->post_parent;
					}

					if ($post->post_type !== $this->post_type) {
						continue;
					}


					// Link variations using WC ajax function
					if ($data_from_request['link_attributes']) {
						if (VGSE()->WC->get_product_type($product_id) !== 'variable') {
							wp_set_object_terms($product_id, 'variable', 'product_type', false);
						}
						$variations = $this->link_all_variations($product_id);
						if (is_wp_error($variations)) {
							wp_send_json_error($variations->get_error_message());
						}
						do_action('vg_sheet_editor/woocommerce/after_linked_variations_created', $product_id, $variations);

						$variations_count = (int) $variations;
					} else {
						$variations_count = (int) $data_from_request['number'];
						$variation_data = array(
							'stock' => '',
						);
						// Copy the price from the parent product into the new variations
						$variation_data['regular_price'] = get_post_meta($product_id, '_regular_price', true);
						$variation_data['sale_price'] = get_post_meta($product_id, '_sale_price', true);

						$x = $variations_count;
						$api_request_data = array(
							'ID' => $product_id,
							'type' => 'variable',
						);
						VGSE()->WC->update_products_with_api($api_request_data);
						$new_variation_ids = array();
						while ($x > 0) {							
							$variation = wc_get_product_object( 'variation' );
							$variation->set_parent_id( $product_id );
							$new_variation_ids[] = $variation->save();
							$x--;
						}
						
						do_action('vg_sheet_editor/woocommerce/after_variations_created', $new_variation_ids, $product_id);

					}
				}
			}

			// We don't retrieve the rows when using the search to reduce
			// memory usage because we might copy to a lot of products at once
			if (in_array($variation_manager_source, array('search', 'all'), true)) {
				$data_rows = array();
			} else {
				$rows = VGSE()->helpers->get_rows(array(
					'nonce' => $data_from_request['nonce'],
					'post_type' => $this->post_type,
					'wp_query_args' => array(
						'post__in' => $product_ids,				
						'orderby'  => array( 'post_date' => 'DESC', 'ID' => 'DESC' ),
					),
					'filters' => '&wc_display_variations=yes',
					'wpse_source' => 'create_variations'
				));

				if (is_wp_error($rows)) {
					wp_send_json_error($rows->get_error_message());
				}
				$data_rows = array_values($rows['rows']);
			}

			if (!empty($_REQUEST['totalCalls']) && !empty($_REQUEST['page']) && (int) $_REQUEST['totalCalls'] === (int) $_REQUEST['page'] && !empty($bulk_edit_id)) {
				$editor->provider->delete_meta_key($bulk_edit_id, 'product');
			}

			wp_send_json_success(array(
				'message' => sprintf(__('%s variations created.', 'vg_sheet_editor' ), $variations_count),
				'deleted' => array_unique(VGSE()->deleted_rows_ids),
				'data' => $data_rows,
				'processed_products' => $product_ids,
				'variations_count' => $variations_count
			));
		}

		/**
		 * Create variations for every possible combination of attributes
		 * @param int $post_id
		 * @return \WP_Error|int
		 */
		function link_all_variations($post_id) {
			global $wpdb;
			if (version_compare(WC()->version, '3.0') < 0) {
				return new WP_Error('wpse', array('message' => __('The option to create variations for every combination of attributes requires WooCommerce 3.0 or higher. Please update WooCommerce.', 'vg_sheet_editor' )));
			}

			if (!WP_Sheet_Editor_Helpers::current_user_can('edit_products')) {
				return new WP_Error('wpse', array('message' => __('User not allowed', 'vg_sheet_editor' )));
			}

			if (!$post_id) {
				return new WP_Error('wpse', array('message' => __('Data missing, try again later.', 'vg_sheet_editor' )));
			}
			if (!get_post_type($post_id)) {
				return 0;
			}
			// Clear the wc cache because sometimes wc_get_products returns stale data and break this process
			WPSE_WC_Products_Data_Formatting_Obj()->clear_wc_caches($post_id);
			$regular_price = get_post_meta($post_id, '_regular_price', true);
			$sale_price = get_post_meta($post_id, '_sale_price', true);

			$max_variations = (!empty(VGSE()->options['maximum_variations_combination'])) ? (int) VGSE()->options['maximum_variations_combination'] : 200;
			wc_maybe_define_constant('WC_MAX_LINKED_VARIATIONS', $max_variations);
			wc_set_time_limit(0);
			$product = wc_get_product($post_id);
			$data_store = $product->get_data_store();

			if (!is_callable(array($data_store, 'create_all_product_variations'))) {
				return new WP_Error('wpse', array('message' => __('Wrong product type. Make sure it is a variable product.', 'vg_sheet_editor' )));
			}

			$existing_variation_ids = array_map('intval', $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type = 'product_variation' AND post_parent = %d", (int) $post_id)));
			$added = intval($data_store->create_all_product_variations($product, WC_MAX_LINKED_VARIATIONS));
			$data_store->sort_all_product_variations($product->get_id());

			if ($added > 0 && $regular_price) {
				$sql = "SELECT ID FROM $wpdb->posts WHERE post_type = 'product_variation' AND post_parent = %d";
				$prepared_data = array($post_id);
				if (!empty($existing_variation_ids)) {
					$ids_in_query_placeholders = implode(', ', array_fill(0, count($existing_variation_ids), '%d'));
					$sql .= ' AND ID NOT IN (' . $ids_in_query_placeholders . ') ';
					$prepared_data = array_merge($prepared_data, $existing_variation_ids);
				}
				$new_variation_ids = $wpdb->get_col($wpdb->prepare($sql, $prepared_data));
				foreach ($new_variation_ids as $variation_id) {
					$variation = wc_get_product($variation_id);
					$variation->set_regular_price($regular_price);
					$variation->set_sale_price($sale_price);
					$variation->save();
				}
			}

			return $added;
		}

		/**
		 * Render modal for creating variations
		 * @param str $post_type
		 * @return null
		 */
		function render_create_variation_modal($post_type) {
			if ($this->post_type !== $post_type) {
				return;
			}
			$nonce = wp_create_nonce('bep-nonce');
			$random_id = rand();
			include VGSE_WC_DIR . '/views/spreadsheet-create-variations-modal.php';
		}

		/**
		 * Save / get default product attributes
		 */
		function get_default_attributes($post_id, $api_data = null) {
			$out = array();
			if (VGSE()->WC->get_product_type($post_id) !== 'variable') {
				return $out;
			}
			// Is get
			if (!$api_data) {
				$api_response = VGSE()->helpers->create_rest_request('GET', '/wc/v1/products/' . $post_id);
				$api_data = $api_response->get_data();
			}

			$default_attributes_out = $api_data['default_attributes'];

			foreach ($default_attributes_out as $default_attribute) {
				if ($default_attribute['id'] > 0) {
					$out[] = $default_attribute;
				} else {
					$attributes_found = wp_list_filter($api_data['attributes'], array('name' => sanitize_title($default_attribute['name'])));

					$attribute = ( $attributes_found) ? current($attributes_found) : $default_attribute;
					$out[] = wp_parse_args(array(
						'name' => $attribute['name']
							), $default_attribute);
				}
			}
			return $out;
		}

		function save_default_attributes($data, $post_id) {
			$_product = wc_get_product($post_id);
			$attributes = $_product->get_attributes();
			$new_data = array();

			if (is_array($data)) {
				foreach ($data as $default_attribute) {
					if (empty($default_attribute['name'])) {
						continue;
					}
					$sanitized_title = sanitize_title($default_attribute['name']);
					if (isset($attributes['pa_' . $sanitized_title])) {
						$key = 'pa_' . $sanitized_title;

						$new_data[] = wp_parse_args(array(
							'id' => wc_attribute_taxonomy_id_by_name($key)
								), $default_attribute);
					} else {
						$new_data[] = wp_parse_args($default_attribute, array(
							'id' => 0,
							'name' => $sanitized_title
						));
					}
				}
			}


			$api_response = VGSE()->WC->update_products_with_api(array(
				'ID' => $post_id,
				'variations' => array(),
				'default_attributes' => $new_data
			));
			$api_data = $api_response->get_data();
			return $api_data;
		}

		function get_product_variations() {
			global $wpdb;

			if (!VGSE()->helpers->verify_nonce_from_request() || !VGSE()->helpers->user_can_edit_post_type($this->post_type)) {
				wp_send_json_error(array('message' => __('You dont have enough permissions to view this page.', 'vg_sheet_editor' )));
			}
			if (empty($_REQUEST['product_id'])) {
				wp_send_json_error(array('message' => __('Please select a source product.', 'vg_sheet_editor' )));
			}
			$product_id = VGSE()->helpers->_get_post_id_from_search(sanitize_text_field($_REQUEST['product_id']));
			$out = $wpdb->get_results($wpdb->prepare("SELECT ID, CONCAT(post_title, ' (ID: ', ID, ', SKU: ', sku, ')') as 'post_title' FROM $wpdb->posts p 
LEFT JOIN {$wpdb->prefix}wc_product_meta_lookup l 
ON l.product_id = p.ID 
WHERE post_parent = %d AND post_type = %s AND post_status IN ('publish', 'draft')", $product_id, $this->variation_post_type), ARRAY_A);
			wp_send_json_success($out);
		}

		/**
		 * Save / get default product attributes
		 */
		function update_default_attributes() {

			if (!VGSE()->helpers->verify_nonce_from_request() || !VGSE()->helpers->user_can_edit_post_type($this->post_type)) {
				wp_send_json_error(array('message' => __('You dont have enough permissions to view this page.', 'vg_sheet_editor' )));
			}

			// Disable post actions to prevent conflicts with other plugins
			VGSE()->helpers->remove_all_post_actions($this->post_type);
			$post_id = (int) $_REQUEST['postId'];

			// Is update
			if (isset($_REQUEST['data'])) {
				if (!is_array($_REQUEST['data'])) {
					$_REQUEST['data'] = array($_REQUEST['data']);
				}
				$default_attributes = array();
				foreach ($_REQUEST['data'] as $default_attribute) {
					$default_attributes[] = array(
						'name' => sanitize_text_field($default_attribute['name']),
						'option' => sanitize_text_field($default_attribute['option']),
					);
				}

				$api_data = $this->save_default_attributes($default_attributes, $post_id);
			} else {
				$api_response = VGSE()->helpers->create_rest_request('GET', '/wc/v1/products/' . $post_id);
				$api_data = $api_response->get_data();
			}

			$out = array(
				'data' => $this->get_default_attributes($post_id, $api_data)
			);

			$out['custom_handsontable_args'] = array(
				'columns' => array(
					array(
						'data' => 'name',
						'type' => 'autocomplete',
						'source' => array_values(wp_list_pluck(wp_list_filter($api_data['attributes'], array(
							'variation' => true
										)), 'name'))
					),
					array(
						'data' => 'option',
						'type' => 'autocomplete',
						'source' => array_reduce(wp_list_pluck(wp_list_filter($api_data['attributes'], array(
							'variation' => true
										)), 'options'), 'array_merge', array())
					),
				)
			);
			wp_send_json_success($out);
		}

		function _save_variation_global_attribute($post_id, $attribute_key, $attribute_value) {

			if ($attribute_value && $term = get_term_by('name', $attribute_value, $attribute_key)) {
				$value = $term->slug;
				$parent_id = get_post_field('post_parent', $post_id);

				$cache_key = $parent_id . $attribute_key;
				if( empty($this->parent_attribute_terms[$cache_key])){
					$this->parent_attribute_terms[$cache_key] = wp_get_object_terms($parent_id, $attribute_key, array('fields' => 'slugs'));		
				}
				$parent_product_attribute_terms = $this->parent_attribute_terms[$cache_key];
					
				if( ! in_array($value, $parent_product_attribute_terms, true)){
					wp_set_object_terms($parent_id, $term->term_id, $attribute_key, true);
					$this->parent_attribute_terms[$cache_key][] = $term->slug;
				}
			} else {
				$value = '';
			}

			update_post_meta($post_id, 'attribute_' . $attribute_key, $value);
		}

		/**
		 * Save variations rows using WC API
		 * @param array $data
		 * @param str $post_type
		 * @param array $spreadsheet_columns
		 * @param array $request
		 * @return array
		 */
		function maybe_save_variations($response, $data, $post_type, $spreadsheet_columns, $request) {

			if (!$this->variations_enabled(null, $request) || empty($GLOBALS['be_wc_variations_rows'])) {
				return $response;
			}
			$variations_rows = $GLOBALS['be_wc_variations_rows'];

			if (empty($variations_rows)) {
				return $response;
			}

			$original_variation_rows = $variations_rows;

			// We save attributes without using the API because the documentation 
			// is not clear and it was too difficult to find the right parameters
			foreach ($variations_rows as $row_index => $row) {
				foreach ($row as $key => $column_value) {
					if (strpos($key, 'pa_') !== false) {
						$this->_save_variation_global_attribute($row['ID'], $key, $column_value);
						unset($variations_rows[$row_index][$key]);
					}
					if ($key === 'post_parent') {
						$parent = VGSE()->helpers->get_page_by_title($column_value, 'product');
						if ($parent) {
							$wc_variation = wc_get_product($row['ID']);
							$wc_variation->set_parent_id($parent->ID); 
							$wc_variation->save();
							unset($variations_rows[$row_index][$key]);
						}
					}
					if (VGSE()->helpers->user_can_delete_post_type($post_type) && $key === 'post_status' && $column_value === 'delete') {
						wp_delete_post($row['ID']);
						unset($variations_rows[$row_index][$key]);
					}

					// If file cells, convert URLs to file IDs					
					if (isset($spreadsheet_columns[$key]) && in_array($spreadsheet_columns[$key]['value_type'], array('boton_gallery', 'boton_gallery_multiple')) && $this->validate_url($column_value)) {

						$variations_rows[$row_index][$key] = intval(implode(',', array_filter(VGSE()->helpers->maybe_replace_urls_with_file_ids(explode(',', $column_value), $row['ID']))));
					}
				}
			}

			$formatted_for_api = WPSE_WC_Products_Data_Formatting_Obj()->convert_row_to_api_format($variations_rows);

			$error_messages = array();
			foreach ($formatted_for_api as $row_to_save) {
				$parent_id = $row_to_save['ID'];
				$final = $row_to_save;

				// Reset the variation index because it's used as menu order and we don't want to change it
				$final['variations'] = array();
				foreach ($row_to_save['variations'] as $variation_row) {
					$variation_row['wpse_source'] = 'save_rows';
					$current_menu_order = (int) get_post_field('menu_order', $variation_row['id']);
					if (!empty($variation_row['menu_order']) && (int) $variation_row['menu_order'] !== $current_menu_order) {
						$menu_order = $variation_row['menu_order'];
					} else {
						$menu_order = $current_menu_order;
					}
					$variation_row['menu_order'] = $menu_order;
					// If the variation has a duplicate menu order, we assign the next number
					// we obey the menu order only if no other variation uses it
					if (isset($final['variations'][$menu_order])) {
						$final['variations'][] = $variation_row;
					} else {
						$final['variations'][$menu_order] = $variation_row;
					}
				}

				$api_response = VGSE()->helpers->create_rest_request('POST', '/wc/v3/products/' . $parent_id . '/variations/batch', array(
					'update' => apply_filters('vg_sheet_editor/woocommerce/variations_data_to_save', $final['variations'], $parent_id, $response, $post_type)
				));

				$response_data = $api_response->get_data();
				if (!empty($response_data['update'])) {
					foreach ($response_data['update'] as $variation_response) {
						if (empty($variation_response['error'])) {
							continue;
						}
						$error_messages[] = sprintf(__('Error on row ID: %d - %s', 'vg_sheet_editor' ), $variation_response['id'], $variation_response['error']['message']);
					}
				}
				do_action('vg_sheet_editor/woocommerce/variable_product_updated', $final, $request, $variations_rows, $original_variation_rows);
			}

			if (!empty($error_messages)) {
				return new WP_Error('wpse', __('Please correct the error and save again.', 'vg_sheet_editor' ) . '<br>' . implode('<br>', $error_messages));
			}

			return $response;
		}

		function validate_url($url) {
			$path = parse_url($url, PHP_URL_PATH);
			$encoded_path = array_map('urlencode', explode('/', $path));
			$url = str_replace($path, implode('/', $encoded_path), $url);

			return filter_var($url, FILTER_VALIDATE_URL) ? true : false;
		}

		/**
		 * Remove variations rows from the posts list before saving
		 * @param array $data
		 * @param array $request
		 * @return array
		 */
		function exclude_variations_from_saving_list($data, $request) {
			if ($request['post_type'] === 'product' && !empty($data)) {
				$data_with_post_type = VGSE()->helpers->add_post_type_to_rows($data);
				if (isset($data_with_post_type[0]['post_type'])) {
					$general_products = wp_list_filter($data_with_post_type, array(
						'post_type' => $this->variation_post_type
							), 'NOT');
					$variation_rows = wp_list_filter($data_with_post_type, array(
						'post_type' => $this->variation_post_type
					));
					$data = array_merge($general_products, $variation_rows);
				}
			}
			if (!$this->variations_enabled(null, $request) ||
					empty($data) || !is_array($data)) {
				return $data;
			}

			$data_with_post_type = VGSE()->helpers->add_post_type_to_rows($data);

			$general_products = wp_list_filter($data_with_post_type, array(
				'post_type' => $this->variation_post_type
					), 'NOT');

			$variation_rows = wp_list_filter($data_with_post_type, array(
				'post_type' => $this->variation_post_type
			));

			$variations_to_save_without_wc_api = array();
			foreach ($variation_rows as $index => $variation_row) {
				$variations_to_save_without_wc_api[$index] = array(
					'ID' => $variation_row['ID']
				);

				foreach ($variation_row as $key => $value) {
					if (!in_array($key, $this->wc_core_variation_columns)) {
						$variations_to_save_without_wc_api[$index][$key] = $value;
						unset( $variation_rows[ $index ][ $key ]);
					}
				}
			}

			$general_products = array_merge($general_products, $variations_to_save_without_wc_api);

			$GLOBALS['be_wc_variations_rows'] = $variation_rows;

			return $general_products;
		}

		/**
		 * Creates or returns an instance of this class.
		 *
		 * 
		 */
		static function get_instance() {
			if (null == WP_Sheet_Editor_WooCommerce_Variations::$instance) {
				WP_Sheet_Editor_WooCommerce_Variations::$instance = new WP_Sheet_Editor_WooCommerce_Variations();
				WP_Sheet_Editor_WooCommerce_Variations::$instance->init();
			}
			return WP_Sheet_Editor_WooCommerce_Variations::$instance;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

}


if (!function_exists('vgse_init_WooCommerce_Variations')) {

	function vgse_init_WooCommerce_Variations() {
		return WP_Sheet_Editor_WooCommerce_Variations::get_instance();
	}

	vgse_init_WooCommerce_Variations();
}