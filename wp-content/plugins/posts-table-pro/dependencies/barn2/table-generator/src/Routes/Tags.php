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
 * Tags API Route.
 *
 * Does pretty much nothing because we're loading tags from
 * the default WP tags api route.
 */
class Tags extends Api_Handler
{
    /**
     * {@inheritdoc}
     */
    public $slug = 'tags';
    /**
     * {@inheritdoc}
     */
    public function register_routes()
    {
        // Leave empty because we're using WP's default route.
    }
    /**
     * {@inheritdoc}
     */
    public function get_api_route()
    {
        return \get_rest_url(null, \trailingslashit('wp/v2/tags'));
    }
}
