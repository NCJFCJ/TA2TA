<?php

namespace IAWP\Migrations;

use IAWP\Query;
/** @internal */
class Migration_1_0 extends \IAWP\Migrations\Migration
{
    /**
     * @var string
     */
    protected $database_version = '1.0';
    /**
     * @return void
     */
    protected function migrate() : void
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $referrers_table = Query::get_table_name(Query::REFERRERS);
        $wpdb->query("DROP TABLE IF EXISTS {$referrers_table}");
        $wpdb->query("CREATE TABLE {$referrers_table} (\n               id bigint(20) UNSIGNED AUTO_INCREMENT,\n               url varchar(2048) NOT NULL,\n               PRIMARY KEY (id)\n           ) {$charset_collate}");
        $views_table = Query::get_table_name(Query::VIEWS);
        $wpdb->query("DROP TABLE IF EXISTS {$views_table}");
        $wpdb->query("CREATE TABLE {$views_table} (\n               id bigint(20) UNSIGNED AUTO_INCREMENT,\n               referrer_id bigint(20) UNSIGNED,\n               resource_id bigint(20) UNSIGNED NOT NULL,\n               viewed_at datetime,\n               PRIMARY KEY (id)\n           ) {$charset_collate}");
        $resources_table = Query::get_table_name(Query::RESOURCES);
        $wpdb->query("DROP TABLE IF EXISTS {$resources_table}");
        $wpdb->query("CREATE TABLE {$resources_table} (\n               id bigint(20) UNSIGNED AUTO_INCREMENT,\n               resource varchar(256) NOT NULL,\n               page bigint(20) UNSIGNED NOT NULL DEFAULT 1,\n               singular_id bigint(20) UNSIGNED,\n               author_id bigint(20) UNSIGNED,\n               date_archive varchar(256),\n               search_query varchar(256),\n               post_type varchar(256),\n               term_id bigint(20) UNSIGNED,\n               not_found_url varchar(256),\n               cached_title varchar(256),\n               cached_url varchar(256),\n               cached_type varchar(256),\n               cached_type_label varchar(256),\n               cached_author_id bigint(20) UNSIGNED,\n               cached_author varchar(256),\n               cached_date varchar(256),\n               PRIMARY KEY (id)\n           ) {$charset_collate}");
    }
}
