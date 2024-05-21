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
 * Users api route.
 *
 * Registers a route where we can search for users via the rest api
 * using ID, username, display name, email address, first name and last name.
 */
class Users extends Api_Handler
{
    /**
     * {@inheritdoc}
     */
    public $slug = 'users';
    /**
     * {@inheritdoc}
     */
    public function register_routes()
    {
        \register_rest_route($this->get_api_namespace(), 'users', [['methods' => 'POST', 'callback' => [$this, 'get_users'], 'permission_callback' => [$this, 'check_permissions']]]);
    }
    /**
     * Search for users via the rest api.
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function get_users($request)
    {
        $query = (new \WP_User_Query(['number' => 9999]))->get_results();
        $results = $query;
        $results = \array_unique($results, \SORT_REGULAR);
        $list_of_ids = [];
        $users = [];
        if (!empty($results)) {
            foreach ($results as $user) {
                if (!\in_array($user->ID, $list_of_ids, \true)) {
                    $list_of_ids[] = $user->ID;
                    $users[] = ['id' => $user->ID, 'text' => $user->user_login];
                }
            }
        }
        return $this->send_success_response(['users' => $users]);
    }
}
