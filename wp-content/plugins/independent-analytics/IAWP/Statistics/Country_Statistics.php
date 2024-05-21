<?php

namespace IAWP\Statistics;

/** @internal */
class Country_Statistics extends \IAWP\Statistics\Statistics
{
    protected function required_column() : ?string
    {
        return 'sessions.country_id';
    }
}
