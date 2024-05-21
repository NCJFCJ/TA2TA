<?php
/**
 * The main Event Schedule Manager plugin service provider: it bootstraps the plugin code.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference
 */

namespace TEC\Conference;

use TEC\Conference\Contracts\Container;
use TEC\Conference\Post_Types\Provider as Post_Types_Provider;
use TEC\Conference\Admin\Provider as Admin_Provider;
use TEC\Conference\Editor\Provider as Editor_Provider;
use TEC\Conference\Taxonomies\Provider as Taxonomies_Provider;
use TEC\Conference\Views\Provider as Views_Provider;
use TEC\Conference\License\Provider as License_Provider;
use TEC\Conference\Telemetry\Provider as Telemetry_Provider;
use TEC\Conference\Site_Health\Provider as Site_Health_Provider;
use TEC\Conference\Vendor\StellarWP\Assets\Config as Assets_Config;
use TEC\Conference\License\License;
use TEC\Conference\Telemetry\Telemetry;

/**
 * Class Plugin
 *
 * @since   1.0.0
 *
 * @package TEC\Conference
 */
class Plugin {

	/**
	 * Stores the version for the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public const VERSION = '1.1.0';

	/**
	 * Stores the base slug for the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const SLUG = 'event-schedule-manager';

	/**
	 * Stores the base slug for the extension.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const FILE = CONFERENCE_SCHEDULE_PRO_FILE;

	/**
	 * The Sessions Post Type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const SESSION_POSTTYPE = 'tec_session';

	/**
	 * The Speakers Post Type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const SPEAKER_POSTTYPE = 'tec_speaker';

	/**
	 * The Sponsors Post Type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const SPONSOR_POSTTYPE = 'tec_sponsor';

	/**
	 * The Track Taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const TRACK_TAXONOMY = 'tec_track';

	/**
	 * The Location Taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const LOCATION_TAXONOMY = 'tec_location';

	/**
	 * The Tags Taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const TAGS_TAXONOMY = 'tec_session_tag';

	/**
	 * The Sponsor Level Taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const SPONSOR_LEVEL_TAXONOMY = 'tec_sponsor_level';

	/**
	 * The Group Taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const GROUP_TAXONOMY = 'tec_group';

	/**
	 * @var bool Prevent autoload initialization
	 */
	private $should_prevent_autoload_init = false;

	/**
	 * @since 1.0.0
	 *
	 * @var string Plugin Directory.
	 */
	public $plugin_dir;

	/**
	 * @since 1.0.0
	 *
	 * @var string Plugin path.
	 */
	public $plugin_path;

	/**
	 * @since 1.0.0
	 *
	 * @var string Plugin basename.
	 */
	public $plugin_basename;

	/**
	 * @since 1.0.0
	 *
	 * @var string Plugin URL.
	 */
	public $plugin_url;

	/**
	 * @since 1.0.0
	 *
	 * @var string Plugin Base URL.
	 */
	public static $plugin_base_url;

	/**
	 * Allows this class to be used as a singleton.
	 *
	 * Note this specifically doesn't have a typing, just a type hinting via Docblocks, it helps
	 * avoid problems with deprecation since this is loaded so early.
	 *
	 * @since 1.0.0
	 *
	 * @var \Tribe__Container
	 */
	protected $container;

	/**
	 * Sets the container for the class.
	 *
	 * Note this specifically doesn't have a typing for the container, just a type hinting via Docblocks, it helps
	 * avoid problems with deprecation since this is loaded so early.
	 *
	 * @since 1.0.0
	 *
	 * @param ?\Tribe__Container $container The container to use, if any. If not provided, the global container will be used.
	 */
	public function set_container( $container = null ): void {
		$this->container = $container ?: new Container();
	}

	/**
	 * Boots the plugin class and registers it as a singleton.
	 *
	 * Note this specifically doesn't have a typing for the container, just a type hinting via Docblocks, it helps
	 * avoid problems with deprecation since this is loaded so early.
	 *
	 * @since 1.0.0
	 *
	 * @param ?\Tribe__Container $container The container to use, if any. If not provided, the global container will be used.
	 */
	public function boot( $container = null ): void {
		// Set up the plugin provider properties.
		$this->plugin_path     = trailingslashit( dirname( static::FILE ) );
		$this->plugin_basename = basename( static::FILE );
		$this->plugin_dir      = trailingslashit( basename( $this->plugin_path ) );
		self::$plugin_base_url = $this->plugin_url = plugins_url( $this->plugin_dir, $this->plugin_path );

		add_action( 'plugins_loaded', [ $this, 'bootstrap' ], 1 );
	}

