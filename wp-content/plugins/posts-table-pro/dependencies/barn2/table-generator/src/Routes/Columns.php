<?php

/**
 * @package   Barn2\table-generator
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Routes;

use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Api_Handler;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Traits\Columns_Aware;
/**
 * Handles registration of the "columns" route.
 *
 * This route determines what kind of content is supported
 * given a content type.
 */
class Columns extends Api_Handler
{
    use Columns_Aware;
    /**
     * {@inheritdoc}
     */
    public $slug = 'cols';
    /**
     * {@inheritdoc}
     */
    public function register_routes()
    {
        \register_rest_route($this->get_api_namespace(), 'cols', [['methods' => 'GET', 'callback' => [$this, 'verify_supported_columns'], 'permission_callback' => [$this, 'check_permissions']]]);
    }
    /**
     * Given a content type, we'll return the list of supported columns
     * for the columns editor React component.
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function verify_supported_columns($request)
    {
        $content_type = $request->get_param('content_type');
        $supports = $this->get_columns_list($content_type);
        return $this->send_success_response(['supports' => $supports]);
    }
}
