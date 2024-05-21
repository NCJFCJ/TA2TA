<?php

/**
 * @package   Barn2\table-generator
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator;

/**
 * Helper class that handles installation and activation of plugins
 * behind the scenes.
 */
class Plugin_Installer extends Api_Handler
{
    /**
     * {@inheritdoc}
     */
    public function register_routes()
    {
        \register_rest_route($this->get_api_namespace(), 'installer', [['methods' => 'GET', 'callback' => [$this, 'verify_plugin_installation'], 'permission_callback' => [$this, 'check_permissions']], ['methods' => 'POST', 'callback' => [$this, 'handle_plugin_installation'], 'permission_callback' => [$this, 'check_permissions']]]);
    }
    /**
     * Get the full url to the installer api route.
     *
     * @return string
     */
    public function get_api_route()
    {
        return \get_rest_url(null, \trailingslashit($this->get_api_namespace()) . 'installer');
    }
    /**
     * Determine the installation status of a plugin via the rest api.
     *
     * @param \WP_REST_Response $request
     * @return \WP_REST_Response
     */
    public function verify_plugin_installation($request)
    {
        if (empty(self::get_plugin_status($request->get_param('plugin')))) {
            return $this->send_error_response(['message' => __('Plugin parameter is missing from the request.','posts-table-pro' )]);
        }
        return $this->send_success_response(['plugin' => self::get_plugin_status($request->get_param('plugin'))]);
    }
    /**
     * Handle installation & activation of a plugin via the rest api.
     *
     * @param \WP_REST_Response $request
     * @return \WP_REST_Response
     */
    public function handle_plugin_installation($request)
    {
        $plugin = $request->get_param('plugin');
        $plugin_path = $request->get_param('plugin_path');
        $status = self::get_plugin_status($plugin_path);
        $installed = \false;
        $activated = \false;
        if ($status === 'not_installed') {
            $installed = $this->install_plugin($plugin);
        } else {
            $installed = \true;
        }
        if ($installed) {
            $activated = $this->activate_plugin($plugin_path);
        }
        if ($activated) {
            return $this->send_success_response(['message' => __('Plugin successfully activated.','posts-table-pro' ), 'plugin' => $plugin_path]);
        }
        return $this->send_error_response(['message' => __('Something went wrong while downloading or installing the plugin.','posts-table-pro' )]);
    }
    /**
     * Determine the status of a specific plugin.
     *
     * @param string $plugin_path
     * @return string
     */
    public static function get_plugin_status(string $plugin_path)
    {
        if (!\current_user_can('install_plugins')) {
            return;
        }
        if (!\function_exists('is_plugin_active_for_network')) {
            require_once \ABSPATH . '/wp-admin/includes/plugin.php';
        }
        if (!\file_exists(\WP_PLUGIN_DIR . '/' . $plugin_path)) {
            return 'not_installed';
        } else {
            $plugin_updates = \get_site_transient('update_plugins');
            $plugin_needs_update = \is_object($plugin_updates) && isset($plugin_updates->response) && \is_array($plugin_updates->response) ? \array_key_exists($plugin_path, $plugin_updates->response) : \false;
            if (\in_array($plugin_path, (array) \get_option('active_plugins', []), \true) || \is_plugin_active_for_network($plugin_path)) {
                return $plugin_needs_update ? 'active_update' : 'active';
            } else {
                return $plugin_needs_update ? 'inactive_update' : 'inactive';
            }
        }
    }
    /**
     * Silently install a plugin.
     *
     * @param string $plugin_slug
     * @return bool
     */
    public function install_plugin(string $plugin_slug)
    {
        if (!\current_user_can('install_plugins')) {
            return;
        }
        if (!\function_exists('request_filesystem_credentials')) {
            require_once \ABSPATH . 'wp-admin/includes/file.php';
        }
        if (!\function_exists('plugins_api')) {
            require_once \ABSPATH . 'wp-admin/includes/plugin-install.php';
        }
        if (!\class_exists('WP_Upgrader')) {
            require_once \ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        }
        if (\false === \filter_var($plugin_slug, \FILTER_VALIDATE_URL)) {
            $api = \plugins_api('plugin_information', ['slug' => $plugin_slug, 'fields' => ['short_description' => \false, 'sections' => \false, 'requires' => \false, 'rating' => \false, 'ratings' => \false, 'downloaded' => \false, 'last_updated' => \false, 'added' => \false, 'tags' => \false, 'compatibility' => \false, 'homepage' => \false, 'donate_link' => \false]]);
            $download_link = $api->download_link;
        } else {
            $download_link = $plugin_slug;
        }
        $upgrader = new \Plugin_Upgrader(new \WP_Ajax_Upgrader_Skin());
        $install = $upgrader->install($download_link);
        return \false !== $install;
    }
    /**
     * Silently activate a plugin.
     *
     * @param string $plugin_path
     * @return bool
     */
    public function activate_plugin(string $plugin_path)
    {
        if (!\current_user_can('install_plugins')) {
            return \false;
        }
        $activate = \activate_plugin($plugin_path, '', \false, \false);
        return !\is_wp_error($activate);
    }
}
