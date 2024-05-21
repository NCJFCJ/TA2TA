<?php

/**
 * @package   Barn2\table-generator
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator;

use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Routes\Categories;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Routes\Columns;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Routes\Filters;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Routes\Posts;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Routes\Stati;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Routes\Supports;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Routes\Tables;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Routes\Tags;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Routes\Taxonomies;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Routes\Terms;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Routes\Users;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Traits\Paths;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Api_Handler;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Routes\Extra_Fields;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Routes\License;
use JsonSerializable;
/**
 * Initialize a new instance of the table generator library.
 */
class Table_Generator implements JsonSerializable
{
    use Paths;
    /**
     * Plugin instance.
     *
     * @var Plugin
     */
    private $plugin;
    /**
     * List of steps for the table generator.
     *
     * @var Step[]
     */
    private $steps = [];
    /**
     * Silent installer instance.
     *
     * @var Plugin_Installer
     */
    private $installer;
    /**
     * List of the internal API Routes.
     *
     * @var array
     */
    private $api_routes = [];
    /**
     * Additional arguments to include into the json configuration.
     *
     * @var array
     */
    public $config = [];
    /**
     * The prefix used to query the correct database table internally by the rest api.
     * Refer to the documentation for more info.
     *
     * @var string
     */
    private $db_prefix = 'barn2';
    /**
     * Array of field types that require preloading of values
     * from the js datastore.
     *
     * Refer to the documentation for more information.
     *
     * @var array
     */
    private $datastore_fields = [];
    /**
     * The shortcode string provided by the plugin using the generator.
     *
     * @var string
     */
    private $shortcode;
    /**
     * Class path of the Table_Args class of the plugin
     * making use of the library.
     *
     * @var string
     */
    private $args_resolver;
    /**
     * Holds the key of the options containing the list of default settings
     * for the generator.
     *
     * The default settings are then used when creating new tables and
     * autofilled into the steps.
     *
     * @var string
     */
    public $options_key;
    /**
     * Keys mapping of inputs into the generator that should preload
     * their default value to the one coming from the database.
     *
     * Example:
     *
     * ```
     * [ 'content_type' => 'post_type' ];
     * ```
     *
     * Where `content_type` is the key assigned to the input into the generator
     * and it should set it's default value to the one that is held by `post_type`.
     *
     * Where `post_type` is retrieved via the `$options_key` above.
     *
     * @var array
     */
    public $options_mapping = [];
    /**
     * Holds the list of default supported columns.
     * This list is inherited by the plugin making use of the library.
     *
     * @var array
     */
    public $columns_defaults = [];
    public $extra_fields;
    /**
     * Get things started.
     *
     * @param object $plugin Instance of the plugin making use of the library.
     * @param string $database_prefix the prefix used internally by the rest api to query the correct db table.
     * @param Step ...$steps list of steps to add to the generator.
     */
    public function __construct($plugin, string $database_prefix, ...$steps)
    {
        $this->plugin = $plugin;
        $this->installer = new Plugin_Installer($plugin);
        $this->db_prefix = $database_prefix;
        // Register all internal api routes.
        $this->api_routes = ['supports' => new Supports($plugin), 'users' => new Users($plugin), 'categories' => new Categories($plugin), 'stati' => new Stati($plugin), 'tags' => new Tags($plugin), 'taxonomies' => new Taxonomies($plugin), 'terms' => new Terms($plugin), 'posts' => new Posts($plugin), 'columns' => new Columns($plugin), 'filters' => new Filters($plugin), 'tables' => new Tables($plugin, $this->db_prefix), 'license' => new License($plugin)];
        /** @var Api_Handler $route */
        foreach ($this->api_routes as $route) {
            $route->attach_table_generator($this);
        }
        if (!empty($steps)) {
            $this->add_steps(...$steps);
        }
    }
    /**
     * Get the plugin's instance.
     *
     * @return object
     */
    public function get_plugin()
    {
        return $this->plugin;
    }
    /**
     * Get the table generator slug unique to this plugin.
     *
     * @return string
     */
    public function get_slug()
    {
        return $this->get_plugin()->get_slug() . '-table-generator';
    }
    /**
     * Get steps defined for the table generator.
     *
     * @return array
     */
    public function get_steps()
    {
        return $this->steps;
    }
    /**
     * Add steps to the table generator instance.
     *
     * @param mixed $steps array or single instance of \Step
     * @return self
     */
    public function add_steps(...$steps)
    {
        // Only accepts class instance of Step
        foreach ($steps as $step) {
            if (!$step instanceof Step) {
                continue;
            }
            $step->set_plugin($this->get_plugin());
            $step->attach_table_generator($this);
            $this->steps[] = $step;
        }
        return $this;
    }
    /**
     * Get the database prefix assigned to the generator.
     *
     * @return string
     */
    public function get_database_prefix()
    {
        return $this->db_prefix;
    }
    /**
     * Set the options key that holds the list of default settings.
     *
     * @param string $key
     * @return self
     */
    public function set_options_key(string $key)
    {
        $this->options_key = $key;
        return $this;
    }
    /**
     * Get the options key.
     *
     * @return string
     */
    public function get_options_key()
    {
        return $this->options_key;
    }
    /**
     * Get the default options from the database, using
     * the options key that has been provided.
     *
     * @return mixed
     */
    public function get_default_options()
    {
        return \get_option($this->options_key);
    }
    /**
     * Set the options mapping as described in the $options_mapping property of the class.
     *
     * @param array $config
     * @return self
     */
    public function set_options_mapping(array $config)
    {
        $this->options_mapping = $config;
        return $this;
    }
    /**
     * Get the options mapping array.
     *
     * @return array
     */
    public function get_options_mapping()
    {
        return $this->options_mapping;
    }
    /**
     * Register a new page in the dashboard menu.
     *
     * @return void
     */
    public function register_admin_page()
    {
        $menu_slug = $this->get_slug();
        $page_title = 'Post Tables';
        \add_menu_page($page_title, $page_title, 'manage_options', $menu_slug, [$this, 'render_admin_page'], 'dashicons-editor-table', 27);
        \add_submenu_page($menu_slug, __('Add New','posts-table-pro' ), __('Add New','posts-table-pro' ), 'manage_options', $menu_slug . '-add-new', [$this, 'render_admin_add_page']);
        global $submenu;
        // Override the label of the 1st submenu page.
        if ($submenu[$menu_slug][0][0] ?? \false) {
            $submenu[$menu_slug][0][0] = __('Tables','posts-table-pro' );
            //phpcs:ignore
        }
    }
    /**
     * Render the DIV responsible of displaying the page.
     * Rendering is handled via javascript.
     *
     * @return void
     */
    public function render_admin_page()
    {
        echo '<div id="b2-table-generator"></div>';
    }
    /**
     * Render the DIV responsible of displaying the page.
     * Rendering is handled via javascript.
     *
     * @return void
     */
    public function render_admin_add_page()
    {
        echo '<div id="b2-table-generator"></div>';
    }
    /**
     * Add body class to the table generator page.
     *
     * @param string $classes
     * @return string
     */
    public function body_class($classes)
    {
        $current_url = \admin_url(\basename($_SERVER['REQUEST_URI']));
        if ($current_url === $this->get_configuration()['listPageURL'] || $current_url === $this->get_configuration()['addPageURL'] || $current_url === $this->get_configuration()['pageHeaderLinks']['wizard']['url'] || $current_url === $this->get_configuration()['settingsPageURL']) {
            $classes .= ' barn2-table-generator-admin';
        }
        return $classes;
    }
    /**
     * Enqueue the table generator assets.
     *
     * @param string $hook
     * @return void
     */
    public function enqueue_assets($hook)
    {
        // We always need this registered.
        \wp_register_style('barn2-table-generator-options', $this->get_library_url() . 'assets/build/admin-page.css', [], '1.0.0');
        $current_url = \admin_url(\basename($_SERVER['REQUEST_URI']));
        if ($current_url !== $this->get_configuration()['listPageURL']) {
            return;
        }
        $file_name = 'tables-list';
        $integration_script_path = '/assets/build/' . $file_name . '.js';
        $integration_script_asset_path = $this->get_library_path() . 'assets/build/' . $file_name . '.asset.php';
        $integration_script_asset = \file_exists($integration_script_asset_path) ? require $integration_script_asset_path : ['dependencies' => [], 'version' => \filemtime($integration_script_path)];
        $script_url = $this->get_library_url() . $integration_script_path;
        \wp_register_script($this->get_slug(), $script_url, $integration_script_asset['dependencies'], $integration_script_asset['version'], \true);
        \wp_enqueue_script($this->get_slug());
        \wp_register_style($this->get_slug(), $this->get_library_url() . 'assets/build/tables-list.css', ['wp-components'], $integration_script_asset['version']);
        \wp_enqueue_style($this->get_slug());
        \wp_add_inline_script($this->get_slug(), 'const Barn2TableGenerator = ' . \wp_json_encode($this), 'before');
    }
    /**
     * Enqueue the table generator editor assets.
     *
     * @param string $hook
     * @return void
     */
    public function enqueue_editor_page_assets($hook)
    {
        $current_url = \admin_url(\basename($_SERVER['REQUEST_URI']));
        if ($current_url !== $this->get_configuration()['addPageURL'] && $current_url !== $this->get_configuration()['pageHeaderLinks']['wizard']['url']) {
            return;
        }
        $file_name = 'add-new-table';
        $integration_script_path = '/assets/build/' . $file_name . '.js';
        $integration_script_asset_path = $this->get_library_path() . 'assets/build/' . $file_name . '.asset.php';
        $integration_script_asset = \file_exists($integration_script_asset_path) ? require $integration_script_asset_path : ['dependencies' => [], 'version' => \filemtime($integration_script_path)];
        $script_url = $this->get_library_url() . $integration_script_path;
        \wp_register_script($this->get_slug(), $script_url, $integration_script_asset['dependencies'], $integration_script_asset['version'], \true);
        \wp_enqueue_script($this->get_slug());
        \wp_register_style($this->get_slug(), $this->get_library_url() . 'assets/build/add-new-table.css', ['wp-components'], $integration_script_asset['version']);
        \wp_enqueue_style($this->get_slug());
        \wp_add_inline_script($this->get_slug(), 'const Barn2TableGenerator = ' . \wp_json_encode($this), 'before');
    }
    /**
     * Enqueue custom assets on the EPT page.
     *
     * @return void
     */
    public function enqueue_ept_assets()
    {
        $screen = \get_current_screen();
        if ($screen->base !== 'toplevel_page_ept_post_types') {
            return;
        }
        $file_name = 'ept-integration';
        $integration_script_path = '/assets/build/' . $file_name . '.js';
        $integration_script_asset_path = $this->get_library_path() . 'assets/build/' . $file_name . '.asset.php';
        $integration_script_asset = \file_exists($integration_script_asset_path) ? require $integration_script_asset_path : ['dependencies' => [], 'version' => \filemtime($integration_script_path)];
        $script_url = $this->get_library_url() . $integration_script_path;
        \wp_register_script('ept-integration', $script_url, $integration_script_asset['dependencies'], $integration_script_asset['version'], \true);
        \wp_enqueue_script('ept-integration');
        \wp_add_inline_script('ept-integration', 'const Barn2TableGenerator = ' . \wp_json_encode($this), 'before');
    }
    /**
     * Set additional arguments for the json configuration of the react app.
     *
     * @param array $args
     * @return self
     */
    public function config($args = [])
    {
        $this->config = $args;
        return $this;
    }
    /**
     * Get the instance of an api route.
     *
     * @param string $route
     * @return Api_Handler
     */
    public function get_api_route(string $route)
    {
        return isset($this->api_routes[$route]) ? $this->api_routes[$route] : \false;
    }
    /**
     * Programmatically add a new api route to the generator.
     *
     * @param string $route_slug
     * @param string $route route class path
     * @return self
     */
    public function add_api_route(string $route_slug, $route)
    {
        $route = new $route($this->plugin);
        $route->attach_table_generator($this);
        $this->api_routes[$route_slug] = $route;
        return $this;
    }
    /**
     * Boot the library.
     *
     * @return void
     */
    public function boot()
    {
        \add_action('admin_menu', [$this, 'register_admin_page']);
        \add_action('admin_enqueue_scripts', [$this, 'enqueue_assets'], 20);
        \add_action('admin_enqueue_scripts', [$this, 'enqueue_editor_page_assets'], 20);
        \add_action('admin_enqueue_scripts', [$this, 'enqueue_ept_assets'], 20);
        \add_filter('admin_body_class', [$this, 'body_class']);
        /** @var Step */
        foreach ($this->get_steps() as $step) {
            $step->register_api_routes();
        }
        $this->installer->register_api_routes();
        if (!empty($this->extra_fields)) {
            $this->add_api_route('extra-fields', new $this->extra_fields($this->plugin));
        }
        /** @var Api_Handler $route */
        foreach ($this->api_routes as $route) {
            $route->register_api_routes();
        }
        $this->disable_license_notices_in_wizard();
        $this->disable_settings_panel_links();
        $shortcode = new Shortcode($this->get_shortcode(), $this);
    }
    /**
     * Disable license notices in wizard pages.
     *
     * @return void
     */
    public function disable_license_notices_in_wizard()
    {
        $current_url = \admin_url(\basename($_SERVER['REQUEST_URI']));
        if ($current_url === $this->get_configuration()['addPageURL'] || $current_url === $this->get_configuration()['pageHeaderLinks']['wizard']['url']) {
            \add_filter('barn2_plugin_hide_license_notices', '__return_true');
        }
    }
    /**
     * Inject setup wizard link into the settings page top links.
     *
     * @return void
     */
    private function disable_settings_panel_links()
    {
        \add_filter('barn2_plugins_title_links', function ($links) {
            return [];
        });
    }
    /**
     * Add a fieldto the list of special fields
     * that make use of the js store.
     *
     * @param string $field_name the name of the field
     * @return self
     */
    public function add_datastore_field(string $field_name)
    {
        $this->datastore_fields = \array_merge($this->datastore_fields, [$field_name]);
        return $this;
    }
    /**
     * Set the shortcode string provided by the plugin.
     *
     * @param string $shortcode
     * @return self
     */
    public function set_shortcode(string $shortcode)
    {
        $this->shortcode = $shortcode;
        return $this;
    }
    /**
     * Get the shortcode string assigned by the plugin.
     *
     * @return string
     */
    public function get_shortcode()
    {
        return $this->shortcode;
    }
    /**
     * Set the args resolver class path.
     *
     * @param string $resolver
     * @return self
     */
    public function set_args_resolver(string $resolver)
    {
        $this->args_resolver = $resolver;
        return $this;
    }
    /**
     * Get the args resolver assigned to the library.
     *
     * @return string
     */
    public function get_args_resolver()
    {
        return $this->args_resolver;
    }
    /**
     * Set the default supported columns.
     *
     * @param array $columns
     * @return self
     */
    public function set_default_columns(array $columns)
    {
        $this->columns_defaults = $columns;
        return $this;
    }
    /**
     * Get the default supported columns.
     *
     * @return array
     */
    public function get_default_columns()
    {
        return $this->columns_defaults;
    }
    /**
     * Set the additional fields that are displayed exclusively on the
     * table edit page.
     *
     * @param string $route class path
     * @return self
     */
    public function set_extra_fields(string $route)
    {
        $this->extra_fields = $route;
        return $this;
    }
    /**
     * Get the list of additional fields that have been configured.
     *
     * @return string
     */
    public function get_extra_fields()
    {
        return $this->extra_fields;
    }
    /**
     * Get the full configuration of the generator.
     *
     * @param array $args
     * @return array
     */
    public function get_configuration($args = [])
    {
        if (empty($args)) {
            $args = $this->config;
        }
        if (!\function_exists('get_plugins')) {
            require_once \ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $menu_slug = $this->get_slug();
        $defaults = ['shortcode' => $this->get_shortcode(), 'dataStoreFields' => $this->datastore_fields, 'contentTypes' => Util::get_registered_post_types(), 'pageHeader' => \true, 'pageHeaderTitle' => __('Posts Table Pro','posts-table-pro' ), 'installedPlugins' => \array_keys(\get_plugins()), 'pluginProductID' => $this->plugin::ITEM_ID, 'indexDescription' => '', 'defaultOptions' => $this->get_default_options(), 'defaultOptionsMapping' => $this->get_options_mapping(), 'pageHeaderLinks' => ['documentation' => ['title' => __('Documentation','posts-table-pro' ), 'url' => 'https://barn2.com/kb-categories/posts-table-pro-kb/'], 'support' => ['title' => __('Support','posts-table-pro' ), 'url' => 'https://barn2.com/support-center/'], 'wizard' => ['title' => __('Setup wizard','posts-table-pro' ), 'url' => \admin_url('admin.php?page=' . $menu_slug . '-add-new&wizard=1')]], 'addPageURL' => \admin_url('admin.php?page=' . $menu_slug . '-add-new'), 'listPageURL' => \admin_url('admin.php?page=' . $menu_slug), 'settingsPageURL' => $this->plugin->get_settings_page_url(), 'isPluginInstalled' => \false, 'advancedOptionsPageURL' => 'https://barn2.com/kb/posts-table-options/'];
        $args = \wp_parse_args($args, $defaults);
        return $args;
    }
    /**
     * Json data required for the react app.
     *
     * @return array
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $steps = [];
        /** @var Step $step */
        foreach ($this->get_steps() as $step) {
            $steps[$step->get_id()] = ['id' => $step->get_id(), 'name' => $step->get_name(), 'title' => $step->get_title(), 'description' => $step->get_description(), 'fields' => $step->get_fields(), 'route' => \get_rest_url(null, $step->get_step_api_route()), 'wizard_only' => $step::WIZARD_ONLY];
        }
        $routes = [];
        /** @var Api_Handler $route */
        foreach ($this->api_routes as $route) {
            $routes[$route->get_route_slug()] = $route->get_api_route();
        }
        return ['restNonce' => \wp_create_nonce('wp_rest'), 'steps' => $steps, 'installer' => $this->installer->get_api_route(), 'commonApi' => $routes, 'config' => $this->get_configuration($this->config), 'pluginSlug' => $this->plugin->get_slug(), 'hasExtraFields' => !empty($this->extra_fields), 'isWizardMode' => isset($_GET['wizard']) && $_GET['wizard'] === '1'];
    }
}
