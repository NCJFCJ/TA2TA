<?php

namespace IAWP\Statistics;

/** @internal */
class Device_Type_Statistics extends \IAWP\Statistics\Statistics
{
    protected function required_column() : ?string
    {
        return 'sessions.device_type_id';
    }
}
