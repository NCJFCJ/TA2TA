<?php defined( 'ABSPATH' ) || exit;
if ( ! defined( 'VGSE_DEBUG' ) ) {
	define( 'VGSE_DEBUG', false );
}
if ( ! defined( 'VGSE_DIR' ) ) {
	define( 'VGSE_DIR', __DIR__ );
}
if ( ! defined( 'VGSE_KEY' ) ) {
	define( 'VGSE_KEY', 'vg_sheet_editor' );
}
if ( ! defined( 'VGSE_MAIN_FILE' ) ) {
	define( 'VGSE_MAIN_FILE', __FILE__ );
}
if ( ! defined( 'VGSE_CORE_MAIN_FILE' ) ) {
	define( 'VGSE_CORE_MAIN_FILE', __FILE__ );
}
require_once VGSE_DIR . '/inc/api/helpers.php';
$vgse_helpers = WP_Sheet_Editor_Helpers::get_instance();
$api          = $vgse_helpers->get_files_list( VGSE_DIR . '/inc/api' );
$teasers      = $vgse_helpers->get_files_list( VGSE_DIR . '/inc/teasers' );
$inc          = $vgse_helpers->get_files_list( VGSE_DIR . '/inc' );
$providers    = $vgse_helpers->get_files_list( VGSE_DIR . '/inc/providers' );
$integrations = $vgse_helpers->get_files_list( VGSE_DIR . '/inc/integrations' );

$files = array_merge( $api, $teasers, $inc, $providers, $integrations );
foreach ( $files as $file ) {
	require_once $file;
}

