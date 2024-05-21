<?php

namespace IAWP\Migrations;

/** @internal */
abstract class Step_Migration
{
    protected abstract function database_version() : int;
    protected abstract function queries() : array;
    public function migrate() : bool
    {
        $current_db_version = \get_option('iawp_db_version', '0');
        if (\version_compare($current_db_version, \strval($this->database_version()), '>=')) {
            return \true;
        }
        $completed = $this->run_queries();
        if ($completed) {
            \update_option('iawp_db_version', $this->database_version());
        }
        return $completed;
    }
    private function run_queries() : bool
    {
        global $wpdb;
        $queries = $this->queries();
        foreach ($queries as $index => $query) {
            // Skip the step if there is no query to run
            if (\is_null($query)) {
                \update_option('iawp_last_finished_migration_step', $index + 1);
                continue;
            }
            $wpdb->query($query);
            if ($wpdb->last_error !== '') {
                $is_connected = $wpdb->check_connection(\false);
                if (!$is_connected) {
                    \IAWPSCOPED\iawp_log('Independent Analytics: Your database connection was temporarily lost');
                    return \false;
                }
                $wpdb->query($query);
                if ($wpdb->last_error !== '') {
                    $last_error = \trim($wpdb->last_error);
                    $last_query = \trim($wpdb->last_query);
                    // Must call update_option after store the last_error and last_query
                    \update_option('iawp_migration_error', $last_error);
                    \update_option('iawp_migration_error_query', $last_query);
                    return \false;
                }
            }
            \update_option('iawp_last_finished_migration_step', $index + 1);
        }
        return \true;
    }
    public static function has_index(string $table, string $name) : bool
    {
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("\n                SHOW INDEX FROM {$table} WHERE Key_name = %s\n            ", $name));
        return !\is_null($row);
    }
}
