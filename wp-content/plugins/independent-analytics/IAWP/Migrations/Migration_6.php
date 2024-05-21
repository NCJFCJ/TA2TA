<?php

namespace IAWP\Migrations;

use IAWP\Known_Referrers;
/** @internal */
class Migration_6 extends \IAWP\Migrations\Migration
{
    /**
     * @var string
     */
    protected $database_version = '6';
    /**
     * @return void
     */
    protected function migrate() : void
    {
        Known_Referrers::replace_known_referrers_table();
    }
}
