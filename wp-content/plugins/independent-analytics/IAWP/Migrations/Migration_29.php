<?php

namespace IAWP\Migrations;

use IAWP\Database;
use IAWP\Query;
/** @internal */
class Migration_29 extends \IAWP\Migrations\Step_Migration
{
    /**
     * @return int
     */
    protected function database_version() : int
    {
        return 29;
    }
    /**
     * @return array
     */
    protected function queries() : array
    {
        return [$this->rename_visitor_id_column(), $this->create_new_visitor_id_column(), $this->drop_table_if_exists(), $this->create_visitors_table(), $this->populate_visitors_table(), $this->populate_token_id_column(), $this->add_index(), $this->remove_very_old_visitors_archive_table()];
    }
    private function rename_visitor_id_column() : string
    {
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        return "\n            ALTER TABLE {$sessions_table} CHANGE COLUMN visitor_id old_visitor_id varchar(32);\n        ";
    }
    private function create_new_visitor_id_column() : string
    {
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        return "\n            ALTER TABLE {$sessions_table} ADD COLUMN visitor_id BIGINT(20) UNSIGNED AFTER old_visitor_id\n        ";
    }
    private function drop_table_if_exists() : string
    {
        $visitors_table = Query::get_table_name(Query::VISITORS);
        return "\n            DROP TABLE IF EXISTS {$visitors_table};\n        ";
    }
    private function create_visitors_table() : string
    {
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        $visitors_table = Query::get_table_name(Query::VISITORS);
        $character_set_and_collation = Database::get_character_set_and_collation_for($sessions_table);
        return "\n            CREATE TABLE IF NOT EXISTS {$visitors_table} (\n                visitor_id BIGINT(20) UNSIGNED AUTO_INCREMENT,\n                hash VARCHAR(32) NOT NULL,\n                PRIMARY KEY (visitor_id),\n                UNIQUE INDEX (hash)\n            ) {$character_set_and_collation};\n        ";
    }
    private function populate_visitors_table() : string
    {
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        $visitors_table = Query::get_table_name(Query::VISITORS);
        return "\n            INSERT INTO {$visitors_table} (hash)\n            SELECT DISTINCT old_visitor_id\n            FROM {$sessions_table}\n            WHERE old_visitor_id IS NOT NULL\n        ";
    }
    private function populate_token_id_column() : string
    {
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        $visitors_table = Query::get_table_name(Query::VISITORS);
        return "\n           UPDATE {$sessions_table} AS sessions\n           JOIN {$visitors_table} AS visitors on sessions.old_visitor_id = visitors.hash\n           SET sessions.visitor_id = visitors.visitor_id\n        ";
    }
    private function add_index() : string
    {
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        return "\n                CREATE INDEX new_bigint_visitor_id ON {$sessions_table} (visitor_id)\n            ";
    }
    private function remove_very_old_visitors_archive_table() : string
    {
        $visitors_archive_table = Query::get_table_name(Query::VISITORS_1_16_ARCHIVE);
        return "\n            DROP TABLE IF EXISTS {$visitors_archive_table};\n        ";
    }
}
