<?php defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPSE_WPML_Term' ) ) {

	class WPSE_WPML_Term {

		private static $instance = false;

		private function __construct() {

		}

		function init() {
			add_action( 'vg_sheet_editor/terms/taxonomy_edited', array( $this, 'after_taxonomy_edited' ), 10, 4 );
			add_action( 'vg_sheet_editor/editor/register_columns', array( $this, 'register_columns' ) );
			// @todo probar
			//          add_action('vg_sheet_editor/save_rows/after_saving_post', array($this, 'sync_translation_fields'), 10, 4);
			//          add_action('vg_sheet_editor/formulas/execute_formula/after_execution_on_field', array($this, 'sync_translation_fields_after_formula'), 10, 6);
			add_action( 'vg_sheet_editor/filters/after_advanced_fields_section', array( $this, 'add_wpml_language_search' ) );
			add_filter( 'terms_clauses', array( $this, 'search_by_wpml_translation' ), 10, 3 );
		}

		public function search_by_wpml_translation( $pieces, $taxonomies, $args ) {
			global $sitepress, $wpdb;

			// Check if our custom argument, 'wpse_term_parents' is set, if not, bail
			if ( empty( $args['wpse_original_filters'] ) || empty( $args['wpse_original_filters']['wpml_translations_missing'] ) ) {
				return $pieces;
			}
			if ( ! WP_Sheet_Editor_WPML_Obj()->is_the_default_language() ) {
				return $pieces;
			}

			$missing_languages = array_unique( $args['wpse_original_filters']['wpml_translations_missing'] );
			$wpml_languages    = wp_list_pluck( $sitepress->get_active_languages(), 'display_name', 'code' );

			// Sanitize. We remove any value received not found in the active wpml languages,
			// and any value that doesn't have 2 letters only.
			foreach ( $missing_languages as $index => $missing_language ) {
				if ( ! isset( $wpml_languages[ $missing_language ] ) || ! preg_match( '/^[a-z]{2}$/', $missing_language ) ) {
					unset( $missing_languages[ $index ] );
				}
			}

			$sql = " AND icl_t.trid IN (
				SELECT trid
				FROM {$wpdb->prefix}icl_translations translations
				WHERE NOT EXISTS (
				SELECT inner_translations.trid
				FROM {$wpdb->prefix}icl_translations inner_translations
				WHERE inner_translations.trid = translations.trid
				AND inner_translations.language_code IN ('" . implode( "','", $missing_languages ) . "') ) ) ";

			$pieces['where'] .= $sql;

			return $pieces;
		}

		function add_wpml_language_search( $spreadsheet_key ) {
			global $sitepress;
			if ( VGSE()->helpers->get_current_provider()->key !== 'term' ) {
				return;
			}
			if ( ! WP_Sheet_Editor_WPML_Obj()->is_the_default_language() ) {
				return;
			}

			if ( ! is_taxonomy_translated( $spreadsheet_key ) ) {
				return;
			}
			$wpml_languages = wp_list_pluck( $sitepress->get_active_languages(), 'display_name', 'code' );
			?>
						<li class="wpml-languages-without-translations">
							<label><?php echo __( 'WPML - Missing translations in these languages', 'vg_sheet_editor' ); ?>  <a href="#" data-wpse-tooltip="right" aria-label="<?php esc_attr_e( 'For example, select "Spanish" and "German" here and we\'ll find terms that don\'t have spanish translations or german translations.', 'vg_sheet_editor' ); ?>">( ? )</a></label>
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


		function sync_translation_fields_after_formula( $term_id, $initial_data, $modified_data, $column, $formula, $taxonomy_key ) {

			$this->sync_translation_fields( $term_id, null, null, $taxonomy_key );
		}

		function sync_translation_fields( $term_id, $item, $data, $taxonomy_key ) {
			global $sitepress;
			if ( ! taxonomy_exists( $taxonomy_key ) ) {
				return;
			}
			$term_actions = $sitepress->get_term_actions_helper();
			$term         = get_term_by( 'term_id', $term_id, $taxonomy_key );
			$term_actions->sync_term_meta( $term->term_id, $term->term_taxonomy_id );
		}

		private function _duplicate_term( $term_id, $taxonomy, $lang ) {
			global $wpdb, $sitepress;

			$term = get_term( $term_id, $taxonomy );

			if ( is_wp_error( $term ) ) {
				return $term;
			}

			if ( $term->parent ) {

				$parent_term          = get_term( $term->parent, $taxonomy );
				$term_parent_trid     = $sitepress->get_element_trid( $parent_term->term_taxonomy_id, 'tax_' . $taxonomy );
				$translated_parent_id = $wpdb->get_var( $wpdb->prepare( "SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE trid = %d AND language_code = %s LIMIT 1", $term_parent_trid, $lang ) );
			} else {
				$translated_parent_id = 0;
			}
			$new_term = wp_insert_term(
				$term->name . ' (Copy)',
				$term->taxonomy,
				array(
					'description' => $term->description,
					'parent'      => $translated_parent_id,
				)
			);

			if ( is_wp_error( $new_term ) ) {
				return $new_term;
			}

			$sql = $wpdb->prepare( sprintf( 'INSERT INTO %s (`term_id`, `meta_key`, `meta_value`) SELECT %%d, `meta_key`, `meta_value`  FROM %s WHERE `term_id` = %%d', $wpdb->termmeta, $wpdb->termmeta ), $new_term['term_id'], $term_id );
			$wpdb->query( $sql );

			return get_term( $new_term['term_id'], $taxonomy );
		}

		function duplicate_to_language( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			global $wpdb, $sitepress;

			$term         = get_term( $post_id, $post_type );
			$new_langs                   = array_filter( array_map( 'trim', explode( ',', strtolower( $data_to_save ) ) ) );
			$existing_languages_for_term = $wpdb->get_col( $wpdb->prepare( "SELECT language_code FROM {$wpdb->prefix}icl_translations WHERE trid IN (SELECT trid FROM {$wpdb->prefix}icl_translations WHERE element_id = %d AND element_type = %s)", $term->term_taxonomy_id, 'tax_' . $post_type ) );
			$new_langs                   = array_diff( $new_langs, $existing_languages_for_term );
			if ( empty( $new_langs ) ) {
				return;
			}
			$term_name    = VGSE()->helpers->get_current_provider()->get_item_data( $post_id, 'name' );
			$element_type = 'tax_' . esc_sql( $post_type );
			$term         = get_term( $post_id, $post_type );
			$trid         = $sitepress->get_element_trid( $term->term_taxonomy_id, $element_type );
			foreach ( $new_langs as $lang ) {
				$sitepress->switch_lang( $lang );
				$new_term = $this->_duplicate_term( $post_id, $post_type, $lang );
				if ( ! $new_term ) {
					continue;
				}
				$sitepress->set_element_language_details( (int) $new_term->term_taxonomy_id, $element_type, $trid, $lang, $sitepress->get_default_language() );
			}
			$sitepress->switch_lang( null );
		}

		/**
		 * Register spreadsheet columns
		 */
		function register_columns( $editor ) {
			global $sitepress;
			if ( $editor->provider->key === 'user' ) {
				return;
			}
			$post_types = $editor->args['enabled_post_types'];
			foreach ( $post_types as $post_type ) {
				// Don't register the columns if this is not a taxonomy, or if this is a taxonomy but there's also a post type with same key
				if ( ! taxonomy_exists( $post_type ) || post_type_exists( $post_type ) ) {
					continue;
				}
				if ( ! WP_Sheet_Editor_WPML_Obj()->is_the_default_language() ) {
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
						)
					);
				} else {

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
						),
						'get_value_callback'    => array( $this, 'get_language_for_cell' ),
						'save_value_callback'   => array( $this, 'save_language_for_cell' ),
						'comment'               => ( WP_Sheet_Editor_WPML_Obj()->is_the_default_language() ) ? null : array( 'value' => __( 'You can change the language of this post. If the translation for the new language exists, this change will not be applied.', 'vg_sheet_editor' ) ),
					)
				);
			}
		}

		function get_language_for_cell( $post, $cell_key, $cell_args ) {
			global $wpdb;

			$term = get_term_by( 'term_id', $post->ID, $post->post_type );
			return $wpdb->get_var( $wpdb->prepare( 'SELECT language_code FROM ' . $wpdb->prefix . 'icl_translations WHERE element_type = %s AND element_id = %d', 'tax_' . $post->post_type, $term->term_taxonomy_id ) );
		}

		function save_language_for_cell( $post_id, $cell_key, $data_to_save, $post_type, $cell_args = array(), $spreadsheet_columns = array() ) {
			global $wpdb, $sitepress;

			$new_language = strtolower( $data_to_save );
			if ( ! icl_is_language_active( $data_to_save ) ) {
				return;
			}

			$term = get_term_by( 'term_id', $post_id, $post_type );
			$translation_for_new_language_exists = (int) $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'icl_translations WHERE language_code = %s AND element_type = %s AND element_id = %d ', $new_language, 'tax_' . $post_type, $term->term_taxonomy_id ) );
			if ( $translation_for_new_language_exists ) {
				return;
			}

			$args = array(
				'language_code'        => $new_language,
				'source_language_code' => ( $new_language === $sitepress->get_default_language() ) ? null : $sitepress->get_default_language(),
			);

			$wpdb->update(
				$wpdb->prefix . 'icl_translations',
				$args,
				array(
					'element_type' => 'tax_' . esc_sql( $post_type ),
					'element_id'   => (int) $term->term_taxonomy_id,
				)
			);
		}

		function get_translation_of_cell( $post, $cell_key, $cell_args ) {
			global $sitepress, $wpdb;
			$main_language = $sitepress->get_default_language();
			$term          = get_term_by( 'term_id', $post->ID, $post->post_type );
			$trid          = $wpdb->get_var( $wpdb->prepare( 'SELECT trid FROM ' . $wpdb->prefix . 'icl_translations WHERE element_id = %d AND element_type = %s', $term->term_taxonomy_id, 'tax_' . $post->post_type ) );
			if ( ! $trid ) {
				return '';
			}
			$main_term_taxonomy_id = $wpdb->get_var( $wpdb->prepare( 'SELECT element_id FROM ' . $wpdb->prefix . 'icl_translations WHERE trid = %d AND element_type = %s AND language_code = %s', $trid, 'tax_' . $post->post_type, $main_language ) );
			$value                 = '';

			if ( ! $main_term_taxonomy_id || $main_term_taxonomy_id === $post->ID ) {
				return $value;
			}

			$current_language = $sitepress->get_current_language();
			if ( $main_language !== $current_language ) {
				$sitepress->switch_lang( $main_language );
			}

			$main_term = get_term_by( 'term_taxonomy_id', $main_term_taxonomy_id, $post->post_type );
			if ( ! $main_term ) {
				return '';
			}
			$value = html_entity_decode( VGSE()->helpers->get_current_provider()->get_item_data( $main_term->term_id, 'name' ) );

			if ( $main_language !== $current_language ) {
				$sitepress->switch_lang( $current_language );
			}
			return $value;
		}

		function get_term_id_from_name( $term_name, $taxonomy, $use_main_language = false ) {
			global $sitepress;
			$term_id          = null;
			$main_language    = $sitepress->get_default_language();
			$current_language = $sitepress->get_current_language();
			if ( $use_main_language && $main_language !== $current_language ) {
				$sitepress->switch_lang( $main_language );
			}
			// Try to find the parent by slug, if not found, find by hierarchical name
			$term_query = new WP_Term_Query();
			$terms      = $term_query->query(
				array(
					'taxonomy'               => $taxonomy,
					'fields'                 => 'ids',
					'number'                 => 1,
					'slug'                   => $term_name,
					'hide_empty'             => false,
					'update_term_meta_cache' => false,
				)
			);

			if ( empty( $terms ) ) {
				$hierarchy_terms     = array_map( 'trim', explode( '>', str_replace( ':', '>', html_entity_decode( $term_name ) ) ) );
				$sanitized_hierarchy = implode( '>', $hierarchy_terms );
				$ids                 = VGSE()->data_helpers->parse_terms_string_for_saving( $sanitized_hierarchy, $taxonomy, null, false );
				$terms               = array( end( $ids['term_ids'] ) );
			}

			if ( ! empty( $terms ) ) {
				$term_id = (int) current( $terms );
			}

			if ( $use_main_language && $main_language !== $current_language ) {
				$sitepress->switch_lang( $current_language );
			}

			return $term_id;
		}

		function update_translation_of_cell( $term_id, $cell_key, $data_to_save, $taxonomy, $cell_args = array(), $spreadsheet_columns = array() ) {
			global $wpdb, $sitepress;
			$data_to_save = html_entity_decode( trim( $data_to_save ) );
			$term         = get_term_by( 'term_id', $term_id, $taxonomy );
			if ( empty( $data_to_save ) ) {
				$wpdb->update(
					$wpdb->prefix . 'icl_translations',
					array(
						'source_language_code' => null,
						'language_code'        => $sitepress->get_current_language(),
					),
					array(
						'element_id'   => $term->term_taxonomy_id,
						'element_type' => 'tax_' . esc_sql( $taxonomy ),
					),
					array( '%s' ),
					array( '%d' )
				);
				return;
			}

			$main_id = $this->get_term_id_from_name( $data_to_save, $taxonomy, true );
			if ( ! $main_id ) {
				return;
			}
			$main_term    = get_term_by( 'term_id', $main_id, $taxonomy );
			$element_type = 'tax_' . esc_sql( $taxonomy );
			$trid         = $sitepress->get_element_trid( $main_term->term_taxonomy_id, $element_type );

			$sitepress->set_element_language_details( (int) $term->term_taxonomy_id, $element_type, $trid, $sitepress->get_current_language(), $sitepress->get_default_language() );
		}

		function after_taxonomy_edited( $term_id, $old_taxonomy, $new_taxonomy, $term ) {
			global $wpdb;
			$wpdb->update(
				$wpdb->prefix . 'icl_translations',
				array(
					'element_type' => 'tax_' . $new_taxonomy,
				),
				array(
					'element_id'   => $term['term_taxonomy_id'],
					'element_type' => 'tax_' . $old_taxonomy,
				),
				array( '%s' ),
				array( '%d', '%s' )
			);
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if ( null == self::$instance ) {
				self::$instance = new WPSE_WPML_Term();
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

if ( ! function_exists( 'WPSE_WPML_Term_Obj' ) ) {

	function WPSE_WPML_Term_Obj() {
		return WPSE_WPML_Term::get_instance();
	}
}
WPSE_WPML_Term_Obj();
