<?php

namespace IAWP\Rows;

use IAWP\Illuminate_Builder;
use IAWP\Models\Referrer;
use IAWP\Query;
use IAWPSCOPED\Illuminate\Database\Query\Builder;
use IAWPSCOPED\Illuminate\Database\Query\JoinClause;
/** @internal */
class Referrers extends \IAWP\Rows\Rows
{
    public function attach_filters(Builder $query) : void
    {
        $query->joinSub($this->query(\true), 'referrer_rows', function (JoinClause $join) {
            $join->on('referrer_rows.referrer_id', '=', 'sessions.referrer_id');
        });
    }
    protected function fetch_rows() : array
    {
        $rows = $this->query()->get()->all();
        return \array_map(function ($row) {
            return new Referrer($row);
        }, $rows);
    }
    private function query(?bool $skip_pagination = \false) : Builder
    {
        if ($skip_pagination) {
            $this->number_of_rows = null;
        }
        $views_table = Query::get_table_name(Query::VIEWS);
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        $wc_orders = Query::get_table_name(Query::WC_ORDERS);
        $referrers_table = Query::get_table_name(Query::REFERRERS);
        $current_period_array = [$this->date_range->iso_start(), $this->date_range->iso_end()];
        $previous_period_array = [$this->date_range->previous_period()->iso_start(), $this->date_range->previous_period()->iso_end()];
        $calculated_columns = ['views_per_session', 'views_growth', 'visitors_growth', 'bounce_rate', 'wc_net_sales', 'wc_conversion_rate', 'wc_earnings_per_visitor', 'wc_average_order_volume'];
        $has_calculate_column_filter = !empty(\array_filter($this->filters, function ($filter) use($calculated_columns) {
            return \in_array($filter->column(), $calculated_columns);
        }));
        if (\in_array($this->sort_configuration->column(), $calculated_columns)) {
            $has_calculate_column_filter = \true;
        }
        $woo_commerce_query = Illuminate_Builder::get_builder();
        $woo_commerce_query->select(['wc_orders.view_id AS view_id'])->selectRaw('IFNULL(COUNT(DISTINCT wc_orders.order_id), 0) AS wc_orders')->selectRaw('IFNULL(ROUND(CAST(SUM(wc_orders.total) AS DECIMAL(10, 2))), 0) AS wc_gross_sales')->selectRaw('IFNULL(ROUND(CAST(SUM(wc_orders.total_refunded) AS DECIMAL(10, 2))), 0) AS wc_refunded_amount')->selectRaw('IFNULL(SUM(wc_orders.total_refunds), 0) AS wc_refunds')->from($wc_orders, 'wc_orders')->whereIn('wc_orders.status', ['wc-completed', 'completed', 'wc-processing', 'processing', 'wc-refunded', 'refunded'])->whereBetween('wc_orders.created_at', $current_period_array)->groupBy('wc_orders.view_id');
        $referrers_query = Illuminate_Builder::get_builder();
        $referrers_query->select('sessions.referrer_id', 'referrer', 'type AS referrer_type', 'domain')->selectRaw('COUNT(DISTINCT views.id)  AS views')->selectRaw('COUNT(DISTINCT sessions.visitor_id)  AS visitors')->selectRaw('COUNT(DISTINCT sessions.session_id)  AS sessions')->selectRaw('ROUND(AVG( TIMESTAMPDIFF(SECOND, sessions.created_at, sessions.ended_at))) AS average_session_duration')->selectRaw('COUNT(DISTINCT IF(sessions.final_view_id IS NULL, sessions.session_id, NULL))  AS bounces')->selectRaw('IFNULL(SUM(wc.wc_orders), 0) AS wc_orders')->selectRaw('IFNULL(SUM(wc.wc_gross_sales), 0) AS wc_gross_sales')->selectRaw('IFNULL(SUM(wc.wc_refunded_amount), 0) AS wc_refunded_amount')->selectRaw('IFNULL(SUM(wc_refunds), 0) AS wc_refunds')->from($views_table, 'views')->leftJoin($referrers_query->raw($sessions_table . ' AS sessions'), function (JoinClause $join) {
            $join->on('views.session_id', '=', 'sessions.session_id');
        })->leftJoin($referrers_query->raw($referrers_table . ' AS referrers'), function (JoinClause $join) {
            $join->on('sessions.referrer_id', '=', 'referrers.id');
        })->leftJoinSub($woo_commerce_query, 'wc', function (JoinClause $join) {
            $join->on('wc.view_id', '=', 'views.id');
        })->whereBetween('views.viewed_at', $current_period_array)->whereBetween('sessions.created_at', $current_period_array)->when(\count($this->filters) > 0, function (Builder $query) use($calculated_columns) {
            foreach ($this->filters as $filter) {
                if (!\in_array($filter->column(), $calculated_columns)) {
                    $filter->apply_to_query($query);
                }
            }
        })->groupBy('sessions.referrer_id')->having('views', '>', 0)->when(!$has_calculate_column_filter, function (Builder $query) {
            $query->when($this->sort_configuration->is_nullable(), function (Builder $query) {
                $query->orderByRaw("CASE WHEN {$this->sort_configuration->column()} IS NULL THEN 1 ELSE 0 END");
            })->orderBy($this->sort_configuration->column(), $this->sort_configuration->direction())->orderBy('referrer')->when(\is_int($this->number_of_rows), function (Builder $query) {
                $query->limit($this->number_of_rows);
            });
        });
        $previous_period_query = Illuminate_Builder::get_builder();
        $previous_period_query->select(['sessions.referrer_id'])->selectRaw('SUM(sessions.total_views) AS previous_period_views')->selectRaw('COUNT(DISTINCT sessions.visitor_id) AS previous_period_visitors')->from($sessions_table, 'sessions')->whereBetween('sessions.created_at', $previous_period_array)->groupBy('sessions.referrer_id');
        $outer_query = Illuminate_Builder::get_builder();
        $outer_query->selectRaw('referrers.*')->selectRaw('IF(sessions = 0, 0, views / sessions) AS views_per_session')->selectRaw('IFNULL((views - previous_period_views) / previous_period_views * 100, 0) AS views_growth')->selectRaw('IFNULL((visitors - previous_period_visitors) / previous_period_visitors * 100, 0) AS visitors_growth')->selectRaw('IFNULL(bounces / sessions * 100, 0) AS bounce_rate')->selectRaw('ROUND(CAST(wc_gross_sales - wc_refunded_amount AS DECIMAL(10, 2))) AS wc_net_sales')->selectRaw('IF(visitors = 0, 0, (wc_orders / visitors) * 100) AS wc_conversion_rate')->selectRaw('IF(visitors = 0, 0, (wc_gross_sales - wc_refunded_amount) / visitors) AS wc_earnings_per_visitor')->selectRaw('IF(wc_orders = 0, 0, ROUND(CAST(wc_gross_sales / wc_orders AS DECIMAL(10, 2)))) AS wc_average_order_volume')->when(\count($this->filters) > 0, function (Builder $query) use($calculated_columns) {
            foreach ($this->filters as $filter) {
                if (\in_array($filter->column(), $calculated_columns)) {
                    $filter->apply_to_query($query);
                }
            }
        })->fromSub($referrers_query, 'referrers')->leftJoinSub($previous_period_query, 'previous_period_stats', 'referrers.referrer_id', '=', 'previous_period_stats.referrer_id')->when($has_calculate_column_filter, function (Builder $query) {
            $query->when($this->sort_configuration->is_nullable(), function (Builder $query) {
                $query->orderByRaw("CASE WHEN {$this->sort_configuration->column()} IS NULL THEN 1 ELSE 0 END");
            })->orderBy($this->sort_configuration->column(), $this->sort_configuration->direction())->orderBy('referrer')->when(\is_int($this->number_of_rows), function (Builder $query) {
                $query->limit($this->number_of_rows);
            });
        });
        return $outer_query;
    }
}
