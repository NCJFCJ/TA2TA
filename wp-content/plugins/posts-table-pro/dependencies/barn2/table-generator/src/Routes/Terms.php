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
 * Terms API Route.
 *
 * Retrieve terms of a given taxonomy slug.
 */
class Terms extends Api_Handler
{
    /**
     * {@inheritdoc}
     */
    public $slug = 'terms';
    /**
     * {@inheritdoc}
     */
    public function register_routes()
    {
        \register_rest_route($this->get_api_namespace(), 'terms', [['methods' => 'GET', 'callback' => [$this, 'get_registered_terms'], 'permission_callback' => [$this, 'check_permissions']]]);
    }
    /**
     * Get the list of registered terms for the given taxonomy.
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function get_registered_terms($request)
    {
        $taxonomy = $request->get_param('taxonomy');
        if (empty($taxonomy)) {
            return $this->send_error_response(['message' => __('The taxonomy parameter was empty.','posts-table-pro' )]);
        }
        return $this->send_success_response(['terms' => \get_terms(\sanitize_text_field($taxonomy), ['hide_empty' => \false])]);
    }
}
