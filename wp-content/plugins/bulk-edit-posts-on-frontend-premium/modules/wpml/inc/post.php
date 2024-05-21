<?php defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPSE_WPML_Posts' ) ) {

	class WPSE_WPML_Posts {

		private static $instance = false;

		private function __construct() {

		}

		public function init() {
			add_action( 'vg_sheet_editor/editor/register_columns', array( $this, 'register_columns' ) );
			add_action( 'woocommerce_rest_insert_product_variation_object', array( $this, 'sync_translation_fields_after_wc_rest_variation_inserted' ), 90, 2 );
			add_action( 'product_variation_linked', array( $this, 'sync_translation_fields' ), 90, 1 );
			add_action( 'vg_sheet_editor/save_rows/before_saving_rows', array( $this, 'stop_automatic_wpml_syncing' ), 10, 2 );
			add_filter( 'vg_sheet_editor/formulas/sql_execution/can_execute', array( $this, 'disable_sql_formulas_to_allow_translation_syncing' ), 9999 );
			add_action( 'vg_sheet_editor/woocommerce/after_variations_created', array( $this, 'add_lang_to_new_variations' ) );

			add_action( 'vg_sheet_editor/save_rows/after_saving_post', array( $this, 'sync_translation_fields_after_saving_post' ), 50, 2 );
			add_filter( 'vg_sheet_editor/save_rows/row_data_before_save', array( $this, 'sync_fields_if_new_post' ), 10, 3 );
			add_action( 'vg_sheet_editor/provider/post/post_converted_to_product', array( $this, 'post_converted_to_product' ) );
			add_action( 'vg_sheet_editor/formulas/execute_formula/after_execution_on_field', array( $this, 'sync_translation_fields_after_formula' ), 10, 4 );

			add_action( 'vg_sheet_editor/woocommerce/variable_product_updated', array( $this, 'after_wc_variations_updated' ), 10, 4 );
			add_action( 'vg_sheet_editor/filters/after_advanced_fields_section', array( $this, 'add_wpml_language_search' ) );
			add_filter( 'posts_clauses', array( $this, 'search_by_wpml_translation' ), 10, 2 );
			add_filter( 'vg_sheet_editor/filters/sanitize_request_filters', array( $this, 'register_custom_filters' ), 10, 2 );
			add_filter( 'vg_sheet_editor/woocommerce/wc_rest_api_product_args', array( $this, 'add_current_language_to_wc_rest_api_requests' ) );
			add_action( 'vg_sheet_editor/add_new_posts/after_all_posts_created', array( $this, 'set_current_language_to_new_rows' ), 10, 2 );
			add_filter( 'vg_sheet_editor/import/save_rows_args', array( $this, 'remove_sku_from_wc_product_translations_import' ) );
		}

		/**
		 * Don't import SKUs on WooCommerce product translations
		 * because it causes a bug in WPML where it updates the original product
		 *
		 * @param  array $args
		 * @return array
		 */
		function remove_sku_from_wc_product_translations_import( $args ) {
			if ( class_exists( 'WooCommerce' ) && $args['post_type'] === 'product' && ! WP_Sheet_Editor_WPML_Obj()->is_the_default_language() ) {
				foreach ( $args['data'] as $index => $row ) {
					if ( ! empty( $row['sku'] ) ) {
						unset( $args['data'][ $index ]['sku'] );
					}
				}
			}
			return $args;
		}

		function set_current_language_to_new_rows( $new_posts_ids, $post_type ) {
			if ( post_type_exists( $post_type ) ) {
				foreach ( $new_posts_ids as $post_id ) {
					$this->sync_translation_fields( $post_id );
				}
			}
			return $new_posts_ids;
		}

		function add_current_language_to_wc_rest_api_requests( $product ) {
			global $sitepress;
			if ( ! isset( $product['lang'] ) ) {
				$product['lang'] = $sitepress->get_current_language();
			}
			return $product;
		}
		function register_custom_filters( $sanitized_filters, $dirty_filters ) {

			if ( isset( $dirty_filters['wpml_translations_missing'] ) ) {
				$sanitized_filters['wpml_translations_missing'] = array();
				foreach ( $dirty_filters['wpml_translations_missing'] as $language ) {
					if ( is_string( $language ) && preg_match( '/^[a-z]{2}$/', $language ) ) {
						$sanitized_filters['wpml_translations_missing'][] = sanitize_text_field( $language );
					}
				}
			}
			return $sanitized_filters;
		}


		function search_by_wpml_translation( $clauses, $wp_query ) {
			global $sitepress, $wpdb;

			if ( empty( $wp_query->query['wpse_original_filters'] ) || empty( $wp_query->query['wpse_original_filters']['wpml_translations_missing'] ) ) {
				return $clauses;
			}
			if ( ! WP_Sheet_Editor_WPML_Obj()->is_the_default_language() ) {
				return $clauses;
			}

			$missing_languages = $wp_query->query['wpse_original_filters']['wpml_translations_missing'];
			$wpml_languages    = wp_list_pluck( $sitepress->get_active_languages(), 'display_name', 'code' );

			// Sanitize. We remove any value received not found in the active wpml languages,
			// and any value that doesn't have 2 letters only.
			foreach ( $missing_languages as $index => $missing_language ) {
				if ( ! isset( $wpml_languages[ $missing_language ] ) || ! preg_match( '/^[a-z]{2}$/', $missing_language ) ) {
					unset( $missing_languages[ $index ] );
				}
			}

			$sql = " AND wpml_translations.trid IN (
				SELECT trid
				FROM {$wpdb->prefix}icl_translations translations
				WHERE NOT EXISTS (
				SELECT inner_translations.trid
				FROM {$wpdb->prefix}icl_translations inner_translations
				WHERE inner_translations.trid = translations.trid
				AND inner_translations.language_code IN ('" . implode( "','", $missing_languages ) . "') ) ) ";

			$clauses['where'] .= $sql;

			return $clauses;
		}
		function add_wpml_language_search( $spreadsheet_key ) {
			global $sitepress;
			if ( ! VGSE()->helpers->get_current_provider()->is_post_type ) {
				return;
			}
			if ( ! WP_Sheet_Editor_WPML_Obj()->is_the_default_language() ) {
				return;
			}

			if ( ! is_post_type_translated( $spreadsheet_key ) ) {
				return;
			}
			$wpml_languages = wp_list_pluck( $sitepress->get_active_languages(), 'display_name', 'code' );
			?>
						<li class="wpml-languages-without-translations">
							<label><?php echo __( 'WPML - Missing translations in these languages', 'vg_sheet_editor' ); ?>  <a href="#" data-wpse-tooltip="right" aria-label="<?php esc_attr_e( 'For example, select "Spanish" and "German" here and we\'ll find products that don\'t have spanish translations or german translations.', 'vg_sheet_editor' ); ?>">( ? )</a></label>
							<select name="wpml_translations_missing[]" multiple class="select2">
<option value="">--</option>
			<?php
			foreach ( $wpml_languages as $code => $display_name ) {
				?>
	<option value="<?php echo esc_attr( $code ); ?>"><?php echo esc_html( $display_name ); ?></option>
				<?php
			}
			?>
							</select>
						</li>

						<?php

		}

		public function add_lang_to_new_variations( $variation_ids ) {
			global $sitepress, $wpdb;
			$current_language = $sitepress->get_current_language();
			foreach ( $variation_ids as $variation_id ) {
				$has_lang = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}icl_translations WHERE element_type = 'post_product_variation' AND element_id = %d", $variation_id ) );
				if ( ! $has_lang ) {
					$trid     = 1 + (int) $wpdb->get_var( "SELECT MAX(trid) FROM {$wpdb->prefix}icl_translations" );
					$response = $wpdb->insert(
						$wpdb->prefix . 'icl_translations',
						array(
							'element_type'  => 'post_product_variation',
							'element_id'    => $variation_id,
							'trid'          => $trid,
							'language_code' => $current_language,
						)
					);
				}
			}
		}

		public function get_fields_to_sync( $row ) {
			if ( ! function_exists( 'wpml_get_setting' ) ) {
				return $row;
			}
			$out                 = array();
			$wpml_config         = wpml_get_setting( 'translation-management' );
			$wpml_custom_fields  = $wpml_config['custom_fields_translation'];
			$wpml_taxonomies     = wpml_get_setting( 'taxonomies_sync_option' );
			$wpml_custom_fields  = array_merge( $wpml_custom_fields, $wpml_taxonomies );
			$current_post        = get_post( $row['ID'] );
			$excluded_keys       = array( 'ID' );
			$row_post_type       = empty( $row['post_type'] ) ? $current_post->post_type : $row['post_type'];
			$spreadsheet_columns = VGSE()->helpers->get_unfiltered_provider_columns( $row_post_type );

			if ( $row_post_type === $current_post->post_type ) {
				$excluded_keys[] = 'post_type';
			}

			foreach ( $row as $field_key => $value ) {
				$is_meta_column = isset( $spreadsheet_columns[ $field_key ] ) && in_array( $spreadsheet_columns[ $field_key ]['data_type'], array( 'post_meta', 'meta_data' ), true );

				// Exclude if it's a meta column and it's not found in the WPML config
				if ( ( $is_meta_column && ! isset( $wpml_custom_fields[ $field_key ] ) ) ||
				// Exclude if the field exists in the WPML config and it's marked as ignore
				// or translate (they don't require syncing because they're translated separately in each post)
				( isset( $wpml_custom_fields[ $field_key ] ) && in_array( (int) $wpml_custom_fields[ $field_key ], array( 0, 2 ), true ) ) ||
				// Exclude if the field is found in our manual exclusion list
				in_array( $field_key, $excluded_keys, true ) ) {
					continue;
				}
				$out[ $field_key ] = $value;
			}
			return $out;
		}

		public function after_wc_variations_updated( $final, $request, $variations_rows, $original_variation_rows ) {
			$has_fields_to_sync = false;
			foreach ( $original_variation_rows as $row ) {
				$syncable_fields = $this->get_fields_to_sync( $row );
				if ( $syncable_fields ) {
					$has_fields_to_sync = true;
					break;
				}
			}

			if ( $has_fields_to_sync ) {
				$this->sync_translation_fields( $final['ID'] );
			}
		}
		public function sync_translation_fields_after_saving_post( $post_id, $item ) {
			if ( ! VGSE()->helpers->get_current_provider()->is_post_type ) {
				return;
			}
			$syncable_fields = $this->get_fields_to_sync( $item );
			if ( ! $syncable_fields ) {
				return;
			}
			// We don't sync variation changes here because variation haven't been saved yet. We save variations later in the page cycle
			if ( class_exists( 'WooCommerce' ) && ! empty( $item['post_type'] ) && $item['post_type'] === 'product_variation' ) {
				return;
			}

			$this->sync_translation_fields( $post_id );
		}

		public function sync_translation_fields_after_formula( $post_id, $initial_data, $modified_data, $column ) {

			if ( ! VGSE()->helpers->get_current_provider()->is_post_type ) {
				return;
			}
			$syncable_fields = $this->get_fields_to_sync( array( $column => $modified_data ) );
			if ( $syncable_fields ) {
				$this->sync_translation_fields( $post_id );
			}
		}

		public function disable_sql_formulas_to_allow_translation_syncing( $allowed ) {
			if ( VGSE()->helpers->get_current_provider()->is_post_type ) {
				$allowed = false;
			}
			return $allowed;
		}

		public function post_converted_to_product( $post_id ) {
			global $wpdb;
			$wpdb->update(
				$wpdb->prefix . 'icl_translations',
				array(
					'element_type' => 'post_product',
				),
				array(
					'element_type' => 'post_post',
					'element_id'   => $post_id,
				)
			);
		}

		public function sync_fields_if_new_post( $item, $post_id, $post_type ) {
			global $wpdb;
			if ( ! VGSE()->helpers->get_current_provider()->is_post_type ) {
				return $item;
			}
			$post       = get_post( $post_id );
			$sql        = "SELECT * FROM {$wpdb->prefix}icl_translations WHERE element_type = %s AND element_id = %d";
			$row_exists = $wpdb->get_row( $wpdb->prepare( $sql, 'post_' . $post->post_type, $post_id ) );
			if ( ! $row_exists ) {
				$this->sync_translation_fields( $post_id );
			}
			return $item;
		}

		public function sync_translation_fields_after_wc_rest_variation_inserted( $variation, $request ) {
			// Exit if this REST request didn't come from WPSE
			if ( empty( $request['wpse_source'] ) ) {
				return;
			}
			// Exit if this REST request came from the save rows process
			// Because the save rows process has its own WPML syncing mechanism
			// We use this sync function only for other REST calls
			if ( $request['wpse_source'] === 'save_rows' ) {
				return;
			}
			$this->sync_translation_fields( $variation->get_id() );
		}


		public function sync_translation_fields( $post_id ) {
			global $wpml_post_translations, $sitepress, $wpdb, $woocommerce_wpml, $wp_object_cache;
			if ( ! VGSE()->helpers->get_current_provider()->is_post_type ) {
				return;
			}

			$post    = get_post( $post_id );
			$main_id = $this->get_main_post_id( $post_id );
			if ( is_object( $wp_object_cache ) ) {
				$wp_object_cache->flush();
			}

			// This way is cleaner programmatically but the syncing of parent variable products is too slow

			$current_language_code = ( ! empty( $_GET['lang'] ) ) ? $_GET['lang'] : $sitepress->get_current_language();
			$is_wc_product         = class_exists( 'WooCommerce' ) && class_exists( 'WP_Sheet_Editor_WooCommerce' ) && in_array( $post->post_type, array( 'product_variation', 'product' ), true );
			if ( $is_wc_product && class_exists( '\WCML\Rest\ProductSaveActions' ) ) {
				if ( get_post_type( $post_id ) === 'product_variation' ) {
					$post_id = get_post_field( 'post_parent', $post_id );
				}
				$product_save_actions = new \WCML\Rest\ProductSaveActions( $sitepress->get_settings(), $wpdb, $sitepress, $woocommerce_wpml->sync_product_data );
				$product_save_actions->run( wc_get_product( $post_id ), null, $current_language_code, $main_id );

				// Better way, but needs some testing
				// $woocommerce_wpml->sync_product_data->woocommerce_product_quick_edit_save( wc_get_product( $post_id ) );
			} else {

				$trid                        = WP_Sheet_Editor_WPML_Obj()->get_main_translation_id( $post_id, 'post_' . get_post_type( $post_id ), true );
				$old_POST                    = $_POST;
				$_POST['icl_translation_of'] = $main_id ? $main_id : 'none';
				$_POST['icl_trid']           = $trid;
				$_POST['icl_post_language']  = $wpml_post_translations->get_save_post_lang( $post_id, $sitepress );
				$wpml_post_translations->save_post_actions( $post_id, $post );
				$_POST = $old_POST;
			}
		}

		public function get_main_post_id( $post_id ) {
			global $wpdb;
			$main_trid = (int) WP_Sheet_Editor_WPML_Obj()->get_main_translation_id( $post_id, 'post_' . get_post_type( $post_id ), true );

			$main_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE trid = %d AND source_language_code IS NULL", (int) $main_trid ) );

			return $main_id;
		}

		public function stop_automatic_wpml_syncing( $data, $post_type ) {
			global $wpml_post_translations;
			if ( ! VGSE()->helpers->get_current_provider()->is_post_type ) {
				return;
			}
			remove_action( 'save_post', array( $wpml_post_translations, 'save_post_actions' ), 100 );
		}

		/**
		 * Register spreadsheet columns
		 */
		public function register_columns( $editor ) {
			global $sitepress;
			if ( $editor->provider->key === 'user' ) {
				return;
			}
			if ( ! $editor->provider->is_post_type ) {
				return;
			}
			$post_types = $editor->args['enabled_post_types'];
			foreach ( $post_types as $post_type ) {
				if ( WP_Sheet_Editor_WPML_Obj()->is_the_default_language() ) {
					$editor->args['columns']->register_item(
						'wpml_duplicate',
						$post_type,
						array(
							'data_type'             => 'meta_data',
							'column_width'          => 150,
							'title'                 => __( 'WPML - Duplicate', 'vg_sheet_editor' ),
							'type'                  => '',
							'supports_formulas'     => true,
							'supports_sql_formulas' => false,
							'allow_to_hide'         => true,
							'allow_to_rename'       => true,
							'allow_plain_text'      => true,
							'formatted'             => array(
								'comment' => array( 'value' => __( 'Enter multiple language codes separated by commas and we will create copies of the main language. For example: en, es. Existing languages will be skipped.', 'vg_sheet_editor' ) ),
							),
							'save_value_callback'   => array( $this, 'duplicate_to_language' ),
						)
					);
				}
				$editor->args['columns']->register_item(
					'icl_translation_of',
					$post_type,
					array(
						'data_type'             => 'meta_data',
						'column_width'          => 200,
						'title'                 => __( 'WPML - Translation of', 'vg_sheet_editor' ),
						'type'                  => '',
						'supports_formulas'     => true,
						'supports_sql_formulas' => false,
						'allow_to_hide'         => true,
						'allow_to_rename'       => true,
						'allow_plain_text'      => true,
						'get_value_callback'    => array( $this, 'get_translation_of_cell' ),
						'save_value_callback'   => array( $this, 'update_translation_of_cell' ),
						'is_locked'             => WP_Sheet_Editor_WPML_Obj()->is_the_default_language(),
						'allow_to_save'         => ( WP_Sheet_Editor_WPML_Obj()->is_the_default_language() ) ? false : true,
					)
				);
				$editor->args['columns']->register_item(
					'wpml_relationship',
					$post_type,
					array(
						'data_type'             => 'meta_data',
						'column_width'          => 150,
						'title'                 => __( 'WPML - Relationship', 'vg_sheet_editor' ),
						'type'                  => '',
						'supports_formulas'     => true,
						'supports_sql_formulas' => false,
						'allow_to_hide'         => true,
						'allow_to_rename'       => true,
						'allow_plain_text'      => true,
						'formatted'             => array(
							'editor'        => 'select',
							'selectOptions' => array(
								''                     => '',
								'duplicate_from_main'  => __( 'Duplicate from the main language', 'vg_sheet_editor' ),
								'translate_separately' => __( 'Translate separately', 'vg_sheet_editor' ),
							),
						),
						'save_value_callback'   => array( $this, 'set_translation_relationship' ),
						'get_value_callback'    => array( $this, 'get_translation_relationship' ),
						'is_locked'             => WP_Sheet_Editor_WPML_Obj()->is_the_default_language(),
						'allow_to_save'         => ( WP_Sheet_Editor_WPML_Obj()->is_the_default_language() ) ? false : true,
					)
				);
				$editor->args['columns']->register_item(
					'wpml_language',
					$post_type,
					array(
						'data_type'             => 'meta_data',
						'column_width'          => 150,
						'title'                 => __( 'WPML - Language', 'vg_sheet_editor' ),
						'type'                  => '',
						'supports_formulas'     => true,
						'supports_sql_formulas' => false,
						'allow_to_hide'         => true,
						'allow_to_rename'       => true,
						'allow_plain_text'      => true,
						'allow_to_save'         => true,
						'formatted'             => array(
							'editor'        => 'select',
							'selectOptions' => wp_list_pluck( $sitepress->get_active_languages(), 'display_name', 'code' ),
							'comment'       => ( WP_Sheet_Editor_WPML_Obj()->is_the_default_language() ) ? null : array( 'value' => __( 'You can change the language of this post. If the translation for the new language exists, this change will not be applied.', 'vg_sheet_editor' ) ),
						),
						'get_value_callback'    => array( $this, 'get_post_language' ),
						'save_value_callback'   => array( $this, 'save_post_language' ),
					)
				);
				$editor->args['columns']->register_item(
					'translation_priority',
					$post_type,
					array(
						'data_type'         => 'post_terms',
						'column_width'      => 150,
						'title'             => __( 'WPML - Translation priority', 'vg_sheet_editor' ),
						'type'              => '',
						'supports_formulas' => true,
						'formatted'         => array(
							'type'   => 'autocomplete',
							'source' => 'loadTaxonomyTerms',
						),
						'allow_to_hide'     => true,
						'allow_to_rename'   => true,
					)
				);
			}
		}

		public function get_post_language( $post, $cell_key, $cell_args ) {
			global $wpdb;

			return $wpdb->get_var( $wpdb->prepare( 'SELECT language_code FROM ' . $wpdb->prefix . 'icl_translations WHERE element_type = %s AND element_id = %d', 'post_' . $post->post_type, $post->ID ) );
		}

		public function get_translation_relationship( $post, $cell_key, $cell_args ) {
			$duplicate_of = (int) get_post_meta( $post->ID, '_icl_lang_duplicate_of', true );
			$value        = $duplicate_of ? 'duplicate_from_main' : 'translate_separately';
			return $value;
		}

		public function get_translation_of_cell( $post, $cell_key, $cell_args ) {
			$main_id = (int) $this->get_main_post_id( $post->ID );
			$value   = ( $main_id && $main_id !== $post->ID ) ? get_the_title( $main_id ) : '';
			return $value;
		}

		public function update_translation_of_cell( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			global $wpdb, $sitepress;
			$data_to_save = trim( $data_to_save );
			if ( empty( $data_to_save ) ) {
				$wpdb->update(
					$wpdb->prefix . 'icl_translations',
					array(
						'source_language_code' => null,
						'language_code'        => $sitepress->get_current_language(),
					),
					array(
						'element_id'   => (int) $post_id,
						'element_type' => 'post_' . esc_sql( $post_type ),
					),
					array( '%s', '%s' ),
					array( '%d', '%s' )
				);
				return;
			}

			if ( is_numeric( $data_to_save ) && get_post_status( (int) $data_to_save ) ) {
				$main_post_id = (int) $data_to_save;
			} else {
				$main_post_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = %s AND post_title = %s LIMIT 1 ", $post_type, $data_to_save ) );
			}
			if ( $main_post_id ) {
				$trid = WP_Sheet_Editor_WPML_Obj()->get_main_translation_id( $main_post_id, 'post_' . esc_sql( $post_type ), is_numeric( $data_to_save ) );
				$wpdb->update(
					$wpdb->prefix . 'icl_translations',
					array(
						'trid'                 => $trid,
						'source_language_code' => $sitepress->get_default_language(),
					),
					array(
						'element_type' => 'post_' . esc_sql( $post_type ),
						'element_id'   => (int) $post_id,
					)
				);
			}
		}

		public function set_translation_relationship( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			global $iclTranslationManagement, $sitepress, $wpdb;

			if ( $data_to_save === 'duplicate_from_main' ) {
				$original_id = (int) $this->get_main_post_id( $post_id );
				$iclTranslationManagement->set_duplicate( $original_id, $sitepress->get_current_language() );
			} elseif ( $data_to_save === 'translate_separately' ) {
				$iclTranslationManagement->reset_duplicate_flag( $post_id );
			} else {
				return;
			}
		}

		public function duplicate_to_language( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			global $iclTranslationManagement, $wpdb;

			// Skip if the post to be duplicated is not the original post
			$main_post_id = $this->get_main_post_id( $post_id );
			if ( $main_post_id !== $post_id ) {
				return;
			}

			// Remove the flag _icl_lang_duplicate_of because main posts are not duplicates of other posts
			// If this flag is found, it will cause a server timeout because it should never exist in original posts
			$duplicate_of = get_post_meta( $post_id, '_icl_lang_duplicate_of', true );
			if ( $duplicate_of ) {
				delete_post_meta( $post_id, '_icl_lang_duplicate_of' );
			}

			$mdata                       = array(
				'duplicate_to' => array(),
			);
			$mdata['iclpost']            = array( $post_id );
			$new_langs                   = array_filter( array_map( 'trim', explode( ',', strtolower( $data_to_save ) ) ) );
			$existing_languages_for_post = $wpdb->get_col( $wpdb->prepare( "SELECT language_code FROM {$wpdb->prefix}icl_translations WHERE trid IN (SELECT trid FROM {$wpdb->prefix}icl_translations WHERE element_id = %d AND element_type LIKE %s)", $post_id, 'post\_%' ) );
			$new_langs                   = array_diff( $new_langs, $existing_languages_for_post );
			if ( empty( $new_langs ) ) {
				return;
			}
			foreach ( $new_langs as $lang ) {
				$mdata['duplicate_to'][ $lang ] = 1;
			}

			$iclTranslationManagement->make_duplicates( $mdata );
			do_action( 'wpml_new_duplicated_terms', (array) $mdata['iclpost'], false );
		}

		public function save_post_language( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			global $wpdb, $sitepress;

			$new_language = strtolower( $data_to_save );
			// This if was preventing us from being able to move translations from one language to another
			// if ( ! icl_is_language_active( $data_to_save ) ) {
			// 	return;
			// }

			// Exit if there is a translation in the new language already
			$translation_for_new_language_exists = (int) $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'icl_translations WHERE language_code = %s AND element_type = %s AND element_id = %d ', $new_language, 'post_' . $post_type, $post_id ) );
			if ( $translation_for_new_language_exists ) {
				return;
			}

			$current_lang = $this->get_post_language( get_post( $post_id ), null, null );

			$args = array(
				'language_code'        => $new_language,
				// Don't set a source lang if the new language is the default language, or we're moving from the default lang into another lang
				'source_language_code' => ( $new_language === $sitepress->get_default_language() || $current_lang === $sitepress->get_default_language() ) ? null : $sitepress->get_default_language(),
			);

			$wpdb->update(
				$wpdb->prefix . 'icl_translations',
				$args,
				array(
					'element_type' => 'post_' . esc_sql( $post_type ),
					'element_id'   => (int) $post_id,
				)
			);

			// If we change the language of a parent post, automatically change the language of the children.
			// I.e. if we change the language of a WC product, change it in the variations too
			$children = $wpdb->get_results( $wpdb->prepare( 'SELECT ID,post_type FROM %i WHERE post_parent = %d', $wpdb->posts, $post_id ), ARRAY_A );
			foreach ( $children as $child ) {
				$this->save_post_language( (int) $child['ID'], $cell_key, $data_to_save, $child['post_type'], $cell_args, $spreadsheet_columns );
			}
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if ( ! self::$instance ) {
				self::$instance = new WPSE_WPML_Posts();
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

if ( ! function_exists( 'WPSE_WPML_Posts_Obj' ) ) {

	function WPSE_WPML_Posts_Obj() {
		return WPSE_WPML_Posts::get_instance();
	}
}
WPSE_WPML_Posts_Obj();
