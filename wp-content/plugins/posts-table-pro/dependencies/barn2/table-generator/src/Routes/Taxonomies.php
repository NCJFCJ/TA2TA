<?php

/**
 * @package   Barn2\table-generator
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Routes;

use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Api_Handler;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Util;
/**
 * Taxonomies API Route.
 *
 * Does pretty much nothing because we're loading taxonomies from
 * the default WP taxonomies api route.
 */
class Taxonomies extends Api_Handler
{
    /**
     * {@inheritdoc}
     */
    public $slug = 'taxonomies';
    /**
     * {@inheritdoc}
     */
    public function register_routes()
    {
        \register_rest_route($this->get_api_namespace(), 'taxonomies', [['methods' => 'GET', 'callback' => [$this, 'get_registered_taxonomies'], 'permission_callback' => [$this, 'check_permissions']]]);
    }
    /**
     * Get the list of registered stati for a given post type.
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function get_registered_taxonomies($request)
    {
        $post_type = $request->get_param('post_type');
        if (empty($post_type)) {
            return $this->send_error_response(['message' => __('The post_type parameter was empty.','posts-table-pro' )]);
        }
        $taxonomies = \get_object_taxonomies(\sanitize_text_field($post_type), 'objects');
        if (!empty($taxonomies)) {
            foreach ($taxonomies as $taxonomy => $config) {
                if (!Util::taxonomy_has_terms($taxonomy)) {
                    unset($taxonomies[$taxonomy]);
                }
            }
        }
        if ($post_type === 'post') {
            unset($taxonomies['category']);
            unset($taxonomies['post_tag']);
        }
        return $this->send_success_response(['taxonomies' => $taxonomies]);
    }
}
