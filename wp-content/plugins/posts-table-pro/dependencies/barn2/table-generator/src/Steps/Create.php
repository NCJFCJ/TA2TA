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
 * First step of the wizard.
 */
class Create extends Step
{
    /**
     * Get things started.
     *
     * @param boolean|object $plugin
     */
    public function __construct($plugin = \false)
    {
        parent::__construct($plugin);
        $this->set_id('create');
        $this->set_name(__('Create','posts-table-pro' ));
        $this->set_title(__('Create a table','posts-table-pro' ));
        $this->set_fields($this->get_fields_list());
    }
    /**
     * Define list of fields.
     *
     * @return array
     */
    public function get_fields_list()
    {
        $registered_types = ['' => __('Select a content type','posts-table-pro' )];
        $registered_types = \array_merge($registered_types, Util::get_registered_post_types());
        $registered_types_options = Util::parse_array_for_dropdown($registered_types);
        $registered_types_options[0]['disabled'] = 'disabled';
        $fields = [['type' => 'text', 'label' => __('Table name','posts-table-pro' ), 'name' => 'name', 'description' => __('Give your table a friendly name to help you identify it later (e.g. “Posts in the Weddings category”)','posts-table-pro' ), 'value' => '', 'placeholder' => __('Name','posts-table-pro' )], ['type' => 'select', 'label' => __('What type of content do you want to display?','posts-table-pro' ), 'name' => 'content_type', 'value' => '', 'placeholder' => __('Name','posts-table-pro' ), 'options' => $registered_types_options]];
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
            if ($table instanceof Content_Table) {
                return $this->send_success_response(['table_id' => $table_id, 'values' => ['name' => $table->get_title(), 'content_type' => $table->get_content_type()]]);
            }
        }
        $default_options = $this->get_generator()->get_default_options();
        return $this->send_success_response(['values' => ['name' => '', 'content_type' => isset($default_options['post_type']) ? $default_options['post_type'] : '']]);
    }
    /**
     * {@inheritdoc}
     */
    public function save_data($request)
    {
        $values = $this->get_submitted_values($request);
        $name = $values['name'] ?? \false;
        $content_type = $values['content_type'] ?? \false;
        // A table ID might be sent through when editing an existing table.
        $table_id = $request->get_param('table_id');
        if (empty($name) || empty($content_type)) {
            return $this->send_error_response(['message' => __('Please enter a name for the table and select a content type.','posts-table-pro' )]);
        }
        $query = new Query($this->get_generator()->get_database_prefix());
        // Maybe update existing table or create a new one.
        if (!empty($table_id)) {
            $existing_table = $query->get_item($table_id);
            if ($existing_table instanceof Content_Table) {
                $settings = $existing_table->get_settings();
                $settings['content_type'] = $content_type;
                $query->update_item($table_id, ['title' => \stripslashes($name), 'settings' => \wp_json_encode($settings)]);
            }
        } else {
            $table_id = $query->add_item(['title' => \stripslashes($name), 'settings' => \wp_json_encode(['content_type' => $content_type])]);
        }
        return $this->send_success_response(['table_id' => $table_id]);
    }
}
