<?php

namespace IAWP\Filter_Lists;

use IAWP\Illuminate_Builder;
use IAWP\Query;
/** @internal */
class Device_Type_Filter_List
{
    use \IAWP\Filter_Lists\Filter_List_Trait;
    protected static function fetch_options() : array
    {
        $device_types_table = Query::get_table_name(Query::DEVICE_TYPES);
        $records = Illuminate_Builder::get_builder()->from($device_types_table)->select('device_type_id', 'device_type')->get()->all();
        return \array_map(function ($record) {
            return [$record->device_type_id, $record->device_type];
        }, $records);
    }
}
