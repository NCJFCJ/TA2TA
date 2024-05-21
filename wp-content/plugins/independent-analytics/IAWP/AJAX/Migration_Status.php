<?php

namespace IAWP\AJAX;

use IAWP\Migrations;
/** @internal */
class Migration_Status extends \IAWP\AJAX\AJAX
{
    protected function action_name() : string
    {
        return 'iawp_migration_status';
    }
    protected function allowed_during_migrations() : bool
    {
        return \true;
    }
    protected function action_callback() : void
    {
        if (!Migrations\Migrations::is_actually_migrating()) {
            Migrations\Migrations::create_or_migrate();
        }
        $response = ['isMigrating' => Migrations\Migrations::is_migrating()];
        if (\get_option('iawp_migration_error', null) && \get_option('iawp_migration_error_query', null)) {
            $response['errorHtml'] = \IAWPSCOPED\iawp_blade()->run('interrupt.migration-error', ['plugin_version' => \IAWP_VERSION, 'migration_db_version' => \intval(\get_option('iawp_db_version', 0)) + 1, 'migration_step' => \intval(\get_option('iawp_last_finished_migration_step', 0)) + 1, 'migration_error' => \get_option('iawp_migration_error', null), 'migration_error_query' => \get_option('iawp_migration_error_query', null)]);
        }
        \wp_send_json_success($response);
    }
}
