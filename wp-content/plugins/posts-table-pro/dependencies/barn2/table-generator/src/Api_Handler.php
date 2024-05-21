<?php

/**
 * @package   Barn2\table-generator
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator;

use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Traits\Generator_Aware;
/**
 * Base class that handles registration of api routes.
 */
abstract class Api_Handler
{
    use Generator_Aware;
    const API_NAMESPACE = 'barn2-table-generator/v1';
    // Determines if the step should be displayed only in the wizard.
    const WIZARD_ONLY = \false;
    /**
     * The instance of the plugin making use of the table generator.
     *
     * @var object
     */
    private $plugin;
    /**
     * Slug of the api route.
     *
     * @var string
     */
    public $slug;
    /**
     * Holds the instance of the generator.
     *
     * @var Table_Generator
     */
    public $generator;
    /**
     * Get things started.
     *
     * @param boolean|object $plugin
     */
    public function __construct($plugin = \false)
    {
        if ($plugin) {
            $this->plugin = $plugin;
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
     * Attach the plugin's instance to the step.
     *
     * @param object $plugin
     * @return self
     */
    public function set_plugin($plugin)
    {
        $this->plugin = $plugin;
        return $this;
    }
    /**
     * Get the slug of the api route.
     *
     * @return string
     */
    public function get_route_slug()
    {
        return $this->slug;
    }
    /**
     * Hook API Routes into WP.
     *
     * @return void
     */
    public function register_api_routes()
    {
        \add_action('rest_api_init', [$this, 'register_routes']);
    }
    /**
     * Register the REST Api routes.
     *
     * @return void
     */
    abstract function register_routes();
    /**
     * Check if a given request has admin access.
     *
     * @param  \WP_REST_Request $request Full data about the request.
     * @return \WP_Error|bool
     */
    public function check_permissions($request)
    {
        return \wp_verify_nonce($request->get_header('x-wp-nonce'), 'wp_rest') && \current_user_can('manage_options');
    }
    /**
     * Get the api namespace for the steps.
     *
     * @return string
     */
    public function get_api_namespace()
    {
        return self::API_NAMESPACE . '/' . $this->get_plugin()->get_slug();
    }
    /**
     * Get the full URL to this api route.
     *
     * @return string
     */
    public function get_api_route()
    {
        return \get_rest_url(null, \trailingslashit($this->get_api_namespace()) . $this->get_route_slug());
    }
    /**
     * Send a successfull response via `WP_Rest_Response`.
     *
     * @param array $data additional data to send through the response.
     * @return \WP_REST_Response
     */
    public function send_success_response($data = [])
    {
        $response = \array_merge(['success' => \true], $data);
        return new \WP_REST_Response($response, 200);
    }
    /**
     * Send a successfull response via `WP_Rest_Response`.
     *
     * @param array $data additional data to send through the response.
     * @return \WP_REST_Response
     */
    public function send_error_response($data = [])
    {
        $response = \array_merge(['success' => \false], $data);
        return new \WP_REST_Response($response, 403);
    }
}
