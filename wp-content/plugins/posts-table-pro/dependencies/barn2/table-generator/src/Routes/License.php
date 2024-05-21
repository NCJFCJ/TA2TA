<?php

/**
 * @package   Barn2\table-generator
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Routes;

use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Api_Handler;
class License extends Api_Handler
{
    public $slug = 'license';
    /**
     * {@inheritdoc}
     */
    public function register_routes()
    {
        \register_rest_route($this->get_api_namespace(), 'license', [['methods' => 'GET', 'callback' => [$this, 'get_details'], 'permission_callback' => [$this, 'check_permissions']], ['methods' => 'POST', 'callback' => [$this, 'handle_license'], 'permission_callback' => [$this, 'check_permissions']]]);
    }
    /**
     * Get license details from the database.
     *
     * @return array
     */
    private function get_license_details()
    {
        $license_handler = $this->get_plugin()->get_license();
        return ['status' => $license_handler->get_status(), 'exists' => $license_handler->exists(), 'key' => $license_handler->get_license_key(), 'status_help_text' => $license_handler->get_status_help_text(), 'error_message' => $license_handler->get_error_message()];
    }
    /**
     * Returns details about the license.
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function get_details($request)
    {
        return $this->send_success_response($this->get_license_details());
    }
    /**
     * Handle licensing actions via the api.
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function handle_license($request)
    {
        $license_key = $request->get_param('license');
        $action = $request->get_param('action');
        $allowed_actions = ['activate', 'check', 'deactivate'];
        if (empty($license_key)) {
            return $this->send_error_response(['message' => __('Please enter a license key.','posts-table-pro' )]);
        }
        if (!\in_array($action, $allowed_actions, \true)) {
            return $this->send_error_response(['message' => __('Invalid action requested.','posts-table-pro' )]);
        }
        $license_handler = $this->get_plugin()->get_license();
        switch ($action) {
            case 'activate':
                $license_handler->activate(\sanitize_text_field($license_key));
                break;
            case 'check':
                $license_handler->refresh();
                break;
            case 'deactivate':
                $license_handler->deactivate();
                break;
        }
        return $this->send_success_response($this->get_license_details());
    }
}
