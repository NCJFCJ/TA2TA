<?php

/**
 * @package   Barn2\table-generator
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator;

use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Traits\Generator_Aware;
/**
 * Step base class.
 */
class Step extends Api_Handler
{
    use Generator_Aware;
    /**
     * Step ID (must be unique to each step)
     *
     * @var string
     */
    public $id;
    /**
     * Name of the step.
     *
     * @var string
     */
    public $name;
    /**
     * Heading title of the step.
     *
     * @var string
     */
    public $title;
    /**
     * Description of the step.
     *
     * @var string
     */
    public $description;
    /**
     * List of fields displayed on this step's page.
     *
     * @var array
     */
    private $fields = [];
    /**
     * Instance of the table generator class.
     *
     * @var Table_Generator
     */
    public $generator;
    /**
     * Get the step's ID.
     *
     * @return string
     */
    public function get_id()
    {
        return $this->id;
    }
    /**
     * Set the id of the step.
     *
     * @param string $id
     * @return self
     */
    public function set_id(string $id)
    {
        $this->id = $id;
        return $this;
    }
    /**
     * Get the name of the step.
     *
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }
    /**
     * Set the name of the step.
     *
     * @param string $name
     * @return self
     */
    public function set_name(string $name)
    {
        $this->name = $name;
        return $this;
    }
    /**
     * Get the step's title.
     *
     * @return string
     */
    public function get_title()
    {
        return $this->title;
    }
    /**
     * Set the title of the header for the step.
     *
     * @param string $title
     * @return self
     */
    public function set_title(string $title)
    {
        $this->title = $title;
        return $this;
    }
    /**
     * Get the step's description.
     *
     * @return string
     */
    public function get_description()
    {
        return $this->description;
    }
    /**
     * Set the description of the step.
     *
     * @param string $desc
     * @return self
     */
    public function set_description(string $desc)
    {
        $this->description = $desc;
        return $this;
    }
    /**
     * Get the list of fields for the step.
     *
     * @return array
     */
    public function get_fields()
    {
        return $this->fields;
    }
    /**
     * Set fields for the step.
     *
     * @param array $fields
     * @return self
     */
    public function set_fields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }
    /**
     * Register the REST Api routes.
     *
     * @return void
     */
    public function register_routes()
    {
        \register_rest_route($this->get_api_namespace(), $this->get_id(), [['methods' => 'GET', 'callback' => [$this, 'get_data'], 'permission_callback' => [$this, 'check_permissions']], ['methods' => 'POST', 'callback' => [$this, 'save_data'], 'permission_callback' => [$this, 'check_permissions']]]);
    }
    /**
     * Get the full url to the step's api route.
     *
     * @return string
     */
    public function get_step_api_route()
    {
        return \trailingslashit($this->get_api_namespace()) . $this->get_id();
    }
    /**
     * Returns data for the forms inside the table generator.
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function get_data($request)
    {
    }
    /**
     * Save data submitted through the form of the step.
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function save_data($request)
    {
    }
    /**
     * Send a successfull response via `WP_Rest_Response`.
     *
     * @param array $data additional data to send through the response.
     * @return \WP_REST_Response
     */
    public function send_success_response($data = [])
    {
        $response = \array_merge(['success' => \true], $data);
        return new \WP_REST_Response($response, 200);
    }
    /**
     * Send a successfull response via `WP_Rest_Response`.
     *
     * @param array $data additional data to send through the response.
     * @return \WP_REST_Response
     */
    public function send_error_response($data = [])
    {
        $response = \array_merge(['success' => \false], $data);
        return new \WP_REST_Response($response, 403);
    }
    /**
     * Retrieve submitted & sanitized values.
     *
     * @param \WP_REST_Request $request
     * @return array
     */
    public function get_submitted_values(\WP_REST_Request $request)
    {
        $values = $request->get_param('values');
        return Util::clean($values);
    }
}
