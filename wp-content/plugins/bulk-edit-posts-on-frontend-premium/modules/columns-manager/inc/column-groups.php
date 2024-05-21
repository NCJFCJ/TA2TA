<?php defined( 'ABSPATH' ) || exit;
if (!class_exists('WPSE_Column_Groups')) {

	class WPSE_Column_Groups {

		static private $instance = false;
		var $groups_key = 'vgse_column_groups';

		private function __construct() {
			
		}

		function init() {

			if (!apply_filters('vg_sheet_editor/column_groups_feature_allowed', true)) {
				return;
			}
			// Column groups
			if (VGSE()->helpers->user_can_manage_options()) {
				add_action('vg_sheet_editor/columns_visibility/after_fields', array($this, 'render_save_columns_view_option'));
				add_action('vg_sheet_editor/columns_visibility/after_options_saved', array($this, 'save_column_group'), 11, 2);
			} else {
				add_action('vg_sheet_editor/editor/before_init', array($this, 'register_toolbar_items'));
			}

			$this->maybe_switch_to_group();
			add_action('vg_sheet_editor/editor/before_init', array($this, 'register_groups_toolbar_items'));
			add_action('wp_ajax_vgse_delete_saved_columns_manager', array($this, 'delete_saved_group'));
			add_filter('vg_sheet_editor/columns_visibility/options', array($this, 'set_current_group_columns'), 10, 2);

			add_action('show_user_profile', array($this, 'render_user_profile_fields'));
			add_action('edit_user_profile', array($this, 'render_user_profile_fields'));
			add_action('edit_user_profile_update', array($this, 'save_user_profile_fields'));
			add_action('personal_options_update', array($this, 'save_user_profile_fields'));
		}

		/**
		 * Register toolbar item to edit columns visibility live on the spreadsheet
		 */
		function register_toolbar_items($editor) {
			$post_types = $editor->args['enabled_post_types'];
			foreach ($post_types as $post_type) {
				$allowed_groups = $this->get_allowed_groups_for_post_type($post_type);
				if (count($allowed_groups) < 2) {
					continue;
				}
				$editor->args['toolbars']->register_item('columns_manager', array(
					'type' => 'button',
					'content' => __('Spreadsheet views', 'vg_sheet_editor' ),
					'url' => 'javascript:void(0)',
					'toolbar_key' => 'secondary',
					'allow_in_frontend' => false,
						), $post_type);
			}
		}

		function save_user_profile_fields($user_id) {
			if (empty(VGSE()->options['enable_spreadsheet_views_restrictions'])) {
				return;
			}
			if (!WP_Sheet_Editor_Helpers::current_user_can('edit_user', $user_id) || !VGSE()->helpers->user_can_manage_options() || !isset($_REQUEST['wpse_allowed_column_groups'])) {
				return false;
			}

			$data = VGSE()->helpers->safe_text_only($_REQUEST['wpse_allowed_column_groups']);
			update_user_meta($user_id, 'wpse_allowed_column_groups', $data);
		}

		function render_user_profile_fields($user) {
			if (empty(VGSE()->options['enable_spreadsheet_views_restrictions'])) {
				return;
			}
			if (!VGSE()->helpers->user_can_manage_options()) {
				return;
			}
			$sheets = VGSE()->helpers->get_prepared_post_types();
			$all_allowed_groups = get_user_meta($user->ID, 'wpse_allowed_column_groups', true);
			if (empty($all_allowed_groups)) {
				$all_allowed_groups = array();
			}
			?>
			<h3><?php _e("WP Sheet Editor", 'vg_sheet_editor' ); ?></h3>
			<p><?php _e("Note. This is not a security feature, the user can edit all the fields in the normal editor based on the role. This option is for convenience, so they only see the columns they need when they open the spreadsheet editor. If you leave these fields empty they can switch between all the existing views", 'vg_sheet_editor' ); ?></p>

			<table class="form-table">
				<?php
				foreach ($sheets as $sheet) {
					$key = $sheet['key'];
					$name = $sheet['label'];

					if (!empty($sheet['is_disabled'])) {
						continue;
					}

					if (!isset($all_allowed_groups[$key])) {
						$all_allowed_groups[$key] = '';
					}
					?>
					<tr>
						<th><label for="wpse-allowed-column-groups<?php echo esc_attr($key); ?>"><?php printf(__("%s: Allowed spreadsheet views", 'vg_sheet_editor' ), esc_html($name)); ?></label></th>
						<td>
							<input type="text" name="wpse_allowed_column_groups[<?php echo esc_attr($key); ?>]" id="wpse-allowed-column-groups<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($all_allowed_groups[$key]); ?>" class="regular-text" /><br />
							<span class="description"><?php _e("Please enter the name of the spreadsheet views separated by commas.", 'vg_sheet_editor' ); ?></span>
						</td>
					</tr>
				<?php } ?>
			</table>
			<?php
		}

		function get_allowed_groups_for_post_type($post_type) {
			if (empty(VGSE()->options['enable_spreadsheet_views_restrictions'])) {
				$existing_groups = get_option($this->groups_key);
				$allowed_groups = isset($existing_groups[$post_type]) ? array_keys($existing_groups[$post_type]) : array();
			} else {
				$all_allowed_groups = get_user_meta(get_current_user_id(), 'wpse_allowed_column_groups', true);
				$allowed_groups = isset($all_allowed_groups[$post_type]) ? array_filter(array_map('sanitize_title', array_map('trim', explode(',', $all_allowed_groups[$post_type])))) : array();
			}
			return $allowed_groups;
		}

		function is_group_usage_allowed($group_key, $post_type) {
			$out = true;
			if (empty($post_type) || empty($group_key)) {
				return false;
			}
			$allowed_groups = $this->get_allowed_groups_for_post_type($post_type);
			$existing_groups = get_option($this->groups_key);

			// Disallow if group doesn't exist
			if (empty($existing_groups) || empty($existing_groups[$post_type]) || empty($existing_groups[$post_type][$group_key])) {
				$out = false;
			}

			// Disallow if the user is restricted to specific groups and the group is not whitelisted for this user
			if (!empty($allowed_groups) && !in_array($group_key, $allowed_groups, true)) {
				$out = false;
			}
			return $out;
		}

		function get_active_group() {
			if (!VGSE()->helpers->is_editor_page() && !wp_doing_ajax()) {
				return false;
			}

			$post_type = VGSE()->helpers->get_provider_from_query_string();
			$last_groups_used = get_user_meta(get_current_user_id(), 'wpse_last_column_group', true);

			if (empty($post_type)) {
				return false;
			}

			$existing_groups = get_option($this->groups_key);
			if (empty($existing_groups)) {
				return false;
			}

			$last_group = isset($last_groups_used[$post_type]) ? $last_groups_used[$post_type] : null;
			$allowed_groups = $this->get_allowed_groups_for_post_type($post_type);

			$current_group = false;
			if ($last_group && (in_array($last_group, $allowed_groups, true) || empty($allowed_groups))) {
				$current_group = $last_group;
			} elseif (!empty($allowed_groups)) {
				$current_group = current($allowed_groups);
			}

			return $current_group;
		}

		function set_current_group_columns($visibility_options, $post_type) {
			if (!apply_filters('vg_sheet_editor/columns_groups_enabled', true, $post_type)) {
				return $visibility_options;
			}
			$group_key = $this->get_active_group();
			if (!$post_type) {
				$post_type = VGSE()->helpers->get_provider_from_query_string();
			}
			if (!$this->is_group_usage_allowed($group_key, $post_type)) {
				return $visibility_options;
			}

			if (empty($visibility_options)) {
				$visibility_options = array();
			}
			if (empty($visibility_options[$post_type])) {
				$visibility_options[$post_type] = array();
			}

			$existing_groups = get_option($this->groups_key);
			$group_columns = $existing_groups[$post_type][$group_key];

			$all_groups_raw = $existing_groups[$post_type];
			$all_groups_raw[] = $visibility_options[$post_type];
			$all_columns = VGSE()->helpers->array_flatten($all_groups_raw);
			foreach ($all_columns as $column_key => $column_title) {
				if (!isset($group_columns['enabled'][$column_key]) && !isset($group_columns['disabled'][$column_key])) {
					$group_columns['disabled'][$column_key] = $column_title;
				}
			}


			$visibility_options[$post_type] = $group_columns;
			return $visibility_options;
		}

		function save_last_columns_group($group_key, $post_type, $user_id = null) {
			if (!$user_id) {
				$user_id = get_current_user_id();
			}
			$last_groups_used = get_user_meta($user_id, 'wpse_last_column_group', true);

			if (empty($last_groups_used)) {
				$last_groups_used = array();
			}
			if (empty($last_groups_used[$post_type])) {
				$last_groups_used[$post_type] = array();
			}

			$last_groups_used[$post_type] = $group_key;
			update_user_meta($user_id, 'wpse_last_column_group', $last_groups_used);
		}

		function maybe_switch_to_group() {
			if (empty($_GET['wpse_cmg'])) {
				return;
			}

			$post_type = VGSE()->helpers->get_provider_from_query_string();
			$group_key = sanitize_title($_GET['wpse_cmg']);

			if ($this->is_group_usage_allowed($group_key, $post_type)) {
				$this->save_last_columns_group($group_key, $post_type);
			}

			$url = esc_url(remove_query_arg('wpse_cmg'));
			wp_redirect($url);
			exit();
		}

		function register_groups_toolbar_items($editor) {
			$existing = get_option($this->groups_key);
			if (empty($existing)) {
				return;
			}

			$post_types = $editor->args['enabled_post_types'];
			$active_group_key = $this->get_active_group();
			foreach ($post_types as $post_type) {
				if (!isset($existing[$post_type])) {
					continue;
				}

				ksort($existing[$post_type]);
				foreach ($existing[$post_type] as $group_key => $group) {
					if (!$this->is_group_usage_allowed($group_key, $post_type)) {
						continue;
					}

					$delete_button_attribute = ( $active_group_key && $group_key === $active_group_key ) ? 'data-active-item data-saved-item' : 'data-saved-item';
					$extra_attributes = 'data-saved-type="columns_manager" ' . $delete_button_attribute . ' data-item-name="' . esc_attr($group_key) . '" ';
					if ($active_group_key && $group_key === $active_group_key) {
						$extra_attributes .= ' data-reload="1" ';
					}
					$editor->args['toolbars']->register_item('cm_g_' . $group_key, array(
						'type' => 'button',
						'toolbar_key' => 'secondary',
						'allow_in_frontend' => false,
						'parent' => 'columns_manager',
						'content' => $group['name'],
						'url' => esc_url(add_query_arg('wpse_cmg', $group_key)),
						'extra_html_attributes' => $extra_attributes
							), $post_type);
				}
			}
		}

		function delete_saved_group() {
			if (empty($_REQUEST['post_type']) || !VGSE()->helpers->verify_nonce_from_request() || !VGSE()->helpers->user_can_manage_options()) {
				wp_send_json_error(array('message' => __('You dont have enough permissions to view this page.', 'vg_sheet_editor' )));
			}

			$post_type = VGSE()->helpers->sanitize_table_key($_REQUEST['post_type']);
			$group_key = sanitize_text_field($_REQUEST['search_name']);

			$saved_items = get_option($this->groups_key);
			if (empty($saved_items)) {
				wp_send_json_success();
			}

			if (!isset($saved_items[$post_type]) || !isset($saved_items[$post_type][$group_key])) {
				wp_send_json_success();
			}
			unset($saved_items[$post_type][$group_key]);
			update_option($this->groups_key, $saved_items, false);
			wp_send_json_success();
		}

		function save_column_group($post_type, $options) {
			if (!isset($_REQUEST['wpse_group_name'])) {
				return;
			}
			$name = sanitize_text_field($_REQUEST['wpse_group_name']);
			$existing = get_option($this->groups_key);
			if (empty($existing)) {
				$existing = array();
			}
			if (empty($existing[$post_type])) {
				$existing[$post_type] = array();
			}
			$group = $options[$post_type];
			$group['name'] = $name;
			$key = sanitize_title($name);
			$existing[$post_type][$key] = $group;
			update_option($this->groups_key, $existing, false);

			// Activate the group that we just created, otherwise they can enable columns
			// and those columns wont appear enabled until they manually switch to the new columns group
			$this->save_last_columns_group($key, $post_type);
		}

		function render_save_columns_view_option($post_type) {
			if (!VGSE()->helpers->is_editor_page()) {
				return;
			}
			$groups = get_option($this->groups_key);
			$active_group_key = $this->get_active_group();
			$name = ( $active_group_key && isset($groups[$post_type][$active_group_key])) ? $groups[$post_type][$active_group_key]['name'] : '';
			?>
			<li class="vgse-save-preset">
				<label><?php _e('Add a name for this group of columns', 'vg_sheet_editor' ); ?> <a href="#" data-wpse-tooltip="right" aria-label="<?php esc_attr_e('We will save this group of columns as a spreadsheet view and you can switch between spreadsheets in the toolbar', 'vg_sheet_editor' ); ?>">( ? )</a></label>
				<input required name="wpse_group_name" type="text" value="<?php echo esc_attr($name); ?>"/>
			</li>
			<?php
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if (null == WPSE_Column_Groups::$instance) {
				WPSE_Column_Groups::$instance = new WPSE_Column_Groups();
				WPSE_Column_Groups::$instance->init();
			}
			return WPSE_Column_Groups::$instance;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

}

if (!function_exists('WPSE_Column_Groups_Obj')) {

	function WPSE_Column_Groups_Obj() {
		return WPSE_Column_Groups::get_instance();
	}

}
WPSE_Column_Groups_Obj();
