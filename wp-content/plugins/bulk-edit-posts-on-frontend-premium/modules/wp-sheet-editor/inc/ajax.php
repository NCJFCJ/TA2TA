<?php defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_Sheet_Editor_Ajax' ) ) {

	class WP_Sheet_Editor_Ajax {

		private static $instance = false;

		private function __construct() {

		}

		/*
		 * Controller for loading posts to the spreadsheet
		 */

		function delete_row_ids() {

			$error_message = array( 'message' => __( 'You dont have enough permissions to do this action.', 'vg_sheet_editor' ) );
			if ( empty( $_REQUEST['post_type'] ) || empty( VGSE()->helpers->get_nonce_from_request() ) || empty( $_REQUEST['ids'] ) || ! VGSE()->helpers->verify_nonce_from_request() ) {
				wp_send_json_error( $error_message );
			}
			$post_type = VGSE()->helpers->sanitize_table_key( $_REQUEST['post_type'] );
			if ( ! VGSE()->helpers->user_can_edit_post_type( $post_type ) || ! VGSE()->helpers->user_can_delete_post_type( $post_type ) ) {
				wp_send_json_error( $error_message );
			}
			$row_ids = array_map( 'intval', $_REQUEST['ids'] );
			$row_ids = VGSE()->helpers->get_current_provider()->filter_rows_before_edit( $row_ids, $post_type );

			foreach ( $row_ids as $id ) {
				VGSE()->helpers->get_current_provider()->update_item_data(
					array(
						'ID'               => (int) $id,
						'post_status'      => 'delete',
						'wpse_status'      => 'delete',
						'comment_approved' => 'delete',
					)
				);
			}
			wp_send_json_success( array( 'message' => __( 'Rows deleted successfully', 'vg_sheet_editor' ) ) );
		}

		function get_taxonomy_terms() {

			$error_message = array( 'message' => __( 'You dont have enough permissions to do this action.', 'vg_sheet_editor' ) );
			if ( empty( $_REQUEST['post_type'] ) || empty( VGSE()->helpers->get_nonce_from_request() ) || ! VGSE()->helpers->verify_nonce_from_request() ) {
				wp_send_json_error( $error_message );
			}

			$post_type = VGSE()->helpers->sanitize_table_key( $_REQUEST['post_type'] );
			// If this is a WC attribute, use WC's sanitization function
			if ( class_exists( 'WooCommerce' ) && strpos( $_REQUEST['taxonomy_key'], 'pa_' ) === 0 ) {
				$taxonomy_key = wc_sanitize_taxonomy_name( $_REQUEST['taxonomy_key'] );
			} else {
				$taxonomy_key = VGSE()->helpers->sanitize_table_key( $_REQUEST['taxonomy_key'] );
			}
			if ( ! VGSE()->helpers->user_can_view_post_type( $post_type ) || ! taxonomy_exists( $taxonomy_key ) ) {
				wp_send_json_error( $error_message );
			}

			$source = ( ! empty( $_REQUEST['wpse_source'] ) ) ? sanitize_text_field( $_REQUEST['wpse_source'] ) : '';
			$out    = VGSE()->data_helpers->get_taxonomy_terms( $taxonomy_key, $source );

			if ( is_array( $out ) ) {
				$out = array_map( 'html_entity_decode', $out );

				$search_term = ( ! empty( $_REQUEST['search'] ) ) ? html_entity_decode( sanitize_text_field( $_REQUEST['search'] ) ) : '';
				if ( ! empty( $search_term ) ) {
					foreach ( $out as $index => $term ) {
						if ( stripos( $term, $search_term ) === false ) {
							unset( $out[ $index ] );
						}
					}
				}
				$out = array_values( $out );
			}
			wp_send_json_success( $out );
		}

		function load_rows() {

			if ( empty( $_REQUEST['post_type'] ) || empty( VGSE()->helpers->get_nonce_from_request() ) ) {
				wp_send_json_error( array( 'message' => __( 'Missing parameters.', 'vg_sheet_editor' ) ) );
			}
			if ( ! VGSE()->helpers->verify_nonce_from_request() || ! VGSE()->helpers->user_can_view_post_type( $_REQUEST['post_type'] ) ) {
				$message = array( 'message' => __( 'You dont have enough permissions to load rows.', 'vg_sheet_editor' ) );
				wp_send_json_error( $message );
			}

			$request_data = array(
				'nonce'                     => sanitize_text_field( VGSE()->helpers->get_nonce_from_request() ),
				'post_type'                 => VGSE()->helpers->sanitize_table_key( $_REQUEST['post_type'] ),
				'paged'                     => isset( $_REQUEST['paged'] ) ? intval( $_REQUEST['paged'] ) : 1,
				'posts_per_page'            => isset( $_REQUEST['posts_per_page'] ) ? intval( $_REQUEST['posts_per_page'] ) : 0,
				'wpse_reset_posts_per_page' => isset( $_REQUEST['wpse_reset_posts_per_page'] ) ? (int) $_REQUEST['wpse_reset_posts_per_page'] : 0,
				'wpse_source_suffix'        => isset( $_REQUEST['wpse_source_suffix'] ) ? sanitize_text_field( $_REQUEST['wpse_source_suffix'] ) : '',
				'wpse_source'               => isset( $_REQUEST['wpse_source'] ) ? sanitize_text_field( $_REQUEST['wpse_source'] ) : '',
				'filters'                   => vgse_filters_init()->get_raw_filters(),
			);
			// Reset the number of rows per page, we receive this parameter from the client when
			// the current rows per page > 300 and the request failed
			if ( ! empty( $request_data['wpse_reset_posts_per_page'] ) ) {
				VGSE()->update_option( 'be_posts_per_page', (int) $request_data['wpse_reset_posts_per_page'] );
			}

			$source_prefix               = ( ! empty( $request_data['wpse_source_suffix'] ) ) ? (string) $request_data['wpse_source_suffix'] : '';
			$request_data['wpse_source'] = 'load_rows' . $source_prefix;

			$rows = VGSE()->helpers->get_rows( $request_data );

			if ( is_wp_error( $rows ) ) {
				wp_send_json_error(
					wp_parse_args(
						array(
							'message' => $rows->get_error_message(),
						),
						$rows->get_error_data()
					)
				);
			}

			$rows['rows']    = array_values( $rows['rows'] );
			$rows['deleted'] = array_unique( VGSE()->deleted_rows_ids );
			wp_send_json_success( $rows );
		}

		/*
		 * Controller for saving posts changes
		 */

		function save_rows() {
			if ( empty( $_REQUEST['post_type'] ) || empty( VGSE()->helpers->get_nonce_from_request() ) || ! VGSE()->helpers->verify_sheet_permissions_from_request( 'edit' ) || ! VGSE()->helpers->verify_nonce_from_request() ) {
				wp_send_json_error( array( 'message' => __( 'You dont have enough permissions to save changes.', 'vg_sheet_editor' ) ) );
			}
			$params         = array(
				'nonce'               => sanitize_text_field( VGSE()->helpers->get_nonce_from_request() ),
				'post_type'           => VGSE()->helpers->sanitize_table_key( $_REQUEST['post_type'] ),
				'allow_to_create_new' => ! empty( $_REQUEST['allow_to_create_new'] ),
				'wpse_source'         => isset( $_REQUEST['wpse_source'] ) ? sanitize_text_field( $_REQUEST['wpse_source'] ) : null,
				'filters'             => vgse_filters_init()->get_raw_filters(),
			);
			$params['data'] = VGSE()->helpers->sanitize_data_for_db( $_REQUEST['data'], $params['post_type'] );

			$result = VGSE()->helpers->save_rows( $params );

			if ( is_wp_error( $result ) ) {
				wp_send_json_error(
					array(
						'message' => $result->get_error_message(),
					)
				);
			}

			// We use this flag to customize the user experience and hide some notifications for people that already learned how to use the sheet
			update_user_meta( get_current_user_id(), 'wpse_has_saved_sheet', 1 );
			wp_send_json_success(
				array(
					'message' => __( 'Changes saved successfully', 'vg_sheet_editor' ),
					'deleted' => array_unique( VGSE()->deleted_rows_ids ),
				)
			);
		}

		/*
		 * Controller for saving new post.
		 */

		function insert_individual_post() {
			if ( empty( $_REQUEST['post_type'] ) || empty( $_REQUEST['rows'] ) || empty( VGSE()->helpers->get_nonce_from_request() ) ) {
				wp_send_json_error( array( 'message' => __( 'Missing parameters.', 'vg_sheet_editor' ) ) );
			}
			if ( ! VGSE()->helpers->verify_nonce_from_request() || ! VGSE()->helpers->verify_sheet_permissions_from_request( 'edit' ) ) {
				wp_send_json_error( array( 'message' => __( 'You dont have enough permissions to create new rows.', 'vg_sheet_editor' ) ) );
			}
			$post_type            = VGSE()->helpers->sanitize_table_key( $_REQUEST['post_type'] );
			$rows                 = (int) $_REQUEST['rows'];
			$dont_return_new_rows = ! empty( $_REQUEST['dont_return_new_rows'] ) && $_REQUEST['dont_return_new_rows'] === 'yes';

			$result = VGSE()->helpers->create_placeholder_posts( $post_type, $rows, $dont_return_new_rows ? 'ids' : 'rows' );
			if ( $dont_return_new_rows ) {
				$result = array();
			}

			if ( is_wp_error( $result ) ) {
				wp_send_json_error(
					array(
						'message' => $result->get_error_message(),
					)
				);
			}
			wp_send_json_success(
				array(
					'message' => $result,
					'deleted' => array_unique( VGSE()->deleted_rows_ids ),
				)
			);
		}

		function list_posts_by_title() {
			global $wpdb;

			if ( empty( $_REQUEST['search_post_type'] ) || empty( VGSE()->helpers->get_nonce_from_request() ) ) {
				wp_send_json_error( array( 'message' => __( 'Missing parameters.', 'vg_sheet_editor' ) ) );
			}
			$post_type = VGSE()->helpers->sanitize_table_key( $_REQUEST['search_post_type'] );

			if ( ! VGSE()->helpers->verify_nonce_from_request() || ! VGSE()->helpers->user_can_view_post_type( $post_type ) ) {
				wp_send_json_error( array( 'message' => __( 'Request not allowed. Try again later.', 'vg_sheet_editor' ) ) );
			}

			$titles = $wpdb->get_col( $wpdb->prepare( "SELECT post_title FROM $wpdb->posts WHERE post_type = %s LIMIT 500", $post_type ) );

			wp_send_json_success( array( 'data' => $titles ) );
		}

		/**
		 * Find posts by name
		 */
		function find_post_by_name() {
			global $wpdb;

			if ( empty( $_REQUEST['post_type'] ) || empty( $_REQUEST['search'] ) || empty( VGSE()->helpers->get_nonce_from_request() ) ) {
				wp_send_json_error( array( 'message' => __( 'Missing parameters.', 'vg_sheet_editor' ) ) );
			}
			$post_type = VGSE()->helpers->sanitize_table_key( $_REQUEST['post_type'] );
			$search    = sanitize_text_field( wp_unslash( html_entity_decode( $_REQUEST['search'], ENT_QUOTES ) ) );

			if ( ! VGSE()->helpers->verify_nonce_from_request() || ! VGSE()->helpers->user_can_view_post_type( $post_type ) ) {
				wp_send_json_error( array( 'message' => __( 'Request not allowed. Try again later.', 'vg_sheet_editor' ) ) );
			}

			$where         = ' post_type = %s AND (post_title LIKE %s ';
			$prepared_data = array( $post_type, '%' . $wpdb->esc_like( $search ) . '%' );
			$join          = '';
			if ( $post_type === 'product' && class_exists( 'WooCommerce' ) ) {
				$where          .= '  OR lookup.sku = %s ';
				$prepared_data[] = $search;
				$join            = " LEFT JOIN {$wpdb->prefix}wc_product_meta_lookup lookup ON {$wpdb->posts}.ID = lookup.product_id ";
			}
			if ( is_numeric( $search ) ) {
				$where          .= '  OR ID = %d ';
				$prepared_data[] = (int) $search;
			}
			$where      .= ') ';
			$sql         = apply_filters( 'vg_sheet_editor/find_post_by_name_sql', $wpdb->prepare( "SELECT * FROM $wpdb->posts $join WHERE " . $where . ' LIMIT 10', $prepared_data ), $search, $post_type );
			$posts_found = $wpdb->get_results( $sql );

			if ( empty( $posts_found ) ) {
				wp_send_json_error( array( 'message' => __( 'No items found.', 'vg_sheet_editor' ) ) );
			}

			$out = array();
			foreach ( $posts_found as $post ) {
				$out[] = array(
					'id'    => $post->post_type . '--' . $post->ID,
					'text'  => $post->post_title . ' ( ID: ' . $post->ID . ', ' . $post->post_type . ' )',
					'title' => $post->post_title,
				);
			}
			wp_send_json_success( array( 'data' => $out ) );
		}

		/**
		 * Controller for saving individual field of post
		 */
		function save_single_post_data() {
			if ( empty( $_REQUEST['post_id'] ) || empty( $_REQUEST['key'] ) || empty( VGSE()->helpers->get_nonce_from_request() ) || empty( $_REQUEST['type'] ) || empty( $_REQUEST['post_type'] ) ) {
				wp_send_json_error( array( 'message' => __( 'Missing parameters.', 'vg_sheet_editor' ) ) );
			}
			if ( ! VGSE()->helpers->verify_nonce_from_request() ) {
				wp_send_json_error( array( 'message' => __( 'You dont have enough permissions to save changes.', 'vg_sheet_editor' ) ) );
			}
			$post_type = VGSE()->helpers->sanitize_table_key( $_REQUEST['post_type'] );
			if ( ! VGSE()->helpers->user_can_edit_post_type( $post_type ) ) {
				wp_send_json_error( array( 'message' => __( 'You dont have enough permissions to save changes.', 'vg_sheet_editor' ) ) );
			}
			$content = wp_kses_post( html_entity_decode( $_REQUEST['content'] ) );
			$id      = (int) $_REQUEST['post_id'];
			$key     = sanitize_text_field( $_REQUEST['key'] );
			$type    = sanitize_text_field( $_REQUEST['type'] );

			if ( VGSE()->options['be_disable_post_actions'] ) {
				$post_type = get_post_type( $id );
				VGSE()->helpers->remove_all_post_actions( $post_type );
			}

			do_action( 'vg_sheet_editor/save_single_post_data/before', $id, $content, $key, $type );
			$result = VGSE()->data_helpers->save_single_post_data( $id, $content, $key, $type );

			do_action( 'vg_sheet_editor/save_single_post_data/after', $result, $id, $content, $key, $type );
			if ( is_wp_error( $result ) ) {

				$errors = $result->get_error_messages();
				wp_send_json_success( array( 'message' => sprintf( __( 'Error: %s', 'vg_sheet_editor' ), implode( ', ', $errors ) ) ) );
			} else {
				VGSE()->helpers->increase_counter( 'editions' );
				VGSE()->helpers->increase_counter( 'processed' );

				$title = VGSE()->data_helpers->get_post_data( 'post_title', $id );
				wp_send_json_success( array( 'message' => sprintf( __( 'Saved: %s', 'vg_sheet_editor' ), $title ) ) );
			}
		}

		function search_users_select2() {
			$_REQUEST['include_ids'] = 'yes';
			return $this->search_users();
		}
		/**
		 * Search taxonomy term
		 * @global obj $wpdb
		 */
		function search_users() {
			global $wpdb;
			if ( empty( $_REQUEST['search'] ) || empty( VGSE()->helpers->get_nonce_from_request() ) || empty( $_REQUEST['post_type'] ) ) {
				wp_send_json_error( array( 'message' => __( 'Missing parameters.', 'vg_sheet_editor' ) ) );
			}

			if ( ! VGSE()->helpers->verify_nonce_from_request() || ! VGSE()->helpers->user_can_view_post_type( $_REQUEST['post_type'] ) ) {
				wp_send_json_error( array( 'message' => __( 'You dont have enough permissions to search taxonomy terms.', 'vg_sheet_editor' ) ) );
			}
			$search      = sanitize_text_field( $_REQUEST['search'] );
			$include_ids = ! empty( $_REQUEST['include_ids'] );

			if ( $include_ids ) {
				$rows = $wpdb->get_results( $wpdb->prepare( "SELECT ID,user_login FROM $wpdb->users WHERE user_email LIKE %s OR user_nicename LIKE %s OR user_login LIKE %s OR display_name LIKE %s LIMIT 5", '%' . $wpdb->esc_like( $search ) . '%', '%' . $wpdb->esc_like( $search ) . '%', '%' . $wpdb->esc_like( $search ) . '%', '%' . $wpdb->esc_like( $search ) . '%' ) );
				$out  = wp_list_pluck( $rows, 'user_login', 'ID' );
			} else {
				$out = $wpdb->get_col( $wpdb->prepare( "SELECT user_login FROM $wpdb->users WHERE user_email LIKE %s OR user_nicename LIKE %s OR user_login LIKE %s OR display_name LIKE %s LIMIT 5", '%' . $wpdb->esc_like( $search ) . '%', '%' . $wpdb->esc_like( $search ) . '%', '%' . $wpdb->esc_like( $search ) . '%', '%' . $wpdb->esc_like( $search ) . '%' ) );
			}
			wp_send_json_success( array( 'data' => $out ) );
		}

		function search_taxonomy_terms() {
			global $wpdb;
			if ( empty( $_REQUEST['search'] ) || empty( VGSE()->helpers->get_nonce_from_request() ) || empty( $_REQUEST['post_type'] ) ) {
				wp_send_json_error( array( 'message' => __( 'Missing parameters.', 'vg_sheet_editor' ) ) );
			}

			$post_type        = VGSE()->helpers->sanitize_table_key( $_REQUEST['post_type'] );
			$is_global_search = ! empty( $_REQUEST['global_search'] );

			// Note. The global search is allowed for administrators only
			if ( ! VGSE()->helpers->verify_nonce_from_request() || ( ! $is_global_search && ! VGSE()->helpers->user_can_view_post_type( $post_type ) ) || ( $is_global_search && ! VGSE()->helpers->user_can_manage_options() ) ) {
				wp_send_json_error( array( 'message' => __( 'You dont have enough permissions to search taxonomy terms.', 'vg_sheet_editor' ) ) );
			}

			$search        = wp_unslash( sanitize_text_field( $_REQUEST['search'] ) );
			$output_format = ( isset( $_REQUEST['output_format'] ) ) ? sanitize_text_field( $_REQUEST['output_format'] ) : '';

			if ( $is_global_search ) {
				$taxonomies = get_taxonomies(
					array(
						'show_ui'      => true,
						'hierarchical' => true,
					),
					'names'
				);
			} else {
				$taxonomies = VGSE()->helpers->get_post_type_taxonomies_single_data( $post_type, 'name' );
			}

			if ( ! empty( $_REQUEST['taxonomies'] ) ) {
				$taxonomies = is_string( $_REQUEST['taxonomies'] ) ? explode( ',', sanitize_text_field( $_REQUEST['taxonomies'] ) ) : array_map( 'sanitize_text_field', $_REQUEST['taxonomies'] );
			}

			if ( empty( $taxonomies ) ) {
				wp_send_json_error( array( 'message' => __( 'No taxonomies found.', 'vg_sheet_editor' ) ) );
			}

			$taxonomies_in_query_placeholders = implode( ', ', array_fill( 0, count( $taxonomies ), '%s' ) );
			$sql                              = $wpdb->prepare( "SELECT term.slug id,term.name text,tax.taxonomy taxonomy, term.slug slug FROM $wpdb->term_taxonomy as tax JOIN $wpdb->terms as term ON term.term_id = tax.term_id WHERE tax.taxonomy IN ($taxonomies_in_query_placeholders) AND term.name LIKE %s ", array_merge( $taxonomies, array( '%' . $wpdb->esc_like( $search ) . '%' ) ) );
			$results                          = $wpdb->get_results( $sql, ARRAY_A );

			if ( ! $results || is_wp_error( $results ) ) {
				$results = array();
			}

			if ( empty( $output_format ) ) {
				$output_format = '%taxonomy%--%slug%';
			} else {
				$output_format = sanitize_text_field( $output_format );
			}
			$taxonomies_labels = array();
			$out               = array();
			foreach ( $results as $result ) {

				if ( ! isset( $taxonomies_labels[ $result['taxonomy'] ] ) ) {
					$tmp_tax                                  = get_taxonomy( $result['taxonomy'] );
					$label                                    = ( $tmp_tax->label === __( 'Tags' ) && $tmp_tax->name !== 'post_tag' ) ? $tmp_tax->name : $tmp_tax->label;
					$taxonomies_labels[ $result['taxonomy'] ] = $label;
				}

				$output_key = strtr(
					$output_format,
					array(
						'%name%'     => $result['text'],
						'%taxonomy%' => $result['taxonomy'],
						'%slug%'     => $result['id'],
					)
				);
				$out[]      = array(
					'id'   => $output_key,
					'text' => $result['text'] . ' ( ' . $taxonomies_labels[ $result['taxonomy'] ] . ', ' . urldecode( $result['slug'] ) . ' )',
				);
			}
			wp_send_json_success( array( 'data' => $out ) );
		}

		/**
		 * Enable the spreadsheet editor on some post types
		 */
		function save_post_types_setting() {
			if ( empty( VGSE()->helpers->get_nonce_from_request() ) || empty( $_REQUEST['post_types'] ) || empty( $_REQUEST['append'] ) ) {
				wp_send_json_error( array( 'message' => __( 'Missing parameters.', 'vg_sheet_editor' ) ) );
			}
			if ( ! VGSE()->helpers->verify_nonce_from_request() || ! VGSE()->helpers->user_can_manage_options() ) {
				wp_send_json_error( array( 'message' => __( 'You dont have enough permissions to perform this action.', 'vg_sheet_editor' ) ) );
			}
			$post_types = array_map( array( VGSE()->helpers, 'sanitize_table_key' ), $_REQUEST['post_types'] );
			$append     = sanitize_text_field( $_REQUEST['append'] );

			$settings = get_option( VGSE()->options_key, array() );
			if ( empty( $settings['be_post_types'] ) ) {
				$settings['be_post_types'] = array();
			}

			if ( $append === 'yes' ) {
				$new_post_types = array_unique( array_merge( $settings['be_post_types'], $post_types ) );
			} else {
				$new_post_types = $post_types;
			}
			$settings['be_post_types'] = $new_post_types;

			update_option( VGSE()->options_key, $settings, false );

			do_action( 'vg_sheet_editor/quick_setup/post_types_saved/after', $new_post_types );

			wp_send_json_success();
		}

		function save_gutenberg_content() {
			$_REQUEST['content']   = wp_kses_post( $_REQUEST['data'] );
			$_REQUEST['post_id']   = (int) $_REQUEST['postId'];
			$_REQUEST['post_type'] = VGSE()->helpers->sanitize_table_key( $_REQUEST['postType'] );
			$_REQUEST['type']      = 'post_data';
			$_REQUEST['key']       = 'post_content';
			$this->save_single_post_data();
		}

		function get_registered_settings() {

			$registered_settings_sections = WPSE_Options_Page_Obj()->getSections();
			$registered_settings          = array();
			foreach ( $registered_settings_sections as $section ) {
				foreach ( $section['fields'] as $field ) {
					$registered_settings[ $field['id'] ] = $field;
				}
			}
			return $registered_settings;
		}

		function _sanitize_general_options( $new_settings ) {
			$registered_settings = $this->get_registered_settings();

			foreach ( $new_settings as $key => $value ) {
				// If this is not a registered setting, delete it
				if ( ! isset( $registered_settings[ $key ] ) ) {
					unset( $registered_settings[ $key ] );
				}
				// Empty values don't need sanitization
				if ( empty( $value ) ) {
					continue;
				}
				if ( ! isset( $registered_settings[ $key ] ) ) {
					$new_settings[ $key ] = sanitize_text_field( wp_unslash( $value ) );
					continue;
				}
				$args = $registered_settings[ $key ];

				if ( $args['type'] === 'text' && ! empty( $args['validate'] ) && $args['validate'] === 'numeric' ) {
					$new_settings[ $key ] = (int) $value;
				} elseif ( $args['type'] === 'switch' ) {
					$new_settings[ $key ] = (bool) $value;
				} elseif ( $args['type'] === 'textarea' ) {
					$new_settings[ $key ] = wp_strip_all_tags( $value );
				} elseif ( $args['type'] === 'editor' ) {
					$new_settings[ $key ] = wp_kses_post( $value );
				} elseif ( $args['type'] === 'media' && ! empty( $args['url'] ) ) {
					$new_settings[ $key ] = esc_url_raw( $value );
				} elseif ( $args['type'] === 'media' && empty( $args['url'] ) ) {
					$new_settings[ $key ] = intval( $value );
				} elseif ( $args['type'] === 'new_select' && ! empty( $args['multi'] ) ) {
					$new_settings[ $key ] = array_map( 'sanitize_text_field', $value );
				} else {
					$new_settings[ $key ] = sanitize_text_field( wp_unslash( $value ) );
				}
			}
			return $new_settings;
		}

		function _import_settings( $import_settings ) {
			$exportable_keys = VGSE()->get_exportable_settings_keys();
			foreach ( $import_settings as $setting_key => $setting_value ) {
				$found_in_equal = in_array( $setting_key, $exportable_keys['equal'], true );
				$found_in_like  = false;
				foreach ( $exportable_keys['like'] as $like_key ) {
					if ( strpos( $setting_key, $like_key ) !== false ) {
						$found_in_like = true;
						break;
					}
				}

				// Only process the keys found in our list of exportable keys
				if ( ! $found_in_equal && ! $found_in_like ) {
					continue;
				}
				// Sanitize every option
				if ( $setting_key === 'vg_sheet_editor' ) {
					$setting_value = $this->_sanitize_general_options( $setting_value );
				} elseif ( in_array( $setting_key, array( 'vgse_welcome_redirect', 'vgse_hide_extensions_popup', 'vgse_dismiss_review_tip', 'vgse_disable_quick_setup', 'vgse_post_type_setup_done' ), true ) ) {
					$setting_value = (int) $setting_value;
				} elseif ( in_array( $setting_key, array( 'vgse_column_groups', 'vgse_saved_exports', 'vgse_removed_columns', 'vg_sheet_editor_custom_columns', 'vgse_favorite_search_fields', 'vg_sheet_editor_custom_post_types', 'vgse_saved_searches' ), true ) || $found_in_like ) {
					$setting_value = VGSE()->helpers->safe_text_only( $setting_value );
				} elseif ( $setting_key === 'vgse_columns_manager' && function_exists( 'vgse_columns_manager_init' ) ) {
					$setting_value = vgse_columns_manager_init()->sanitize_column_settings( $setting_value );
				} else {
					continue;
				}
				// We only save the keys found in our list of exportable keys (Line 415),
				// But we also have this preg_match to be extra safe and never save any option unrelated to our plugin
				if ( preg_match( '/(vgse_|vg_sheet_editor)/', $setting_key ) ) {
					update_option( $setting_key, $setting_value );
				}
			}
			// Disable columns that weren't manually enabled so the columns match after the import
			if ( isset( $import_settings['vgse_columns_visibility'] ) ) {
				VGSE()->update_option( 'dont_auto_enable_new_fields', 1 );
			}
		}

		function set_settings() {
			if ( empty( VGSE()->helpers->get_nonce_from_request() ) || empty( $_REQUEST['settings'] ) || ! VGSE()->helpers->verify_nonce_from_request() || ! VGSE()->helpers->user_can_manage_options() ) {
				wp_send_json_error();
			}

			// if this is a settings import
			if ( ! empty( $_REQUEST['wpse_import_settings'] ) ) {
				$import_settings = json_decode( html_entity_decode( wp_unslash( $_REQUEST['wpse_import_settings'] ) ), true );
				if ( is_array( $import_settings ) ) {
					// All the data that will be imported is sanitized inside _import_settings()
					$this->_import_settings( $import_settings );
					wp_send_json_success();
				} else {
					wp_send_json_error();
				}
			} else {
				// If this is a regular save process
				$new_settings = $this->_sanitize_general_options( $_REQUEST['settings'] );
				if ( ! empty( $new_settings ) ) {
					$options = get_option( VGSE()->options_key );
					if ( empty( $options ) || ! is_array( $options ) ) {
						$options = array();
					}
					$options = wp_parse_args( $new_settings, $options );
					update_option( VGSE()->options_key, $options, false );
				}
				wp_send_json_success();
			}
		}

		function dismiss_review_tip() {
			if ( empty( VGSE()->helpers->get_nonce_from_request() ) || ! VGSE()->helpers->verify_nonce_from_request() ) {
				wp_send_json_error();
			}
			update_option( 'vgse_dismiss_review_tip', 1 );

			wp_send_json_success();
		}

		function notice_dismiss() {
			if ( ! VGSE()->helpers->user_can_manage_options() ) {
				wp_send_json_error();
			}
			if ( empty( VGSE()->helpers->get_nonce_from_request() ) || ! VGSE()->helpers->verify_nonce_from_request() ) {
				wp_send_json_error();
			}
			$key = sanitize_text_field( $_REQUEST['key'] );
			// Only allow to dismiss notices with keys starting with wpse_hide_
			if ( strpos( $key, 'wpse_hide_' ) !== 0 ) {
				wp_send_json_error();
			}
			update_option( $key, 1 );
			wp_send_json_success();
		}

		function init() {

			// Ajax actions
			add_action( 'wp_ajax_vgse_delete_row_ids', array( $this, 'delete_row_ids' ) );
			add_action( 'wp_ajax_vgse_dismiss_review_tip', array( $this, 'dismiss_review_tip' ) );
			add_action( 'wp_ajax_vgse_notice_dismiss', array( $this, 'notice_dismiss' ) );
			add_action( 'wp_ajax_vgse_get_taxonomy_terms', array( $this, 'get_taxonomy_terms' ) );
			add_action( 'wp_ajax_vgse_load_data', array( $this, 'load_rows' ) );
			add_action( 'wp_ajax_vgse_save_gutenberg_content', array( $this, 'save_gutenberg_content' ) );
			add_action( 'wp_ajax_vgse_save_data', array( $this, 'save_rows' ) );
			add_action( 'wp_ajax_vgse_find_post_by_name', array( $this, 'find_post_by_name' ) );
			add_action( 'wp_ajax_vgse_list_post_titles', array( $this, 'list_posts_by_title' ) );
			add_action( 'wp_ajax_vgse_save_individual_post', array( $this, 'save_single_post_data' ) );
			add_action( 'wp_ajax_vgse_insert_individual_post', array( $this, 'insert_individual_post' ) );
			add_action( 'wp_ajax_vgse_search_taxonomy_terms', array( $this, 'search_taxonomy_terms' ) );
			add_action( 'wp_ajax_vgse_find_users_by_keyword', array( $this, 'search_users' ) );
			add_action( 'wp_ajax_vgse_find_users_by_keyword_for_select2', array( $this, 'search_users_select2' ) );
			add_action( 'wp_ajax_vgse_save_post_types_setting', array( $this, 'save_post_types_setting' ) );
			add_action( 'wp_ajax_vgse_set_settings', array( $this, 'set_settings' ) );
		}

		/**
		 * Creates or returns an instance of this class.
		 *
		 * @return  Foo A single instance of this class.
		 */
		static function get_instance() {
			if ( ! self::$instance ) {
				self::$instance = new WP_Sheet_Editor_Ajax();
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

if ( ! function_exists( 'WP_Sheet_Editor_Ajax_Obj' ) ) {

	function WP_Sheet_Editor_Ajax_Obj() {
		return WP_Sheet_Editor_Ajax::get_instance();
	}
}
