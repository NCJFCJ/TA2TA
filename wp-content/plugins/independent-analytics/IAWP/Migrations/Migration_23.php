<?php

namespace IAWP\Migrations;

use IAWP\Query;
/** @internal */
class Migration_23 extends \IAWP\Migrations\Step_Migration
{
    /**
     * @return int
     */
    protected function database_version() : int
    {
        return 23;
    }
    /**
     * @return array
     */
    protected function queries() : array
    {
        return [$this->maybe_drop_device_types_table(), $this->create_device_types_table(), $this->maybe_drop_device_oss_table(), $this->create_device_oss_table(), $this->maybe_drop_device_browsers_table(), $this->create_device_browsers_table(), $this->add_columns_to_sessions(), $this->populate_device_types(), $this->populate_device_oss(), $this->populate_device_browsers(), $this->link_sessions_with_types(), $this->link_sessions_with_oss(), $this->link_sessions_with_browsers(), $this->drop_device_id_column_from_sessions(), $this->drop_original_devices_table()];
    }
    private function maybe_drop_device_types_table() : string
    {
        $device_types_table = Query::get_table_name(Query::DEVICE_TYPES);
        return "\n            DROP TABLE IF EXISTS {$device_types_table}\n        ";
    }
    private function create_device_types_table() : string
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $device_types_table = Query::get_table_name(Query::DEVICE_TYPES);
        return "\n            CREATE TABLE {$device_types_table} (\n                device_type_id bigint(20) UNSIGNED AUTO_INCREMENT,\n                device_type varchar(64) NOT NULL UNIQUE,\n                PRIMARY KEY (device_type_id)\n            ) {$charset_collate}\n        ";
    }
    private function maybe_drop_device_oss_table() : string
    {
        $device_oss_table = Query::get_table_name(Query::DEVICE_OSS);
        return "\n            DROP TABLE IF EXISTS {$device_oss_table}\n        ";
    }
    private function create_device_oss_table() : string
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $device_oss_table = Query::get_table_name(Query::DEVICE_OSS);
        return "\n            CREATE TABLE {$device_oss_table} (\n                device_os_id bigint(20) UNSIGNED AUTO_INCREMENT,\n                device_os varchar(64) NOT NULL UNIQUE,\n                PRIMARY KEY (device_os_id)\n            ) {$charset_collate}\n        ";
    }
    private function maybe_drop_device_browsers_table() : string
    {
        $device_browsers_table = Query::get_table_name(Query::DEVICE_BROWSERS);
        return "\n            DROP TABLE IF EXISTS {$device_browsers_table}\n        ";
    }
    private function create_device_browsers_table() : string
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $device_browsers_table = Query::get_table_name(Query::DEVICE_BROWSERS);
        return "\n            CREATE TABLE {$device_browsers_table} (\n               device_browser_id bigint(20) UNSIGNED AUTO_INCREMENT,\n               device_browser varchar(64) NOT NULL UNIQUE,\n               PRIMARY KEY (device_browser_id)\n           ) {$charset_collate}\n        ";
    }
    private function add_columns_to_sessions() : string
    {
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        return "\n            ALTER TABLE {$sessions_table}\n            ADD COLUMN device_type_id BIGINT(20) UNSIGNED,\n            ADD COLUMN device_os_id BIGINT(20) UNSIGNED,\n            ADD COLUMN device_browser_id BIGINT(20) UNSIGNED,\n            ADD INDEX (device_type_id),\n            ADD INDEX (device_os_id),\n            ADD INDEX (device_browser_id);\n        ";
    }
    private function populate_device_types() : string
    {
        $devices_table = Query::get_table_name(Query::DEVICES);
        $device_types_table = Query::get_table_name(Query::DEVICE_TYPES);
        return "\n            INSERT INTO {$device_types_table} (device_type)\n            SELECT DISTINCT type\n            FROM {$devices_table} WHERE type IS NOT NULL\n        ";
    }
    private function populate_device_oss() : string
    {
        $devices_table = Query::get_table_name(Query::DEVICES);
        $device_oss_table = Query::get_table_name(Query::DEVICE_OSS);
        return "\n            INSERT INTO {$device_oss_table} (device_os)\n            SELECT DISTINCT os\n            FROM {$devices_table} WHERE os IS NOT NULL\n        ";
    }
    private function populate_device_browsers()
    {
        $devices_table = Query::get_table_name(Query::DEVICES);
        $device_browsers_table = Query::get_table_name(Query::DEVICE_BROWSERS);
        return "\n            INSERT INTO {$device_browsers_table} (device_browser)\n            SELECT DISTINCT browser\n            FROM {$devices_table} WHERE browser IS NOT NULL\n        ";
    }
    private function link_sessions_with_types() : string
    {
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        $devices_table = Query::get_table_name(Query::DEVICES);
        $device_types_table = Query::get_table_name(Query::DEVICE_TYPES);
        return "\n            UPDATE {$sessions_table} AS sessions\n            JOIN {$devices_table} AS devices ON sessions.device_id = devices.device_id\n            JOIN {$device_types_table} AS device_types ON devices.type = device_types.device_type\n            SET sessions.device_type_id = device_types.device_type_id;\n        ";
    }
    private function link_sessions_with_oss() : string
    {
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        $devices_table = Query::get_table_name(Query::DEVICES);
        $device_oss_table = Query::get_table_name(Query::DEVICE_OSS);
        return "\n            UPDATE {$sessions_table} AS sessions\n            JOIN {$devices_table} AS devices ON sessions.device_id = devices.device_id\n            JOIN {$device_oss_table} AS device_oss ON devices.os = device_oss.device_os\n            SET sessions.device_os_id = device_oss.device_os_id;\n        ";
    }
    private function link_sessions_with_browsers() : string
    {
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        $devices_table = Query::get_table_name(Query::DEVICES);
        $device_browsers_table = Query::get_table_name(Query::DEVICE_BROWSERS);
        return "\n            UPDATE {$sessions_table} AS sessions\n            JOIN {$devices_table} AS devices ON sessions.device_id = devices.device_id\n            JOIN {$device_browsers_table} AS device_browsers ON devices.browser = device_browsers.device_browser\n            SET sessions.device_browser_id = device_browsers.device_browser_id;\n        ";
    }
    private function drop_device_id_column_from_sessions() : string
    {
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        return "\n            ALTER TABLE {$sessions_table} DROP COLUMN device_id;\n        ";
    }
    private function drop_original_devices_table() : string
    {
        $devices_table = Query::get_table_name(Query::DEVICES);
        return "\n            DROP TABLE IF EXISTS {$devices_table};\n        ";
    }
}
