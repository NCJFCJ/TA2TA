<?php

namespace IAWP\Filter_Lists;

use IAWP\Illuminate_Builder;
use IAWP\Query;
/** @internal */
class Device_OS_Filter_List
{
    use \IAWP\Filter_Lists\Filter_List_Trait;
    protected static function fetch_options() : array
    {
        $device_oss_table = Query::get_table_name(Query::DEVICE_OSS);
        $records = Illuminate_Builder::get_builder()->from($device_oss_table)->select('device_os_id', 'device_os')->get()->all();
        return \array_map(function ($record) {
            return [$record->device_os_id, $record->device_os];
        }, $records);
    }
}
