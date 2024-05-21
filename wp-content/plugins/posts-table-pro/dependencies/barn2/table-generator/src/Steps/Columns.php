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
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Util;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Content_Table;
use Barn2\Plugin\Posts_Table_Pro\Dependencies\Barn2\Table_Generator\Traits\Columns_Aware;
/**
 * The columns step handles the setup of columns of the table.
 */
class Columns extends Step
{
    use Columns_Aware;
    /**
     * Get things started.
     *
     * @param boolean|object $plugin
     */
    public function __construct($plugin = \false)
    {
        parent::__construct($plugin);
        $this->set_id('columns');
        $this->set_name(__('Columns','posts-table-pro' ));
        $this->set_title(__('Table columns','posts-table-pro' ));
        $this->set_description(__('Next, choose which columns to display in the table.','posts-table-pro' ));
        $this->set_fields($this->get_fields_list());
    }
    /**
     * List of fields for the step.
     *
     * @return array
     */
    public function get_fields_list()
    {
        $fields = [['type' => 'columns', 'label' => __('Columns','posts-table-pro' ), 'name' => 'columns', 'value' => '']];
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
            return $this->send_success_response(['table_id' => $table_id, 'values' => ['columns' => $table->get_setting('columns', $this->get_default_columns($table->get_content_type()))]]);
        }
        return $this->send_success_response(['values' => ['columns' => $this->get_default_columns($this->get_generator()->get_default_options()['content_type'] ?? 'post')]]);
    }
    /**
     * Get a formatted list of default columns for the react component.
     *
     * @param boolean|string $content_type
     * @return array
     */
    private function get_default_columns($content_type = \false)
    {
        $supported_columns = $this->get_columns_list($content_type);
        $default_options = $this->get_generator()->get_default_options();
        $default_columns = isset($default_options['columns']) ? $default_options['columns'] : \false;
        $columns = [];
        if (empty($default_columns)) {
            return $columns;
        }
        $parsable = \explode(',', $default_columns);
        foreach ($parsable as $column) {
            if (!isset($supported_columns[$column])) {
                continue;
            }
            $columns[] = ['name' => $supported_columns[$column], 'slug' => $column, 'settings' => ['input' => $supported_columns[$column], 'visibility' => 'true']];
        }
        return $columns;
    }
    /**
     * {@inheritdoc}
     */
    public function save_data($request)
    {
        $values = $this->get_submitted_values($request);
        $columns = $values['columns'] ?? [];
        $table_id = $request->get_param('table_id');
        if (empty($table_id)) {
            return $this->send_error_response(['message' => __('The table_id parameter is missing.','posts-table-pro' )]);
        }
        // Cannot save empty columns.
        if (empty($columns)) {
            return $this->send_error_response(['message' => __('You must add at least one column.','posts-table-pro' )]);
        }
        $columns = Util::array_unset_recursive($columns, 'priority');
        $columns = Util::array_unset_recursive($columns, 'id');
        /** @var Content_Table $table */
        $table = (new Query($this->get_generator()->get_database_prefix()))->get_item($table_id);
        $table_settings = $table->get_settings();
        $table_settings['columns'] = $columns;
        $updated_table = (new Query($this->get_generator()->get_database_prefix()))->update_item($table_id, ['settings' => \wp_json_encode($table_settings)]);
        return $this->send_success_response(['table_id' => $table_id]);
    }
}
