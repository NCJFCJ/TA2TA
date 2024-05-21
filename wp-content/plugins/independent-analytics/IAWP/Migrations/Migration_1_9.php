<?php

namespace IAWP\Migrations;

use IAWP\Query;
/** @internal */
class Migration_1_9 extends \IAWP\Migrations\Migration
{
    /**
     * @var string
     */
    protected $database_version = '1.9';
    /**
     * @return void
     */
    protected function migrate() : void
    {
        global $wpdb;
        $visitors_table = Query::get_table_name(Query::VISITORS);
        $wpdb->query("\n            ALTER TABLE {$visitors_table}\n            ADD (\n               country_code varchar(256),\n               city varchar(256),\n               subdivision varchar(256),\n               country varchar(256),\n               continent varchar(256)\n            )\n        ");
    }
}
