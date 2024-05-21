<?php

namespace IAWP\Migrations;

use IAWP\Query;
/** @internal */
class Migration_16 extends \IAWP\Migrations\Migration
{
    /**
     * @var string
     */
    protected $database_version = '16';
    /**
     * @return void
     */
    protected function migrate() : void
    {
        $this->create_reports_table();
    }
    private function create_reports_table() : void
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $reports_table = Query::get_table_name(Query::REPORTS);
        $wpdb->query("DROP TABLE IF EXISTS {$reports_table}");
        $wpdb->query("CREATE TABLE {$reports_table} (\n               report_id bigint(20) UNSIGNED AUTO_INCREMENT,\n               user_created_report boolean NOT NULL DEFAULT true,\n               name varchar(255) NOT NULL,\n               type varchar(64) NOT NULL,\n               exact_start datetime,\n               exact_end datetime,\n               relative_range_id varchar(64),\n               sort_column varchar(64),\n               sort_direction varchar(64),\n               group_name varchar(64),\n               chart_interval varchar(64),\n               columns text,\n               filters text,\n               visible_datasets text,\n               position int,\n               PRIMARY KEY (report_id)\n           ) {$charset_collate}");
    }
}
