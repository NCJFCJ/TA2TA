<?php

namespace IAWP\Statistics;

/** @internal */
class Campaign_Statistics extends \IAWP\Statistics\Statistics
{
    protected function required_column() : ?string
    {
        return 'sessions.campaign_id';
    }
}
