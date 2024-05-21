<?php

namespace IAWP\Public_API;

use IAWP\Date_Range\Date_Range;
use IAWP\Illuminate_Builder;
use IAWP\Query;
use IAWPSCOPED\Illuminate\Database\Query\JoinClause;
/** @internal */
class Singular_Analytics
{
    public $singular_id;
    public $views;
    public $visitors;
    public $sessions;
    private function __construct($singular_id, $row)
    {
        $this->singular_id = $singular_id;
        $this->views = $row->views ?? 0;
        $this->visitors = $row->visitors ?? 0;
        $this->sessions = $row->sessions ?? 0;
    }
    /**
     * @param string|int $singular_id
     * @param Date_Range $date_range
     *
     * @return self
     */
    public static function for($singular_id, Date_Range $date_range) : ?self
    {
        if (\get_post($singular_id) === null) {
            return null;
        }
        $resources_table = Query::get_table_name(Query::RESOURCES);
        $views_table = Query::get_table_name(Query::VIEWS);
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        $resource_statistics_query = Illuminate_Builder::get_builder();
        $resource_statistics_query->selectRaw('COUNT(DISTINCT views.id) AS views')->selectRaw('COUNT(DISTINCT sessions.visitor_id) AS visitors')->selectRaw('COUNT(DISTINCT sessions.session_id) AS sessions')->from("{$views_table} as views")->join("{$resources_table} AS resources", function (JoinClause $join) {
            $join->on('resources.id', '=', 'views.resource_id');
        })->join("{$sessions_table} AS sessions", function (JoinClause $join) {
            $join->on('sessions.session_id', '=', 'views.session_id');
        })->where('resources.resource', '=', 'singular')->where('resources.singular_id', '=', $singular_id)->whereBetween('views.viewed_at', [$date_range->iso_start(), $date_range->iso_end()])->groupBy('resources.id');
        return new self((int) $singular_id, $resource_statistics_query->get()->first());
    }
}
