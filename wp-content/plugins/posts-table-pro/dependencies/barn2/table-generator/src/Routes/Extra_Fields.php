<?php

/**
 * @package   Barn2\table-generator
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Routes;

use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Api_Handler;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Database\Query;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Content_Table;
/**
 * Class responsible for registering an optional
 * rest api route that handles extra fields for the
 * table edit page.
 */
abstract class Extra_Fields extends Api_Handler
{
    /**
     * {@inheritdoc}
     */
    public $slug = 'extra-fields';
    /**
     * Holds the list of extra fields.
     *
     * @var array
     */
    public $extra_fields = [];
    /**
     * {@inheritdoc}
     */
    public function __construct($plugin = \false)
    {
        parent::__construct($plugin);
        $this->extra_fields = $this->get_extra_fields();
    }
    /**
     * {@inheritdoc}
     */
    public function register_routes()
    {
        \register_rest_route($this->get_api_namespace(), 'extra-fields', [['methods' => 'GET', 'callback' => [$this, 'get_fields'], 'permission_callback' => [$this, 'check_permissions']]]);
    }
    /**
     * Defines the list of extra fields that is exclusively
     * displayed on the table edit page.
     *
     * @return array
     */
    public abstract function get_extra_fields();
    /**
     * Get the extra fields and their values.
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function get_fields($request)
    {
        $table_id = $request->get_param('table_id');
        $fields = $this->extra_fields;
        $parsed = [];
        $default_options = $this->generator->get_default_options();
        foreach ($fields as $field) {
            if (isset($default_options[$field['name']])) {
                $field['value'] = $default_options[$field['name']];
                $parsed[] = $field;
            } else {
                $parsed[] = $field;
            }
        }
        if (!empty($table_id) && \is_numeric($table_id)) {
            /** @var Content_Table $table */
            $table = (new Query($this->generator->get_database_prefix()))->get_item($table_id);
            if ($table instanceof Content_Table) {
                foreach ($parsed as $key => $parsed_field) {
                    $setting_value = $table->get_setting($parsed_field['name']);
                    if (!empty($setting_value)) {
                        $parsed[$key]['value'] = $setting_value;
                    }
                }
            }
        }
        return $this->send_success_response(['fields' => $parsed]);
    }
}