if ( ! class_exists( 'WP_Sheet_Editor' ) ) {

	class WP_Sheet_Editor {

		private $post_type;
		public $version     = '2.25.13';
		public $textname    = 'vg_sheet_editor';
		public $options_key = 'vg_sheet_editor';
		public $plugin_url  = null;
		public $logo_url    = null;
		public $plugin_dir  = null;
		public $options     = null;
		/**
		 * @var WP_Sheet_Editor_Data
		 */
		public $data_helpers = null;
		/**
		 * @var WP_Sheet_Editor_Helpers
		 */
		public $helpers            = null;
		public $registered_columns = null;
		public $toolbar            = null;
		public $columns            = null;
		public $WC                 = null;
		public $support_links      = array();
		public $extensions         = array();
		public $bundles            = array();
		public $buy_link           = null;
		private static $instance   = null;
		public $current_provider   = null;
		/**
		 * Registered editors instances grouped by provider key
		 * I.e. all the editors that use the "post" provider, would appear as enabled_post_types in the "post" WP_Sheet_Editor_Factory instance
		 *
		 * @var WP_Sheet_Editor_Factory[]
		 */
		public $editors   = array();
		public $user_path = array();
		/**
		 * When we execute ajax calls, we always store the deleted post IDs and return this field, so the JS can remove the rows from the sheet
		 *
		 * @var int[]
		 */
		public $deleted_rows_ids = array();

		/**
		 * Creates or returns an instance of this class.
		 *
		 *
		 */
		static function get_instance() {
			if ( null == self::$instance ) {
				self::$instance = new WP_Sheet_Editor();
			}
			return self::$instance;
		}

		function __set( $name, $value ) {
			$this->$name = $value;
		}

		function __get( $name ) {
			return $this->$name;
		}

		private function __construct() {

		}

		public static function allow_to_initialize() {
			if ( ! is_admin() && ! ( defined( 'WP_CLI' ) && WP_CLI ) && ! wp_doing_cron() && ! apply_filters( 'vg_sheet_editor/allowed_on_frontend', false ) ) {
				return false;
			}
			return true;
		}

		/**
		 * Plugin init
		 */
		function init() {
			do_action( 'vg_sheet_editor/before_initialized' );

			// Exit if frontend and it\'s not allowed
			if ( ! self::allow_to_initialize() ) {
				return;
			}

			// Disable WC's marketplace ads
			add_filter( 'woocommerce_allow_marketplace_suggestions', '__return_false' );

			$lang_path = str_replace( wp_normalize_path( WP_PLUGIN_DIR ) . '/', '', wp_normalize_path( __DIR__ . '/lang/' ) );
			load_plugin_textdomain( VGSE()->textname, false, $lang_path );

			// Init internal APIs
			$this->data_helpers = WP_Sheet_Editor_Data::get_instance();
			$this->helpers      = WP_Sheet_Editor_Helpers::get_instance();

			$this->buy_link = ( function_exists( 'vgse_freemius' ) ) ? vgse_freemius()->checkout_url() : 'https://wpsheeteditor.com';

			if ( defined( 'WPSE_PROFILE_WHATISSOSLOW' ) && class_exists( 'WPSE_WhatIsSoSlow' ) ) {
				WPSE_WhatIsSoSlow();
			}

			if ( ! empty( $_GET['wpse_hard_reset'] ) && ! empty( $_GET['wpse_nonce'] ) && VGSE()->helpers->user_can_manage_options() && wp_verify_nonce( $_GET['wpse_nonce'], 'wpse' ) ) {
				$this->on_uninstall();
				if ( ! headers_sent() ) {
					wp_redirect( remove_query_arg( array( 'wpse_hard_reset', 'wpse_nonce' ) ) );
					exit();
				}
			}
			if ( ! empty( $_GET['wpse_export_settings'] ) && ! empty( $_GET['wpse_nonce'] ) && VGSE()->helpers->user_can_manage_options() && wp_verify_nonce( $_GET['wpse_nonce'], 'wpse' ) ) {
				$this->export_settings();
			}

			$options         = get_option( $this->options_key );
			$default_options = array(
				'last_tab'                   => null,
				'be_post_types'              => array(
					'post',
				),
				'be_posts_per_page'          => 40,
				'be_load_items_on_scroll'    => 1,
				'be_fix_columns_left'        => 2,
				'be_posts_per_page_save'     => 8,
				'be_timeout_between_batches' => 6,
				'be_disable_post_actions'    => 0,
			);
			if ( empty( $options ) ) {
				$options = $default_options;
				update_option( $this->options_key, $options );
			} else {
				$options = wp_parse_args( $options, $default_options );
			}

			$this->options = $options;

			do_action( 'vg_sheet_editor/initialized' );

			$post_type       = $options['be_post_types'];
			$this->post_type = $post_type;

			$this->plugin_url = plugins_url( '/', __FILE__ );
			$this->plugin_dir = __DIR__;
			$this->logo_url   = apply_filters( 'vg_sheet_editor/logo_url', $this->plugin_url . 'assets/imgs/logo.svg' );

			$free_plugin_uri      = 'plugin-install.php?tab=search&type=term&s=';
			$free_plugin_base_url = ( is_multisite() ) ? network_admin_url( $free_plugin_uri ) : admin_url( $free_plugin_uri );
			$this->extensions     = apply_filters(
				'vg_sheet_editor/extensions',
				array(
					'users_lite'          => array(
						'title'               => __( 'Edit User Profiles in Spreadsheet - Basic', 'vg_sheet_editor' ),
						'icon'                => 'fa-users', // fa-search
						'image'               => '', // fa-search
						'description'         => __( '<p >Edit WordPress users in spreadsheet, edit only basic profiles and make basic searches.</p>', 'vg_sheet_editor' ), // incluir <p>
						'bundle'              => array( 'users' ),
						'class_function_name' => 'WP_Sheet_Editor_Users',
						'wp_org_slug'         => 'bulk-edit-user-profiles-in-spreadsheet',
						'post_types'          => array(),
						'extension_id'        => 24,
					),
					'users'               => array(
						'title'               => __( 'Edit User Profiles in Spreadsheet - FULL', 'vg_sheet_editor' ),
						'icon'                => 'fa-users', // fa-search
						'image'               => '', // fa-search
						'description'         => __( '<p >Edit WordPress users in spreadsheet. Edit FULL user profiles, including custom fields. Add new columns to the spreadsheet, Good for ecommerce stores, membership sites, events directories, business directories, user directories</p>', 'vg_sheet_editor' ), // incluir <p>
						'bundle'              => array( 'users' ),
						'class_function_name' => 'VGSE_USERS_IS_PREMIUM',
						'post_types'          => array(),
						'extension_id'        => 24,
					),
					'wc_customers'        => array(
						'title'               => __( 'WooCommerce Customers Spreadsheet', 'vg_sheet_editor' ),
						'icon'                => 'fa-users', // fa-search
						'image'               => '', // fa-search
						'description'         => __( '<p >View all your Customers in a Spreadsheet. View full profile, Edit Profiles Quickly, View Billing and Shipping information, Make advanced customer searches, Export Customers to Excel or Google Sheets, Import Customers from External Applications</p>', 'vg_sheet_editor' ), // incluir <p>
						'bundle'              => array( 'users' ),
						'class_function_name' => 'VGSE_USERS_IS_PREMIUM',
						'post_types'          => array(),
						'extension_id'        => 24,
					),
					'media_library'       => array(
						'title'                 => __( 'Media Library Spreadsheet', 'vg_sheet_editor' ),
						'icon'                  => 'fa-image', // fa-search
						'image'                 => '', // fa-search
						'description'           => __( '<p >View the image, videos, and all the files from the WP Media library in a spreadsheet. Edit all the file fields, including alt text, image captions, file descriptions, Advanced Search by any field , Auto generate alt text, captions, etc. using the parent post title or categorías, Update thousands of files at once, and more.</p>', 'vg_sheet_editor' ), // incluir <p>
						'inactive_action_url'   => 'https://wpsheeteditor.com/go/media-addon?utm_source=wp-admin&utm_medium=extensions-list&utm_campaign=media',
						'inactive_action_label' => __( 'Buy', 'vg_sheet_editor' ),
						'freemius_function'     => 'wpseml_freemius',
						'bundle'                => false,
						'class_function_name'   => 'VGSE_MEDIA_LIBRARY_IS_PREMIUM',
						'wp_org_slug'           => '',
						'post_types'            => array( 'attachment' ),
						'extension_id'          => 78,
					),
					'taxonomy_terms'      => array(
						'title'                 => __( 'Edit categories, tags, attributes in a spreadsheet', 'vg_sheet_editor' ),
						'icon'                  => 'fa-tags', // fa-search
						'image'                 => '', // fa-search
						'description'           => __( '<p >One spreadsheet for categories, tags, product attributes, event categories, portfolio categories, real state tags, etc. View and edit all the items in one place, copy paste, upload category images quickly, add descriptions, edit SEO, etc.</p>', 'vg_sheet_editor' ), // incluir <p>
						'inactive_action_url'   => 'https://wpsheeteditor.com/go/taxonomy-terms-addon?utm_source=wp-admin&utm_medium=extensions-list&utm_campaign=taxonomy-terms',
						'inactive_action_label' => __( 'Buy', 'vg_sheet_editor' ),
						'freemius_function'     => 'wpsett_fs',
						'bundle'                => false,
						'class_function_name'   => 'VGSE_TAXONOMY_TERMS_IS_PREMIUM',
						'wp_org_slug'           => 'bulk-edit-categories-tags',
						'post_types'            => array_unique(
							array_merge(
								get_taxonomies(
									array(
										'public'   => true,
										'show_ui'  => true,
										'_builtin' => true,
									),
									'names'
								),
								get_taxonomies(
									array(
										'show_ui'  => true,
										'_builtin' => false,
									),
									'names'
								)
							)
						),
						'extension_id'          => 19,
					),
					'woocommerce_coupons' => array(
						'title'                 => __( 'WooCommerce Coupons Spreadsheet', 'vg_sheet_editor' ),
						'icon'                  => 'fa-bullhorn', // fa-search
						'image'                 => '', // fa-search
						'description'           => __( '<p >View WooCommerce Coupons in a spreadsheet. Edit all coupon fields, Advanced Search by any field , Auto generate hundreds of coupons, Update hundreds of coupons at once, and more.</p>', 'vg_sheet_editor' ), // incluir <p>
						'inactive_action_url'   => 'https://wpsheeteditor.com/go/wc-coupons-addon?utm_source=wp-admin&utm_medium=extensions-list&utm_campaign=coupons',
						'inactive_action_label' => __( 'Buy', 'vg_sheet_editor' ),
						'freemius_function'     => 'wpsewcc_fs',
						'bundle'                => false,
						'class_function_name'   => 'VGSE_WC_COUPONS_IS_PREMIUM',
						'wp_org_slug'           => 'woo-coupons-bulk-editor',
						'post_types'            => array( 'shop_coupon' ),
						'extension_id'          => 21,
					),
					'woocommerce_orders'  => array(
						'title'                 => __( 'WooCommerce Orders Spreadsheet', 'vg_sheet_editor' ),
						'icon'                  => 'fa-shopping-cart', // fa-search
						'image'                 => '', // fa-search
						'description'           => __( '<p >View and dispatch all the Orders quickly. Advanced Search by any field (shipping method, taxes, VAT, payment methods, customers, products, etc), Export orders and customers information including guest customers; edit thousands of orders quickly, and more.</p>', 'vg_sheet_editor' ), // incluir <p>
						'inactive_action_url'   => 'https://wpsheeteditor.com/extensions/woocommerce-orders-spreadsheet/?utm_source=wp-admin&utm_medium=extensions-list&utm_campaign=orders',
						'inactive_action_label' => __( 'Buy', 'vg_sheet_editor' ),
						'freemius_function'     => 'wpsewco_fs',
						'bundle'                => false,
						'class_function_name'   => 'VGSE_WC_ORDERS_IS_PREMIUM',
						'wp_org_slug'           => '',
						'post_types'            => array( 'shop_order', $GLOBALS['wpdb']->prefix . 'wc_orders', 'shop_subscription' ),
						'extension_id'          => 79,
					),
					'comments'            => array(
						'title'                 => __( 'Comments, Reviews, and Order Notes Spreadsheet', 'vg_sheet_editor' ),
						'icon'                  => 'fa-comments', // fa-search
						'image'                 => '', // fa-search
						'description'           => __( '<p >It is the best way to manage your comments, WooCommerce customer reviews, event reviews, testimonials, and order notes in a spreadsheet. You can make advanced searches by any field (keyword, order note, status, find comments by post type, and all the fields). You can bulk edit them, delete all at once, export them to excel or external systems, import comments and reviews from other systems, and more.</p>', 'vg_sheet_editor' ), // incluir <p>
						'inactive_action_url'   => 'https://wpsheeteditor.com/go/comments-addon?utm_source=wp-admin&utm_medium=extensions-list&utm_campaign=comments',
						'inactive_action_label' => __( 'Buy', 'vg_sheet_editor' ),
						'freemius_function'     => 'wpsecr_fs',
						'bundle'                => false,
						'class_function_name'   => 'VGSE_COMMENTS_IS_PREMIUM',
						'wp_org_slug'           => '',
						'post_types'            => array( 'comments' ),
						'extension_id'          => 81,
					),
					'custom_tables'       => array(
						'title'                 => __( 'Custom Database Tables Spreadsheet', 'vg_sheet_editor' ),
						'icon'                  => 'fa-table', // fa-search
						'image'                 => '', // fa-search
						'description'           => __( '<p >One Spreadsheet for Every Custom Database Table added by other plugins. Live edit in the cells, Bulk Edit, Make advanced searches by any field, export and import, bulk delete, move information between sites, edit thousands of items, and more.</p>', 'vg_sheet_editor' ), // incluir <p>
						'inactive_action_url'   => 'https://wpsheeteditor.com/extensions/custom-database-tables-spreadsheet/?utm_source=wp-admin&utm_medium=extensions-list&utm_campaign=custom-tables',
						'inactive_action_label' => __( 'Buy', 'vg_sheet_editor' ),
						'freemius_function'     => 'wpsect_fs',
						'bundle'                => false,
						'class_function_name'   => 'VGSE_CUSTOM_TABLES_IS_PREMIUM',
						'wp_org_slug'           => '',
						// xxxxx because we don't know the keys of custom tables yet
						'post_types'            => array( 'xxxxxx' ),
						'extension_id'          => 95,
					),
					'edd'                 => array(
						'title'                 => __( 'Easy Digital Downloads Spreadsheet', 'vg_sheet_editor' ),
						'icon'                  => 'fa-download', // fa-search
						'image'                 => '', // fa-search
						'description'           => __( '<p >View all the EDD products in a spreadsheet, create downloads and files in bulk, edit hundreds of products at once using formulas, Advanced searches using multiple fields, etc.</p>', 'vg_sheet_editor' ), // incluir <p>
						'inactive_action_url'   => 'https://wpsheeteditor.com/go/edd-downloads-addon?utm_source=wp-admin&utm_medium=extensions-list&utm_campaign=edd',
						'inactive_action_label' => __( 'Buy', 'vg_sheet_editor' ),
						'freemius_function'     => 'wpseedd_fs',
						'bundle'                => false,
						'class_function_name'   => 'VGSE_EDD_DOWNLOADS_IS_PREMIUM',
						'wp_org_slug'           => 'wp-sheet-editor-edd-downloads',
						'post_types'            => array( 'download' ),
						'extension_id'          => 18,
					),
					'events'              => array(
						'title'                 => __( 'Events Spreadsheet', 'vg_sheet_editor' ),
						'icon'                  => 'fa-ticket', // fa-search
						'image'                 => '', // fa-search
						'description'           => __( '<p >View all the events in a spreadsheet, create events in bulk, edit hundreds of events at once using formulas, Advanced searches using multiple event fields, etc.</p>', 'vg_sheet_editor' ), // incluir <p>
						'inactive_action_url'   => 'https://wpsheeteditor.com/go/events-addon?utm_source=wp-admin&utm_medium=extensions-list&utm_campaign=events',
						'inactive_action_label' => __( 'Buy', 'vg_sheet_editor' ),
						'freemius_function'     => 'wpsee_fs',
						'bundle'                => false,
						'class_function_name'   => 'VGSE_EVENTS_IS_PREMIUM',
						'wp_org_slug'           => 'bulk-edit-events',
						'post_types'            => array( 'tribe_events', 'tribe_organizer', 'tribe_venue' ),
						'extension_id'          => 22,
					),
					'give'                => array(
						'title'                 => __( 'Give Spreadsheet', 'vg_sheet_editor' ),
						'icon'                  => 'fa-gift',
						'image'                 => '',
						'description'           => __( '<p>This includes spreadsheets to manage Donation Forms, Donations, and Donors. You can bulk edit, export, import, and quickly manage your donations website created with GiveWP</p>', 'vg_sheet_editor' ), // incluir <p>
						'inactive_action_url'   => 'https://wpsheeteditor.com/extensions/givewp-donations-spreadsheet/?utm_source=wp-admin&utm_medium=extensions-list&utm_campaign=givewp',
						'inactive_action_label' => __( 'Buy', 'vg_sheet_editor' ),
						'freemius_function'     => 'wpsegwf_fs',
						'bundle'                => false,
						'class_function_name'   => 'VGSE_GIVEWP_IS_PREMIUM',
						'wp_org_slug'           => '',
						'post_types'            => array( 'give_forms', 'give_payment', $GLOBALS['wpdb']->prefix . 'give_donors' ),
						'extension_id'          => 129,
					),
					'frontend_editor'     => array(
						'title'                 => __( 'Display the spreadsheet editor in the frontend', 'vg_sheet_editor' ),
						'icon'                  => 'fa-rocket', // fa-search
						'image'                 => '', // fa-search
						'description'           => __( '<p >Create new spreadsheets with custom columns and Share the Spreadsheets with your Users or Employees on the Frontend. Useful for marketplaces where vendors should edit products in the spreadsheet, allow post or event submissions on the Frontend, events directories, Web Apps, Custom Dashboards, etc.</p>', 'vg_sheet_editor' ), // incluir <p>
						'inactive_action_url'   => 'https://wpsheeteditor.com/go/frontend-addon?utm_source=wp-admin&utm_medium=extensions-list&utm_campaign=frontend',
						'inactive_action_label' => __( 'Buy', 'vg_sheet_editor' ),
						'freemius_function'     => 'bepof_fs',
						'bundle'                => false,
						'class_function_name'   => 'VGSE_FRONTEND_IS_PREMIUM',
						'post_types'            => array(),
						'extension_id'          => 25,
					),
					'woocommerce'         => array(
						'title'               => __( 'WooCommerce - Products Integration', 'vg_sheet_editor' ),
						'icon'                => 'fa-shopping-cart', // fa-search
						'image'               => '', // fa-search
						'description'         => __( '<p >Edit WooCommerce products in the spreadsheet. It supports all kinds of products, including Variable Products, Downloadable Products, External Products, Simple Products. You can edit all product fields in the spreadsheet, including attributes, images, etc.</p>', 'vg_sheet_editor' ), // incluir <p>
				//      //'status' => __('Included in "Pro Bundle".', 'vg_sheet_editor' ), // vacío  o installed
						'bundle'              => array( 'custom_post_types' ),
						'class_function_name' => 'WP_Sheet_Editor_WooCommerce',
						'wp_org_slug'         => 'woo-bulk-edit-products',
						'post_types'          => array( 'product' ),
						'extension_id'        => 17,
					),
					'custom_post_types'   => array(
						'title'               => __( 'Custom post types', 'vg_sheet_editor' ),
						'icon'                => 'fa-file', // fa-search
						'image'               => '', // fa-search
						'description'         => __( '<p >Edit restaurant menus, courses, projects, portfolios, and all custom post types.</p>', 'vg_sheet_editor' ), // incluir <p>
				//      //'status' => __('Included in "Pro Bundle".', 'vg_sheet_editor' ), // vacío  o installed
						'bundle'              => array( 'custom_post_types' ),
						'class_function_name' => 'WP_Sheet_Editor_CPTs',
						'post_types'          => array(),
					),
					'columns_renaming'    => array(
						'title'                 => __( 'Columns renaming', 'vg_sheet_editor' ),
						'icon'                  => 'fa-exchange', // fa-search
						'image'                 => '', // fa-search
						'description'           => __( '<p >You can rename the columns of the spreadsheet.<br>Example. Instead of showing “Post author” on the spreadsheet, you can change it to “Uploaded by”.</p>', 'vg_sheet_editor' ), // incluir <p>
						'inactive_action_label' => __( 'Install for Free', 'vg_sheet_editor' ),
						'inactive_action_url'   => $free_plugin_base_url . 'wp-sheet-editor-columns-renaming',
						//      'status' => __('Free.', 'vg_sheet_editor' ), // vacío  o installed
														'bundle' => false,
						'class_function_name'   => 'WP_Sheet_Editor_Columns_Renaming',
						'post_types'            => array(),
					),
					'yoast'               => array(
						'title'                 => __( 'YOAST SEO', 'vg_sheet_editor' ),
						'icon'                  => 'fa-google', // fa-search
						'image'                 => '', // fa-search
						'description'           => __( '<p >Edit SEO title, description, keyword, and SEO score in spreadsheet</p>', 'vg_sheet_editor' ), // incluir <p>
						'inactive_action_label' => __( 'Install for Free', 'vg_sheet_editor' ),
						'inactive_action_url'   => $free_plugin_base_url . 'wp-sheet-editor-yoast-seo',
						//      'status' => __('Free.', 'vg_sheet_editor' ), // vacío  o installed
														'bundle' => false,
						'class_function_name'   => 'WP_Sheet_Editor_YOAST_SEO',
						'post_types'            => array(),
					),
					'advanced_filters'    => array(
						'title'               => __( 'Advanced Search', 'vg_sheet_editor' ),
						'icon'                => 'fa-search', // fa-search
						'image'               => '', // fa-search
						'description'         => __( '<p >Find posts by keyword, taxonomies, author, date, status, or custom fields.</p><p>Search in multiple fields with advanced operators: =, !=, &lt;, &gt;, LIKE, NOT LIKE</p><p>Examples: Find products from category Audio with stock < 20, or products from category Apple without featured image, or products containing the keyword "Google" without image gallery.</p>', 'vg_sheet_editor' ), // incluir <p>
						//'status' => __('Included in "Pro Bundle".', 'vg_sheet_editor' ), // vacío  o installed
						'bundle'              => array( 'users', 'custom_post_types' ),
						'class_function_name' => 'WP_Sheet_Editor_Advanced_Filters',
						'post_types'          => array(),
					),
					'replace_formulas'    => array(
						'title'               => __( 'Formulas', 'vg_sheet_editor' ),
						'icon'                => 'fa fa-pencil-square', // fa-search
						'image'               => '', // fa-search
						'description'         => __( '<p >Edit Hundreds of Posts at Once with just a few clicks. Search and replace, Replace urls and phrases, save values to fields in bulk, copy values between fields, merge fields, etc..</p><p>Examples: Copy regular price to sale price, update product attributes names, etc.</p>', 'vg_sheet_editor' ), // incluir <p>
				//      //'status' => __('Included in "Pro Bundle".', 'vg_sheet_editor' ), // vacío  o installed
						'bundle'              => array( 'users', 'custom_post_types' ),
						'class_function_name' => 'WP_Sheet_Editor_Formulas',
						'post_types'          => array(),
					),
					'math_formulas'       => array(
						'title'               => __( 'Math Formulas', 'vg_sheet_editor' ),
						'icon'                => 'fa-hashtag', // fa-search
						'image'               => '', // fa-search
						'description'         => __( '<p >Edit Hundreds of Posts at Once. Update numeric fields using advanced math formulas. Example, increase prices by 10% , manage inventory , etc. Run any math formula.</p><p>You can use multiple fields in the formula, for example, "Regular price x Inventory / Sales price</p>', 'vg_sheet_editor' ), // incluir <p>
						//'status' => __('Included in "Pro Bundle".', 'vg_sheet_editor' ), // vacío  o installed
						'bundle'              => array( 'users', 'custom_post_types' ),
						'class_function_name' => 'WP_Sheet_Editor_Formulas',
						'post_types'          => array(),
					),
					'acf'                 => array(
						'title'               => __( 'Advanced Custom Fields', 'vg_sheet_editor' ),
						'icon'                => 'fa-files-o', // fa-search
						'image'               => '', // fa-search
						'description'         => __( '<p >Advanced Custom Fields metaboxes appear in the Spreadsheet Automatically. So you can edit custom fields easily.</p>', 'vg_sheet_editor' ), // incluir <p>
						//'status' => __('Included in "Pro Bundle".', 'vg_sheet_editor' ), // vacío  o installed
						'bundle'              => array( 'users', 'custom_post_types' ),
						'class_function_name' => 'WP_Sheet_Editor_ACF',
						'post_types'          => array(),
					),
					'custom_columns'      => array(
						'title'               => __( 'Edit Custom Fields in Spreadsheet', 'vg_sheet_editor' ),
						'icon'                => 'fa-plus', // fa-search
						'image'               => '', // fa-search
						'description'         => __( '<p >You can create columns for custom fields. <br>Edit page settings added by your theme, event details, products information, etc.</p>', 'vg_sheet_editor' ), // incluir <p>
						//'status' => __('Included in "Pro Bundle".', 'vg_sheet_editor' ), // vacío  o installed
						'bundle'              => array( 'users', 'custom_post_types' ),
						'class_function_name' => 'WP_Sheet_Editor_Custom_Columns',
						'post_types'          => array(),
					),
					'posts'               => array(
						'title'               => __( 'Edit Posts and Pages in Spreadsheet', 'vg_sheet_editor' ),
						'icon'                => 'fa-table', // fa-search
						'image'               => '', // fa-search
						'description'         => __( '<p >Edit default post fields in spreadsheet.</p>', 'vg_sheet_editor' ), // incluir <p>
				//                  'bundle' => array('custom_post_types'),
						'class_function_name' => 'WP_Sheet_Editor_Dist',
						'wp_org_slug'         => 'wp-sheet-editor-bulk-spreadsheet-editor-for-posts-and-pages',
						'post_types'          => array(), // We can't add post types here, they are added in $this->bundles.
						'extension_id'        => 20,
					),
					'wc_lite'             => array(
						'title'               => __( 'WooCommerce - BASIC integration', 'vg_sheet_editor' ),
						'icon'                => 'fa-table', // fa-search
						'image'               => '', // fa-search
						'description'         => __( '<p>You can edit simple products only. Available columns: title, url, description, date, SKU, regular price, sale price, stock status, manage stock, stock quantity.</p><p>More columns and product types available as premium extension.</p>', 'vg_sheet_editor' ), // incluir <p>
				//                  'bundle' => array('custom_post_types'),
						'class_function_name' => 'WP_Sheet_Editor_Dist',
						'wp_org_slug'         => 'woo-bulk-edit-products',
						'post_types'          => array( 'product' ),
						'extension_id'        => 17,
					),
					'columns_visibility'  => array(
						'title'               => __( 'Columns visibility', 'vg_sheet_editor' ),
						'icon'                => 'fa-cog', // fa-search
						'image'               => '', // fa-search
						'description'         => __( '<p >You can show, hide, and sort columns in the spreadsheet.</p>', 'vg_sheet_editor' ), // incluir <p>
						'bundle'              => false,
						'class_function_name' => 'WP_Sheet_Editor_Columns_Visibility',
						'post_types'          => array(),
					),
					'autofill'            => array(
						'title'               => __( 'Autofill cells', 'vg_sheet_editor' ),
						'icon'                => '', // fa-search
						'image'               => '<img src="' . VGSE()->plugin_url . 'assets/imgs/drag-down-autofill-demo.gif" style="max-height: 65px;">', // fa-search
						'description'         => __( '<p >You can auto fill cells (copy) by dragging the cell corner into other cells, as you can do in excel.</p>', 'vg_sheet_editor' ), // incluir <p>
						'button_label'        => __( 'Install for Free', 'vg_sheet_editor' ),
						'button_url'          => $free_plugin_base_url . 'wp-sheet-editor-autofill',
						'bundle'              => false,
						'class_function_name' => 'WP_Sheet_Editor_Autofill_Cells',
						'post_types'          => array(),
					),
					'basic_filters'       => array(
						'title'               => __( 'Basic search', 'vg_sheet_editor' ),
						'icon'                => 'fa-search', // fa-search
						'description'         => __( '<p >Search in the spreadsheet. Find posts by keyword, status, and author.</p>', 'vg_sheet_editor' ), // incluir <p>
				//      'button_label' => __('Install for Free', 'vg_sheet_editor' ),
				//      'button_url' => $free_plugin_base_url . 'wp-sheet-editor-autofill',
						'bundle'              => false,
						'class_function_name' => 'WP_Sheet_Editor_Filters',
						'post_types'          => array(),
					),
					'columns_resizing'    => array(
						'title'               => __( 'Columns resizing', 'vg_sheet_editor' ),
						'icon'                => 'fa-arrows-h', // fa-search
						'description'         => __( '<p >Resize columns in the spreadsheet and save it for future sessions.</p>', 'vg_sheet_editor' ), // incluir <p>
						'bundle'              => false,
						'class_function_name' => 'VGSE_Columns_Resizing',
						'post_types'          => array(),
					),
					'post_templates'      => array(
						'title'                 => __( 'Duplicate (Tool)', 'vg_sheet_editor' ),
						'icon'                  => 'fa-copy', // fa-search
						'description'           => __( '<p >Add a "duplicate" tool to the spreadsheet. You can select one row (post, product, coupon, etc.) and create a lot of copies.</p><p>Example. Create 100 products with the same tags, dimensions, attributes, and variations. And only change a couple of fields manually.</p>', 'vg_sheet_editor' ), // incluir <p>
						'inactive_action_label' => __( 'Install for Free', 'vg_sheet_editor' ),
						'inactive_action_url'   => $free_plugin_base_url . 'wp-sheet-editor-post-templates',
						'bundle'                => false,
						'class_function_name'   => 'WP_Sheet_Editor_Post_Templates',
						'post_types'            => array(),
					),
				)
			);

			$this->bundles = apply_filters(
				'vg_sheet_editor/extensions/bundles',
				array(
					'custom_post_types' => array(
						'name'                  => __( 'Everything you need for All Posts Types and Products', 'vg_sheet_editor' ),
						'old_price'             => '99.99',
						'price'                 => '49.99',
						'percentage_off'        => 50,
						'coupon'                => null,
						'extensions'            => array(),
						'inactive_action_url'   => 'https://wpsheeteditor.com/buy-extension/?extension_id=886&utm_source=wp-admin&utm_medium=extensions-list&utm_campaign=posts-bundle',
						'inactive_action_label' => __( 'Buy bundle', 'vg_sheet_editor' ),
						'freemius_function'     => 'vgse_freemius',
						'wp_org_slug'           => 'wp-sheet-editor-bulk-spreadsheet-editor-for-posts-and-pages',
						'post_types'            => array(),
					),
					'users'             => array(
						'name'                  => __( 'Everything you need for Users and Customers', 'vg_sheet_editor' ),
						'old_price'             => '99.99',
						'price'                 => '49.99',
						'percentage_off'        => 50,
						'coupon'                => null,
						'extensions'            => array(),
						'inactive_action_url'   => 'https://wpsheeteditor.com/go/users-addon?utm_source=wp-admin&utm_medium=extensions-list&utm_campaign=users-bundle',
						'inactive_action_label' => __( 'Buy bundle', 'vg_sheet_editor' ),
						'freemius_function'     => 'beupis_fs',
						'wp_org_slug'           => 'bulk-edit-user-profiles-in-spreadsheet',
						'post_types'            => array( 'user' ),
					),
				)
			);
			// We define the post types of the bundle here because all the other extensions
			// and bundles are needed by get_post_types_without_own_sheet()
			$this->bundles['custom_post_types']['post_types'] = array_merge( array( 'post', 'page' ), array_keys( VGSE()->helpers->get_post_types_without_own_sheet() ) );

			// Check if the extension is active
			foreach ( $this->bundles as $index => $bundle ) {
				$freemius = function_exists( $bundle['freemius_function'] ) ? $bundle['freemius_function']() : null;
				if ( $freemius ) {
					$this->bundles[ $index ]['inactive_action_url'] = $freemius->checkout_url();
				}
			}
			foreach ( $this->extensions as $index => $extension ) {
				$this->extensions[ $index ]['is_active'] = ! empty( $extension['class_function_name'] ) && ( class_exists( $extension['class_function_name'] ) || function_exists( $extension['class_function_name'] ) || defined( $extension['class_function_name'] ) );

				if ( ! empty( $extension['freemius_function'] ) && function_exists( $extension['freemius_function'] ) ) {
					$this->extensions[ $index ]['active_action_url']  = $extension['freemius_function']()->checkout_url();
					$this->extensions[ $index ]['is_any_mode_active'] = true;
				}

				$this->extensions[ $index ]['has_paid_offering'] = ! empty( $extension['bundle'] ) || ! empty( $extension['freemius_function'] );

				if ( ! empty( $extension['bundle'] ) ) {
					$bundle = $this->bundles[ current( $extension['bundle'] ) ];
					$this->extensions[ $index ]['active_action_url']     = ( isset( $bundle['active_action_url'] ) ) ? $bundle['active_action_url'] : '';
					$this->extensions[ $index ]['inactive_action_label'] = ( isset( $bundle['inactive_action_label'] ) ) ? $bundle['inactive_action_label'] : '';
					$this->extensions[ $index ]['active_action_label']   = ( isset( $bundle['active_action_label'] ) ) ? $bundle['active_action_label'] : '';
					$this->extensions[ $index ]['freemius_function']     = ( isset( $bundle['freemius_function'] ) ) ? $bundle['freemius_function'] : '';

					$freemius = function_exists( $bundle['freemius_function'] ) ? $bundle['freemius_function']() : null;
					if ( $freemius ) {
						$this->extensions[ $index ]['inactive_action_url'] = $freemius->checkout_url();
					}
				}
			}
			do_action( 'vg_sheet_editor/after_extensions_registered' );

			if ( ! defined( 'VGSE_ANY_PREMIUM_ADDON' ) ) {
				define( 'VGSE_ANY_PREMIUM_ADDON', (bool) VGSE()->helpers->has_paid_addon_active() );
			}

			if ( ! empty( VGSE()->options['be_disable_extension_offerings'] ) ) {
				add_filter( 'vg_sheet_editor/extensions/is_toolbar_allowed', '__return_false' );
				add_filter( 'vg_sheet_editor/extensions/is_page_allowed', '__return_false' );
			}

			// Init wp hooks
			add_action( 'admin_menu', array( $this, 'register_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ), 999 );
			// Gutenberg
			add_action( 'admin_footer', array( $this, 'register_script_for_metabox_iframes' ) );
			add_action( 'add_meta_boxes', array( $this, 'remove_all_metaboxes_in_gutenberg' ), 99, 2 );

			add_action( 'admin_enqueue_scripts', array( $this, 'register_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_media_wp_media' ) );
			add_action( 'admin_init', array( $this, 'redirect_to_whats_new_page' ) );
			add_action( 'wp_dashboard_setup', array( $this, 'register_dashboard_widgets' ) );
			add_filter( 'wp_kses_allowed_html', array( $this, 'allow_iframes_in_html' ), 10, 2 );

			VGSE()->options['be_allowed_user_roles'] = ( empty( VGSE()->options['be_allowed_user_roles'] ) || ! is_array( VGSE()->options['be_allowed_user_roles'] ) ) ? array() : array_filter( VGSE()->options['be_allowed_user_roles'] );

			// Don't apply the role restrictions if the user can install plugins
			if ( ! empty( VGSE()->options['be_allowed_user_roles'] ) && ! current_user_can( 'install_plugins' ) ) {
				if ( ! is_user_logged_in() ) {
					add_filter( 'vg_sheet_editor/use_rest_api_only', '__return_true' );
				} else {
					$user = get_userdata( get_current_user_id() );
					if ( ! array_intersect( $user->roles, VGSE()->options['be_allowed_user_roles'] ) ) {
						add_filter( 'vg_sheet_editor/use_rest_api_only', '__return_true' );
					}
				}
			}
			$rest_api_only = apply_filters( 'vg_sheet_editor/use_rest_api_only', ! empty( VGSE()->options['be_rest_api_only'] ) );
			if ( ! empty( $rest_api_only ) ) {
				VGSE()->options['be_disable_dashboard_widget'] = true;
				// High priority because the parent plugins use the filter to activate the admin pages
				add_filter( 'vg_sheet_editor/register_admin_pages', '__return_false', 99999 );
				add_filter( 'vg_sheet_editor/bootstrap/settings', array( $this, 'disable_admin_menus_when_api_only' ), 99 );
			}

			// After all extensions are loaded
			add_action( 'vg_sheet_editor/after_init', array( $this, 'after_init' ), 999 );

			WP_Sheet_Editor_Ajax_Obj();

			do_action( 'vg_sheet_editor/after_init' );

			// clear internal caches
			add_action( 'created_term', array( $this, 'clear_cache_after_term_created' ), 10, 3 );
			add_filter( 'wp_update_term_data', array( $this, 'clear_cache_after_term_edited' ), 10, 4 );
			add_action( 'delete_term', array( $this, 'clear_cache_after_term_deleted' ), 10, 3 );
			add_action( 'user_register', array( $this, 'clear_cache_after_user_created' ), 10, 1 );
			add_action( 'vg_sheet_editor/on_uninstall', array( $this, 'on_uninstall' ) );
			add_action( 'admin_page_access_denied', array( $this, 'catch_license_page_error' ) );
			$this->maybe_auto_enable_sheet();
		}

		function maybe_auto_enable_sheet() {
			if ( empty( $_GET['wpse_auto_enable_sheet'] ) || ! is_admin() || ! VGSE()->helpers->user_can_manage_options() || ! VGSE()->helpers->is_editor_page() || ! VGSE()->helpers->verify_nonce_from_request( '_wpnonce' ) ) {
				return;
			}

			$post_types        = VGSE()->get_option( 'be_post_types' );
			$current_sheet_key = VGSE()->helpers->get_provider_from_query_string( false );
			if ( $current_sheet_key && ! in_array( $current_sheet_key, $post_types, true ) ) {
				$post_types[] = $current_sheet_key;
				VGSE()->update_option( 'be_post_types', $post_types );
				$redirect_to = esc_url_raw( remove_query_arg( array( '_wpnonce', 'wpse_auto_enable_sheet' ) ) );
				wp_safe_redirect( $redirect_to );
				exit();
			}
		}

		function catch_license_page_error() {
			if ( empty( $_POST ) && ! empty( $_GET['page'] ) && preg_match( '/^(wpse|vgse|vg_sheet_editor)/', $_GET['page'] ) && strpos( $_GET['page'], '-account' ) !== false ) {
				$message = sprintf(
					__(
						'<h1>WP Sheet Editor</h1>
                <p>Do you want to install the premium plugin that you purchased? Follow these steps:</p>
                <ol>
                    <li>If you were using the free version of the plugin before the purchase, you need to uninstall the free version now</li>
                    <li>When you purchased the plugin, you received an email with the download link and license key</li>
                    <li>Now go to <a href="%1$s" target="_blank">this page</a> and click on the "Upload" button at the top</li>
                    <li>Upload the premium zip file</li>
                    <li>Activate the plugin</li>
                    <li>You will see a screen asking for a license, enter your license key</li>
                    <li>Done. Now you should see the welcome page where you can set up the plugin and start using it</li>
                </ol>
                <p>If you need help, you can <a href="%2$s" target="_blank">contact us</a></p>',
						'vg_sheet_editor'
					),
					esc_url( admin_url( 'plugin - install . php' ) ),
					VGSE()->get_support_links( 'contact_us', 'url', 'license - page - error' )
				);
				wp_die( $message );
			}
		}

		function get_option( $key, $default = null ) {
			$out = null;
			if ( empty( $this->options ) ) {
				$this->options = get_option( $this->options_key );
			}
			if ( isset( $this->options[ $key ] ) ) {
				$out = $this->options[ $key ];
			}

			if ( empty( $out ) && is_object( VGSE()->helpers ) && empty( $default ) ) {
				$registered_settings = WP_Sheet_Editor_Ajax_Obj()->get_registered_settings();
				if ( isset( $registered_settings[ $key ] ) && ! empty( $registered_settings[ $key ]['default'] ) ) {
					$default = $registered_settings[ $key ]['default'];
				}
			}

			if ( ! is_null( $default ) && empty( $out ) ) {
				$out = $default;
			}
			return $out;
		}
		function update_option( $key, $value ) {
			$new_options = WP_Sheet_Editor_Ajax_Obj()->_sanitize_general_options(
				array(
					$key => $value,
				)
			);
			// If this field is not registered, automatically sanitize as text field
			if ( empty( $new_options ) ) {
				$new_options = array(
					sanitize_text_field( $key ) => sanitize_text_field( $value ),
				);
			}
			$existing_options = get_option( $this->options_key );
			$final_options    = wp_parse_args( $new_options, $existing_options );
			update_option( $this->options_key, $final_options, false );

			$this->options = wp_parse_args( $new_options, $this->options );
		}

		function get_exportable_settings_keys() {

			$option_keys_like  = array(
				'vgse_detected_fields',
				'vgse_all_meta_keys_',
				'vgse_columns_visibility',
				'vgse_variation_meta_keys',
			);
			$option_keys_equal = array(
				'vgse_welcome_redirect',
				'vgse_hide_extensions_popup',
				'vgse_disable_quick_setup',
				'vgse_dismiss_review_tip',
				'vgse_post_type_setup_done',
				'vg_sheet_editor',
				'vgse_column_groups',
				'vgse_saved_exports',
				'vgse_removed_columns',
				'vg_sheet_editor_custom_columns',
				'vgse_favorite_search_fields',
				'vg_sheet_editor_custom_post_types',
				'vgse_saved_searches',
				'vgse_columns_manager',
			);
			$out               = array(
				'like'  => $option_keys_like,
				'equal' => $option_keys_equal,
			);
			return $out;
		}

		function export_settings() {
			global $wpdb;

			if ( ! VGSE()->helpers->user_can_manage_options() ) {
				return;
			}

			$exportable_keys   = $this->get_exportable_settings_keys();
			$option_keys_like  = $exportable_keys['like'];
			$option_keys_equal = $exportable_keys['equal'];

			$out = array();
			foreach ( $option_keys_like as $option_key ) {
				$out = array_merge( $out, $wpdb->get_results( $wpdb->prepare( "SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE %s ", '%' . $wpdb->esc_like( $option_key ) . '%' ), ARRAY_A ) );
			}
			foreach ( $option_keys_equal as $option_key ) {
				$out = array_merge( $out, $wpdb->get_results( $wpdb->prepare( "SELECT option_name, option_value FROM $wpdb->options WHERE option_name = %s ", $option_key ), ARRAY_A ) );
			}
			$final     = array_map( 'maybe_unserialize', wp_list_pluck( $out, 'option_value', 'option_name' ) );
			$file_path = WP_CONTENT_DIR . '/uploads/wp-sheet-editor-settings-' . wp_generate_password( 12, false, false ) . '.json';
			file_put_contents( $file_path, json_encode( $final, JSON_PRETTY_PRINT ) );

			header( 'Content-type: application/json' );
			header( 'Content-disposition: attachment; filename = ' . basename( $file_path ) );
			VGSE()->helpers->readfile_chunked( $file_path );
			unlink( $file_path );
			die();
		}

		function on_uninstall() {
			global $wpdb;

			// Delete unnecessary info from the DB
			$option_keys_like  = array(
				'vgse_user_path',
				'vgse_hide_whats_new',
				'vgse_dismiss_review_tip',
				'vgse_post_type_setup_done',
				'vgse_detected_fields',
				'vgse_all_meta_keys_',
			);
			$option_keys_equal = array(
				'vgse_welcome_redirect',
				'vgse_hide_extensions_popup',
				'vgse_variation_meta_keys',
				'vgse_disable_quick_setup',
				'vgse_dismiss_review_tip',
				'vgse_columns_visibility_migrated',
				'vgse_post_type_setup_done',
				'vgse_welcome_redirect',
				'vgse_favorite_search_fields',
				'vgse_saved_searches',
				'vgse_json_fields',
			);
			// We no longer remove the key vg_sheet_editor because it
			// caused issues when using the frontend sheet, the enabled post types
			// might be removed and break the frontend sheet
			if ( ! empty( $_GET['wpse_hard_reset'] ) ) {
				$option_keys_equal[] = 'vg_sheet_editor';
				$option_keys_equal[] = 'vgse_column_groups';
			}

			foreach ( $option_keys_like as $option_key ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name LIKE %s", '%' . $wpdb->esc_like( $option_key ) . '%' ) );
			}
			foreach ( $option_keys_equal as $option_key ) {
				delete_option( $option_key );
			}

			$sheet_sessions_key = $wpdb->prefix . 'wpse_sheet_sessions';
			delete_user_meta( get_current_user_id(), $sheet_sessions_key );
		}

		function clear_cache_after_user_created( $user_id ) {
			wp_cache_delete( 'wpse_authors' . (int) true );
			wp_cache_delete( 'wpse_authors' . (int) false );
		}

		function clear_cache_after_term_deleted( $term_id, $tt_id, $taxonomy ) {
			$cache_key_name = apply_filters( 'vg_sheet_editor/data/taxonomy_terms/cache_key', 'wpse_terms_' . $taxonomy . 'name', $taxonomy, '' );
			$cache_key_slug = apply_filters( 'vg_sheet_editor/data/taxonomy_terms/cache_key', 'wpse_terms_' . $taxonomy . 'slug', $taxonomy, '' );
			delete_transient( $cache_key_name );
			delete_transient( $cache_key_slug );
		}
		function clear_cache_after_term_edited( $data, $term_id, $taxonomy, $args ) {
			$term                              = get_term_by( 'id', $term_id, $taxonomy );
			$fields_that_require_cache_cleanup = array( 'name', 'parent', 'taxonomy' );

			$must_cleanup = false;
			foreach ( $fields_that_require_cache_cleanup as $field_that_require_cache_cleanup ) {
				if ( ! empty( $args[ $field_that_require_cache_cleanup ] ) && $args[ $field_that_require_cache_cleanup ] !== $term->$field_that_require_cache_cleanup ) {
					$must_cleanup = true;
					break;
				}
			}
			if ( $must_cleanup ) {
				$cache_key_name = apply_filters( 'vg_sheet_editor/data/taxonomy_terms/cache_key', 'wpse_terms_' . $taxonomy . 'name', $taxonomy, '' );
				$cache_key_slug = apply_filters( 'vg_sheet_editor/data/taxonomy_terms/cache_key', 'wpse_terms_' . $taxonomy . 'slug', $taxonomy, '' );
				delete_transient( $cache_key_name );
				delete_transient( $cache_key_slug );
			}
			return $data;
		}

		function clear_cache_after_term_created( $term_id, $tt_id, $taxonomy ) {
			$cache_key_name = apply_filters( 'vg_sheet_editor/data/taxonomy_terms/cache_key', 'wpse_terms_' . $taxonomy . 'name', $taxonomy, '' );
			$cache_key_slug = apply_filters( 'vg_sheet_editor/data/taxonomy_terms/cache_key', 'wpse_terms_' . $taxonomy . 'slug', $taxonomy, '' );
			delete_transient( $cache_key_name );
			delete_transient( $cache_key_slug );
		}

		function disable_admin_menus_when_api_only( $settings ) {
			$settings['register_admin_menus'] = false;
			return $settings;
		}

		function allow_iframes_in_html( $tags, $context ) {
			if ( 'post' === $context ) {
				$tags['iframe'] = array(
					'align'           => true,
					'width'           => true,
					'height'          => true,
					'frameborder'     => true,
					'allowfullscreen' => true,
					'allow'           => true,
					'name'            => true,
					'src'             => true,
					'id'              => true,
					'class'           => true,
					'style'           => true,
					'scrolling'       => true,
					'marginwidth'     => true,
					'marginheight'    => true,
				);
			}
			return $tags;
		}

		function remove_all_metaboxes_in_gutenberg( $post_type, $post ) {
			global $wp_meta_boxes;
			if ( empty( $_GET['wpse_metabox_iframe'] ) || empty( $_GET['wpse_post_type'] ) ) {
				return;
			}

			$post_type = sanitize_text_field( $_GET['wpse_post_type'] );
			if ( isset( $wp_meta_boxes[ $post_type ] ) ) {
				unset( $wp_meta_boxes[ $post_type ] );
			}
		}

		function register_script_for_metabox_iframes() {
			if ( empty( $_GET['wpse_metabox_iframe'] ) ) {
				return;
			}
			?>
			<style>
				.vgca-only-admin-content body {
					background: transparent;
				}
				.vgca-only-admin-content #wpadminbar,
				.vgca-only-admin-content #adminmenumain,
				.vgca-only-admin-content #update-nag, 
				.vgca-only-admin-content .update-nag,
				.vgca-only-admin-content #wpfooter{
					display: none !important;
				}

				.vgca-only-admin-content .folded #wpcontent, 
				.vgca-only-admin-content .folded #wpfooter,
				.vgca-only-admin-content #wpcontent,
				.vgca-only-admin-content #wpfooter {
					margin-left: 0px !important;
					padding-left: 0px !important;
				}
				html.wp-toolbar.vgca-only-admin-content  {
					padding-top: 0px !important;
				}

				.vgca-only-admin-content .block-editor__container {
					min-height: 700px !important;
				}
				.vgca-only-admin-content button.components-button.editor-post-publish-panel__toggle,
				.vgca-only-admin-content .editor-post-publish-button,
				<?php if ( ! empty( $_GET['wpse_column'] ) && $_GET['wpse_column'] === 'post_content' ) { ?>
					.vgca-only-admin-content .edit-post-layout__metaboxes, 
				<?php } ?>
				.vgca-only-admin-content .editor-post-title, 
				.vgca-only-admin-content .components-panel__header.edit-post-sidebar__panel-tabs li:first-child, 
				.vgca-only-admin-content .edit-post-fullscreen-mode-close.components-button, 
				.vgca-only-admin-content .interface-pinned-items, 
				.vgca-only-admin-content div.fs-notice.updated, 
				.vgca-only-admin-content div.fs-notice.success, 
				.vgca-only-admin-content div.fs-notice.promotion {
					display: none !important;
				}
				.vgca-only-admin-content .editor-styles-wrapper.block-editor-writing-flow {
					padding-bottom: 0px !important;
					max-height: 1000px !important;
				}
			</style>
			<script>

				function vgseGetGutenbergContent() {		
					return wp.data.select("core/editor").getEditedPostContent();
				}
				function vgseSaveGutenbergContent() {
					wp.data.dispatch("core/editor").savePost();
				}
				function vgseGutenbergEditToCell() {	
					// wp.data.dispatch("core/editor").autosave();
					var value = vgseGetGutenbergContent();
					console.log(parent.wpseCurrentPopupSourceCoords);
					parent.hot.setDataAtRowProp(parent.wpseCurrentPopupSourceCoords.row, 'post_content', value);		
				}
				function vgseCancelGutenbergEdit() {
					for (var i = 0; i < 1000; i++) {
						wp.data.dispatch("core/editor").undo();
					}
				}
				function vgseInitMetaboxIframe() {

					if (window.parent.location.href !== window.location.href) {
						jQuery('html').addClass('vgca-only-admin-content');

						// If URL is not for wp-admin page, open outside the iframe
						jQuery(document).ready(function () {
							jQuery('body').on('click', 'a', function (e) {
								// If the link is in the post content, disable it to avoid 
								// navigating away from the editor
								if (jQuery(this).parents('.editor-styles-wrapper').length) {
									e.preventDefault();
									return false;
								}

								var url = jQuery(this).attr('href');

								if (typeof url === 'string' && url.indexOf('/wp-admin') < 0 && url.indexOf('http') > -1) {
									top.window.location.href = url;
									e.preventDefault();
									return false;
								}
							});
						});
						jQuery(window).on('load', function () {
							if (jQuery('.block-editor__container').length) {
								jQuery('.components-panel__header.edit-post-sidebar__panel-tabs li:last button').click();
							}
							parent.jQuery('.vgca-iframe-wrapper .vgca-loading-indicator').hide();

						});
					}
				}

				vgseInitMetaboxIframe();
			</script>
			<?php
		}

		function get_site_link( $link, $campaign = 'help' ) {
			$medium = VGSE()->helpers->get_plugin_mode();
			$source = VGSE()->helpers->get_provider_from_query_string();
			if ( empty( $source ) ) {
				$source = 'wp-admin';
			}
			$utm = 'utm_source=' . $source . '&utm_medium=' . $medium . '&utm_campaign=' . $campaign;
			if ( strpos( $link, '?' ) === false ) {
				$link .= '?' . $utm;
			} else {
				$link .= '&' . $utm;
			}
			$link = apply_filters( 'vg_sheet_editor/site_link', $link );
			return esc_url( $link );
		}

		function get_support_links( $link_key = null, $field = 'all', $campaign = 'help' ) {
			$medium = VGSE()->helpers->get_plugin_mode();
			$source = VGSE()->helpers->get_provider_from_query_string();
			if ( empty( $source ) ) {
				$source = 'wp-admin';
			}
			$support_links = array(
				'faq'        => array(
					'url'         => 'https://wpsheeteditor.com/documentation/faq/?utm_source=' . $source . '&utm_medium=' . $medium . '&utm_campaign=' . $campaign,
					'label'       => __( 'Quick Answers', 'vg_sheet_editor' ),
					'description' => __( 'You can read our FAQ with a list of hundreds of questions', 'vg_sheet_editor' ),
				),
				'guides'     => array(
					'url'         => 'https://wpsheeteditor.com/blog/?utm_source=' . $source . '&utm_medium=' . $medium . '&utm_campaign=' . $campaign,
					'label'       => __( 'Guides and Tutorials', 'vg_sheet_editor' ),
					'description' => __( 'We have +200 tutorials and guides on our blog', 'vg_sheet_editor' ),
				),
				'contact_us' => array(
					'url'         => 'https://wpsheeteditor.com/company/contact/?utm_source=' . $source . '&utm_medium=' . $medium . '&utm_campaign=' . $campaign,
					'label'       => __( 'Contact us', 'vg_sheet_editor' ),
					'description' => __( 'Get instant help in the live chat + email support during business hours', 'vg_sheet_editor' ),
				),
			);
			$links         = apply_filters( 'vg_sheet_editor/support_links', $support_links );
			if ( $link_key && isset( $links[ $link_key ] ) ) {
				$link = $links[ $link_key ];
				$out  = ( $field && $field !== 'all' ) ? $link[ $field ] : $link;
			} else {
				$out = $links;
			}
			return $out;
		}

		function after_init() {
		}

		function get_plugin_install_url( $plugin_slug ) {
			$install_plugin_base_url = ( is_multisite() ) ? network_admin_url() : admin_url();
			$install_plugin_url      = add_query_arg(
				array(
					's'    => $plugin_slug,
					'tab'  => 'search',
					'type' => 'term',
				),
				$install_plugin_base_url . 'plugin-install.php'
			);
			return esc_url( $install_plugin_url );
		}

		/**
		 * Register dashboard widgets.
		 * Currently the only widget is "Editions stats".
		 */
		function register_dashboard_widgets() {
			if ( ! empty( VGSE()->options['be_disable_dashboard_widget'] ) ) {
				return;
			}
			add_meta_box( 'vg_sheet_editor_usage_stats', __( 'WP Sheet Editor Usage', 'vg_sheet_editor' ), array( $this, 'render_usage_stats_widget' ), 'dashboard', 'normal', 'high' );
		}

		function render_usage_stats_widget() {
			require 'views/usage-stats-widget.php';
		}

		/**
		 * Redirect to "whats new" page after plugin update
		 */
		function redirect_to_whats_new_page() {

			// bail if settings are empty = fresh install
			if ( empty( VGSE()->options ) ) {
				return;
			}

			// bail if there aren\'t new features for this release
			if ( ! file_exists( VGSE_DIR . '/views/whats-new/' . VGSE()->version . '.php' ) ) {
				return;
			}

			// exit if the welcome page hasn\'t been showed
			if ( get_option( 'vgse_welcome_redirect' ) !== 'no' ) {
				return;
			}

			// exit if the page was already showed
			if ( get_option( 'vgse_hide_whats_new_' . VGSE()->version ) ) {
				return;
			}

			// Delete the redirect transient
			update_option( 'vgse_hide_whats_new_' . VGSE()->version, 'yes' );

			// Bail if activating from network, or bulk
			if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
				return;
			}

			if ( ! empty( $_GET['sheet_skip_whatsnew'] ) ) {
				return;
			}

			wp_redirect( esc_url_raw( add_query_arg( array( 'page' => 'vg_sheet_editor_whats_new' ), admin_url( 'admin.php' ) ) ) );
			exit();
		}

		/**
		 * Register admin pages
		 */
		function register_menu() {
			if ( apply_filters( 'vg_sheet_editor/register_admin_pages', true ) ) {
				add_menu_page( __( 'WP Sheet Editor', 'vg_sheet_editor' ), __( 'WP Sheet Editor', 'vg_sheet_editor' ), 'manage_options', 'vg_sheet_editor_setup', array( $this, 'render_quick_setup_page' ), VGSE()->plugin_url . 'assets/imgs/icon.svg' );
				add_submenu_page( 'vg_sheet_editor_setup', __( 'Extensions', 'vg_sheet_editor' ), __( 'Extensions', 'vg_sheet_editor' ), 'manage_options', 'vg_sheet_editor_extensions', array( $this, 'render_extensions_page' ) );
			}

			// Add the whats_new page only when we need to see the page
			if ( strpos( $_SERVER['REQUEST_URI'], 'vg_sheet_editor_whats_new' ) !== false ) {
				add_submenu_page( null, __( 'Sheet Editor', 'vg_sheet_editor' ), __( 'Sheet Editor', 'vg_sheet_editor' ), 'manage_options', 'vg_sheet_editor_whats_new', array( $this, 'render_whats_new_page' ) );
			}
		}

		/**
		 * Render extensions page
		 */
		function render_extensions_page() {
			if ( ! VGSE()->helpers->user_can_manage_options() ) {
				wp_die( __( 'You dont have enough permissions to view this page.', 'vg_sheet_editor' ) );
			}

			if ( ! apply_filters( 'vg_sheet_editor/extensions/is_page_allowed', true ) ) {
				return;
			}
			require 'views/extensions-page.php';
		}

		/**
		 * Render quick setup page
		 */
		function render_quick_setup_page() {
			if ( ! VGSE()->helpers->user_can_manage_options() ) {
				wp_die( __( 'You dont have enough permissions to view this page.', 'vg_sheet_editor' ) );
			}

			require 'views/quick-setup.php';
		}

		/**
		 * Render "whats new" page
		 */
		function render_whats_new_page() {
			if ( ! VGSE()->helpers->user_can_manage_options() ) {
				wp_die( __( 'You dont have enough permissions to view this page.', 'vg_sheet_editor' ) );
			}

			require 'views/whats-new.php';
		}

		/*
		* Register js scripts
		*/

		function register_scripts() {
			$current_post         = VGSE()->helpers->get_provider_from_query_string();
			$pages_to_load_assets = $this->frontend_assets_allowed_on_pages();
			if ( empty( $_GET['page'] ) ||
					! in_array( $_GET['page'], $pages_to_load_assets ) ) {
				return;
			}

			$this->_register_scripts( $current_post );
		}

		function _register_scripts( $current_post = null ) {

			wp_add_inline_script( 'jquery-core', 'window.$ = jQuery;' );

			if ( VGSE_DEBUG ) {
				wp_enqueue_script( 'select2_js', $this->plugin_url . 'assets/vendor/select2/dist/js/select2.min.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( 'modal_js', $this->plugin_url . 'assets/vendor/remodal/dist/remodal.min.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( 'labelauty', $this->plugin_url . 'assets/vendor/labelauty/source/jquery-labelauty.js', array( 'jquery' ), $this->version, false );

				wp_enqueue_script( 'notifications_js', $this->plugin_url . 'assets/vendor/oh-snap/ohsnap.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( 'handsontable_js', $this->plugin_url . 'assets/vendor/handsontable/dist/handsontable.full.js', array(), $this->version, false );

				wp_enqueue_script( 'chosen', $this->plugin_url . 'assets/vendor/chosen/chosen.jquery.min.js', array(), $this->version, false );
				wp_enqueue_script( 'chosen-editor', $this->plugin_url . 'assets/js/handsontable-chosen-editor.js', array(), $this->version, false );
				wp_enqueue_script( 'text_editor_js', $this->plugin_url . 'assets/vendor/jqueryte/dist/jquery-te-1.4.0.min.js', array(), $this->version, false );
				wp_enqueue_script( 'bep_nanobar', $this->plugin_url . 'assets/vendor/nanobar/nanobar.js', array(), $this->version, false );
				wp_enqueue_script( 'bep-form-to-object', $this->plugin_url . 'assets/vendor/formToObject/dist/formToObject.js', array(), $this->version, false );

				wp_enqueue_script( 'bep_global', $this->plugin_url . 'assets/js/global.js', array(), $this->version, false );

				wp_enqueue_script( 'bep_init_js', $this->plugin_url . 'assets/js/init.js', array( 'handsontable_js' ), $this->version, false );
				wp_enqueue_script( 'bep_post-status-plugin_js', $this->plugin_url . 'assets/js/post-status-plugin.js', array( 'bep_init_js' ), $this->version, false );
				$localize_handle = 'bep_global';
			} else {

				$min_extension = ( ! empty( $_GET['wpse_debug'] ) || ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ) ? '' : '.min';
				wp_enqueue_script( 'bep_libraries_js', $this->plugin_url . 'assets/vendor/js/libraries' . $min_extension . '.js', array(), $this->version, false );
				wp_enqueue_script( 'bep_init_js', $this->plugin_url . 'assets/js/scripts' . $min_extension . '.js', array( 'bep_libraries_js' ), $this->version, false );
				$localize_handle = 'bep_init_js';
			}
			wp_localize_script(
				$localize_handle,
				'vgse_global_data',
				apply_filters(
					'vg_sheet_editor/global_js_data',
					array(
						'ajax_url' => admin_url( 'admin-ajax.php' ),
					)
				)
			);
			do_action( 'vg_sheet_editor/after_enqueue_assets' );
		}

		/**
		 * Get pages allowed to load frontend assets.
		 * @return array
		 */
		function frontend_assets_allowed_on_pages() {

			$allowed_pages = array();
			if ( ! empty( $_GET['page'] ) && ( strpos( $_GET['page'], 'vgse-bulk-' ) !== false || strpos( $_GET['page'], 'vgse_' ) !== false || strpos( $_GET['page'], 'vg_sheet_editor' ) !== false ) ) {
				$allowed_pages[] = sanitize_text_field( $_GET['page'] );
			}
			$allowed_pages = apply_filters( 'vg_sheet_editor/scripts/pages_allowed', $allowed_pages );

			return $allowed_pages;
		}

		function get_trigger_link( $prefix, $url, $id = '', $append_page_slug = false ) {
			$id = $prefix . '-' . $id;
			if ( $append_page_slug && ! empty( $_GET['page'] ) ) {
				$id .= '-' . sanitize_text_field( $_GET['page'] );
			}
			return esc_url( add_query_arg( 'vgseup_t', $id, $url ) );
		}

		function get_buy_link( $id = '', $url = null, $append_page_slug = false, $post_type = '' ) {
			if ( ! $url ) {
				$url = $this->buy_link;
			}
			if ( ! $post_type ) {
				$post_type = VGSE()->helpers->get_provider_from_query_string( false );
			}
			if ( ! $post_type ) {
				$post_type = '';
			}
			$extension = VGSE()->helpers->get_extension_by_post_type( $post_type );
			if ( $extension && ! empty( $extension['inactive_action_url'] ) ) {
				$url = ( ! empty( $extension['is_any_mode_active'] ) ) ? $extension['active_action_url'] : $extension['inactive_action_url'];
			}

			return esc_url( str_replace( '{post_type}', $post_type, $this->get_trigger_link( 'buy', $url, $id, $append_page_slug ) ) );
		}

		/**
		 * Register CSS files.
		 */
		function register_styles() {
			$pages_to_load_assets = $this->frontend_assets_allowed_on_pages();
			if ( empty( $_GET['page'] ) ||
					! in_array( $_GET['page'], $pages_to_load_assets ) ) {
				return;
			}

			$this->_register_styles();
		}

		function render_extensions_list() {

			$extensions = VGSE()->extensions;
			$bundles    = VGSE()->bundles;
			foreach ( $extensions as $index => $extension ) {
				if ( ! empty( $extension['bundle'] ) && is_array( $extension['bundle'] ) && ! $extensions[ $index ]['is_active'] ) {
					foreach ( $extension['bundle'] as $bundle ) {
						array_push( $bundles[ $bundle ]['extensions'], $extensions[ $index ] );
					}
				}
			}

			include VGSE()->plugin_dir . '/views/extensions.php';
		}

		function render_extensions_group( $extensions, $bundle = null ) {

			$defaults = array(
				'title'                 => '',
				'icon'                  => '',
				'image'                 => '', // fa-search
				'description'           => '',
				'status'                => '',
				'active_action_url'     => '',
				'inactive_action_url'   => '',
				'freemius_function'     => '',
				'inactive_action_label' => '',
				'active_action_label'   => '',
				'bundle'                => false, // any string, we'll group them by value
				'class_function_name'   => '',
			);

			foreach ( $extensions as $extension ) {
				$extension = wp_parse_args( $extension, $defaults );

				if ( ! empty( $bundle ) ) {
					$bundle                             = wp_parse_args( $bundle, $extension );
					$extension['active_action_url']     = $bundle['active_action_url'];
					$extension['inactive_action_url']   = $bundle['inactive_action_url'];
					$extension['inactive_action_label'] = $bundle['inactive_action_label'];
					$extension['active_action_label']   = $bundle['active_action_label'];
					$extension['freemius_function']     = $bundle['freemius_function'];
				}

				$is_active    = $extension['is_active'];
				$freemius     = function_exists( $extension['freemius_function'] ) ? $extension['freemius_function']() : null;
				$button_label = $is_active ? $extension['active_action_label'] : $extension['inactive_action_label'];
				$button_url   = $is_active ? $extension['active_action_url'] : $extension['inactive_action_url'];

				if ( $freemius ) {
					$button_url   = ( $freemius->can_use_premium_code__premium_only() ) ? $freemius->get_account_url() : $this->get_buy_link( 'extensions', $freemius->checkout_url(), true );
					$button_label = ( $freemius->can_use_premium_code__premium_only() ) ? __( 'My license', 'vg_sheet_editor' ) : $button_label;
				}
				include VGSE()->plugin_dir . '/views/single-extension.php';
			}
		}

		function _register_styles() {
			if ( VGSE_DEBUG ) {
				wp_enqueue_style( 'wpse-fontawesome', $this->plugin_url . 'assets/vendor/font-awesome/css/font-awesome.min.css', '', $this->version, 'all' );
				wp_enqueue_style( 'select2_styles', $this->plugin_url . 'assets/vendor/select2/dist/css/select2.min.css', '', $this->version, 'all' );
				wp_enqueue_style( 'labelauty_styles', $this->plugin_url . 'assets/vendor/labelauty/source/jquery-labelauty.css', '', $this->version, 'all' );
				wp_enqueue_style( 'handsontable_css', $this->plugin_url . 'assets/vendor/handsontable/dist/handsontable.full.css', '', $this->version, 'all' );

				wp_enqueue_style( 'text_editor_css', $this->plugin_url . 'assets/vendor/jqueryte/dist/jquery-te-1.4.0.css', '', $this->version, 'all' );
				wp_enqueue_style( 'chosen-css', $this->plugin_url . 'assets/vendor/chosen/chosen.min.css', '', $this->version, 'all' );
				wp_enqueue_style( 'wp-sheet-editor-main-css', $this->plugin_url . 'assets/css/style.css', '', $this->version, 'all' );
				wp_enqueue_style( 'loading_anim_css', $this->plugin_url . 'assets/css/loading-animation.css', '', $this->version, 'all' );
				wp_enqueue_style( 'modal_css', $this->plugin_url . 'assets/vendor/remodal/dist/remodal.css', '', $this->version, 'all' );
				wp_enqueue_style( 'modal_theme_css', $this->plugin_url . 'assets/vendor/remodal/dist/remodal-default-theme.css', '', $this->version, 'all' );
			} else {
				wp_enqueue_style( 'wp-sheet-editor-libraries-css', $this->plugin_url . 'assets/vendor/css/libraries.min.css', '', $this->version, 'all' );
				wp_enqueue_style( 'wp-sheet-editor-main-css', $this->plugin_url . 'assets/css/styles.min.css', '', $this->version, 'all' );
			}
			$css_src = includes_url( 'css/' ) . 'editor.css';
			wp_enqueue_style( 'tinymce_css', $css_src, '', $this->version, 'all' );
		}

		/*
		* Enqueue wp media scripts on editor page
		*/

		function enqueue_media_wp_media() {
			$current_post = VGSE()->helpers->get_provider_from_query_string();

			$pages_to_load_assets = $this->frontend_assets_allowed_on_pages();
			if ( empty( $_GET['page'] ) ||
					! in_array( $_GET['page'], $pages_to_load_assets ) ) {
				return;
			}
			wp_enqueue_media();
		}

	}

}

if ( ! function_exists( 'VGSE' ) ) {

	/**
	 * Main WP Sheet Editor object
	 *
	 * @return WP_Sheet_Editor
	 */
	function VGSE() {
		return WP_Sheet_Editor::get_instance();
	}

	function vgse_init() {
		VGSE()->init();
	}

	if ( is_admin() || wp_doing_cron() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		add_action( 'wp_loaded', 'vgse_init', 999 );
	} else {
		// Priority 9 because the Google Sheets Sync needs the WPSE core to initialize before the front end forms save their submissions to be able to detect the changes and sync
		add_action( 'wp', 'vgse_init', 9 );
	}
}


// If the locale is RTL, force the locale to en_US because we don't support RTL
$wp_locale = get_option( 'WPLANG' );
if ( ! function_exists( 'vgse_force_editor_in_english' ) && preg_match( '/^(ar|he|fa|ku|ur)/', $wp_locale ) ) {
	function vgse_force_editor_in_english() {
		if ( ! is_admin() ) {
			return;
		}
		$is_editor_page           = isset( $_GET['page'] ) && strpos( $_GET['page'], 'vgse-bulk-edit-' ) !== false;
		$is_editor_export_request = ! empty( $_REQUEST['vgse_csv_export'] );

		if ( $is_editor_page || $is_editor_export_request ) {
			// This is required to switch the admin language without WPML
			add_action(
				'init',
				function() {
					switch_to_locale( 'en_US' );
				},
				1
			);
			// This is required to switch the admin language when using WPML
			if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
				add_filter(
					'get_user_metadata',
					function( $check, $object_id, $meta_key ) {
						if ( 'icl_admin_language' === $meta_key ) {
							return array( 'en' );
						}

						return $check; // Go on with the normal execution
					},
					10,
					3
				);
			}
		}
	}
	vgse_force_editor_in_english();
}
