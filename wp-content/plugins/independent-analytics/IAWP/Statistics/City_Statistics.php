<?php

namespace IAWP\Statistics;

/** @internal */
class City_Statistics extends \IAWP\Statistics\Statistics
{
    protected function required_column() : ?string
    {
        return 'sessions.city_id';
    }
}
