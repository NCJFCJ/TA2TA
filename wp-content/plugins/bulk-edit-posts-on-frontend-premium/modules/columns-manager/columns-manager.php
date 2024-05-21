<?php defined( 'ABSPATH' ) || exit;
if ( ! class_exists( 'WP_Sheet_Editor_Columns_Manager' ) ) {

	/**
	 * Rename the columns of the spreadsheet editor to something more meaningful.
	 */
	class WP_Sheet_Editor_Columns_Manager {

		private static $instance = false;
		var $key                 = 'vgse_columns_manager';
		public $settings         = array();

		private function __construct() {

		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if ( ! self::$instance ) {
				self::$instance = new WP_Sheet_Editor_Columns_Manager();
				self::$instance->init();
			}
			return self::$instance;
		}

		function init() {

			if ( version_compare( VGSE()->version, '2.24.21-beta.2' ) < 0 ) {
				return;
			}

			require __DIR__ . '/inc/column-groups.php';

			// Allow to manage the columns formatting
			// UI
			if ( VGSE()->helpers->user_can_manage_options() ) {
				add_action( 'vg_sheet_editor/columns_visibility/enabled/after_column_action', array( $this, 'render_settings_button' ), 30, 2 );
				add_action( 'vg_sheet_editor/after_enqueue_assets', array( $this, 'enqueue_assets' ) );
				add_action( 'vg_sheet_editor/columns_visibility/after_options_saved', array( $this, 'save_column_settings' ) );
				add_action( 'vg_sheet_editor/frontend/metabox/after_fields_saved', array( $this, 'save_column_settings_from_frontend_sheet' ) );
				add_action( 'vg_sheet_editor/editor/before_init', array( $this, 'register_toolbar_items' ) );
				add_action( 'vg_sheet_editor/columns_visibility/after_instructions', array( $this, 'render_instructions' ) );
				add_filter( 'vg_sheet_editor/custom_columns/columns_detected_settings_before_cache', array( $this, 'maybe_detect_column_type_automatically' ), 10, 2 );
				add_filter( 'vg_sheet_editor/js_data', array( $this, 'add_lazy_loaded_select_options' ) );
			}

			// Apply formatting settings
			add_filter( 'vg_sheet_editor/columns/all_items', array( $this, 'apply_settings' ), 10, 2 );
			add_filter( 'vg_sheet_editor/serialized_addon/column_settings', array( $this, 'apply_settings_to_serialized_column' ), 10, 5 );
			add_filter( 'vg_sheet_editor/infinite_serialized_column/column_settings', array( $this, 'apply_settings_to_infinitely_serialized_column' ), 10, 3 );
			add_action( 'vg_sheet_editor/editor_page/after_editor_page', array( $this, 'render_column_background_picker' ) );
			add_action( 'wp_ajax_vgse_save_column_backgrounds', array( $this, 'save_column_backgrounds' ) );
			add_filter( 'vg_sheet_editor/js_data', array( $this, 'add_column_backgrounds' ), 10, 2 );
		}

		function add_column_backgrounds( $js_settings, $post_type ) {

			$user_id          = get_current_user_id();
			$user_backgrounds = get_user_meta( $user_id, 'wpse_column_backgrounds', true );

			$js_settings['columnsBackgroundColors'] = ! empty( $user_backgrounds[ $post_type ] ) ? $user_backgrounds[ $post_type ] : array();
			return $js_settings;
		}


		function save_column_backgrounds() {

			$error_message = array( 'message' => __( 'You dont have enough permissions to do this action.', 'vg_sheet_editor' ) );
			if ( empty( $_POST['post_type'] ) || empty( $_POST['backgrounds'] ) || empty( VGSE()->helpers->get_nonce_from_request() ) || ! VGSE()->helpers->verify_nonce_from_request() ) {
				wp_send_json_error( $error_message );
			}

			$post_type = VGSE()->helpers->sanitize_table_key( $_POST['post_type'] );

			if ( ! VGSE()->helpers->user_can_view_post_type( $post_type ) ) {
				wp_send_json_error( $error_message );
			}

			$backgrounds = array();
			foreach ( $_POST['backgrounds'] as $column_key => $background ) {
				if ( strpos( $background, '#' ) !== 0 ) {
					continue;
				}
				$backgrounds[ sanitize_text_field( $column_key ) ] = sanitize_text_field( $background );
			}
			$user_id          = get_current_user_id();
			$user_backgrounds = get_user_meta( $user_id, 'wpse_column_backgrounds', true );

			if ( empty( $user_backgrounds ) ) {
				$user_backgrounds = array();
			}
			$user_backgrounds[ $post_type ] = $backgrounds;
			update_user_meta( $user_id, 'wpse_column_backgrounds', $user_backgrounds );
			wp_send_json_success( true );
		}


		function render_column_background_picker( $post_type ) {
			?>
			<div class="wpse-column-background-selector-wrapper">
				<h3><?php _e( 'Please select the background color', 'vg_sheet_editor' ); ?></h3>
				<input type="color" />
				<button type="button" class="button clear-background-color"><?php _e( 'Clear value', 'vg_sheet_editor' ); ?></button>
				<br><br>
				<button type="button" class="button button-primary save-background-color"><?php _e( 'Save', 'vg_sheet_editor' ); ?></button>
				<button type="button" class="button cancel-background-change"><?php _e( 'Cancel', 'vg_sheet_editor' ); ?></button>
			</div>
			<?php
		}

		function are_values_date_time( $values ) {
			$out    = array(
				'possible_dates' => array(),
				'is_date'        => false,
				'save_format'    => false,
			);
			$values = array_filter( array_unique( $values ) );
			if ( ! empty( $values ) ) {
				foreach ( $values as $value ) {
					if ( ! is_scalar( $value ) ) {
						continue;
					}
					if ( empty( $value ) || preg_match( '/^(\d{4}-\d{2}-\d{2} \d{1,2}:\d{1,2}:\d{1,2}|-?\d{9,10})$/', $value ) ) {
						$out['possible_dates'][] = $value;
					}
				}

				$out['is_date'] = count( $values ) === count( $out['possible_dates'] );
				if ( ! empty( $out['possible_dates'] ) && $out['is_date'] ) {
					$first_value = $out['possible_dates'][0];
					if ( preg_match( '/^\d{4}-\d{2}-\d{2} \d{1}:\d{1,2}:\d{1,2}$/', $first_value ) ) {
						$out['save_format'] = 'Y-m-d G:i:s';
					} elseif ( preg_match( '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $first_value ) ) {
						$out['save_format'] = 'Y-m-d H:i:s';
					} elseif ( preg_match( '/^-?\d{9,10}$/', $first_value ) ) {
						$out['save_format'] = 'U';
					}
				}
			}
			return $out;
		}
		function are_values_dates( $values ) {
			$out    = array(
				'possible_dates' => array(),
				'is_date'        => false,
				'display_format' => 'YYYY-MM-DD', // moment.js format used by the cell's calendar
				'save_format'    => false,
			);
			$values = array_filter( array_unique( $values ) );
			if ( ! empty( $values ) ) {
				foreach ( $values as $value ) {
					if ( ! is_scalar( $value ) ) {
						continue;
					}
					if ( empty( $value ) || preg_match( '/^(\d{4}-\d{2}-\d{2}|\d{8})$/', $value ) ) {
						$out['possible_dates'][] = $value;
					}
				}

				$out['is_date'] = count( $values ) === count( $out['possible_dates'] );
				if ( ! empty( $out['possible_dates'] ) ) {
					$first_value = $out['possible_dates'][0];
					if ( $out['is_date'] ) {
						if ( is_numeric( $first_value ) && strlen( $first_value ) === 8 ) {
							$out['save_format'] = 'Ymd';
						} elseif ( preg_match( '/^(\d{4}-\d{2}-\d{2})$/', $value ) ) {
							$out['save_format'] = 'Y-m-d';
						}
					}
				}
			}
			return $out;
		}

		function are_values_media_files( $values ) {
			$out    = array(
				'possible_files' => array(),
				'is_file'        => false,
			);
			$values = array_filter( array_unique( $values ) );
			if ( ! empty( $values ) ) {
				foreach ( $values as $value ) {
					if ( ! is_scalar( $value ) ) {
						continue;
					}
					if ( is_numeric( $value ) && get_post_type( $value ) === 'attachment' ) {
						$out['possible_files'][] = $value;
					} elseif ( strpos( $value, WP_CONTENT_URL . '/uploads/' ) === 0 ) {
						$out['possible_files'][] = $value;
					}
				}

				$out['is_file'] = count( $values ) === count( $out['possible_files'] );
			}
			return $out;
		}

		function maybe_detect_column_type_automatically( $columns_detected, $post_type ) {
			if ( ! empty( VGSE()->options['disable_automatic_formatting_detection'] ) ) {
				return $columns_detected;
			}

			$new_formatting = array();
			if ( isset( $columns_detected['normal'] ) ) {
				foreach ( $columns_detected['normal'] as $column_key => $column_settings ) {
					if ( $column_settings['detected_type']['type'] !== 'text' ) {
						continue;
					}

					// If we have defined formatting previously, don't overwrite it automatically
					$current_format_settings = $this->get_formatted_column_settings( $column_key, $post_type );
					if ( ! empty( $current_format_settings ) ) {
						continue;
					}

					if ( ! isset( $new_formatting[ $column_key ] ) ) {
						$date_detection = $this->are_values_dates( $column_settings['detected_type']['sample_values'] );
						if ( $date_detection['is_date'] ) {
							$new_formatting[ $column_key ] = array(
								'field_type'          => 'date',
								'date_format_save'    => $date_detection['save_format'],
								'date_format_display' => 'Y-m-d',
							);
						}
					}
					if ( ! isset( $new_formatting[ $column_key ] ) ) {
						$date_detection = $this->are_values_date_time( $column_settings['detected_type']['sample_values'] );
						if ( $date_detection['is_date'] ) {
							$new_formatting[ $column_key ] = array(
								'field_type'               => 'date_time',
								'date_time_format_save'    => $date_detection['save_format'],
								'date_time_format_display' => 'Y-m-d H:i:s',
							);
						}
					}
					if ( ! isset( $new_formatting[ $column_key ] ) ) {
						$files_detection = $this->are_values_media_files( $column_settings['detected_type']['sample_values'] );
						if ( $files_detection['is_file'] ) {
							$new_formatting[ $column_key ] = array(
								'field_type'            => 'file',
								'file_saved_format'     => is_numeric( $files_detection['possible_files'][0] ) ? 'id' : 'url',
								'allow_multiple_files'  => strpos( $files_detection['possible_files'][0], ',' ) !== false,
								'multiple_files_format' => 'comma',
							);
						}
					}
				}
			}

			if ( ! empty( $new_formatting ) ) {
				$this->save_column_settings( $post_type, $new_formatting );
			}
			return $columns_detected;
		}

		function render_instructions() {
			_e( ' Some columns have the <i class="fa fa-cog"></i> button to change the formatting', 'vg_sheet_editor' );
		}

		/**
		 * Register toolbar item to edit columns visibility live on the spreadsheet
		 */
		function register_toolbar_items( $editor ) {
			$post_types = $editor->args['enabled_post_types'];
			foreach ( $post_types as $post_type ) {
				$editor->args['toolbars']->register_item(
					'columns_manager',
					array(
						'type'                  => 'button',
						'allow_in_frontend'     => false,
						'content'               => __( 'Columns manager', 'vg_sheet_editor' ),
						'toolbar_key'           => 'secondary',
						'extra_html_attributes' => 'data-remodal-target="modal-columns-visibility"',
					),
					$post_type
				);
			}
		}

		function maybe_apply_settings_to_serialized_column( $column_args, $post_type ) {
			if ( ! empty( $column_args['key'] ) ) {
				$new_settings = $this->get_formatted_column_settings( $column_args['key'], $post_type, $column_args );
				$column_args  = wp_parse_args( $new_settings, $column_args );
			}

			return $column_args;
		}

		function apply_settings_to_infinitely_serialized_column( $column_args, $serialized_column, $post_type ) {
			return $this->maybe_apply_settings_to_serialized_column( $column_args, $post_type );
		}

		function apply_settings_to_serialized_column( $column_args, $first_set_keys, $field, $key, $post_type ) {
			return $this->maybe_apply_settings_to_serialized_column( $column_args, $post_type );
		}

		function apply_settings( $columns ) {
			$options = $this->get_settings();

			if ( empty( $options ) ) {
				return $columns;
			}
			$formatted_options = array();
			foreach ( $options as $post_type => $columns_settings ) {
				if ( ! isset( $formatted_options[ $post_type ] ) ) {
					$formatted_options[ $post_type ] = array();
				}
				foreach ( $columns_settings as $column_key => $column_settings ) {
					$formatted_options[ $post_type ][ $column_key ] = $this->format_column_settings( $column_settings );
					if ( preg_match( '/\d/', $column_key ) ) {
						$formatted_options[ $post_type ][ $this->key_to_regex( $column_key ) ] = $formatted_options[ $post_type ][ $column_key ];
					}
				}
			}

			foreach ( $columns as $post_type_key => $post_type_columns ) {
				// Skip if special formatting not defined for this post type
				if ( ! isset( $options[ $post_type_key ] ) ) {
					continue;
				}
				foreach ( $post_type_columns as $key => $column ) {
					$custom_column_settings = null;
					if ( isset( $formatted_options[ $post_type_key ][ $key ] ) ) {
						$custom_column_settings = $formatted_options[ $post_type_key ][ $key ];
					} elseif ( preg_match( '/\d/', $key ) ) {
						$regex_key = $this->key_to_regex( $key );
						if ( isset( $formatted_options[ $post_type_key ][ $regex_key ] ) ) {
							$custom_column_settings = $formatted_options[ $post_type_key ][ $regex_key ];
						}
					}

					// Skip if special formatting not defined for this column
					if ( ! $custom_column_settings ) {
						continue;
					}

					if ( ! empty( $column['allow_custom_format'] ) && ! empty( $custom_column_settings['field_type'] ) ) {
						$custom_format_settings = $this->get_custom_format_column_settings( $key, $post_type, $custom_column_settings );
						if ( $custom_format_settings ) {
							$columns[ $post_type_key ][ $key ] = wp_parse_args( $custom_format_settings, $column );
						}
					}

					$other_column_settings = $this->get_other_column_settings( $columns[ $post_type_key ][ $key ], $key, $post_type, $custom_column_settings );
					if ( $other_column_settings ) {
						$columns[ $post_type_key ][ $key ] = wp_parse_args( $other_column_settings, $column );
					}
				}
			}

			return $columns;
		}

		function _get_all_capabilities() {
			if ( ! function_exists( 'wp_roles' ) ) {
				return array();
			}
			$roles        = wp_roles();
			$capabilities = array();
			foreach ( $roles->roles as $role ) {
				if ( ! empty( $role['capabilities'] ) ) {
					$capabilities = array_merge( $capabilities, array_keys( $role['capabilities'] ) );
				}
			}
			sort( $capabilities );
			return $capabilities;
		}

		function get_other_column_settings( $out, $key, $post_type, $column_settings = null ) {
			if ( ! is_array( $column_settings ) ) {
				$column_settings = $this->get_column_settings( $key, $post_type );
			}

			if ( ! empty( $column_settings['is_read_only'] ) && $column_settings['is_read_only'] === 'yes' ) {
				$out['is_locked'] = true;
				if ( method_exists( 'WP_Sheet_Editor_Columns', '_make_column_read_only' ) ) {
					$out = WP_Sheet_Editor_Columns::_make_column_read_only( $out );
				}
			}

			if ( ! empty( $column_settings['user_capabilities_can_read'] ) ) {
				$out['user_capabilities_can_read'] = $column_settings['user_capabilities_can_read'];
			}
			if ( ! empty( $column_settings['user_capabilities_can_edit'] ) ) {
				$out['user_capabilities_can_edit'] = $column_settings['user_capabilities_can_edit'];
			}

			return $out;
		}
		function get_formatted_column_settings( $key, $post_type, $column = array() ) {
			$column_settings     = $this->get_column_settings( $key, $post_type );
			$out                 = array();
			$allow_custom_format = empty( $column ) || ! empty( $column['allow_custom_format'] );
			if ( $allow_custom_format ) {
				$out = $this->get_custom_format_column_settings( $key, $post_type, $column_settings );
			}

			if ( ! empty( $column_settings['is_read_only'] ) && $column_settings['is_read_only'] === 'yes' ) {
				$out['is_locked'] = true;
				if ( method_exists( 'WP_Sheet_Editor_Columns', '_make_column_read_only' ) ) {
					$out = WP_Sheet_Editor_Columns::_make_column_read_only( $out );
				}
			}
			if ( ! empty( $column_settings['user_capabilities_can_read'] ) ) {
				$out['user_capabilities_can_read'] = $column_settings['user_capabilities_can_read'];
			}
			if ( ! empty( $column_settings['user_capabilities_can_edit'] ) ) {
				$out['user_capabilities_can_edit'] = $column_settings['user_capabilities_can_edit'];
			}
			return $out;
		}

		function get_custom_format_column_settings( $key, $post_type, $column_settings = null ) {
			$out = array();
			// Skip if field type = automatic
			if ( ! is_array( $column_settings ) ) {
				$column_settings = $this->get_column_settings( $key, $post_type );
			}
			if ( empty( $column_settings['field_type'] ) ) {
				return $out;
			}

			if ( $column_settings['field_type'] === 'text' ) {
				$out['formatted'] = array(
					'data' => $key,
				);
				$out['default_value'] = '';
			} elseif ( $column_settings['field_type'] === 'button' ) {
				$out['formatted']   = array(
					'data'     => $key,
					'renderer' => 'wp_external_button',
					'readOnly' => true,
				);
				$out['unformatted'] = $out['formatted'];
			} elseif ( $column_settings['field_type'] === 'text_editor' ) {
				$out['formatted'] = array(
					'data'     => $key,
					'renderer' => 'wp_tinymce',
				);
			} elseif ( $column_settings['field_type'] === 'select' && ! empty( $column_settings['allowed_values'] ) ) {
				$lines          = array_map( 'trim', preg_split( '/\r\n|\r|\n/', $column_settings['allowed_values'] ) );
				$column_options = array();
				foreach ( $lines as $line ) {
					$line_parts                    = array_map( 'trim', explode( ':', $line ) );
					$label                         = isset( $line_parts[1] ) ? $line_parts[1] : $line_parts[0];
					$option_key                    = $line_parts[0];
					$column_options[ $option_key ] = $label;
				}
				$out['formatted'] = array(
					'data'          => $key,
					'editor'        => 'select',
					'selectOptions' => $column_options,
				);
				if ( empty( VGSE()->options['enable_plain_select_cells'] ) ) {
					$out['formatted']['renderer'] = 'wp_friendly_select';
				}
			} elseif ( $column_settings['field_type'] === 'multi_select' && ! empty( $column_settings['multi_select_allowed_values'] ) ) {
				$lines          = array_map( 'trim', preg_split( '/\r\n|\r|\n/', $column_settings['multi_select_allowed_values'] ) );
				$column_options = array();
				foreach ( $lines as $line ) {
					$line_parts       = array_map( 'trim', explode( ':', $line ) );
					$label            = isset( $line_parts[1] ) ? $line_parts[1] : $line_parts[0];
					$option_key       = $line_parts[0];
					$column_options[] = array(
						'id'    => $option_key,
						'label' => $label,
					);
				}
				$out['formatted']                  = array(
					'renderer'      => 'wp_chosen_dropdown',
					'data'          => $key,
					'editor'        => 'chosen',
					'source'        => $column_options,
					'chosenOptions' => array(
						'multiple'        => true,
						'search_contains' => true,
						//                      'skip_no_results' => true,
														'data' => $column_options,
					),
				);
				$out['prepare_value_for_database'] = array( $this, 'prepare_multi_select_for_database' );
				$out['prepare_value_for_display']  = array( $this, 'prepare_multi_select_for_display' );
				$out['columns_manager_settings']   = $column_settings;
			} elseif ( $column_settings['field_type'] === 'checkbox' && ! empty( $column_settings['checked_template'] ) ) {
				$out['formatted']     = array(
					'data'              => $key,
					'type'              => 'checkbox',
					'checkedTemplate'   => $column_settings['checked_template'],
					'uncheckedTemplate' => $column_settings['unchecked_template'],
				);
				$out['default_value'] = $column_settings['unchecked_template'];
			} elseif ( $column_settings['field_type'] === 'date' && ! empty( $column_settings['date_format_save'] ) ) {
				$out                             = $this->get_format_settings_for_date_column( $key, $column_settings['date_format_save'], $column_settings['date_format_display'] );
				$out['columns_manager_settings'] = $column_settings;
			} elseif ( $column_settings['field_type'] === 'date_time' && ! empty( $column_settings['date_time_format_save'] ) ) {
				$out                             = $this->get_format_settings_for_date_time_column( $key, $column_settings['date_time_format_save'], $column_settings['date_time_format_display'] );
				$out['columns_manager_settings'] = $column_settings;
			} elseif ( $column_settings['field_type'] === 'file' ) {
				$out['type']                       = $column_settings['allow_multiple_files'] ? 'boton_gallery_multiple' : 'boton_gallery';
				$out['formatted']                  = array(
					'data'     => $key,
					'renderer' => 'wp_media_gallery',
				);
				$out['wp_media_multiple']          = true;
				$out['columns_manager_settings']   = $column_settings;
				$out['prepare_value_for_database'] = array( $this, 'prepare_files_for_database' );
				$out['prepare_value_for_display']  = array( $this, 'prepare_files_for_display' );
			} elseif ( $column_settings['field_type'] === 'url' ) {
				$out['formatted']                         = array(
					'data' => $key,
				);
				$out['custom_sanitization_before_saving'] = 'esc_url_raw';
			} elseif ( $column_settings['field_type'] === 'email' ) {
				$out['formatted']                         = array(
					'data' => $key,
				);
				$out['custom_sanitization_before_saving'] = 'sanitize_email';
				$out['value_type']                        = 'email';
			} elseif ( $column_settings['field_type'] === 'color_picker' ) {
				$out['formatted']                         = array(
					'editor' => 'wp_color_picker',
					'data' => $key,
				);
				$out['custom_sanitization_before_saving'] = 'sanitize_hex_color';
			} elseif ( $column_settings['field_type'] === 'raw_html' && WP_Sheet_Editor_Helpers::current_user_can( 'unfiltered_html' ) ) {
				$out['formatted']                         = array(
					'data' => $key,
				);
				$out['custom_sanitization_before_saving'] = 'strval';
			} elseif ( $column_settings['field_type'] === 'number' ) {
				$out['formatted']                         = array(
					'data' => $key,
				);
				$out['custom_sanitization_before_saving'] = 'intval';
			} elseif ( $column_settings['field_type'] === 'currency' ) {
				$out['formatted']                  = array(
					'data' => $key,
				);
				$out['prepare_value_for_database'] = array( $this, 'prepare_currency_for_database' );
				$out['columns_manager_settings']   = $column_settings;
			} elseif ( $column_settings['field_type'] === 'term' ) {
				$taxonomy_filter = ( empty( $column_settings['taxonomy_filter'] ) ) ? $post_type : $column_settings['taxonomy_filter'];
				if ( ! empty( VGSE()->options['be_enable_fancy_taxonomy_cell'] ) ) {
					$formatted = array(
						'data'          => $key,
						'editor'        => 'chosen',
						'source'        => array( VGSE()->data_helpers, 'get_taxonomy_terms' ),
						'callback_args' => array( $taxonomy_filter ),
						'chosenOptions' => array(
							'multiple'                 => ! empty( $column_settings['allow_multiple_terms'] ),
							'search_contains'          => true,
							'create_option'            => true,
							'skip_no_results'          => true,
							'persistent_create_option' => true,
							'data'                     => array(),
						),
					);
				} else {
					$hierarchy_tip = is_taxonomy_hierarchical( $taxonomy_filter ) ? __( '. Add child categories using this format: Parent > child1 > child2', 'vg_sheet_editor' ) : '';
					$formatted     = array(
						'data'   => $key,
						'type'   => 'autocomplete',
						'source' => 'loadTaxonomyTerms',
					);

					$multiple_tip = '';
					if ( ! empty( $column_settings['allow_multiple_terms'] ) ) {
						$multiple_tip = __( 'Enter multiple terms separated by commas', 'vg_sheet_editor' );
					}
					$formatted['comment'] = array( 'value' => $multiple_tip . $hierarchy_tip );
				}
				$out['formatted']                  = $formatted;
				$out['columns_manager_settings']   = $column_settings;
				$out['prepare_value_for_database'] = array( $this, 'prepare_terms_for_database' );
				$out['prepare_value_for_display']  = array( $this, 'prepare_terms_for_display' );
				$out['default_value']              = '';
				$out['formatted']['taxonomy_key']  = $taxonomy_filter;
			} elseif ( $column_settings['field_type'] === 'post' ) {
				$out['formatted'] = array(
					'data'           => $key,
					'type'           => 'autocomplete',
					'source'         => 'searchPostByKeyword',
					'searchPostType' => ( empty( $column_settings['post_type_filter'] ) ) ? $post_type : $column_settings['post_type_filter'],
				);
				if ( $column_settings['allow_multiple_posts'] ) {
					$out['formatted']['comment'] = array( 'value' => __( 'Enter multiple post titles separated by commas', 'vg_sheet_editor' ) );
				}
				$out['columns_manager_settings']   = $column_settings;
				$out['prepare_value_for_database'] = array( $this, 'prepare_posts_for_database' );
				$out['prepare_value_for_display']  = array( $this, 'prepare_posts_for_display' );
				$out['default_value']              = '';
			} elseif ( $column_settings['field_type'] === 'user' ) {
				$out['formatted']                  = array(
					'data'   => $key,
					'type'   => 'autocomplete',
					'source' => 'searchUsers',
				);
				$out['columns_manager_settings']   = $column_settings;
				$out['prepare_value_for_database'] = array( $this, 'prepare_user_for_database' );
				$out['prepare_value_for_display']  = array( $this, 'prepare_user_for_display' );
				$out['default_value']              = '';
			}
			return $out;
		}

		function prepare_user_for_database( $post_id, $cell_key, $data_to_save, $post_type, $column_settings, $spreadsheet_columns ) {
			if ( empty( $data_to_save ) ) {
				return $data_to_save;
			}

			$manager_settings = $column_settings['columns_manager_settings'];
			$user             = get_user_by( 'login', $data_to_save );
			if ( ! $user ) {
				return '';
			}

			$out = '';
			if ( $manager_settings['user_saved_format'] === 'ID' ) {
				$out = $user->ID;
			} elseif ( $manager_settings['user_saved_format'] === 'user_login' ) {
				$out = $user->user_login;
			} elseif ( $manager_settings['user_saved_format'] === 'user_email' ) {
				$out = $user->user_email;
			}
			return $out;
		}

		function prepare_user_for_display( $value, $post, $column_key, $column_settings ) {
			if ( empty( $value ) ) {
				return '';
			}

			$manager_settings = $column_settings['columns_manager_settings'];
			$user             = get_user_by( str_replace( 'user_', '', $manager_settings['user_saved_format'] ), $value );
			if ( ! $user ) {
				return '';
			}

			$out = $user->user_login;
			return $out;
		}

		function prepare_terms_for_display( $value, $post, $column_key, $column_settings ) {
			global $wpdb;
			$out = '';
			if ( empty( $value ) ) {
				return $out;
			}
			$separator = VGSE()->helpers->get_term_separator();
			if ( is_string( $value ) ) {
				$terms = array_map( 'trim', explode( $separator, $value ) );
			} elseif ( is_array( $value ) ) {
				$terms = $value;
			}
			$manager_settings = $column_settings['columns_manager_settings'];
			$save_format      = $manager_settings['term_saved_format'];
			if ( method_exists( VGSE()->helpers, 'sanitize_table_key' ) ) {
				$save_format = VGSE()->helpers->sanitize_table_key( $save_format );
			}
			if ( ! in_array( $save_format, array( 'term_id', 'name', 'slug' ), true ) ) {
				return $out;
			}
			if ( empty( $manager_settings['taxonomy_filter'] ) ) {
				$manager_settings['taxonomy_filter'] = $post_type;
			}
			$args = array(
				'hide_empty'             => false,
				'taxonomy'               => $manager_settings['taxonomy_filter'],
				'update_term_meta_cache' => false,
			);
			if ( $save_format == 'term_id' ) {
				$args['include'] = $terms;
			} elseif ( $save_format === 'slug' ) {
				$args['slug'] = $terms;
			} elseif ( $save_format === 'name' ) {
				$term_ids        = VGSE()->data_helpers->prepare_post_terms_for_saving( implode( $separator, $terms ), $manager_settings['taxonomy_filter'] );
				$args['include'] = $term_ids;
			} else {
				return $out;
			}

			$term_objects = get_terms( $args );
			$out          = VGSE()->data_helpers->prepare_post_terms_for_display( $term_objects );
			return $out;
		}

		function prepare_terms_for_database( $post_id, $cell_key, $data_to_save, $post_type, $column_settings, $spreadsheet_columns ) {
			if ( empty( $data_to_save ) ) {
				return $data_to_save;
			}

			$manager_settings = $column_settings['columns_manager_settings'];
			$save_format      = $manager_settings['term_saved_format'];

			if ( method_exists( VGSE()->helpers, 'sanitize_table_key' ) ) {
				$save_format = VGSE()->helpers->sanitize_table_key( $save_format );
			}
			if ( ! in_array( $save_format, array( 'term_id', 'name', 'slug' ), true ) ) {
				return '';
			}
			if ( empty( $manager_settings['taxonomy_filter'] ) ) {
				$manager_settings['taxonomy_filter'] = $post_type;
			}
			$separator      = VGSE()->helpers->get_term_separator();
			$raw_term_names = array_map( 'trim', explode( $separator, $data_to_save ) );

			if ( empty( $manager_settings['allow_multiple_terms'] ) ) {
				$term_names = array( $raw_term_names[0] );
			} else {
				$term_names = $raw_term_names;
			}

			if ( $save_format === 'name' ) {
				$values = $term_names;
			} elseif ( $save_format === 'term_id' ) {
				$values = VGSE()->data_helpers->prepare_post_terms_for_saving( implode( $separator, $term_names ), $manager_settings['taxonomy_filter'] );
			} elseif ( $save_format === 'slug' ) {
				$term_ids = VGSE()->data_helpers->prepare_post_terms_for_saving( implode( $separator, $term_names ), $manager_settings['taxonomy_filter'] );
				$args     = array(
					'hide_empty'             => false,
					'include'                => $term_ids,
					'taxonomy'               => $manager_settings['taxonomy_filter'],
					'fields'                 => 'slugs',
					'update_term_meta_cache' => false,
				);
				$values   = get_terms( $args );
			}
			if ( $manager_settings['multiple_terms_format'] === 'comma' ) {
				$out = implode( $separator, $values );
			} else {
				$out = $values;
			}
			return $out;
		}

		function prepare_posts_for_database( $post_id, $cell_key, $data_to_save, $post_type, $column_settings, $spreadsheet_columns ) {
			global $wpdb;
			if ( empty( $data_to_save ) ) {
				return $data_to_save;
			}

			$manager_settings = $column_settings['columns_manager_settings'];
			$save_format      = $manager_settings['post_saved_format'];

			if ( method_exists( VGSE()->helpers, 'sanitize_table_key' ) ) {
				$save_format = VGSE()->helpers->sanitize_table_key( $save_format );
			}
			if ( ! in_array( $save_format, array( 'ID', 'post_title', 'post_name' ) ) ) {
				return '';
			}
			if ( empty( $manager_settings['post_type_filter'] ) ) {
				$manager_settings['post_type_filter'] = $post_type;
			}
			$post_titles = array_map( 'html_entity_decode', array_map( 'trim', explode( ',', $data_to_save ) ) );

			$posts_in_query_placeholders = implode( ', ', array_fill( 0, count( $post_titles ), '%s' ) );
			$values                      = $wpdb->get_col( $wpdb->prepare( "SELECT $save_format FROM $wpdb->posts WHERE post_type = %s AND post_title IN ($posts_in_query_placeholders) ", array_merge( array( $manager_settings['post_type_filter'] ), $post_titles ) ) );

			if ( $manager_settings['multiple_posts_format'] === 'comma' ) {
				$out = implode( ',', $values );
			} else {
				$out = $values;
			}
			return $out;
		}

		function prepare_multi_select_for_database( $post_id, $cell_key, $data_to_save, $post_type, $column_settings, $spreadsheet_columns ) {
			if ( empty( $data_to_save ) ) {
				return $data_to_save;
			}
			$manager_settings = $column_settings['columns_manager_settings'];
			$save_format      = $manager_settings['multi_select_saved_format'];

			if ( $save_format === 'serialized' ) {
				$data_to_save = array_map( 'trim', explode( ',', $data_to_save ) );
			}
			return $data_to_save;
		}

		function prepare_multi_select_for_display( $value, $post, $column_key, $column_settings ) {
			$out = '';
			if ( empty( $value ) ) {
				return $out;
			}
			if ( is_string( $value ) ) {
				$out = $value;
			} elseif ( is_array( $value ) ) {
				$out = implode( ', ', $value );
			}
			return $out;
		}

		function prepare_posts_for_display( $value, $post, $column_key, $column_settings ) {
			global $wpdb;
			$posts = '';
			if ( empty( $value ) ) {
				return $posts;
			}
			if ( is_string( $value ) ) {
				$posts = array_map( 'trim', explode( ',', $value ) );
			} elseif ( is_array( $value ) ) {
				$posts = $value;
			}
			$manager_settings = $column_settings['columns_manager_settings'];
			$save_format      = $manager_settings['post_saved_format'];
			if ( method_exists( VGSE()->helpers, 'sanitize_table_key' ) ) {
				$save_format = VGSE()->helpers->sanitize_table_key( $save_format );
			}
			if ( ! in_array( $save_format, array( 'ID', 'post_title', 'post_name' ) ) ) {
				return $posts;
			}
			if ( empty( $manager_settings['post_type_filter'] ) ) {
				$manager_settings['post_type_filter'] = $post->post_type;
			}
			if ( $save_format == 'ID' ) {
				$post_ids = $posts;
			} else {
				$posts_in_query_placeholders = implode( ', ', array_fill( 0, count( $posts ), '%s' ) );
				$post_ids                    = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = %s AND $save_format IN ($posts_in_query_placeholders) ", array_merge( array( $manager_settings['post_type_filter'] ), $posts ) ) );
			}
			$post_titles = array();
			foreach ( $post_ids as $single_post ) {
				$post_titles[] = html_entity_decode( get_post_field( 'post_title', (int) $single_post, 'raw' ) );
			}
			$out = implode( ', ', array_filter( $post_titles ) );
			return $out;
		}

		function prepare_files_for_display( $value, $post, $column_key, $column_settings ) {
			$value = VGSE()->helpers->get_gallery_cell_content( $post->ID, $column_key, $column_settings['data_type'], $value );
			return $value;
		}

		function get_format_settings_for_date_column( $key, $date_format_save, $date_format_display ) {
			$settings                               = array();
			$settings['formatted']                  = array(
				'data'                 => $key,
				'type'                 => 'date',
				'customDatabaseFormat' => $date_format_save,
				'dateFormatPhp'        => $date_format_display,
				'correctFormat'        => true,
				'defaultDate'          => '',
				'datePickerConfig'     => array(
					'firstDay'       => 0,
					'showWeekNumber' => true,
					'numberOfMonths' => 1,
					'yearRange'      => array( 1900, (int) date( 'Y' ) + 20 ),
				),
			);
			$settings['prepare_value_for_database'] = array( $this, 'prepare_date_for_database' );
			$settings['prepare_value_for_display']  = array( $this, 'format_date_for_cell' );
			return $settings;
		}
		function get_format_settings_for_date_time_column( $key, $date_format_save, $date_format_display ) {
			$settings                               = array();
			$settings['formatted']                  = array(
				'editor'               => 'wp_datetime',
				'data'                 => $key,
				'type'                 => 'date',
				'customDatabaseFormat' => $date_format_save,
				'dateFormatPhp'        => $date_format_display,
				'correctFormat'        => true,
				'defaultDate'          => '',
				'datePickerConfig'     => array(
					'firstDay'       => 0,
					'showWeekNumber' => true,
					'numberOfMonths' => 1,
				),
			);
			$settings['prepare_value_for_database'] = array( $this, 'prepare_date_for_database' );
			$settings['prepare_value_for_display']  = array( $this, 'format_date_for_cell' );
			return $settings;
		}

		function format_date_for_cell( $value, $post, $cell_key, $cell_args ) {
			$column_settings = $cell_args['columns_manager_settings'];
			if ( ! in_array( $column_settings['field_type'], array( 'date', 'date_time' ), true ) ) {
				return $value;
			}
			// Disabled this because it only worked with meta columns, we use the received $value as is
			// $value  = VGSE()->helpers->get_current_provider()->get_item_meta( $post->ID, $cell_key, true, 'read' );
			$format = $column_settings['field_type'] === 'date' ? 'Y-m-d' : 'Y-m-d H:i:s';
			if ( ! empty( $column_settings[ $column_settings['field_type'] . '_format_display' ] ) ) {
				$format = $column_settings[ $column_settings['field_type'] . '_format_display' ];
			}
			if ( ! empty( $value ) ) {
				$timestamp = preg_match( '/^-?\d{9,10}$/', $value ) ? (int) $value : strtotime( $value );
				$value     = date( $format, $timestamp );
			}

			return $value;
		}

		function prepare_date_for_database( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			$column_settings = $cell_args['columns_manager_settings'];
			if ( ! in_array( $column_settings['field_type'], array( 'date', 'date_time' ), true ) ) {
				return $data_to_save;
			}
			if ( ! empty( $data_to_save ) ) {
				$save_format = $column_settings[ $column_settings['field_type'] . '_format_save' ];
				$date        = DateTime::createFromFormat( $column_settings[ $column_settings['field_type'] . '_format_display' ], $data_to_save );
				if ( $date ) {
					$data_to_save = $date->format( $save_format );
				} else {
					$data_to_save = date( $save_format, strtotime( $data_to_save ) );
				}
			}
			return $data_to_save;
		}

		function prepare_files_for_database( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			$column_settings = $cell_args['columns_manager_settings'];
			if ( $column_settings['field_type'] !== 'file' ) {
				return $data_to_save;
			}
			if ( ! empty( $data_to_save ) ) {
				$urls = array_map( 'trim', explode( ',', $data_to_save ) );
				if ( $column_settings['file_saved_format'] === 'id' ) {
					$file_ids = VGSE()->helpers->maybe_replace_urls_with_file_ids( $urls, $post_id );
				} else {
					foreach ( $urls as $index => $url ) {
						$urls[ $index ] = remove_query_arg( 'wpId', $url );
					}
					$file_ids = $urls;
				}
				if ( $column_settings['allow_multiple_files'] ) {
					$data_to_save = ( $column_settings['multiple_files_format'] === 'comma' ) ? implode( ',', $file_ids ) : $file_ids;
				} else {
					$data_to_save = current( $file_ids );
				}
			}
			return $data_to_save;
		}

		function prepare_currency_for_database( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			$column_settings = $cell_args['columns_manager_settings'];
			if ( ! empty( $data_to_save ) && is_numeric( $data_to_save ) ) {
				$data_to_save = number_format( (float) $data_to_save, (int) $column_settings['currency_decimals'], $column_settings['decimal_separator'], $column_settings['thousands_separator'] );
			}
			return $data_to_save;
		}

		function get_settings( $post_type = '' ) {

			if ( isset( $this->settings[ $post_type ] ) ) {
				return $this->settings[ $post_type ];
			} else {
				$existing = get_option( $this->key );
				if ( empty( $existing ) ) {
					$existing = array();
				}
				if ( $post_type && empty( $existing[ $post_type ] ) ) {
					$existing[ $post_type ] = array();
				}
				$this->settings[ $post_type ] = $existing;
			}
			return $this->settings[ $post_type ];
		}

		function key_to_regex( $column_key ) {
			$regex = false;
			if ( ! empty( $column_key ) && preg_match( '/\d/', $column_key ) ) {
				$regex = '/' . str_replace( '/', '', preg_replace( '/[0-9]+/', '\d+', $column_key ) ) . '/';
			}
			return $regex;
		}

		function save_column_settings_from_frontend_sheet( $post_id ) {
			$this->save_column_settings( get_post_meta( $post_id, 'vgse_post_type', true ) );
		}

		function sanitize_column_settings( $dirty_column_settings ) {
			$cleaned_column_settings = array();
			foreach ( $dirty_column_settings as $column_key => $args ) {
				$cleaned_column_settings[ sanitize_text_field( $column_key ) ] = array_filter(
					array(
						'field_type'                  => isset( $args['field_type'] ) ? sanitize_text_field( $args['field_type'] ) : null,
						'allowed_values'              => isset( $args['allowed_values'] ) ? sanitize_textarea_field( $args['allowed_values'] ) : null,
						'multi_select_allowed_values' => isset( $args['multi_select_allowed_values'] ) ? sanitize_textarea_field( $args['multi_select_allowed_values'] ) : null,
						'multi_select_saved_format'   => isset( $args['multi_select_saved_format'] ) ? sanitize_text_field( $args['multi_select_saved_format'] ) : null,
						'checked_template'            => isset( $args['checked_template'] ) ? sanitize_text_field( $args['checked_template'] ) : null,
						'unchecked_template'          => isset( $args['unchecked_template'] ) ? sanitize_text_field( $args['unchecked_template'] ) : null,
						'user_saved_format'           => isset( $args['user_saved_format'] ) ? sanitize_text_field( $args['user_saved_format'] ) : null,
						'post_saved_format'           => isset( $args['post_saved_format'] ) ? sanitize_text_field( $args['post_saved_format'] ) : null,
						'post_type_filter'            => isset( $args['post_type_filter'] ) ? sanitize_text_field( $args['post_type_filter'] ) : null,
						'allow_multiple_posts'        => isset( $args['allow_multiple_posts'] ) ? sanitize_text_field( $args['allow_multiple_posts'] ) : null,
						'multiple_posts_format'       => isset( $args['multiple_posts_format'] ) ? sanitize_text_field( $args['multiple_posts_format'] ) : null,
						'term_saved_format'           => isset( $args['term_saved_format'] ) ? sanitize_text_field( $args['term_saved_format'] ) : null,
						'taxonomy_filter'             => isset( $args['taxonomy_filter'] ) ? sanitize_text_field( $args['taxonomy_filter'] ) : null,
						'allow_multiple_terms'        => isset( $args['allow_multiple_terms'] ) ? sanitize_text_field( $args['allow_multiple_terms'] ) : null,
						'multiple_terms_format'       => isset( $args['multiple_terms_format'] ) ? sanitize_text_field( $args['multiple_terms_format'] ) : null,
						'thousands_separator'         => isset( $args['thousands_separator'] ) ? sanitize_text_field( $args['thousands_separator'] ) : null,
						'decimal_separator'           => isset( $args['decimal_separator'] ) ? sanitize_text_field( $args['decimal_separator'] ) : null,
						'currency_decimals'           => isset( $args['currency_decimals'] ) ? sanitize_text_field( $args['currency_decimals'] ) : null,
						'file_saved_format'           => isset( $args['file_saved_format'] ) ? sanitize_text_field( $args['file_saved_format'] ) : null,
						'allow_multiple_files'        => isset( $args['allow_multiple_files'] ) ? sanitize_text_field( $args['allow_multiple_files'] ) : null,
						'multiple_files_format'       => isset( $args['multiple_files_format'] ) ? sanitize_text_field( $args['multiple_files_format'] ) : null,
						'date_format_save'            => isset( $args['date_format_save'] ) ? sanitize_text_field( $args['date_format_save'] ) : null,
						'date_format_display'         => isset( $args['date_format_display'] ) ? sanitize_text_field( $args['date_format_display'] ) : null,
						'date_time_format_save'       => isset( $args['date_time_format_save'] ) ? sanitize_text_field( $args['date_time_format_save'] ) : null,
						'date_time_format_display'    => isset( $args['date_time_format_display'] ) ? sanitize_text_field( $args['date_time_format_display'] ) : null,
						'is_read_only'                => isset( $args['is_read_only'] ) ? sanitize_text_field( $args['is_read_only'] ) : '',
						'user_capabilities_can_read'  => isset( $args['user_capabilities_can_read'] ) ? sanitize_text_field( $args['user_capabilities_can_read'] ) : '',
						'user_capabilities_can_edit'  => isset( $args['user_capabilities_can_edit'] ) ? sanitize_text_field( $args['user_capabilities_can_edit'] ) : '',
					)
				);
			}
			return apply_filters( 'vg_sheet_editor/columns_manager/cleaned_column_settings', $cleaned_column_settings, $dirty_column_settings );
		}

		function save_column_settings( $post_type, $custom_settings = array() ) {
			if ( $custom_settings ) {
				$_POST['column_settings'] = $custom_settings;
			}
			if ( ! isset( $_POST['column_settings'] ) ) {
				return;
			}
			$cleaned_column_settings = $this->sanitize_column_settings( $_POST['column_settings'] );
			$existing                = $this->get_settings( $post_type );
			$existing[ $post_type ]  = wp_parse_args( $cleaned_column_settings, $existing[ $post_type ] );
			$existing                = VGSE()->helpers->array_remove_empty( $existing );

			update_option( $this->key, apply_filters( 'vg_sheet_editor/columns_manager/save_settings', $existing, $cleaned_column_settings, $post_type ), false );
			// Clear the local cache
			$this->settings = array();
		}

		/**
		 * Enqueue frontend assets
		 */
		function enqueue_assets() {
			wp_enqueue_script( 'wp-sheet-editor-columns-manager', plugins_url( '/assets/js/init.js', __FILE__ ), array(), VGSE()->version );
		}

		function format_column_settings( $column_settings ) {
			if ( empty( $column_settings ) ) {
				$column_settings = array();
			}

			$default_settings = array(
				'field_type'                  => '',
				'allowed_values'              => '',
				'multi_select_allowed_values' => '',
				'multi_select_saved_format'   => '',
				'checked_template'            => '',
				'unchecked_template'          => '',
				'user_saved_format'           => '',
				'post_saved_format'           => '',
				'post_type_filter'            => '',
				'allow_multiple_posts'        => '',
				'multiple_posts_format'       => '',
				'term_saved_format'           => '',
				'taxonomy_filter'             => '',
				'allow_multiple_terms'        => '',
				'multiple_terms_format'       => '',
				'thousands_separator'         => '',
				'decimal_separator'           => '',
				'currency_decimals'           => '',
				'file_saved_format'           => '',
				'allow_multiple_files'        => '',
				'multiple_files_format'       => '',
				'date_format_save'            => '',
				'date_format_display'         => '',
				'date_time_format_save'       => '',
				'date_time_format_display'    => '',
				'user_capabilities_can_read'  => '',
				'user_capabilities_can_edit'  => '',
				'is_read_only'                => false,
			);
			$column_settings  = wp_parse_args( $column_settings, $default_settings );
			return $column_settings;
		}

		function get_column_settings( $column_key, $post_type ) {

			$existing_settings = $this->get_settings( $post_type );
			if ( isset( $existing_settings[ $post_type ][ $column_key ] ) ) {
				$column_settings = $existing_settings[ $post_type ][ $column_key ];
			} elseif ( preg_match( '/\d/', $column_key ) ) {
				$regex_key = $this->key_to_regex( $column_key );
				if ( $regex_key ) {
					foreach ( $existing_settings[ $post_type ] as $column_key => $raw_column_settings ) {
						if ( preg_match( $regex_key, $column_key ) ) {
							$column_settings = $raw_column_settings;
							break;
						}
					}
				}
			}
			if ( empty( $column_settings ) ) {
				$column_settings = array();
			}

			return $column_settings = $this->format_column_settings( $column_settings );
		}

		function render_settings_button( $column, $post_type ) {
			if ( ! apply_filters( 'vg_sheet_editor/columns_manager/can_render_button', true, $column, $post_type ) ) {
				return;
			}
			$column_options = array(
				'custom_format'         => false,
				'read_only'             => array( $this, 'render_read_only_option' ),
				'required_capabilities' => array( $this, 'render_required_capabilities_options' ),
			);
			if ( ! empty( $column['allow_custom_format'] ) ) {
				$column_options['custom_format'] = array( $this, 'render_custom_format_options' );
			}

			$registered_column_options = array_filter( apply_filters( 'vg_sheet_editor/columns_manager/column_options', $column_options, $column, $post_type ) );
			if ( empty( $registered_column_options ) ) {
				return;
			}
			$column_settings = $this->get_column_settings( $column['key'], $post_type );
			?>
			<button class="settings-column column-action" title="<?php echo esc_attr( __( 'Settings', 'vg_sheet_editor' ) ); ?>"><i class="fa fa-cog"></i></button>
			<div class="column-settings"> 
				<?php
				foreach ( $registered_column_options as $option_key => $callback ) {
					call_user_func( $callback, $option_key, $column, $post_type, $column_settings );
				}
				do_action( 'vg_sheet_editor/columns_manager/after_settings_fields_rendered', $column, $post_type, $column_settings );
				?>
			</div>
			<?php
		}

		function render_read_only_option( $option_key, $column, $post_type, $column_settings ) {
			?>

			<div class="column-settings-field">					
				<label for="<?php echo sanitize_html_class('column_settings' . $column['key'] . 'is_read_only'); ?>"><?php esc_html_e( 'Is read only?', 'vg_sheet_editor' ); ?></label>				

				<select id="<?php echo sanitize_html_class('column_settings' . $column['key'] . 'is_read_only'); ?>" data-lazy-key="columnsManagerIsReadOnly" data-selected="<?php echo esc_attr( $column_settings['is_read_only'] ); ?>" name="column_settings[<?php echo esc_attr( $column['key'] ); ?>][is_read_only]">					
				</select>
				<p><?php esc_html_e( 'Read-only columns will display a lock and it won\'t be possible to edit them anywhere in the spreadsheet. This is not a security feature because people still can edit in the regular WP screens.', 'vg_sheet_editor' ); ?></p>				
			</div>
			<?php
		}

		function render_required_capabilities_options( $option_key, $column, $post_type, $column_settings ) {
			?>

			<div class="column-settings-field">
				<label for="<?php echo sanitize_html_class('column_settings' . $column['key'] . 'user_capabilities_can_read'); ?>"><?php esc_html_e( 'User capabilities that can read this column', 'vg_sheet_editor' ); ?> <a href="#" data-wpse-tooltip="right" aria-label="<?php esc_attr_e( 'The column will appear in the spreadsheet and exports if the user has a role with the required capability.', 'vg_sheet_editor' ); ?>">( ? )</a></label>
				<select id="<?php echo sanitize_html_class('column_settings' . $column['key'] . 'user_capabilities_can_read'); ?>"  data-lazy-key="columnsManagerUserCapabilities" data-selected="<?php echo esc_attr( $column_settings['user_capabilities_can_read'] ); ?>"   name="column_settings[<?php echo esc_attr( $column['key'] ); ?>][user_capabilities_can_read]"></select>

				<label for="<?php echo sanitize_html_class('column_settings' . $column['key'] . 'user_capabilities_can_edit'); ?>"><?php esc_html_e( 'User capabilities that can edit this column', 'vg_sheet_editor' ); ?> <a href="#" data-wpse-tooltip="right" aria-label="<?php esc_attr_e( 'The column will be read only if the user doesn\'t have a role with the required capability.', 'vg_sheet_editor' ); ?>">( ? )</a></label>
				<select id="<?php echo sanitize_html_class('column_settings' . $column['key'] . 'user_capabilities_can_edit'); ?>" data-lazy-key="columnsManagerUserCapabilities" data-selected="<?php echo esc_attr( $column_settings['user_capabilities_can_edit'] ); ?>"  name="column_settings[<?php echo esc_attr( $column['key'] ); ?>][user_capabilities_can_edit]"></select>			
			</div>
			<?php
		}

		function add_lazy_loaded_select_options( $js_data ) {
			if ( ! isset( $js_data['lazy_loaded_select_options'] ) ) {
				$js_data['lazy_loaded_select_options'] = array();
			}
			$js_data['lazy_loaded_select_options']['columnsManagerMultiSelectSavedFormat'] = array(
				'comma'      => __( 'Separated with commas', 'vg_sheet_editor' ),
				'serialized' => __( 'Serialized array', 'vg_sheet_editor' ),
			);
			$js_data['lazy_loaded_select_options']['columnsManagerFileSavedFormat']        = array(
				'id'  => __( 'File ID', 'vg_sheet_editor' ),
				'url' => __( 'File URL', 'vg_sheet_editor' ),
			);
			$js_data['lazy_loaded_select_options']['columnsManagerIsReadOnly']             = array(
				''    => __( 'Use default', 'vg_sheet_editor' ),
				'yes' => __( 'Yes', 'vg_sheet_editor' ),
				'no'  => __( 'No', 'vg_sheet_editor' ),
			);
			$js_data['lazy_loaded_select_options']['columnsManagerUserSavedFormat']        = array(
				'ID'         => __( 'ID', 'vg_sheet_editor' ),
				'user_login' => __( 'Username', 'vg_sheet_editor' ),
				'user_email' => __( 'Email', 'vg_sheet_editor' ),
			);
			$js_data['lazy_loaded_select_options']['columnsManagerPostSavedFormat']        = array(
				'ID'         => __( 'ID', 'vg_sheet_editor' ),
				'post_title' => __( 'Title', 'vg_sheet_editor' ),
				'post_name'  => __( 'Slug', 'vg_sheet_editor' ),
			);
			$js_data['lazy_loaded_select_options']['columnsManagerMultipleTermsFormat']    = array(
				'comma' => __( 'Saved them separated by comma', 'vg_sheet_editor' ),
				'array' => __( 'Save them as serialized array', 'vg_sheet_editor' ),
			);
			$js_data['lazy_loaded_select_options']['columnsManagerTermSavedFormat']        = array(
				'term_id' => __( 'Term ID', 'vg_sheet_editor' ),
				'name'    => __( 'Name', 'vg_sheet_editor' ),
				'slug'    => __( 'Slug', 'vg_sheet_editor' ),
			);
			$js_data['lazy_loaded_select_options']['columnsManagerTaxonomies']             = array_merge(
				array(
					'' => __( 'Same as the spreadsheet taxonomy', 'vg_sheet_editor' ),
				),
				wp_list_pluck( get_taxonomies( array(), 'objects' ), 'label', 'name' )
			);
			$js_data['lazy_loaded_select_options']['columnsManagerPostTypes']              = array_merge(
				array(
					'' => __( 'Same as the spreadsheet post type', 'vg_sheet_editor' ),
				),
				wp_list_pluck( VGSE()->helpers->get_all_post_types(), 'label', 'name' )
			);
			$js_data['lazy_loaded_select_options']['columnsManagerFormats']                = array(
				''             => __( 'Automatic', 'vg_sheet_editor' ),
				'text'         => __( 'Text', 'vg_sheet_editor' ),
				'text_editor'  => __( 'Text editor (tinymce)', 'vg_sheet_editor' ),
				'select'       => __( 'Single selection dropdown', 'vg_sheet_editor' ),
				'multi_select' => __( 'Multi select dropdown', 'vg_sheet_editor' ),
				'checkbox'     => __( 'Checkbox', 'vg_sheet_editor' ),
				'file'         => __( 'File upload', 'vg_sheet_editor' ),
				'date'         => __( 'Date', 'vg_sheet_editor' ),
				'date_time'    => __( 'Date and time', 'vg_sheet_editor' ),
				'user'         => __( 'User dropdown', 'vg_sheet_editor' ),
				'post'         => __( 'Post  dropdown', 'vg_sheet_editor' ),
				'term'         => __( 'Taxonomy term  dropdown', 'vg_sheet_editor' ),
				'currency'     => __( 'Currency', 'vg_sheet_editor' ),
				'url'          => __( 'URL', 'vg_sheet_editor' ),
				'email'        => __( 'Email', 'vg_sheet_editor' ),
				'number'       => __( 'Number', 'vg_sheet_editor' ),
				'button'       => __( 'Clickable button', 'vg_sheet_editor' ),
				'raw_html'     => __( 'Raw HTML', 'vg_sheet_editor' ),
				'color_picker'     => __( 'Color picker', 'vg_sheet_editor' ),
			);

			$capabilities = $this->_get_all_capabilities();
			$js_data['lazy_loaded_select_options']['columnsManagerUserCapabilities'] = array_merge(
				array(
					'' => __( 'Default', 'vg_sheet_editor' ),
				),
				array_combine( $capabilities, $capabilities )
			);

			return $js_data;
		}

		function render_custom_format_options( $option_key, $column, $post_type, $column_settings ) {
			$column_key = $column['key'];
			?>
			<div class="column-settings-field field-type">
				<label for="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'field_type' ); ?>"><?php esc_html_e( 'Column format', 'vg_sheet_editor' ); ?></label>
				<select id="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'field_type' ); ?>" data-lazy-key="columnsManagerFormats" data-selected="<?php echo esc_attr( $column_settings['field_type'] ); ?>" name="column_settings[<?php echo esc_attr( $column_key ); ?>][field_type]">					
				</select>
			</div>
			<div class="column-settings-field settings-for-type settings-for-raw_html">
				<p><?php esc_html_e( 'This format will only allow administrators with the capability unfiltered_html to save any HTML in this column, we\'ll still remove unsafe html tags when non-administrators save values in this column.', 'vg_sheet_editor' ); ?></p>
			</div>
			<div class="column-settings-field settings-for-type settings-for-color_picker">
				<p><?php esc_html_e( 'This will allow users to edit colors using a color picker. The values will be saved in hex format. For example: #000000', 'vg_sheet_editor' ); ?></p>
			</div>
			<div class="column-settings-field settings-for-type settings-for-button">
				<p><?php esc_html_e( 'This can be used if the cell value will always be a URL, so the cell will be displayed as readonly and it will contain a button that will open the URL from the cell value.', 'vg_sheet_editor' ); ?></p>
			</div>
			<div class="column-settings-field settings-for-type settings-for-select">
				<label for="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'allowed_values' ); ?>"><?php esc_html_e( 'Allowed values', 'vg_sheet_editor' ); ?></label>
				<p><?php esc_html_e( 'Enter each choice on a new line. For more control, you may specify both a value and label like this: red : Red', 'vg_sheet_editor' ); ?></p>
				<textarea id="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'allowed_values' ); ?>" name="column_settings[<?php echo esc_attr( $column_key ); ?>][allowed_values]"><?php echo esc_html( $column_settings['allowed_values'] ); ?></textarea>
			</div>
			<div class="column-settings-field settings-for-type settings-for-multi_select">
				<label for="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'multi_select_allowed_values' ); ?>"><?php esc_html_e( 'Allowed values', 'vg_sheet_editor' ); ?></label>
				<p><?php esc_html_e( 'Enter each choice on a new line. For more control, you may specify both a value and label like this: red : Red', 'vg_sheet_editor' ); ?></p>
				<textarea id="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'multi_select_allowed_values' ); ?>" name="column_settings[<?php echo esc_attr( $column_key ); ?>][multi_select_allowed_values]"><?php echo esc_html( $column_settings['multi_select_allowed_values'] ); ?></textarea>
				<label for="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'multi_select_saved_format' ); ?>"><?php esc_html_e( 'How are the multiple values saved in the database?', 'vg_sheet_editor' ); ?></label>	
				<select id="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'multi_select_saved_format' ); ?>" data-lazy-key="columnsManagerMultiSelectSavedFormat"  data-selected="<?php echo esc_attr( $column_settings['multi_select_saved_format'] ); ?>"  name="column_settings[<?php echo esc_attr( $column_key ); ?>][multi_select_saved_format]">
				</select>
			</div>
			<div class="column-settings-field settings-for-type settings-for-checkbox">
				<label for="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'checked_template' ); ?>"><?php esc_html_e( 'What value is saved when the checkbox is checked?', 'vg_sheet_editor' ); ?></label>					
				<input id="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'checked_template' ); ?>" value="<?php echo esc_attr( $column_settings['checked_template'] ); ?>" type="text" name="column_settings[<?php echo esc_attr( $column_key ); ?>][checked_template]">
				<label for="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'unchecked_template' ); ?>"><?php esc_html_e( 'What value is saved when the checkbox is unchecked?', 'vg_sheet_editor' ); ?></label>					
				<input id="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'unchecked_template' ); ?>" value="<?php echo esc_attr( $column_settings['unchecked_template'] ); ?>" type="text" name="column_settings[<?php echo esc_attr( $column_key ); ?>][unchecked_template]">
			</div>
			<div class="column-settings-field settings-for-type settings-for-user">	
				<p><?php esc_html_e( 'You will be able to type the username in the cell and the cell will show a dropdown with suggestions.', 'vg_sheet_editor' ); ?></p>
				<label for="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'user_saved_format' ); ?>"><?php esc_html_e( 'How is the user saved in the database?', 'vg_sheet_editor' ); ?></label>	
				<select id="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'user_saved_format' ); ?>" data-lazy-key="columnsManagerUserSavedFormat" data-selected="<?php echo esc_attr( $column_settings['user_saved_format'] ); ?>" name="column_settings[<?php echo esc_attr( $column_key ); ?>][user_saved_format]">
				</select>
			</div>
			<div class="column-settings-field settings-for-type settings-for-term">	
				<p><?php esc_html_e( 'You will be able to type the term name in the cell and the cell will show a dropdown with suggestions.', 'vg_sheet_editor' ); ?></p>
				<label for="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'term_saved_format' ); ?>"><?php esc_html_e( 'How is the taxonomy term saved in the database?', 'vg_sheet_editor' ); ?></label>	
				<select id="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'term_saved_format' ); ?>" data-lazy-key="columnsManagerTermSavedFormat" data-selected="<?php echo esc_attr( $column_settings['term_saved_format'] ); ?>" name="column_settings[<?php echo esc_attr( $column_key ); ?>][term_saved_format]">
					
				</select>
				<br>
				<label><input  <?php checked( $column_settings['allow_multiple_terms'], 'yes' ); ?> value="yes" type="checkbox" name="column_settings[<?php echo esc_attr( $column_key ); ?>][allow_multiple_terms]"> <?php esc_html_e( 'Allow multiple terms per field?', 'vg_sheet_editor' ); ?></label>

				<label for="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'multiple_terms_format' ); ?>"><?php esc_html_e( 'How do you want to save the multiple terms?', 'vg_sheet_editor' ); ?></label>
				<select id="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'multiple_terms_format' ); ?>" data-lazy-key="columnsManagerMultipleTermsFormat" data-selected="<?php echo esc_attr( $column_settings['multiple_terms_format'] ); ?>" name="column_settings[<?php echo esc_attr( $column_key ); ?>][multiple_terms_format]">
				</select>
				<label for="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'taxonomy_filter' ); ?>"><?php esc_html_e( 'Accept terms from this taxonomy', 'vg_sheet_editor' ); ?></label>	
				<p><?php esc_html_e( 'For example, if you select the blog categories, we will only accept blog categories in this column.', 'vg_sheet_editor' ); ?></p>
				<select id="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'taxonomy_filter' ); ?>" data-lazy-key="columnsManagerTaxonomies" data-selected="<?php echo esc_attr( $column_settings['taxonomy_filter'] ); ?>" name="column_settings[<?php echo esc_attr( $column_key ); ?>][taxonomy_filter]"></select>
			</div>
			<div class="column-settings-field settings-for-type settings-for-currency">	
				<p><?php esc_html_e( 'You will be able to type numbers without formatting, for example: 999999.88 or 100, and we will automatically save them in the formatted way. This conversion will happen when you save and not live when you edit in the cells and you will see the modified values on the next spreadsheet reload.', 'vg_sheet_editor' ); ?></p>
				<label for="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'currency_decimals' ); ?>"><?php esc_html_e( 'Number of decimals', 'vg_sheet_editor' ); ?></label>	
				<input id="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'currency_decimals' ); ?>" name="column_settings[<?php echo esc_attr( $column_key ); ?>][currency_decimals]" value="<?php echo (int) $column_settings['currency_decimals']; ?>">
				<br>
				<label for="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'decimal_separator' ); ?>"><?php esc_html_e( 'Decimals separator', 'vg_sheet_editor' ); ?></label>	
				<input id="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'decimal_separator' ); ?>" name="column_settings[<?php echo esc_attr( $column_key ); ?>][decimal_separator]" value="<?php echo esc_attr( $column_settings['decimal_separator'] ); ?>">
				<br>
				<label for="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'thousands_separator' ); ?>"><?php esc_html_e( 'Thousands separator', 'vg_sheet_editor' ); ?></label>	
				<input id="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'thousands_separator' ); ?>" name="column_settings[<?php echo esc_attr( $column_key ); ?>][thousands_separator]" value="<?php echo esc_attr( $column_settings['thousands_separator'] ); ?>">
			</div>
			<div class="column-settings-field settings-for-type settings-for-post">	
				<p><?php esc_html_e( 'You will be able to type the post title in the cell and the cell will show a dropdown with suggestions.', 'vg_sheet_editor' ); ?></p>
				<label for="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'post_saved_format' ); ?>"><?php esc_html_e( 'How is the post saved in the database?', 'vg_sheet_editor' ); ?></label>	
				<select id="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'post_saved_format' ); ?>" data-lazy-key="columnsManagerPostSavedFormat" data-selected="<?php echo esc_attr( $column_settings['post_saved_format'] ); ?>" name="column_settings[<?php echo esc_attr( $column_key ); ?>][post_saved_format]">
				</select>
				<br>
				<label><input  <?php checked( $column_settings['allow_multiple_posts'], 'yes' ); ?> value="yes" type="checkbox" name="column_settings[<?php echo esc_attr( $column_key ); ?>][allow_multiple_posts]"> <?php esc_html_e( 'Allow multiple posts per field?', 'vg_sheet_editor' ); ?></label>

				<label for="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'multiple_posts_format' ); ?>"><?php esc_html_e( 'How do you want to save the multiple posts?', 'vg_sheet_editor' ); ?></label>
				<select id="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'multiple_posts_format' ); ?>" data-lazy-key="columnsManagerMultipleTermsFormat" data-selected="<?php echo esc_attr( $column_settings['multiple_posts_format'] ); ?>" name="column_settings[<?php echo esc_attr( $column_key ); ?>][multiple_posts_format]">
				</select>
				<label for="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'post_type_filter' ); ?>"><?php esc_html_e( 'Accept post from this post type', 'vg_sheet_editor' ); ?></label>	
				<p><?php esc_html_e( 'For example, if you select the post type "product", we will only accept product titles.', 'vg_sheet_editor' ); ?></p>
				<select id="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'post_type_filter' ); ?>"  data-lazy-key="columnsManagerPostTypes"  data-selected="<?php echo esc_attr( $column_settings['post_type_filter'] ); ?>" name="column_settings[<?php echo esc_attr( $column_key ); ?>][post_type_filter]"></select>
			</div>
			<div class="column-settings-field settings-for-type settings-for-file">
				<label for="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'file_saved_format' ); ?>"><?php esc_html_e( 'How is the file saved in the database?', 'vg_sheet_editor' ); ?></label>	
				<p><?php _e( 'The cell will display the values as URLs and you can edit in the cells using full URLs, file ID, or file name.<br>External URLs are automatically imported into the media library.<br>We will save the value in the format selected here', 'vg_sheet_editor' ); ?></p>
				<select id="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'file_saved_format' ); ?>" data-lazy-key="columnsManagerFileSavedFormat" data-selected="<?php echo esc_attr( $column_settings['file_saved_format'] ); ?>" name="column_settings[<?php echo esc_attr( $column_key ); ?>][file_saved_format]">					
				</select>
				<br>
				<label><input  <?php checked( $column_settings['allow_multiple_files'], 'yes' ); ?> value="yes" type="checkbox" name="column_settings[<?php echo esc_attr( $column_key ); ?>][allow_multiple_files]"> <?php esc_html_e( 'Allow multiple files per field?', 'vg_sheet_editor' ); ?></label>
				<label for="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'multiple_files_format' ); ?>"><?php esc_html_e( 'How do you want to save the multiple files?', 'vg_sheet_editor' ); ?></label>
				<select id="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'multiple_files_format' ); ?>" data-lazy-key="columnsManagerMultipleTermsFormat" data-selected="<?php echo esc_attr( $column_settings['multiple_files_format'] ); ?>"  name="column_settings[<?php echo esc_attr( $column_key ); ?>][multiple_files_format]">
				</select>
			</div>
			<div class="column-settings-field settings-for-type settings-for-date">	
				<label for="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'date_format_display' ); ?>"><?php esc_html_e( 'What date format do you want to display in the spreadsheet?', 'vg_sheet_editor' ); ?></label>	
				<p><?php _e( 'Enter a date format. <a href="https://www.php.net/date#refsect1-function.date-parameters" target="_blank">List of formats</a>. If you leave it empty, we\'ll use the default: Y-m-d', 'vg_sheet_editor' ); ?></p>
				<input id="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'date_format_display' ); ?>" value="<?php echo esc_attr( $column_settings['date_format_display'] ); ?>" type="text" name="column_settings[<?php echo esc_attr( $column_key ); ?>][date_format_display]">

				<label for="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'date_format_save' ); ?>"><?php esc_html_e( 'What date format do you want to save in the database?', 'vg_sheet_editor' ); ?></label>	
				<p><?php _e( 'Enter a date format. <a href="https://www.php.net/date#refsect1-function.date-parameters" target="_blank">List of formats</a>. Example: Y-m-d', 'vg_sheet_editor' ); ?></p>
				<input id="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'date_format_save' ); ?>" value="<?php echo esc_attr( $column_settings['date_format_save'] ); ?>" type="text" name="column_settings[<?php echo esc_attr( $column_key ); ?>][date_format_save]">
			</div>
			<div class="column-settings-field settings-for-type settings-for-date_time">	
				<label for="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'date_time_format_display' ); ?>"><?php esc_html_e( 'What date time format do you want to display in the spreadsheet?', 'vg_sheet_editor' ); ?></label>	
				<p><?php _e( 'Enter a date format. <a href="https://www.php.net/date#refsect1-function.date-parameters" target="_blank">List of formats</a>. If you leave it empty, we\'ll use the default: Y-m-d H:i:s', 'vg_sheet_editor' ); ?></p>
				<input id="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'date_time_format_display' ); ?>" value="<?php echo esc_attr( $column_settings['date_time_format_display'] ); ?>" type="text" name="column_settings[<?php echo esc_attr( $column_key ); ?>][date_time_format_display]">

				<label for="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'date_time_format_save' ); ?>"><?php esc_html_e( 'What date format do you want to save in the database?', 'vg_sheet_editor' ); ?></label>	
				<p><?php _e( 'Enter a date format. <a href="https://www.php.net/date#refsect1-function.date-parameters" target="_blank">List of formats</a>. Example: Y-m-d H:i:s', 'vg_sheet_editor' ); ?></p>
				<input id="<?php echo sanitize_html_class( 'column_settings' . $column_key . 'date_time_format_save' ); ?>" value="<?php echo esc_attr( $column_settings['date_time_format_save'] ); ?>" type="text" name="column_settings[<?php echo esc_attr( $column_key ); ?>][date_time_format_save]">
			</div>
			<?php
		}

		function __set( $name, $value ) {
			$this->$name = $value;
		}

		function __get( $name ) {
			return $this->$name;
		}

	}

	add_action( 'vg_sheet_editor/initialized', 'vgse_columns_manager_init' );

	function vgse_columns_manager_init() {
		return WP_Sheet_Editor_Columns_Manager::get_instance();
	}
}
