<?php defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_Sheet_Editor_Formulas' ) ) {

	/**
	 * Use formulas in the spreadsheet editor to update a lot of posts at once.
	 */
	class WP_Sheet_Editor_Formulas {

		private static $instance     = false;
		public $plugin_url           = null;
		public $plugin_dir           = null;
		public $documentation_url    = '';
		static $regex_flag           = '[::regex::]';
		public $sequential_number    = 1;
		public $current_bulk_edit_id = null;

		private function __construct() {

		}

		function init() {

			$source = VGSE()->helpers->get_provider_from_query_string();
			if ( empty( $source ) ) {
				$source = 'wp-admin';
			}
			$this->documentation_url = 'https://wpsheeteditor.com/documentation/how-to-use-the-formulas/?utm_source=' . $source . '&utm_medium=pro-plugin&utm_campaign=formulas-field-help';
			$this->plugin_url        = plugins_url( '/', __FILE__ );
			$this->plugin_dir        = __DIR__;

			require 'inc/ui.php';

			// Init wp hooks
			add_action( 'wp_ajax_vgse_bulk_edit_formula_big', array( $this, 'bulk_execute_formula_ajax' ) );
		}

		// Fix formula formatting
		function sanitize_formula( $formula ) {

			//          $formula = stripslashes($formula);
			//          $formula = html_entity_decode($formula);
			//          $formula = str_replace('&quot;', '"', $formula);

			return $formula;
		}

		function prepare_formula( $formula, $original_formula ) {
			$out = array(
				'type' => '', // REPLACE, MATH
				'set1' => '', // search, OR MATH formula
				'set2' => '', // replace
			);
			// if REPLACE formula
			if ( strpos( $formula, '=REPLACE(' ) !== false ) {
				if ( strpos( $formula, ',' ) !== false ) {
					$out['type'] = 'REPLACE';

					$regExp                   = '/=REPLACE\(""(.*)\"",""(.*)""\)/s';
					$matched                  = preg_match_all( $regExp, str_replace( '"", ""', '"",""', $formula ), $result );
					$matched_original_formula = preg_match_all( $regExp, str_replace( '"", ""', '"",""', $original_formula ), $result_original_formula );

					// make replacement
					// If the search is current_value, assign the replace string directly
					if ( trim( $result_original_formula[1][0] ) === '$current_value$' ) {
						$replace = ( WP_Sheet_Editor_Helpers::current_user_can( 'unfiltered_html' ) ) ? $result[2][0] : wp_kses_post( $result[2][0] );

						$out['set1'] = '$current_value$';
						$out['set2'] = $replace;
					} else {
						if ( empty( $result[1][0] ) ) {
							$result[1][0] = '';
						}
						if ( empty( $result[2][0] ) ) {
							$result[2][0] = '';
						}
						$search  = ( WP_Sheet_Editor_Helpers::current_user_can( 'unfiltered_html' ) ) ? $result[1][0] : wp_kses_post( $result[1][0] );
						$replace = ( WP_Sheet_Editor_Helpers::current_user_can( 'unfiltered_html' ) ) ? $result[2][0] : wp_kses_post( $result[2][0] );

						$out['set1'] = $search;
						$out['set2'] = $replace;
					}
				} else {
					return new WP_Error( VGSE()->options_key, __( 'Invalid #898AJSI. The replace requires 2 parameters, we received one', 'vg_sheet_editor' ) );
				}
			} elseif ( strpos( $formula, '=MATH(' ) !== false ) {
				$out['type'] = 'MATH';

				// if MATH formula
				if ( strpos( $formula, ' ' ) !== false ) {
					$formula = str_replace( ' ', '', $formula );
				}
				$regExp = '/\(\s*"(.*)"\s*\)/i';
				preg_match( $regExp, $formula, $result );

				if ( empty( $result[1] ) ) {
					return new WP_Error( VGSE()->options_key, __( 'Invalid #8W89PQ. Math formula is empty.', 'vg_sheet_editor' ) );
				}
				$raw_formula = $result[1];

				if ( strpos( $raw_formula, ',' ) !== false ) {
					return new WP_Error( VGSE()->options_key, sprintf( __( 'Invalid #PQ8SPQ. The math formula contains a comma: %s', 'vg_sheet_editor' ), $formula ) );
				}
				$formula = preg_replace( '/[^0-9\.\+\*\-\/\(\)]/', '', $raw_formula );

				if ( empty( $formula ) && ! empty( $raw_formula ) || ! preg_match( '/\d/', $formula ) ) {
					return new WP_Error( VGSE()->options_key, __( 'Invalid #8W89PWQ. Math formula is invalid.', 'vg_sheet_editor' ) );
				}

				chdir( dirname( __DIR__ ) );

				$out['set1'] = $formula;
			} elseif ( strpos( $formula, '=PHP:' ) !== false && VGSE()->helpers->user_can_manage_options() ) {
				$out['type'] = 'PHP';
				$php_formula = preg_replace( '/[^A-Za-z0-9\_\:]/', '', str_replace( array( '=PHP:', '()' ), '', $formula ) );

				if ( ! function_exists( $php_formula ) && ! is_callable( $php_formula ) ) {
					return new WP_Error( VGSE()->options_key, sprintf( __( 'Invalid #PQ8PHP. The PHP function does not exist: %s', 'vg_sheet_editor' ), $php_formula ) );
				} else {
					$out['set1'] = $php_formula;
				}
			} else {
				return new WP_Error( VGSE()->options_key, __( 'Invalid #8W23CV. We accept MATH and REPLACE formulas only, we received an unknown formula type.', 'vg_sheet_editor' ) );
			}

			return $out;
		}

		/**
		 * Backwards compatibility
		 *
		 * @return string
		 */
		function get_uuid() {
			return VGSE()->helpers->get_uuid();
		}

		function _replace_placeholders( $formula, $data, $post_id, $post_type, $cell_args ) {

			// Replacing placeholders with real values
			$regex_flag = self::$regex_flag;
			if ( strpos( $formula, $regex_flag ) !== false ) {
				return $formula;
			}
			$formula = str_replace( '$current_value$', $data, $formula );
			$formula = str_replace( '$current_value_capitalize_each_word$', trim( ucwords( mb_strtolower( $data ) ) ), $formula );
			$formula = str_replace( '$current_value_lowercase$', mb_strtolower( $data ), $formula );
			$formula = str_replace( '$current_value_uppercase$', mb_strtoupper( $data ), $formula );
			$formula = str_replace( '$random_number$', mt_rand( 10000, 999999 ), $formula );
			$formula = str_replace( '$random_letters$', wp_generate_password( 6, false ), $formula );
			$formula = str_replace( '$uuid$', VGSE()->helpers->get_uuid(), $formula );
			$formula = str_replace( '$uniqid$', uniqid( wp_generate_password( 4 ) ), $formula );
			$formula = str_replace( '$current_timestamp$', time(), $formula );
			$formula = str_replace( '$current_time_friendly$', current_time( 'H:i:s', false ), $formula );
			$formula = str_replace( '$current_date$', date( 'd-m-Y' ), $formula );
			if ( strpos( $formula, '$sequential_number$' ) !== false ) {
				$current_sequence = (int) get_transient( 'vgse_sequence' . $this->current_bulk_edit_id );
				$formula          = str_replace( '$sequential_number$', $current_sequence, $formula );
				set_transient( 'vgse_sequence' . $this->current_bulk_edit_id, $current_sequence + 1, WEEK_IN_SECONDS );
			}
			$formula = str_replace( '$random_date$', VGSE()->helpers->get_random_date_in_range( strtotime( 'January 1st, -2 years' ), time() ), $formula );

			if ( $post_type && post_type_exists( $post_type ) && get_post_meta( $post_id, '_thumbnail_id', true ) ) {
				$thumbnail_id = (int) get_post_meta( $post_id, '_thumbnail_id', true );
				$file_name    = basename( wp_get_attachment_url( $thumbnail_id ) );
				$formula      = str_replace( '$featured_image_file_name$', $file_name, $formula );
			}

			if ( strpos( $formula, '$current_value_excerpt' ) !== false ) {
				preg_match( '/\$current_value_excerpt(\d+)\$/', $formula, $excerpt_variable_matches );
				$excerpt_length = (int) $excerpt_variable_matches[1];
				$formula        = preg_replace( '/\$current_value_excerpt(\d+)\$/', '$current_value_excerpt$', $formula );
				$excerpt        = wp_trim_words( strip_shortcodes( $data ), $excerpt_length, ' ...' );
				$formula        = str_replace( '$current_value_excerpt$', $excerpt, $formula );
			}
			if ( strpos( $formula, '$random_value_start$' ) !== false && strpos( $formula, '$random_value_end$' ) !== false ) {
				$raw_values_list = preg_replace( '/^.+\$random_value_start\$(.+)\$random_value_end\$.+/', '$1', html_entity_decode( $formula ) );
				if ( ! $raw_values_list ) {
					return new WP_Error( VGSE()->options_key, __( 'The random values could not be read.', 'vg_sheet_editor' ) );
				}
				if ( strpos( $raw_values_list, '|' ) === false && strpos( $raw_values_list, '>' ) !== false ) {
					$values_list = array_map( 'trim', explode( '>', $raw_values_list ) );
					$formula     = preg_replace( '/\$random_value_start\$(.+)\$random_value_end\$/', VGSE()->helpers->get_random_date_in_range( strtotime( $values_list[0] ), strtotime( $values_list[1] ) ), $formula );
				} else {
					$values_list = array_map( 'trim', explode( '|', $raw_values_list ) );
					$random_key  = array_rand( $values_list, 1 );
					$formula     = preg_replace( '/\$random_value_start\$(.+)\$random_value_end\$/', $values_list[ $random_key ], $formula );
				}
			}

			// Replacing placeholders for columns names.
			// The column name must be in the format of $column_key$
			if ( ! empty( $post_id ) ) {

				// Replace taxonomy term variables like:
				// $product_cat-name$, $product_cat-slug$, $product_cat-description$
				if ( preg_match( '/-(name|slug|description)\$/', $formula ) ) {
					$taxonomies = get_object_taxonomies( $post_type );
					foreach ( $taxonomies as $taxonomy_key ) {
						if ( strpos( $formula, '$' . $taxonomy_key . '-' ) === false ) {
							continue;
						}
						if ( class_exists( 'WooCommerce' ) && strpos( get_post_type( $post_id ), 'variation' ) !== false && strpos( $taxonomy_key, 'pa_' ) === 0 ) {
							$term_slug = get_post_meta( $post_id, 'attribute_' . $taxonomy_key, true );
							$terms     = $term_slug ? array( get_term_by( 'slug', $term_slug, $taxonomy_key ) ) : false;
						} else {
							$terms = get_the_terms( $post_id, $taxonomy_key );
						}
						$term_properties = array( 'name', 'slug', 'description' );

						foreach ( $term_properties as $term_property ) {
							$term_variable = '$' . $taxonomy_key . '-' . $term_property . '$';
							if ( strpos( $formula, $term_variable ) !== false ) {
								$formula = str_replace( $term_variable, $terms ? implode( '-', array_filter( wp_list_pluck( $terms, $term_property ) ) ) : '', $formula );
							}
						}
					}
				}

				$formula = $this->_replace_column_values_placeholders( $formula, $post_id, $post_type, $cell_args );

				if ( strpos( $formula, '$parent:' ) !== false ) {
					$formula   = str_replace( '$parent:', '$', $formula );
					$parent_id = get_post_field( 'post_parent', $post_id );
					$formula   = $this->_replace_column_values_placeholders( $formula, $parent_id, $post_type, $cell_args );
				}
			}
			$formula = apply_filters( 'vg_sheet_editor/formulas/formula_after_placeholders_replaced', $formula, $data, $post_id, $cell_args, $post_type );
			return $formula;
		}

		function _replace_column_values_placeholders( $formula, $post_id, $post_type, $cell_args ) {

			$columns_regex = '/\$([a-zA-Z0-9_\-\=\[\]]+)\$/';
			$columns_found = preg_match_all( $columns_regex, $formula, $columns_matched );

			if ( ! $columns_found || empty( $columns_matched[1] ) || ! is_array( $columns_matched[1] ) ) {
				return $formula;
			}
			foreach ( $columns_matched[1] as $column_key ) {
				$column_value = VGSE()->helpers->get_column_text_value( $column_key, $post_id, null, $post_type );
				$column_value = apply_filters( 'vg_sheet_editor/formulas/variables/column_value_before_formula_replacement', $column_value, $formula, $column_key, $post_id, $cell_args, $post_type );

				if ( strpos( $formula, '=MATH' ) !== false ) {
					$column_value = (float) $column_value;
				}

				$formula = str_replace( '$' . $column_key . '$', $column_value, $formula );
			}
			return $formula;
		}

		function apply_formula_to_data( $formula, $data, $post_id = null, $cell_args = array(), $post_type = null ) {
			// Fix formula formatting
			$regex_flag       = self::$regex_flag;
			$formula          = $this->sanitize_formula( $formula );
			$original_formula = $formula;

			$original_data = $data;
			if ( strpos( $formula, '=MATH(' ) !== false ) {
				$sanitized_data = trim( $data );
				if ( empty( $sanitized_data ) ) {
					$data = 0;
				}
			}
			$formula          = $this->_replace_placeholders( $formula, $data, $post_id, $post_type, $cell_args );
			$prepared_formula = $this->prepare_formula( $formula, $original_formula );

			if ( ! $prepared_formula || is_wp_error( $prepared_formula ) ) {
				return $prepared_formula;
			}

			if ( $prepared_formula['type'] === 'REPLACE' ) {
				$search  = $prepared_formula['set1'];
				$replace = $prepared_formula['set2'];
				if ( empty( $search ) && empty( $replace ) ) {
					return '';
				}

				// If this column is a users dropdown, convert the username into user ids
				if ( ! empty( $cell_args ) && ! empty( $cell_args['formatted']['source'] ) && $cell_args['formatted']['source'] === 'searchUsers' && empty( $cell_args['prepare_value_for_database'] ) ) {
					if ( ! is_numeric( $search ) && $search !== '$current_value$' ) {
						$search = VGSE()->data_helpers->set_post( 'post_author', $search, $post_id );
					}
					if ( ! is_numeric( $replace ) ) {
						$replace = VGSE()->data_helpers->set_post( 'post_author', $replace, $post_id );
					}
				}

				// If search is empty it means we want to update only empty fields.
				// So we apply the replace only if the existing data is empty
				if ( empty( $search ) && empty( $data ) ) {
					$data = $replace;
				}

				if ( trim( $search ) === '$current_value$' ) {
					$data = $replace;
				} else {
					// Use regex if search has wildcards
					if ( strpos( $search, $regex_flag ) !== false ) {

						$search = htmlspecialchars_decode( str_replace( $regex_flag, '', $search ) );

						$data = preg_replace( "$search", $replace, $data );
						$data = wp_kses_post( $data );
					} else {
						$data = str_replace( $search, $replace, $data );
					}
				}
			} elseif ( $prepared_formula['type'] === 'MATH' ) {
				require_once __DIR__ . '/vendor/autoload.php';

				$formula = $prepared_formula['set1'];
				// if existing field is empty, we assume a value of 0 to allow the math operation
				if ( empty( $data ) ) {
					$data = 0;
				}
				if ( ! is_numeric( $data ) ) {
					return new WP_Error( VGSE()->options_key, sprintf( __( 'The math formula can\'t be applied. We found some existing data is not numeric. Data found: %1$s, ID: %2$d', 'vg_sheet_editor' ), $data, $post_id ) );
				}
				// Execute math operation. It sanitizes the formula automatically.
				$parser         = new \MathParser\StdMathParser();
				$AST            = $parser->parse( $formula );
				$evaluator      = new \MathParser\Interpreting\Evaluator();
				$formula_result = $AST->accept( $evaluator );

				//              $parser = new VG_Math_Calculator();
				//              $formula_result = $parser->calculate($formula);

				$roundup = isset( VGSE()->options['math_formula_roundup_decimals'] ) && is_numeric( VGSE()->options['math_formula_roundup_decimals'] ) ? intval( VGSE()->options['math_formula_roundup_decimals'] ) : 2;
				$data    = round( $formula_result, (int) $roundup );

				if ( $data === $formula ) {
					return new WP_Error( VGSE()->options_key, sprintf( __( 'Error. The math engine could not execute the math operation: %1$s, ID: %2$d', 'vg_sheet_editor' ), $formula, $post_id ) );
				}
			} elseif ( $prepared_formula['type'] === 'PHP' && VGSE()->helpers->user_can_manage_options() ) {
				$php_function = $prepared_formula['set1'];
				$data         = call_user_func( $php_function, $data, $post_id );
			}

			return $data;
		}

		function can_execute_formula_as_sql( $formula, $column, $post_type, $spreadsheet_columns, $raw_form_data ) {
			$custom_check = apply_filters( 'vg_sheet_editor/formulas/sql_execution/can_execute', null, $formula, $column, $post_type, $spreadsheet_columns, $raw_form_data );
			if ( is_bool( $custom_check ) ) {
				return $custom_check;
			}

			// Use column callback to retrieve the cell value
			if ( ! empty( $column['save_value_callback'] ) ) {
				return false;
			}
			if ( in_array( $raw_form_data['action_name'], array( 'add_time', 'reduce_time', 'remove_duplicates', 'remove_duplicates_title_content', 'remove_everything_after', 'remove_everything_before', 'send_email' ), true ) ) {
				return false;
			}
			if ( ! empty( $column['prepare_value_for_database'] ) ) {
				return false;
			}
			if ( empty( $column['supports_sql_formulas'] ) ) {
				return false;
			}
			if ( ! empty( $raw_form_data['use_slower_execution'] ) ) {
				return false;
			}

			// If formula is not replace, exit
			if ( strpos( $formula, '=REPLACE(' ) === false ) {
				return false;
			}

			$final_formula_value = end( $raw_form_data['formula_data'] );
			if ( $column['key'] === 'post_date' && $final_formula_value && $final_formula_value > current_time( 'mysql' ) ) {
				return false;
			}

			// If we are setting post status to publish, use slow execution so wp generates the post slugs
			if ( $column['key'] === 'post_status' && in_array( 'publish', $raw_form_data['formula_data'], true ) && $raw_form_data['action_name'] === 'set_value' ) {
				return false;
			}

			// If we are deleting posts completely, use slow execution
			if ( in_array( $column['key'], array( 'wpse_status', 'post_status' ), true ) && in_array( 'delete', $raw_form_data['formula_data'], true ) ) {
				return false;
			}

			// If formula has wildcards, exit
			if ( strpos( $formula, self::$regex_flag ) !== false ) {
				return false;
			}
			// If data type is not a post, exit
			if ( ! in_array( $column['data_type'], array( 'post_data', 'post_meta', 'meta_data' ) ) ) {
				return false;
			}
			// If value_type is not supported, exit
			$unsupported_value_types = apply_filters( 'vg_sheet_editor/formulas/sql_execution/unsupported_value_types', array(), $formula, $column, $post_type, $spreadsheet_columns );
			if ( ! empty( $unsupported_value_types ) && in_array( $column['value_type'], $unsupported_value_types ) ) {
				return false;
			}

			// If formula has placeholders besides $current_value$, exit
			$formula       = str_replace( '$current_value$', '', $formula );
			$columns_regex = '/\$([a-zA-Z0-9_\-]+)\$/';
			$columns_found = preg_match_all( $columns_regex, $formula, $columns_matched );
			if ( $columns_found ) {
				return false;
			}

			// If the column uses a users dropdown, we need to use PHP processing to convert the username value to a user ID
			if ( ! empty( $column['formatted']['source'] ) && $column['formatted']['source'] === 'searchUsers' ) {
				return false;
			}

			return true;
		}

		function execute_formula_as_sql( $post_ids, $formula, $column, $post_type ) {
			global $wpdb;
			if ( empty( $post_ids ) ) {
				return false;
			}

			$editor               = VGSE()->helpers->get_provider_editor( $post_type );
			$table_name           = $editor->provider->get_table_name_for_field( $column['key_for_formulas'], $column );
			$meta_object_id_field = $editor->provider->get_meta_object_id_field( $column['key_for_formulas'], $column );
			$data_object_id_field = $editor->provider->get_post_data_table_id_key( $post_type );

			$prepared_data = array();
			if ( strpos( $table_name, 'meta' ) === false ) {
				$field_to_update              = VGSE()->helpers->sanitize_table_key( $column['key_for_formulas'] );
				$object_id_field              = $data_object_id_field;
				$extra_where                  = '';
				$prepared_data['extra_where'] = null;
			} else {
				$field_to_update              = 'meta_value';
				$extra_where                  = ' AND meta_key = %s ';
				$object_id_field              = $meta_object_id_field;
				$prepared_data['extra_where'] = $column['key_for_formulas'];
			}

			$sanitized_formula = $this->sanitize_formula( $formula );
			$prepared_formula  = $this->prepare_formula( $sanitized_formula, $sanitized_formula );
			if ( ! $prepared_formula || is_wp_error( $prepared_formula ) || ( empty( $prepared_formula['set1'] ) && empty( $prepared_formula['set2'] ) ) ) {
				return $prepared_formula;
			}

			// If the REPLACE formula contains $current_value$ in the search parameter, it can't have any other text besides $current_value$ because
			// replacing $current_value$+any other text would never work (you can't replace a value that will never exist/match)
			if ( strpos( $prepared_formula['set1'], '$current_value$' ) !== false && trim( $prepared_formula['set1'] ) !== '$current_value$' ) {
				return new WP_Error( VGSE()->options_key, __( 'The search parameter must contain $current_value$ only', 'vg_sheet_editor' ) );
			}
			// If the SEARCH part = $current_value$, it means we're replacing the whole value of the field, so it's a set_value bulk edit
			// And we're setting a full field value, so we run it through _prepare_data_for_saving to prepare the data format, etc.
			// We don't do this when it's not a set_value because it means we're making partial replacements
			if ( $prepared_formula['set1'] === '$current_value$' ) {
				$set2_prepared = $this->_prepare_data_for_saving( $prepared_formula['set2'], $column );
				if ( $set2_prepared === false ) {
					return new WP_Error( VGSE()->options_key, __( 'Value in the replace section is not valid', 'vg_sheet_editor' ) );
				}
				$prepared_formula['set2'] = $set2_prepared;
			}

			if ( empty( $prepared_formula['set1'] ) ) {
				$prepared_formula['set1'] = '$current_value$';

				$extra_checks = array(
					$field_to_update . " = '' ",
					$field_to_update . ' IS NULL ',
				);
				if ( strpos( $table_name, 'meta' ) === false ) {
					$extra_checks[] = $field_to_update . " REGEXP '^[0-9]+$' ";
				}
				$extra_where .= ' AND (' . implode( ' OR ', $extra_checks ) . ' ) ';
			}

			if ( $prepared_formula['set1'] === '$current_value$' ) {
				$search                  = $field_to_update;
				$prepared_data['search'] = null;
			} else {
				$search                  = '%s';
				$prepared_data['search'] = $prepared_formula['set1'];
			}

			if ( strpos( $prepared_formula['set2'], '$current_value$' ) === false ) {
				$replace                  = '%s';
				$prepared_data['replace'] = $prepared_formula['set2'];
			} else {
				$concat_parts             = array_filter( explode( '$$$', preg_replace( '/\$current_value\$/', '$$$' . $field_to_update . '$$$', $prepared_formula['set2'] ) ) );
				$prepared_data['replace'] = array();
				$replace                  = ' CONCAT( ';

				$concat_parts_final = array();
				foreach ( $concat_parts as $concat_part ) {
					// If this is not the current_value, treat it as string
					if ( $concat_part !== $field_to_update ) {
						$concat_parts_final[]       = '%s';
						$prepared_data['replace'][] = $concat_part;
					} else {
						// Current value part just references the db column
						$concat_parts_final[] = $field_to_update;
					}
				}
				$replace .= implode( ',', $concat_parts_final ) . ') ';
			}

			// Insert meta data for posts missing the meta key because the replace only updates existing meta data
			if ( strpos( $table_name, 'meta' ) !== false ) {
				$ids_in_query_placeholders = implode( ', ', array_fill( 0, count( $post_ids ), '%d' ) );
				$prepared_data_for_query   = $post_ids;
				if ( ! is_null( $prepared_data['extra_where'] ) ) {
					$prepared_data_for_query[] = $prepared_data['extra_where'];
				}
				$existing_rows_sql = $wpdb->prepare( "SELECT $object_id_field FROM $table_name WHERE  $object_id_field IN ($ids_in_query_placeholders) $extra_where", $prepared_data_for_query );
				$existing_rows     = $wpdb->get_col( $existing_rows_sql );
				$missing_rows      = array_diff( $post_ids, $existing_rows );

				foreach ( $missing_rows as $missing_rows_object_id ) {
					$wpdb->insert(
						$table_name,
						array(
							$object_id_field => $missing_rows_object_id,
							$field_to_update => '',
							'meta_key'       => $column['key_for_formulas'],
						),
						array(
							'%d',
							'%s',
							'%s',
						)
					);
				}
			}

			$empty_wheres = array();
			// We used to apply this WHERE on post data only because it checks if the field exists with empty string as value
			// but some meta fields were being excluded, so we applied this where to fix it
			//          if (strpos($table_name, 'meta') === false) {
			$empty_wheres[] = " $field_to_update = '' ";
			//          }
			$empty_wheres[] = " $field_to_update IS NULL ";

			$total_updated = 0;

			$prepared_data_for_query = array();
			if ( ! is_null( $prepared_data['search'] ) ) {
				$prepared_data_for_query[] = $prepared_data['search'];
			}
			if ( ! is_null( $prepared_data['replace'] ) && is_array( $prepared_data['replace'] ) ) {
				$prepared_data_for_query = array_merge( $prepared_data_for_query, $prepared_data['replace'] );
			} elseif ( ! is_null( $prepared_data['replace'] ) && ! is_array( $prepared_data['replace'] ) ) {
				$prepared_data_for_query[] = $prepared_data['replace'];
			}
			$ids_in_query_placeholders = implode( ', ', array_fill( 0, count( $post_ids ), '%d' ) );
			$prepared_data_for_query   = array_merge( $prepared_data_for_query, $post_ids );
			if ( ! is_null( $prepared_data['extra_where'] ) ) {
				$prepared_data_for_query[] = $prepared_data['extra_where'];
			}

			$sql            = "UPDATE $table_name SET $field_to_update = REPLACE($field_to_update, $search, $replace ) WHERE  $object_id_field IN ($ids_in_query_placeholders) $extra_where;";
			$total_updated += $wpdb->query( $wpdb->prepare( $sql, $prepared_data_for_query ) );

			$prepared_data_for_query = array();
			if ( ! is_null( $prepared_data['replace'] ) && is_array( $prepared_data['replace'] ) ) {
				$prepared_data_for_query = array_merge( $prepared_data_for_query, $prepared_data['replace'] );
			} elseif ( ! is_null( $prepared_data['replace'] ) && ! is_array( $prepared_data['replace'] ) ) {
				$prepared_data_for_query[] = $prepared_data['replace'];
			}
			$prepared_data_for_query = array_merge( $prepared_data_for_query, $post_ids );
			if ( ! is_null( $prepared_data['extra_where'] ) ) {
				$prepared_data_for_query[] = $prepared_data['extra_where'];
			}
			$sql_empty_fields = "UPDATE $table_name SET $field_to_update = $replace WHERE  $object_id_field IN ($ids_in_query_placeholders) AND (" . implode( ' OR ', $empty_wheres ) . ") $extra_where;";

			$total_updated += $wpdb->query( $wpdb->prepare( $sql_empty_fields, $prepared_data_for_query ) );

			if ( ! empty( VGSE()->options['run_save_post_action_always'] ) && VGSE()->helpers->get_current_provider()->is_post_type ) {
				foreach ( $post_ids as $post_id ) {
					do_action( 'save_post', $post_id, get_post( $post_id ), true );
				}
			}
			return $total_updated;
		}


		/**
		 * Controller - apply formula in bulk
		 */
		function bulk_execute_formula_ajax() {

			if ( empty( VGSE()->helpers->get_nonce_from_request() ) || ! VGSE()->helpers->verify_nonce_from_request() || ! VGSE()->helpers->verify_sheet_permissions_from_request( 'edit' ) ) {
				wp_send_json_error( array( 'message' => __( 'You dont have enough permissions to view this page.', 'vg_sheet_editor' ) ) );
			}

			parse_str( urldecode( html_entity_decode( $_REQUEST['raw_form_data'] ) ), $raw_form_data );
			$settings = array(
				'column'        => sanitize_text_field( $_REQUEST['column'] ),
				'formula'       => strpos( $_REQUEST['formula'], self::$regex_flag ) !== false ? $_REQUEST['formula'] : wp_kses_post( wp_unslash( $_REQUEST['formula'] ) ), // We don't use wp_kses_post for regex formulas because it breaks the regex syntax, we'll sanitize after the string replacement instead
				'post_type'     => VGSE()->helpers->sanitize_table_key( $_REQUEST['post_type'] ),
				'page'          => intval( $_REQUEST['page'] ),
				'wpse_job_id'   => sanitize_text_field( VGSE()->helpers->get_job_id_from_request() ),
				'nonce'         => sanitize_text_field( VGSE()->helpers->get_nonce_from_request() ),
				'filters'       => vgse_filters_init()->get_raw_filters(),
				'raw_form_data' => array(
					'columns'              => array_map( 'sanitize_text_field', $raw_form_data['columns'] ),
					'formula_data'         => array_map( 'wp_kses_post', $raw_form_data['formula_data'] ),
					'action_name'          => sanitize_text_field( $raw_form_data['action_name'] ),
					'use_slower_execution' => ! empty( $raw_form_data['use_slower_execution'] ),
				),
			);

			$result = $this->bulk_execute_formula( $settings );

			if ( is_wp_error( $result ) ) {
				wp_send_json_error( array( 'message' => $result->get_error_message() ) );
			}

			wp_send_json_success( $result );
		}

		function _get_initial_data_from_cell( $cell_args, $post, $editor ) {
			$cell_key = $cell_args['key_for_formulas'];
			$data     = VGSE()->helpers->get_column_text_value( $cell_key, $post, $cell_args );
			return $data;
		}

		function maybe_replace_file_urls_with_ids( $formula ) {

			$placeholders_regex           = '/\$([a-zA-Z0-9_\-]+)\$/';
			$formula_without_placeholders = str_replace( '$current_value$', '', $formula );
			if ( preg_match( $placeholders_regex, $formula_without_placeholders ) ) {
				return $formula;
			}
			$regExp       = '/=REPLACE\(""(.*)\"",""(.*)""\)/s';
			$temp_formula = $this->sanitize_formula( $formula );
			$matched      = preg_match_all( $regExp, str_replace( '"", ""', '"",""', $temp_formula ), $result );

			if ( $matched ) {
				$first_url  = $result[1][0];
				$second_url = $result[2][0];
				$first_ids  = VGSE()->helpers->maybe_replace_urls_with_file_ids( explode( ',', $first_url ) );
				$formula    = str_replace( $first_url, implode( ',', $first_ids ), $formula );

				$second_ids = VGSE()->helpers->maybe_replace_urls_with_file_ids( explode( ',', $second_url ) );
				$formula    = str_replace( $second_url, implode( ',', $second_ids ), $formula );
			}

			return $formula;
		}

		function _mark_all_items_for_bulk_edit_session( $total, $editor, $base_query, $bulk_edit_id, $post_type ) {
			$meta_table             = $editor->provider->key === 'custom_table' ? false : $editor->provider->get_meta_table_name( $post_type );
			$pages_to_mark_per_page = 2000;
			$total_pages            = ceil( (int) $total / $pages_to_mark_per_page ) + 1;
			$all_ids_to_mark        = '';
			for ( $i = 1; $i < $total_pages; $i++ ) {
				$full_list_query = $editor->provider->get_items(
					wp_parse_args(
						array(
							'posts_per_page' => $pages_to_mark_per_page,
							'fields'         => 'ids',
							'paged'          => $i,
						),
						$base_query
					)
				);
				if ( ! empty( $full_list_query->posts ) ) {
					if ( $meta_table ) {
						$this->execute_formula_as_sql(
							$full_list_query->posts,
							'=REPLACE(""$current_value$"", ""1"")',
							array(
								'key_for_formulas' => $bulk_edit_id,
								'data_type'        => 'post_meta',
							),
							$post_type
						);
					} else {
						if ( $all_ids_to_mark ) {
							$all_ids_to_mark .= ',' . implode( ',', $full_list_query->posts );
						} else {
							$all_ids_to_mark .= implode( ',', $full_list_query->posts );
						}
					}
				}
			}

			if ( $all_ids_to_mark ) {
				update_option( 'vgse_' . $bulk_edit_id, $all_ids_to_mark );
			}
		}

		function _fast_wp_delete_post( $posts, $post_type, $bulk_edit_id ) {
			global $wpdb;
			if ( empty( $bulk_edit_id ) ) {
				return;
			}
			if ( 'attachment' === $post_type ) {
				foreach ( $posts as $post ) {
					wp_delete_attachment( $post->ID, true );
				}
				return;
			}

			$delete_post_ids          = wp_list_pluck( $posts, 'ID' );
			$where_query_placeholders = implode( ', ', array_fill( 0, count( $posts ), '%d' ) );

			// Mark posts with the bulk edit ID for fast access in next queries
			$mark_posts_sql = $wpdb->prepare( "UPDATE $wpdb->posts SET post_content_filtered = %s WHERE ID IN ($where_query_placeholders) ", array_merge( array( $bulk_edit_id ), $delete_post_ids ) );
			$wpdb->query( $mark_posts_sql );

			// Get ID of child posts where post_type IN (same post type or revision)
			if ( is_post_type_hierarchical( $post_type ) ) {
				$get_children_sql = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_type = %s AND post_parent IN (SELECT ID FROM $wpdb->posts WHERE post_content_filtered = %s) ", $post_type, $bulk_edit_id );
				$children         = $wpdb->get_results( $get_children_sql );
				$posts            = array_merge( $posts, $children );

				// Reset variables to include children
				$delete_post_ids          = wp_list_pluck( $posts, 'ID' );
				$where_query_placeholders = implode( ', ', array_fill( 0, count( $posts ), '%d' ) );
			}

			// Include product variations
			if ( class_exists( 'WooCommerce' ) && $post_type === 'product' ) {
				$get_variations_sql = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_type = 'product_variation' AND post_parent IN (SELECT ID FROM $wpdb->posts WHERE post_content_filtered = %s)", $bulk_edit_id );
				$children           = $wpdb->get_results( $get_variations_sql );
				$posts              = array_merge( $posts, $children );

				// Reset variables to include children
				$delete_post_ids          = wp_list_pluck( $posts, 'ID' );
				$where_query_placeholders = implode( ', ', array_fill( 0, count( $posts ), '%d' ) );
			}

			// Mark posts with the bulk edit ID for fast access in next queries
			$mark_posts_sql = $wpdb->prepare( "UPDATE $wpdb->posts SET post_content_filtered = %s WHERE ID IN ($where_query_placeholders) ", array_merge( array( $bulk_edit_id ), $delete_post_ids ) );
			$wpdb->query( $mark_posts_sql );

			// Clear memory
			$children = null;

			if ( empty( $delete_post_ids ) ) {
				return;
			}

			$delete_meta_sql = $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE post_id IN (SELECT ID FROM $wpdb->posts WHERE post_content_filtered = %s) ", $bulk_edit_id );
			$wpdb->query( $delete_meta_sql );

			// Delete taxonomy terms if post type has taxonomies
			if ( get_object_taxonomies( $post_type ) ) {
				$delete_terms_sql = $wpdb->prepare( "DELETE FROM $wpdb->term_relationships WHERE object_id IN (SELECT ID FROM $wpdb->posts WHERE post_content_filtered = %s) ", $bulk_edit_id );
				$wpdb->query( $delete_terms_sql );
			}

			// Delete comments if post type supports comments
			if ( post_type_supports( $post_type, 'comments' ) ) {
				$get_comments_sql = $wpdb->prepare( "SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID IN (SELECT ID FROM $wpdb->posts WHERE post_content_filtered = %s) ", $bulk_edit_id );
				$comment_ids      = $wpdb->get_col( $get_comments_sql );
				if ( ! empty( $comment_ids ) ) {
					$comments_where_query_placeholders = implode( ', ', array_fill( 0, count( $comment_ids ), '%d' ) );

					$delete_comments_meta_sql = $wpdb->prepare( "DELETE FROM $wpdb->commentmeta WHERE comment_id IN ($comments_where_query_placeholders) ", $comment_ids );
					$wpdb->query( $delete_comments_meta_sql );

					$delete_comments_sql = $wpdb->prepare( "DELETE FROM $wpdb->comments WHERE comment_post_ID IN (SELECT ID FROM $wpdb->posts WHERE post_content_filtered = %s) ", $bulk_edit_id );
					$wpdb->query( $delete_comments_sql );

					// Clear memory
					$comment_ids = null;
				}
			}

			// Point all attachments to this post to parent = 0
			$update_attachments_sql = $wpdb->prepare( "UPDATE $wpdb->posts SET post_parent = 0 WHERE post_type = 'attachment' AND post_parent IN ($where_query_placeholders) ", $delete_post_ids );
			$wpdb->query( $update_attachments_sql );

			/**
			 * Fires immediately before a post is deleted from the database.
			 *
			 * @since 1.2.0
			 * @since 5.5.0 Added the `$post` parameter.
			 *
			 * @param int     $postid Post ID.
			 * @param WP_Post $post   Post object.
			 */
			foreach ( $posts as $post ) {
				do_action( 'delete_post', $post->ID, $post );
			}
			// Delete posts
			$delete_posts_sql = $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE post_content_filtered = %s", $bulk_edit_id );
			$wpdb->query( $delete_posts_sql );

			do_action( 'vg_sheet_editor/formulas/fast_post_deleted', $posts, $post_type, $bulk_edit_id );

			/**
			 * Fires immediately after a post is deleted from the database.
			 *
			 * @since 2.2.0
			 * @since 5.5.0 Added the `$post` parameter.
			 *
			 * @param int     $postid Post ID.
			 * @param WP_Post $post   Post object.
			 */
			foreach ( $posts as $post ) {
				do_action( 'deleted_post', $post->ID, $post );
				clean_post_cache( $post );
				wp_clear_scheduled_hook( 'publish_future_post', array( $post->ID ) );

				/**
				 * Fires after a post is deleted, at the conclusion of wp_delete_post().
				 *
				 * @since 3.2.0
				 * @since 5.5.0 Added the `$post` parameter.
				 *
				 * @see wp_delete_post()
				 *
				 * @param int     $postid Post ID.
				 * @param WP_Post $post   Post object.
				 */
				do_action( 'after_delete_post', $post->ID, $post );
			}
		}

		function bulk_execute_formula( $request_data = array() ) {
			global $wpdb;
			$column                     = $request_data['column'];
			$raw_form_data              = $request_data['raw_form_data'];
			$formula                    = wp_unslash( $request_data['formula'] );
			$post_type                  = $request_data['post_type'];
			$page                       = (int) $request_data['page'];
			$per_page                   = ! empty( $request_data['per_page'] ) ? (int) $request_data['per_page'] : VGSE()->get_option( 'be_posts_per_page_save', 8 );
			$bulk_edit_id               = 'wpsebe' . $request_data['wpse_job_id'];
			$editor                     = VGSE()->helpers->get_provider_editor( $post_type );
			VGSE()->current_provider    = $editor->provider;
			$this->current_bulk_edit_id = $bulk_edit_id;

			$meta_table = $editor->provider->key === 'custom_table' ? false : $editor->provider->get_meta_table_name( $post_type );

			if ( ! VGSE()->helpers->user_can_delete_post_type( $post_type ) && $raw_form_data['action_name'] === 'set_value' && in_array( 'delete', $raw_form_data['formula_data'] ) ) {
				return new WP_Error( 'vgse', __( 'You do not have permission to delete posts.', 'vg_sheet_editor' ) );
			}

			$is_fast_post_delete = false;
			if ( post_type_exists( $post_type ) && $raw_form_data['action_name'] === 'set_value' && in_array( 'delete', $raw_form_data['formula_data'] ) && empty( $raw_form_data['use_slower_execution'] ) && empty( VGSE()->options['delete_attached_images_when_post_delete'] ) ) {
				$is_fast_post_delete = true;
				$per_page            = ( ! empty( VGSE()->options['delete_posts_per_page'] ) ) ? (int) VGSE()->options['delete_posts_per_page'] : 500;
			}

			if ( $page > 1 && ! $is_fast_post_delete ) {
				if ( $meta_table ) {
					$request_data['filters'] = array(
						'meta_query' => array(
							array(
								'source'  => 'meta',
								'key'     => $bulk_edit_id,
								'compare' => '=',
								'value'   => '1',
							),
						),
					);
				} else {
					$all_ids                 = implode( ',', array_filter( explode( ',', get_option( 'vgse_' . $bulk_edit_id ) ) ) );
					$request_data['filters'] = array(
						'post__in' => $all_ids,
					);
				}
				if ( vgse_filters_init()->has_filter( 'wc_display_variations', 'yes' ) ) {
					$request_data['filters']['wc_display_variations'] = 'yes';
				}
				if ( vgse_filters_init()->has_filter( 'search_variations', 'yes' ) ) {
					$request_data['filters']['search_variations'] = 'yes';
				}
				$_REQUEST['filters'] = $request_data['filters'];
			}

			// If we're deleting posts completely, always use page 1 because the
			// pagination breaks when the rows are deleted and the real page number becomes wrong
			if ( in_array( $column, array( 'post_status', 'wpse_status' ) ) && $raw_form_data['formula_data'][0] === 'delete' ) {
				$page = 1;
			}
			// If we're changing post types in posts, always use page 1 because the
			// pagination breaks when the rows are deleted and the real page number becomes wrong
			if ( $column === 'post_type' && in_array( $raw_form_data['action_name'], array( 'set_value', 'replace' ) ) ) {
				$page = 1;
			}

			// If we're regenerating slugs, always use the slow execution method
			if ( in_array( $column, array( 'post_name', 'slug' ) ) && $raw_form_data['action_name'] === 'clear_value' ) {
				$raw_form_data['use_slower_execution'] = true;
			}

			$spreadsheet_columns = VGSE()->helpers->get_unfiltered_provider_columns( $post_type );

			if ( in_array( $raw_form_data['action_name'], array( 'add_time', 'reduce_time' ), true ) ) {
				if ( empty( $raw_form_data['formula_data'] ) || empty( $raw_form_data['formula_data'][0] ) || empty( $raw_form_data['formula_data'][1] ) ) {
					return new WP_Error( 'vgse', __( 'Missing number and time unit.', 'vg_sheet_editor' ) );
				}
				$seconds_constant = strtoupper( $raw_form_data['formula_data'][1] ) . '_IN_SECONDS';
				if ( ! defined( $seconds_constant ) ) {
					return new WP_Error( 'vgse', __( 'Invalid time unit.', 'vg_sheet_editor' ) );
				}
			}
			if ( in_array( $raw_form_data['action_name'], array( 'remove_terms' ), true ) ) {
				if ( empty( $raw_form_data['formula_data'] ) || empty( $raw_form_data['formula_data'][0] ) ) {
					return new WP_Error( 'vgse', __( 'Please indicate which terms you want to remove.', 'vg_sheet_editor' ) );
				}
			}

			$email_sent_log_path = WPSE_CSV_API_Obj()->imports_dir . $bulk_edit_id . '-send_email.csv';
			if ( $raw_form_data['action_name'] === 'send_email' && ! WP_Sheet_Editor_Helpers::current_user_can( 'manage_option' ) ) {
				return new WP_Error( 'vgse', __( 'Only administrators are allowed to send emails to users.', 'vg_sheet_editor' ) );
			}
			if ( $raw_form_data['action_name'] === 'send_email' && ( empty( $raw_form_data['formula_data'][0] ) || empty( $raw_form_data['formula_data'][1] ) ) ) {
				return new WP_Error( 'vgse', __( 'Please enter the email subject and email message.', 'vg_sheet_editor' ) );
			}

			if ( empty( $spreadsheet_columns[ $column ] ) ) {
				return new WP_Error( 'vgse', __( 'The column selected is not valid.', 'vg_sheet_editor' ) );
			}

			$column_settings = $spreadsheet_columns[ $column ];
			$column          = $spreadsheet_columns[ $column ]['key_for_formulas'];

			$extra_validation = apply_filters( 'vg_sheet_editor/formulas/early_validation', null, $raw_form_data, $column_settings, $spreadsheet_columns, $post_type, $formula, $editor );
			if ( is_wp_error( $extra_validation ) ) {
				return $extra_validation;
			}

			if ( VGSE()->options['be_disable_post_actions'] ) {
				VGSE()->helpers->remove_all_post_actions( $post_type );
			}

			$get_rows_args = apply_filters(
				'vg_sheet_editor/formulas/search_query/get_rows_args',
				array(
					'nonce'       => $request_data['nonce'],
					'post_type'   => $post_type,
					'filters'     => isset( $request_data['filters'] ) ? $request_data['filters'] : '',
					'wpse_source' => 'formulas',
				)
			);
			$base_query    = VGSE()->helpers->prepare_query_params_for_retrieving_rows( $get_rows_args );

			$base_query['posts_per_page'] = $per_page;
			$base_query['paged']          = $page;

			$can_execute_formula_as_sql = $this->can_execute_formula_as_sql( $formula, $column_settings, $post_type, $spreadsheet_columns, $raw_form_data );
			if ( $can_execute_formula_as_sql ) {
				$base_query['posts_per_page'] = -1;
				$base_query['fields']         = 'ids';
			}

			if ( ! empty( $request_data['custom_wp_query_params'] ) ) {
				$base_query = wp_parse_args( $request_data['custom_wp_query_params'], $base_query );
				unset( $request_data['custom_wp_query_params'] );
			}
			$base_query = apply_filters( 'vg_sheet_editor/formulas/execute/posts_query', $base_query, $request_data );

			$query = $editor->provider->get_items( $base_query );

			$total = $query->found_posts;

			// If remove_duplicates is active and posts were found
			if ( ! empty( $query->posts ) && in_array( $raw_form_data['action_name'], array( 'remove_duplicates', 'remove_duplicates_title_content' ), true ) ) {
				global $wpdb;

				if ( ! isset( $spreadsheet_columns['post_status'] ) ) {
					return new WP_Error( 'vgse', __( 'Error. The status column must be enabled in the spreadsheet before we can remove duplicates.', 'vg_sheet_editor' ) );
				}

				$duplicate_ids_to_delete = apply_filters( 'vg_sheet_editor/formulas/execute/get_duplicate_ids', null, $column, $post_type, $raw_form_data, $column_settings, $query );

				if ( is_null( $duplicate_ids_to_delete ) ) {

					$main_sql = str_replace( array( "SQL_CALC_FOUND_ROWS  $wpdb->posts.ID", 'SQL_CALC_FOUND_ROWS' ), array( "$wpdb->posts.*", '' ), substr( $query->request, 0, strripos( $query->request, 'ORDER BY' ) ) );

					$remove_duplicates_meta_keys = array_filter( array_map( 'trim', explode( ',', VGSE()->get_option( 'allow_formula_remove_duplicates_meta_keys', '' ) ) ) );
					if ( $raw_form_data['action_name'] === 'remove_duplicates_title_content' ) {
						// We don't select the sample value in the query ('value') because it might be very large here
						$get_items_sql = 'SELECT  count(p.' . esc_sql( $column ) . ") 'count', GROUP_CONCAT(p.ID SEPARATOR ',') as post_ids FROM ($main_sql) p WHERE p.post_title <> '' AND p.post_content <> '' GROUP BY CONCAT(p.post_title, p.post_content)  having count(*) >= 2";
					} elseif ( ! empty( $remove_duplicates_meta_keys ) && in_array( $column, $remove_duplicates_meta_keys, true ) ) {
						$main_sql      = str_replace( "SELECT $wpdb->posts.*", "SELECT $wpdb->posts.ID", $main_sql );
						$get_items_sql = $wpdb->prepare( "SELECT meta_value 'value', count(meta_value) 'count', GROUP_CONCAT(post_id SEPARATOR ',') as post_ids  FROM $wpdb->postmeta pm WHERE post_id IN ($main_sql) AND meta_key = %s AND meta_value <> '' GROUP BY meta_value having count(*) >= 2", $column );
					} else {
						$get_items_sql = 'SELECT p.' . esc_sql( $column ) . " 'value', count(p." . esc_sql( $column ) . ") 'count', GROUP_CONCAT(p.ID SEPARATOR ',') as post_ids FROM ($main_sql) p WHERE p." . esc_sql( $column ) . " <> '' GROUP BY p." . esc_sql( $column ) . ' having count(*) >= 2';
					}

					$get_items_sql = apply_filters( 'vg_sheet_editor/formulas/execute/get_duplicate_items_sql', $get_items_sql, $column, $post_type, $raw_form_data, $column_settings, $query );

					$items_with_duplicates = $wpdb->get_results( $get_items_sql, ARRAY_A );
					// Get all items with duplicates, we use the main sql query and wrap it to add our own conditions
					// We iterate over each post containing duplicates, and we fetch all the duplicates.
					// Note. We use limit = count-1 to leave one item only
					$duplicate_ids_to_delete = array();
					foreach ( $items_with_duplicates as $item ) {
						$duplicate_value_post_ids = array_map( 'intval', explode( ',', $item['post_ids'] ) );
						sort( $duplicate_value_post_ids );
						$to_preserve = ( ! empty( $raw_form_data['formula_data'] ) && $raw_form_data['formula_data'][0] === 'delete_latest' ) ? array_shift( $duplicate_value_post_ids ) : array_pop( $duplicate_value_post_ids );

						$duplicate_ids_to_delete = array_merge( $duplicate_ids_to_delete, $duplicate_value_post_ids );

						do_action( 'vg_sheet_editor/formulas/duplicates_to_remove', $duplicate_value_post_ids, $to_preserve, $post_type, $column, $raw_form_data );
					}
				}

				// Get all items with duplicates, we use the main sql query and wrap it to add our own conditions
				// We iterate over each post containing duplicates, and we fetch all the duplicates.
				// Note. We use limit = count-1 to leave one item only
				$query        = (object) array();
				$query->posts = (array) $duplicate_ids_to_delete;

				$total              = count( $query->posts );
				$query->found_posts = $total;

				// We use a sql formula to update all items at once
				$formula                    = '=REPLACE(""$current_value$"",""trash"")';
				$column                     = 'post_status';
				$column_settings            = $spreadsheet_columns[ $column ];
				$base_query['fields']       = 'ids';
				$can_execute_formula_as_sql = true;
			}

			if ( ! empty( $query->posts ) ) {

				if ( strpos( $formula, '$sequential_number$' ) !== false && $page === 1 ) {
					set_transient( 'vgse_sequence' . $bulk_edit_id, 1, WEEK_IN_SECONDS );
				}
				$count = 0;
				do_action( 'vg_sheet_editor/formulas/execute_formula/before_execution', $column, $formula, $post_type, $spreadsheet_columns );

				// If file cells, convert URLs to file IDs before replacement
				if ( in_array( $column_settings['type'], array( 'boton_gallery', 'boton_gallery_multiple' ) ) && strpos( $formula, '=REPLACE(' ) !== false && strpos( $formula, ',' ) !== false ) {
					$formula = $this->maybe_replace_file_urls_with_ids( $formula );
				}

				$editions_count = apply_filters( 'vg_sheet_editor/formulas/execute_formula', null, $raw_form_data, $query->posts, $column_settings );
				$processed_ids  = array();
				$updated_ids    = array();

				if ( is_null( $editions_count ) ) {
					if ( $can_execute_formula_as_sql ) {
						$sql_updated_count = $this->execute_formula_as_sql( $query->posts, $formula, $column_settings, $post_type );
						$sql_updated       = ( ! empty( $sql_updated ) ) ? $sql_updated + $sql_updated_count : $sql_updated_count;
						$updated_ids       = $query->posts;
						$processed_ids     = $query->posts;
						$updated_count     = $sql_updated;
						$editions_count    = $sql_updated;
						VGSE()->helpers->increase_counter( 'editions', $updated_count );
						do_action( 'vg_sheet_editor/formulas/execute_formula/after_sql_execution', $column, $formula, $post_type, $spreadsheet_columns, $query->posts );
					} else {
						// Mark the items in batches of 2000 to prevent memory leaks
						if ( $page === 1 && ! $is_fast_post_delete ) {
							$this->_mark_all_items_for_bulk_edit_session( $total, $editor, $base_query, $bulk_edit_id, $post_type );
						}
						$editions_count = 0;

						if ( $is_fast_post_delete ) {
							$this->_fast_wp_delete_post( $query->posts, $post_type, $bulk_edit_id );
							$editions_count = count( $query->posts );
							$processed_ids  = wp_list_pluck( $query->posts, 'ID' );
							$updated_ids    = $processed_ids;
						} else {

							// Loop through all the posts
							foreach ( $query->posts as $post ) {

								$GLOBALS['post'] = & $post;

								//                      Disabled because WC caches the $product object when the postdata is setup,
								//                      which causes issues when we update post type.
								//                      if (isset($post->post_title)) {
								//                          setup_postdata($post);
								//                      }

								$post_id = $post->ID;

								do_action( 'vg_sheet_editor/formulas/execute_formula/before_execution_on_field', $post_id, $column_settings, $formula, $post_type, $spreadsheet_columns );

								if ( $results = apply_filters( 'vg_sheet_editor/formulas/execute_formula/custom_formula_handler_executed', false, $post_id, $column_settings, $formula, $post_type, $spreadsheet_columns, $raw_form_data ) ) {
									do_action( 'vg_sheet_editor/formulas/execute_formula/after_execution_on_field', $post->ID, $results['initial_data'], $results['modified_data'], $column, $formula, $post_type, $column_settings, $spreadsheet_columns );

									if ( $results['initial_data'] !== $results['modified_data'] ) {
										$editions_count++;
										$updated_ids[]   = $post->ID;
										$processed_ids[] = $post->ID;
									}

									$count++;
									continue;
								}

								// Send email
								if ( $raw_form_data['action_name'] === 'send_email' ) {
									$email_to = $this->_get_initial_data_from_cell( $column_settings, $post, $editor );
									if ( ! empty( $email_to ) ) {
										// If we're sending one per user, skip if the email_to is found in the file
										if ( $raw_form_data['formula_data'][3] === 'per_user' ) {
											$emails_sent = file( $email_sent_log_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
											if ( is_array( $emails_sent ) && in_array( $email_to, $emails_sent, true ) ) {
												$count++;
												continue;
											}
											// Save the email_to in the file
											file_put_contents( $email_sent_log_path, $email_to, FILE_APPEND );
										}

										$email_subject = $this->_replace_placeholders( $raw_form_data['formula_data'][0], $email_to, $post->ID, $post_type, $column_settings );
										$email_body    = $this->_replace_placeholders( $raw_form_data['formula_data'][1], $email_to, $post->ID, $post_type, $column_settings );
										$reply_to      = sanitize_email( $raw_form_data['formula_data'][2] );
										$headers       = array(
											'Content-Type: text/html; charset=UTF-8',
										);
										if ( ! empty( $reply_to ) ) {
											$headers[] = 'Reply-To: ' . $reply_to . ' <' . $reply_to . '>';
										}
										if ( strpos( $email_body, 'aligncenter' ) !== false ) {
											$email_body = str_replace( '<img ', '<img style="margin: 0 auto;display: block;" ', $email_body );
										}
										$email_sent = wp_mail( $email_to, $email_subject, $email_body, $headers );
										if ( $email_sent ) {
											$editions_count++;
											$processed_ids[] = $post->ID;
										}
									}

									$count++;
									continue;
								}

								// loop through every column in the spreadsheet
								$cell_key     = $column;
								$cell_args    = $column_settings;
								$data         = $this->_get_initial_data_from_cell( $cell_args, $post, $editor );
								$initial_data = $data;

								if ( $raw_form_data['action_name'] === 'remove_everything_before' ) {
									if ( ! empty( $data ) && strpos( $data, $raw_form_data['formula_data'][0] ) !== false ) {
										$data_parts = explode( $raw_form_data['formula_data'][0], $data );
										$data       = $raw_form_data['formula_data'][1] === 'yes' ? end( $data_parts ) : $raw_form_data['formula_data'][0] . end( $data_parts );
									}
								} elseif ( $raw_form_data['action_name'] === 'remove_everything_after' ) {
									if ( ! empty( $data ) && strpos( $data, $raw_form_data['formula_data'][0] ) !== false ) {
										$data_parts = explode( $raw_form_data['formula_data'][0], $data );
										$data       = $raw_form_data['formula_data'][1] === 'yes' ? current( $data_parts ) : current( $data_parts ) . $raw_form_data['formula_data'][0];
									}
								} elseif ( in_array( $raw_form_data['action_name'], array( 'add_time', 'reduce_time' ), true ) ) {
									$timestamp        = ( empty( $data ) ) ? current_time( 'timestamp' ) : strtotime( $data );
									$seconds_constant = strtoupper( $raw_form_data['formula_data'][1] ) . '_IN_SECONDS';
									$seconds_for_edit = constant( $seconds_constant ) * (int) $raw_form_data['formula_data'][0];
									$new_timestamp    = $raw_form_data['action_name'] === 'add_time' ? $timestamp + $seconds_for_edit : $timestamp - $seconds_for_edit;
									$data             = date( 'Y-m-d H:i:s', $new_timestamp );
								} elseif ( in_array( $raw_form_data['action_name'], array( 'remove_terms' ), true ) ) {
									if ( ! empty( $initial_data ) ) {
										$term_separator      = VGSE()->helpers->get_term_separator();
										$raw_terms_to_remove = preg_replace( '/ ?> ?/', '>', str_replace( $term_separator, PHP_EOL, html_entity_decode( $raw_form_data['formula_data'][0] ) ) );
										$terms_to_remove     = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n|\t/', $raw_terms_to_remove ) ) );
										$existing_terms      = array_map( 'trim', explode( $term_separator, preg_replace( '/ ?> ?/', '>', $initial_data ) ) );
										$terms_to_keep       = array_diff( $existing_terms, $terms_to_remove );
										$data                = implode( $term_separator, $terms_to_keep );
									}
								} else {
									$data = $this->apply_formula_to_data( $formula, $data, $post->ID, $cell_args, $post_type );
								}
								if ( is_wp_error( $data ) ) {
									return $data;
								}

								// If file cells, convert URLs to file IDs before saving
								// We do this a second time in case the image URLs reference other columns and
								// the first time the placeholder don't have values
								if ( in_array( $column_settings['type'], array( 'boton_gallery', 'boton_gallery_multiple' ) ) && strpos( $formula, '=REPLACE(' ) !== false ) {
									$data = implode( ',', VGSE()->helpers->maybe_replace_urls_with_file_ids( explode( ',', $data ) ) );
								}

								if ( $initial_data !== $data ) {

									try {
										// Same filter is available on save_rows
										$item = apply_filters(
											'vg_sheet_editor/save_rows/row_data_before_save',
											array(
												'ID'      => $post->ID,
												$cell_key => $data,
											),
											$post_id,
											$post_type,
											$spreadsheet_columns,
											$request_data
										);

										if ( is_wp_error( $item ) ) {
											return $item;
										}
										if ( empty( $item ) ) {
											$processed_ids[] = $post->ID;
											$editions_count++;
											continue;
										}

										do_action(
											'vg_sheet_editor/save_rows/before_saving_cell',
											array(
												'ID'      => $post->ID,
												$cell_key => $data,
											),
											$post_type,
											$cell_args,
											$cell_key,
											$spreadsheet_columns,
											$post_id
										);

										$data_to_save = $this->_prepare_data_for_saving( $data, $cell_args );

										// If the value should be prepared using a callback before we save
										if ( ! empty( $cell_args['prepare_value_for_database'] ) ) {
											$data_to_save = call_user_func( $cell_args['prepare_value_for_database'], $post->ID, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns );
										}

										// Use column callback to save the cell value
										if ( ! empty( $cell_args['save_value_callback'] ) && is_callable( $cell_args['save_value_callback'] ) ) {
											call_user_func( $cell_args['save_value_callback'], $post->ID, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns );
											$updated_ids[]   = $post->ID;
											$processed_ids[] = $post->ID;
											$editions_count++;
										} else {

											$updated_ids[]   = $post->ID;
											$processed_ids[] = $post->ID;
											$editions_count++;

											if ( $cell_args['data_type'] === 'post_data' ) {

												// If the modified data is different, we save it
												$update = array();

												$final_key = $cell_key;
												if ( VGSE()->helpers->get_current_provider()->is_post_type ) {
													if ( ! in_array( $cell_key, array( 'comment_status', 'menu_order', 'comment_count', 'ID' ) ) && strpos( $cell_key, 'post_' ) === false ) {
														$final_key = 'post_' . $cell_key;
													}
												}
												$update[ $final_key ] = $data_to_save;

												if ( empty( $update['ID'] ) ) {
													$update['ID'] = $post->ID;
												}
												$post_id = $editor->provider->update_item_data( $update, true );

											}
											if ( $cell_args['data_type'] === 'meta_data' || $cell_args['data_type'] === 'post_meta' ) {
												$data   = $data_to_save;
												$update = $editor->provider->update_item_meta( $post->ID, $cell_key, $data );
											}
											if ( $cell_args['data_type'] === 'post_terms' ) {
												$update = $editor->provider->set_object_terms( $post->ID, $data_to_save, $cell_key );
											}
										}
									} catch ( Exception $e ) {
										$exception_message = $e->getMessage();
										do_action( 'vg_sheet_editor/formulas/fatal_error_handler', $e, $post->ID, $initial_data, $data, $column, $formula, $post_type, $cell_args, $spreadsheet_columns );
										return new WP_Error( 'vgse', sprintf( __( 'Error: %s', 'vg_sheet_editor' ), $exception_message ) );
									}
								} else {
									// if the data is the same after running the formula, we don't save it.
									$post_id = true;
								}

								$modified_data = $data;
								do_action( 'vg_sheet_editor/formulas/execute_formula/after_execution_on_field', $post->ID, $initial_data, $modified_data, $column, $formula, $post_type, $cell_args, $spreadsheet_columns );
								$count++;

								if ( ! empty( VGSE()->options['run_save_post_action_always'] ) && VGSE()->helpers->get_current_provider()->is_post_type ) {
									do_action( 'save_post', $post->ID, get_post( $post->ID ), true );
								}
							}
						}
						VGSE()->helpers->increase_counter( 'editions', $editions_count );
						$updated_count = count( $processed_ids );
					}

					if ( method_exists( VGSE()->helpers->get_current_provider(), 'update_modified_date' ) && $column !== 'post_modified' ) {
						VGSE()->helpers->get_current_provider()->update_modified_date( $updated_ids );
					}
				} else {
					$updated_count = $editions_count;

					// Mark the items in batches of 2000 to prevent memory leaks
					if ( $page === 1 && ! $is_fast_post_delete ) {
						$this->_mark_all_items_for_bulk_edit_session( $total, $editor, $base_query, $bulk_edit_id, $post_type );
					}
				}

				do_action( 'vg_sheet_editor/formulas/execute_formula/after_execution', $column, $formula, $post_type, $spreadsheet_columns, $query->posts );
			} else {

				if ( $page === 1 ) {

					return new WP_Error( 'vgse', __( 'Bulk edit not executed. No items found matching the criteria.', 'vg_sheet_editor' ) );
				} else {

					if ( strpos( $formula, '$sequential_number$' ) !== false ) {
						delete_transient( 'vgse_sequence' . $bulk_edit_id );
					}
					// Remove list flag
					if ( $meta_table ) {
						$editor->provider->delete_meta_key( $bulk_edit_id, $post_type );
					} else {
						delete_option( 'vgse_' . $bulk_edit_id );
					}
					if ( file_exists( $email_sent_log_path ) ) {
						unlink( $email_sent_log_path );
					}
					return array(
						'force_complete' => true,
						'message'        => __( '<p>Complete</p>.', 'vg_sheet_editor' ),
					);
				}
			}
			wp_reset_postdata();
			wp_reset_query();

			// Send final message indicating the number of posts updated.
			$processed = ( ! $can_execute_formula_as_sql && $total > ( $per_page * $page ) ) ? $per_page * $page : $total;
			VGSE()->helpers->increase_counter( 'processed', $processed );
			$total_updated = $updated_count;

			// If the post has orphan meta data, it might update more rows than the posts total
			// so make sure the total updated is not higher than the total of posts
			if ( $total_updated > $total ) {
				$total_updated = $total;
			}
			$message = sprintf( __( '<p class="success-message" data-column-key="%s"><b>Editing the field: {column_label}</b>. Items to process: {total}, Progress: {progress_percentage}%%, We have updated {edited} items.</p>', 'vg_sheet_editor' ), esc_attr( $request_data['column'] ), $processed, $total, $total_updated );

			// Remove list flag
			if ( (int) $processed === (int) $total ) {

				if ( strpos( $formula, '$sequential_number$' ) !== false ) {
					delete_transient( 'vgse_sequence' . $bulk_edit_id );
				}
				if ( $meta_table ) {
					$editor->provider->delete_meta_key( $bulk_edit_id, $post_type );
				} else {
					delete_option( 'vgse_' . $bulk_edit_id );
				}
				if ( file_exists( $email_sent_log_path ) ) {
					unlink( $email_sent_log_path );
				}
			}

			return array(
				'message'          => $message,
				'total'            => (int) $total,
				'processed'        => (int) $processed,
				// Standarize output with other endpoints
				'processed_rows'   => (int) $processed,
				'updated'          => $total_updated,
				'processed_posts'  => ( ! empty( $base_query['fields'] ) && $base_query['fields'] === 'ids' ) ? $query->posts : wp_list_pluck( $query->posts, 'ID' ),
				'updated_posts'    => $updated_ids,
				'force_complete'   => ( $can_execute_formula_as_sql ) ? true : false,
				'execution_method' => ( $can_execute_formula_as_sql ) ? 'sql_formula' : 'php_formula',
			);
		}

		function _prepare_data_for_saving( $data, $cell_args ) {
			if ( is_wp_error( $data ) ) {
				return $data;
			}

			$out = $data;

			$cell_key = $cell_args['key_for_formulas'];

			if ( $cell_args['data_type'] === 'post_data' ) {
				if ( $cell_key !== 'post_content' ) {
					$out = VGSE()->data_helpers->set_post( $cell_key, $data );
				}
				if ( $cell_key === 'post_title' ) {
					$out = wp_strip_all_tags( $out );
				}
			}
			if ( $cell_args['data_type'] === 'post_terms' ) {
				$out = VGSE()->data_helpers->prepare_post_terms_for_saving( $data, $cell_key );
			}

			return $out;
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if ( ! self::$instance ) {
				self::$instance = new WP_Sheet_Editor_Formulas();
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

	add_action( 'vg_sheet_editor/initialized', 'vgse_formulas_init' );

	function vgse_formulas_init() {
		return WP_Sheet_Editor_Formulas::get_instance();
	}

	require 'inc/testing.php';
}
