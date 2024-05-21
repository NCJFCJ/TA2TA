<?php defined( 'ABSPATH' ) || exit;
if ( ! class_exists( 'WPSE_WC_Products_Universal_Sheet' ) ) {

	class WPSE_WC_Products_Universal_Sheet {

		private static $instance = false;

		private function __construct() {

		}

		public function init() {

			// Export
			add_filter( 'vg_sheet_editor/export/allowed_column_keys', array( $this, 'allow_wc_core_columns_keys_for_export' ), 10, 3 );
			add_filter( 'vg_sheet_editor/export/final_headers', array( $this, 'add_friendly_column_headers_for_export' ), 10, 2 );
			add_action( 'vg_sheet_editor/export/pre_cleanup', array( $this, 'add_special_columns_data_to_export' ), 10, 4 );
			add_filter( 'vg_sheet_editor/export/columns', array( $this, 'add_special_columns_to_export_list' ), 10, 2 );
			add_action( 'vg_sheet_editor/export/columns', array( $this, 'remove_core_fields_from_export_list' ), 20, 2 );
			add_filter( 'vg_sheet_editor/columns/all_items', array( $this, 'add_export_keys' ) );
			add_filter( 'vg_sheet_editor/export/existing_file_keys', array( $this, 'convert_file_labels_to_keys_for_export' ), 10, 4 );
			add_filter( 'vg_sheet_editor/export/is_not_supported', array( $this, 'is_import_export_supported' ), 10, 2 );
			add_filter( 'vg_sheet_editor/load_rows/full_output', array( $this, 'sort_columns_after_export' ), 20, 4 );
			add_filter( 'vg_sheet_editor/load_rows/full_output', array( $this, 'align_variation_attributes_with_parent_attributes_after_export' ), 20, 4 );

			// Import
			add_action( 'vg_sheet_editor/import/before_available_columns_options', array( $this, 'add_special_columns_to_import_list' ) );
			add_action( 'vg_sheet_editor/import/columns', array( $this, 'add_special_columns_to_api_import_list' ), 10, 2 );
			add_action( 'vg_sheet_editor/import/columns', array( $this, 'remove_core_fields_from_export_list' ), 20, 2 );
			add_filter( 'vg_sheet_editor/import/wp_check/available_columns_options', array( $this, 'filter_wp_check_options_for_import' ), 10, 2 );
			add_filter( 'vg_sheet_editor/save_rows/row_data_before_save', array( $this, 'save_columns_data_during_import' ), 10, 5 );
			add_filter( 'vg_sheet_editor/save_rows/incoming_data', array( $this, 'maybe_create_template_products' ), 10, 2 );
			add_action( 'vg_sheet_editor/save_rows/after_saving_rows', array( $this, 'remove_placeholder_products_after_import' ), 10, 4 );
			add_action( 'vg_sheet_editor/save_rows/fatal_error_handler', array( $this, 'remove_placeholder_products_after_failed_save' ), 10, 3 );
			add_filter( 'vg_sheet_editor/import/is_not_supported', array( $this, 'is_import_export_supported' ), 10, 2 );
			add_filter( 'vg_sheet_editor/import/after_data_sources', array( $this, 'render_import_sample_csv_link' ) );
			add_filter( 'vg_sheet_editor/import/find_post_id', array( $this, 'find_product_id_for_import' ), 10, 6 );
			add_action( 'vg_sheet_editor/import/after_advanced_options', array( $this, 'import_after_advanced_options' ) );
			add_filter( 'sanitize_taxonomy_name', array( $this, 'prevent_long_attribute_name_error_during_import' ) );

			// Google Sheets
			add_filter( 'vg_sheet_editor/google_sheets/sync/task_args_to_get_rows', array( $this, 'include_variations_on_sync_to_google_sheets' ), 10, 3 );

		}

		public function include_variations_on_sync_to_google_sheets( $task_args, $ids, $job ) {

			if ( $job['sheet_key'] === 'product' ) {
				$filters = json_decode( wp_unslash( $job['task_args']['filters'] ), true );
				if ( ! empty( $filters['wc_display_variations'] ) && $filters['wc_display_variations'] === 'yes' ) {
					$new_task_filters                          = json_decode( wp_unslash( $task_args['filters'] ), true );
					$new_task_filters['wc_display_variations'] = 'yes';
					$task_args['filters']                      = wp_json_encode( $new_task_filters );
				}
			}
			return $task_args;
		}
		public function _file_contains_word( $file_path, $word ) {
			// Loop in batches of 100 rows to prevent memory leaks
			$position = 0;
			$csv_data = WPSE_CSV_API_Obj()->get_rows( $file_path, ',', false, 100, $position );
			while ( $csv_data['rows'] ) {
				if ( empty( $csv_data['rows'] ) ) {
					break;
				}
				$rows_text = json_encode( $csv_data['rows'] );
				if ( stripos( $rows_text, $word ) !== false ) {
					return true;
				}
				$position = $csv_data['file_position'];
				$csv_data = WPSE_CSV_API_Obj()->get_rows( $file_path, ',', false, 100, $position );
			}
			return false;
		}

		public function align_variation_attributes_with_parent_attributes_after_export( $out, $wp_query_args, $spreadsheet_columns, $clean_data ) {
			global $wpdb;
			if ( empty( $out['export_complete'] ) || $wp_query_args['post_type'] !== VGSE()->WC->post_type ) {
				return $out;
			}

			$file_path = WPSE_CSV_API_Obj()->exports_dir . $out['export_file_name'] . '.csv';
			if ( ! file_exists( $file_path ) ) {
				return $out;
			}

			if ( ! $this->_file_contains_word( $file_path, 'variation' ) ) {
				return $out;
			}
			$parent_attributes_index    = array();
			$variation_attributes_index = array();

			// Sort the CSV data using a while loop in batches of 100 rows to prevent memory leaks
			$position                 = 0;
			$csv_data                 = WPSE_CSV_API_Obj()->get_rows( $file_path, ',', false, 100, $position );
			$parent_column_name       = __( 'Parent', 'woocommerce' );
			$type_column_name         = __( 'Type', 'woocommerce' );
			$attribute_name_column    = __( 'Attribute %d name', 'woocommerce' );
			$attribute_value_column   = __( 'Attribute %d value(s)', 'woocommerce' );
			$attribute_visible_column = __( 'Attribute %d visible', 'woocommerce' );
			$attribute_global_column  = __( 'Attribute %d global', 'woocommerce' );
			$attribute_column_names   = compact( 'attribute_name_column', 'attribute_value_column', 'attribute_visible_column', 'attribute_global_column' );
			$new_file_path            = str_replace( '.csv', '-variations-aligned.csv', $file_path );
			while ( $csv_data['rows'] ) {
				if ( empty( $csv_data['rows'] ) ) {
					break;
				}
				foreach ( $csv_data['rows'] as $row_index => $row ) {
					$is_parent    = ( ! empty( $row[ $type_column_name ] ) && $row[ $type_column_name ] !== 'variation' ) || empty( $row[ $parent_column_name ] );
					$is_variation = ! $is_parent;

					if ( $is_parent && ! isset( $parent_attributes_index[ $row['record_id'] ] ) ) {
						$parent_attributes_index[ $row['record_id'] ] = array();
					}
					if ( $is_variation && ! isset( $variation_attributes_index[ $row['record_id'] ] ) ) {
						$variation_attributes_index[ $row['record_id'] ] = array();
					}
					foreach ( $row as $column_key => $column_value ) {
						$column_key_with_number_placeholder = preg_replace( '/\d+/', '%d', $column_key );
						if ( ! in_array( $column_key_with_number_placeholder, $attribute_column_names, true ) ) {
							continue;
						}
						// If this is a parent row, add the attribute names to the index
						if ( $column_key_with_number_placeholder === $attribute_name_column ) {
							$attribute_number = (int) preg_replace( '/[^\d]/', '', $column_key );
							$attribute_data   = array(
								$attribute_name_column    => $row[ str_replace( '%d', $attribute_number, $attribute_name_column ) ],
								$attribute_value_column   => $row[ str_replace( '%d', $attribute_number, $attribute_value_column ) ],
								$attribute_visible_column => $row[ str_replace( '%d', $attribute_number, $attribute_visible_column ) ],
								$attribute_global_column  => $row[ str_replace( '%d', $attribute_number, $attribute_global_column ) ],
							);

							if ( $is_parent ) {
								$parent_attributes_index[ $row['record_id'] ][ $column_value ] = array_fill_keys( array_keys( $attribute_data ), '' );
							} elseif ( $column_value ) {
								$variation_attributes_index[ $row['record_id'] ][ $column_value ] = $attribute_data;
							}
						}
					}

					// If this is a variation row
					if ( $is_variation ) {
						$variation_id = (int) $row['record_id'];
						$parent_id    = (int) $wpdb->get_var( $wpdb->prepare( "SELECT post_parent FROM $wpdb->posts WHERE post_type = 'product_variation' AND ID = %d", $variation_id ) );
						if ( ! $parent_id || ! isset( $parent_attributes_index[ $parent_id ] ) ) {
							continue;
						}

						$sorted_variation_attributes = array_merge( $parent_attributes_index[ $parent_id ], $variation_attributes_index[ $variation_id ] );
						$attribute_number            = 1;
						foreach ( $sorted_variation_attributes as $attribute ) {
							$attribute_string               = wp_json_encode( $attribute );
							$attribute_string               = str_replace( '%d', $attribute_number, $attribute_string );
							$row                            = array_merge( $row, json_decode( $attribute_string, true ) );
							$csv_data['rows'][ $row_index ] = $row;
							$attribute_number++;
						}
					}
				}

				WPSE_CSV_API_Obj()->_array_to_csv( $csv_data['rows'], $new_file_path );
				$position = $csv_data['file_position'];
				$csv_data = WPSE_CSV_API_Obj()->get_rows( $file_path, ',', false, 100, $position );
			}
			// Replace the old file with the new file
			unlink( $file_path );
			rename( $new_file_path, $file_path );

			return $out;
		}

		public function sort_columns_after_export( $out, $wp_query_args, $spreadsheet_columns, $clean_data ) {
			if ( empty( $out['export_complete'] ) || empty( $out['export_file_name'] ) || $wp_query_args['post_type'] !== VGSE()->WC->post_type ) {
				return $out;
			}

			$file_path = WPSE_CSV_API_Obj()->exports_dir . $out['export_file_name'] . '.csv';
			if ( ! file_exists( $file_path ) ) {
				return $out;
			}

			$first_lines = WPSE_CSV_API_Obj()->get_rows( $file_path, ',', false, 1 );
			if ( empty( $first_lines['rows'] ) ) {
				return $out;
			}
			$first_row          = $first_lines['rows'][0];
			$sorted_column_keys = array_map( 'trim', explode( ',', $clean_data['custom_enabled_columns'] ) );

			$exporter = $this->get_exporter();
			$exporter->set_column_names( wp_unslash( $this->get_exporter()->get_default_column_names() ) );
			$wc_api_column_headers = $exporter->get_column_names();

			$sorted_column_headers = array_merge( array_flip( $sorted_column_keys ), array_intersect_key( $wc_api_column_headers, array_flip( $sorted_column_keys ) ), array_intersect_key( wp_list_pluck( $spreadsheet_columns, 'title', 'key' ), array_flip( $sorted_column_keys ) ) );
			$wc_repeatable_columns = array(
				'attributes' => array(
					trim( __( 'Attribute %d name', 'woocommerce' ) ),
					trim( __( 'Attribute %d value(s)', 'woocommerce' ) ),
					trim( __( 'Attribute %d visible', 'woocommerce' ) ),
					trim( __( 'Attribute %d global', 'woocommerce' ) ),
					trim( __( 'Attribute %d default', 'woocommerce' ) ),
				),
				'downloads'  => array(
					__( 'Download %d name', 'woocommerce' ),
					__( 'Download %d ID', 'woocommerce' ),
					__( 'Download %d URL', 'woocommerce' ),
				),
			);
			// Generate an array with the list of repeatable columns found in the CSV file
			// i.e. group all the attribute and downloads columns
			$grouped_columns = array();
			if ( array_intersect( array_keys( $wc_repeatable_columns ), $sorted_column_keys ) ) {
				foreach ( array_keys( $first_row ) as $row_key ) {
					foreach ( $wc_repeatable_columns as $wc_repeatable_column_group => $wc_group_columns ) {
						if ( ! in_array( $wc_repeatable_column_group, $sorted_column_keys, true ) ) {
							continue;
						}
						if ( ! isset( $grouped_columns[ $wc_repeatable_column_group ] ) ) {
							$grouped_columns[ $wc_repeatable_column_group ] = array();
						}
						foreach ( $wc_group_columns as $index => $wc_repeatable_column ) {
							$wc_repeatable_column_regex = str_replace( array( '%d', '(', ')' ), array( '\d+', '\(', '\)' ), $wc_repeatable_column );
							if ( preg_match( '/' . $wc_repeatable_column_regex . '/', $row_key ) ) {
								$grouped_columns[ $wc_repeatable_column_group ][] = $row_key;
							}
						}
					}
				}
				foreach ( $wc_repeatable_columns as $wc_repeatable_column_group => $wc_group_columns ) {
					if ( empty( $grouped_columns[ $wc_repeatable_column_group ] ) ) {
						unset( $grouped_columns[ $wc_repeatable_column_group ] );
						unset( $sorted_column_headers[ $wc_repeatable_column_group ] );
					}
				}
			}
			$final_column_headers = array();
			foreach ( $sorted_column_headers as $column_key => $label ) {
				$final_column_headers[] = is_int( $label ) ? $column_key : $label;
			}
			// Replace the "attributes" and "downloads" keys in the sorted columns with all
			// the real attribute and downloads columns found in the CSV file
			foreach ( $grouped_columns as $group_key => $columns ) {
				$group_key_position = array_search( $group_key, $final_column_headers, true );
				if ( $group_key_position !== false ) {
					$final_column_headers = $this->_insert_values_in_array_position( $final_column_headers, $group_key_position, $columns );
				}
			}
			if ( ! in_array( 'record_id', $final_column_headers, true ) ) {
				$final_column_headers[] = 'record_id';
			}

			$new_file_path = WPSE_CSV_API_Obj()->exports_dir . $out['export_file_name'] . '-sorted.csv';

			$csv_sorted = $this->_sort_csv_rows( $file_path, $new_file_path, $final_column_headers );
			// If the csv data was sorted successfully, replace the old file with the new file
			if ( $csv_sorted ) {
				unlink( $file_path );
				rename( $new_file_path, $file_path );
			}

			return $out;
		}

		public function _insert_values_in_array_position( $array, $position, $values ) {
			$out = array();
			foreach ( $array as $key => $value ) {
				if ( $key !== $position ) {
					$out[] = $value;
				} else {
					foreach ( $values as $value_key => $value_value ) {
						$out[] = $value_value;
					}
				}
			}
			return $out;
		}

		public function _sort_csv_rows( $file_path, $new_file_path, $sorted_headers ) {
			if ( $file_path === $new_file_path || ! $new_file_path ) {
				return false;
			}
			// Sort the CSV data using a while loop in batches of 100 rows to prevent memory leaks
			$position = 0;
			$csv_data = WPSE_CSV_API_Obj()->get_rows( $file_path, ',', false, 100, $position );
			while ( $csv_data['rows'] ) {
				if ( empty( $csv_data['rows'] ) ) {
					break;
				}
				$sorted_headers_prepared = array_flip( $sorted_headers );
				foreach ( $csv_data['rows'] as $row_index => $row ) {
					$csv_data['rows'][ $row_index ] = array_merge( $sorted_headers_prepared, array_intersect_key( $row, $sorted_headers_prepared ) );
				}

				if ( method_exists( WPSE_CSV_API_Obj(), '_str_putcsv' ) ) {
					WPSE_CSV_API_Obj()->_array_to_csv( $csv_data['rows'], $new_file_path, WPSE_CSV_API_Obj()->_str_putcsv( $sorted_headers ) );
				} else {
					// Backwards compatibility
					WPSE_CSV_API_Obj()->_array_to_csv( $csv_data['rows'], $new_file_path, implode( ',', $sorted_headers ) );
				}
				$position = $csv_data['file_position'];
				$csv_data = WPSE_CSV_API_Obj()->get_rows( $file_path, ',', false, 100, $position );
			}
			return true;
		}

		public function prevent_long_attribute_name_error_during_import( $sanitized_name ) {
			if ( strlen( $sanitized_name ) > 28 && doing_action( 'wp_ajax_vgse_import_csv' ) ) {
				$sanitized_name = substr( $sanitized_name, 0, 25 );
			}

			return $sanitized_name;
		}

		public function import_after_advanced_options( $post_type ) {
			if ( $post_type !== VGSE()->WC->post_type ) {
				return;
			}
			?>
			<div class="field">
				<label><input type="checkbox" name="skip_broken_images" class="skip-broken-images"/> <?php _e( 'Skip broken images?', 'vg_sheet_editor' ); ?> <a href="#" data-wpse-tooltip="right" aria-label="<?php _e( 'By the default, the import stops when a product references a broken images and you have to correct the issue in the file and start a new import. Enable this option and we will let you import products without images when the image url is broken', 'vg_sheet_editor' ); ?>">( ? )</a></label>								
			</div>
			<?php
		}

		public function get_wc_product_core_columns_for_export() {
			$columns               = $this->get_exporter()->get_default_column_names();
			$columns['downloads']  = __( 'Downloads', 'woocommerce' );
			$columns['attributes'] = __( 'Attributes', 'woocommerce' );

			$columns = apply_filters( 'vg_sheet_editor/woocommerce/export/columns_list', $columns );
			return $columns;
		}

		public function add_special_columns_to_export_list( $columns, $post_type ) {
			if ( $post_type !== VGSE()->WC->post_type ) {
				return $columns;
			}

			$sheet_to_wc_keys = array_flip( VGSE()->WC->core_to_woo_importer_columns_list );
			$special_columns  = $this->get_wc_product_core_columns_for_export();
			$new_columns      = array();
			foreach ( $special_columns as $column_id => $column_name ) {
				$old_column_args           = ( isset( $sheet_to_wc_keys[ $column_id ] ) && isset( $columns[ $sheet_to_wc_keys[ $column_id ] ] ) ) ? $columns[ $sheet_to_wc_keys[ $column_id ] ] : array();
				$new_columns[ $column_id ] = array_merge(
					$old_column_args,
					array(
						'title' => $column_name,
						'key'   => $column_id,
					)
				);
			}

			$out = array_merge( $new_columns, $columns );
			return $out;
		}

		public function add_special_columns_to_api_import_list( $columns, $post_type ) {
			if ( $post_type !== VGSE()->WC->post_type ) {
				return $columns;
			}

			$sheet_to_wc_keys    = array_flip( VGSE()->WC->core_to_woo_importer_columns_list );
			$importer_controller = $this->get_importer_controller();
			$new_columns         = array();
			foreach ( $importer_controller->get_mapping_options( '' ) as $key => $value ) {
				if ( is_array( $value ) ) {
					foreach ( $value['options'] as $sub_key => $sub_value ) {
						$new_columns[ $sub_key ] = array(
							'title' => $sub_value,
							'key'   => $sub_key,
						);
					}
				} else {
					$new_columns[ $key ] = array(
						'title' => $value,
						'key'   => $key,
					);
				}
			}
			foreach ( $new_columns as $column_id => $column_args ) {
				$old_column_args           = ( isset( $sheet_to_wc_keys[ $column_id ] ) && isset( $columns[ $sheet_to_wc_keys[ $column_id ] ] ) ) ? $columns[ $sheet_to_wc_keys[ $column_id ] ] : array();
				$new_columns[ $column_id ] = array_merge( $old_column_args, $column_args );
			}

			$out = array_merge( $new_columns, $columns );
			return $out;
		}

		public function add_export_keys( $columns ) {
			if ( ! isset( $columns[ VGSE()->WC->post_type ] ) ) {
				return $columns;
			}
			foreach ( $columns[ VGSE()->WC->post_type ] as $column_key => $column ) {
				$export_key = null;

				if ( isset( VGSE()->WC->core_to_woo_importer_columns_list[ $column_key ] ) ) {
					$export_key = VGSE()->WC->core_to_woo_importer_columns_list[ $column_key ];
				}
				if ( ! empty( $column['export_key'] ) ) {
					$export_key = $column['export_key'];
				}
				if ( $export_key && $column_key !== $export_key ) {
					$columns[ VGSE()->WC->post_type ][ $column_key ]['export_key'] = $export_key;
					VGSE()->WC->core_to_woo_importer_columns_list[ $column_key ]   = $export_key;
					if ( ! in_array( $column_key, VGSE()->WC->core_columns_list, true ) ) {
						VGSE()->WC->core_columns_list[] = $column_key;
					}
				}
			}
			return $columns;
		}

		public function filter_wp_check_options_for_import( $columns, $post_type ) {

			if ( $post_type === VGSE()->WC->post_type ) {
				// The array elements contain the <option> html, so we use str_replace to change the option key
				$new_columns = array(
					'ID'   => $columns['ID'],
					'name' => str_replace( 'post_title', 'name', $columns['post_title'] ),
				);
				if ( isset( $columns['_sku'] ) ) {
					$new_columns['sku'] = str_replace( '_sku', 'sku', $columns['_sku'] );
				}
				$columns = $new_columns;
			}
			return $columns;
		}

		// Note. The $row uses the same keys as the WooCommerce core importer
		// and in order to receive those keys, we need to use the wp filter to rename the
		// option keys selected by the user: vg_sheet_editor/import/wp_check/available_columns_options.
		// See filter_wp_check_options_for_import() se example
		public function find_product_id_for_import( $post_id, $row, $post_type, $meta_query, $writing_type, $check_wp_fields ) {

			if ( $post_type === VGSE()->WC->post_type ) {
				$post_id = 0;

				if ( ! empty( $row['ID'] ) && in_array( 'ID', $check_wp_fields ) ) {
					$post_id = get_post_status( $row['ID'] ) ? (int) $row['ID'] : 0;
				} elseif ( ! empty( $row['sku'] ) && in_array( 'sku', $check_wp_fields ) ) {
					$post_id = (int) wc_get_product_id_by_sku( str_replace( '&', 'and', $row['sku'] ) );
				} elseif ( ! empty( $row['name'] ) && in_array( 'name', $check_wp_fields ) ) {
					$post = VGSE()->helpers->get_page_by_title( $row['name'], $post_type );
					if ( $post ) {
						$post_id = $post->ID;
					}
				}
			}
			return $post_id;
		}

		public function render_import_sample_csv_link( $post_type ) {

			if ( $post_type !== VGSE()->WC->post_type ) {
				return;
			}
			?>

			<p><?php printf( __( 'Here is a <a href="%s" target="_blank">sample CSV</a> containing all types of products.', 'vg_sheet_editor' ), 'https://github.com/woocommerce/woocommerce/blob/master/sample-data/sample_products.csv' ); ?></p>
			<?php
		}

		public function is_import_export_supported( $supported, $post_type ) {

			if ( $post_type === VGSE()->WC->post_type && version_compare( WC()->version, '3.1.0' ) < 0 ) {
				$supported = false;
			}
			return $supported;
		}

		public function maybe_create_template_products( $data, $settings ) {

			if ( $settings['post_type'] !== VGSE()->WC->post_type || empty( $settings['wpse_source'] ) || $settings['wpse_source'] !== 'import' || empty( $settings['allow_to_create_new'] ) ) {
				return $data;
			}

			$rows_missing_ids = wp_list_filter( $data, array( 'ID' => null ) );
			$new_ids_count    = 0;
			$skus_found_count = 0;
			foreach ( $rows_missing_ids as $row_index => $item ) {
				$row_id  = null;
				$row_sku = null;
				if ( ! empty( $item['sku'] ) ) {
					$row_sku = str_replace( '&', 'and', $item['sku'] );
					$row_id  = wc_get_product_id_by_sku( $row_sku );
					if ( $row_id ) {
						$skus_found_count++;
					}
				}
				if ( ! $row_id ) {
					$product = new WC_Product_Simple();
					$product->set_name( 'Import placeholder' );
					$product->set_status( 'importing' );

					// If row has a SKU, make sure placeholder has it too.
					if ( $row_sku ) {
						$product->set_sku( $row_sku );
					}
					$row_id = $product->save();
					$new_ids_count++;
				}
				if ( $row_id ) {
					$data[ $row_index ]['ID'] = $row_id;
				}
			}
			if ( function_exists( 'WPSE_Logger_Obj' ) && ! empty( VGSE()->helpers->get_job_id_from_request() ) ) {
				WPSE_Logger_Obj()->entry( sprintf( 'Before saving: We found %d new rows that need a placeholder product. Created %d rows as placeholder that will be used for saving real data later. We didn\'t create placeholder products for %d rows because the SKUs matched existing products', count( $rows_missing_ids ), $new_ids_count, $skus_found_count ), sanitize_text_field( VGSE()->helpers->get_job_id_from_request() ) );
			}

			return $data;
		}

		public function get_importer( $args = array() ) {
			$this->include_importer();
			$importer = new WPSE_WC_Importer( $args );
			return $importer;
		}

		public function get_importer_controller() {
			$this->include_importer();
			include_once WC_ABSPATH . 'includes/admin/importers/class-wc-product-csv-importer-controller.php';
			require_once VGSE_WC_DIR . '/inc/wc-core-importer-controller.php';
			$importer_controller = new WPSE_WC_Importer_Controller();
			return $importer_controller;
		}

		public function get_exporter() {

			if ( ! class_exists( 'WC_Product_CSV_Exporter' ) ) {
				include_once WC_ABSPATH . 'includes/export/class-wc-product-csv-exporter.php';
			}
			require_once VGSE_WC_DIR . '/inc/wc-core-exporter.php';
			$exporter = new WPSE_WC_Exporter();
			return $exporter;
		}

		public function include_importer() {
			if ( ! class_exists( 'WP_Importer' ) ) {
				$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';

				if ( file_exists( $class_wp_importer ) ) {
					require $class_wp_importer;
				}
			}
			if ( ! class_exists( 'WC_Product_CSV_Importer' ) ) {
				include_once WC_ABSPATH . 'includes/import/class-wc-product-csv-importer.php';
			}
			require_once VGSE_WC_DIR . '/inc/wc-core-importer.php';
		}

		public function save_columns_data_during_import( $data, $post_id, $post_type, $spreadsheet_columns, $settings ) {
			global $wpdb;
			if ( is_wp_error( $data ) || $post_type !== VGSE()->WC->post_type || empty( $settings['wpse_source'] ) || $settings['wpse_source'] !== 'import' ) {
				return $data;
			}

			$import_settings = $settings['wpse_import_settings'];
			$original_data   = $data;
			$product_type    = null;

			if ( ! empty( $data['type'] ) ) {
				$product_type = $data['type'];
			} elseif ( ! empty( $data['ID'] ) ) {
				$product_type = VGSE()->WC->get_product_type( $data['ID'] );
			} elseif ( ! empty( $data['id'] ) ) {
				$product_type = VGSE()->WC->get_product_type( $data['id'] );
			}

			// Convert the special column keys from attribute_name to attribute:name,
			// required by the WC importer class.
			// When we import on wp-admin, the columns already have the attribute:name syntax.
			// we need this when we import through the REST API.
			foreach ( $data as $key => $value ) {
				$key_without_number = preg_replace( '/[^a-zA-Z_]/', '', $key );
				if ( in_array( $key_without_number, VGSE()->WC->special_columns_import_prefixes ) ) {
					$data[ str_replace( '_', ':', $key ) ] = $value;
					unset( $data[ $key ] );
				}

				// WC uses the ID as id
				if ( ! empty( $data['ID'] ) ) {
					$data['id'] = $data['ID'];
				}

				// Make sure there is a default attribute always, otherwise WC won't save the variations
				if ( strpos( $key, 'attributes:value' ) !== false && $product_type === 'variable' ) {
					$default_attribute_key = str_replace( 'attributes:value', 'attributes:default', $key );
					if ( empty( $data[ $default_attribute_key ] ) ) {
						$data[ $default_attribute_key ] = current( array_map( 'trim', explode( ',', $value ) ) );
					}
				}

				// Copy the category_ids column to product_cat to save it with
				// WPSE CORE so the wpse_old_platform_id option works
				if ( ! empty( $data['category_ids'] ) ) {
					$data['product_cat']  = $data['category_ids'];
					$data['category_ids'] = '';
				}
			}

			// Weird bug on WC's side. It returns a "SKU duplicated" error when
			// the SKU contains & and it's not really duplicated.
			if ( ! empty( $data['sku'] ) ) {
				$data['sku'] = str_replace( '&', '-', $data['sku'] );
			}

			// Prevent error. Notify when variation references a non-existent parent
			if ( ! empty( $data['parent_id'] ) ) {
				if ( preg_match( '/^id:(\d+)$/', $data['parent_id'], $matches ) ) {
					$raw_id = intval( $matches[1] );
					$id     = (int) $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type IN ( 'product', 'product_variation' ) AND ID = %d;", $raw_id ) ); // WPCS: db call ok, cache ok.
					$type   = 'ID';
				} else {
					$data['parent_id'] = str_replace( '&', '-', $data['parent_id'] );
					$id   = (int) wc_get_product_id_by_sku( $data['parent_id'] );
					$type = 'SKU';
				}
				if ( ! $id ) {
					return new WP_Error( 'wpse', sprintf( __( 'One variation row has a parent product that does not exist. The "parent" column contains the %1$s: %2$s. Please correct it and start a new import', 'vg_sheet_editor' ), esc_html( $type ), $data['parent_id'] ) );
				}

				// Remove invalid status in variation rows
				if ( isset( $data['post_status'] ) && ! in_array( $data['post_status'], array( 'publish', 'private' ), true ) ) {
					$data['post_status'] = 'publish';
				}
			}

			// WC won't save variation rows when the parent id is not defined in the CSV file, so get the parent id from the database as a fallback
			if ( empty( $data['parent_id'] ) && ! empty( $data['id'] ) ) {
				$parent_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT post_parent FROM $wpdb->posts WHERE ID = %d AND post_type = 'product_variation' ", $data['id'] ) );
				if ( $parent_id ) {
					$data['parent_id'] = 'id:' . $parent_id;
				}
			}

			// If the user allows skipping broken images, we try to download them with our CORE function
			// and save the downloaded ids, this way WooCommerce won't stop the import if the images fail
			if ( ! empty( $settings['wpse_import_settings']['skip_broken_images'] ) && ! empty( $data['images'] ) ) {
				// Compatibility for WP Offload Media - We need the real WP URL, not the AWS URL here
				remove_all_filters( 'wp_get_attachment_url' );
				$image_ids      = implode( ',', array_map( 'wp_get_attachment_url', array_filter( VGSE()->helpers->maybe_replace_urls_with_file_ids( explode( ',', $data['images'] ) ) ) ) );
				$data['images'] = $image_ids;
			}

			$update_existing = get_post_status( $data['ID'] ) !== 'importing';

			// Updating the SKU is very expensive in terms of DB queries, even if the SKU did not change
			// So we will remove the SKU field if the value did not change
			$existing_sku = get_post_meta( $data['ID'], '_sku', true );
			if ( isset( $data['sku'] ) && $data['sku'] === $existing_sku ) {
				unset( $data['sku'] );
			}
			$mapping = array_combine( array_keys( $data ), array_keys( $data ) );

			$keys_to_be_updated = array_diff( array_keys( $data ), array( 'id', 'ID', '', 'post_type' ) );
			$lookup_exists      = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}wc_product_meta_lookup WHERE product_id = %d", $data['ID'] ) );
			// If we are updating stock only, we will save it with direct DB queries to avoid using the WC API
			// because the WC API is saving +10 fields even when they are not edited and it can add +4k
			// unnecessary database queries and +2 minutes in load time when we import 200 products per batch
			if ( count( $keys_to_be_updated ) === 1 && ! empty( $data['stock_quantity'] ) && $lookup_exists ) {
				$stock = wc_stock_amount( $data['stock_quantity'] );
				update_post_meta( $data['ID'], '_stock', $stock );
				update_post_meta( $data['ID'], '_stock_status', $stock ? 'instock' : 'outofstock' );
				$wpdb->update(
					$wpdb->prefix . 'wc_product_meta_lookup',
					array(
						'stock_quantity' => $stock,
						'stock_status'   => $stock ? 'instock' : 'outofstock',
					),
					array(
						'product_id' => $data['ID'],
					)
				);
				WPSE_WC_Products_Data_Formatting_Obj()->clear_wc_caches( $data['ID'] );
				do_action( 'woocommerce_update_product', $data['ID'], wc_get_product( $data['ID'] ) );
				$data = array(
					'ID' => $data['ID'],
				);
			} else {

				// The mapping somehow contains an element with empty key,
				// we can't remove it, if we remove it the mapping breaks in the WC importer class
				// Save row
				$importer = $this->get_importer(
					array(
						'data'            => array( $data ), // This is designed to import multiple rows at once, we import one in this case
						'mapping'         => $mapping, // wpse already mapped the fields
						'update_existing' => $update_existing,
					)
				);
				$result   = $importer->import();

				if ( ! empty( $result['skipped'] ) ) {
					return current( $result['skipped'] );
				}
				if ( ! empty( $result['failed'] ) ) {
					return current( $result['failed'] );
				}

				// Resave the custom attribute values because WC removes line breaks
				// from attribute values and we want to preserve them
				if ( ! empty( VGSE()->options['allow_line_breaks_export_import'] ) ) {
					$saved_attributes    = get_post_meta( $data['ID'], '_product_attributes', true );
					$modified_attributes = $saved_attributes;
					$all_data            = json_encode( $data );
					if ( ! empty( $saved_attributes ) && is_array( $saved_attributes ) && strpos( $all_data, 'attributes:taxonomy' ) !== false ) {
						foreach ( $data as $key => $value ) {
							if ( strpos( $key, 'attributes:taxonomy' ) !== 0 || (int) $value !== 0 ) {
								continue;
							}
							$attribute_number = (int) str_replace( 'attributes:taxonomy', '', $key );
							if ( empty( $data[ 'attributes:value' . $attribute_number ] ) || empty( $data[ 'attributes:name' . $attribute_number ] ) ) {
								continue;
							}
							$attribute_name     = $data[ 'attributes:name' . $attribute_number ];
							$attribute_name_key = sanitize_title( $attribute_name );
							if ( ! isset( $saved_attributes[ $attribute_name_key ] ) ) {
								continue;
							}
							$attribute_value = $data[ 'attributes:value' . $attribute_number ];
							if ( $saved_attributes[ $attribute_name_key ]['value'] !== $attribute_value ) {
								$modified_attributes[ $attribute_name_key ]['value'] = wp_kses_post( $attribute_value );
							}
						}

						if ( $saved_attributes !== $modified_attributes ) {
							update_post_meta( $data['ID'], '_product_attributes', $modified_attributes );
						}
					}
				}

				// Remove the special columns from the $data, so sheet editor core saves the other fields only
				$data = array_diff_key( $data, VGSE()->helpers->array_flatten( $this->get_importer_controller()->get_mapping_options( '' ) ) );
			}
			if ( isset( $data[''] ) ) {
				unset( $data[''] );
			}

			// The ID should not be removed
			if ( ! isset( $data['ID'] ) ) {
				$data['ID'] = $original_data['ID'];
			}

			return $data;
		}

		public function allow_wc_core_columns_keys_for_export( $column_keys, $cleaned_rows, $clean_data ) {
			if ( $clean_data['post_type'] === VGSE()->WC->post_type && ! empty( $GLOBALS['wpse_wc_last_exported_keys'] ) ) {
				$column_keys = array_unique( array_merge( $column_keys, array_keys( $GLOBALS['wpse_wc_last_exported_keys'] ) ) );
			}
			return $column_keys;
		}

		public function convert_file_labels_to_keys_for_export( $existing_file_keys, $first_row, $cleaned_rows, $clean_data ) {
			if ( $clean_data['post_type'] === VGSE()->WC->post_type && ! empty( $GLOBALS['wpse_wc_last_exported_keys'] ) ) {

				foreach ( $existing_file_keys as $index => $header ) {
					$column_key = array_search( $header, $GLOBALS['wpse_wc_last_exported_keys'] );
					if ( $column_key !== false ) {
						$existing_file_keys[ $index ] = $column_key;
					}
				}
			}
			return $existing_file_keys;
		}

		public function add_friendly_column_headers_for_export( $headers, $clean_data ) {
			if ( $clean_data['post_type'] === VGSE()->WC->post_type && ! empty( $GLOBALS['wpse_wc_last_exported_keys'] ) ) {
				foreach ( $headers as $index => $header ) {
					if ( isset( $GLOBALS['wpse_wc_last_exported_keys'][ $header ] ) ) {
						$headers[ $index ] = $GLOBALS['wpse_wc_last_exported_keys'][ $header ];
					}
				}
			}

			return $headers;
		}

		public function _remove_placeholder_products() {
			global $wpdb;

			$placeholder_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = %s AND post_status = 'importing' ", VGSE()->WC->post_type ) );
			foreach ( $placeholder_ids as $post_id ) {
				wp_delete_post( $post_id, true );
			}
		}

		public function remove_placeholder_products_after_import( $data, $post_type, $spreadsheet_columns, $settings ) {
			if ( $post_type !== VGSE()->WC->post_type || empty( $settings['wpse_source'] ) || $settings['wpse_source'] !== 'import' ) {
				return;
			}
			$this->_remove_placeholder_products();
		}

		public function remove_placeholder_products_after_failed_save( $e, $data, $post_type ) {
			if ( $post_type !== VGSE()->WC->post_type ) {
				return;
			}
			$this->_remove_placeholder_products();
		}

		public function add_special_columns_data_to_export( $cleaned_rows, $clean_data, $wp_query_args, $spreadsheet_columns ) {

			if ( $clean_data['post_type'] === VGSE()->WC->post_type && ! empty( $clean_data['custom_enabled_columns'] ) ) {
				$exporter = $this->get_exporter();
				$exporter->set_column_names( wp_unslash( $this->get_exporter()->get_default_column_names() ) );
				$exporter->set_columns_to_export( wp_unslash( explode( ',', $clean_data['custom_enabled_columns'] ) ) ); // WPCS: input var ok, sanitization ok.

				$all_exported_keys = array();
				foreach ( $cleaned_rows as $cleaned_row_index => $cleaned_row ) {
					$new_data = $exporter->generate_row_data( wc_get_product( $cleaned_row['ID'] ) );
					// WPSE core has the ID key, remove duplicate from WC
					if ( isset( $new_data['id'] ) ) {
						unset( $new_data['id'] );
						unset( $new_data['ID'] );
					}
					// Fix bug. Some plugin adds this field to the WC export breaking our export
					if ( isset( $new_data['person_types'] ) ) {
						unset( $new_data['person_types'] );
					}
					foreach ( $new_data as $column_id => $column_value ) {
						$new_data[ $column_id ] = $exporter->format_data( $column_value );
					}
					$all_exported_keys                  = array_unique( array_merge( $all_exported_keys, array_keys( $new_data ) ) );
					$cleaned_rows[ $cleaned_row_index ] = array_merge( $cleaned_row, $new_data );
				}

				$column_headers = $exporter->get_export_column_headers();
				$id_index       = array_search( 'ID', $column_headers, true );
				if ( $id_index !== false ) {
					unset( $column_headers[ $id_index ] );
				}

				// FIX. For some strange reason, sometimes there are more columns in the WPSE rows than the manually enabled in the export, which caused a fatal error during the array_combine below. Now we standarize the length of both arrays to avoid the fatal error
				if ( count( $all_exported_keys ) > count( $column_headers ) ) {
					foreach ( $all_exported_keys as $index => $key ) {
						if ( ! isset( $column_headers[ $index ] ) ) {
							$column_headers[ $index ] = $key;
						}
					}
				}
				$GLOBALS['wpse_wc_last_exported_keys'] = array_combine( $all_exported_keys, $column_headers );
			}

			return $cleaned_rows;
		}

		public function add_special_columns_to_import_list( $post_type ) {
			if ( $post_type !== VGSE()->WC->post_type ) {
				return;
			}
			$mapped_value        = '';
			$importer_controller = $this->get_importer_controller();
			$mapping_options     = apply_filters( 'vg_sheet_editor/import/woocommerce/special_product_mapping_options', $importer_controller->get_mapping_options( $mapped_value ) );
			?>
			<?php
			foreach ( $mapping_options as $key => $value ) :
				?>
				<?php if ( is_array( $value ) ) : ?>
					<optgroup label="<?php echo esc_attr( $value['name'] ); ?>">
						<?php
						foreach ( $value['options'] as $sub_key => $sub_value ) :
							?>
							<option value="<?php echo esc_attr( $sub_key ); ?>" <?php selected( $mapped_value, $sub_key ); ?>><?php echo esc_html( $sub_value ); ?></option>
						<?php endforeach ?>
					</optgroup>
					<?php
				else :
					?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $mapped_value, $key ); ?>><?php echo esc_html( $value ); ?></option>
				<?php endif; ?>
			<?php endforeach ?>
			<?php
		}

		public function remove_core_fields_from_export_list( $column_options, $post_type ) {
			if ( $post_type !== VGSE()->WC->post_type ) {
				return $column_options;
			}

			// Exclude variation custom fields from the list (not handled by WC)
			$core_columns_list = array_diff( VGSE()->WC->core_columns_list, WP_Sheet_Editor_WooCommerce_Variations::get_instance()->get_variation_meta_keys() );
			if ( ! empty( VGSE()->options['wc_use_separate_image_columns'] ) ) {
				$core_columns_list = array_diff( $core_columns_list, array( '_thumbnail_id', '_product_image_gallery' ) );
			}
			$core_columns_list = array_diff( $core_columns_list, array( 'menu_order', 'post_status' ) );
			$column_options    = array_diff_key( $column_options, array_flip( $core_columns_list ) );

			return $column_options;
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		public static function get_instance() {
			if ( ! self::$instance ) {
				self::$instance = new WPSE_WC_Products_Universal_Sheet();
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

	}

}

if ( ! function_exists( 'WPSE_WC_Products_Universal_Sheet_Obj' ) ) {

	function WPSE_WC_Products_Universal_Sheet_Obj() {
		return WPSE_WC_Products_Universal_Sheet::get_instance();
	}
}
WPSE_WC_Products_Universal_Sheet_Obj();
