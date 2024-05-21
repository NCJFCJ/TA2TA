<?php defined( 'ABSPATH' ) || exit;
if ( ! class_exists( 'WP_Sheet_Editor_YOAST_SEO' ) ) {

	class WP_Sheet_Editor_YOAST_SEO {

		private static $instance = false;
		public $yoast_options    = array();

		private function __construct() {

		}

		function notify_wrong_core_version() {
			$plugin_data = get_plugin_data( __FILE__, false, false );
			?>
			<div class="notice notice-error">
				<p><?php _e( 'Please update the WP Sheet Editor plugin and all its extensions to the latest version. The features of the plugin "' . $plugin_data['Name'] . '" will be disabled temporarily because it is the newest version and it conflicts with old versions of other WP Sheet Editor plugins. The features will be enabled automatically after you install the updates.', 'vg_sheet_editor' ); ?></p>
			</div>
			<?php
		}

		function init() {

			if ( version_compare( VGSE()->version, '2.0.0' ) < 0 ) {
				add_action( 'admin_notices', array( $this, 'notify_wrong_core_version' ) );
				return;
			}
			// exit if yoast seo plugin is not active
			if ( ! $this->is_yoast_seo_plugin_active() ) {
				return;
			}

			add_action( 'vg_sheet_editor/editor/register_columns', array( $this, 'register_columns' ) );
			add_action( 'vg_sheet_editor/load_rows/output', array( $this, 'filter_seo_score_cell_html' ), 10, 3 );
			add_filter( 'vg_sheet_editor/provider/post/update_item_meta', array( $this, 'filter_cell_data_for_saving' ), 10, 3 );
			add_action( 'vg_sheet_editor/save_rows/after_saving_post', array( $this, 'build_indexables' ), 10, 4 );
			add_filter( 'vg_sheet_editor/formulas/sql_execution/can_execute', array( $this, 'disable_sql_formulas' ), 99, 3 );
			add_action( 'vg_sheet_editor/formulas/execute_formula/after_execution_on_field', array( $this, 'build_indexables_after_formula' ), 10, 6 );
		}

		function build_indexables_after_formula( $post_id, $initial_data, $modified_data, $column_key, $formula, $post_type ) {
			$this->build_indexables(
				$post_id,
				array(
					$column_key => $modified_data,
				),
				array(),
				$post_type
			);
		}

		/**
		 * Disable sql formulas because we need to trigger the rebuild of indexables
		 * @param boolean $allowed
		 * @param string $formula
		 * @param array $column
		 * @return boolean
		 */
		function disable_sql_formulas( $allowed, $formula, $column ) {
			if ( strpos( $column['key'], 'yoast' ) !== false ) {
				$allowed = false;
			}
			return $allowed;
		}

		function build_indexables( $post_id, $item, $data, $post_type ) {
			$modified_keys = implode( ',', array_keys( $item ) );
			if ( strpos( $modified_keys, 'yoast' ) === false ) {
				return;
			}
			if ( ! taxonomy_exists( $post_type ) ) {
				return;
			}
			$term = get_term_by( 'term_id', $post_id, $post_type );
			do_action( 'edited_term', $term->term_id, $term->term_taxonomy_id, $post_type );
		}

		/**
		 * Filter html of SEO score cells to display the score icon.
		 * @param array $data
		 * @param array $qry
		 * @param array $spreadsheet_columns
		 * @return array
		 */
		function filter_seo_score_cell_html( $data, $qry, $spreadsheet_columns ) {

			if ( ! isset( $spreadsheet_columns['_yoast_wpseo_linkdex'] ) ) {
				return $data;
			}
			foreach ( $data as $post_index => $post_row ) {

				$noindex = (int) VGSE()->helpers->get_current_provider()->get_item_meta( $post_row['ID'], '_yoast_wpseo_meta-robots-noindex', true );

				$score = '';
				if ( $noindex ) {
					$score = 'noindex';
				} elseif ( ! empty( $post_row['_yoast_wpseo_linkdex'] ) && method_exists( 'WPSEO_Utils', 'translate_score' ) ) {
					$score = WPSEO_Utils::translate_score( $post_row['_yoast_wpseo_linkdex'] );
				} elseif ( ! empty( $post_row['_yoast_wpseo_linkdex'] ) && method_exists( 'WPSEO_Rank', 'from_numeric_score' ) ){
					$rank  = WPSEO_Rank::from_numeric_score( (int) $post_row['_yoast_wpseo_linkdex'] );
					$score = $rank->get_label();
				}
				$data[ $post_index ]['_yoast_wpseo_linkdex'] = $score;
			}
			return $data;
		}

		/**
		 * Is yoast seo plugin active
		 * @return boolean
		 */
		function is_yoast_seo_plugin_active() {
			return defined( 'WPSEO_VERSION' );
		}

		/**
		 * Test whether the yoast metabox is hidden either by choice of the admin or because
		 * the post type is not a public post type
		 *
		 * @param  string $post_type (optional) The post type to test, defaults to the current post post_type
		 *
		 * @return  bool        Whether or not the meta box (and associated columns etc) should be hidden
		 */
		function is_yoast_metabox_hidden( $post_type = null ) {
			if ( ! $this->yoast_options ) {
				$this->yoast_options = get_option( 'wpseo_titles' );
			}
			$options  = $this->yoast_options;
			$disabled = false;
			if ( ( isset( $options[ 'hideeditbox-' . $post_type ] ) && $options[ 'hideeditbox-' . $post_type ] === true ) || ( isset( $options[ 'hideeditbox-tax-' . $post_type ] ) && $options[ 'hideeditbox-tax-' . $post_type ] === true ) ) {
				$disabled = true;
			}
			return $disabled;
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if ( null == self::$instance ) {
				self::$instance = new WP_Sheet_Editor_YOAST_SEO();
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

		function get_term_meta( $post, $cell_key, $cell_args ) {
			$value = WPSEO_Taxonomy_Meta::get_term_meta( $post, $post->taxonomy, str_replace( '_yoast_wpseo_', '', $cell_key ) );

			if ( $value === 'default' ) {
				$value = '';
			}
			if ( $cell_key === '_yoast_wpseo_noindex' && empty( $value ) ) {
				$value = 'index';
			}
			return $value;
		}

		function save_opengraph_image( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			if ( empty( $data_to_save ) ) {
				$file_id = '';
				$url     = '';
			} else {
				$url_parts = explode( '?', $data_to_save );
				$url       = current( $url_parts );
				$file_id   = VGSE()->helpers->get_attachment_id_from_url( $url );
			}
			update_post_meta( $post_id, $cell_key . '-id', $file_id );
			update_post_meta( $post_id, $cell_key, $url );
		}

		function update_term_meta( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			$old = WPSEO_Taxonomy_Meta::get_term_meta( $post_id, $post_type );
			if ( empty( $old ) ) {
				$old = array();
			}
			$new = array_merge(
				$old,
				array(
					str_replace( '_yoast_', '', $cell_key ) => $data_to_save,
				)
			);

			if ( in_array( $cell_key, array( '_yoast_wpseo_opengraph-image', '_yoast_wpseo_twitter-image' ), true ) ) {
				$new[ $cell_key . '-id' ] = VGSE()->helpers->get_attachment_id_from_url( $data_to_save );
			}

			WPSEO_Taxonomy_Meta::set_values( $post_id, $post_type, $new );
		}

		/**
		 * Register spreadsheet columns
		 */
		function register_columns( $editor ) {
			if ( $editor->provider->key === 'user' ) {
				return;
			}
			$post_types          = $editor->args['enabled_post_types'];
			$tax_settings_global = array(
				'get_value_callback'    => array( $this, 'get_term_meta' ),
				'save_value_callback'   => array( $this, 'update_term_meta' ),
				'supports_sql_formulas' => false,
			);

			foreach ( $post_types as $post_type ) {
				$is_taxonomy = taxonomy_exists( $post_type );
				// Register SEO columns for post types, taxonomies, and users only
				if ( ! post_type_exists( $post_type ) && ! $is_taxonomy && $post_type !== 'user' ) {
					continue;
				}
				if ( $this->is_yoast_metabox_hidden( $post_type ) ) {
					continue;
				}
				$tax_settings = $is_taxonomy ? $tax_settings_global : array();
				$editor->args['columns']->register_item(
					'_yoast_wpseo_title',
					$post_type,
					array_merge(
						array(
							'data_type'         => 'meta_data',
							'unformatted'       => array( 'data' => '_yoast_wpseo_title' ),
							'column_width'      => 300,
							'title'             => __( 'SEO Title', 'vg_sheet_editor' ),
							'type'              => '',
							'supports_formulas' => true,
							'formatted'         => array(
								'data'     => '_yoast_wpseo_title',
								'renderer' => 'html',
							),
							'allow_to_hide'     => true,
							'allow_to_rename'   => true,
						),
						$tax_settings
					)
				);
				$desc_key = ( $is_taxonomy ) ? '_yoast_wpseo_desc' : '_yoast_wpseo_metadesc';
				$editor->args['columns']->register_item(
					$desc_key,
					$post_type,
					array_merge(
						array(
							'data_type'         => 'meta_data',
							'unformatted'       => array( 'data' => $desc_key ),
							'column_width'      => 300,
							'title'             => __( 'SEO Description', 'vg_sheet_editor' ),
							'type'              => '',
							'supports_formulas' => true,
							'formatted'         => array( 'data' => $desc_key ),
							'allow_to_hide'     => true,
							'allow_to_rename'   => true,
						),
						$tax_settings
					)
				);
				$editor->args['columns']->register_item(
					'_yoast_wpseo_focuskw',
					$post_type,
					array_merge(
						array(
							'data_type'         => 'meta_data',
							'unformatted'       => array( 'data' => '_yoast_wpseo_focuskw' ),
							'column_width'      => 120,
							'title'             => __( 'SEO Keyword', 'vg_sheet_editor' ),
							'type'              => '',
							'supports_formulas' => true,
							'formatted'         => array(
								'data'     => '_yoast_wpseo_focuskw',
								'renderer' => 'html',
							),
							'allow_to_hide'     => true,
							'allow_to_rename'   => true,
						),
						$tax_settings
					)
				);

				$editor->args['columns']->register_item(
					'_yoast_wpseo_opengraph-title',
					$post_type,
					array_merge(
						array(
							'data_type'         => 'meta_data',
							'column_width'      => 120,
							'title'             => __( 'SEO FB title', 'vg_sheet_editor' ),
							'type'              => '',
							'supports_formulas' => true,
							'allow_to_hide'     => true,
							'allow_to_rename'   => true,
						),
						$tax_settings
					)
				);
				$editor->args['columns']->register_item(
					'_yoast_wpseo_opengraph-description',
					$post_type,
					array_merge(
						array(
							'data_type'         => 'meta_data',
							'column_width'      => 120,
							'title'             => __( 'SEO FB description', 'vg_sheet_editor' ),
							'type'              => '',
							'supports_formulas' => true,
							'allow_to_hide'     => true,
							'allow_to_rename'   => true,
						),
						$tax_settings
					)
				);
				$editor->args['columns']->register_item(
					'_yoast_wpseo_opengraph-image',
					$post_type,
					array_merge(
						array(
							'data_type'             => 'meta_data',
							'column_width'          => 120,
							'title'                 => __( 'SEO FB image', 'vg_sheet_editor' ),
							'type'                  => 'boton_gallery',
							'supports_formulas'     => true,
							'supports_sql_formulas' => false,
							'allow_to_hide'         => true,
							'allow_to_rename'       => true,
							'save_value_callback'   => array( $this, 'save_opengraph_image' ),
						),
						$tax_settings
					)
				);
				$editor->args['columns']->register_item(
					'_yoast_wpseo_twitter-title',
					$post_type,
					array_merge(
						array(
							'data_type'         => 'meta_data',
							'column_width'      => 120,
							'title'             => __( 'SEO TW title', 'vg_sheet_editor' ),
							'type'              => '',
							'supports_formulas' => true,
							'allow_to_hide'     => true,
							'allow_to_rename'   => true,
						),
						$tax_settings
					)
				);
				$editor->args['columns']->register_item(
					'_yoast_wpseo_twitter-description',
					$post_type,
					array_merge(
						array(
							'data_type'         => 'meta_data',
							'column_width'      => 120,
							'title'             => __( 'SEO TW description', 'vg_sheet_editor' ),
							'type'              => '',
							'supports_formulas' => true,
							'allow_to_hide'     => true,
							'allow_to_rename'   => true,
						),
						$tax_settings
					)
				);
				$editor->args['columns']->register_item(
					'_yoast_wpseo_twitter-image',
					$post_type,
					array_merge(
						array(
							'data_type'             => 'meta_data',
							'column_width'          => 120,
							'title'                 => __( 'SEO TW image', 'vg_sheet_editor' ),
							'type'                  => 'boton_gallery',
							'supports_formulas'     => true,
							'supports_sql_formulas' => false,
							'allow_to_hide'         => true,
							'allow_to_rename'       => true,
							'save_value_callback'   => array( $this, 'save_opengraph_image' ),
						),
						$tax_settings
					)
				);
				$editor->args['columns']->register_item(
					'_yoast_wpseo_canonical',
					$post_type,
					array_merge(
						array(
							'data_type'         => 'meta_data',
							'column_width'      => 120,
							'title'             => __( 'SEO Canonical URL', 'vg_sheet_editor' ),
							'type'              => '',
							'supports_formulas' => true,
							'allow_to_hide'     => true,
							'allow_to_rename'   => true,
						),
						$tax_settings
					)
				);

				$noindex_key       = ( $is_taxonomy ) ? '_yoast_wpseo_noindex' : '_yoast_wpseo_meta-robots-noindex';
				$noindex_checked   = ( $is_taxonomy ) ? 'noindex' : '1';
				$noindex_unchecked = ( $is_taxonomy ) ? 'index' : null;
				$noindex_default   = ( $is_taxonomy ) ? 'noindex' : null;
				$editor->args['columns']->register_item(
					$noindex_key,
					$post_type,
					array_merge(
						array(
							'data_type'         => 'meta_data',
							'unformatted'       => array( 'data' => $noindex_key ),
							'column_width'      => 120,
							'title'             => __( 'SEO No Index', 'vg_sheet_editor' ),
							'type'              => '',
							'supports_formulas' => true,
							'formatted'         => array(
								'data'              => $noindex_key,
								'type'              => 'checkbox',
								'checkedTemplate'   => $noindex_checked,
								'uncheckedTemplate' => $noindex_unchecked,
								'className'         => 'htCenter htMiddle',
							),
							'default_value'     => $noindex_default,
							'allow_to_hide'     => true,
							'allow_to_rename'   => true,
						),
						$tax_settings
					)
				);
				$editor->args['columns']->register_item(
					'_yoast_wpseo_linkdex',
					$post_type,
					array(
						'data_type'         => 'meta_data',
						'unformatted'       => array(
							'data'     => '_yoast_wpseo_linkdex',
							'readOnly' => true,
							'renderer' => 'html',
						),
						'column_width'      => 150,
						'title'             => __( 'SEO', 'vg_sheet_editor' ),
						'type'              => '',
						'supports_formulas' => false,
						'formatted'         => array(
							'data'     => '_yoast_wpseo_linkdex',
							'readOnly' => true,
							'renderer' => 'html',
						),
						'allow_to_hide'     => true,
						'allow_to_rename'   => true,
						'allow_plain_text'  => false,
						'is_locked'         => true,
					)
				);

				if ( $editor->provider->is_post_type ) {
					$primary_taxonomies = $this->generate_primary_term_taxonomies( $post_type );
					foreach ( $primary_taxonomies as $taxonomy ) {

						$editor->args['columns']->register_item(
							'_yoast_wpseo_primary_' . $taxonomy->name,
							$post_type,
							array(
								'data_type'             => 'meta_data',
								'column_width'          => 100,
								'title'                 => sprintf( __( 'SEO Primary %s', 'vg_sheet_editor' ), esc_html( $taxonomy->labels->singular_name ) ),
								'type'                  => '',
								'supports_formulas'     => true,
								'supports_sql_formulas' => false,
								'formatted'             => array(
									'type'         => 'autocomplete',
									'source'       => 'loadTaxonomyTerms',
									'taxonomy_key' => $taxonomy->name,
								),
								'allow_to_hide'         => true,
								'allow_to_rename'       => true,
								'allow_plain_text'      => true,
								'prepare_value_for_display' => array( $this, 'prepare_primary_category_value_for_display' ),
							)
						);
					}
				}
			}
		}

		function prepare_primary_category_value_for_display( $value, $post, $key, $column_settings ) {
			$taxonomy = str_replace( '_yoast_wpseo_primary_', '', $key );
			$term     = get_term_by( 'term_id', $value, $taxonomy );

			if ( is_object( $term ) && ! is_wp_error( $term ) ) {
				$value = $term->name;
			} else {
				$value = '';
			}
			return $value;
		}

		function filter_cell_data_for_saving( $new_value, $id, $key ) {
			$post_type = get_post_type( $id );
			if ( $this->is_yoast_metabox_hidden( $post_type ) ) {
				return $new_value;
			}

			if ( strpos( $key, '_yoast_wpseo_primary_' ) !== false ) {
				$taxonomy    = str_replace( '_yoast_wpseo_primary_', '', $key );
				$terms_saved = VGSE()->data_helpers->prepare_post_terms_for_saving( $new_value, $taxonomy );
				if ( ! empty( $terms_saved ) ) {
					$new_value = current( $terms_saved );
				} else {
					$new_value = '';
				}
			}

			return $new_value;
		}

		/**
		 * Returns whether or not a taxonomy is hierarchical.
		 *
		 * @param stdClass $taxonomy Taxonomy object.
		 *
		 * @return bool
		 */
		private function filter_hierarchical_taxonomies( $taxonomy ) {
			return (bool) $taxonomy->hierarchical;
		}

		function generate_primary_term_taxonomies( $post_type ) {
			$all_taxonomies = get_object_taxonomies( $post_type, 'objects' );
			$all_taxonomies = array_filter( $all_taxonomies, array( $this, 'filter_hierarchical_taxonomies' ) );

			/**
			 * Filters which taxonomies for which the user can choose the primary term.
			 *
			 * @api array    $taxonomies An array of taxonomy objects that are primary_term enabled.
			 *
			 * @param string $post_type      The post type for which to filter the taxonomies.
			 * @param array  $all_taxonomies All taxonomies for this post types, even ones that don't have primary term
			 *                               enabled.
			 */
			$taxonomies = (array) apply_filters( 'wpseo_primary_term_taxonomies', $all_taxonomies, $post_type, $all_taxonomies );

			return $taxonomies;
		}

	}

}

if ( ! function_exists( 'WP_Sheet_Editor_YOAST_SEO_Obj' ) ) {

	function WP_Sheet_Editor_YOAST_SEO_Obj() {
		return WP_Sheet_Editor_YOAST_SEO::get_instance();
	}
}


add_action( 'vg_sheet_editor/initialized', 'WP_Sheet_Editor_YOAST_SEO_Obj' );
