<?php

namespace IAWP\Migrations;

/** @internal */
abstract class Migration
{
    /**
     * Classes that extend must provide their database version
     *
     * @var string
     */
    protected $database_version = '0';
    public function __construct()
    {
        $current_db_version = \get_option('iawp_db_version', '0');
        if (\version_compare($current_db_version, $this->database_version, '<')) {
            $this->migrate();
            \update_option('iawp_db_version', $this->database_version);
        }
    }
    /**
     * Classes that extend must define a handle method where migration work is done
     *
     * @return void
     */
    protected abstract function migrate() : void;
}
