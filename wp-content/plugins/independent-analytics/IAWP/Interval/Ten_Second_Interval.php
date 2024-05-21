<?php

namespace IAWP\Interval;

use IAWP\Date_Range\Date_Range;
use IAWP\Illuminate_Builder;
use IAWP\Query;
use IAWPSCOPED\Illuminate\Database\Query\JoinClause;
/** @internal */
class Ten_Second_Interval extends \IAWP\Interval\Interval
{
    public function fetch(Date_Range $date_range) : array
    {
        $views_table = Query::get_table_name(Query::VIEWS);
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        return Illuminate_Builder::get_builder()->from($views_table, 'views')->selectRaw('COUNT(DISTINCT (sessions.visitor_id)) AS visitors')->selectRaw('COUNT(*) AS views')->selectRaw("ABS(CEILING(TIMESTAMPDIFF(SECOND, '{$date_range->iso_end()}', views.viewed_at) / 10)) AS interval_ago")->leftJoin("{$sessions_table} AS sessions", function (JoinClause $join) {
            $join->on('views.session_id', '=', 'sessions.session_id');
        })->whereBetween('viewed_at', [$date_range->iso_start(), $date_range->iso_end()])->groupBy('interval_ago')->get()->all();
    }
    public function get_date_interval() : \DateInterval
    {
        return new \DateInterval('PT10S');
    }
    public function short_label() : string
    {
        return \__('sec');
    }
    public function long_label_singular() : string
    {
        return \__('second ago');
    }
    public function long_label_plural() : string
    {
        return \__('seconds ago');
    }
    public function interval_multiplier() : int
    {
        return 10;
    }
}
