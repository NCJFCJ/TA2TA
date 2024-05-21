<?php

/**
 * @package   Barn2\table-generator
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
namespace Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Steps;

use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Database\Query;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Step;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Content_Table;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Util;
/**
 * Handles generation of the include and exclude parameters.
 */
class Refine extends Step
{
    /**
     * Get things started.
     *
     * @param boolean|object $plugin
     */
    public function __construct($plugin = \false)
    {
        parent::__construct($plugin);
        $this->set_id('refine');
        $this->set_name(__('Refine','posts-table-pro' ));
        $this->set_title(__('Select your %contentType%','posts-table-pro' ));
        $this->set_fields($this->get_fields_list());
    }
    /**
     * Define list of fields.
     *
     * @return array
     */
    public function get_fields_list()
    {
        $fields = [['type' => 'refine', 'label' => __('Which %s do you want to display?','posts-table-pro' ), 'name' => 'refine', 'value' => '']];
        return $fields;
    }
    /**
     * {@inheritdoc}
     */
    public function get_data($request)
    {
        $table_id = $request->get_param('table_id');
        if (!empty($table_id)) {
            /** @var Content_Table $table */
            $table = (new Query($this->get_generator()->get_database_prefix()))->get_item($table_id);
            return $this->send_success_response(['table_id' => $table_id, 'values' => ['refine' => $table->get_setting('refine', []), 'refine_mode' => $table->get_setting('refine_mode', 'all')]]);
        }
        return $this->send_success_response();
    }
    /**
     * {@inheritdoc}
     */
    public function save_data($request)
    {
        $values = $this->get_submitted_values($request);
        $table_id = $request->get_param('table_id');
        if (empty($table_id)) {
            return $this->send_error_response(['message' => __('The table_id parameter is missing.','posts-table-pro' )]);
        }
        /** @var Content_Table $table */
        $table = (new Query($this->get_generator()->get_database_prefix()))->get_item($table_id);
        $table_settings = $table->get_settings();
        $refine_mode = $values['refine']['mode'] ? $values['refine']['mode'] : 'all';
        $formatted = $this->prepare_parameters($values['refine']['refinements']);
        $table_settings['refine'] = $formatted;
        $table_settings['refine_mode'] = $refine_mode;
        $updated_table = (new Query($this->get_generator()->get_database_prefix()))->update_item($table_id, ['settings' => \wp_json_encode($table_settings)]);
        return $this->send_success_response(['table_id' => $table_id]);
    }
    /**
     * Loop through parameters and format each and every one of them.
     *
     * @param array $parameters
     * @return array
     */
    public static function prepare_parameters($parameters)
    {
        $formatted = [];
        foreach ($parameters as $parameter_key => $parameter_config) {
            // Skip empty or false parameters or _data parameters.
            if (empty($parameter_config) || Util::string_ends_with($parameter_key, '_data')) {
                continue;
            }
            $data = isset($parameters["{$parameter_key}_data"]) ? $parameters["{$parameter_key}_data"] : [];
            if (empty($data)) {
                continue;
            }
            $is_taxonomy = isset($data['terms']);
            $is_cf = $parameter_key === 'cf';
            $is_stati = $parameter_key === 'status';
            $is_author = $parameter_key === 'author';
            $is_include = $parameter_key === 'include';
            $is_mime = $parameter_key === 'mime';
            if ($is_taxonomy) {
                $data = self::format_terms($data);
            } elseif ($is_cf) {
                $data = self::format_cf($data);
            } elseif ($is_stati) {
                $data = self::format_stati($data);
            } elseif ($is_author || $is_include) {
                $data = self::unset_name($data);
            } elseif ($is_mime) {
                $data = \array_filter(\array_map('trim', \explode(',', $data)));
            }
            if ($data instanceof \WP_Error || empty($data)) {
                continue;
            }
            $formatted[$parameter_key] = $data;
        }
        return $formatted;
    }
    /**
     * Format terms data.
     *
     * Basically we just remove the "name" property here
     * and then check the value of the "match" property.
     *
     * @param array $data
     * @return array
     */
    public static function format_terms($data)
    {
        $terms = Util::array_unset_recursive($data['terms'], 'name');
        $match = isset($data['match']) && !empty($data['match']);
        return ['terms' => $terms, 'match' => $match];
    }
    /**
     * Format custom fields - validate that all inputs are filled.
     *
     * @param array $data
     * @return array|\WP_Error
     */
    public static function format_cf($data)
    {
        foreach ($data as $field) {
            $name = isset($field['name']) ? $field['name'] : \false;
            $value = isset($field['value']) ? $field['value'] : \false;
            if (empty($name) || empty($value)) {
                return new \WP_Error('barn2-generator-cf-empty', __('Custom field must contain both the name and value.','posts-table-pro' ));
            }
        }
        return $data;
    }
    /**
     * Format status data.
     *
     * Basically we just remove the "label" property here
     * and then check the value of the "match" property.
     *
     * @param array $data
     * @return array
     */
    public static function format_stati($data)
    {
        $stati = Util::array_unset_recursive($data['stati'], 'label');
        $match = isset($data['match']) && !empty($data['match']);
        return ['stati' => $stati, 'match' => $match];
    }
    /**
     * Format author data.
     * Basically we just remove the "name" and "label" property here.
     *
     * @param array $data
     * @return array
     */
    public static function unset_name($data)
    {
        $data = Util::array_unset_recursive($data, 'name');
        $data = Util::array_unset_recursive($data, 'label');
        return $data;
    }
}
