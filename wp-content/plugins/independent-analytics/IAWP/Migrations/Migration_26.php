<?php

namespace IAWP\Migrations;

use IAWP\Query;
/** @internal */
class Migration_26 extends \IAWP\Migrations\Step_Migration
{
    /**
     * @return int
     */
    protected function database_version() : int
    {
        return 26;
    }
    /**
     * @return array
     */
    protected function queries() : array
    {
        return [$this->set_empty_values_to_null('cached_title'), $this->set_empty_values_to_null('cached_url'), $this->set_empty_values_to_null('cached_type'), $this->set_empty_values_to_null('cached_type_label'), $this->set_empty_values_to_null('cached_author'), $this->set_empty_values_to_null('cached_category'), $this->nullify_authors_with_id_of_zero()];
    }
    private function set_empty_values_to_null(string $column) : string
    {
        $resources_table = Query::get_table_name(Query::RESOURCES);
        return "\n            UPDATE {$resources_table} SET {$column} = NULL WHERE {$column} = '' \n        ";
    }
    private function nullify_authors_with_id_of_zero() : string
    {
        $resources_table = Query::get_table_name(Query::RESOURCES);
        return "\n            UPDATE {$resources_table} SET cached_author_id = NULL, cached_author = NULL WHERE cached_author_id = 0\n        ";
    }
}
