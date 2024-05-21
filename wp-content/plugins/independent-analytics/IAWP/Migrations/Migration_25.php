<?php

namespace IAWP\Migrations;

use IAWP\Query;
/** @internal */
class Migration_25 extends \IAWP\Migrations\Step_Migration
{
    /**
     * @return int
     */
    protected function database_version() : int
    {
        return 25;
    }
    /**
     * @return array
     */
    protected function queries() : array
    {
        return [$this->add_temporary_column(), $this->copy_timestamp_data_to_temporary_column(), $this->delete_original_column(), $this->rename_temporary_column()];
    }
    private function add_temporary_column() : string
    {
        $resources_table = Query::get_table_name(Query::RESOURCES);
        return "\n           ALTER TABLE {$resources_table} ADD COLUMN cached_date_temp DATE; \n        ";
    }
    private function copy_timestamp_data_to_temporary_column() : string
    {
        $resources_table = Query::get_table_name(Query::RESOURCES);
        return "\n            UPDATE {$resources_table} SET cached_date_temp = FROM_UNIXTIME(cached_date) WHERE cached_date REGEXP '^[0-9]+\$';\n        ";
    }
    private function delete_original_column() : string
    {
        $resources_table = Query::get_table_name(Query::RESOURCES);
        return "\n           ALTER TABLE {$resources_table} DROP COLUMN cached_date;\n        ";
    }
    private function rename_temporary_column() : string
    {
        $resources_table = Query::get_table_name(Query::RESOURCES);
        return "\n            ALTER TABLE {$resources_table} CHANGE COLUMN cached_date_temp cached_date DATE;\n        ";
    }
}
