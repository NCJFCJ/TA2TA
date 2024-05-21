<?php defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_Sheet_Editor_WooCommerce_Attrs' ) ) {

	/**
	 * Display woocommerce item in the toolbar to tease users of the free
	 * version into purchasing the premium plugin.
	 */
	class WP_Sheet_Editor_WooCommerce_Attrs {

		private static $instance = false;
		var $post_type           = null;
		var $post_type_variation = 'product_variation';
		var $cell_key            = '_vgse_create_attribute';

		private function __construct() {

		}

		function init() {
			$this->post_type = apply_filters( 'vg_sheet_editor/woocommerce/product_post_type_key', 'product' );
			add_filter( 'vg_sheet_editor/handsontable_cell_content/existing_value', array( $this, 'filter_cell_value' ), 10, 4 );
			add_action( 'wp_ajax_vgse_wc_save_attributes', array( $this, 'save_attributes' ) );
			add_action( 'wp_ajax_vgse_wc_get_attributes', array( $this, 'get_attributes_for_popup' ) );
			add_action( 'vg_sheet_editor/editor/register_columns', array( $this, 'register_columns' ) );
			add_filter( 'vgse_sheet_editor/provider/post/prefetch/taxonomy_keys', array( $this, 'prefetch_global_attributes' ) );
			add_filter( 'vg_sheet_editor/data/taxonomy_terms', array( $this, 'add_select_all_option_to_terms' ), 10, 3 );
			add_filter( 'vg_sheet_editor/save_rows/row_data_before_save', array( $this, 'replace_all_terms_with_real_terms' ), 10, 4 );
			add_filter( 'vg_sheet_editor/formulas/form_settings', array( $this, 'add_formula_type_toggle_attribute_settings' ), 10, 2 );
			add_filter( 'vg_sheet_editor/formulas/execute_formula', array( $this, 'execute_formula_toggle_attribute_settings' ), 10, 4 );
			add_filter( 'vg_sheet_editor/formulas/execute_formula', array( $this, 'execute_formula_default_attribute_replace' ), 10, 4 );
			add_filter( 'vg_sheet_editor/advanced_filters/taxonomy_labels', array( $this, 'remove_attribute_labels_from_search' ), 10, 2 );
			add_filter( 'vg_sheet_editor/save_rows/incoming_data', array( $this, 'resave_global_attributes_when_type_changes' ) );
		}

		function resave_global_attributes_when_type_changes( $data ) {
			$all_data = json_encode( $data );
			// Skip if we're importing attribute columns
			if ( strpos( $all_data, 'attributes:' ) !== false ) {
				return $data;
			}
			foreach ( $data as $index => $row ) {
				if ( empty( $row['product_type'] ) || $row['product_type'] !== 'variable' ) {
					continue;
				}
				$product_attributes = get_post_meta( $row['ID'], '_product_attributes', true );
				if ( empty( $product_attributes ) || ! is_array( $product_attributes ) ) {
					continue;
				}
				foreach ( $product_attributes as $attribute_key => $attribute_settings ) {
					if ( isset( $row[ $attribute_key ] ) || strpos( $attribute_key, 'pa_' ) !== 0 ) {
						continue;
					}
					$data[ $index ][ $attribute_key ] = VGSE()->helpers->get_current_provider()->get_item_terms( $row['ID'], $attribute_key );
				}
			}
			return $data;
		}

		function remove_attribute_labels_from_search( $labels, $post_type ) {
			if ( $post_type === $this->post_type ) {
				$taxonomy_keys = VGSE()->helpers->get_post_type_taxonomies_single_data( $post_type, 'name' );
				$final_labels  = array();
				foreach ( $taxonomy_keys as $index => $taxonomy_key ) {
					if ( strpos( $taxonomy_key, 'pa_' ) !== 0 ) {
						$final_labels[] = $labels[ $index ];
					}
				}
				$final_labels[] = __( 'Product attributes', 'vg_sheet_editor' );
				$labels         = $final_labels;
			}

			return $labels;
		}

		function execute_formula_toggle_attribute_settings( $update_count, $raw_form_data, $post_ids, $column_settings ) {
			global $wpdb;
			// If wc_attributes_toggle_setting is active and posts were found,
			// modify the formula to properly replace on the serialized value
			if ( $raw_form_data['action_name'] !== 'wc_attributes_toggle_setting' ) {
				return $update_count;
			}
			$subfield_key = $raw_form_data['formula_data'][0];
			$allowed_keys = array( 'is_visible', 'is_variation' );
			if ( ! in_array( $subfield_key, $allowed_keys, true ) ) {
				return 0;
			}

			$new_value      = (int) $raw_form_data['formula_data'][1];
			$attribute      = sanitize_text_field( $raw_form_data['formula_data'][2] );
			$previous_value = $new_value === 1 ? 0 : 1;
			$first_post_id  = current( $post_ids );
			if ( is_object( $first_post_id ) ) {
				$post_ids = wp_list_pluck( $post_ids, 'ID' );
			}

			if ( empty( $attribute ) ) {
				// We can't use $wpdb->prepare because this query sql is generated dynamically. However, every variable is sanitized. $post_ids goes through intval, $new_value and $previou_value are verified integers, $subfield_key goes through a whitelist of 2 keys.
				$sql = "UPDATE $wpdb->postmeta SET meta_value = {replace} WHERE  post_id IN (" . implode( ',', array_map( 'intval', $post_ids ) ) . ")  AND meta_value <> '' AND meta_key = '_product_attributes' ;";

				// We execute the sql query  twice, one time to update attributes with values
				// as strings and the other with values as int
				$sql1         = str_replace( '{replace}', "REPLACE(meta_value, '$subfield_key\";i:$previous_value', '$subfield_key\";i:$new_value' )", $sql );
				$update_count = $wpdb->query( $sql1 );

				$sql2          = str_replace( '{replace}', "REPLACE(meta_value, '$subfield_key\";s:1:\"$previous_value', '$subfield_key\";s:1:\"$new_value' )", $sql );
				$update_count += $wpdb->query( $sql2 );
			} else {

				$ids_in_query_placeholders = implode( ', ', array_fill( 0, count( $post_ids ), '%d' ) );
				$attributes_rows           = $wpdb->get_results( $wpdb->prepare( "SELECT post_id,meta_value FROM $wpdb->postmeta WHERE post_id IN ($ids_in_query_placeholders) AND meta_key = '_product_attributes' AND meta_value <> '' GROUP BY post_id", $post_ids ), ARRAY_A );

				$update_count = 0;
				foreach ( $attributes_rows as $row ) {
					$attributes = maybe_unserialize( $row['meta_value'] );
					if ( ! is_array( $attributes ) || empty( $attributes ) || ! isset( $attributes[ $attribute ] ) ) {
						continue;
					}
					$attributes[ $attribute ][ $subfield_key ] = $new_value;
					update_post_meta( $row['post_id'], '_product_attributes', $attributes );
					$update_count++;
				}
			}
			return $update_count;
		}

		function _maybe_convert_term_name_to_slug( $taxonomy_key, $search_term ) {
			if ( ! $search_term ) {
				return $search_term;
			}
			$term_exists_args = array(
				'taxonomy'   => $taxonomy_key,
				'slug'       => $search_term,
				'fields'     => 'slugs',
				'hide_empty' => false,
			);
			$term             = get_terms( $term_exists_args );
			if ( $term ) {
				$slug = current( $term );
			} else {
				$term_exists_args['name'] = $search_term;
				unset( $term_exists_args['slug'] );
				$term = get_terms( $term_exists_args );
				$slug = $term ? current( $term ) : false;
			}
			return $slug;
		}
		function execute_formula_default_attribute_replace( $update_count, $raw_form_data, $post_ids, $column_settings ) {
			global $wpdb;
			// If wc_default_attribute_replace is active and posts were found,
			// modify the formula to properly replace on the serialized value
			if ( $raw_form_data['action_name'] !== 'wc_default_attribute_replace' ) {
				return $update_count;
			}
			if ( count( $raw_form_data['formula_data'] ) !== 3 ) {
				return 0;
			}
			$attribute_name = trim( sanitize_title( $raw_form_data['formula_data'][0] ) );
			$search         = trim( sanitize_text_field( $raw_form_data['formula_data'][1] ) );
			$replace        = trim( sanitize_text_field( $raw_form_data['formula_data'][2] ) );

			if ( empty( $search ) || empty( $attribute_name ) ) {
				return 0;
			}

			if ( stripos( $attribute_name, 'pa_' ) === 0 ) {
				$search  = $this->_maybe_convert_term_name_to_slug( $attribute_name, $search );
				$replace = $this->_maybe_convert_term_name_to_slug( $attribute_name, $replace );
			}

			if ( ! $search ) {
				return 0;
			}

			$ids_in_query_placeholders = implode( ', ', array_fill( 0, count( $post_ids ), '%d' ) );
			$default_attribute_rows    = $wpdb->get_results( $wpdb->prepare( "SELECT post_id,meta_value FROM $wpdb->postmeta WHERE post_id IN ($ids_in_query_placeholders) AND meta_key = '_default_attributes' AND meta_value LIKE %s AND meta_value LIKE %s GROUP BY post_id", array_merge( $post_ids, array( '%"' . $wpdb->esc_like( $search ) . '"%', '%"' . $wpdb->esc_like( $attribute_name ) . '"%' ) ) ), ARRAY_A );

			$update_count = 0;
			foreach ( $default_attribute_rows as $row ) {
				$attributes = maybe_unserialize( $row['meta_value'] );
				if ( ! is_array( $attributes ) || empty( $attributes ) || ! isset( $attributes[ $attribute_name ] ) || $attributes[ $attribute_name ] !== $search ) {
					continue;
				}

				if ( $replace ) {
					$attributes[ $attribute_name ] = $replace;
				} else {
					unset( $attributes[ $attribute_name ] );
				}

				update_post_meta( $row['post_id'], '_default_attributes', $attributes );
				$update_count++;
			}
			return $update_count;
		}

		function get_all_attributes() {
			global $wpdb;
			$transient_key  = 'vgse_wc_attributes';
			$all_attributes = get_transient( $transient_key );

			// Clear cache
			if ( method_exists( VGSE()->helpers, 'can_rescan_db_fields' ) && VGSE()->helpers->can_rescan_db_fields( $this->post_type ) ) {
				$all_attributes = false;
			}

			if ( empty( $all_attributes ) ) {
				$raw_attributes = array_map( 'maybe_unserialize', $wpdb->get_col( "SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = '_product_attributes' AND meta_value <> '' GROUP BY meta_value LIMIT 100" ) );
				$all_attributes = array(
					'custom' => array(),
					'global' => array(),
				);
				foreach ( $raw_attributes as $attributes ) {
					if ( ! is_array( $attributes ) || empty( $attributes ) ) {
						continue;
					}

					foreach ( $attributes as $key => $attribute ) {
						if ( strpos( $key, 'pa_' ) === 0 || empty( $attribute['name'] ) || ! is_string( $attribute['name'] ) ) {
							continue;
						}
						$all_attributes['custom'][ $key ] = $attribute['name'];
					}
				}

				$attribute_taxonomies = wc_get_attribute_taxonomies();
				if ( ! empty( $attribute_taxonomies ) ) {
					foreach ( $attribute_taxonomies as $tax ) {
						$key                              = wc_attribute_taxonomy_name( $tax->attribute_name );
						$all_attributes['global'][ $key ] = $tax->attribute_label;
					}
				}
				set_transient( $transient_key, $all_attributes, DAY_IN_SECONDS * 3 );
			}

			// Add custom attributes defined in the advanced settings
			if ( ! empty( VGSE()->options['wc_products_custom_attribute_names'] ) ) {
				$custom_attribute_keys = array_map( 'trim', explode( ',', VGSE()->options['wc_products_custom_attribute_names'] ) );
				foreach ( $custom_attribute_keys as $custom_attribute_key ) {
					$all_attributes['custom'][ sanitize_title( $custom_attribute_key ) ] = $custom_attribute_key;
				}
			}

			return $all_attributes;
		}

		function add_formula_type_toggle_attribute_settings( $formulas, $post_type ) {
			global $wpdb;
			if ( $this->post_type !== $post_type ) {
				return $formulas;
			}

			$all_attributes     = $this->get_all_attributes();
			$attributes_options = '<option value="">All</option>';
			foreach ( $all_attributes as $group => $attributes ) {
				foreach ( $attributes as $attribute_key => $attribute_label ) {
					if ( empty( $attribute_label ) || empty( $attribute_key ) ) {
						continue;
					}
					$attributes_options .= '<option value="' . esc_attr( $attribute_key ) . '">' . esc_html( $attribute_label . ' (' . $group . ')' ) . '</option>';
				}
			}

			$formulas['columns_actions']['text']['wc_attributes_toggle_setting'] = 'default';
			$formulas['default_actions']['wc_attributes_toggle_setting']         = array(
				'label'               => __( 'Change attribute settings', 'vg_sheet_editor' ),
				'description'         => '',
				'fields_relationship' => 'AND',
				'jsCallback'          => 'vgseGenerateReplaceFormula',
				'allowed_column_keys' => array( '_vgse_create_attribute' ),
				'input_fields'        =>
				array(
					array(
						'label'   => __( 'What setting do you want to change?', 'vg_sheet_editor' ),
						'tag'     => 'select',
						'options' => '<option value="">--</option><option value="is_visible">' . __( 'Is Visible?', 'vg_sheet_editor' ) . '</option>' . '<option value="is_variation">' . __( 'Used for Variations?', 'vg_sheet_editor' ) . '</option>',
					),
					array(
						'label'   => __( 'New value', 'vg_sheet_editor' ),
						'tag'     => 'select',
						'options' => '<option value="0">' . __( 'No', 'vg_sheet_editor' ) . '</option>' . '<option value="1">' . __( 'Yes', 'vg_sheet_editor' ) . '</option>',
					),
					array(
						'label'   => __( 'What attribute do you want to edit?', 'vg_sheet_editor' ),
						'tag'     => 'select',
						'options' => $attributes_options,
					),
				),
			);
			$formulas['columns_actions']['text']['wc_default_attribute_replace'] = 'default';
			$formulas['default_actions']['wc_default_attribute_replace']         = array(
				'label'               => __( 'Replace a default attribute term', 'vg_sheet_editor' ),
				'description'         => '',
				'fields_relationship' => 'AND',
				'jsCallback'          => 'vgseGenerateReplaceFormula',
				'allowed_column_keys' => array( '_default_attributes' ),
				'input_fields'        =>
				array(
					array(
						'label'   => __( 'Enter the attribute name', 'vg_sheet_editor' ),
						'tooltip' => __( 'If this is a global attribute, enter the attribute key (i.e. pa_color). Enter the name if this is a custom attribute.', 'vg_sheet_editor' ),
						'tag'     => 'input',
					),
					array(
						'label'   => __( 'Replace this term', 'vg_sheet_editor' ),
						'tag'     => 'input',
						'tooltip' => __( 'Enter the term name or slug.', 'vg_sheet_editor' ),
					),
					array(
						'label'   => __( 'With this term', 'vg_sheet_editor' ),
						'tag'     => 'input',
						'tooltip' => __( 'Leave this empty if you want to unset the term.', 'vg_sheet_editor' ),
					),
				),
			);

			return $formulas;
		}

		function replace_all_terms_with_real_terms( $item, $post_id, $post_type, $spreadsheet_columns ) {
			if ( is_wp_error( $item ) || $post_type !== $this->post_type ) {
				return $item;
			}
			$select_all_keyword = __( 'Select all', 'vg_sheet_editor' );
			$serialized_item    = serialize( $item );
			/// Bail if row does not contain the "select all" text anywhere
			if ( strpos( $serialized_item, $select_all_keyword ) === false ) {
				return $item;
			}
			$terms_columns = wp_list_filter( $spreadsheet_columns, array( 'data_type' => 'post_terms' ) );
			$separator     = VGSE()->helpers->get_term_separator();

			foreach ( $terms_columns as $term_column ) {
				$taxonomy = $term_column['key'];
				if ( strpos( $taxonomy, 'pa_' ) !== false && isset( $item[ $taxonomy ] ) && $item[ $taxonomy ] === $select_all_keyword ) {
					$item[ $taxonomy ] = implode(
						"$separator ",
						get_terms(
							array(
								'taxonomy'               => $taxonomy,
								'hide_empty'             => false,
								'fields'                 => 'names',
								'update_term_meta_cache' => false,
							)
						)
					);
				}
			}

			return $item;
		}

		function add_select_all_option_to_terms( $terms, $taxonomy, $source ) {

			if ( strpos( $taxonomy, 'pa_' ) !== false && $source === 'taxonomy_column' ) {
				$terms = array_merge( array( __( 'Select all', 'vg_sheet_editor' ) ), $terms );
			}
			return $terms;
		}

		function prefetch_global_attributes( $taxonomy_keys ) {

			$post_type = VGSE()->helpers->get_provider_from_query_string();
			if ( $post_type === $this->post_type ) {
				$attribute_taxonomies = wc_get_attribute_taxonomies();
				foreach ( $attribute_taxonomies as $attribute ) {
					$taxonomy_keys[] = wc_attribute_taxonomy_name( $attribute->attribute_name );
				}
			}
			return array_unique( $taxonomy_keys );
		}

		/**
		 * Filter "edit attributes" cell html
		 * @param str $value
		 * @param obj $post WP_Post object
		 * @param str $key
		 * @param array $cell_args
		 * @return str
		 */
		function filter_cell_value( $value, $post, $key, $cell_args ) {
			if ( $key !== $this->cell_key ) {
				return $value;
			}

			$boolean_fields = array(
				'is_visible',
				'is_taxonomy',
				'is_variation',
			);

			// @todo Obtener attrs. de variaciones con API DE WC.
			// Hacer un merge de los datos de la API con _product_attributes
			// para enviar los datos faltantes en la respuesta de la API.

			if ( $post->post_type === $this->post_type ) {
				$attributes = VGSE()->helpers->get_current_provider()->get_item_meta( $post->ID, '_product_attributes', true );

				if ( ! empty( $attributes ) && is_array( $attributes ) ) {
					$i = 0;

					foreach ( $attributes as $index => $attribute ) {
						if ( $attribute['is_taxonomy'] && taxonomy_exists( $index ) ) {
							$attributes[ $index ]['taxonomy_key'] = $index;
							$taxonomy                             = get_taxonomy( $index );
							$attributes[ $index ]['name']         = $taxonomy->label;

							$attributes[ $index ]['value'] = VGSE()->helpers->get_current_provider()->get_item_terms( $post->ID, $index );
						}
						if ( empty( $attributes[ $index ]['position'] ) ) {
							$attributes[ $index ]['position'] = $i;
						}
						foreach ( $boolean_fields as $boolean_field ) {
							if ( empty( $attributes[ $index ][ $boolean_field ] ) ) {
								$attributes[ $index ][ $boolean_field ] = 0;
							}
						}

						$i++;
					}
				}
			} elseif ( $post->post_type === 'product_variation' ) {

				// @todo Obtener attrs. de variaciones con API DE WC.

				$parent_attributes = VGSE()->helpers->get_current_provider()->get_item_meta( $post->post_parent, '_product_attributes', true );
				$variation_meta    = get_post_meta( $post->ID );
				$attributes        = array();
				if ( is_array( $variation_meta ) ) {
					foreach ( $variation_meta as $key => $value ) {
						if ( strpos( $key, 'attribute_' ) === false ) {
							continue;
						}

						$attribute_key = sanitize_title( str_replace( 'attribute_', '', $key ) );

						if ( ! isset( $parent_attributes[ $attribute_key ] ) ) {
							continue;
						}
						$attributes[ $attribute_key ]          = $parent_attributes[ $attribute_key ];
						$attributes[ $attribute_key ]['value'] = ( is_array( $value ) ) ? current( $value ) : $value;
						if ( $parent_attributes[ $attribute_key ]['is_taxonomy'] && taxonomy_exists( $attribute_key ) ) {
							$taxonomy                             = get_taxonomy( $attribute_key );
							$attributes[ $attribute_key ]['name'] = $taxonomy->label;
						}
					}
				}
			}
			$custom_attributes = $attributes;

			if ( ! is_array( $custom_attributes ) ) {
				$custom_attributes = array( $custom_attributes );
			}

			return $custom_attributes;
		}

		function get_custom_attribute_for_cell( $post, $cell_key, $cell_args ) {
			$attribute_key = $cell_args['attribute_key'];
			$value         = '';
			if ( $post->post_type === 'product' ) {
				$attributes = VGSE()->helpers->get_current_provider()->get_item_meta( $post->ID, '_product_attributes', true );
				$value      = ( is_array( $attributes ) && isset( $attributes[ $attribute_key ] ) ) ? $attributes[ $attribute_key ]['value'] : '';
			} elseif ( $post->post_type === 'product_variation' ) {
				$raw_value = VGSE()->helpers->get_current_provider()->get_item_meta( $post->ID, 'attribute_' . $attribute_key, true );
				$value     = ( ! empty( $raw_value ) ) ? $raw_value : '';
			}
			return $value;
		}

		function update_custom_attribute_from_cell( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			$attribute_key = $cell_args['attribute_key'];
			$post          = get_post( $post_id );

			if ( $post->post_type === 'product' ) {

				$attributes = get_post_meta( $post_id, '_product_attributes', true );
				if ( empty( $attributes ) || ! is_array( $attributes ) ) {
					$attributes = array();
				}

				$new_attribute_settings = ( is_array( $attributes ) && isset( $attributes[ $attribute_key ] ) ) ? $attributes[ $attribute_key ] : array();

				// Bail if the attribute value is empty and the product wasn't using the attribute
				if ( empty( $data_to_save ) && empty( $new_attribute_settings ) ) {
					return;
				}

				$data_to_save = trim( $data_to_save );
				if ( empty( $new_attribute_settings ) ) {
					$all_attributes         = $this->get_all_attributes();
					$new_attribute_settings = array(
						'name'         => $all_attributes['custom'][ $attribute_key ],
						'value'        => $data_to_save,
						'is_taxonomy'  => 0,
						'position'     => count( $attributes ),
						'is_visible'   => $this->is_attribute_visible( $attribute_key ),
						'is_variation' => $this->is_attribute_for_variations( $attribute_key, $post_id ),
					);
				} else {
					$new_attribute_settings['value'] = $data_to_save;
				}
				if ( ! empty( $new_attribute_settings['value'] ) ) {
					$attributes[ $attribute_key ] = $new_attribute_settings;
				} elseif ( isset( $attributes[ $attribute_key ] ) ) {
					unset( $attributes[ $attribute_key ] );
				}
				update_post_meta( $post_id, '_product_attributes', $attributes );
			} elseif ( $post->post_type === 'product_variation' ) {
				if ( ! empty( $data_to_save ) ) {
					update_post_meta( $post_id, 'attribute_' . $attribute_key, $data_to_save );
				} else {
					delete_post_meta( $post_id, 'attribute_' . $attribute_key );
				}
			}
		}

		/**
		 * Callback when the product was updated.
		 * @param int $product_id
		 * @param mixed $new_value
		 * @param string $key
		 * @param string $data_source
		 */
		function _sync_product_terms( $product_id, $new_value, $key, $data_source, $row = array() ) {

			// sync woocommerce attributes
			if ( $data_source !== 'post_terms' || strpos( $key, 'pa_' ) === false ) {
				return;
			}
			if ( is_array( $row ) && isset( $row['post_type'] ) && $row['post_type'] === 'product_variation' ) {
				vgse_init_WooCommerce_Variations()->_save_variation_global_attribute( $product_id, $key, $new_value );
			}

			// We can't use the provider's get_item_meta function because it uses the object cache
			// and we need to read the real value from the database everytime otherwise it won't save
			// all attributes on next calls from the same ajax call
			$attributes = maybe_unserialize( get_post_meta( $product_id, '_product_attributes', true ) );
			if ( empty( $attributes ) || ! is_array( $attributes ) ) {
				$attributes = array();
			}
			$attribute_key             = sanitize_title( $key );
			$product_type              = ( is_array( $row ) && isset( $row['product_type'] ) ) ? $row['product_type'] : VGSE()->WC->get_product_type( $product_id );
			$current_attribute_from_db = isset( $attributes[ $attribute_key ] ) ? $attributes[ $attribute_key ] : array();

			$new_attribute_settings                 = array();
			$new_attribute_settings['is_variation'] = $this->is_attribute_for_variations( $attribute_key, $product_id, $product_type, $current_attribute_from_db );
			$new_attribute_settings['is_visible']   = $this->is_attribute_visible( $attribute_key, $current_attribute_from_db );

			$current_attribute_settings = ! empty( $current_attribute_from_db ) ? $current_attribute_from_db : array(
				'name'         => wc_clean( $key ),
				'value'        => '',
				'is_taxonomy'  => 1,
				'position'     => count( $attributes ),
				'is_visible'   => 0,
				'is_variation' => 0,
			);

			// Add attribute association only if it doesn't exist.
			$attributes[ $attribute_key ] = array_merge( $current_attribute_settings, $new_attribute_settings );

			if ( empty( $new_value ) && isset( $attributes[ $attribute_key ] ) ) {
				unset( $attributes[ $attribute_key ] );
			}
			update_post_meta( $product_id, '_product_attributes', $attributes );

			$this->_transfer_variations_to_global_attribute( $attributes, $new_value, $attribute_key, $product_id, $product_type );
		}

		function _transfer_variations_to_global_attribute( $attributes, $new_value, $attribute_key, $product_id, $product_type ) {
			global $wpdb;
			if ( strpos( $product_type, 'variable' ) === false ) {
				return;
			}

			// Check if we're copying custom attribute into global attribute, automatically migrate variations
			$custom_attributes_with_same_value = wp_list_filter( $attributes, array( 'value' => $new_value ) );
			if ( empty( $custom_attributes_with_same_value ) ) {
				return;
			}
			$custom_attribute_key = null;

			foreach ( array_keys( $custom_attributes_with_same_value ) as $raw_custom_attribute_key ) {
				if ( strpos( $raw_custom_attribute_key, 'pa_' ) === false ) {
					$custom_attribute_key = $raw_custom_attribute_key;
					break;
				}
			}

			if ( ! $custom_attribute_key ) {
				return;
			}

			$children = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = 'product_variation' AND post_parent = %d", $product_id ) );
			foreach ( $children as $child_id ) {
				$variation_attribute_value = get_post_meta( $child_id, 'attribute_' . $custom_attribute_key, true );

				if ( $variation_attribute_value ) {
					$new_attribute_term  = get_term_by( 'name', $variation_attribute_value, $attribute_key );
					$new_attribute_value = $new_attribute_term->slug;
				} else {
					$new_attribute_value = '';
				}
				update_post_meta( $child_id, 'attribute_' . $attribute_key, $new_attribute_value );
				update_post_meta( $child_id, 'attribute_' . $custom_attribute_key, '' );
			}
		}

		function is_attribute_for_variations( $attribute_key, $product_id, $new_product_type = null, $current_attribute_settings = array() ) {
			$is_variation = 0;
			$product_type = ( ! empty( $new_product_type ) ) ? $new_product_type : VGSE()->WC->get_product_type( $product_id );

			if ( $product_type === 'variable' ) {
				$is_variation = empty( VGSE()->options['be_disable_wc_auto_attribute_used_for_variation'] );
			}
			if ( ! empty( $current_attribute_settings ) ) {
				$is_variation = (int) $current_attribute_settings['is_variation'];
			} elseif ( ! empty( VGSE()->options['wc_product_attributes_not_variation'] ) ) {
				$attributes_not_used_for_variations = array_filter( array_map( 'trim', explode( ',', VGSE()->options['wc_product_attributes_not_variation'] ) ) );
				foreach ( $attributes_not_used_for_variations as $attribute_not_used_for_variations ) {
					if ( stripos( $attribute_key, $attribute_not_used_for_variations ) !== false ) {
						$is_variation = 0;
						break;
					}
				}
			}
			return (int) $is_variation;
		}

		function is_attribute_visible( $attribute_key, $current_attribute_settings = array() ) {
			$attribute_visible = empty( VGSE()->options['be_disable_wc_auto_attribute_visible'] );
			if ( ! empty( $current_attribute_settings ) ) {
				$attribute_visible = (int) $current_attribute_settings['is_visible'];
			} elseif ( ! empty( VGSE()->options['wc_product_attributes_is_not_visible'] ) ) {
				$attributes_not_visible = array_filter( array_map( 'trim', explode( ',', VGSE()->options['wc_product_attributes_is_not_visible'] ) ) );
				foreach ( $attributes_not_visible as $attribute_not_visible ) {
					if ( stripos( $attribute_key, $attribute_not_visible ) !== false ) {
						$attribute_visible = 0;
						break;
					}
				}
			}
			return (int) $attribute_visible;
		}

		/**
		 * Register spreadsheet columns
		 */
		function register_columns( $editor ) {
			$post_type = $this->post_type;

			if ( ! in_array( $post_type, $editor->args['enabled_post_types'] ) ) {
				return;
			}

			$all_attributes = $this->get_all_attributes();
			foreach ( $all_attributes['custom'] as $attribute_key => $attribute_label ) {

				$column_key = 'wpseca';
				if ( strlen( $attribute_key ) > 20 ) {
					$column_key .= crc32( $attribute_key );
				} else {
					$column_key .= $attribute_key;
				}
				$editor->args['columns']->register_item(
					$column_key,
					$post_type,
					array(
						'data_type'             => 'meta_data',
						'column_width'          => 200,
						'title'                 => sprintf( __( 'Custom attribute: %s', 'vg_sheet_editor' ), esc_html( $attribute_label ) ),
						'type'                  => '',
						'supports_formulas'     => true,
						'supports_sql_formulas' => false,
						'allow_to_hide'         => true,
						'allow_to_rename'       => true,
						'allow_plain_text'      => true,
						'get_value_callback'    => array( $this, 'get_custom_attribute_for_cell' ),
						'save_value_callback'   => array( $this, 'update_custom_attribute_from_cell' ),
						'allow_to_save'         => true,
						'allow_for_variations'  => true,
						'export_key'            => 'attributes',
						'formatted'             => array(
							'comment' => array( 'value' => __( 'Enter multiple attributes separated by |', 'vg_sheet_editor' ) ),
						),
						'attribute_key'         => $attribute_key,
					)
				);
			}

			$attribute_taxonomies = wc_get_attribute_taxonomies();
			if ( empty( $attribute_taxonomies ) ) {
				$attribute_taxonomies = array();
			}
			$attribute_labels = wp_list_pluck( $attribute_taxonomies, 'attribute_label' );
			$chosen_options   = array();
			foreach ( $attribute_labels as $attribute_label ) {
				$chosen_options[] = array(
					'id'    => $attribute_label,
					'label' => $attribute_label,
				);
			}

			$editor->args['columns']->register_item(
				'_vgse_create_attribute',
				$post_type,
				array(
					'data_type'                  => null,
					'unformatted'                => array(
						'data'     => '_vgse_create_attribute',
						'renderer' => 'html',
						'readOnly' => true,
					),
					'column_width'               => 150,
					'title'                      => __( 'Product attributes', 'vg_sheet_editor' ),
					'supports_formulas'          => true,
					'forced_supports_formulas'   => true,
					'supported_formula_types'    => array( 'clear_value', 'wc_attributes_toggle_setting' ),
					'key_for_formulas'           => '_product_attributes',
					'formatted'                  => array(
						'data'     => '_vgse_create_attribute',
						'renderer' => 'html',
						'readOnly' => true,
					),
					'allow_to_hide'              => true,
					'allow_to_save'              => false,
					'allow_to_rename'            => true,
					'type'                       => 'handsontable',
					'edit_button_label'          => __( 'Edit attributes', 'vg_sheet_editor' ),
					'edit_modal_id'              => 'vgse-edit-attributes',
					'edit_modal_title'           => __( 'Edit attributes', 'vg_sheet_editor' ),
					'edit_modal_description'     => sprintf( __( 'Note: Separate values with the character %1$s<br/>We recommend using Global Attributes if you will use them in many products.<a href="%2$s" target="_blank">Create Global Attribute</a><br>Global attributes have their own columns in the spreadsheet. You can edit them in the columns (faster) or using this popup.<br/><span class="vg-only-variations-enabled">If you are editing the attributes of variations, the variation must be enabled, otherwise the attributes won\'t be saved.</span>', 'vg_sheet_editor' ), WC_DELIMITER, esc_url( admin_url( 'edit.php?post_type=product&page=product_attributes' ) ) ),
					'edit_modal_save_action'     => 'vgse_wc_save_attributes',
					'edit_modal_get_action'      => 'vgse_wc_get_attributes',
					'handsontable_columns'       => array(
						$this->post_type    => array(
							array(
								'data'          => 'name',
								'renderer'      => 'wp_chosen_dropdown',
								'editor'        => 'chosen',
								'width'         => 150,
								'source'        => $chosen_options,
								'chosenOptions' => array(
									'multiple'        => false,
									'search_contains' => true,
									'data'            => $chosen_options,
								),
							),
							array(
								'data' => 'options',
							),
							array(
								'data' => 'position',
								'type' => 'numeric',
							),
							array(
								'data'              => 'visible',
								'type'              => 'checkbox',
								'checkedTemplate'   => true,
								'uncheckedTemplate' => false,
							),
							array(
								'data'              => 'variation',
								'type'              => 'checkbox',
								'checkedTemplate'   => true,
								'uncheckedTemplate' => false,
							),
						),
						'product_variation' => array(
							array(
								'data' => 'name',
							),
							array(
								'data' => 'options',
							),
						),
					),
					'handsontable_column_names'  => array(
						$this->post_type    => array( __( 'Name', 'vg_sheet_editor' ), __( 'Value', 'vg_sheet_editor' ), __( 'Position', 'vg_sheet_editor' ), __( 'Is visible?', 'vg_sheet_editor' ), __( 'Used for variation?', 'vg_sheet_editor' ) ),
						'product_variation' => array( __( 'Name', 'vg_sheet_editor' ), __( 'Value', 'vg_sheet_editor' ) ),
					),
					'handsontable_column_widths' => array(
						$this->post_type    => array( 150, 240, 90, 90, 130 ),
						'product_variation' => array( 150, 240 ),
					),
				)
			);
		}

		/**
		 * Save / get attributes via ajax
		 */
		function save_attributes() {
			if ( empty( VGSE()->helpers->get_nonce_from_request() ) || empty( $_REQUEST['postId'] ) || ! isset( $_REQUEST['data'] ) || ! is_array( $_REQUEST['data'] ) ) {
				wp_send_json_error( array( 'message' => __( 'Missing parameters.', 'vg_sheet_editor' ) ) );
			}

			if ( ! VGSE()->helpers->verify_nonce_from_request() || ! VGSE()->helpers->user_can_edit_post_type( $this->post_type ) ) {
				wp_send_json_error( array( 'message' => __( 'You dont have enough permissions to view this page.', 'vg_sheet_editor' ) ) );
			}
			$post_id   = (int) $_REQUEST['postId'];
			$post      = get_post( $post_id );
			$post_type = $post->post_type;

			if ( $post_type === 'product_variation' ) {
				$product_id = $post->post_parent;
			} else {
				$product_id = $post_id;
			}
			$_product = wc_get_product( $product_id );

			$attributes = $_product->get_attributes();

			$request_data = array();
			foreach ( $_REQUEST['data'] as $index => $attribute ) {
				if ( $post_type === 'product_variation' ) {
					$request_data[] = array(
						'id'     => (int) $attribute['id'],
						'name'   => sanitize_text_field( $attribute['name'] ),
						'option' => sanitize_text_field( $attribute['option'] ),
					);
				} else {
					$request_data[] = array(
						'id'        => (int) $attribute['id'],
						'name'      => sanitize_text_field( $attribute['name'] ),
						'position'  => (int) $attribute['position'],
						'visible'   => (bool) VGSE()->WC->_do_booleable( $attribute['visible'] ),
						'variation' => (bool) VGSE()->WC->_do_booleable( $attribute['variation'] ),
						'options'   => sanitize_text_field( $attribute['options'] ),
					);
				}
			}

			// Save variation attributes
			if ( $post_type === 'product_variation' ) {
				$new_data = array();

				foreach ( $request_data as $attribute ) {
					if ( empty( $attribute['name'] ) ) {
						continue;
					}
					$sanitized_title = sanitize_title( $attribute['name'] );
					if ( isset( $attributes[ 'pa_' . $sanitized_title ] ) ) {
						$key = 'pa_' . $sanitized_title;

						$new_data[] = wp_parse_args(
							array(
								'id' => wc_attribute_taxonomy_id_by_name( $key ),
							),
							$attribute
						);
					} else {
						$new_data[] = wp_parse_args(
							$attribute,
							array(
								'id'   => 0,
								'name' => $sanitized_title,
							)
						);
					}
				}

				$api_response = VGSE()->helpers->create_rest_request(
					'PUT',
					'/wc/v3/products/' . $product_id . '/variations/' . $post_id,
					array(
						'id'          => $post_id,
						'attributes'  => $new_data,
						'wpse_source' => 'save_attributes',
					)
				);

				$variation      = $api_response->get_data();
				$attributes_out = $variation['attributes'];

				$product_data_response = VGSE()->helpers->create_rest_request(
					'GET',
					'/wc/v3/products/' . $product_id
				);
				$product_data          = $product_data_response->get_data();

				$out                             = array(
					'data' => $attributes_out,
				);
				$out['custom_handsontable_args'] = array(
					'columns' => array(
						array(
							'data'   => 'name',
							'type'   => 'autocomplete',
							'source' => array_values(
								wp_list_pluck(
									wp_list_filter(
										$product_data['attributes'],
										array(
											'variation' => true,
										)
									),
									'name'
								)
							),
						),
						array(
							'data'   => 'option',
							'type'   => 'autocomplete',
							'source' => array_reduce(
								wp_list_pluck(
									wp_list_filter(
										$product_data['attributes'],
										array(
											'variation' => true,
										)
									),
									'options'
								),
								'array_merge',
								array()
							),
						),
					),
				);
			} else {

				// Products

				$new_data = array();

				$global_attributes         = wc_get_attribute_taxonomies();
				$attribute_taxonomies_keys = wp_list_pluck( $global_attributes, 'attribute_name', 'attribute_label' );

				foreach ( $request_data as $attribute ) {
					if ( empty( $attribute['name'] ) ) {
						continue;
					}
					// WC uses "position" to determine if it's custom attr.
					// we want to determine it by attr. name.
					//                  unset($attribute['position']);

					$sanitized_title = sanitize_title( $attribute['name'] );
					// Try to guess the global taxonomy key based on the sanitized name
					if ( in_array( $sanitized_title, $attribute_taxonomies_keys ) ) {
						$key = 'pa_' . $sanitized_title;

						$prepared_attribute = wp_parse_args(
							array(
								'id' => wc_attribute_taxonomy_id_by_name( $key ),
							),
							$attribute
						);
						// Try to guess the global taxonomy key based on the DB label
					} elseif ( isset( $attribute_taxonomies_keys[ $attribute['name'] ] ) ) {
						$key                = 'pa_' . $attribute_taxonomies_keys[ $attribute['name'] ];
						$prepared_attribute = wp_parse_args(
							array(
								'id' => wc_attribute_taxonomy_id_by_name( $key ),
							),
							$attribute
						);
					} else {
						$prepared_attribute = wp_parse_args(
							array(
								'id' => 0,
							),
							$attribute
						);
					}

					if ( is_string( $prepared_attribute['options'] ) ) {
						$prepared_attribute['options'] = array_map( 'trim', explode( WC_DELIMITER, $prepared_attribute['options'] ) );
					}
					if ( ! isset( $prepared_attribute['visible'] ) ) {
						$prepared_attribute['visible'] = 0;
					}
					if ( ! isset( $prepared_attribute['variation'] ) ) {
						$prepared_attribute['variation'] = 0;
					}

					$new_data[] = wp_parse_args(
						array(
							'visible'   => VGSE()->WC->_do_booleable( $prepared_attribute['visible'] ),
							'variation' => VGSE()->WC->_do_booleable( $prepared_attribute['variation'] ),
						),
						$prepared_attribute
					);
				}

				$product_update_data = array(
					'ID'          => $product_id,
					'attributes'  => $new_data,
					'wpse_source' => 'save_attributes',
				);
				// Make the product variable if at least one attribute is allowed for variations
				// (in case they edit the attributes before setting the right product type)
				$variation_attributes = wp_list_filter( $new_data, array( 'variation' => true ) );
				if ( ! empty( $variation_attributes ) ) {
					$product_update_data['type'] = 'variable';
				}
				$api_response = VGSE()->WC->update_products_with_api( $product_update_data, 3 );

				$product_data = $api_response->get_data();

				$out = $product_data['attributes'];

				foreach ( $out as $out_index => $item ) {
					$out[ $out_index ]['options'] = implode( ' ' . WC_DELIMITER . ' ', $item['options'] );
				}
			}
			wp_send_json_success( $out );
		}

		/**
		 * Save / get attributes via ajax
		 */
		function get_attributes_for_popup() {
			if ( empty( VGSE()->helpers->get_nonce_from_request() ) || empty( $_REQUEST['postId'] ) ) {
				wp_send_json_error( array( 'message' => __( 'Missing parameters.', 'vg_sheet_editor' ) ) );
			}

			if ( ! VGSE()->helpers->verify_nonce_from_request() || ! VGSE()->helpers->user_can_edit_post_type( $this->post_type ) ) {
				wp_send_json_error( array( 'message' => __( 'You dont have enough permissions to view this page.', 'vg_sheet_editor' ) ) );
			}
			$post_id = (int) $_REQUEST['postId'];
			$out     = $this->_get_attributes_for_popup( $post_id );
			wp_send_json_success( $out );
		}

		function _get_attributes_for_popup( $post_id ) {
			$post      = get_post( $post_id );
			$post_type = $post->post_type;

			if ( $post_type === 'product_variation' ) {
				$product_id = $post->post_parent;
			} else {
				$product_id = $post_id;
			}

			// Variation attributes
			if ( $post_type === 'product_variation' ) {

				$api_response = VGSE()->helpers->create_rest_request( 'GET', '/wc/v1/products/' . $product_id );
				$product_data = $api_response->get_data();

				$variation = current(
					wp_list_filter(
						$product_data['variations'],
						array(
							'id' => $post_id,
						)
					)
				);

				$attributes_out = $variation['attributes'];

				$out                             = array(
					'data' => $attributes_out,
				);
				$out['custom_handsontable_args'] = array(
					'columns' => array(
						array(
							'data'   => 'name',
							'type'   => 'autocomplete',
							'source' => array_values(
								wp_list_pluck(
									wp_list_filter(
										$product_data['attributes'],
										array(
											'variation' => true,
										)
									),
									'name'
								)
							),
						),
						array(
							'data'   => 'option',
							'type'   => 'autocomplete',
							'source' => array_reduce(
								wp_list_pluck(
									wp_list_filter(
										$product_data['attributes'],
										array(
											'variation' => true,
										)
									),
									'options'
								),
								'array_merge',
								array()
							),
						),
					),
				);
			} else {

				// Products
				$api_response = VGSE()->helpers->create_rest_request( 'GET', '/wc/v1/products/' . $product_id );

				$product_data = $api_response->get_data();

				$out = $product_data['attributes'];

				foreach ( $out as $out_index => $item ) {
					$out[ $out_index ]['options'] = implode( ' ' . WC_DELIMITER . ' ', $item['options'] );
				}
			}
			return $out;
		}

		/**
		 * Creates or returns an instance of this class.
		 *
		 *
		 */
		static function get_instance() {
			if ( ! self::$instance ) {
				self::$instance = new WP_Sheet_Editor_WooCommerce_Attrs();
				self::$instance->init();
			}
			return self::$instance;
		}

		function __set( $name, $value ) {
			$this->$name = $value;
		}

		function __get( $name ) {
			return $this->$name;
		}

	}

}


if ( ! function_exists( 'vgse_init_WooCommerce_Attrs' ) ) {

	function vgse_init_WooCommerce_Attrs() {
		return WP_Sheet_Editor_WooCommerce_Attrs::get_instance();
	}
}

vgse_init_WooCommerce_Attrs();
