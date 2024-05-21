<?php

namespace IAWP\Migrations;

use IAWP\Query;
/** @internal */
class Migration_1_6 extends \IAWP\Migrations\Migration
{
    /**
     * @var string
     */
    protected $database_version = '1.6';
    /**
     * @return void
     */
    protected function migrate() : void
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $views_table = Query::get_table_name(Query::VIEWS);
        $wpdb->query("ALTER TABLE {$views_table} ADD visitor_id bigint(20) UNSIGNED");
        $visitors_table = Query::get_table_name(Query::VISITORS);
        $wpdb->query("DROP TABLE IF EXISTS {$visitors_table}");
        $wpdb->query("CREATE TABLE {$visitors_table} (\n               id bigint(20) UNSIGNED AUTO_INCREMENT,\n               visitor_token varchar(256),\n               PRIMARY KEY (id)\n           ) {$charset_collate}");
    }
}
