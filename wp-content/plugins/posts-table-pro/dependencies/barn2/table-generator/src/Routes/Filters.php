<?php

/**
 * @package   Barn2\table-generator
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Routes;

use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Api_Handler;
class Filters extends Api_Handler
{
    public $slug = 'filter';
    /**
     * {@inheritdoc}
     */
    public function register_routes()
    {
        \register_rest_route($this->get_api_namespace(), 'filter', [['methods' => 'GET', 'callback' => [$this, 'get_filters'], 'permission_callback' => [$this, 'check_permissions']]]);
    }
    /**
     * Given a content type, we'll return the list of supported filters.
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function get_filters($request)
    {
        $content_type = $request->get_param('content_type');
        $supports = ['categories' => __('Categories','posts-table-pro' ), 'tags' => __('Tags','posts-table-pro' )];
        if ($content_type !== 'post') {
            unset($supports['categories']);
            unset($supports['tags']);
        }
        $taxonomies = \get_object_taxonomies(\sanitize_text_field($content_type), 'objects');
        $parsed_taxonomies = [];
        $skip = ['category', 'post_tag'];
        foreach ($taxonomies as $taxonomy) {
            $name = \sanitize_text_field($taxonomy->name);
            $label = \sanitize_text_field($taxonomy->label);
            if ($content_type === 'post' && \in_array($name, $skip, \true)) {
                continue;
            }
            $parsed_taxonomies["tax:{$name}"] = $label;
        }
        return $this->send_success_response(['taxonomies' => \array_merge($supports, $parsed_taxonomies)]);
    }
}
