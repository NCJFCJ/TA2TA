<?php

namespace IAWP\Rows;

use IAWP\Illuminate_Builder;
use IAWP\Models\Page;
use IAWP\Query;
use IAWPSCOPED\Illuminate\Database\Query\Builder;
use IAWPSCOPED\Illuminate\Database\Query\JoinClause;
/** @internal */
class Pages extends \IAWP\Rows\Rows
{
    private static $has_wp_comments_table = null;
    public function attach_filters(Builder $query) : void
    {
        $query->joinSub($this->query(\true), 'page_rows', function (JoinClause $join) {
            $join->on('page_rows.id', '=', 'views.resource_id');
        });
    }
    protected function fetch_rows() : array
    {
        $rows = $this->query()->get()->all();
        return \array_map(function (object $row) {
            return Page::from_row($row);
        }, $rows);
    }
    private function has_wp_comments_table() : bool
    {
        if (\is_bool(self::$has_wp_comments_table)) {
            return self::$has_wp_comments_table;
        }
        global $wpdb;
        $table_name = $wpdb->prefix . 'comments';
        $tables = $wpdb->get_row($wpdb->prepare("SHOW TABLES LIKE %s", $table_name));
        self::$has_wp_comments_table = !\is_null($tables);
        return self::$has_wp_comments_table;
    }
    private function query(?bool $skip_pagination = \false) : Builder
    {
        global $wpdb;
        if ($skip_pagination) {
            $this->number_of_rows = null;
        }
        $views_table = Query::get_table_name(Query::VIEWS);
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        $resources_table = Query::get_table_name(Query::RESOURCES);
        $wc_orders_table = Query::get_table_name(Query::WC_ORDERS);
        $comments_table = $wpdb->prefix . 'comments';
        $current_period_array = [$this->date_range->iso_start(), $this->date_range->iso_end()];
        $previous_period_array = [$this->date_range->previous_period()->iso_start(), $this->date_range->previous_period()->iso_end()];
        $total_period_array = [$this->date_range->previous_period()->iso_start(), $this->date_range->iso_end()];
        $calculated_columns = ['comments', 'views_growth', 'visitors_growth', 'bounce_rate', 'exit_percent', 'wc_net_sales', 'wc_conversion_rate', 'wc_earnings_per_visitor', 'wc_average_order_volume'];
        $has_calculate_column_filter = !empty(\array_filter($this->filters, function ($filter) use($calculated_columns) {
            return \in_array($filter->column(), $calculated_columns);
        }));
        if (\in_array($this->sort_configuration->column(), $calculated_columns)) {
            $has_calculate_column_filter = \true;
        }
        $database_sort_columns = ['title' => 'cached_title', 'url' => 'cached_url', 'author' => 'cached_author', 'type' => 'cached_type_label', 'date' => 'cached_date', 'category' => 'cached_category'];
        $sort_column = $this->sort_configuration->column();
        foreach ($database_sort_columns as $key => $value) {
            if ($sort_column === $key) {
                $sort_column = $value;
            }
        }
        $woo_commerce_query = Illuminate_Builder::get_builder();
        $woo_commerce_query->select(['sessions.initial_view_id AS view_id'])->selectRaw('IFNULL(COUNT(DISTINCT wc_orders.order_id), 0) AS wc_orders')->selectRaw('IFNULL(ROUND(CAST(SUM(wc_orders.total) AS DECIMAL(10, 2))), 0) AS wc_gross_sales')->selectRaw('IFNULL(ROUND(CAST(SUM(wc_orders.total_refunded) AS DECIMAL(10, 2))), 0) AS wc_refunded_amount')->selectRaw('IFNULL(SUM(wc_orders.total_refunds), 0) AS wc_refunds')->from($wc_orders_table, 'wc_orders')->leftJoin($woo_commerce_query->raw($views_table . ' AS views'), function (JoinClause $join) {
            $join->on('wc_orders.view_id', '=', 'views.id');
        })->leftJoin($woo_commerce_query->raw($sessions_table . ' AS sessions'), function (JoinClause $join) {
            $join->on('views.session_id', '=', 'sessions.session_id');
        })->whereIn('wc_orders.status', ['wc-completed', 'completed', 'wc-processing', 'processing', 'wc-refunded', 'refunded'])->whereBetween('wc_orders.created_at', $current_period_array)->groupBy('wc_orders.view_id');
        $pages_query = Illuminate_Builder::get_builder();
        $pages_query->select('resources.*')->selectRaw('COUNT(DISTINCT IF(views.viewed_at BETWEEN ? AND ?, views.id, NULL))  AS views', $current_period_array)->selectRaw('COUNT(DISTINCT IF(views.viewed_at BETWEEN ? AND ?, views.id, NULL))  AS previous_period_views', $previous_period_array)->selectRaw('COUNT(DISTINCT IF(views.viewed_at BETWEEN ? AND ?, sessions.visitor_id, NULL))  AS visitors', $current_period_array)->selectRaw('COUNT(DISTINCT IF(views.viewed_at BETWEEN ? AND ? AND initial_view.resource_id = resources.id, sessions.visitor_id, NULL))  AS landing_page_visitors', $current_period_array)->selectRaw('COUNT(DISTINCT IF(views.viewed_at BETWEEN ? AND ?, sessions.visitor_id, NULL))  AS previous_period_visitors', $previous_period_array)->selectRaw('COUNT(DISTINCT IF(views.viewed_at BETWEEN ? AND ?, sessions.session_id, NULL))  AS sessions', $current_period_array)->selectRaw('COUNT(DISTINCT IF(views.viewed_at BETWEEN ? AND ? AND sessions.final_view_id IS NULL, sessions.session_id, NULL))  AS bounces', $current_period_array)->selectRaw('IF(resources.singular_id IS NOT NULL, COUNT(DISTINCT IF(comments.comment_date_gmt BETWEEN ? AND ? AND comments.comment_approved = "1", comments.comment_ID, null)), NULL) as comments', $current_period_array)->selectRaw('AVG(IF(views.viewed_at BETWEEN ? AND ?, TIMESTAMPDIFF(SECOND, views.viewed_at, views.next_viewed_at), NULL))  AS average_view_duration', $current_period_array)->selectRaw('COUNT(DISTINCT IF(views.viewed_at BETWEEN ? AND ? AND resources.id = initial_view.resource_id, sessions.session_id, NULL))  AS entrances', $current_period_array)->selectRaw('COUNT(DISTINCT IF(views.viewed_at BETWEEN ? AND ? AND (resources.id = final_view.resource_id OR (resources.id = initial_view.resource_id AND sessions.final_view_id IS NULL)), sessions.session_id, NULL))  AS exits', $current_period_array)->selectRaw('IFNULL(SUM(wc.wc_orders), 0) AS wc_orders')->selectRaw('IFNULL(SUM(wc.wc_gross_sales), 0) AS wc_gross_sales')->selectRaw('IFNULL(SUM(wc.wc_refunded_amount), 0) AS wc_refunded_amount')->selectRaw('IFNULL(SUM(wc_refunds), 0) AS wc_refunds')->from($views_table, 'views')->leftJoin($pages_query->raw($sessions_table . ' AS sessions'), function (JoinClause $join) {
            $join->on('views.session_id', '=', 'sessions.session_id');
        })->leftJoin($pages_query->raw($resources_table . ' AS resources'), function (JoinClause $join) {
            $join->on('views.resource_id', '=', 'resources.id');
        })->leftJoin($pages_query->raw($views_table . ' AS initial_view'), function (JoinClause $join) {
            $join->on('sessions.initial_view_id', '=', 'initial_view.id');
        })->leftJoin($pages_query->raw($views_table . ' AS final_view'), function (JoinClause $join) {
            $join->on('sessions.final_view_id', '=', 'final_view.id');
        })->leftJoinSub($woo_commerce_query, 'wc', function (JoinClause $join) {
            $join->on('wc.view_id', '=', 'views.id');
        })->when($this->has_wp_comments_table(), function (Builder $query) use($comments_table) {
            $query->leftJoin($query->raw($comments_table . ' AS comments'), function (JoinClause $join) {
                $join->on('resources.singular_id', '=', 'comments.comment_post_ID');
            });
        }, function (Builder $query) {
            $query->leftJoinSub('SELECT now() AS comment_date_gmt, 0 AS comment_ID, 0 AS comment_post_ID, "1" AS comment_approved LIMIT 0', 'comments', function (JoinClause $join) {
                $join->on('resources.singular_id', '=', 'comments.comment_post_ID');
            });
        })->whereBetween('views.viewed_at', $total_period_array)->whereBetween('sessions.created_at', $total_period_array)->where(function (Builder $query) use($total_period_array) {
            $query->whereNull('sessions.ended_at')->orWhereBetween('sessions.ended_at', $total_period_array);
        })->where(function (Builder $query) use($total_period_array) {
            $query->whereNull('initial_view.viewed_at')->orWhereBetween('initial_view.viewed_at', $total_period_array);
        })->where(function (Builder $query) use($total_period_array) {
            $query->whereNull('final_view.viewed_at')->orWhereBetween('final_view.viewed_at', $total_period_array);
        })->when(\count($this->filters) > 0, function (Builder $query) use($calculated_columns) {
            foreach ($this->filters as $filter) {
                if (!\in_array($filter->column(), $calculated_columns)) {
                    $filter->apply_to_query($query);
                }
            }
        })->groupBy('resources.id')->having('views', '>', 0)->when(!$has_calculate_column_filter, function (Builder $query) use($sort_column) {
            $query->when($this->sort_configuration->is_nullable(), function (Builder $query) use($sort_column) {
                $query->orderByRaw("CASE WHEN {$sort_column} IS NULL THEN 1 ELSE 0 END");
            })->orderBy($sort_column, $this->sort_configuration->direction())->orderBy('cached_title')->when(\is_int($this->number_of_rows), function (Builder $query) {
                $query->limit($this->number_of_rows);
            });
        });
        $outer_query = Illuminate_Builder::get_builder();
        $outer_query->selectRaw('pages.*')->selectRaw('IFNULL((views - previous_period_views) / previous_period_views * 100, 0) AS views_growth')->selectRaw('IFNULL((visitors - previous_period_visitors) / previous_period_visitors * 100, 0) AS visitors_growth')->selectRaw('IFNULL(bounces / sessions * 100, 0) AS bounce_rate')->selectRaw('IFNULL((exits / views) * 100, 0) AS exit_percent')->selectRaw('ROUND(CAST(wc_gross_sales - wc_refunded_amount AS DECIMAL(10, 2))) AS wc_net_sales')->selectRaw('IF(visitors = 0, 0, (wc_orders / landing_page_visitors) * 100) AS wc_conversion_rate')->selectRaw('IF(visitors = 0, 0, (wc_gross_sales - wc_refunded_amount) / landing_page_visitors) AS wc_earnings_per_visitor')->selectRaw('IF(wc_orders = 0, 0, ROUND(CAST(wc_gross_sales / wc_orders AS DECIMAL(10, 2)))) AS wc_average_order_volume')->when(\count($this->filters) > 0, function (Builder $query) use($calculated_columns) {
            foreach ($this->filters as $filter) {
                if (\in_array($filter->column(), $calculated_columns)) {
                    $filter->apply_to_query($query);
                }
            }
        })->fromSub($pages_query, 'pages')->when($has_calculate_column_filter, function (Builder $query) use($sort_column) {
            $query->when($this->sort_configuration->is_nullable(), function (Builder $query) use($sort_column) {
                $query->orderByRaw("CASE WHEN {$sort_column} IS NULL THEN 1 ELSE 0 END");
            })->orderBy($sort_column, $this->sort_configuration->direction())->orderBy('cached_title')->when(\is_int($this->number_of_rows), function (Builder $query) {
                $query->limit($this->number_of_rows);
            });
        });
        return $outer_query;
    }
}
