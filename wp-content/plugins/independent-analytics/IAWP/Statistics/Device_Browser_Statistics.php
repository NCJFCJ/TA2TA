<?php

namespace IAWP\Statistics;

/** @internal */
class Device_Browser_Statistics extends \IAWP\Statistics\Statistics
{
    protected function required_column() : ?string
    {
        return 'sessions.device_browser_id';
    }
}
