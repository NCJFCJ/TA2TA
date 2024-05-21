<?php

namespace IAWP\Migrations;

use IAWP\Utils\WP_Async_Request;
/** @internal */
class Migration_Job extends WP_Async_Request
{
    /**
     * @var string
     */
    protected $action = 'iawp_database_migration';
    /**
     * Handle
     *
     * Override this method to perform any actions required
     * during the async request.
     *
     * @return void
     */
    protected function handle() : void
    {
        \IAWP\Migrations\Migrations::create_or_migrate();
    }
    /**
     * Dispatch a migration job if the database is out of date and no migration is currently running
     *
     * @return void
     */
    public static function maybe_dispatch() : void
    {
        if (\IAWP\Migrations\Migrations::should_migrate()) {
            $migration_job = new self();
            $migration_job->dispatch();
        }
    }
}
