<?php

namespace IAWP\Migrations;

use IAWP\Query;
/** @internal */
class Migration_7 extends \IAWP\Migrations\Migration
{
    /**
     * @var string
     */
    protected $database_version = '7';
    /**
     * @return void
     */
    protected function migrate() : void
    {
        global $wpdb;
        \update_option('iawp_need_clear_cache', \true);
        $charset_collate = $wpdb->get_charset_collate();
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        $views_table = Query::get_table_name(Query::VIEWS);
        $visitors_table = Query::get_table_name(Query::VISITORS);
        $wpdb->query("DROP TABLE IF EXISTS {$sessions_table};");
        $wpdb->query("CREATE TABLE {$sessions_table} (\n               session_id bigint(20) UNSIGNED AUTO_INCREMENT,\n               visitor_id varchar(32),\n               initial_view_id bigint(20) UNSIGNED,\n               referrer_id bigint(20) UNSIGNED,\n               campaign_id bigint(20) UNSIGNED,\n               created_at datetime NOT NULL,\n               legacy_view boolean DEFAULT false NOT NULL,\n               PRIMARY KEY (session_id)\n           ) {$charset_collate};");
        $wpdb->query("\n            ALTER TABLE {$views_table}\n            ADD (\n               session_id bigint(20) UNSIGNED\n            ),\n                ADD INDEX (visitor_id);\n        ");
        $wpdb->query("\n            INSERT INTO {$sessions_table} (visitor_id, initial_view_id, referrer_id, campaign_id, created_at, legacy_view)\n            SELECT  IF(visitors.visitor_token = '', NULL, LEFT(visitors.visitor_token, 32)) AS visitor_id,\n                views.id        AS initial_view_id,\n                views.referrer_id,\n                views.campaign_id,\n                views.viewed_at AS created_at,\n                1         AS legacy_view\n            FROM {$views_table} AS views\n                LEFT JOIN {$visitors_table} AS visitors ON views.visitor_id = visitors.id;\n        ");
        // Add the indices after bulk insert
        $wpdb->query("\n            ALTER TABLE {$sessions_table}\n               ADD INDEX(visitor_id),\n               ADD INDEX(initial_view_id),\n               ADD INDEX(referrer_id),\n               ADD INDEX(campaign_id);\n        ");
        $wpdb->query("\n            UPDATE\n                {$views_table} AS views\n                INNER JOIN {$sessions_table} AS sessions ON views.id = sessions.initial_view_id\n            SET\n                views.session_id = sessions.session_id;\n        ");
        $wpdb->query("\n            ALTER TABLE {$views_table} DROP COLUMN referrer_id, DROP INDEX visitor_id, DROP COLUMN visitor_id, DROP COLUMN campaign_id, ADD INDEX (session_id);\n        ");
        $visitors_tmp_table = Query::get_table_name(Query::VISITORS_TMP);
        $visitors_archive_table = Query::get_table_name(Query::VISITORS_1_16_ARCHIVE);
        $wpdb->query("DROP TABLE IF EXISTS {$visitors_tmp_table};");
        $wpdb->query("CREATE TABLE {$visitors_tmp_table} (\n               visitor_id varchar(32),\n               country_code varchar(256),\n               city varchar(256),\n               subdivision varchar(256),\n               country varchar(256),\n               continent varchar(256),\n               PRIMARY KEY (visitor_id)\n           ) {$charset_collate};");
        $wpdb->query("INSERT INTO {$visitors_tmp_table} (visitor_id, country_code, city, subdivision, country, continent)\n            SELECT\n                LEFT(visitor_token, 32) AS visitor_token,\n                country_code,\n                city,\n                subdivision,\n                country,\n                continent\n            FROM\n                {$visitors_table} AS visitors ON DUPLICATE KEY UPDATE country_code = visitors.country_code,\n                city = visitors.city,\n                subdivision = visitors.subdivision,\n                country = visitors.country,\n                continent = visitors.continent;\n        ");
        $wpdb->query("DROP TABLE IF EXISTS {$visitors_archive_table};");
        $wpdb->query("RENAME TABLE {$visitors_table} TO {$visitors_archive_table};");
        $wpdb->query("RENAME TABLE {$visitors_tmp_table} TO {$visitors_table};");
    }
}
