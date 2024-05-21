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
/**
 * This step handles setup of sort for a table.
 */
class Sort extends Step
{
    /**
     * Get things started.
     *
     * @param boolean|object $plugin
     */
    public function __construct($plugin = \false)
    {
        parent::__construct($plugin);
        $this->set_id('sort');
        $this->set_name(__('Sort','posts-table-pro' ));
        $this->set_title(__('Sort','posts-table-pro' ));
        $this->set_description(__('How do you want to sort your %contentType%?','posts-table-pro' ));
        $this->set_fields($this->get_fields_list());
    }
    /**
     * List of fields for this spte.
     *
     * @return array
     */
    public function get_fields_list()
    {
        $fields = [['type' => 'sortby', 'label' => __('Sort by','posts-table-pro' ), 'name' => 'sortby', 'value' => ''], ['type' => 'select', 'label' => __('Sort direction','posts-table-pro' ), 'name' => 'sort_order', 'options' => Util::parse_array_for_dropdown(['' => __('Automatic','posts-table-pro' ), 'asc' => __('Ascending (A to Z, old to new)','posts-table-pro' ), 'desc' => __('Descending (Z to A, new to old)','posts-table-pro' )]), 'value' => '']];
        return $fields;
    }
    /**
     * {@inheritdoc}
     */
    public function get_data($request)
    {
        $table_id = $request->get_param('table_id');
        $default_options = $this->get_generator()->get_default_options();
        $default_sort_order = isset($default_options['sort_order']) ? $default_options['sort_order'] : '';
        $default_sortby = isset($default_options['sort_by']) ? $default_options['sort_by'] : '';
        if (!empty($table_id)) {
            /** @var Content_Table $table */
            $table = (new Query($this->get_generator()->get_database_prefix()))->get_item($table_id);
            return $this->send_success_response(['table_id' => $table_id, 'values' => ['sort_order' => $table->get_setting('sort_order', $default_sort_order), 'sortby' => $table->get_setting('sortby', $default_sortby)]]);
        }
        return $this->send_success_response(['values' => ['sort_order' => $default_sort_order, 'sortby' => $default_sortby]]);
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
        $sortby = isset($values['sortby']) ? $values['sortby'] : 'title';
        $sort_order = isset($values['sort_order']) ? $values['sort_order'] : '';
        /** @var Content_Table $table */
        $table = (new Query($this->get_generator()->get_database_prefix()))->get_item($table_id);
        $table_settings = $table->get_settings();
        $table_settings['sortby'] = $sortby;
        $table_settings['sort_order'] = $sort_order;
        $updated_table = (new Query($this->get_generator()->get_database_prefix()))->update_item($table_id, ['settings' => \wp_json_encode($table_settings)]);
        return $this->send_success_response(['table_id' => $table_id]);
    }
}
