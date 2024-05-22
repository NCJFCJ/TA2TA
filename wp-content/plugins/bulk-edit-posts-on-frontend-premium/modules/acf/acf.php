<?php defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_Sheet_Editor_ACF' ) ) {

	class WP_Sheet_Editor_ACF {

		private static $instance                     = false;
		static $checkbox_keys                        = array();
		static $map_keys                             = array();
		public $gallery_field_keys                   = array();
		public $repeater_keys                        = array();
		public $flexible_content_keys                = array();
		public $flexible_content_columns_keys        = array();
		public $flexible_content_columns_keys_parsed = array();
		public $group_keys                           = array();
		public $excluded_serialized_keys             = array();

		private function __construct() {

		}

		public function init() {

			// exit if acf plugin is not active
			if ( ! $this->is_acf_plugin_active() ) {
				return;
			}

			add_action( 'vg_sheet_editor/editor/register_columns', array( $this, 'register_columns' ) );

			// Checkbox
			add_filter( 'vg_sheet_editor/infinite_serialized_column/column_settings', array( $this, 'filter_checkbox_column_settings' ), 20, 3 );
			add_filter( 'vg_sheet_editor/infinite_serialized_column/save_value_in_full_array', array( $this, 'filter_save_checkbox_from_serialized_class' ), 10, 9 );

			// Map
			add_filter( 'vg_sheet_editor/infinite_serialized_column/update_value', array( $this, 'filter_map_data_for_saving' ), 10, 3 );

			// Save ACF field key
			add_action( 'vg_sheet_editor/save_rows/before_saving_cell', array( $this, 'save_acf_field_key' ), 10, 6 );
			add_action( 'vg_sheet_editor/formulas/execute_formula/after_sql_execution', array( $this, 'save_acf_field_key_after_sql_formula' ), 10, 5 );

			// Repeater fields
			add_filter( 'vg_sheet_editor/provider/post/update_item_meta', array( $this, 'sync_repeater_main_field_count' ), 10, 3 );
			add_filter( 'vg_sheet_editor/provider/user/update_item_meta', array( $this, 'sync_repeater_main_field_count' ), 10, 3 );

			// Group fields
			add_filter( 'vg_sheet_editor/provider/post/update_item_meta', array( $this, 'sync_group_field' ), 10, 3 );
			add_filter( 'vg_sheet_editor/provider/user/update_item_meta', array( $this, 'sync_group_field' ), 10, 3 );

			// Flexible content fields
			add_action( 'vg_sheet_editor/save_rows/before_saving_rows', array( $this, 'register_flexible_content_hooks_for_saving' ) );
			add_action( 'vg_sheet_editor/formulas/execute_formula/before_execution', array( $this, 'register_flexible_content_hooks_for_saving' ) );
			add_action(
				'vg_sheet_editor/get_rows/after_query',
				function() {
					if ( empty( $this->flexible_content_keys ) ) {
						return;
					}
					add_filter( 'get_post_metadata', array( $this, 'get_flexible_content_field_value' ), 10, 4 );
					add_filter( 'get_user_metadata', array( $this, 'get_flexible_content_field_value' ), 10, 4 );
					add_filter( 'vgse_sheet_editor/provider/post/prefetch/meta_keys', array( $this, 'remove_flexible_content_columns_from_prefetch' ), 10, 2 );
				}
			);

			add_filter( 'vg_sheet_editor/serialized_addon/column_settings', array( $this, 'exclude_keys_from_serialized_columns' ), 10, 5 );
			add_filter( 'vg_sheet_editor/options_page/options', array( $this, 'add_settings_page_options' ) );
		}

		public function register_flexible_content_hooks_for_saving() {
			if ( empty( $this->flexible_content_keys ) ) {
				return;
			}
			add_filter( 'update_post_metadata', array( $this, 'sync_flexible_content_main_layouts_list' ), 10, 4 );
			add_filter( 'update_user_metadata', array( $this, 'sync_flexible_content_main_layouts_list' ), 10, 4 );
		}

		/**
		 * Add fields to options page
		 * @param array $sections
		 * @return array
		 */
		public function add_settings_page_options( $sections ) {
			$sections['misc']['fields'][] = array(
				'id'    => 'acf_show_checkboxes_multi_dropdown',
				'type'  => 'switch',
				'title' => __( 'ACF: Show checkboxes as multi select dropdowns?', 'vg_sheet_editor' ),
				'desc'  => __( 'By default, we show every checkbox as a separate column, but if you have checkboxes with many options you might want to use one column with a dropdown instead.', 'vg_sheet_editor' ),
			);
			return $sections;
		}

		public function sync_group_field( $value, $id, $key ) {
			if ( empty( $this->group_keys ) || strpos( $key, '_' ) === 0 ) {
				return $value;
			}

			$group_key = null;
			foreach ( $this->group_keys as $raw_repeater_key => $subfields ) {
				if ( in_array( $key, $subfields, true ) ) {
					$group_key = $raw_repeater_key;
					break;
				}
			}
			if ( empty( $group_key ) ) {
				return $value;
			}

			remove_filter( 'vg_sheet_editor/provider/post/update_item_meta', array( $this, __FUNCTION__ ) );
			remove_filter( 'vg_sheet_editor/provider/user/update_item_meta', array( $this, __FUNCTION__ ) );

			VGSE()->helpers->get_current_provider()->update_item_meta( $id, $group_key, '' );

			add_filter( 'vg_sheet_editor/provider/post/update_item_meta', array( $this, __FUNCTION__ ), 10, 3 );
			add_filter( 'vg_sheet_editor/provider/user/update_item_meta', array( $this, __FUNCTION__ ), 10, 3 );

			return $value;
		}

		public function sync_repeater_main_field_count( $value, $id, $key ) {
			global $wpdb;

			if ( empty( $this->repeater_keys ) || strpos( $key, '_' ) === 0 ) {
				return $value;
			}

			$repeater_key = null;
			$regex        = null;
			foreach ( $this->repeater_keys as $raw_repeater_key => $subfields ) {
				foreach ( $subfields as $repeater_key_regex ) {
					if ( preg_match( $repeater_key_regex, $key ) ) {
						$repeater_key = $raw_repeater_key;
						$regex        = $repeater_key_regex;
						break;
					}
				}
				if ( $repeater_key ) {
					break;
				}
			}
			if ( empty( $repeater_key ) ) {
				return $value;
			}

			$mysql_regex          = str_replace( array( '/', '\d' ), array( '', '[0-9]' ), $regex );
			$meta_table_name      = VGSE()->helpers->get_current_provider()->get_meta_table_name();
			$meta_table_id_column = VGSE()->helpers->get_current_provider()->get_meta_table_post_id_key();
			$sql                  = "SELECT meta_key FROM $meta_table_name WHERE meta_key RLIKE %s AND " . esc_sql( $meta_table_id_column ) . ' = %d ORDER BY meta_key DESC LIMIT 1';
			$highest_key          = $wpdb->get_var( $wpdb->prepare( $sql, '^' . $mysql_regex, $id ) );

			if ( empty( $highest_key ) ) {
				$highest_key = $key;
			}

			$count_regex     = str_replace( '\d+', '(\d+)', $regex );
			$repeater_count  = (int) preg_replace( $count_regex, '$1', $highest_key );
			$key_index_count = (int) preg_replace( $count_regex, '$1', $key );
			if ( $repeater_count < $key_index_count ) {
				$repeater_count = $key_index_count;
			}

			// Subfields index starts from 0, but the parent count starts from 1
			$repeater_count++;

			remove_filter( 'vg_sheet_editor/provider/post/update_item_meta', array( $this, __FUNCTION__ ), 10 );
			remove_filter( 'vg_sheet_editor/provider/user/update_item_meta', array( $this, __FUNCTION__ ), 10 );

			VGSE()->helpers->get_current_provider()->update_item_meta( $id, $repeater_key, $repeater_count );

			add_filter( 'vg_sheet_editor/provider/post/update_item_meta', array( $this, __FUNCTION__ ), 10, 3 );
			add_filter( 'vg_sheet_editor/provider/user/update_item_meta', array( $this, __FUNCTION__ ), 10, 3 );

			return $value;
		}

		public function get_flexible_content_field_data_from_wpse_key( $key ) {
			return isset( $this->flexible_content_columns_keys_parsed[ $key ] ) ? $this->flexible_content_columns_keys_parsed[ $key ] : false;
		}

		public function remove_flexible_content_columns_from_prefetch( $column_keys, $post_type ) {
			$regex = '/^(' . implode( '|', array_keys( $this->flexible_content_keys ) ) . ')=\d/';
			foreach ( $column_keys as $index => $column_key ) {
				if ( preg_match( $regex, $column_key ) ) {
					unset( $column_keys[ $index ] );
				}
			}
			return $column_keys;
		}
		public function get_flexible_content_field_value( $value, $object_id, $key, $single ) {
			if ( empty( $this->flexible_content_keys ) || strpos( $key, '_' ) === 0 || ! preg_match( '/=\d+=/', $key ) ) {
				return $value;
			}
			$data_from_key = $this->get_flexible_content_field_data_from_wpse_key( $key );
			if ( empty( $data_from_key ) ) {
				return $value;
			}

			remove_filter( 'get_post_metadata', array( $this, __FUNCTION__ ) );
			remove_filter( 'get_user_metadata', array( $this, __FUNCTION__ ) );

			$layouts_list = VGSE()->helpers->get_current_provider()->get_item_meta( $object_id, $data_from_key['main_key'], true, 'read' );
			if ( empty( $layouts_list ) ) {
				$layouts_list = array();
			}

			if ( isset( $layouts_list[ $data_from_key['row_index'] ] ) && $layouts_list[ $data_from_key['row_index'] ] === $data_from_key['layout_key'] ) {
				$key_parts = explode( '=', $key );
				unset( $key_parts[2] );
				$key   = implode( '_', $key_parts );
				$value = VGSE()->helpers->get_current_provider()->get_item_meta( $object_id, $key, $single, 'save', true );
			} else {
				$value = '';
			}
			add_filter( 'get_post_metadata', array( $this, __FUNCTION__ ), 10, 4 );
			add_filter( 'get_user_metadata', array( $this, __FUNCTION__ ), 10, 4 );

			return $value;
		}
		public function sync_flexible_content_main_layouts_list( $continue_saving, $id, $key, $value ) {
			global $wpdb;

			if ( empty( $this->flexible_content_keys ) || strpos( $key, '_' ) === 0 || ! preg_match( '/=\d+=/', $key ) ) {
				return $continue_saving;
			}

			$data_from_key = $this->get_flexible_content_field_data_from_wpse_key( $key );
			if ( empty( $data_from_key ) ) {
				return $continue_saving;
			}

			remove_filter( 'update_post_metadata', array( $this, __FUNCTION__ ), 10 );
			remove_filter( 'update_user_metadata', array( $this, __FUNCTION__ ), 10 );

			// Save the subfield value
			$key_parts = explode( '=', $key );
			unset( $key_parts[2] );
			$subfield_key = implode( '_', $key_parts );
			if ( empty( $value ) ) {
				VGSE()->helpers->get_current_provider()->delete_item_meta( $id, $subfield_key );
			} else {
				VGSE()->helpers->get_current_provider()->update_item_meta( $id, $subfield_key, $value );
			}

			// Update the list of layouts in the main value
			if ( ! empty( $value ) ) {
				$main_value = VGSE()->helpers->get_current_provider()->get_item_meta( $id, $data_from_key['main_key'], true, 'save', true );
				if ( empty( $main_value ) ) {
					$main_value = array();
				}
				$main_value[ (int) $data_from_key['row_index'] ] = $data_from_key['layout_key'];
				VGSE()->helpers->get_current_provider()->update_item_meta( $id, $data_from_key['main_key'], $main_value );
			}

			// Save the ACF field keys
			// We have a function that saves the acf field keys of all the fields except flexible content fields because they use special keys
			$sheet_key       = VGSE()->helpers->get_provider_from_query_string();
			$column_settings = VGSE()->helpers->get_column_settings( $key, $sheet_key );
			if ( empty( $value ) ) {
				VGSE()->helpers->get_current_provider()->delete_item_meta( $id, '_' . $subfield_key );

				if ( ! empty( $column_settings['acf_field']['parent'] ) && is_array( $column_settings['acf_field']['parent'] ) ) {
					VGSE()->helpers->get_current_provider()->delete_item_meta( $id, '_' . $column_settings['acf_field']['parent']['name'] );
				}
			} else {
				VGSE()->helpers->get_current_provider()->update_item_meta( $id, '_' . $subfield_key, $column_settings['acf_field']['key'] );

				if ( ! empty( $column_settings['acf_clone_main_key'] ) ) {
					VGSE()->helpers->get_current_provider()->update_item_meta( $id, '_' . $column_settings['acf_clone_field_key'], $column_settings['acf_clone_field_name'] );
					VGSE()->helpers->get_current_provider()->update_item_meta( $id, '_' . $column_settings['acf_clone_main_name'], $column_settings['acf_clone_main_key'] );
					VGSE()->helpers->get_current_provider()->update_item_meta( $id, $column_settings['acf_clone_main_name'], '' );
				} elseif ( ! empty( $column_settings['acf_field']['parent'] ) && is_array( $column_settings['acf_field']['parent'] ) ) {
					VGSE()->helpers->get_current_provider()->update_item_meta( $id, '_' . $column_settings['acf_field']['parent']['name'], $column_settings['acf_field']['parent']['key'] );
				}
			}

			add_filter( 'update_post_metadata', array( $this, __FUNCTION__ ), 10, 4 );
			add_filter( 'update_user_metadata', array( $this, __FUNCTION__ ), 10, 4 );

			return false;
		}

		public function filter_save_checkbox_from_serialized_class( $custom_saved, $final_array, $value, $post_id, $cell_key, $post_type, $column_settings, $spreadsheet_columns, $serialized_field ) {
			if ( empty( $column_settings['is_acf_checkbox'] ) ) {
				return $custom_saved;
			}

			$sample_field_key = $serialized_field->settings['sample_field_key'];

			// Allow to save field with the acf choice key, 1, yes, true, or check
			if ( in_array( $value, array( '1', 'yes', 'true', 'check', $column_settings['formatted']['checkedTemplate'] ), true ) ) {
				$value = $column_settings['formatted']['checkedTemplate'];
			} else {
				$value = '';
			}

			if ( empty( $final_array ) || ! is_array( $final_array ) ) {
				$final_array = array();
			}
			$final_array[] = $value;
			$final_array   = VGSE()->helpers->array_remove_empty( array_unique( VGSE()->helpers->array_flatten( $final_array ) ) );

			if ( empty( $value ) ) {
				$index = array_search( $column_settings['formatted']['checkedTemplate'], $final_array );
				if ( $index !== false ) {
					unset( $final_array[ $index ] );
				}
			}

			return $final_array;
		}

		public function save_acf_field_key_after_sql_formula( $column, $formula, $post_type, $spreadsheet_columns, $post_ids ) {
			$column_settings = $spreadsheet_columns[ $column ];
			if ( empty( $column_settings['acf_field'] ) || empty( $column_settings['acf_field']['key'] ) || in_array( $column, $this->flexible_content_columns_keys, true ) ) {
				return;
			}
			$column_settings['key_for_formulas'] = '_' . $column_settings['key_for_formulas'];
			$formula                             = '=REPLACE(""$current_value$"",""' . $column_settings['acf_field']['key'] . '"")';
			WP_Sheet_Editor_Formulas::get_instance()->execute_formula_as_sql( $post_ids, $formula, $column_settings, $post_type );

			if ( ! empty( $column_settings['acf_field']['parent'] ) ) {
				$column_settings['key_for_formulas'] = '_' . $column_settings['acf_field']['parent']['name'];
				$formula                             = '=REPLACE(""$current_value$"",""' . $column_settings['acf_field']['parent']['key'] . '"")';
				WP_Sheet_Editor_Formulas::get_instance()->execute_formula_as_sql( $post_ids, $formula, $column_settings, $post_type );
			}
		}

		public function save_acf_field_key( $item, $post_type, $column_settings, $key, $spreadsheet_columns, $post_id ) {
			if ( empty( $column_settings['acf_field'] ) || empty( $column_settings['acf_field']['key'] ) || in_array( $key, $this->flexible_content_columns_keys, true ) ) {
				return;
			}

			if ( ! empty( $column_settings['acf_field'] ) ) {
				$real_key = $column_settings['acf_field']['name'];
			} else {
				$real_key = preg_replace( '/_\d+_i_\d+$/', '', $key );
			}
			VGSE()->helpers->get_current_provider()->update_item_meta( $post_id, '_' . $real_key, $column_settings['acf_field']['key'] );

			if ( ! empty( $column_settings['acf_field']['parent'] ) && is_array( $column_settings['acf_field']['parent'] ) ) {
				VGSE()->helpers->get_current_provider()->update_item_meta( $post_id, '_' . $column_settings['acf_field']['parent']['name'], $column_settings['acf_field']['parent']['key'] );
			}
		}

		public function exclude_keys_from_serialized_columns( $column_settings, $first_set_keys, $field, $key, $post_type ) {
			if ( ! isset( $this->excluded_serialized_keys[ $post_type ] ) ) {
				return $column_settings;
			}
			foreach ( $this->excluded_serialized_keys[ $post_type ] as $field_key ) {
				if ( ! empty( $column_settings['serialized_field_original_key'] ) && $column_settings['serialized_field_original_key'] === $field_key ) {
					$column_settings = array();
				}
			}

			return $column_settings;
		}

		public function filter_map_data_for_saving( $new_value, $id, $real_key ) {
			if ( ! isset( self::$map_keys[ $real_key ] ) ) {
				return $new_value;
			}

			if ( empty( $new_value['address'] ) ) {
				$new_value['lat'] = '';
				$new_value['lng'] = '';
			} else {
				$google_maps_api_key = acf_get_setting( 'google_api_key' );

				if ( empty( $google_maps_api_key ) ) {
					throw new Exception( __( 'You need to configure your Google Maps API key to save the Google Maps columns. This is required by Advanced Custom Fields, <a href="https://www.advancedcustomfields.com/blog/google-maps-api-settings/" target="_blank">you can follow this tutorial</a>', 'vg_sheet_editor' ), E_USER_ERROR );
				}

				$geo_response = wp_remote_get( 'https://maps.googleapis.com/maps/api/geocode/json?key=' . $google_maps_api_key . '&language=en&address=' . urlencode( $new_value['address'] ) . '&sensor=false' );
				$geo_json     = wp_remote_retrieve_body( $geo_response );

				$geo = json_decode( $geo_json, true );
				if ( $geo['status'] === 'OK' ) {
					$new_value['lat'] = $geo['results'][0]['geometry']['location']['lat'];
					$new_value['lng'] = $geo['results'][0]['geometry']['location']['lng'];
				}
			}

			return $new_value;
		}

		public function prepare_gallery_value_for_display( $value, $post, $key, $column_settings ) {
			$post_type = VGSE()->helpers->get_provider_from_query_string();
			if ( ! isset( $this->gallery_field_keys[ $post_type ] ) || ! in_array( $key, $this->gallery_field_keys[ $post_type ] ) ) {
				return $value;
			}

			if ( ! empty( $value ) && is_array( $value ) ) {
				$value = implode( ',', $value );
			}

			return $value;
		}

		public function prepare_checkbox_value_for_display( $value, $post, $key, $column_settings ) {
			$real_key = preg_replace( '/_\d+$/', '', $key );
			if ( $key === $real_key || ! isset( self::$checkbox_keys[ $real_key ] ) ) {
				return $value;
			}
			$post_id = $post->ID;

			$raw_value = VGSE()->helpers->get_current_provider()->get_item_meta( $post_id, $real_key, true, 'read' );
			if ( empty( $raw_value ) || ! is_array( $raw_value ) ) {
				return $value;
			}
			$index           = (int) preg_replace( '/^.+_(\d+)$/', '$1', $key );
			$accepted_values = array_keys( self::$checkbox_keys[ $real_key ]['choices'] );
			$expected_value  = $accepted_values[ $index ];

			$value = ( in_array( $expected_value, $raw_value ) ) ? $expected_value : '';
			return $value;
		}


		public function filter_checkbox_column_settings( $column_settings, $serialized_field, $post_type ) {
			// If this serialized field is not an acf checkbox but uses a key known as
			// acf checkbox, return empty to not register the column
			$settings = $serialized_field->settings;
			if ( empty( $settings['is_acf_checkbox'] ) && in_array( $settings['sample_field_key'], array_keys( self::$checkbox_keys ) ) ) {
				return array();
			}

			if ( empty( $settings['is_acf_checkbox'] ) ) {
				return $column_settings;
			}

			$key_parts   = explode( '_', $column_settings['key'] );
			$field_index = (int) end( $key_parts );

			$choices_values                                    = array_keys( $settings['acf_choices'] );
			$column_settings['formatted']['type']              = 'checkbox';
			$column_settings['formatted']['checkedTemplate']   = $choices_values[ $field_index ];
			$column_settings['formatted']['uncheckedTemplate'] = '';
			$column_settings['formatted']['default_value']     = isset( $column_settings['default_value'] ) ? $column_settings['default_value'] : '';
			$column_settings['title']                          = $settings['column_title_prefix'] . ': ' . $settings['acf_choices'][ $choices_values[ $field_index ] ];

			// We ignore the default value set in ACF because it causes issues.
			// If we show the checkbox with the default value (i.e. checked), it will ignore it as checked when saving
			// because it would have the same value as initially loaded
			$column_settings['default_value']   = '';
			$column_settings['is_acf_checkbox'] = true;

			return $column_settings;
		}

		/**
		 * Get fields registered in Advanced Custom Fields for a specific post type
		 * @param str $post_type
		 * @return boolean|array
		 */
		public function get_acf_fields_objects_by_post_type( $post_type, $editor ) {
			// get field groups
			$acfs   = acf_get_field_groups();
			$fields = array();

			if ( $acfs ) {
				foreach ( $acfs as $acf ) {
					if ( empty( $acf['location'] ) ) {
						continue;
					}
					if ( empty( $acf['active'] ) ) {
						continue;
					}
					$post_type_fields = false;
					$location         = serialize( $acf['location'] );
					if ( $editor->provider->is_post_type ) {
						if ( $post_type === 'attachment' && strpos( $location, '"attachment"' ) ) {
							$post_type_fields = true;
						} elseif ( strpos( $location, '"post_type"' ) !== false && strpos( $location, '"' . $post_type . '"' ) !== false ) {
							$post_type_fields = true;
						} elseif ( strpos( $location, 'post_taxonomy' ) !== false ) {
							$one_level_locations     = call_user_func_array( 'array_merge', $acf['location'] );
							$post_taxonomy_locations = wp_list_filter(
								$one_level_locations,
								array(
									'param'    => 'post_taxonomy',
									'operator' => '==',
								)
							);
							foreach ( $post_taxonomy_locations as $post_taxonomy_location ) {
								$location_value = explode( ':', $post_taxonomy_location['value'] );
								$taxonomy_key   = current( $location_value );

								if ( taxonomy_exists( $taxonomy_key ) && in_array( $post_type, get_taxonomy( $taxonomy_key )->object_type, true ) ) {
									$post_type_fields = true;
									break;
								}
							}
						} else {
							$post_type_fields = array_merge(
								wp_list_filter(
									$acf['location'][0],
									array(
										'param'    => 'post_type',
										'operator' => '==',
										'value'    => $post_type,
									)
								),
								wp_list_filter(
									$acf['location'][0],
									array(
										'param'    => 'post_type',
										'operator' => '==',
										'value'    => 'all',
									)
								)
							);
						}
					} elseif ( $editor->provider->key === 'term' ) {
						$post_type_fields = array_merge(
							wp_list_filter(
								$acf['location'][0],
								array(
									'param'    => 'taxonomy',
									'operator' => '==',
									'value'    => $post_type,
								)
							),
							wp_list_filter(
								$acf['location'][0],
								array(
									'param'    => 'taxonomy',
									'operator' => '==',
									'value'    => 'all',
								)
							)
						);

					} elseif ( $editor->provider->key === 'user' ) {
						$post_type_fields = preg_match( '/(user_role|user_form)/', $location ) > 0;
					} else {
						$post_type_fields = true;
					}

					if ( ! empty( $post_type_fields ) ) {
						$fields[] = acf_get_fields( $acf );
					}
				}
			}

			return apply_filters( 'vg_sheet_editor/acf/fields', $fields, $post_type, $acfs );
		}

		/**
		 * Is acf plugin active
		 * @return boolean
		 */
		public function is_acf_plugin_active() {
			return function_exists( 'acf_get_field_groups' ) && class_exists( 'ACF' );
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if ( null == self::$instance ) {
				self::$instance = new WP_Sheet_Editor_ACF();
				self::$instance->init();
			}
			return self::$instance;
		}

		public function __set( $name, $value ) {
			$this->$name = $value;
		}

		public function __get( $name ) {
			return $this->$name;
		}

		/**
		 * Register columns in the spreadsheet
		 * @return null
		 */
		public function register_columns( $editor ) {

			if ( $editor->provider->key === 'user' ) {
				$post_types = array(
					'user',
				);
			} else {
				$post_types = $editor->args['enabled_post_types'];
			}

			if ( empty( $post_types ) ) {
				return;
			}

			foreach ( $post_types as $post_type ) {
				if ( empty( $post_type ) ) {
					continue;
				}
				$acf_post_type_groups = $this->get_acf_fields_objects_by_post_type( $post_type, $editor );
				if ( empty( $acf_post_type_groups ) ) {
					continue;
				}

				if ( ! isset( $this->gallery_field_keys[ $post_type ] ) ) {
					$this->gallery_field_keys[ $post_type ] = array();
				}
				if ( ! isset( $this->excluded_serialized_keys[ $post_type ] ) ) {
					$this->excluded_serialized_keys[ $post_type ] = array();
				}
				foreach ( $acf_post_type_groups as $acf_group_index => $acf_group ) {
					if ( empty( $acf_group ) ) {
						continue;
					}
					$this->_register_columns_for_acf_fields( $acf_group, $post_type, $editor );
				}
			}

		}

		public function get_taxonomy_cell( $post, $cell_key, $cell_args ) {
			$terms    = VGSE()->helpers->get_current_provider()->get_item_meta( $post->ID, $cell_key, true );
			$taxonomy = $cell_args['acf_field']['taxonomy'];
			$out      = '';
			if ( $terms ) {
				$out = VGSE()->data_helpers->prepare_post_terms_for_display(
					get_terms(
						array(
							'taxonomy'               => $taxonomy,
							'include'                => $terms,
							'update_term_meta_cache' => false,
							'hide_empty'             => false,
						)
					)
				);
			}
			return html_entity_decode( $out );
		}

		public function update_taxonomy_cell( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			$data_to_save = trim( $data_to_save );
			$taxonomy     = $cell_args['acf_field']['taxonomy'];
			if ( empty( $data_to_save ) ) {
				$terms_saved = '';
			} else {
				$terms_saved = VGSE()->data_helpers->prepare_post_terms_for_saving( $data_to_save, $taxonomy );
			}
			VGSE()->helpers->get_current_provider()->update_item_meta( $post_id, $cell_key, $terms_saved );

			if ( $cell_args['acf_field']['save_terms'] ) {
				wp_set_object_terms( $post_id, $terms_saved, $taxonomy );
			}
		}

		public function _prepare_date_for_display( $value, $post, $cell_key, $cell_args ) {
			if ( ! empty( $value ) ) {
				$timestamp = strtotime( $value );
				$value     = date( $cell_args['acf_field']['display_format'], $timestamp );
			}
			return $value;
		}

		public function _prepare_date_for_database( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			if ( ! empty( $data_to_save ) ) {
				$date = DateTime::createFromFormat( $cell_args['acf_field']['display_format'], $data_to_save );
				if ( $date ) {
					$data_to_save = $date->format( 'Ymd' );
				} else {
					$data_to_save = date( 'Ymd', strtotime( $data_to_save ) );
				}
			}
			return $data_to_save;
		}

		public function _prepare_gallery_for_database( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			if ( ! empty( $data_to_save ) ) {
				$data_to_save = array_filter( VGSE()->helpers->maybe_replace_urls_with_file_ids( explode( ',', $data_to_save ) ) );
			}
			return $data_to_save;
		}

		public function _register_columns_for_acf_fields( $acf_group, $post_type, $editor, $default_args = array() ) {
			$unnecessary_acf_field_keys = array_flip( array( 'wrapper', 'conditional_logic', 'class', 'instructions', 'aria-label', 'menu_order', 'maxlength', 'prepend', 'append', '_valid' ) );
			if ( empty( $default_args ) ) {
				$default_args = array(
					'allow_custom_format' => true,
				);
			}
			foreach ( $acf_group as $acf_field_index => $acf_field ) {
				$acf_field = array_diff_key( $acf_field, $unnecessary_acf_field_keys );

				// We don't register the text fields and unsupported fields because
				// they will appear automatically. The custom columns module registers
				// all custom fields as plain text. We only register fields with special format here.

				if ( in_array( $acf_field['type'], array( 'image', 'file' ) ) ) {
					$editor->args['columns']->register_item(
						$acf_field['name'],
						$post_type,
						array_merge(
							$default_args,
							array(
								'data_type'             => 'meta_data',
								'column_width'          => 150,
								'title'                 => $acf_field['label'],
								'type'                  => 'boton_gallery',
								'supports_formulas'     => true,
								'supports_sql_formulas' => false,
								'allow_plain_text'      => true,
								'acf_field'             => $acf_field,
							)
						)
					);
				} elseif ( $acf_field['type'] === 'date_picker' ) {
					$editor->args['columns']->register_item(
						$acf_field['name'],
						$post_type,
						array_merge(
							$default_args,
							array(
								'data_type'             => 'meta_data',
								'column_width'          => 150,
								'title'                 => $acf_field['label'],
								'type'                  => '',
								'supports_formulas'     => true,
								'supports_sql_formulas' => false,
								'allow_plain_text'      => true,
								'acf_field'             => $acf_field,
								'formatted'             => array(
									'type'                 => 'date',
									'customDatabaseFormat' => 'Ymd',
									'dateFormatPhp'        => $acf_field['display_format'],
									'correctFormat'        => true,
									'defaultDate'          => '',
									'datePickerConfig'     => array(
										'firstDay'       => 0,
										'showWeekNumber' => true,
										'numberOfMonths' => 1,
										'yearRange'      => array( 1900, (int) date( 'Y' ) + 20 ),
									),
								),
								'prepare_value_for_database' => array( $this, '_prepare_date_for_database' ),
								'prepare_value_for_display' => array( $this, '_prepare_date_for_display' ),
							)
						)
					);
				} elseif ( in_array( $acf_field['type'], array( 'text', 'textarea', 'number', 'range', 'email', 'url', 'password', 'oembed' ) ) ) {
					$value_type = 'text';
					if ( $acf_field['type'] === 'email' ) {
						$value_type = 'email';
					}
					$editor->args['columns']->register_item(
						$acf_field['name'],
						$post_type,
						array_merge(
							$default_args,
							array(
								'data_type'             => 'meta_data',
								'column_width'          => 200,
								'title'                 => $acf_field['label'],
								'type'                  => '',
								'supports_formulas'     => true,
								'supports_sql_formulas' => false,
								'allow_plain_text'      => true,
								'acf_field'             => $acf_field,
								'value_type'            => $value_type,
							)
						)
					);
				} elseif ( in_array( $acf_field['type'], array( 'relationship' ) ) ) {
					$this->excluded_serialized_keys[ $post_type ][] = $acf_field['name'];
					$editor->args['columns']->register_item(
						$acf_field['name'],
						$post_type,
						array_merge(
							$default_args,
							array(
								'data_type'             => 'meta_data',
								'column_width'          => 200,
								'title'                 => $acf_field['label'],
								'type'                  => '',
								'supports_formulas'     => true,
								'supports_sql_formulas' => false,
								'allow_plain_text'      => true,
								'prepare_value_for_display' => array( $this, 'prepare_relationship_for_display' ),
								'save_value_callback'   => array( $this, 'update_relationship_for_cell' ),
								'acf_field'             => $acf_field,
								'list_separation_character' => ',',
							)
						)
					);
				} elseif ( in_array( $acf_field['type'], array( 'wysiwyg' ) ) ) {
					$editor->args['columns']->register_item(
						$acf_field['name'],
						$post_type,
						array_merge(
							$default_args,
							array(
								'data_type'             => 'meta_data',
								'column_width'          => 200,
								'title'                 => $acf_field['label'],
								'type'                  => '',
								'supports_formulas'     => true,
								'supports_sql_formulas' => false,
								'allow_plain_text'      => true,
								'acf_field'             => $acf_field,
								'formatted'             => array(
									'renderer'          => 'wp_tinymce',
									'wpse_template_key' => 'tinymce_cell_template',
								),
							)
						)
					);
				} elseif ( in_array( $acf_field['type'], array( 'radio' ) ) || ( $acf_field['type'] === 'select' && ! $acf_field['multiple'] ) ) {
					$editor->args['columns']->register_item(
						$acf_field['name'],
						$post_type,
						array_merge(
							$default_args,
							array(
								'data_type'             => 'meta_data',
								'column_width'          => 200,
								'title'                 => $acf_field['label'],
								'type'                  => '',
								'supports_formulas'     => true,
								'supports_sql_formulas' => false,
								'allow_plain_text'      => true,
								'acf_field'             => $acf_field,
								'default_value'         => $acf_field['default_value'],
								'formatted'             => array(
									'editor'        => 'select',
									'selectOptions' => $acf_field['choices'],
								),
							)
						)
					);

				} elseif ( $acf_field['type'] === 'taxonomy' ) {
					$this->excluded_serialized_keys[ $post_type ][] = $acf_field['name'];
					$editor->args['columns']->register_item(
						$acf_field['name'],
						$post_type,
						array_merge(
							$default_args,
							array(
								'data_type'             => 'meta_data',
								'column_width'          => 200,
								'title'                 => $acf_field['label'],
								'type'                  => '',
								'supports_formulas'     => true,
								'supports_sql_formulas' => false,
								'allow_plain_text'      => true,
								'get_value_callback'    => array( $this, 'get_taxonomy_cell' ),
								'save_value_callback'   => array( $this, 'update_taxonomy_cell' ),
								'acf_field'             => $acf_field,
								'list_separation_character' => ',',
								'formatted'             => array(
									'editor'        => 'wp_chosen',
									'selectOptions' => array(),
									'chosenOptions' => array(
										'multiple'        => true,
										'search_contains' => true,
										'create_option'   => true,
										'skip_no_results' => true,
										'persistent_create_option' => true,
										'data'            => array(),
										'ajaxParams'      => array(
											'action'       => 'vgse_get_taxonomy_terms',
											'taxonomy_key' => $acf_field['taxonomy'],
										),
									),
								),
							)
						)
					);
				} elseif ( $acf_field['type'] === 'select' && $acf_field['multiple'] ) {
					$this->excluded_serialized_keys[ $post_type ][] = $acf_field['name'];
					$editor->args['columns']->register_item(
						$acf_field['name'],
						$post_type,
						array_merge(
							$default_args,
							array(
								'data_type'             => 'meta_data',
								'column_width'          => 200,
								'title'                 => $acf_field['label'],
								'type'                  => '',
								'supports_formulas'     => true,
								'supports_sql_formulas' => false,
								'allow_plain_text'      => true,
								'prepare_value_for_display' => array( $this, 'prepare_multi_select_for_display' ),
								'save_value_callback'   => array( $this, 'update_multi_select_for_cell' ),
								'acf_field'             => $acf_field,
								'list_separation_character' => ',',
							)
						)
					);
				} elseif ( in_array( $acf_field['type'], array( 'user' ) ) ) {
					$editor->args['columns']->register_item(
						$acf_field['name'],
						$post_type,
						array_merge(
							$default_args,
							array(
								'data_type'             => 'meta_data',
								'column_width'          => 200,
								'title'                 => $acf_field['label'],
								'type'                  => '',
								'supports_formulas'     => true,
								'supports_sql_formulas' => false,
								'allow_plain_text'      => true,
								'formatted'             => array(
									'type'   => 'autocomplete',
									'source' => 'searchUsers',
								),
								'prepare_value_for_display' => array( $this, '_prepare_user_for_display' ),
								'prepare_value_for_database' => array( $this, '_prepare_user_for_database' ),
								'acf_field'             => $acf_field,
							)
						)
					);
				} elseif ( in_array( $acf_field['type'], array( 'page_link', 'post_object' ) ) ) {
					$acf_field_post_type = null;
					if ( ! empty( $acf_field['post_type'] ) ) {
						$acf_field_post_type = is_array( $acf_field['post_type'] ) ? current( $acf_field['post_type'] ) : $acf_field['post_type'];
					}

					$formatted = array(
						'type'           => 'autocomplete',
						'source'         => 'searchPostByKeyword',
						'searchPostType' => $acf_field_post_type,
						'comment'        => array( 'value' => __( 'Enter a title', 'vg_sheet_editor' ) ),
					);
					if ( ! empty( $acf_field['multiple'] ) && $acf_field_post_type ) {
						$posts_count = array_sum( (array) wp_count_posts() );
						if ( $posts_count < 500 ) {
							$formatted = array(
								'editor'        => 'wp_chosen',
								'selectOptions' => array(),
								'chosenOptions' => array(
									'multiple'        => true,
									'search_contains' => true,
									'create_option'   => true,
									'skip_no_results' => true,
									'persistent_create_option' => true,
									'data'            => array(),
									'ajaxParams'      => array(
										'action'           => 'vgse_find_post_by_name',
										'search_post_type' => $acf_field_post_type,
									),
								),
							);
						} else {
							$formatted = array(
								'comment' => array( 'value' => sprintf( __( 'Enter titles separated by %s', 'vg_sheet_editor' ), VGSE()->helpers->get_term_separator() ) ),
							);
						}
						$this->excluded_serialized_keys[ $post_type ][] = $acf_field['name'];
					}
					$editor->args['columns']->register_item(
						$acf_field['name'],
						$post_type,
						array_merge(
							$default_args,
							array(
								'data_type'             => 'meta_data',
								'column_width'          => 200,
								'title'                 => $acf_field['label'],
								'type'                  => '',
								'supports_formulas'     => true,
								'supports_sql_formulas' => false,
								'allow_plain_text'      => true,
								'formatted'             => $formatted,
								'prepare_value_for_display' => array( $this, '_prepare_posts_for_display' ),
								'prepare_value_for_database' => array( $this, '_prepare_posts_for_database' ),
								'acf_field'             => $acf_field,
								'acf_field_search_post_type' => $acf_field_post_type,
							)
						)
					);
				} elseif ( in_array( $acf_field['type'], array( 'true_false' ) ) ) {
					$editor->args['columns']->register_item(
						$acf_field['name'],
						$post_type,
						array_merge(
							$default_args,
							array(
								'data_type'             => 'meta_data',
								'column_width'          => 200,
								'title'                 => $acf_field['label'],
								'type'                  => '',
								'supports_formulas'     => true,
								'supports_sql_formulas' => false,
								'allow_plain_text'      => true,
								'acf_field'             => $acf_field,
								'default_value'         => $acf_field['default_value'],
								'formatted'             => array(
									'type'              => 'checkbox',
									'checkedTemplate'   => 1,
									'uncheckedTemplate' => 0,
								),
								'default_value'         => 0,
							)
						)
					);
				} elseif ( in_array( $acf_field['type'], array( 'gallery' ) ) ) {
					$this->gallery_field_keys[ $post_type ][]       = $acf_field['name'];
					$this->excluded_serialized_keys[ $post_type ][] = $acf_field['name'];

					$editor->args['columns']->register_item(
						$acf_field['name'],
						$post_type,
						array_merge(
							$default_args,
							array(
								'data_type'             => 'meta_data',
								'column_width'          => 150,
								'title'                 => $acf_field['label'],
								'type'                  => 'boton_gallery_multiple',
								'supports_formulas'     => true,
								'supports_sql_formulas' => false,
								'allow_plain_text'      => true,
								'acf_field'             => $acf_field,
								'prepare_value_for_database' => array( $this, '_prepare_gallery_for_database' ),
								'prepare_value_for_display' => array( $this, 'prepare_gallery_value_for_display' ),
							)
						)
					);

				} elseif ( in_array( $acf_field['type'], array( 'link' ) ) ) {
					$sample_field = array(
						'title'  => '',
						'url'    => '',
						'target' => '',
					);

					new WP_Sheet_Editor_Infinite_Serialized_Field(
						array(
							'sample_field_key'    => $acf_field['name'],
							'sample_field'        => $sample_field,
							'column_width'        => 150,
							'column_title_prefix' => $acf_field['label'], // to remove the field key from the column title
							'level'               => 1,
							'allowed_post_types'  => array( $post_type ),
							'is_single_level'     => true,
							'allow_in_wc_product_variations' => false,
							'is_acf_link'         => true,
							'column_settings'     =>
							array_merge(
								$default_args,
								array(
									'acf_field' => $acf_field,
								)
							),
						)
					);
					$this->excluded_serialized_keys[ $post_type ][] = $acf_field['name'];
				} elseif ( in_array( $acf_field['type'], array( 'checkbox' ) ) && empty( VGSE()->options['acf_show_checkboxes_multi_dropdown'] ) ) {
					$sample_field = array();
					$choice_index = 0;
					foreach ( $acf_field['choices'] as $choice_key => $choice_label ) {
						$sample_field[] = ( is_array( $acf_field['default_value'] ) && isset( $acf_field['default_value'][ $choice_index ] ) ) ? $acf_field['default_value'][ $choice_index ] : '';
						$choice_index++;
					}

					new WP_Sheet_Editor_Infinite_Serialized_Field(
						array(
							'sample_field_key'    => $acf_field['name'],
							'sample_field'        => $sample_field,
							'column_width'        => 150,
							'column_title_prefix' => $acf_field['label'], // to remove the field key from the column title
							'level'               => 1,
							'allowed_post_types'  => array( $post_type ),
							'is_single_level'     => true,
							'allow_in_wc_product_variations' => false,
							'is_acf_checkbox'     => true,
							'acf_choices'         => $acf_field['choices'],
							'column_settings'     =>
							array_merge(
								$default_args,
								array(
									'acf_field' => $acf_field,
									'prepare_value_for_display' => array( $this, 'prepare_checkbox_value_for_display' ),
								)
							),
						)
					);
					self::$checkbox_keys[ $acf_field['name'] ]      = $acf_field;
					$this->excluded_serialized_keys[ $post_type ][] = $acf_field['name'];
				} elseif ( $acf_field['type'] === 'checkbox' && ! empty( VGSE()->options['acf_show_checkboxes_multi_dropdown'] ) ) {

					$select_options = array();
					foreach ( $acf_field['choices'] as $choice_key => $choice_label ) {
						$select_options[] = array(
							'id'    => $choice_key,
							'label' => $choice_label,
						);
					}
					$editor->args['columns']->register_item(
						$acf_field['name'],
						$post_type,
						array_merge(
							$default_args,
							array(
								'data_type'             => 'meta_data',
								'column_width'          => 200,
								'title'                 => $acf_field['label'],
								'type'                  => '',
								'supports_formulas'     => true,
								'supports_sql_formulas' => false,
								'allow_plain_text'      => true,
								'formatted'             => array(
									'editor'        => 'wp_chosen',
									'selectOptions' => $acf_field['choices'],
									'chosenOptions' => array(
										'multiple'        => true,
										'search_contains' => true,
									),
								),
								'prepare_value_for_display' => array( $this, 'prepare_multi_select_for_display' ),
								'save_value_callback'   => array( $this, 'update_multi_select_for_cell' ),
								'acf_field'             => $acf_field,
							)
						)
					);
				} elseif ( in_array( $acf_field['type'], array( 'google_map' ) ) ) {
					new WP_Sheet_Editor_Infinite_Serialized_Field(
						array(
							'sample_field_key'    => $acf_field['name'],
							'sample_field'        => array(
								'address' => '',
								'lat'     => '',
								'lng'     => '',
							),
							'column_width'        => 150,
							'column_title_prefix' => $acf_field['label'], // to remove the field key from the column title
							'level'               => 1,
							'allowed_post_types'  => array( $post_type ),
							'is_single_level'     => true,
							'allow_in_wc_product_variations' => false,
							'is_acf_map'          => true,
							'column_settings'     =>
							array_merge(
								$default_args,
								array(
									'acf_field' => $acf_field,
								)
							),
						)
					);
					self::$map_keys[ $acf_field['name'] ] = $acf_field;
				} elseif ( in_array( $acf_field['type'], array( 'repeater' ) ) && class_exists( 'acf_pro' ) ) {
					$this->repeater_keys[ $acf_field['name'] ] = array();

					// The parent repeater is not editable, it's used internally to keep count of internal rows
					$editor->args['columns']->remove_item( $acf_field['name'], $post_type );

					$repeater_count_values = $this->_get_repeater_count_values( $acf_field['name'], $post_type, $editor );

					$highest_count = ( empty( $repeater_count_values ) || empty( $repeater_count_values[0] ) ) ? 3 : (int) $repeater_count_values[0];

					// Save the subfield keys for processing the values during saving/reading
					foreach ( $acf_field['sub_fields'] as $subfield ) {
						$this->repeater_keys[ $acf_field['name'] ][] = '/' . preg_quote( $acf_field['name'], '/' ) . '_\d+_' . preg_quote( $subfield['name'], '/' ) . '$/';
					}

					// Register columns for each subfield
					for ( $i = 0; $i < $highest_count; $i++ ) {
						$repeater_field_group = array();
						foreach ( $acf_field['sub_fields'] as $subfield ) {
							$subfield['parent']     = array(
								'name'  => $acf_field['name'],
								'label' => $acf_field['label'],
								'key'   => $acf_field['key'],
							);
							$subfield['name']       = $acf_field['name'] . '_' . $i . '_' . $subfield['name'];
							$subfield['label']      = implode( ' : ', array( $acf_field['label'], $i + 1, $subfield['label'] ) );
							$repeater_field_group[] = $subfield;
						}
						$this->_register_columns_for_acf_fields(
							$repeater_field_group,
							$post_type,
							$editor,
							array(
								'allow_for_global_sort' => false,
								'allow_role_restrictions_in_columns_manager' => false,
								'allow_readonly_option_in_columns_manager' => false,
								'allow_custom_format'   => false,
							)
						);
					}
				} elseif ( in_array( $acf_field['type'], array( 'flexible_content' ) ) && class_exists( 'acf_pro' ) ) {
					$this->flexible_content_keys[ $acf_field['name'] ] = array();

					// The parent repeater is not editable, it's used internally to keep count of internal rows
					$editor->args['columns']->remove_item( $acf_field['name'], $post_type );
					// $repeater_count_values = $this->_get_repeater_count_values( $acf_field['name'], $post_type, $editor );

					// $highest_count = ( empty( $repeater_count_values ) || empty( $repeater_count_values[0] ) ) ? 3 : (int) $repeater_count_values[0];
					$highest_count = 3;

					// Remove the automatically registered columns, because they're duplicates as we use our own columns with special keys
					$registered_columns = array_keys( $editor->get_provider_items( $post_type ) );
					foreach ( $registered_columns as $column_key ) {
						foreach ( $acf_field['layouts'] as $layout ) {
							foreach ( $layout['sub_fields'] as $subfield ) {
								$regex = '/^' . preg_quote( $acf_field['name'], '/' ) . '_\d+_' . preg_quote( $subfield['name'], '/' ) . '$/';
								if ( preg_match( $regex, $column_key ) ) {
									$editor->args['columns']->remove_item( $column_key, $post_type );
								}
							}
						}
					}
					$editor->args['columns']->clear_cache( $post_type );

					for ( $i = 0; $i < $highest_count; $i++ ) {
						foreach ( $acf_field['layouts'] as $layout ) {
							if ( ! isset( $this->flexible_content_keys[ $acf_field['name'] ][ $layout['name'] ] ) ) {
								$this->flexible_content_keys[ $acf_field['name'] ][ $layout['name'] ] = array();
							}

							// Register columns for each subfield
							$repeater_field_group = array();
							foreach ( $layout['sub_fields'] as $subfield ) {

								// Save the subfield keys for processing the values during saving/reading
								$field_regex = '/^' . preg_quote( $acf_field['name'], '/' ) . '=\d+=' . preg_quote( $layout['name'] . '=' . $subfield['name'], '/' ) . '$/';
								$this->flexible_content_keys[ $acf_field['name'] ][ $layout['name'] ][] = $field_regex;

								$subfield['parent']                    = array(
									'name'  => $acf_field['name'],
									'label' => $acf_field['label'],
									'key'   => $acf_field['key'],
								);
								$subfield['name']                      = $acf_field['name'] . '=' . $i . '=' . $layout['name'] . '=' . $subfield['name'];
								$this->flexible_content_columns_keys[] = $subfield['name'];
								$this->flexible_content_columns_keys_parsed[ $subfield['name'] ] = array(
									'main_key'    => $acf_field['name'],
									'field_regex' => $field_regex,
									'layout_key'  => $layout['name'],
									'row_index'   => $i,
								);
								$subfield['label']      = implode( ' : ', array( $acf_field['label'], __( 'Row', 'acf' ) . ' ' . ( $i + 1 ), $layout['label'], $subfield['label'] ) );
								$repeater_field_group[] = $subfield;
							}
							$extra_args = array(
								'supports_sql_formulas'   => false,
								'allow_to_prefetch_value' => false,
								'allow_for_global_sort'   => false,
								'allow_role_restrictions_in_columns_manager' => false,
								'allow_readonly_option_in_columns_manager' => false,
								'allow_custom_format'     => false,
							);

							if ( ! empty( $acf_field['_clone'] ) ) {
								$extra_args['acf_clone_main_key']   = $acf_field['_clone'];
								$extra_args['acf_clone_main_name']  = trim( $acf_field['_name'], '_' . $acf_field['__name'] );
								$extra_args['acf_clone_field_key']  = $acf_field['_name'];
								$extra_args['acf_clone_field_name'] = $acf_field['__key'];
							}

							$this->_register_columns_for_acf_fields(
								$repeater_field_group,
								$post_type,
								$editor,
								$extra_args
							);
						}
					}
				} elseif ( in_array( $acf_field['type'], array( 'group' ) ) ) {
					$this->group_keys[ $acf_field['name'] ] = array();

					// The parent repeater is not editable, it's used internally to keep count of internal rows
					$editor->args['columns']->remove_item( $acf_field['name'], $post_type );

					// Save the subfield keys for processing the values during saving/reading
					foreach ( $acf_field['sub_fields'] as $subfield ) {
						$this->group_keys[ $acf_field['name'] ][] = $acf_field['name'] . '_' . $subfield['name'];
					}

					// Register columns for each subfield
					$field_group = array();
					foreach ( $acf_field['sub_fields'] as $subfield ) {
						$subfield['parent'] = array(
							'name'  => $acf_field['name'],
							'label' => $acf_field['label'],
							'key'   => $acf_field['key'],
						);
						$subfield['name']   = $acf_field['name'] . '_' . $subfield['name'];
						$subfield['label']  = implode( ' : ', array( $acf_field['label'], $subfield['label'] ) );
						$field_group[]      = $subfield;
					}
					$this->_register_columns_for_acf_fields( $field_group, $post_type, $editor );
				}
			}
		}

		public function _prepare_user_for_database( $post_id, $cell_key, $data_to_save, $post_type, $column_settings, $spreadsheet_columns ) {
			if ( empty( $data_to_save ) ) {
				return $data_to_save;
			}
			$out = '';
			if ( empty( $column_settings['acf_field']['multiple'] ) ) {
				$user = get_user_by( 'login', $data_to_save );
				$out  = $user ? $user->ID : '';
			} else {
				$user_logins = array_filter( explode( ',', $data_to_save ) );
				$out         = array();
				foreach ( $user_logins as $user_login ) {
					$user = get_user_by( 'login', $user_login );
					if ( $user ) {
						$out[] = $user->ID;
					}
				}
			}
			return $out;
		}

		public function _prepare_user_for_display( $value, $post, $column_key, $column_settings ) {
			if ( empty( $value ) ) {
				return '';
			}

			$out = '';
			if ( empty( $column_settings['acf_field']['multiple'] ) ) {
				$user = get_user_by( 'ID', $value );
				$out  = $user ? $user->user_login : '';
			} elseif ( is_array( $value ) ) {
				$user_logins = array();
				foreach ( $value as $user_id ) {
					$user = get_user_by( 'ID', $user_id );
					if ( $user ) {
						$user_logins[] = $user->user_login;
					}
				}
				$out = implode( ', ', $user_logins );
			}
			return $out;
		}

		public function _prepare_posts_for_database( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			$out = '';
			if ( empty( $data_to_save ) ) {
				return $out;
			}

			if ( empty( $cell_args['acf_field']['multiple'] ) ) {
				$out = is_numeric( $data_to_save ) ? $data_to_save : VGSE()->data_helpers->get_post_id_from_title( $data_to_save, $cell_args['acf_field_search_post_type'] );
			} else {
				$titles = array_map( 'trim', explode( VGSE()->helpers->get_term_separator(), $data_to_save ) );
				$out    = array();
				foreach ( $titles as $title ) {
					$out[] = VGSE()->data_helpers->get_post_id_from_title( $title, $cell_args['acf_field_search_post_type'] );
				}
			}
			return $out;
		}

		public function _prepare_posts_for_display( $value, $post, $column_key, $column_settings ) {
			global $wpdb;
			$out = '';
			if ( ! empty( $value ) ) {
				if ( empty( $column_settings['acf_field']['multiple'] ) && is_numeric( $value ) ) {
					$out = html_entity_decode( get_the_title( (int) $value ) );
				} elseif ( ! empty( $column_settings['acf_field']['multiple'] ) && is_array( $value ) ) {

					$ids                       = array_filter( array_map( 'intval', $value ) );
					$ids_in_query_placeholders = implode( ', ', array_fill( 0, count( $ids ), '%d' ) );
					$raw_titles                = $wpdb->get_col( $wpdb->prepare( "SELECT post_title FROM $wpdb->posts WHERE ID IN ($ids_in_query_placeholders)", $ids ) );
					$titles                    = ! empty( $raw_titles ) ? array_map( 'html_entity_decode', $raw_titles ) : array();
					$out                       = implode( VGSE()->helpers->get_term_separator() . ' ', $titles );
				}
			}
			return $out;
		}

		public function prepare_relationship_for_display( $value, $post, $column_key, $column_settings ) {
			global $wpdb;
			$titles = '';
			if ( is_array( $value ) && ! empty( $value ) ) {
				$ids_in_query_placeholders = implode( ', ', array_fill( 0, count( $value ), '%d' ) );
				$raw_titles                = array_unique( $wpdb->get_col( $wpdb->prepare( "SELECT post_title FROM $wpdb->posts WHERE ID IN ($ids_in_query_placeholders)  ORDER BY FIELD(ID, $ids_in_query_placeholders) ", array_merge( $value, $value ) ) ) );
				$titles                    = implode( ', ', $raw_titles );
			}
			return $titles;
		}

		public function update_relationship_for_cell( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			global $wpdb;
			$titles = array_map( 'trim', explode( ',', $data_to_save ) );
			$ids    = '';
			if ( ! empty( $titles ) ) {
				$titles_in_query_placeholders = implode( ', ', array_fill( 0, count( $titles ), '%s' ) );
				$sql                          = "SELECT ID FROM $wpdb->posts WHERE post_title IN ($titles_in_query_placeholders) ";
				if ( ! empty( $cell_args['acf_field']['post_type'] ) ) {
					$post_types_in_query_placeholders = implode( ', ', array_fill( 0, count( $cell_args['acf_field']['post_type'] ), '%s' ) );
					$sql                             .= " AND post_type IN ($post_types_in_query_placeholders) ";
					$merged_variables                 = array_merge( $titles, $cell_args['acf_field']['post_type'], $titles );
				} else {
					$merged_variables = array_merge( $titles, $titles );
				}
				$sql         .= " ORDER BY FIELD(post_title, $titles_in_query_placeholders) ";
				$prepared_sql = $wpdb->prepare( $sql, $merged_variables );
				$ids          = array_unique( $wpdb->get_col( $prepared_sql ) );
			}
			VGSE()->helpers->get_current_provider()->update_item_meta( $post_id, $cell_key, $ids );
		}

		public function prepare_multi_select_for_display( $value, $post, $column_key, $column_settings ) {
			$titles = '';
			if ( is_array( $value ) && ! empty( $value ) ) {
				$raw_titles = array();
				foreach ( $value as $key ) {
					if ( ! empty( $column_settings['acf_field']['choices'][ $key ] ) ) {
						$raw_titles[] = $column_settings['acf_field']['choices'][ $key ];
					}
				}

				$separator = VGSE()->helpers->get_term_separator();
				$titles    = implode( $separator . ' ', $raw_titles );
			}
			return $titles;
		}

		public function update_multi_select_for_cell( $post_id, $cell_key, $data_to_save, $post_type, $column_settings, $spreadsheet_columns ) {
			$separator = VGSE()->helpers->get_term_separator();
			$titles    = array_map( 'trim', explode( $separator, $data_to_save ) );
			$ids       = '';
			if ( ! empty( $titles ) ) {
				$ids = array();
				foreach ( $titles as $title ) {
					$key = array_search( $title, $column_settings['acf_field']['choices'] );
					if ( isset( $column_settings['acf_field']['choices'][ $title ] ) ) {
						$ids[] = $title;
					} elseif ( $key !== false ) {
						$ids[] = $key;
					} else {
						continue;
					}
				}
				$ids = array_unique( $ids );
			}
			if ( empty( $ids ) ) {
				$ids = '';
			}
			VGSE()->helpers->get_current_provider()->update_item_meta( $post_id, $cell_key, $ids );
		}

		public function _get_repeater_count_values( $key, $post_type, $editor ) {
			$is_flexible_child_field = empty( $this->remove_flexible_content_columns_from_prefetch( array( $key ), $post_type ) );
			if ( $is_flexible_child_field ) {
				return false;
			}

			$cache_key             = 'vgse_acf_repeater_values' . $key . $post_type;
			$repeater_count_values = get_transient( $cache_key );
			if ( method_exists( VGSE()->helpers, 'can_rescan_db_fields' ) && VGSE()->helpers->can_rescan_db_fields( $post_type ) ) {
				$repeater_count_values = false;
			}

			if ( ! $repeater_count_values ) {
				$repeater_count_values = array_filter( array_map( 'maybe_unserialize', $editor->provider->get_meta_field_unique_values( $key, $post_type ) ) );
				set_transient( $cache_key, $repeater_count_values, DAY_IN_SECONDS );
			}
			return $repeater_count_values;
		}

	}

}

if ( ! function_exists( 'WP_Sheet_Editor_ACF_Obj' ) ) {

	function WP_Sheet_Editor_ACF_Obj() {
		return WP_Sheet_Editor_ACF::get_instance();
	}
}


add_action( 'vg_sheet_editor/initialized', 'WP_Sheet_Editor_ACF_Obj' );
