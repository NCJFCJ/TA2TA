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
 * This step handles setup of filters for a table.
 */
class Filters extends Step
{
    /**
     * Get things started.
     *
     * @param boolean|object $plugin
     */
    public function __construct($plugin = \false)
    {
        parent::__construct($plugin);
        $this->set_id('filters');
        $this->set_name(__('Filters','posts-table-pro' ));
        $this->set_title(__('Filters','posts-table-pro' ));
        $this->set_description(__('Filters are displayed above the table, and help users to search and refine the %contentType%.','posts-table-pro' ));
        $this->set_fields($this->get_fields_list());
    }
    /**
     * List of fields for this spte.
     *
     * @return array
     */
    public function get_fields_list()
    {
        $fields = [['type' => 'filters', 'label' => __('Which filters do you want to display?','posts-table-pro' ), 'name' => 'filters', 'value' => '']];
        return $fields;
    }
    /**
     * {@inheritdoc}
     */
    public function get_data($request)
    {
        $table_id = $request->get_param('table_id');
        $default_options = $this->get_generator()->get_default_options();
        $default_filters_mode = isset($default_options['filters']) ? $default_options['filters'] : '';
        $default_filters = isset($default_options['filters_custom']) ? $default_options['filters_custom'] : '';
        if (!empty($table_id)) {
            /** @var Content_Table $table */
            $table = (new Query($this->get_generator()->get_database_prefix()))->get_item($table_id);
            return $this->send_success_response(['table_id' => $table_id, 'values' => ['filter_mode' => $table->get_setting('filter_mode', $default_filters_mode), 'filters' => $table->get_setting('filters', $this->get_default_filters())]]);
        }
        return $this->send_success_response(['values' => ['filter_mode' => $default_filters_mode, 'filters' => $this->get_default_filters()]]);
    }
    /**
     * Get the default list of filters from the global options.
     *
     * @return array
     */
    private function get_default_filters()
    {
        $default_options = $this->get_generator()->get_default_options();
        $default_filters_mode = isset($default_options['filters']) ? $default_options['filters'] : '';
        $default_filters = isset($default_options['filters_custom']) ? $default_options['filters_custom'] : '';
        $filters = [];
        if ($default_filters_mode === 'custom' && !empty($default_filters)) {
            $parsable = \explode(',', $default_filters);
            foreach ($parsable as $filter) {
                if (\strpos($filter, 'tax:') === 0) {
                    $custom_taxonomy_parts = \explode(':', $filter);
                    $custom_taxonomy = isset($custom_taxonomy_parts[1]) ? $custom_taxonomy_parts[1] : \false;
                    $custom_taxonomy_label = isset($custom_taxonomy_parts[2]) ? $custom_taxonomy_parts[2] : \false;
                    if (empty($custom_taxonomy)) {
                        continue;
                    }
                    $filters[] = ['name' => $custom_taxonomy, 'settings' => ['input' => $custom_taxonomy_label, 'taxonomy' => $custom_taxonomy]];
                } else {
                    $filters[] = ['name' => $filter, 'settings' => ['input' => Util::get_taxonomy_name($filter), 'taxonomy' => $filter]];
                }
            }
        }
        return $filters;
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
        $filters = isset($values['filters']) ? $values['filters'] : [];
        $filter_mode = isset($filters['mode']) ? $filters['mode'] : 'disabled';
        $filter_items = isset($filters['items']) ? $filters['items'] : [];
        if (!empty($filter_items)) {
            $filter_items = Util::array_unset_recursive($filter_items, 'id');
            $filter_items = Util::array_unset_recursive($filter_items, 'priority');
        }
        // Reset filter mode to disabled if the list of filters is empty.
        if ($filter_mode === 'custom' && empty($filter_items)) {
            $filter_mode = 'disabled';
        }
        /** @var Content_Table $table */
        $table = (new Query($this->get_generator()->get_database_prefix()))->get_item($table_id);
        $table_settings = $table->get_settings();
        $table_settings['filter_mode'] = $filter_mode;
        $table_settings['filters'] = $filter_items;
        $updated_table = (new Query($this->get_generator()->get_database_prefix()))->update_item($table_id, ['settings' => \wp_json_encode($table_settings)]);
        return $this->send_success_response(['table_id' => $table_id]);
    }
}
