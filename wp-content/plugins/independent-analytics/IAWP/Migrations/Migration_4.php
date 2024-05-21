<?php

namespace IAWP\Migrations;

use IAWP\Known_Referrers;
/** @internal */
class Migration_4 extends \IAWP\Migrations\Migration
{
    /**
     * @var string
     */
    protected $database_version = '4';
    /**
     * @return void
     */
    protected function migrate() : void
    {
        Known_Referrers::replace_known_referrers_table();
    }
}
