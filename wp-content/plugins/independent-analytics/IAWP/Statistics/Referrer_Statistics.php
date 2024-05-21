<?php

namespace IAWP\Statistics;

/** @internal */
class Referrer_Statistics extends \IAWP\Statistics\Statistics
{
    protected function required_column() : ?string
    {
        return 'sessions.referrer_id';
    }
}
