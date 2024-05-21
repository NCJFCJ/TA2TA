<?php

namespace IAWP\Filter_Lists;

use IAWP\Illuminate_Builder;
use IAWP\Query;
/** @internal */
class Device_Browser_Filter_List
{
    use \IAWP\Filter_Lists\Filter_List_Trait;
    protected static function fetch_options() : array
    {
        $device_browsers_table = Query::get_table_name(Query::DEVICE_BROWSERS);
        $records = Illuminate_Builder::get_builder()->from($device_browsers_table)->select('device_browser_id', 'device_browser')->get()->all();
        return \array_map(function ($record) {
            return [$record->device_browser_id, $record->device_browser];
        }, $records);
    }
}
