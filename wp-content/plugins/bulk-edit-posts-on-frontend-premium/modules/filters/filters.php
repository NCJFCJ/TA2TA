<?php defined( 'ABSPATH' ) || exit;
if ( ! class_exists( 'WP_Sheet_Editor_Filters' ) ) {

	/**
	 * Filter rows in the spreadsheet editor.
	 */
	class WP_Sheet_Editor_Filters {

		private static $instance = false;
		var $plugin_url          = null;
		var $plugin_dir          = null;

		private function __construct() {

		}

		function init() {

			$this->plugin_url = plugins_url( '/', __FILE__ );
			$this->plugin_dir = __DIR__;

			add_action( 'vg_sheet_editor/editor/before_init', array( $this, 'register_toolbar_locate' ), 80 );
			add_action( 'vg_sheet_editor/editor/before_init', array( $this, 'register_toolbar_search' ), 8 );
			add_action( 'vg_sheet_editor/after_enqueue_assets', array( $this, 'register_assets' ) );
			add_filter( 'vg_sheet_editor/load_rows/wp_query_args', array( $this, 'filter_posts' ), 10, 2 );
			add_action( 'vg_sheet_editor/load_rows/after_processing', array( $this, 'save_session_filters' ), 10, 4 );

			add_filter( 'vg_sheet_editor/handsontable/custom_args', array( $this, 'enable_cell_locator_js' ) );
			add_filter( 'posts_clauses', array( $this, 'search_by_keyword' ), 10, 2 );
			add_filter( 'vg_sheet_editor/js_data', array( $this, 'set_initial_filters' ), 10, 2 );
			add_filter( 'vg_sheet_editor/load_rows/wp_query_args', array( $this, 'add_session_query_vars_from_session_id' ), 10, 2 );
			add_action( 'vg_sheet_editor/editor_page/after_editor_page', array( $this, 'init_session_parameters_from_url' ) );
		}

		function init_session_parameters_from_url( $post_type ) {

			$filters = array_filter(
				array(
					'wpse_session_query' => ! empty( $_GET['wpse_session_query'] ) ? sanitize_text_field( $_GET['wpse_session_query'] ) : '',
					'wpse_auto_export'   => ! empty( $_GET['wpse_auto_export'] ) ? 1 : 0,
				)
			);
			?>
			<script>
				jQuery(document).ready(function () {
			<?php if ( ! empty( $filters ) ) { ?>
						jQuery('body').data('be-filters', <?php echo json_encode( $filters ); ?>);
			<?php } ?>


					// Start auto export if the URL contains wpse_auto_export=1
					jQuery('body').on('vgSheetEditor:afterRowsInsert', function (event, data) {
						console.log('data', data);
						if (data.length && window.location.href.indexOf('wpse_auto_export=1') > -1) {
							jQuery('[data-remodal-target="export-csv-modal"]').click();
							jQuery('.export-csv-modal .export-columns option').prop('selected', true).trigger('change');
							jQuery('.export-csv-modal .use-search-query-container input').prop('checked', true);
							jQuery('.export-csv-modal .vgse-trigger-export').click();
						}
					});
				});
			</script>
			<?php
		}

		function add_session_query_vars_from_session_id( $wp_query, $settings ) {
			$filters = self::get_instance()->get_raw_filters( $settings );
			if ( empty( $filters['wpse_session_query'] ) ) {
				return $wp_query;
			}
			$custom_wp_query_json = get_transient( $filters['wpse_session_query'] );
			if ( empty( $custom_wp_query_json ) ) {
				return $wp_query;
			}

			$custom_wp_query = json_decode( $custom_wp_query_json, true );
			if ( empty( $custom_wp_query ) ) {
				return $wp_query;
			}

			// We will use the pagination with the sheet settings and ignore
			//  the pagination parameters from the saved query
			// We will use the author restrictions from the sheet settings and ignore
			//  the author parameters from the saved query for security reasons
			$fields_to_remove = array( 'posts_per_page', 'paged', 'author', 'fields', 'posts_per_archive_page', 'nopaging' );
			foreach ( $fields_to_remove as $field_to_remove ) {
				if ( isset( $custom_wp_query[ $field_to_remove ] ) ) {
					unset( $custom_wp_query[ $field_to_remove ] );
				}
			}
			$wp_query = wp_parse_args( $custom_wp_query, $wp_query );

			return $wp_query;
		}

		/**
		 * Save session filters only if the loading of rows is successful,
		 * otherwise clear the saved filters for the post type to prevent errors
		 * where the spreadsheet is stuck doing expensive queries on every page reload
		 *
		 * @global object $wpdb
		 * @param array $rows
		 * @param array $wp_query_args
		 * @param array $spreadsheet_columns
		 * @param array $data
		 */
		function save_session_filters( $rows, $wp_query_args, $spreadsheet_columns, $data ) {
			global $wpdb;
			// Save session filters
			if ( ! empty( $data['wpse_source'] ) && $data['wpse_source'] === 'load_rows' ) {
				$sheet_sessions_key = $wpdb->prefix . 'wpse_sheet_sessions';
				$provider           = VGSE()->helpers->get_provider_from_query_string();
				$sheet_sessions     = get_user_meta( get_current_user_id(), $sheet_sessions_key, true );
				$filters            = $this->get_raw_filters( $data );
				if ( empty( $sheet_sessions ) || ! is_array( $sheet_sessions ) ) {
					$sheet_sessions = array();
				}
				if ( empty( $rows ) ) {
					$sheet_sessions[ $provider ] = array();
				} else {
					$sheet_sessions[ $provider ] = $filters;
				}
				update_user_meta( get_current_user_id(), $sheet_sessions_key, $sheet_sessions );
			}
		}

		function search_by_keyword( $clauses, $wp_query ) {
			if ( ! empty( $wp_query->query['wpse_contains_keyword'] ) ) {
				$clauses = $this->add_search_by_keyword_clause( $clauses, $wp_query->query['wpse_contains_keyword'], 'LIKE' );
			}
			return $clauses;
		}

		function add_search_by_keyword_clause( $clauses, $raw_keywords, $operator, $internal_join = 'OR' ) {
			global $wpdb;

			if ( ! in_array( $internal_join, array( 'AND', 'OR' ), true ) ) {
				$internal_join = 'OR';
			}

			$checks          = array();
			$search_columns  = array( 'post_title', 'post_content', 'post_excerpt' );
			$phrases         = array_map( 'trim', explode( ';', $raw_keywords ) );
			$prepared_values = array();
			$phrase_checks   = array();
			foreach ( $phrases as $phrase ) {
				$words = explode( ' ', $phrase );
				if ( empty( $words ) ) {
					continue;
				}

				foreach ( $words as $word ) {
					$word_checks = array();
					foreach ( $search_columns as $search_column ) {
						$word_checks[]   = $wpdb->posts . '.%i ' . $operator . ' %s';
						$prepared_values = array_merge( $prepared_values, array( $search_column, '%' . $wpdb->esc_like( $word ) . '%' ) );
					}
					if ( is_numeric( $phrase ) ) {
						$word_checks[]     = $wpdb->posts . '.%i = %d';
						$prepared_values[] = 'ID';
						$prepared_values[] = intval( $phrase );
					}
					$phrase_checks[] = '( ' . implode( " $internal_join ", $word_checks ) . ' )';
				}
				$all_checks = implode( ' AND ', $phrase_checks );
				$checks     = apply_filters( 'vg_sheet_editor/filters/search_by_keyword_clauses/keyword_check', $all_checks, $phrase, $clauses, $raw_keywords, $operator, $internal_join );
			}
			$checks            = $wpdb->prepare( $checks, $prepared_values );
			$clauses['where'] .= ' AND ( ( ' . $checks . ' ) ) ';
			return apply_filters( 'vg_sheet_editor/filteres/search_by_keyword_clauses', $clauses, $raw_keywords, $operator, $internal_join );
		}

		function enable_cell_locator_js( $args ) {
			$args['search'] = true;
			return $args;
		}

		/**
		 * Register frontend assets
		 */
		function register_assets() {
			wp_enqueue_script( 'filters_js', $this->plugin_url . 'assets/js/init.js', array(), VGSE()->version, false );
		}

		/**
		 * Register toolbar items
		 */
		function register_toolbar_locate( $editor ) {

			$post_types = $editor->args['enabled_post_types'];

			foreach ( $post_types as $post_type ) {
				$editor->args['toolbars']->register_item(
					'cell_locator',
					array(
						'type'         => 'html',
						'help_tooltip' => __( 'To search among all posts use the Search tool. Use this to locate and highlight one value in the loaded rows in the spreadsheet. I.e. highlight a SKU or email or title.', 'vg_sheet_editor' ),
						'content'      => '<input type="search" id="cell-locator-input" placeholder="' . __( 'Locate cell', 'vg_sheet_editor' ) . '"/>',
						'label'        => __( 'Locate cell.', 'vg_sheet_editor' ),
					),
					$post_type
				);
				$editor->args['toolbars']->register_item(
					'column_locator',
					array(
						'type'         => 'html',
						'help_tooltip' => __( 'Enter a word and we will find the first matching column. You will avoid scrolling through dozens of columns to find the one you need.', 'vg_sheet_editor' ),
						'content'      => '<input type="search" id="column-locator-input" placeholder="' . __( 'Locate column', 'vg_sheet_editor' ) . '"/>',
						'label'        => __( 'Locate column', 'vg_sheet_editor' ),
					),
					$post_type
				);
			}
		}

		function register_toolbar_search( $editor ) {

			$post_types = $editor->args['enabled_post_types'];
			foreach ( $post_types as $post_type ) {
				$editor->args['toolbars']->register_item(
					'run_filters',
					array(
						'type'                  => 'button',
						'content'               => __( 'Search', 'vg_sheet_editor' ),
						'icon'                  => 'fa fa-search',
						'extra_html_attributes' => 'data-remodal-target="modal-filters"',
						'footer_callback'       => array( $this, 'render_filters_form' ),
					),
					$post_type
				);

				$editor->args['toolbars']->register_item(
					'quick_search',
					array(
						'type'              => 'html', // html | switch | button
						'content'           => __( 'Quick search', 'vg_sheet_editor' ) . '<input type="search" name="keyword" class="wpse-quick-search" placeholder="' . __( 'Enter a keyword', 'vg_sheet_editor' ) . '"/><button type="button" class="wpse-start-quick-search">' . __( 'Search', 'vg_sheet_editor' ) . '</button>',
						'allow_in_frontend' => true,
						'parent'            => 'run_filters',
					),
					$post_type
				);
			}
		}

		function _get_allowed_operators() {
			return array(
				'=',
				'!=',
				'<',
				'<=',
				'>',
				'>=',
				'OR',
				'LIKE',
				'NOT LIKE',
				'starts_with',
				'ends_with',
				'length_less',
				'length_higher',
				'REGEXP',
				'last_hours',
				'last_days',
				'last_weeks',
				'last_months',
			);
		}

		function _sanitize_filters( $filters ) {
			$out = array();
			if ( empty( $filters ) || ! is_array( $filters ) ) {
				return $out;
			}

			$post_type = VGSE()->helpers->get_provider_from_query_string();

			// Cache variable that will hold the unfiltered columns that we get and use below
			$columns = null;

			foreach ( $filters as $filter_key => $filter ) {
				if ( empty( $filter ) || empty( $filter_key ) ) {
					continue;
				}
				if ( strpos( $filter_key, '{' ) !== false ) {
					continue;
				}
				if ( is_array( $filter ) ) {
					$filter = array_filter( $filter );
				}
				if ( in_array( $filter_key, array( 'search_name', 'date_from', 'date_to' ) ) ) {
					$out[ $filter_key ] = sanitize_text_field( $filter );
				} elseif ( $filter_key === 'apply_to' ) {
					$out[ $filter_key ] = array_map( 'sanitize_text_field', array_filter( $filter ) );
				} elseif ( $filter_key === 'post__in' ) {
					$out[ $filter_key ] = sanitize_textarea_field( $filter );
				} elseif ( $filter_key === 'keyword' ) {
					$out[ $filter_key ] = wp_kses_post( $filter );
				} elseif ( $filter_key === 'keyword_exclude' ) {
					$out[ $filter_key ] = wp_kses_post( $filter );
				} elseif ( $filter_key === 'post_parent' ) {
					$out[ $filter_key ] = (int) str_replace( 'page--', '', $filter );
				} elseif ( $filter_key === 'post_status' ) {
					$out[ $filter_key ] = array_map( 'sanitize_key', array_filter( $filter ) );
				} elseif ( $filter_key === 'post_author' && WP_Sheet_Editor_Helpers::current_user_can( 'edit_others_posts' ) ) {
					$out[ $filter_key ] = array_map( 'intval', array_filter( $filter ) );
				} elseif ( $filter_key === 'meta_query' && ! empty( $filter ) ) {
					$allowed_operators  = $this->_get_allowed_operators();
					$out[ $filter_key ] = array();
					foreach ( $filter as $meta_query ) {
						if ( ! isset( $meta_query['compare'] ) ) {
							continue;
						}
						// Skip search param if the operator is not found in the allowed operators
						$operator = htmlspecialchars_decode( $meta_query['compare'] );
						if ( ! in_array( $operator, $allowed_operators, true ) ) {
							continue;
						}
						if ( isset( $meta_query['key'] ) && is_array( $meta_query['key'] ) ) {
							$meta_query['key'] = array_filter( $meta_query['key'] );
						}
						if ( empty( $meta_query['key'] ) || empty( $meta_query['compare'] ) || empty( $meta_query['source'] ) ) {
							continue;
						}
						if ( ! isset( $meta_query['value'] ) ) {
							$meta_query['value'] = '';
						}

						if ( is_null( $columns ) ) {
							$columns = VGSE()->helpers->get_unfiltered_provider_columns( $post_type );
						}
						$is_date_filter = isset( $columns[ $meta_query['key'] ] ) && $columns[ $meta_query['key'] ]['value_type'] === 'date';
						if ( $is_date_filter ) {
							$date_format_for_db = isset( $columns[ $meta_query['key'] ]['formatted']['customDatabaseFormat'] ) ? $columns[ $meta_query['key'] ]['formatted']['customDatabaseFormat'] : $columns[ $meta_query['key'] ]['formatted']['dateFormatPhp'];
						}
						if ( empty( $date_format_for_db ) ) {
							$date_format_for_db = 'Y-m-d H:i:s';
						}

						// If this is a "contains date" filter, remove the 0:00:00 time because they want contains=date without time
						if ( $is_date_filter && $operator === 'LIKE' && strpos( $meta_query['value'], ' 00:00:00' ) !== false ) {
							$meta_query['value'] = str_replace( ' 00:00:00', '', $meta_query['value'] );
						}
						if ( in_array( $operator, array( 'last_hours', 'last_days', 'last_weeks', 'last_months' ), true ) ) {
							$time_unit             = str_replace( 'last_', '', $operator );
							$operator              = '>=';
							$meta_query['compare'] = $operator;
							$meta_query['value']   = date( $date_format_for_db, strtotime( '-' . (int) $meta_query['value'] . ' ' . $time_unit . ' 00:00:00' ) );
						}

						$meta_filter = array(
							'key'     => sanitize_text_field( $meta_query['key'] ),
							'compare' => $operator,
							'source'  => sanitize_text_field( $meta_query['source'] ),
							'value'   => html_entity_decode( wp_kses_post( $meta_query['value'] ) ),
						);
						// Create a unique id so we can automatically remove duplicate filters
						$meta_filter_unique_id                        = md5( wp_json_encode( $meta_filter ) );
						$out[ $filter_key ][ $meta_filter_unique_id ] = $meta_filter;
					}
					$out[ $filter_key ] = array_values( $out[ $filter_key ] );
				} else {
					$out[ $filter_key ] = sanitize_text_field( $filter );
				}
			}
			return apply_filters( 'vg_sheet_editor/filters/sanitize_request_filters', array_filter( $out ), $filters );
		}

		/**
		 * Return sanitized filters
		 *
		 * @param  string|array $raw_filters This accepts a JSON or urlencoded string, or array with key values.
		 * @return array
		 */
		function _get_raw_filters( $raw_filters ) {
			if ( empty( $raw_filters ) ) {
				return array();
			}

			if ( is_string( $raw_filters ) && strpos( $raw_filters, '{' ) !== false ) {
				$json_decoded = json_decode( wp_unslash( $raw_filters ), true );
				if ( is_array( $json_decoded ) ) {
					$filters = $json_decoded;
				}
			} elseif ( is_string( $raw_filters ) ) {
				parse_str( urldecode( html_entity_decode( $raw_filters ) ), $filters );
			} elseif ( is_array( $raw_filters ) ) {
				$filters = $raw_filters;
			}
			if ( empty( $filters ) ) {
				return array();
			}
			$filters = $this->_sanitize_filters( $filters );
			return $filters;
		}

		function has_filter( $key, $value, $filters = null ) {
			if ( ! $filters ) {
				$filters = $this->get_raw_filters();
			}

			$filter_value = VGSE()->helpers->get_with_dot_notation( $filters, $key );
			return $filter_value === $value;
		}
		function get_raw_filters( $data = array() ) {
			$raw_filters = null;
			if ( isset( $_REQUEST['filters'] ) ) {
				$raw_filters = $_REQUEST['filters'];
			} elseif ( isset( $_REQUEST['raw_form_data']['filters'] ) ) {
				$raw_filters = $_REQUEST['raw_form_data']['filters'];
			}

			return $this->_get_raw_filters( $raw_filters );
		}

		function get_last_session_filters( $current_provider_in_page ) {
			global $wpdb;

			// Save session filters
			$sheet_sessions_key = $wpdb->prefix . 'wpse_sheet_sessions';
			$sheet_sessions     = get_user_meta( get_current_user_id(), $sheet_sessions_key, true );
			if ( empty( $sheet_sessions ) || ! is_array( $sheet_sessions ) ) {
				$sheet_sessions = array();
			}

			$out = ( ! empty( $sheet_sessions[ $current_provider_in_page ] ) ) ? array_filter( $sheet_sessions[ $current_provider_in_page ] ) : array();

			if ( ! empty( $out['meta_query'] ) ) {
				foreach ( $out['meta_query'] as $index => $meta_query ) {
					if ( empty( $meta_query['key'] ) || ( is_array( $meta_query['key'] ) && count( $meta_query['key'] ) > 1 ) ) {
						unset( $out['meta_query'][ $index ] );
					}
				}
			}

			return apply_filters( 'vg_sheet_editor/filters/last_session_filters', $out );
		}

		function set_initial_filters( $all_settings, $current_provider_in_page ) {

			// If we received custom filters from the URL or post body (wpse_custom_filters query string), don't apply the previous session filters
			if ( VGSE()->helpers->user_can_edit_post_type( $current_provider_in_page ) && ! empty( $_REQUEST['wpse_custom_filters'] ) && is_array( $_REQUEST['wpse_custom_filters'] ) ) {
				$last_session_filters = $this->_sanitize_filters( $_REQUEST['wpse_custom_filters'] );
			} elseif ( ! empty( $_REQUEST['wpse_session_query'] ) ) {
				// If the request has wpse_session_query filter, it means that we're loading a spreadsheet with a pre-saved full query so we don't want to apply the previous session filters
				$last_session_filters = array();
			} else {
				$last_session_filters = $this->get_last_session_filters( $current_provider_in_page );
			}
			$all_settings['last_session_filters'] = $last_session_filters;

			// We remove the wpse_session_query and auto export filters, we don't want to show them to the user
			if ( ! empty( $all_settings['last_session_filters'] ) ) {
				if ( isset( $all_settings['last_session_filters']['wpse_session_query'] ) ) {
					unset( $all_settings['last_session_filters']['wpse_session_query'] );
				}
				if ( isset( $all_settings['last_session_filters']['wpse_auto_export'] ) ) {
					unset( $all_settings['last_session_filters']['wpse_auto_export'] );
				}
			}

			return $all_settings;
		}

		/**
		 * Apply filters to wp-query args
		 * @param array $query_args
		 * @param array $data
		 * @return array
		 */
		function filter_posts( $query_args, $data ) {
			if ( ! empty( $data['filters'] ) ) {
				$filters = $this->get_raw_filters( $data );

				if ( ! empty( $filters['post_status'] ) ) {
					$filters['post_status']    = array_map( 'sanitize_key', array_filter( $filters['post_status'] ) );
					$query_args['post_status'] = $filters['post_status'];
				}

				if ( ! empty( $filters['post_author'] ) && is_array( $filters['post_author'] ) && WP_Sheet_Editor_Helpers::current_user_can( 'edit_others_posts' ) ) {
					$filters['post_author']   = array_map( 'intval', array_filter( $filters['post_author'] ) );
					$query_args['author__in'] = $filters['post_author'];
				}

				if ( ! empty( $filters['keyword'] ) ) {
					$editor = VGSE()->helpers->get_provider_editor( $query_args['post_type'] );
					if ( $editor->provider->is_post_type ) {
						$query_args['wpse_contains_keyword'] = $filters['keyword'];
					} else {
						$post_id_include        = $editor->provider->get_item_ids_by_keyword( $filters['keyword'], $query_args['post_type'], 'LIKE' );
						$query_args['post__in'] = ( empty( $post_id_include ) ) ? array( time() * 2 ) : $post_id_include;
					}
				}
			}

			return $query_args;
		}

		/**
		 * Render filters modal html
		 * @param string $current_post_type
		 */
		function render_filters_form( $current_post_type ) {

			$fields = array(
				'keyword' => array(
					'label'       => __( 'Contains keyword', 'vg_sheet_editor' ),
					'description' => __( 'It searches in the post title and post content. Search by multiple keywords separating keywords with a semicolon (;)' ),
				),
			);

			if ( VGSE()->helpers->get_current_provider()->is_post_type ) {
				$fields = array_merge(
					$fields,
					array(
						'post_status' => array(
							'label'       => __( 'Status', 'vg_sheet_editor' ),
							'description' => '',
						),
					)
				);
				if ( WP_Sheet_Editor_Helpers::current_user_can( 'edit_others_posts' ) ) {
					$fields['post_author'] = array(
						'label'       => __( 'Author', 'vg_sheet_editor' ),
						'description' => '',
					);
				}
			}
			$filters = apply_filters( 'vg_sheet_editor/filters/allowed_fields', $fields, $current_post_type );
			?>


			<div class="remodal remodal8 remodal-draggable" data-remodal-id="modal-filters" data-remodal-options="closeOnOutsideClick: false">

				<div class="modal-content">
					<form action="<?php echo esc_url( admin_url() ); ?>" method="GET" id="be-filters" >
						<h3><?php _e( 'Search', 'vg_sheet_editor' ); ?></h3>

						<?php do_action( 'vg_sheet_editor/filters/above_form_fields', $filters, $current_post_type ); ?>

						<ul class="unstyled-list basic-filters">
							<?php if ( isset( $filters['keyword'] ) ) { ?>
								<li>
									<label><?php echo wp_kses_post( $filters['keyword']['label'] ); ?> <?php
									if ( ! empty( $filters['keyword']['description'] ) ) {
										?>
										<a href="#" data-wpse-tooltip="right" aria-label="<?php echo esc_attr( $filters['keyword']['description'] ); ?>">( ? )</a><?php } ?></label><input type="text" name="keyword" />
								</li>
							<?php } ?>
							<?php if ( isset( $filters['post_status'] ) ) { ?>
								<li>
									<label><?php echo wp_kses_post( $filters['post_status']['label'] ); ?>  <?php
									if ( ! empty( $filters['post_status']['description'] ) ) {
										?>
										<a href="#" data-wpse-tooltip="right" aria-label="<?php echo esc_attr( $filters['post_status']['description'] ); ?>">( ? )</a><?php } ?></label>
									<select name="post_status[]" multiple data-placeholder="<?php _e( 'Select...', 'vg_sheet_editor' ); ?>" class="select2">
										<?php
										$statuses = VGSE()->helpers->get_data_provider( $current_post_type )->get_statuses();
										if ( ! empty( $statuses ) && is_array( $statuses ) ) {
											foreach ( $statuses as $item => $value ) {
												echo '<option value="' . esc_attr( $item ) . '" ';
												echo '>' . esc_html( $value ) . '</option>';
											}
										}
										?>
									</select>
								</li>
							<?php } ?>
							<?php if ( isset( $filters['post_author'] ) && post_type_supports( $current_post_type, 'author' ) ) { ?>
								<li>
									<label><?php echo wp_kses_post( $filters['post_author']['label'] ); ?>  <?php
									if ( ! empty( $filters['post_author']['description'] ) ) {
										?>
										<a href="#" data-wpse-tooltip="right" aria-label="<?php echo esc_attr( $filters['post_author']['description'] ); ?>">( ? )</a><?php } ?></label>										
									<select data-placeholder="<?php _e( 'Enter a username or email...', 'vg_sheet_editor' ); ?>" name="post_author[]" class="select2"  multiple data-remote="true" data-action="vgse_find_users_by_keyword_for_select2" data-min-input-length="4">
									</select>
								</li>
							<?php } ?>

							<?php
							do_action( 'vg_sheet_editor/filters/after_fields', $current_post_type, $filters );
							?>
						</ul>

						<?php
						do_action( 'vg_sheet_editor/filters/before_form_closing', $current_post_type, $filters );
						?>
						<button type="submit" class="remodal-confirm"><?php _e( 'Run search', 'vg_sheet_editor' ); ?></button>
						<button data-remodal-action="confirm" class="remodal-cancel"><?php _e( 'Close', 'vg_sheet_editor' ); ?></button>
						<?php
						do_action( 'vg_sheet_editor/filters/after_form_closing', $current_post_type, $filters );
						?>
					</form>
				</div>
				<br>
			</div>
			<?php
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if ( ! self::$instance ) {
				self::$instance = new WP_Sheet_Editor_Filters();
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

	add_action( 'vg_sheet_editor/initialized', 'vgse_filters_init' );

	function vgse_filters_init() {
		return WP_Sheet_Editor_Filters::get_instance();
	}
}
