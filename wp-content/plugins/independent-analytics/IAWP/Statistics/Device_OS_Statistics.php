<?php

namespace IAWP\Statistics;

/** @internal */
class Device_OS_Statistics extends \IAWP\Statistics\Statistics
{
    protected function required_column() : ?string
    {
        return 'sessions.device_os_id';
    }
}
