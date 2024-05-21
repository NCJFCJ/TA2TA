<?php

namespace IAWP\Migrations;

use IAWP\Query;
/** @internal */
class Migration_14 extends \IAWP\Migrations\Migration
{
    /**
     * @var string
     */
    protected $database_version = '14';
    /**
     * @return void
     */
    protected function migrate() : void
    {
        $this->create_devices_table();
        $this->add_foreign_keys_to_sessions();
    }
    private function create_devices_table()
    {
        global $wpdb;
        $charset_and_collation = $wpdb->get_charset_collate();
        $devices_table = Query::get_table_name(Query::DEVICES);
        $wpdb->query("DROP TABLE IF EXISTS {$devices_table}");
        $wpdb->query("\n            CREATE TABLE {$devices_table} (\n               device_id bigint(20) UNSIGNED AUTO_INCREMENT,\n               type varchar(16),\n               os varchar(16),\n               browser varchar(32),\n               PRIMARY KEY (device_id)\n            ) {$charset_and_collation}\n        ");
        $wpdb->query("\n            CREATE UNIQUE INDEX devices_unique_index\n            ON {$devices_table} (type, os, browser)\n        ");
        $wpdb->query("\n            CREATE INDEX type_index\n            ON {$devices_table} (type)\n        ");
        $wpdb->query("\n            CREATE INDEX os_index\n            ON {$devices_table} (os)\n        ");
        $wpdb->query("\n            CREATE INDEX browser_index\n            ON {$devices_table} (browser)\n        ");
    }
    private function add_foreign_keys_to_sessions()
    {
        global $wpdb;
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        $wpdb->query("\n            ALTER TABLE {$sessions_table}\n                ADD COLUMN device_id BIGINT(20) UNSIGNED;\n        ");
        $wpdb->query("\n            ALTER TABLE {$sessions_table}\n                ADD INDEX(device_id);\n        ");
    }
}
