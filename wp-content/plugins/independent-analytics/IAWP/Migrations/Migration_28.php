<?php

namespace IAWP\Migrations;

use IAWP\Query;
/** @internal */
class Migration_28 extends \IAWP\Migrations\Step_Migration
{
    /**
     * @return int
     */
    protected function database_version() : int
    {
        return 28;
    }
    /**
     * @return array
     */
    protected function queries() : array
    {
        return [$this->add_initial_view_id_column(), $this->populate_column(), $this->index_column()];
    }
    private function add_initial_view_id_column() : string
    {
        $wc_orders_table = Query::get_table_name(Query::WC_ORDERS);
        return "\n            ALTER TABLE {$wc_orders_table} ADD COLUMN initial_view_id BIGINT(20) UNSIGNED AFTER view_id;\n        ";
    }
    private function populate_column() : string
    {
        $views_table = Query::get_table_name(Query::VIEWS);
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        $wc_orders_table = Query::get_table_name(Query::WC_ORDERS);
        return "\n            UPDATE {$wc_orders_table} AS orders\n            JOIN {$views_table} AS views ON orders.view_id = views.id\n            JOIN {$sessions_table} AS sessions on views.session_id = sessions.session_id\n            SET orders.initial_view_id = sessions.initial_view_id\n        ";
    }
    private function index_column() : string
    {
        $wc_orders_table = Query::get_table_name(Query::WC_ORDERS);
        return "\n            CREATE INDEX initial_view_id ON {$wc_orders_table} (initial_view_id)\n        ";
    }
}
