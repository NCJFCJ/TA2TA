<?php

/**
 * @package   Barn2\table-generator
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Routes;

use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Api_Handler;
/**
 * Stati api route.
 *
 * Returns the list of supported post stati.
 */
class Stati extends Api_Handler
{
    public $slug = 'stati';
    /**
     * {@inheritdoc}
     */
    public function register_routes()
    {
        \register_rest_route($this->get_api_namespace(), 'stati', [['methods' => 'GET', 'callback' => [$this, 'get_post_stati'], 'permission_callback' => [$this, 'check_permissions']]]);
    }
    /**
     * Get the list of registered stati for a given post type.
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function get_post_stati($request)
    {
        $statis = \get_post_stati([], 'objects');
        $allowed = ['publish', 'draft', 'pending', 'future'];
        $plucked = [];
        foreach ($statis as $status => $config) {
            if (\in_array($status, $allowed, \true)) {
                $plucked[$status] = $config;
            }
        }
        return $this->send_success_response(['stati' => $plucked]);
    }
}
