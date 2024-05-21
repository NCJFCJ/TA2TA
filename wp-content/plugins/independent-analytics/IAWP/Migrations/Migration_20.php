<?php

namespace IAWP\Migrations;

use IAWP\Query;
/** @internal */
class Migration_20 extends \IAWP\Migrations\Migration
{
    /**
     * @var string
     */
    protected $database_version = '20';
    /**
     * @return void
     */
    protected function migrate() : void
    {
        global $wpdb;
        $views_table = Query::get_table_name(Query::VIEWS);
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        $wpdb->query("\n            ALTER TABLE {$views_table}\n               ADD INDEX(session_id, viewed_at)\n        ");
        $wpdb->query("\n            ALTER TABLE {$sessions_table}\n               ADD COLUMN total_views int\n        ");
        $wpdb->query("\n            UPDATE\n                {$sessions_table} AS sessions\n            LEFT JOIN (\n                SELECT\n                    session_id,\n                    COUNT(*) AS view_count\n                FROM\n                    {$views_table} AS views\n                GROUP BY\n                    session_id\n            ) AS view_counts ON sessions.session_id = view_counts.session_id\n            SET sessions.total_views = COALESCE(view_counts.view_count, 0)\n        ");
    }
}
