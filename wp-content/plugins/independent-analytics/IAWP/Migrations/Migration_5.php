<?php

namespace IAWP\Migrations;

use IAWP\Known_Referrers;
/** @internal */
class Migration_5 extends \IAWP\Migrations\Migration
{
    /**
     * @var string
     */
    protected $database_version = '5';
    /**
     * @return void
     */
    protected function migrate() : void
    {
        Known_Referrers::replace_known_referrers_table();
    }
}
