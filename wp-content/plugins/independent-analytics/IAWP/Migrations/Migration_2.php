<?php

namespace IAWP\Migrations;

use IAWP\Query;
/** @internal */
class Migration_2 extends \IAWP\Migrations\Migration
{
    /**
     * @var string
     */
    protected $database_version = '2';
    /**
     * @return void
     */
    protected function migrate() : void
    {
        global $wpdb;
        $resources_table = Query::get_table_name(Query::RESOURCES);
        $wpdb->query("\n            ALTER TABLE {$resources_table}\n            ADD (\n               cached_category varchar(256)\n            )\n        ");
    }
}
