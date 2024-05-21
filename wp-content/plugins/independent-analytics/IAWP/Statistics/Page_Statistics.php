<?php

namespace IAWP\Statistics;

/** @internal */
class Page_Statistics extends \IAWP\Statistics\Statistics
{
    public function total_table_rows_column() : ?string
    {
        return 'views.resource_id';
    }
}
