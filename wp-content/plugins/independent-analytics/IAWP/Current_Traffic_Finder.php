<?php

namespace IAWP;

use IAWP\Date_Range\Date_Range;
use IAWP\Models\Current_Traffic;
use IAWPSCOPED\Illuminate\Database\Query\JoinClause;
/** @internal */
class Current_Traffic_Finder
{
    /**
     * @var Date_Range
     */
    private $date_range;
    /**
     * @param Date_Range $date_range Range to fetch referrers for
     */
    public function __construct(Date_Range $date_range)
    {
        $this->date_range = $date_range;
    }
    public function fetch() : Current_Traffic
    {
        $views_table = \IAWP\Query::get_table_name(\IAWP\Query::VIEWS);
        $sessions_table = \IAWP\Query::get_table_name(\IAWP\Query::SESSIONS);
        $row = \IAWP\Illuminate_Builder::get_builder()->from($views_table, 'views')->selectRaw('COUNT(DISTINCT (sessions.visitor_id)) AS visitor_count')->selectRaw('COUNT(DISTINCT (views.resource_id)) AS page_count')->selectRaw('COUNT(DISTINCT (sessions.referrer_id)) AS referrer_count')->selectRaw('COUNT(DISTINCT (sessions.country_id)) AS country_count')->selectRaw('COUNT(DISTINCT (sessions.campaign_id)) AS campaign_count')->selectRaw('COUNT(*) AS view_count')->leftJoin("{$sessions_table} AS sessions", function (JoinClause $join) {
            $join->on('views.session_id', '=', 'sessions.session_id');
        })->whereBetween('viewed_at', [$this->date_range->iso_start(), $this->date_range->iso_end()])->first();
        return new Current_Traffic($row);
    }
}