	/**
	 * Plugins shouldn't include their functions before `plugins_loaded` because this will allow
	 * better compatibility with the autoloader methods.
	 *
	 * @since 1.0.0
	 */
	public function bootstrap() {
		if ( $this->should_prevent_autoload_init ) {
			return;
		}
		$plugin = new static();
		$plugin->register_autoloader();
		$plugin->set_container();
		$plugin->container->singleton( static::class, $plugin );
		$plugin->register();

		// Assets Config.
		Assets_Config::set_hook_prefix( PLUGIN::SLUG );
		Assets_Config::set_path( dirname( PLUGIN::FILE ) );
		Assets_Config::set_version( PLUGIN::VERSION );
		Assets_Config::set_relative_asset_path( 'src/resources/' );

		/**
		 * Configure the container.
		 *
		 * The container must be compatible with stellarwp/container-contract.
		 * See here: https://github.com/stellarwp/container-contract#usage.
		 *
		 * If you do not have a container, we recommend https://github.com/lucatume/di52
		 * and the corresponding wrapper:
		 * https://github.com/stellarwp/container-contract/blob/main/examples/di52/Container.php
		 */
		$container = new Container();
		License::boot( $container, $this->plugin_dir . $this->plugin_basename, $plugin );
		Telemetry::boot( $container );
	}

	/**
	 * Setup the Extension's properties.
	 *
	 * This always executes even if the required plugins are not present.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		conference_schedule_load_text_domain();

		$this->register_autoloader();

		// Register this provider as the main one and use a bunch of aliases.
		$this->container->singleton( static::class, $this );
		$this->container->singleton( 'event-schedule-manager', $this );
		$this->container->singleton( 'event-schedule-manager.plugin', $this );

		$this->container->register( Post_Types_Provider::class );
		$this->container->register( Taxonomies_Provider::class );
		$this->container->register( Admin_Provider::class );
		$this->container->register( Editor_Provider::class );
		$this->container->register( Views_Provider::class );
		$this->container->register( License_Provider::class );
		$this->container->register( Telemetry_Provider::class );
		$this->container->register( Site_Health_Provider::class );
	}

	/**
	 * Register the Autoloader for Event Schedule Manager.
	 *
	 * @since 1.0.0
	 */
	protected function register_autoloader() {
		// Load Composer autoload and strauss autoloader.
		require_once dirname( CONFERENCE_SCHEDULE_PRO_FILE ) . '/vendor/vendor-prefixed/autoload.php';
		require_once dirname( CONFERENCE_SCHEDULE_PRO_FILE ) . '/vendor/autoload.php';
		require_once dirname( CONFERENCE_SCHEDULE_PRO_FILE ) . '/vendor/cmb2/cmb2/init.php';
		require_once dirname( CONFERENCE_SCHEDULE_PRO_FILE ) . '/vendor/jesseeproductions/wp-cmb2-conditional-logic/cmb2-conditional-logic.php';
		require_once dirname( CONFERENCE_SCHEDULE_PRO_FILE ) . '/vendor/jesseeproductions/cmb-field-select2/cmb-field-select2.php';
	}

	/**
	 * Registers the plugin and dependency manifest among those managed by Event Schedule Manager.
	 *
	 * @since 1.0.0
	 */
	protected function register_plugin_dependencies() {
		$plugin_register = new Plugin_Register();
		$plugin_register->register_plugin();

		$this->container->singleton( Plugin_Register::class, $plugin_register );
		$this->container->singleton( 'event-schedule-manager.plugin_register', $plugin_register );
	}

	/**
	 * Get Vendor URL.
	 *
	 * @since 1.0.0
	 */
	public static function get_vendor_url() {
		return self::$plugin_base_url . 'vendor/';
	}

	/**
	 * Get Assets Path.
	 *
	 * @since 1.0.0
	 */
	public static function get_asset_path() {
		return self::$plugin_base_url . 'src/resources/';
	}

	/**
	 * Plugin activation callback.
	 * @see register_activation_hook()
	 *
	 * @since 1.0.0
	 */
	public static function activate() {}

	/**
	 * Plugin deactivation callback.
	 * @see register_deactivation_hook()
	 *
	 * @since 1.0.0
	 *
	 * @param bool $network_deactivating
	 */
	public static function deactivate( $network_deactivating ) {
		flush_rewrite_rules();
	}
}
