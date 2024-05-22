<?php

namespace IAWP;

use IAWP\Custom_WordPress_Columns\Views_Column;
use IAWP\Migrations\Migrations;
/** @internal */
class Database_Manager
{
    public function reset_analytics() : void
    {
        \delete_option('iawp_db_version');
        Migrations::create_or_migrate();
        $this->delete_all_post_meta();
    }
    public function delete_all_data() : void
    {
        $this->delete_all_iawp_options();
        $this->delete_all_iawp_user_metadata();
        $this->delete_all_iawp_tables();
        $this->delete_all_post_meta();
    }
    private function delete_all_iawp_options() : void
    {
        global $wpdb;
        $options = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->options} WHERE option_name LIKE %s", 'iawp_%'));
        foreach ($options as $option) {
            \delete_option($option->option_name);
        }
    }
    private function delete_all_iawp_user_metadata() : void
    {
        global $wpdb;
        $metadata = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->usermeta} WHERE meta_key LIKE %s", 'iawp_%'));
        foreach ($metadata as $metadata) {
            \delete_user_meta($metadata->user_id, $metadata->meta_key);
        }
    }
    private function delete_all_iawp_tables() : void
    {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $rows = $wpdb->get_results($wpdb->prepare("SELECT table_name FROM information_schema.tables WHERE TABLE_SCHEMA = %s AND table_name LIKE %s", $wpdb->dbname, $prefix . 'independent_analytics_%'));
        foreach ($rows as $row) {
            $wpdb->query('DROP TABLE ' . $row->table_name);
        }
    }
    private function delete_all_post_meta() : void
    {
        \delete_post_meta_by_key(Views_Column::$meta_key);
    }
}
