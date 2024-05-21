<?php defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPSE_Sheet_Factory' ) ) {

	/**
	 * Display woocommerce item in the toolbar to tease users of the free
	 * version into purchasing the premium plugin.
	 */
	class WPSE_Sheet_Factory {

		public $args          = array();
		var $sheets_bootstrap = null;

		function __construct( $args = array() ) {
			$defaults   = array(
				'fs_object'                         => null,
				'post_type'                         => array(), // callable or array of keys
				'post_type_label'                   => array(),
				'serialized_columns'                => array(), // column keys
				'columns'                           => array(),
				'toolbars'                          => array(),
				'register_default_columns'          => true,
				'register_default_taxonomy_columns' => true,
				'bootstrap_class'                   => 'WP_Sheet_Editor_Bootstrap',
				'allowed_columns'                   => array(),
				'remove_columns'                    => array(), // column keys
				'sheets_list_priority'              => 10,
				'allow_to_enable_individual_sheets' => false,
			);
			$this->args = wp_parse_args( $args, $defaults );

			if ( empty( $this->args['post_type'] ) ) {
				return;
			}

			add_action( 'vg_sheet_editor/initialized', array( $this, 'init' ) );
			add_action( 'vg_sheet_editor/after_init', array( $this, 'after_full_core_init' ) );
		}

		function after_full_core_init() {
			// Exit in case the init() method didn't run.
			// $this->get_prop('post_type') contains a callable initially, which
			// is converted into the real post types with the init() method
			// but the init() method might not run under some conditions
			if ( is_callable( $this->get_prop( 'post_type' ) ) ) {
				return;
			}
			// It is possible that plugins will use a callable for 'post_type' that might return empty values if they don't want to initialize the sheet at all under special conditions
			if ( empty( $this->get_prop( 'post_type' ) ) ) {
				return;
			}
			// Set up spreadsheet.
			// Allow to bootstrap editor manually, later.
			if ( ! apply_filters( 'vg_sheet_editor/bootstrap/manual_init', false ) ) {
				$bootstrap_class = $this->get_prop( 'bootstrap_class' );
				$enabled_sheets  = $this->args['allow_to_enable_individual_sheets'] ? array_intersect( VGSE()->helpers->get_enabled_post_types(), $this->get_prop( 'post_type' ) ) : $this->get_prop( 'post_type' );

				if ( ! empty( $enabled_sheets ) ) {
					$this->sheets_bootstrap = new $bootstrap_class(
						array(
							'enabled_post_types'          => $enabled_sheets,
							'register_toolbars'           => true,
							'register_columns'            => $this->get_prop( 'register_default_columns' ),
							'register_taxonomy_columns'   => $this->get_prop( 'register_default_taxonomy_columns' ),
							'register_admin_menus'        => true,
							'register_spreadsheet_editor' => true,
							'post_type_labels'            => array_combine( $this->get_prop( 'post_type' ), $this->get_prop( 'post_type_label' ) ),
						)
					);
				}
			}
		}

		function get_prop( $key, $default = null ) {
			return ( isset( $this->args[ $key ] ) ) ? $this->args[ $key ] : $default;
		}

		function init() {

			if ( method_exists( 'WP_Sheet_Editor', 'allow_to_initialize' ) ) {
				if ( ! WP_Sheet_Editor::allow_to_initialize() ) {
					return;
				}
			} else {
				if ( ! is_admin() && ! apply_filters( 'vg_sheet_editor/allowed_on_frontend', false ) ) {
					return;
				}
			}

			if ( is_callable( $this->args['post_type'] ) ) {
				$post_types                    = call_user_func( $this->get_prop( 'post_type' ) );
				$this->args['post_type']       = $post_types['post_types'];
				$this->args['post_type_label'] = $post_types['labels'];
			}

			// It is possible that plugins will use a callable for 'post_type' that might return empty values if they don't want to initialize the sheet at all under special conditions
			if ( empty( $this->args['post_type'] ) ) {
				return;
			}
			add_action( 'vg_sheet_editor/editor/register_columns', array( $this, 'register_columns' ), 60 );
			add_action( 'vg_sheet_editor/editor/register_columns', array( $this, 'lock_disallowed_columns' ), 90 );
			add_action( 'vg_sheet_editor/editor/register_columns', array( $this, 'remove_columns' ), 90 );
			add_action( 'vg_sheet_editor/editor/before_init', array( $this, 'register_toolbars' ), 10 );
			add_filter( 'vg_sheet_editor/custom_columns/teaser/allow_to_lock_column', array( $this, 'dont_lock_allowed_columns' ), 99, 2 );
			add_filter( 'vg_sheet_editor/custom_post_types/get_all_post_types', array( $this, 'disable_from_custom_post_types_addon_object' ) );
			add_filter( 'vg_sheet_editor/custom_post_types/get_all_post_types_names', array( $this, 'disable_from_custom_post_types_addon_names' ) );

			if ( $this->args['fs_object']->can_use_premium_code__premium_only() ) {
				add_filter( 'vg_sheet_editor/formulas/sql_execution/can_execute', array( $this, 'disable_sql_formulas_for_serialized_columns__premium_only' ), 10, 4 );
			}

			add_filter( 'vg_sheet_editor/allowed_post_types', array( $this, 'allow_post_types' ) );

			add_filter( 'vg_sheet_editor/frontend/allowed_post_types', array( $this, 'allow_post_types' ) );
			add_filter( 'vg_sheet_editor/api/all_post_types', array( $this, 'append_to_sheets_list' ), $this->args['sheets_list_priority'], 3 );
		}
		function append_to_sheets_list( $post_types, $args, $output ) {
			if ( $output === 'names' ) {
				$post_types = array_merge( $post_types, array_combine( $this->args['post_type'], $this->args['post_type'] ) );
			} else {
				foreach ( $this->args['post_type'] as $index => $post_type_key ) {
					$post_types[ $post_type_key ] = (object) array(
						'label' => $this->args['post_type_label'][ $index ],
						'name'  => $post_type_key,
					);
				}
			}
			return $post_types;
		}

		function disable_from_custom_post_types_addon_names( $post_types_names ) {
			foreach ( $this->args['post_type'] as $post_type ) {
				if ( $index = array_search( $post_type, $post_types_names ) ) {
					unset( $post_types_names[ $index ] );
				}
			}
			return $post_types_names;
		}

		function disable_from_custom_post_types_addon_object( $post_types_objects ) {
			$indexed_post_types = wp_list_pluck( $post_types_objects, 'name' );
			foreach ( $this->args['post_type'] as $post_type ) {
				if ( $index = array_search( $post_type, $indexed_post_types ) ) {
					unset( $post_types_objects[ $index ] );
				}
			}

			return $post_types_objects;
		}

		function disable_sql_formulas_for_serialized_columns__premium_only( $allowed, $formula, $column, $post_type ) {
			if ( in_array( $post_type, $this->args['post_type'] ) && ! empty( $this->args['serialized_columns'] ) && in_array( $column['key'], $this->args['serialized_columns'] ) ) {
				$allowed = false;
			}

			return $allowed;
		}

		function allow_post_types( $post_types ) {
			$labels = $this->get_prop( 'post_type_label' );
			foreach ( $this->args['post_type'] as $index => $post_type ) {

				if ( isset( $post_types[ $post_type ] ) ) {
					continue;
				}
				$post_types[ $post_type ] = $labels[ $index ];
			}

			return $post_types;
		}

		function append_post_type_to_post_types_list( $post_types, $args, $output ) {
			$labels = $this->get_prop( 'post_type_label' );
			foreach ( $this->args['post_type'] as $index => $post_type ) {

				if ( isset( $post_types[ $post_type ] ) ) {
					continue;
				}
				if ( $output === 'names' ) {
					$post_types[ $post_type ] = $labels[ $index ];
				} else {
					$post_types[ $post_type ] = (object) array(
						'label' => $labels[ $index ],
						'name'  => $post_type,
					);
				}
			}
			return $post_types;
		}

		function allow_post_type( $post_types ) {
			$labels = $this->get_prop( 'post_type_label' );
			foreach ( $this->args['post_type'] as $index => $post_type ) {
				$post_types[ $post_type ] = $labels[ $index ];
			}
			return $post_types;
		}

		function dont_lock_allowed_columns( $allowed_to_lock, $column_key ) {
			if ( ! empty( $this->args['allowed_columns'] ) ) {
				$allowed_to_lock = ! $this->is_column_allowed( $column_key );
			}

			return $allowed_to_lock;
		}

		/**
		 * Register spreadsheet columns
		 */
		function register_toolbars( $editor ) {
			$post_types = array_intersect( $editor->args['enabled_post_types'], $this->get_prop( 'post_type' ) );
			if ( ! $post_types || ! in_array( $editor->args['provider'], $post_types, true ) ) {
				return;
			}

			if ( $this->toolbars ) {
				$toolbars = ( is_callable( $this->toolbars ) ) ? call_user_func( $this->get_prop( 'toolbars' ) ) : $this->get_prop( 'toolbars' );

				if ( empty( $toolbars ) ) {
					return;
				}
				foreach ( $post_types as $post_type ) {
					foreach ( $toolbars as $key => $toolbar ) {
						$editor->args['toolbars']->register_item( $key, $toolbar, $post_type );
					}
				}
			}

			if ( WP_Sheet_Editor_Helpers::current_user_can( 'install_plugins' ) ) {
				foreach ( $post_types as $post_type ) {
					$editor->args['toolbars']->register_item(
						'wpse_license',
						array(
							'type'                  => 'button',
							'content'               => __( 'My license', 'vg_sheet_editor' ),
							'url'                   => $this->args['fs_object']->get_account_url(),
							'toolbar_key'           => 'secondary',
							'extra_html_attributes' => ' target="_blank" ',
							'allow_in_frontend'     => false,
							'fs_id'                 => $this->args['fs_object']->get_id(),
						),
						$post_type
					);
				}
			}
		}

		/**
		 * Register spreadsheet columns
		 */
		function register_columns( $editor ) {
			$post_types = array_intersect( $editor->args['enabled_post_types'], $this->get_prop( 'post_type' ) );
			if ( ! $post_types || ! in_array( $editor->args['provider'], $post_types, true ) ) {
				return;
			}
			if ( ! $this->columns ) {
				return;
			}

			$columns = ( is_callable( $this->columns ) ) ? call_user_func( $this->get_prop( 'columns' ) ) : $this->get_prop( 'columns' );

			if ( empty( $columns ) ) {
				return;
			}
			foreach ( $post_types as $post_type ) {

				foreach ( $columns as $column_key => $column ) {
					$editor->args['columns']->register_item( $column_key, $post_type, $column );
				}
			}
		}

		function remove_columns( $editor ) {
			$post_types = array_intersect( $editor->args['enabled_post_types'], $this->get_prop( 'post_type' ) );
			if ( ! $post_types || ! in_array( $editor->args['provider'], $post_types, true ) ) {
				return;
			}
			if ( ! $this->args['remove_columns'] ) {
				return;
			}

			foreach ( $post_types as $post_type ) {
				foreach ( $this->args['remove_columns'] as $column_key ) {
					$editor->args['columns']->remove_item( $column_key, $post_type );
				}
			}
		}

		function lock_disallowed_columns( $editor ) {
			$post_types = array_intersect( $editor->args['enabled_post_types'], $this->get_prop( 'post_type' ) );
			if ( ! $post_types || ! in_array( $editor->args['provider'], $post_types, true ) ) {
				return;
			}
			if ( empty( $this->args['allowed_columns'] ) ) {
				return;
			}
			foreach ( $post_types as $post_type ) {
				// Increase column width for disabled columns, so the "premium" message fits
				$spreadsheet_columns = $editor->get_provider_items( $post_type );
				foreach ( $spreadsheet_columns as $key => $column ) {
					if ( ! in_array( $key, $this->args['allowed_columns'], true) ) {
						$editor->args['columns']->register_item(
							$key,
							$post_type,
							array(
								'column_width'      => $column['column_width'] + 80,
								'is_locked'         => true,
								'lock_template_key' => 'lock_cell_template_pro',
							),
							true
						);
					}
				}
				$editor->args['columns']->clear_cache( $post_type );
			}
		}

		function is_column_allowed( $column_key ) {
			$allowed_columns = $this->allowed_columns;
			if ( empty( $allowed_columns ) ) {
				return true;
			}

			$allowed = false;
			foreach ( $allowed_columns as $allowed_column ) {
				if ( strpos( $column_key, $allowed_column ) !== false ) {
					$allowed = true;
					break;
				}
			}
			return apply_filters( 'vg_sheet_editor/factory/is_column_allowed', $allowed, $column_key, $this );
		}

		function __set( $name, $value ) {
			$this->args[ $name ] = $value;
		}

		function __get( $name ) {
			return $this->get_prop( $name );
		}

	}

}
