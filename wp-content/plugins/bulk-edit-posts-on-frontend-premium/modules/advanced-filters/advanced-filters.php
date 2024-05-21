<?php defined( 'ABSPATH' ) || exit;
if ( ! class_exists( 'WP_Sheet_Editor_Advanced_Filters' ) ) {

	/**
	 * Filter rows in the spreadsheet editor.
	 */
	class WP_Sheet_Editor_Advanced_Filters {

		private static $instance    = false;
		var $plugin_url             = null;
		var $plugin_dir             = null;
		var $favorite_search_fields = 'vgse_favorite_search_fields';

		private function __construct() {

		}

		function init() {

			$this->plugin_url = plugins_url( '/', __FILE__ );
			$this->plugin_dir = __DIR__;

			add_action( 'vg_sheet_editor/after_enqueue_assets', array( $this, 'register_assets' ) );
			add_action( 'vg_sheet_editor/filters/after_fields', array( $this, 'add_filters_fields' ), 10, 2 );
			add_action( 'vg_sheet_editor/filters/before_form_closing', array( $this, 'add_advanced_filters_fields' ), 10, 2 );
			// Priority 12 because this needs to run after the filters.php module added its own query parameters, otherwise the post__in parameter doesn't work well
			add_filter( 'vg_sheet_editor/load_rows/wp_query_args', array( $this, 'filter_posts' ), 12, 2 );
			add_filter( 'vg_sheet_editor/filters/allowed_fields', array( $this, 'register_filters' ), 10, 2 );
			add_filter( 'posts_clauses', array( $this, 'exclude_by_keyword' ), 10, 2 );
			add_filter( 'posts_clauses', array( $this, 'add_advanced_post_data_query' ), 10, 2 );
			add_filter( 'posts_clauses', array( $this, 'add_advanced_taxonomy_query' ), 10, 2 );
			add_action( 'vg_sheet_editor/filters/after_form_closing', array( $this, 'render_save_this_search' ) );
			add_action( 'vg_sheet_editor/editor/before_init', array( $this, 'register_toolbar_items' ), 50 );
			add_action( 'wp_ajax_vgse_delete_saved_search', array( $this, 'delete_saved_search' ) );
			add_action( 'wp_ajax_vgse_mark_search_fields_as_favorite', array( $this, 'mark_search_fields_as_favorite' ) );
			add_filter( 'vg_sheet_editor/js_data', array( $this, 'add_favorite_search_fields_data' ), 10, 2 );
			add_filter( 'vg_sheet_editor/filters/sanitize_request_filters', array( $this, 'register_custom_filters' ), 10, 2 );
		}

		function add_favorite_search_fields_data( $data, $post_type ) {
			$saved_items = get_option( $this->favorite_search_fields );

			$data['favorite_search_fields'] = ( is_array( $saved_items ) && isset( $saved_items[ $post_type ] ) ) ? $saved_items[ $post_type ] : array();
			return $data;
		}

		function mark_search_fields_as_favorite() {
			if ( empty( $_POST['post_type'] ) || ! VGSE()->helpers->verify_nonce_from_request() || ! VGSE()->helpers->user_can_manage_options() ) {
				wp_send_json_error( array( 'message' => __( 'You dont have enough permissions to view this page.', 'vg_sheet_editor' ) ) );
			}

			$post_type = VGSE()->helpers->sanitize_table_key( $_POST['post_type'] );
			$fields    = empty( $_POST['fields'] ) ? array() : array_map( 'sanitize_text_field', $_POST['fields'] );

			$saved_items = get_option( $this->favorite_search_fields );
			if ( empty( $saved_items ) ) {
				$saved_items = array();
			}

			if ( ! isset( $saved_items[ $post_type ] ) ) {
				$saved_items[ $post_type ] = array();
			}

			$saved_items[ $post_type ] = $fields;
			update_option( $this->favorite_search_fields, $saved_items );
			wp_send_json_success();
		}

		function delete_saved_search() {
			if ( empty( $_POST['post_type'] ) || ! VGSE()->helpers->verify_nonce_from_request() || ! VGSE()->helpers->user_can_manage_options() ) {
				wp_send_json_error( array( 'message' => __( 'You dont have enough permissions to view this page.', 'vg_sheet_editor' ) ) );
			}

			$post_type = VGSE()->helpers->sanitize_table_key( $_POST['post_type'] );
			$name      = sanitize_text_field( $_POST['search_name'] );

			$saved_items = get_option( 'vgse_saved_searches' );
			if ( empty( $saved_items ) ) {
				wp_send_json_success();
			}

			if ( ! isset( $saved_items[ $post_type ] ) ) {
				wp_send_json_success();
			}

			$same_name = wp_list_filter( $saved_items[ $post_type ], array( 'name' => $name ) );
			foreach ( $same_name as $index => $same_name_search ) {
				unset( $saved_items[ $post_type ][ $index ] );
			}
			update_option( 'vgse_saved_searches', $saved_items, false );
			wp_send_json_success();
		}

		function register_toolbar_items( $editor ) {

			if ( ! VGSE()->helpers->user_can_manage_options() ) {
				return;
			}
			$post_types   = $editor->args['enabled_post_types'];
			$private_keys = array( 'name', 'search_name', 'post_type' );
			foreach ( $post_types as $post_type ) {
				$saved_searches = $this->get_saved_searches( $post_type );
				foreach ( $saved_searches as $index => $saved_search ) {
					$name = esc_html( $saved_search['name'] );
					foreach ( $saved_search as $key => $value ) {
						if ( in_array( $key, $private_keys, true ) ) {
							unset( $saved_search[ $key ] );
						}
					}

					$editor->args['toolbars']->register_item(
						'saved_search' . $index,
						array(
							'type'                  => 'button',
							'content'               => $name,
							'toolbar_key'           => 'secondary',
							'allow_in_frontend'     => true,
							'parent'                => 'run_filters',
							'extra_html_attributes' => 'data-saved-type="search" data-saved-item data-item-name="' . esc_attr( $name ) . '" data-start-saved-search="' . esc_attr( json_encode( $saved_search ) ) . '"',
						),
						$post_type
					);
				}
			}
		}

		function render_save_this_search() {
			if ( ! VGSE()->helpers->user_can_manage_options() || ! is_admin() ) {
				return;
			}
			?>
			<div class="save-search-wrapper">
				<label class="save-search"><?php _e( 'Save this search', 'vg_sheet_editor' ); ?></label>
				<input name="search_name" placeholder="<?php esc_attr_e( 'Enter a name...', 'vg_sheet_editor' ); ?>" class="save-search-input" type="text">
			</div>
			<?php
		}

		function add_advanced_taxonomy_query( $clauses, $wp_query ) {
			global $wpdb;
			if ( empty( $wp_query->query['wpse_original_filters'] ) || empty( $wp_query->query['wpse_original_filters']['meta_query'] ) || ! is_array( $wp_query->query['wpse_original_filters']['meta_query'] ) ) {
				return $clauses;
			}
			$post_data_query = wp_list_filter(
				$wp_query->query['wpse_original_filters']['meta_query'],
				array(
					'source' => 'taxonomy_keys',
				)
			);
			if ( empty( $post_data_query ) ) {
				return $clauses;
			}

			$wheres = array(
				'IN'     => array(),
				'NOT IN' => array(),
			);
			foreach ( $post_data_query as $post_data_parameters ) {
				if ( empty( $post_data_parameters['key'] ) || empty( $post_data_parameters['compare'] ) ) {
					continue;
				}
				if ( in_array( $post_data_parameters['compare'], array( 'LIKE', 'NOT LIKE' ) ) ) {
					$post_data_parameters['value'] = '%' . $post_data_parameters['value'] . '%';
				}

				if ( $post_data_parameters['compare'] === 'length_less' ) {
					if ( (int) $post_data_parameters['value'] < 1 ) {
						$post_data_parameters['value'] = 1;
					}
					$post_data_parameters['compare'] = 'REGEXP';
					$post_data_parameters['value']   = '^.{0,' . (int) $post_data_parameters['value'] . '}$';
				}
				if ( $post_data_parameters['compare'] === 'length_higher' ) {
					if ( (int) $post_data_parameters['value'] < 1 ) {
						$post_data_parameters['value'] = 1;
					}
					$post_data_parameters['compare'] = 'REGEXP';
					$post_data_parameters['value']   = '^.{' . (int) $post_data_parameters['value'] . ',}$';
				}

				if ( $post_data_parameters['compare'] === 'OR' ) {
					$post_data_parameters['compare'] = 'REGEXP';
					$keywords                        = array_filter( array_map( 'preg_quote', array_map( 'trim', explode( ';', $post_data_parameters['value'] ) ) ) );
					$post_data_parameters['value']   = '(' . implode( '|', $keywords ) . ')';
				}
				if ( $post_data_parameters['compare'] === 'starts_with' ) {
					$post_data_parameters['compare'] = 'LIKE';
					$post_data_parameters['value']   = $post_data_parameters['value'] . '%';
				}
				if ( $post_data_parameters['compare'] === 'ends_with' ) {
					$post_data_parameters['compare'] = 'LIKE';
					$post_data_parameters['value']   = '%' . $post_data_parameters['value'];
				}

				$group = 'IN';
				if ( in_array( $post_data_parameters['compare'], array( 'NOT LIKE' ) ) ) {
					$post_data_parameters['compare'] = 'LIKE';
					$group                           = 'NOT IN';
				} elseif ( empty( $post_data_parameters['value'] ) && $post_data_parameters['compare'] === '=' ) {
					$group = 'NOT IN';
				}
				if ( empty( $post_data_parameters['value'] ) ) {
					$sql_where = "tt.taxonomy IN ('" . esc_sql( $post_data_parameters['key'] ) . "')";
				} else {
					$sql_where = "tt.taxonomy IN ('" . esc_sql( $post_data_parameters['key'] ) . "') AND t.name " . esc_sql( $post_data_parameters['compare'] ) . " '" . esc_sql( $post_data_parameters['value'] ) . "' ";
				}
				$sql                = "SELECT tr.object_id
FROM $wpdb->terms AS t 
INNER JOIN $wpdb->term_taxonomy AS tt 
ON t.term_id = tt.term_id
INNER JOIN $wpdb->term_relationships AS tr
ON tr.term_taxonomy_id = tt.term_taxonomy_id
INNER JOIN $wpdb->posts AS p 
ON (p.ID = tr.object_id)

WHERE 1 = 1 AND p.post_type = '" . $wp_query->query['post_type'] . "' 

AND " . $sql_where . ' 
  
GROUP BY tr.object_id';
				$wheres[ $group ][] = $sql;
			}

			foreach ( $wheres as $operator => $queries ) {
				foreach ( $queries as $query ) {
					$clauses['where'] .= " AND $wpdb->posts.ID $operator (" . $query . ')  ';
				}
			}
			return $clauses;
		}

		function _parse_meta_query_args( $meta_query_args, $allowed_source = 'meta', &$query_args = array() ) {
			foreach ( $meta_query_args as $index => $meta_query ) {
				if ( $allowed_source && $meta_query['source'] !== $allowed_source ) {
					unset( $meta_query_args[ $index ] );
					continue;
				}

				// When searching for non-empty featured images, it's more accurate the query field > 0
				if ( $meta_query['key'] === '_thumbnail_id' && $meta_query['compare'] === '!=' && $meta_query['value'] === '' ) {
					$meta_query['compare'] = '>';
					$meta_query['value']   = '0';
				}

				if ( $meta_query['compare'] === 'length_less' ) {
					if ( (int) $meta_query['value'] < 1 ) {
						$meta_query['value'] = 1;
					}
					$meta_query_args[ $index ]['compare'] = 'REGEXP';
					$meta_query_args[ $index ]['value']   = '^.{0,' . (int) $meta_query['value'] . '}$';
				}
				if ( $meta_query['compare'] === 'length_higher' ) {
					if ( (int) $meta_query['value'] < 1 ) {
						$meta_query['value'] = 1;
					}
					$meta_query_args[ $index ]['compare'] = 'REGEXP';
					$meta_query_args[ $index ]['value']   = '^.{' . (int) $meta_query['value'] . ',}$';
				}
				if ( $meta_query['compare'] === 'OR' ) {
					$meta_query_args[ $index ]['compare'] = 'REGEXP';
					$keywords                             = array_filter( array_map( 'trim', explode( ';', $meta_query['value'] ) ) );
					$meta_query_args[ $index ]['value']   = '(' . implode( '|', $keywords ) . ')';
				}
				if ( $meta_query['compare'] === 'starts_with' ) {
					$meta_query_args[ $index ]['compare'] = 'REGEXP';
					$meta_query_args[ $index ]['value']   = '^' . $meta_query['value'];
				}
				if ( $meta_query['compare'] === 'ends_with' ) {
					$meta_query_args[ $index ]['compare'] = 'REGEXP';
					$meta_query_args[ $index ]['value']  .= '$';
				}

				if ( class_exists( 'WooCommerce' ) && in_array( $meta_query['key'], array( '_sale_price_dates_from', '_sale_price_dates_to' ), true ) && ! empty( $meta_query['value'] ) ) {
					if ( $meta_query['key'] === '_sale_price_dates_to' ) {
						$meta_query['value'] .= ' 23:59:59';
					}
					$meta_query_args[ $index ]['value'] = wp_date( 'U', get_gmt_from_date( $meta_query['value'], 'U' ) );
				}

				if ( in_array( $meta_query['compare'], array( '>', '>=', '<', '<=' ) ) && is_numeric( $meta_query['value'] ) ) {
					$meta_query_args[ $index ]['type'] = 'NUMERIC';
				}
				if ( empty( $meta_query['value'] ) && in_array( $meta_query['compare'], array( '=', 'LIKE' ) ) && $allowed_source === 'meta' ) {
					$not_exists                = $meta_query;
					$not_exists['compare']     = 'NOT EXISTS';
					$meta_query_args[ $index ] = array(
						'relation' => 'OR',
						$meta_query,
						$not_exists,
					);
				}

				if ( ! empty( $meta_query_args[ $index ]['value'] ) ) {
					$meta_query_args[ $index ]['value'] = $this->_maybe_convert_username_to_user_id( 'meta', $meta_query_args[ $index ] );
					$meta_query_args[ $index ]['value'] = $this->_maybe_convert_post_to_ids( $meta_query_args[ $index ] );
				}
			}
			return $meta_query_args;
		}

		/**
		 * This function is necessary to convert the friendly post titles received from the search form into the post IDs saved in the database to be used for the searches.
		 * Ideally, this function should work automatically on any column with "post dropdown" format, however we have many columns with post dropdowns that don't use the columns manager and we need to update all those columns to use the columns manager for this to work on all the post dropdown columns.
		 * We're hardcoding support for 2 meta keys for now as a workaround.
		 *
		 * @param  array $meta_query
		 * @return string
		 */
		function _maybe_convert_post_to_ids( $meta_query ) {
			global $wpdb;
			$cell_key = $meta_query['key'];
			if ( $cell_key === '_EventVenueID' ) {
				$meta_query['value'] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = 'tribe_venue'", html_entity_decode( $meta_query['value'] ) ) );
			}
			if ( $cell_key === '_EventOrganizerID' ) {
				$meta_query['value'] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = 'tribe_organizer'", html_entity_decode( $meta_query['value'] ) ) );
			}

			return $meta_query['value'];
		}

		function _maybe_convert_username_to_user_id( $allowed_source, $meta_query ) {
			$post_type           = VGSE()->helpers->get_provider_from_query_string();
			$spreadsheet_columns = VGSE()->helpers->get_unfiltered_provider_columns( $post_type );
			$cell_key            = $meta_query['key'];

			$column_uses_user_search = in_array( $allowed_source, array( 'meta', 'post_data' ), true ) && ! empty( $spreadsheet_columns ) && isset( $spreadsheet_columns[ $cell_key ] ) && ! empty( $spreadsheet_columns[ $cell_key ]['formatted']['source'] ) && $spreadsheet_columns[ $cell_key ]['formatted']['source'] === 'searchUsers';

			if ( ! empty( $meta_query['value'] ) && $column_uses_user_search ) {
				$column_settings = $spreadsheet_columns[ $cell_key ];
				$username        = $meta_query['value'];
				// If it uses the regular saving (not prepare nor save callback)
				if ( empty( $column_settings['prepare_value_for_database'] ) && empty( $column_settings['save_value_callback'] ) ) {
					$meta_query['value'] = VGSE()->data_helpers->set_post( 'post_author', $username );
				} elseif ( ! empty( $column_settings['prepare_value_for_database'] ) && empty( $column_settings['save_value_callback'] ) ) {
					$meta_query['value'] = call_user_func( $column_settings['prepare_value_for_database'], null, $cell_key, $username, null, $column_settings, $spreadsheet_columns );
				}
			}
			return $meta_query['value'];
		}

		function _build_sql_wheres_for_data_table( $post_data_query, $table_name ) {
			$wheres = array();
			foreach ( $post_data_query as $post_data_parameters ) {
				if ( empty( $post_data_parameters['key'] ) || empty( $post_data_parameters['compare'] ) ) {
					continue;
				}
				if ( ! is_string( $post_data_parameters['key'] ) || ! is_string( $post_data_parameters['compare'] ) ) {
					continue;
				}
				if ( in_array( $post_data_parameters['compare'], array( 'LIKE', 'NOT LIKE' ) ) ) {
					$post_data_parameters['value'] = '%' . $post_data_parameters['value'] . '%';
				}

				if ( $post_data_parameters['compare'] === 'length_less' ) {
					if ( (int) $post_data_parameters['value'] < 1 ) {
						$post_data_parameters['value'] = 1;
					}
					$post_data_parameters['compare']    = '<';
					$post_data_parameters['use_length'] = true;
				}
				if ( $post_data_parameters['compare'] === 'length_higher' ) {
					if ( (int) $post_data_parameters['value'] < 1 ) {
						$post_data_parameters['value'] = 1;
					}
					$post_data_parameters['compare']    = '>';
					$post_data_parameters['use_length'] = true;
				}
				if ( $post_data_parameters['compare'] === 'OR' ) {
					$post_data_parameters['compare'] = 'REGEXP';
					$keywords                        = array_filter( array_map( 'trim', explode( ';', $post_data_parameters['value'] ) ) );
					$post_data_parameters['value']   = '(' . implode( '|', $keywords ) . ')';
				}
				if ( $post_data_parameters['compare'] === 'starts_with' ) {
					$post_data_parameters['compare'] = 'LIKE';
					$post_data_parameters['value']   = $post_data_parameters['value'] . '%';
				}
				if ( $post_data_parameters['compare'] === 'ends_with' ) {
					$post_data_parameters['compare'] = 'LIKE';
					$post_data_parameters['value']   = '%' . $post_data_parameters['value'];
				}
				// If the value is a date like Y-m-d 00:00:00 and uses the CONTAINS (LIKE) operator,
				// remove the time part because they always want to get CONTAINS=date without time
				if ( $post_data_parameters['compare'] === 'LIKE' && preg_match( '/^\d{4}-\d{2}-\d{2} 00:00:00$/', $post_data_parameters['value'] ) ) {
					$post_data_parameters['value'] = str_replace( ' 00:00:00', '', $post_data_parameters['value'] );
				}
				$post_data_parameters['value'] = $this->_maybe_convert_username_to_user_id( 'post_data', $post_data_parameters );

				if ( in_array( $post_data_parameters['compare'], array( '<', '>' ), true ) && ! empty( $post_data_parameters['use_length'] ) ) {
					$wheres[] = " LENGTH($table_name." . esc_sql( $post_data_parameters['key'] ) . ') ' . esc_sql( $post_data_parameters['compare'] ) . '  ' . (int) $post_data_parameters['value'];
				} else {
					$wheres[] = " $table_name." . esc_sql( $post_data_parameters['key'] ) . ' ' . esc_sql( $post_data_parameters['compare'] ) . " '" . esc_sql( $post_data_parameters['value'] ) . "' ";
				}
			}
			return $wheres;
		}

		function add_advanced_post_data_query( $clauses, $wp_query ) {
			global $wpdb;
			if ( empty( $wp_query->query['wpse_original_filters'] ) || empty( $wp_query->query['wpse_original_filters']['meta_query'] ) || ! is_array( $wp_query->query['wpse_original_filters']['meta_query'] ) ) {
				return $clauses;
			}
			$post_data_query = wp_list_filter(
				$wp_query->query['wpse_original_filters']['meta_query'],
				array(
					'source' => 'post_data',
				)
			);
			if ( empty( $post_data_query ) ) {
				return $clauses;
			}

			// Remove the post_status clause added by wp_query automatically because we
			// have an advanced filter for the same field
			if ( ! empty( $wp_query->query['wpse_original_post_statuses'] ) ) {
				$post_type_object = get_post_type_object( $wp_query->query['post_type'] );

				$statuses_to_remove = $wp_query->query['wpse_original_post_statuses'];
				if ( $post_type_object && WP_Sheet_Editor_Helpers::current_user_can( $post_type_object->cap->edit_published_posts ) ) {
					$allowed_statuses = array_merge( array( 'trash' ), $statuses_to_remove );
				} else {
					$allowed_statuses = $statuses_to_remove;
				}
				$has_allowed_post_status_filter = false;
				foreach ( $post_data_query as $index => $post_data_query_single ) {
					if ( $post_data_query_single['key'] !== 'post_status' ) {
						continue;
					}
					if ( in_array( $post_data_query_single['value'], $allowed_statuses, true ) ) {
						$has_allowed_post_status_filter = true;
					} else {
						unset( $post_data_query[ $index ] );
					}
				}
				if ( $has_allowed_post_status_filter ) {
					$clauses['where'] = preg_replace( '/AND \(\(' . $wpdb->posts . ".post_status[A-Za-z0-9 ='\._\-]+\)\)/", '', $clauses['where'] );
				}
			}

			$wheres = $this->_build_sql_wheres_for_data_table( $post_data_query, $wpdb->posts );
			if ( ! empty( $wheres ) ) {
				$clauses['where'] .= ' AND ' . implode( ' AND ', $wheres );
			}

			return $clauses;
		}

		function exclude_by_keyword( $clauses, $wp_query ) {
			if ( ! empty( $wp_query->query['wpse_not_contains_keyword'] ) ) {
				$clauses = WP_Sheet_Editor_Filters::get_instance()->add_search_by_keyword_clause( $clauses, $wp_query->query['wpse_not_contains_keyword'], 'NOT LIKE', 'AND' );
			}
			return $clauses;
		}

		/**
		 * Register frontend assets
		 */
		function register_assets() {
			wp_enqueue_script( 'advanced-filters_js', $this->plugin_url . 'assets/js/init.js', array(), VGSE()->version, false );
		}

		function register_filters( $filters, $post_type ) {
			if ( VGSE()->helpers->get_current_provider()->is_post_type ) {
				$taxonomies = get_object_taxonomies( $post_type );
				if ( ! empty( $taxonomies ) ) {
					$filters['taxonomy_term'] = array(
						'label'       => '',
						'description' => '',
					);
				}
				$filters['date'] = array(
					'label'       => '',
					'description' => '',
				);
				if ( post_type_supports( $post_type, 'page-attributes' ) && $post_type !== 'attachment' ) {
					$filters['post_parent'] = array(
						'label'       => __( 'Parent', 'vg_sheet_editor' ),
						'description' => '',
					);
				}
				// Remove the post status field because they can search using the advanced filters
				if ( isset( $filters['post_status'] ) ) {
					unset( $filters['post_status'] );
				}
			}
			return $filters;
		}

		function get_saved_searches( $post_type ) {

			$saved_items = get_option( 'vgse_saved_searches' );
			if ( empty( $saved_items ) ) {
				$saved_items = array();
			}

			if ( ! isset( $saved_items[ $post_type ] ) ) {
				$saved_items[ $post_type ] = array();
			}
			usort(
				$saved_items[ $post_type ],
				function( $a, $b ) {
					return strcmp( $a['search_name'], $b['search_name'] );
				}
			);
			return $saved_items[ $post_type ];
		}

		function save_search( $data ) {
			if ( empty( $data['name'] ) ) {
				return;
			}
			$post_type   = $data['post_type'];
			$saved_items = get_option( 'vgse_saved_searches' );
			if ( empty( $saved_items ) ) {
				$saved_items = array();
			}

			if ( ! isset( $saved_items[ $post_type ] ) ) {
				$saved_items[ $post_type ] = array();
			}

			$same_name = wp_list_filter( $saved_items[ $post_type ], array( 'name' => $data['name'] ) );
			foreach ( $same_name as $index => $same_name_search ) {
				unset( $saved_items[ $post_type ][ $index ] );
			}
			$saved_items[ $post_type ][] = $data;
			update_option( 'vgse_saved_searches', $saved_items, false );
		}

		function register_custom_filters( $sanitized_filters, $dirty_filters ) {

			if ( isset( $dirty_filters['post_name__in'] ) ) {
				$sanitized_filters['post_name__in'] = sanitize_textarea_field( $dirty_filters['post_name__in'] );
			}
			return $sanitized_filters;
		}

		/**
		 * Apply filters to wp-query args
		 * @param array $query_args
		 * @param array $data
		 * @return array
		 */
		function filter_posts( $query_args, $data ) {

			if ( ! empty( $data['filters'] ) ) {
				$filters = WP_Sheet_Editor_Filters::get_instance()->get_raw_filters( $data );

				if ( ! empty( $filters['search_name'] ) && VGSE()->helpers->user_can_manage_options() ) {
					$this->save_search(
						array_merge(
							$filters,
							array(
								'name'      => sanitize_text_field( $filters['search_name'] ),
								'post_type' => $query_args['post_type'],
							)
						)
					);
				}
				$query_args['wpse_original_filters'] = $filters;

				if ( ! empty( $filters['apply_to'] ) && is_array( $filters['apply_to'] ) ) {
					$taxonomies_group = array();

					$filters['apply_to'] = array_map( 'sanitize_text_field', $filters['apply_to'] );
					foreach ( $filters['apply_to'] as $term ) {
						$term_parts = explode( '--', $term );
						if ( count( $term_parts ) !== 2 ) {
							continue;
						}
						$taxonomy = $term_parts[0];
						$term     = $term_parts[1];

						if ( ! isset( $taxonomies_group[ $taxonomy ] ) ) {
							$taxonomies_group[ $taxonomy ] = array();
						}
						$taxonomies_group[ $taxonomy ][] = $term;
					}

					$query_args['tax_query'] = array(
						'relation' => 'AND',
					);

					foreach ( $taxonomies_group as $taxonomy_key => $terms ) {
						$query_args['tax_query'][] = array(
							'taxonomy' => $taxonomy_key,
							'field'    => 'slug',
							'terms'    => $terms,
						);
					}
				}

				if ( ! empty( $filters['keyword_exclude'] ) ) {
					$editor = VGSE()->helpers->get_provider_editor( $query_args['post_type'] );
					if ( $editor->provider->is_post_type ) {
						$query_args['wpse_not_contains_keyword'] = $filters['keyword_exclude'];
					} else {
						$post_id_exclude            = $editor->provider->get_item_ids_by_keyword( $filters['keyword_exclude'], $query_args['post_type'], 'LIKE' );
						$query_args['post__not_in'] = $post_id_exclude;
					}
				}

				if ( ! empty( $filters['post_parent'] ) ) {
					$query_args['post_parent'] = (int) str_replace( 'page--', '', $filters['post_parent'] );
				}
				if ( ! empty( $filters['date_from'] ) || ! empty( $filters['date_to'] ) ) {
					$query_args['date_query'] = array(
						'inclusive' => true,
					);
				}
				if ( ! empty( $filters['post__in'] ) ) {
					$post_ids_parts = preg_split( '/\r\n|\r|\n|\t|\s|,/', $filters['post__in'] );
					$post_ids       = array();
					foreach ( $post_ids_parts as $post_ids_part ) {
						if ( strpos( $post_ids_part, '-' ) !== false ) {
							$range_parts = array_filter( explode( '-', $post_ids_part ) );
							if ( count( $range_parts ) === 2 ) {
								$post_ids = array_merge( $post_ids, range( (int) $range_parts[0], (int) $range_parts[1] ) );
							}
						} else {
							$post_ids[] = $post_ids_part;
						}
					}
					$post_ids               = array_map( 'intval', $post_ids );
					$query_args['post__in'] = ( ! empty( $query_args['post__in'] ) ) ? array_intersect( $query_args['post__in'], $post_ids ) : $post_ids;
				}
				if ( ! empty( $filters['post_name__in'] ) ) {
					$post_slugs = array_unique( array_filter( array_map( 'sanitize_text_field', array_map( 'basename', array_map( 'trim', preg_split( '/\r\n|\r|\n/', $filters['post_name__in'] ) ) ) ) ) );
					if ( ! empty( $post_slugs ) ) {
						$query_args['post_name__in'] = $post_slugs;
					}
				}
				if ( ! empty( $filters['date_from'] ) ) {
					$query_args['date_query']['after'] = sanitize_text_field( $filters['date_from'] );
				}
				if ( ! empty( $filters['date_to'] ) ) {
					$query_args['date_query']['before'] = sanitize_text_field( $filters['date_to'] );
				}
				if ( ! empty( $filters['meta_query'] ) && is_array( $filters['meta_query'] ) ) {

					$all_advanced_filters = json_encode( $filters['meta_query'] );
					// If there is one advanced filter for post_status, we add a flag
					// and we will remove the post_status clause from the sql query later
					if ( strpos( $all_advanced_filters, '"key":"post_status"' ) !== false && isset( $query_args['post_status'] ) && post_type_exists( $query_args['post_type'] ) ) {
						$query_args['wpse_original_post_statuses'] = $query_args['post_status'];
					}
					$filters['meta_query']    = $this->_parse_meta_query_args( $filters['meta_query'], 'meta', $query_args );
					$query_args['meta_query'] = $filters['meta_query'];
				}
			}

			return $query_args;
		}

		function add_filters_fields( $current_post_type, $filters ) {
			?>

			<?php
			if ( isset( $filters['taxonomy_term'] ) ) {
				?>
				<li class="
				<?php
				$labels = apply_filters( 'vg_sheet_editor/advanced_filters/taxonomy_labels', VGSE()->helpers->get_post_type_taxonomies_single_data( $current_post_type, 'label' ), $current_post_type );
				if ( empty( $labels ) ) {
					echo ' hidden';
				}

				if ( count( $labels ) > 1 ) {
					$labels[ count( $labels ) - 1 ] = ' or ' . end( $labels );
				}
				?>
				">
					<label><?php printf( __( 'Enter %s', 'vg_sheet_editor' ), esc_html( implode( ', ', array_unique( $labels ) ) ) ); ?> <a href="#" data-wpse-tooltip="up" aria-label="<?php _e( 'Enter the names of ' . esc_html( implode( ', ', $labels ) ) ); ?>">( ? )</a></label>
					<select data-placeholder="<?php _e( 'Category name...', 'vg_sheet_editor' ); ?>" name="apply_to[]" class="select2"  multiple data-remote="true" data-action="vgse_search_taxonomy_terms" data-min-input-length="4">

					</select>
				</li>
			<?php } ?>

			<?php if ( isset( $filters['post_parent'] ) ) { ?>
				<li>
					<label><?php echo wp_kses_post( $filters['post_parent']['label'] ); ?>  <?php
					if ( ! empty( $filters['post_parent']['description'] ) ) {
						?>
						<a href="#" data-wpse-tooltip="right" aria-label="<?php echo esc_attr( $filters['post_parent']['description'] ); ?>">( ? )</a><?php } ?></label>
					<select name="post_parent" data-remote="true" data-min-input-length="4" data-action="vgse_find_post_by_name" data-post-type="<?php echo esc_attr( $current_post_type ); ?>" data-nonce="<?php echo wp_create_nonce( 'bep-nonce' ); ?>" data-placeholder="<?php _e( 'Select...', 'vg_sheet_editor' ); ?> " class="select2" multiple>
						<option></option>
					</select> 									
				</li>
				<?php
			}
		}

		function get_advanced_filters_fields( $current_post_type, $filters ) {
			global $wpdb;

			$cache_key = 'vgse_advanced_filter_fields' . $current_post_type;
			$out       = get_transient( $cache_key );
			if ( method_exists( VGSE()->helpers, 'can_rescan_db_fields' ) && VGSE()->helpers->can_rescan_db_fields( $current_post_type ) ) {
				$out = false;
			}

			if ( ! $out ) {
				$maximum_fields = ( ! empty( VGSE()->options['maximum_advanced_filters_fields'] ) ) ? (int) VGSE()->options['maximum_advanced_filters_fields'] : 1000;
				$all_meta_keys  = apply_filters( 'vg_sheet_editor/advanced_filters/all_meta_keys', VGSE()->helpers->get_all_meta_keys( $current_post_type, $maximum_fields ), $current_post_type, $filters );

				// post data and taxonomy advanced filters are available for post types only
				if ( VGSE()->helpers->get_current_provider()->is_post_type ) {
					$taxonomy_keys = VGSE()->helpers->get_post_type_taxonomies_single_data( $current_post_type, 'name' );
					$item_raw      = $wpdb->get_row( "SELECT * FROM $wpdb->posts LIMIT 1", ARRAY_A );
					$item          = ( is_array( $item_raw ) ) ? array_keys( $item_raw ) : array();
				} else {
					$item          = array();
					$taxonomy_keys = array();
				}

				$all_fields = array(
					'meta'          => array_unique( $all_meta_keys ),
					'post_data'     => array_unique( $item ),
					'taxonomy_keys' => array_unique( $taxonomy_keys ),
				);
				$out        = apply_filters( 'vg_sheet_editor/advanced_filters/all_fields_groups', $all_fields, $current_post_type, $filters );
				set_transient( $cache_key, $out, DAY_IN_SECONDS );
			}

			return $out;
		}

		function _render_advanced_filter_row( $current_post_type, $filters, $selected_values = array(), $wrapper = 'li' ) {
			$default_selected_values = array(
				'source'  => '',
				'key'     => '',
				'value'   => '',
				'compare' => '',
			);
			$selected_values         = wp_parse_args( $selected_values, $default_selected_values );
			?>
			<<?php echo esc_html( $wrapper ); ?> class="base advanced-field" style="display: none;">
			<div class="fields-wrap">
				<div class="field-wrap search-field-wrap">
					<label><?php _e( 'Field', 'vg_sheet_editor' ); ?></label>
					<input type="hidden" name="meta_query[][source]" class="field-source" value="<?php echo esc_attr( $selected_values['source'] ); ?>">
					<select name="meta_query[][key]" data-placeholder="<?php _e( 'Select...', 'vg_sheet_editor' ); ?>" class="select2 wpse-advanced-filters-field-selector">
						<option value="" <?php selected( $selected_values['key'], '' ); ?>>- -</option>
						<?php
						$all_fields = $this->get_advanced_filters_fields( $current_post_type, $filters );
						if ( ! empty( $all_fields ) && is_array( $all_fields ) ) {
							$columns              = VGSE()->helpers->get_unfiltered_provider_columns( $current_post_type );
							$default_field_labels = array(
								'_edit_last'          => __( 'Last edit', 'vg_sheet_editor' ),
								'_wp_old_slug'        => __( 'Old slug', 'vg_sheet_editor' ),
								'post_author'         => __( 'Author', 'vg_sheet_editor' ),
								'post_date_gmt'       => __( 'Date (GMT)', 'vg_sheet_editor' ),
								'post_modified_gmt'   => __( 'Modified Date (GMT)', 'vg_sheet_editor' ),
								'comment_status'      => __( 'Comments', 'vg_sheet_editor' ),
								'ping_status'         => __( 'Ping status', 'vg_sheet_editor' ),
								'post_name'           => __( 'URL Slug', 'vg_sheet_editor' ),
								'post_parent'         => __( 'Page Parent', 'vg_sheet_editor' ),
								'post_mime_type'      => __( 'Mime type', 'vg_sheet_editor' ),
								'comment_count'       => __( 'Comment count', 'vg_sheet_editor' ),
								'edd_variable_prices' => __( 'EDD Variable Prices', 'vg_sheet_editor' ),
								'edd_download_files'  => __( 'EDD Download Files', 'vg_sheet_editor' ),
							);
							if ( ! empty( VGSE()->options['exclude_non_visible_columns_from_tools'] ) ) {
								$visible_columns = VGSE()->helpers->get_provider_columns( $current_post_type );
							}
							foreach ( $all_fields as $group_key => $group_fields ) {
								foreach ( $group_fields as $field_key ) {
									if ( isset( $columns[ $field_key ] ) && empty( $columns[ $field_key ]['allow_direct_search'] ) ) {
										continue;
									}
									if ( ! empty( $visible_columns ) && ! isset( $visible_columns[ $field_key ] ) ) {
										continue;
									}

									$field_label = '';
									if ( isset( $columns[ $field_key ] ) && isset( $columns[ $field_key ]['title'] ) ) {
										$field_label = $columns[ $field_key ]['title'];
									} elseif ( isset( $default_field_labels[ $field_key ] ) ) {
										$field_label = $default_field_labels[ $field_key ];
									} else {
										$field_label = VGSE()->helpers->convert_key_to_label( $field_key );
									}
									$label = ( $field_label && $field_label !== $field_key ) ? $field_label . " ($field_key)" : $field_key;

									echo '<option ' . selected( $selected_values['key'], $field_key, false ) . ' value="' . esc_attr( $field_key ) . '" data-source="' . esc_attr( $group_key ) . '" ';
									echo '>' . esc_html( $label ) . '</option>';
								}
							}
						}
						?>
					</select>

					<?php if ( is_admin() && VGSE()->helpers->user_can_manage_options() && empty( VGSE()->options['enable_simple_mode'] ) ) { ?>
						<br/><span class="search-tool-missing-column-tip"><small><?php printf( __( 'A field is missing? <a href="%s">Click here</a>', 'vg_sheet_editor' ), esc_url( add_query_arg( 'wpse_rescan_db_fields', $current_post_type ) ) ); ?></small></span>
					<?php } ?>
				</div>
				<div class="field-wrap search-operator-wrap">
					<label><?php _e( 'Operator', 'vg_sheet_editor' ); ?></label>
					<select name="meta_query[][compare]" data-placeholder="<?php _e( 'Select...', 'vg_sheet_editor' ); ?>" class=" wpse-advanced-filters-operator-selector">
						<?php $this->render_operator_options( $selected_values['compare'] ); ?>
					</select>
				</div>
				<div class="field-wrap search-value-wrap">
					<label><?php _e( 'Value', 'vg_sheet_editor' ); ?></label>
					<input name="meta_query[][value]" type="text" class=" wpse-advanced-filters-value-selector" value="<?php echo esc_attr( $selected_values['value'] ); ?>"/>
				</div>

				<div class="fields-wrap search-row-add-new">
					<a href="#" class="button new-advanced-filter"><?php _e( 'Add new', 'vg_sheet_editor' ); ?></a>
				</div>
				<div class="fields-wrap search-row-remove-wrap">
					<a href="#" class="button remove-advanced-filter"><?php _e( 'X', 'vg_sheet_editor' ); ?></a>
				</div>
			</div>
			</<?php echo esc_html( $wrapper ); ?>>
			<?php
		}

		function add_advanced_filters_fields( $current_post_type, $filters ) {
			?>

			<p class="wpse-advanced-filters-toggle"><label><input type="checkbox" class="advanced-filters-toggle"> <?php _e( 'Enable advanced filters', 'vg_sheet_editor' ); ?></label></p>
			<div class="advanced-filters"  style="display: none;">
				<?php if ( empty( VGSE()->options['enable_simple_mode'] ) ) { ?>
					<h3><?php _e( 'Advanced search', 'vg_sheet_editor' ); ?></h3>
				<?php } ?>
				<p class="advanced-filters-message"><?php _e( 'You can search by any field using operators. I.e. price > 100, image != (empty)', 'vg_sheet_editor' ); ?></p>
				<ul class="unstyled-list advanced-filters-list">
					<?php
					$this->_render_advanced_filter_row( $current_post_type, $filters );
					do_action( 'vg_sheet_editor/filters/after_advanced_fields', $current_post_type );
					?>
				</ul>

				<div class="fields-wrap" style="display: none;"><a href="#" class="button new-advanced-filter"><?php _e( 'Add new', 'vg_sheet_editor' ); ?></a></div>
				<hr>
				<ul class="unstyled-list bottom-advanced-filters">
					<?php
					do_action( 'vg_sheet_editor/filters/after_advanced_fields_section', $current_post_type );

					if ( empty( VGSE()->options['enable_simple_mode'] ) ) {
						?>
						<li class="exclude-keyword">
							<label><?php echo __( 'NOT Contains this keyword', 'vg_sheet_editor' ); ?>  <a href="#" data-wpse-tooltip="right" aria-label="<?php echo __( 'Enter a keyword to exclude posts, separate multiple keywords with a semicolon (;)', 'vg_sheet_editor' ); ?>">( ? )</a></label>
							<input type="text" name="keyword_exclude">
						</li>
						<li class="post--in">
							<label><?php _e( 'Find these IDs:', 'vg_sheet_editor' ); ?> <a href="#" data-wpse-tooltip="right" aria-label="<?php _e( 'Enter IDs separated by commas, spaces, new lines, or tabs. You can use ID ranges like 20-50 as a shortcut.', 'vg_sheet_editor' ); ?>">( ? )</a></label>
							<textarea name="post__in"></textarea>
						</li>
						<?php if ( VGSE()->helpers->get_current_provider()->is_post_type ) { ?>
							<li class="post-name--in">
								<label><?php _e( 'Find these URLs:', 'vg_sheet_editor' ); ?> <a href="#" data-wpse-tooltip="right" aria-label="<?php _e( 'Enter one URL per line', 'vg_sheet_editor' ); ?>">( ? )</a></label>
								<textarea name="post_name__in"></textarea>
							</li>
							<li class="date-range">
								<label><?php _e( 'Date range from', 'vg_sheet_editor' ); ?> <a href="#" data-wpse-tooltip="right" aria-label="<?php _e( 'Show items published between these dates' ); ?>">( ? )</a></label><input type="date" name="date_from" /><br/> <?php _e( 'to', 'vg_sheet_editor' ); ?><br/> <input type="date" name="date_to" />
							</li>
							<?php
						}
					}
					?>
				</ul>
			</div>
			<?php
		}

		function render_operator_options( $selected = '' ) {
			?>
			<option value="=" <?php selected( in_array( $selected, array( '', '=' ) ) ); ?>>=</option>
			<option value="!=" <?php selected( $selected, '!=' ); ?>>!=</option>
			<option value="<" <?php selected( $selected, '<' ); ?> ><</option>
			<option value="<=" <?php selected( $selected, '<=' ); ?> ><=</option>
			<option value=">"  <?php selected( $selected, '>' ); ?>>></option>
			<option value=">="  <?php selected( $selected, '>=' ); ?>>>=</option>
			<option value="OR"  <?php selected( $selected, 'OR' ); ?> data-custom-label="ANY"><?php _e( 'Any of these values (Enter multiple values separated by ;)', 'vg_sheet_editor' ); ?></option>
			<option value="LIKE"  <?php selected( $selected, 'LIKE' ); ?>><?php _e( 'CONTAINS', 'vg_sheet_editor' ); ?></option>
			<option value="NOT LIKE"  <?php selected( $selected, 'NOT LIKE' ); ?>><?php _e( 'NOT CONTAINS', 'vg_sheet_editor' ); ?></option>
			<option value="starts_with" <?php selected( $selected, 'starts_with' ); ?> ><?php _e( 'STARTS WITH', 'vg_sheet_editor' ); ?></option>
			<option value="ends_with"  <?php selected( $selected, 'ends_with' ); ?>><?php _e( 'ENDS WITH', 'vg_sheet_editor' ); ?></option>
			<option value="length_less"  <?php selected( $selected, 'length_less' ); ?>><?php _e( 'CHARACTER LENGTH <', 'vg_sheet_editor' ); ?></option>
			<option value="length_higher"  <?php selected( $selected, 'length_higher' ); ?>><?php _e( 'CHARACTER LENGTH >', 'vg_sheet_editor' ); ?></option>
			<option value="REGEXP"  <?php selected( $selected, 'REGEXP' ); ?>><?php _e( 'REGEXP', 'vg_sheet_editor' ); ?></option>
			<option data-value-field-type="number" data-value-type="date" value="last_hours"  <?php selected( $selected, 'last_hours' ); ?>><?php _e( 'In the last x hours', 'vg_sheet_editor' ); ?></option>
			<option data-value-field-type="number" data-value-type="date" value="last_days"  <?php selected( $selected, 'last_days' ); ?>><?php _e( 'In the last x days', 'vg_sheet_editor' ); ?></option>
			<option data-value-field-type="number" data-value-type="date" value="last_weeks"  <?php selected( $selected, 'last_weeks' ); ?>><?php _e( 'In the last x weeks', 'vg_sheet_editor' ); ?></option>
			<option data-value-field-type="number" data-value-type="date" value="last_months"  <?php selected( $selected, 'last_months' ); ?>><?php _e( 'In the last x years', 'vg_sheet_editor' ); ?></option>
			<?php
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if ( ! self::$instance ) {
				self::$instance = new WP_Sheet_Editor_Advanced_Filters();
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

	add_action( 'vg_sheet_editor/initialized', 'vgse_advanced_filters_init' );

	function vgse_advanced_filters_init() {
		return WP_Sheet_Editor_Advanced_Filters::get_instance();
	}
}
