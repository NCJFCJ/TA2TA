<?php

namespace IAWP\Migrations;

use IAWP\Known_Referrers;
use IAWP\Query;
/** @internal */
class Migration_21 extends \IAWP\Migrations\Migration
{
    /**
     * @var string
     */
    protected $database_version = '21';
    /**
     * @return void
     */
    protected function migrate() : void
    {
        global $wpdb;
        $referrer_groups_table = Query::get_table_name(Query::REFERRER_GROUPS);
        $wpdb->query("\n           ALTER TABLE {$referrer_groups_table} MODIFY COLUMN type ENUM ('Search', 'Social', 'Referrer', 'Ad') NOT NULL;\n        ");
        Known_Referrers::replace_known_referrers_table();
    }
}
