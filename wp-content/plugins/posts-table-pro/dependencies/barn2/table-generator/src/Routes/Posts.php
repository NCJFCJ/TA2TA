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
 * Posts api route.
 * Search for posts based on post type.
 */
class Posts extends Api_Handler
{
    public $slug = 'posts';
    /**
     * {@inheritdoc}
     */
    public function register_routes()
    {
        \register_rest_route($this->get_api_namespace(), 'posts', [['methods' => 'GET', 'callback' => [$this, 'get_posts'], 'permission_callback' => [$this, 'check_permissions']]]);
    }
    /**
     * Get list of posts.
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function get_posts($request)
    {
        $post_type = $request->get_param('post_type');
        $args = ['post_type' => \sanitize_text_field($post_type), 'posts_per_page' => -1];
        $query = new \WP_Query($args);
        return $this->send_success_response(['posts' => $query->get_posts()]);
    }
}
