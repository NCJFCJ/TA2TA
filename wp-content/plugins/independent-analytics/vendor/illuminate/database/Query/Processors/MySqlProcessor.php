<?php

namespace IAWPSCOPED\Illuminate\Database\Query\Processors;

/** @internal */
class MySqlProcessor extends Processor
{
    /**
     * Process the results of a column listing query.
     *
     * @param  array  $results
     * @return array
     */
    public function processColumnListing($results)
    {
        return \array_map(function ($result) {
            return ((object) $result)->column_name;
        }, $results);
    }
}
