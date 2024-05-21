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
 * Handles registration of the "supports" route.
 *
 * This route determines what kind of content is supported
 * given a content type.
 */
class Supports extends Api_Handler
{
    /**
     * {@inheritdoc}
     */
    public $slug = 'supports';
    /**
     * {@inheritdoc}
     */
    public function register_routes()
    {
        \register_rest_route($this->get_api_namespace(), 'supports', [['methods' => 'GET', 'callback' => [$this, 'verify_supported_content'], 'permission_callback' => [$this, 'check_permissions']]]);
    }
    /**
     * Given a content type, we'll return the list of supported content
     * for the parameters generator React component.
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function verify_supported_content($request)
    {
        $content_type = $request->get_param('content_type');
        $taxonomies = \get_object_taxonomies(\sanitize_text_field($content_type), 'objects');
        if (!empty($taxonomies)) {
            foreach ($taxonomies as $taxonomy => $config) {
                if (!Util::taxonomy_has_terms($taxonomy)) {
                    unset($taxonomies[$taxonomy]);
                }
            }
        }
        $parsed_taxonomies = $this->parse_taxonomies($taxonomies);
        $supports = \array_merge($parsed_taxonomies, [
            'cf' => __('Custom fields','posts-table-pro' ),
            'status' => __('Status','posts-table-pro' ),
            'author' => __('Author','posts-table-pro' ),
            'include' => __('Individual %contentType%','posts-table-pro' ),
            //phpcs:ignore
            'mime' => __('MIME type','posts-table-pro' ),
        ]);
        if ($content_type !== 'attachment') {
            unset($supports['mime']);
        }
        if (!\post_type_supports($content_type, 'author')) {
            unset($supports['author']);
        }
        return $this->send_success_response(['supports' => $supports, 'taxonomies' => $parsed_taxonomies]);
    }
    /**
     * Parses the list of registered taxonomies and returns
     * an array formatted for being used on the frontend.
     *
     * @param array $taxonomies
     * @return array
     */
    private function parse_taxonomies(array $taxonomies)
    {
        $parsed = [];
        foreach ($taxonomies as $taxonomy) {
            $parsed[$taxonomy->name] = $taxonomy->label;
        }
        return $parsed;
    }
}
