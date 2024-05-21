<?php

namespace IAWP\Migrations;

use IAWP\Query;
/** @internal */
class Migration_12 extends \IAWP\Migrations\Migration
{
    /**
     * @var string
     */
    protected $database_version = '12';
    /**
     * @return void
     */
    protected function migrate() : void
    {
        global $wpdb;
        $wc_orders_table = Query::get_table_name(Query::WC_ORDERS);
        $wc_order_stats_table = $wpdb->prefix . 'wc_order_stats';
        $wpdb->query("\n            ALTER TABLE {$wc_orders_table} \n                ADD COLUMN total DOUBLE,\n                ADD COLUMN total_refunded DOUBLE,\n                ADD COLUMN total_refunds SMALLINT,\n                ADD COLUMN status VARCHAR(200);\n        ");
        if (\IAWPSCOPED\iawp_using_woocommerce()) {
            $wpdb->query("\n                UPDATE\n                    {$wc_orders_table} AS wc_orders\n                    LEFT JOIN (\n                      SELECT\n                    wc_order_stats.order_id,\n                    wc_order_stats.status,\n                    wc_order_stats.total_sales,\n                    IFNULL(ABS(SUM(refunds.total_sales)), 0) AS total_refunded,\n                    COUNT(DISTINCT refunds.order_id) AS total_refunds\n                FROM\n                    {$wc_orders_table} AS wc_orders\n                    JOIN {$wc_order_stats_table} AS wc_order_stats ON wc_orders.order_id = wc_order_stats.order_id\n                    LEFT JOIN {$wc_order_stats_table} AS refunds ON wc_order_stats.order_id = refunds.parent_id\n                        AND refunds.total_sales < 0\n                    GROUP BY\n                        wc_order_stats.order_id\n                    ) AS wc_order_stats ON wc_orders.order_id = wc_order_stats.order_id\n                SET\n                    wc_orders.status = wc_order_stats.status,\n                    wc_orders.total = wc_order_stats.total_sales,\n                    wc_orders.total_refunded = wc_order_stats.total_refunded,\n                    wc_orders.total_refunds = wc_order_stats.total_refunds;\n            ");
        }
    }
}
