<?php

namespace IAWP\Migrations;

use IAWP\Query;
/** @internal */
class Migration_9 extends \IAWP\Migrations\Migration
{
    /**
     * @var string
     */
    protected $database_version = '9';
    /**
     * @return void
     */
    protected function migrate() : void
    {
        global $wpdb;
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        $visitors_table = Query::get_table_name(Query::VISITORS);
        if ($this->sessions_fix_required()) {
            $wpdb->query("\n                ALTER TABLE {$sessions_table} MODIFY COLUMN visitor_id VARCHAR(32);\n            ");
        }
        if ($this->visitors_fix_required()) {
            $wpdb->query("\n                ALTER TABLE {$visitors_table} MODIFY COLUMN visitor_id VARCHAR(32);\n            ");
        }
    }
    private function sessions_fix_required() : bool
    {
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        return $this->fix_required($sessions_table);
    }
    private function visitors_fix_required() : bool
    {
        $visitors_table = Query::get_table_name(Query::VISITORS);
        return $this->fix_required($visitors_table);
    }
    private function fix_required(string $table) : bool
    {
        global $wpdb;
        $big_field = $wpdb->get_row("\n            SELECT\n                CHARACTER_MAXIMUM_LENGTH\n            FROM\n                information_schema.columns\n            WHERE\n                TABLE_NAME = '" . $table . "'\n                AND COLUMN_NAME = 'visitor_id'\n                AND CHARACTER_MAXIMUM_LENGTH > 32;\n        ");
        if (\is_null($big_field) || $wpdb->last_error !== '') {
            return \false;
        }
        return \true;
    }
}
