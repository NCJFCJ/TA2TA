<?php

namespace IAWP\Migrations;

use IAWP\Query;
/** @internal */
class Migration_3 extends \IAWP\Migrations\Migration
{
    /**
     * @var string
     */
    protected $database_version = '3';
    /**
     * @return void
     */
    protected function migrate() : void
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $campaigns_table = Query::get_table_name(Query::CAMPAIGNS);
        $wpdb->query("DROP TABLE IF EXISTS {$campaigns_table}");
        $wpdb->query("CREATE TABLE {$campaigns_table} (\n               campaign_id bigint(20) UNSIGNED AUTO_INCREMENT,\n               utm_source varchar(2048) NOT NULL, \n               utm_medium varchar(2048) NOT NULL,\n               utm_campaign varchar(2048) NOT NULL,\n               utm_term varchar(2048),\n               utm_content varchar(2048),\n               PRIMARY KEY (campaign_id)\n           ) {$charset_collate}");
        $campaign_urls_table = Query::get_table_name(Query::CAMPAIGN_URLS);
        $wpdb->query("DROP TABLE IF EXISTS {$campaign_urls_table}");
        $wpdb->query("CREATE TABLE {$campaign_urls_table} (\n               campaign_url_id bigint(20) UNSIGNED AUTO_INCREMENT,\n               path varchar(2048), \n               utm_source varchar(2048) NOT NULL, \n               utm_medium varchar(2048) NOT NULL,\n               utm_campaign varchar(2048) NOT NULL,\n               utm_term varchar(2048),\n               utm_content varchar(2048),\n               created_at datetime NOT NULL,\n               PRIMARY KEY (campaign_url_id)\n           ) {$charset_collate}");
        $views_table = Query::get_table_name(Query::VIEWS);
        $wpdb->query("\n            ALTER TABLE {$views_table}\n            ADD (\n               campaign_id bigint(20) UNSIGNED\n            )\n        ");
    }
}
