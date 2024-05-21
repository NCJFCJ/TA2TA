<?php

namespace IAWP\Migrations;

use IAWP\Query;
/** @internal */
class Migration_11 extends \IAWP\Migrations\Migration
{
    /**
     * @var string
     */
    protected $database_version = '11';
    /**
     * @return void
     */
    protected function migrate() : void
    {
        global $wpdb;
        $views_table = Query::get_table_name(Query::VIEWS);
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        $wpdb->query("\n            ALTER TABLE {$views_table} ADD COLUMN next_view_id BIGINT(20) UNSIGNED, ADD COLUMN next_viewed_at DATETIME;\n        ");
        $wpdb->query("\n            ALTER TABLE {$sessions_table} ADD COLUMN final_view_id BIGINT(20) UNSIGNED, ADD COLUMN ended_at DATETIME;\n        ");
        // Populate ended_at for all sessions where legacy_view=0
        //   Find the sessions last view and use it's viewed_at value for sessions.ended_at
        $wpdb->query("\n            UPDATE\n                {$sessions_table} AS sessions\n                    INNER JOIN (\n                        SELECT\n                            sessions.session_id AS session_id,\n                            MAX(views.id) AS final_view_id,\n                            MAX(views.viewed_at) AS ended_at\n                        FROM\n                            {$sessions_table} AS sessions\n                            INNER JOIN {$views_table} AS views ON sessions.session_id = views.session_id\n                                AND sessions.initial_view_id != views.id\n                        WHERE\n                            sessions.legacy_view = FALSE\n                        GROUP BY\n                            sessions.session_id) AS query ON sessions.session_id = query.session_id\n                    SET sessions.ended_at = query.ended_at, sessions.final_view_id = query.final_view_id\n        ");
        // Populate next_viewed_at for all views where session.legacy_view=0
        //   Find the most previous view based on views.viewed_at that belongs to the same session
        $wpdb->query("\n            UPDATE\n                {$views_table} AS views\n                INNER JOIN (\n                    SELECT\n                        views.id AS id,\n                        views.viewed_at AS viewed_at,\n                        MIN(query.id) AS next_view_id,\n                        MIN(query.viewed_at) AS next_viewed_at\n                    FROM\n                        {$views_table} AS views\n                        INNER JOIN (\n                            SELECT\n                                *\n                            FROM\n                                {$views_table} AS views) AS query ON views.session_id = query.session_id\n                                AND views.id != query.id\n                        WHERE\n                            views.id < query.id\n                        GROUP BY\n                            views.id) AS query ON views.id = query.id\n                    SET views.next_viewed_at = query.next_viewed_at, views.next_view_id = query.next_view_id\n        ");
    }
}
