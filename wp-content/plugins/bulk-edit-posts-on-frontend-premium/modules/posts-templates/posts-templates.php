<?php defined( 'ABSPATH' ) || exit;
if ( ! defined( 'VGSE_POST_TEMPLATES_DIR' ) ) {
	define( 'VGSE_POST_TEMPLATES_DIR', __DIR__ );
}

if ( ! class_exists( 'WP_Sheet_Editor_Post_Templates' ) ) {

	/**
	 * Rename the columns of the spreadsheet editor to something more meaningful.
	 */
	class WP_Sheet_Editor_Post_Templates {

		private static $instance = false;
		public $plugin_url       = null;
		public $plugin_dir       = null;
		public $textname         = 'wpsept';
		public $posts_data       = array();

		private function __construct() {

		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if ( null == self::$instance ) {
				self::$instance = new WP_Sheet_Editor_Post_Templates();
				self::$instance->init();
			}
			return self::$instance;
		}

		function init() {

			$this->plugin_url = plugins_url( '/', __FILE__ );
			$this->plugin_dir = __DIR__;

			// Priority 9 to execute before everything else.
			// The WC extension uses this filter with priority 10 to create products using the WC api
			add_filter( 'vg_sheet_editor/add_new_posts/create_new_posts', array( $this, 'duplicate_post' ), 9, 3 );
			add_action( 'vg_sheet_editor/editor/before_init', array( $this, 'register_toolbar' ) );
			add_action( 'vg_sheet_editor/after_enqueue_assets', array( $this, 'register_assets' ) );
			add_filter( 'vg_sheet_editor/duplicate/new_post_id', array( $this, 'duplicate_woocommerce_product' ), 10, 3 );
			add_filter( 'vg_sheet_editor/options_page/options', array( $this, 'add_settings_page_options' ) );
		}
		/**
		 * Add fields to options page
		 * @param array $sections
		 * @return array
		 */
		function add_settings_page_options( $sections ) {
			$sections['speed']['fields'][] = array(
				'id'       => 'duplicate_batch_size',
				'type'     => 'text',
				'validate' => 'numeric',
				'title'    => __( 'Duplicate rows: Number of copies to create per batch', 'vg_sheet_editor' ),
				'desc'     => __( 'By default, we create 100 copies per batch', 'vg_sheet_editor' ),
				'default'  => 100,
			);
			return $sections;

		}

		/**
		 * Register frontend assets
		 */
		function register_assets() {
			wp_enqueue_script( 'wpse-duplicate_js', plugins_url( '/assets/js/init.js', __FILE__ ), array(), filemtime( __DIR__ . '/assets/js/init.js' ), false );
		}

		/**
		 * Render modal html
		 * @param string $current_post_type
		 */
		function render_form( $current_post_type ) {
			$nonce = wp_create_nonce( 'bep-nonce' );
			?>


			<div class="remodal remodal-duplicate" data-remodal-id="modal-duplicate">

				<div class="modal-content">
					<h3><?php _e( 'Duplicate items in bulk', 'vg_sheet_editor' ); ?></h3>

					<?php do_action( 'vg_sheet_editor/duplicate/above_form_fields', $current_post_type ); ?>

					<ul class="unstyled-list">
						<li>
							<label><?php _e( 'Duplicate this item:', 'vg_sheet_editor' ); ?></label>									
							<select name="duplicate_this[]" multiple data-remote="true" data-min-input-length="4" data-action="vgse_find_post_by_name" data-post-type="<?php echo esc_attr( $current_post_type ); ?>" data-nonce="<?php echo esc_attr( $nonce ); ?>" class="duplicate-modal-post-selector select2" data-placeholder="<?php _e( 'Enter item name...', 'vg_sheet_editor' ); ?>" required>
								<option></option>
							</select>
						</li>
						<li>
							<label><?php _e( 'How many copies do you want?', 'vg_sheet_editor' ); ?></label>
							<input type="number" name="number_of_copies" value="1">
						</li>
						<?php
						do_action( 'vg_sheet_editor/duplicate/after_fields', $current_post_type );
						?>
					</ul>

					<div class="response"></div>

					<button class="remodal-confirm wpse-duplicate-trigger"><?php _e( 'Execute', 'vg_sheet_editor' ); ?></button>
					<button data-remodal-action="confirm" class="remodal-cancel"><?php _e( 'Close', 'vg_sheet_editor' ); ?></button>
				</div>
				<br>
			</div>
			<?php
		}

		function register_toolbar( $editor ) {
			if ( $editor->provider->key === 'user' ) {
				return;
			}
			$post_types = $editor->args['enabled_post_types'];
			foreach ( $post_types as $post_type ) {
				$editor->args['toolbars']->register_item(
					'duplicate',
					array(
						'type'                  => 'button',
						'help_tooltip'          => __( 'Duplicate items in bulk.', 'vg_sheet_editor' ),
						'content'               => __( 'Duplicate', 'vg_sheet_editor' ),
						'icon'                  => 'fa fa-copy',
						'extra_html_attributes' => 'data-remodal-target="modal-duplicate"',
						'footer_callback'       => array( $this, 'render_form' ),
					),
					$post_type
				);
			}
		}

		function _get_post_data( $post_id ) {
			if ( isset( $this->posts_data[ $post_id ] ) ) {
				return $this->posts_data[ $post_id ];
			}

			$this->posts_data[ $post_id ] = array(
				'post'       => get_post( $post_id, ARRAY_A ),
				'meta'       => get_post_custom( $post_id ),
				'taxonomies' => array(),
			);
			$taxonomies                   = get_object_taxonomies( $this->posts_data[ $post_id ]['post']['post_type'] );
			foreach ( $taxonomies as $taxonomy ) {
				$post_terms = wp_get_object_terms(
					$post_id,
					$taxonomy,
					array(
						'fields'                 => 'slugs',
						'update_term_meta_cache' => false,
					)
				);
				$this->posts_data[ $post_id ]['taxonomies'][ $taxonomy ] = $post_terms;
			}
			return $this->posts_data[ $post_id ];

		}

		function _duplicate_post( $post_id = null, $custom_post_data = array(), $extra_data = array() ) {

			if ( empty( $post_id ) ) {
				return new WP_Error( 'wpse', 'Empty $post_id' );
			}
			$post = apply_filters( 'vg_sheet_editor/duplicate/existing_post_data', $this->_get_post_data( $post_id ), $post_id, $extra_data );
			
			$post_data = $post['post'];
			$post_data = wp_parse_args( $custom_post_data, $post_data );

			$post_meta        = $post['meta'];
			$taxonomies_terms = $post['taxonomies'];

			if ( VGSE()->options['be_disable_post_actions'] ) {
				VGSE()->helpers->remove_all_post_actions( $post_data['post_type'] );
			}
			
			// We wont copy the ID and dates
			unset( $post_data['ID'] );
			unset( $post_data['post_date'] );
			unset( $post_data['post_modified'] );
			unset( $post_data['post_date_gmt'] );
			unset( $post_data['post_modified_gmt'] );

			$new_post_id = wp_insert_post( apply_filters( 'vg_sheet_editor/duplicate/new_post_data', $post_data, $extra_data ) );
			// Copy post metadata
			foreach ( $post_meta as $key => $values ) {

				if ( ! empty( $custom_post_data['meta_input'] ) && isset( $custom_post_data['meta_input'][ $key ] ) ) {
					continue;
				}
				foreach ( $values as $value ) {
					if ( $key === '_elementor_data' ) {
						$value = wp_slash( $value );
					}

					add_post_meta( $new_post_id, $key, maybe_unserialize( $value ) );
				}
			}
			foreach ( $taxonomies_terms as $taxonomy => $post_terms ) {
				wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
			}

			return $new_post_id;
		}

		function duplicate_post( $post_ids, $post_type, $rows ) {

			if ( empty( $_REQUEST['extra_data'] ) || strpos( $_REQUEST['extra_data'], 'duplicate_this' ) === false ) {
				return $post_ids;
			}
			parse_str( urldecode( html_entity_decode( $_REQUEST['extra_data'] ) ), $raw_extra_data );
			if ( empty( $raw_extra_data['duplicate_this'] ) ) {
				return $post_ids;
			}
			$extra_data = array(
				'duplicate_this'     => array_map( 'sanitize_text_field', array_filter( $raw_extra_data['duplicate_this'] ) ),
				'coupon_code_prefix' => isset( $raw_extra_data['coupon_code_prefix'] ) ? sanitize_text_field( $raw_extra_data['coupon_code_prefix'] ) : '',
			);
			foreach ( $extra_data['duplicate_this'] as $template_title ) {
				$template_id = VGSE()->helpers->_get_post_id_from_search( $template_title );

				if ( empty( $template_id ) || get_post_type( $template_id ) !== $post_type || ! VGSE()->helpers->user_can_edit_post_type( $post_type ) ) {
					return new WP_Error( 'wpse', sprintf( __( 'Template item with ID %d was not found or is not allowed to be duplicated.', 'vg_sheet_editor' ), $template_id ) );
				}

				for ( $i = 0; $i < $rows; $i++ ) {
					$new_post_id = apply_filters( 'vg_sheet_editor/duplicate/new_post_id', null, $template_id, $post_type, $extra_data );

					if ( ! is_int( $new_post_id ) ) {
						$new_post_id = $this->_duplicate_post(
							$template_id,
							array(
								'post_status' => 'draft',
								'post_title'  => get_the_title( $template_id ) . ' (Copy)',
							),
							$extra_data
						);
					}

					if ( is_int( $new_post_id ) ) {
						$post_ids[] = apply_filters( 'vg_sheet_editor/duplicate/final_post_id', $new_post_id, $template_id, $post_type, $extra_data );
					}
				}
			}

			return $post_ids;
		}

		function duplicate_woocommerce_product( $new_post_id, $template_id, $post_type ) {
			if ( $post_type === apply_filters( 'vg_sheet_editor/woocommerce/product_post_type_key', 'product' ) && class_exists( 'WooCommerce' ) && empty( $new_post_id ) ) {

				if ( ! class_exists( 'WC_Admin_Duplicate_Product' ) ) {
					include_once WC_ABSPATH . 'includes/admin/class-wc-admin-duplicate-product.php';
				}
				$duplicate        = new WC_Admin_Duplicate_Product();
				$template_product = wc_get_product( $template_id );

				if ( empty( $template_product ) ) {
					return $new_post_id;
				}

				$new_post    = $duplicate->product_duplicate( $template_product );
				$new_post_id = $new_post->get_id();
				// Polylang uses this hook to duplicate the translated variations
				do_action( 'woocommerce_product_duplicate', $new_post, $template_product );
			}
			return $new_post_id;
		}

		function __set( $name, $value ) {
			$this->$name = $value;
		}

		function __get( $name ) {
			return $this->$name;
		}

	}

	add_action( 'vg_sheet_editor/initialized', 'vgse_post_templates_init' );

	function vgse_post_templates_init() {
		WP_Sheet_Editor_Post_Templates::get_instance();
	}
}
