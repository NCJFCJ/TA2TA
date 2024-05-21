<?php
/**
 * Controls the filter views.
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

use Tribe\Utils\Body_Classes as Body_Classes_Object;
use Tribe\Events\Views\V2\Template_Bootstrap;
use \Tribe\Events\Filterbar\Compatibility\Divi\Service_Provider as Divi_Service_Provider;

if ( ! class_exists( 'Tribe__Events__Filterbar__View' ) ) {
	class Tribe__Events__Filterbar__View {

		/**
		 * @var The instance of the class.
		 *
		 * @static
		 */
		protected static $instance;

		/**
		 * @var string The absolute path to the main plugin file
		 *
		 * @static
		 */
		protected static $plugin_file = '';

		/**
		 * @var The directory of the plugin.
		 */
		public $pluginDir;

		/**
		 * @var the plugin path.
		 */
		public $pluginPath;

		/**
		 * @var Whether filters sidebar is being displayed or not.
		 */
		protected $sidebarDisplayed;

		/**
		 * @var The default filters for a MU site.
		 *
		 * @static
		 */
		protected static $defaultMuFilters;

		const VERSION = '5.5.3';

		/**
		 * The Events Calendar Required Version
		 * Use Tribe__Events__Filterbar__Plugin_Register instead
		 *
		 * @deprecated 4.6
		 *
		 */
		const REQUIRED_TEC_VERSION = '6.1.0';

		/**
		 * Where in the themes we will look for templates
		 *
		 * @since 4.9.0
		 *
		 * @var string
		 */
		public $template_namespace = 'events-filterbar';

		/**
		 * Holds the allowed body classes for this object.
		 *
		 * @since 5.0.0
		 *
		 * @var array<string>
		 */
		protected $body_classes = [
			'tribe-events-filter-view',
			'tribe-filters-closed',
			'tribe-filters-open',
			'tribe-filters-vertical',
			'tribe-filters-horizontal',
		];

		/**
		 * Create the plugin instance and include the other class.
		 *
		 * @since 3.4
		 *
		 * @param string $plugin_file_path Deprecated in 4.3, path set by TRIBE_EVENTS_FILTERBAR_FILE instead.
		 *
		 * @return void
		 */
		public static function init( $plugin_file_path = null ) {

			if ( null === $plugin_file_path ) {
				$plugin_file_path = TRIBE_EVENTS_FILTERBAR_FILE;
			}

			require_once self::instance()->pluginPath . 'src/functions/views/provider.php';
			static::$plugin_file = $plugin_file_path;
			static::$instance    = static::instance();

			tribe_register_provider( Tribe\Events\Filterbar\Service_Providers\Context::class );
			tribe_register_provider( Tribe\Events\Filterbar\Views\V2_1\Service_Provider::class );

			// Custom tables v1 implementation.
			if ( class_exists( '\\TEC\\Filter_Bar\\Custom_Tables\\V1\\Provider' ) ) {
				tribe_register_provider( '\\TEC\\Filter_Bar\\Custom_Tables\\V1\\Provider' );
			}

			tribe_register_provider( Divi_Service_Provider::class );
		}

		/**
		 * The singleton function.
		 *
		 * @since 3.4
		 *
		 * @return Tribe__Events__Filterbar__View The instance.
		 */
		public static function instance() {
			if ( ! static::$instance instanceof static ) {
				static::$instance = new static();
			}

			return static::$instance;
		}

		/**
		 * The class constructor.
		 *
		 * @since 3.4
		 *
		 * @return void
		 */
		public function __construct() {

			$this->pluginPath       = trailingslashit( TRIBE_EVENTS_FILTERBAR_DIR );
			$this->pluginDir        = trailingslashit( basename( $this->pluginPath ) );
			$this->pluginUrl        = trailingslashit( plugins_url() . '/' . $this->pluginDir );
			$this->sidebarDisplayed = false;
			$this->register_active_plugin();

			add_action( 'tribe_events_ajax_accessibility_check', [ $this, 'display_dynamic_a11y_notice' ] );
			add_action( 'current_screen', [ $this, 'maybe_initialize_filters_for_screen' ], 10, 0 );
			/**
			 * Run on 'wp' to be sure all functions we may rely on are available.
			 * Priority ensures we run after TEC & ECP.
			 */
			add_filter( 'wp', [ $this, 'add_body_classes' ], 100 );
			// Priority ensures we run after TEC & ECP.
			add_filter( 'tribe_body_class_should_add_to_queue', [ $this, 'should_add_body_class_to_queue' ], 20, 3 );
			add_filter( 'tribe_body_classes_should_add', [ $this, 'filter_body_classes_should_add' ], 20, 4 );

			add_action( 'admin_enqueue_scripts', [ $this, 'enqueueAdminScripts' ] );

			$settings_page = new Tribe__Events__Filterbar__Settings();
			$settings_page->set_hooks();

			add_action( 'tribe_load_text_domains', [ $this, 'loadTextDomain' ] );

			// Load multisite defaults
			if ( is_multisite() ) {
				$tribe_events_filters_default_mu_filters = [];
				if ( file_exists( WP_CONTENT_DIR . '/tribe-events-mu-defaults.php' ) ) {
					include( WP_CONTENT_DIR . '/tribe-events-mu-defaults.php' );
				}
				self::$defaultMuFilters = apply_filters( 'tribe_events_mu_filters_default_filters', $tribe_events_filters_default_mu_filters );
			}

			add_action( 'admin_init', [ $this, 'run_updates' ], 10, 0 );

			/** Load the main filter-view.css stylesheet */
			tribe_asset(
				$this,
				'tribe-filterbar-styles',
				'filter-view.css',
				[ 'tribe-select2-css', 'tribe-common-admin', 'dashicons' ],
				'wp_enqueue_scripts',
				[
					'conditionals' => [ $this, 'should_enqueue_assets' ],
				]
			);

			/** Load the mobile filter-view stylesheet */
			tribe_asset(
				$this,
				'tribe-filterbar-mobile-styles',
				'filter-view-mobile.css',
				[ 'tribe-select2-css', 'tribe-common-admin', 'dashicons' ],
				'wp_enqueue_scripts',
				[
					'media'        => 'only screen and (max-width: ' . tribe_get_mobile_breakpoint() . 'px)',
					'conditionals' => [ $this, 'should_enqueue_assets' ],
				]
			);

			/** Load JS */
			tribe_asset(
				$this,
				'tribe-filterbar-js',
				'filter-scripts.js',
				[ 'tribe-dropdowns', 'jquery-ui-slider' ],
				'wp_enqueue_scripts',
				[
					'conditionals' => [ $this, 'should_enqueue_assets' ],
				]
			);
		}

		/**
		 * Registers this plugin as being active for other tribe plugins and extensions
		 *
		 * @return bool Indicates if Tribe Common wants the plugin to run
		 */
		public function register_active_plugin() {
			if ( ! function_exists( 'tribe_register_plugin' ) ) {
				return true;
			}

			return tribe_register_plugin( TRIBE_EVENTS_FILTERBAR_FILE, __CLASS__, self::VERSION );
		}

		/**
		 * Enqueue the plugin stylesheet(s).
		 *
		 * @since 3.4
		 * @depecated 5.4.0
		 *
		 * @return void
		 */
		public function enqueueStylesAndScripts() {
			_deprecated_function( __METHOD__, '5.4.0', 'No replacement method available. This method was only used for legacy views.' );
		}

		/**
		 * Enqueue the admin scripts.
		 *
		 * @since 3.4
		 *
		 * @return void
		 */
		public function enqueueAdminScripts() {
			global $current_screen;
			if ( $current_screen->id == 'tribe_events_page_' . Tribe__Settings::$parent_slug && isset( $_GET['tab'] ) && $_GET['tab'] == 'filter-view' ) {
				wp_enqueue_script( 'jquery-ui-sortable' );
			}
		}

		/**
		 * Sets whether the sidebar should be displayed.
		 *
		 * @since 3.0
		 *
		 * @return void
		 */
		public function setSidebarDisplayed() {
			if ( tribe_is_event_query() && ( ! is_single() || tribe_is_showing_all() ) && ! is_admin() ) {
				$active_filters = $this->get_active_filters();
				if ( ! empty( $active_filters ) ) {
					$this->sidebarDisplayed = true;
				}
			}
		}

		/**
		 * A simple conditional to check if the current front-end page is one on which Filter Bar
		 * assets should be loaded.
		 *
		 * @since 4.5.3
		 *
		 * @return boolean Should Filter Bar assets be loaded?
		 */
		public function should_enqueue_assets() {
			return ! tribe( Template_Bootstrap::class )->should_load() && ( tribe_is_event_query() || tribe_is_event_organizer() || tribe_is_event_venue() );
		}

		/**
		 * Add the filters body class.
		 *
		 *
		 * @since      3.4
		 * @deprecated 5.0.0
		 *
		 * @return array<string> The new set of body classes.
		 */
		public function addBodyClass( $classes ) {
			_deprecated_function( __METHOD__, '5.4.0', 'No replacement method available. This method was only used for legacy views.' );
			$classes[] = 'tribe-events-filter-view';
			$classes[] = 'tribe-filters-' . tribe_get_option( 'events_filters_default_state', 'closed' );
			$classes[] = 'tribe-filters-' . tribe_get_option( 'events_filters_layout', 'vertical' );

			return $classes;
		}

		/**
		 * Hook in and add FE body classes.
		 * This function does not handle logic -
		 * just adds them to the queue of Tribe classes to potentially add.
		 *
		 * @since 5.0.0
		 *
		 * @return void
		 */
		public function add_body_classes() {
			/** @var Body_Classes_Object $body_classes */
			$body_classes = tribe( Body_Classes_Object::class );

			$body_classes->add_classes( $this->body_classes );
		}

		/**
		 * Handles all the logic for adding Filter Bar classes to the Tribe body class queue.
		 *
		 * @since 5.0.0
		 *
		 * @param boolean $add   Whether to add the class to the queue or not.
		 * @param array   $class The body class name to add.
		 * @param string  $queue The queue we want to get (default) 'display', 'admin', 'all'.
		 *
		 * @return boolean       Whether to add the class to the queue or not.
		 */
		public function should_add_body_class_to_queue( $add, $class, $queue = 'display' ) {
			// Bail if it's not a class we care about.
			// This comes first so we don't affect other classes inadvertently.
			if ( ! in_array( $class, $this->body_classes ) ) {
				return $add;
			}

			// Bail on non-FE queues.
			if ( 'display' !== $queue ) {
				return false;
			}

			// No FBAR on singles (may require a tweak if we ever enable FBAR for shortcodes).
			if ( is_singular() ) {
				return false;
			}

			// Bail if we're doing v2.
			if ( tribe_events_views_v2_is_enabled() ) {
				return false;
			}

			// We should not add classes to pages where we don't enqueue assets.
			if ( ! $this->should_enqueue_assets() ) {
				return false;
			}

			// Per-class logic:
			$closed_option = tribe_get_option( 'events_filters_default_state', 'closed' );
			$layout_option = tribe_get_option( 'events_filters_layout', 'vertical' );

			if ( 'tribe-filters-horizontal' === $class ) {
				return 'horizontal' === $layout_option;
			}

			if ( 'tribe-filters-vertical' === $class ) {
				return 'vertical' === $layout_option;
			}

			if ( 'tribe-filters-open' === $class ) {
				return 'open' === $closed_option || 'vertical' === $layout_option;
			}

			if ( 'tribe-filters-closed' === $class ) {
				return 'closed' === $closed_option && 'horizontal' === $layout_option;
			}

			// Failsafe.
			return true;
		}

		/**
		 * Handles the logic for if we should be adding the queue to the body class list.
		 * Individual class logic is handled in should_add_body_class_to_queue() above.
		 *
		 * @since 5.0.0
		 *
		 * @param boolean $add                     Whether to add classes or not.
		 * @param string  $queue                   The queue we want to get 'admin', 'display', 'all'.
		 * @param array   $add_classes             The array of body class names to add.
		 * @param array   $unused_existing_classes An array of existing body class names from WP.
		 *
		 * @return boolean Whether to add our queue of classes to the body or not.
		 */
		public function filter_body_classes_should_add( $add, $queue, $add_classes, $unused_existing_classes ) {
			// Bail on non-FE queues.
			if ( 'display' !== $queue ) {
				return $add;
			}

			/**
			 * We want to be sure to add our classes,
			 * they've already been checked for appropriateness when added to the queue.
			 */
			if ( ! empty( array_intersect( $this->body_classes, $add_classes ) ) ) {
				return true;
			}

			return $add;
		}

		/**
		 * Add premium plugin paths for each file in the templates array
		 *
		 * @since 3.4
		 * @deprecated 5.4.0
		 *
		 * @param $template_paths array
		 *
		 * @return array
		 */
		public function template_paths( $template_paths = [] ) {
			_deprecated_function( __METHOD__, '5.4.0' );
			return $template_paths;
		}

		/**
		 * Display the filters sidebar.
		 *
		 * @since 3.4
		 * @deprecated 5.4.0
		 *
		 * @return void
		 */
		public function displaySidebar( $html ) {
			_deprecated_function( __METHOD__, '5.4.0' );
		}

		/**
		 * Returns whether or not there are tribe specific query vars in the given query object
		 *
		 * @param WP_Query $query Query object
		 *
		 * @deprecated 5.4.0
		 *
		 * @return boolean Are there are tribe specific query vars in the query object?
		 */
		public function is_tribe_query( $query ) {
			_deprecated_function( __METHOD__, '5.4.0' );

			return false;
		}

		/**
		 * Create the default filters and execute the action for other filters to hook into.
		 * NOTE: The slug must be one word, no underscores or hyphens.
		 *
		 * @param WP_Query $query
		 *
		 * @deprecated 5.4.0
		 *
		 * @return void
		 */
		public function maybe_initialize_filters_for_query( $query = null ) {
			_deprecated_function( __METHOD__, '5.4.0' );
		}

		/**
		 * Initialize filters if we're on the settings page.
		 *
		 * @since 3.5
		 *
		 * @return void
		 */
		public function maybe_initialize_filters_for_screen() {
			if ( $this->on_settings_page() ) {
				$this->initialize_filters();
			}
		}

		/**
		 * Detect the settings page.
		 *
		 * @since 3.5
		 * @since 5.3.0 Use admin pages to determine if we're on the filterbar settings page.
		 *
		 * @return boolean Are we on the settings page?
		 */
		private function on_settings_page() {
			$admin_pages          = tribe( 'admin.pages' );
			$current_page         = $admin_pages->get_current_page();
			$tec_settings_page_id = tribe( \Tribe\Events\Admin\Settings::class )::$settings_page_id;

			if (
				( ! empty( $current_page ) && $tec_settings_page_id === $current_page )
				&& ( ! empty( $_GET['tab'] ) && 'filter-view' === $_GET['tab'] )
			) {
				return true;
			}

			return false;
		}

		/**
		 * Initialize the filters.
		 *
		 * @since 3.5
		 *
		 * @return void
		 */
		public function initialize_filters() {
			static $initialized = false;
			if ( $initialized ) {
				return; // only run once
			}
			$initialized = true;

			tribe_singleton(
				'filterbar.filters.category',
				new Tribe__Events__Filterbar__Filters__Category( sprintf( esc_html__( '%s Category', 'tribe-events-filter-view' ), tribe_get_event_label_singular() ), 'eventcategory' )
			);

			tribe_singleton(
				'filterbar.filters.cost',
				new Tribe__Events__Filterbar__Filters__Cost(
					sprintf( __( 'Cost (%s)', 'tribe-events-filter-view' ), tribe_get_option( 'defaultCurrencySymbol', '$' ) ),
					'cost'
				)
			);

			tribe_singleton(
				'filterbar.filters.tag',
				new Tribe__Events__Filterbar__Filters__Tag( __( 'Tags', 'tribe-events-filter-view' ), 'tags' )
			);

			tribe_singleton(
				'filterbar.filters.venue',
				new Tribe__Events__Filterbar__Filters__Venue( tribe_get_venue_label_plural(), 'venues' )
			);

			tribe_singleton(
				'filterbar.filters.organizer',
				new Tribe__Events__Filterbar__Filters__Organizer( tribe_get_organizer_label_plural(), 'organizers' )
			);
			tribe_singleton(
				'filterbar.filters.day-of-week',
				new Tribe__Events__Filterbar__Filters__Day_Of_Week( __( 'Day', 'tribe-events-filter-view' ), 'dayofweek' )
			);
			tribe_singleton(
				'filterbar.filters.time-of-day',
				new Tribe__Events__Filterbar__Filters__Time_Of_Day( __( 'Time', 'tribe-events-filter-view' ), 'timeofday' )
			);

			tribe_singleton(
				'filterbar.filters.country',
				new Tribe__Events__Filterbar__Filters__Country( __( 'Country', 'tribe-events-filter-view' ), 'country' )
			);

			tribe_singleton(
				'filterbar.filters.city',
				new Tribe__Events__Filterbar__Filters__City( __( 'City', 'tribe-events-filter-view' ), 'city' )
			);

			tribe_singleton(
				'filterbar.filters.state',
				new Tribe__Events__Filterbar__Filters__State( __( 'State/Province', 'tribe-events-filter-view' ), 'state' )
			);

			tribe_singleton(
				'filterbar.filters.featured-events',
				new Tribe__Events__Filterbar__Filters__Featured_Events(
					sprintf( esc_html__( 'Featured %s', 'tribe-events-filter-view' ), tribe_get_event_label_plural() ),
					'featuredevent'
				)
			);

			Tribe__Events__Filterbar__Additional_Fields__Manager::init();

			do_action( 'tribe_events_filters_create_filters' );
		}

		/**
		 * Get settings for the filters.
		 *
		 * @since 3.5
		 *
		 * @return array|boolean An array of Filter Bar settings. False if none found.
		 */
		public function get_filter_settings() {
			$settings = get_option( Tribe__Events__Filterbar__Settings::OPTION_ACTIVE_FILTERS, false );

			if ( false === $settings ) {
				$settings = $this->get_multisite_default_settings();
			}

			return $settings;
		}

		/**
		 * Get multisite filter settings.
		 *
		 * @since 3.5
		 *
		 * @return array|boolean An array of multisite settings. False if site is not a multisite or no settings found.
		 */
		protected function get_multisite_default_settings() {
			if ( ! is_multisite() ) {
				return false;
			}

			if ( empty( self::$defaultMuFilters ) ) {
				return false;
			}

			return self::$defaultMuFilters;
		}

		/**
		 * Get active filters.
		 *
		 * @since 3.5
		 *
		 * @return array<string> Array of filter slugs.
		 */
		public function get_active_filters() {
			$current_filters = $this->get_filter_settings();
			if ( ! is_array( $current_filters ) ) { // everything is active
				$current_filters = $this->get_registered_filters();
			}

			return apply_filters( 'tribe_events_active_filters', array_keys( $current_filters ) );
		}

		/**
		 * Get the registered filters.
		 *
		 * @since 3.5
		 *
		 * @return array<array> An array of registered filters.
		 */
		public function get_registered_filters() {
			$filters = apply_filters( 'tribe_events_all_filters_array', [] );

			return $filters;
		}

		/**
		 * Load the plugin's textdomain.
		 *
		 * @since 3.4
		 * @return void
		 */
		public function loadTextDomain() {
			$mopath = $this->pluginDir . 'lang/';
			$domain = 'tribe-events-filter-view';

			// If we don't have Common classes load the old fashioned way
			if ( ! class_exists( 'Tribe__Main' ) ) {
				load_plugin_textdomain( $domain, false, $mopath );
			} else {
				// This will load `wp-content/languages/plugins` files first
				Tribe__Main::instance()->load_text_domain( $domain, $mopath );
			}
		}

		/**
		 * Get the absolute system path to the plugin directory, or a file therein
		 *
		 * @static
		 *
		 * @param string $path
		 *
		 * @return string
		 */
		public static function plugin_path( $path ) {
			$base = dirname( self::$plugin_file );
			if ( $path ) {
				return trailingslashit( $base ) . $path;
			} else {
				return untrailingslashit( $base );
			}
		}

		/**
		 * Get the absolute URL to the plugin directory, or a file therein
		 *
		 * @deprecated
		 *
		 * @param string $path
		 *
		 * @return string
		 */
		public static function plugin_url( $path ) {
			_deprecated_function( __METHOD__, '5.4.0' );
		}

		/**
		 * Make necessary database updates on admin_init
		 *
		 * @since 4.5
		 *
		 */
		public function run_updates() {
			if ( ! class_exists( 'Tribe__Events__Updater' ) ) {
				return; // core needs to be updated for compatibility
			}

			$updater = new Tribe__Events__Filterbar__Updater( self::VERSION );
			if ( $updater->update_required() ) {
				$updater->do_updates();
			}
		}

		/**
		 * Display a11y notice for live filter updates.
		 *
		 * @since 4.7
		 *
		 * @return void
		 */
		public function display_dynamic_a11y_notice() {
			if ( 'automatic' === tribe_get_option( 'liveFiltersUpdate', 'automatic' ) ) {
				echo '<div class="a11y-hidden" aria-label="' . __( 'Accessibility Form Notice', 'tribe-events-filter-view' ) . '">';
				echo __( 'Notice: Utilizing the form controls will dynamically update the content', 'tribe-events-filter-view' );
				echo '</div>';
			}

			return false;
		}
	}
}
