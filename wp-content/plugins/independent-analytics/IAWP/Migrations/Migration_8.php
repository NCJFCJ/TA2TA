<?php

namespace IAWP\Migrations;

use IAWP\Query;
/** @internal */
class Migration_8 extends \IAWP\Migrations\Migration
{
    /**
     * @var string
     */
    protected $database_version = '8';
    /**
     * @return void
     */
    protected function migrate() : void
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $wc_orders_table = Query::get_table_name(Query::WC_ORDERS);
        $wpdb->query("DROP TABLE IF EXISTS {$wc_orders_table};");
        $wpdb->query("CREATE TABLE {$wc_orders_table} (\n               order_id bigint(20) UNSIGNED NOT NULL,\n               view_id bigint(20) UNSIGNED NOT NULL,\n               created_at datetime NOT NULL,\n               PRIMARY KEY (order_id)\n           ) {$charset_collate};");
    }
}
