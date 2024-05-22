<?php

namespace IAWP\Statistics;

use DatePeriod;
use DateTime;
use IAWP\Date_Range\Date_Range;
use IAWP\Illuminate_Builder;
use IAWP\Query;
use IAWP\Rows\Rows;
use IAWP\Statistics\Intervals\Daily;
use IAWP\Statistics\Intervals\Interval;
use IAWPSCOPED\Illuminate\Database\Query\Builder;
use IAWPSCOPED\Illuminate\Database\Query\JoinClause;
use IAWPSCOPED\Proper\Timezone;
/** @internal */
abstract class Statistics
{
    protected $date_range;
    protected $rows;
    protected $chart_interval;
    private $views;
    private $visitors;
    private $sessions;
    private $average_session_duration;
    private $views_per_session;
    private $bounce_rate;
    private $woocommerce_orders;
    private $woocommerce_net_sales;
    private $statistics_by_day;
    private $statistics;
    private $previous_period_statistics;
    // The biggest flaw here is that it requires two queries for the stats (current and previous) when
    // that could be one. I think it would also be possible to reuse the rows query and not limit
    // by 50 and just SUM() up all the stats columns for the quick stats. Maybe that would be faster
    // even if two queries were still used. Needs testing.
    public function __construct(Date_Range $date_range, ?Rows $rows = null, ?Interval $chart_interval = null)
    {
        $this->date_range = $date_range;
        $this->rows = $rows;
        $this->chart_interval = $chart_interval ?? new Daily();
        $this->statistics_by_day = $this->query($this->date_range, \true);
        $this->statistics = $this->query($this->date_range, \false);
        $this->previous_period_statistics = $this->query($this->date_range->previous_period(), \false);
        $this->views = $this->get_statistic('views');
        $this->visitors = $this->get_statistic('visitors');
        $this->sessions = $this->get_statistic('sessions');
        $this->woocommerce_orders = $this->get_statistic('wc_orders');
        $this->woocommerce_net_sales = $this->get_statistic('wc_net_sales');
        $this->average_session_duration = $this->get_statistic('average_session_duration');
        $this->bounce_rate = new \IAWP\Statistics\Statistic($this->calculate_percent($this->statistics->bounces, $this->statistics->sessions), $this->calculate_percent($this->previous_period_statistics->bounces, $this->previous_period_statistics->sessions));
        $this->views_per_session = new \IAWP\Statistics\Statistic($this->divide($this->statistics->total_views, $this->statistics->sessions, 2), $this->divide($this->previous_period_statistics->total_views, $this->previous_period_statistics->sessions, 2));
    }
    public function views() : \IAWP\Statistics\Statistic
    {
        return $this->views;
    }
    public function visitors() : \IAWP\Statistics\Statistic
    {
        return $this->visitors;
    }
    public function sessions() : \IAWP\Statistics\Statistic
    {
        return $this->sessions;
    }
    public function average_session_duration() : \IAWP\Statistics\Statistic
    {
        return $this->average_session_duration;
    }
    public function woocommerce_orders() : \IAWP\Statistics\Statistic
    {
        return $this->woocommerce_orders;
    }
    public function woocommerce_net_sales() : \IAWP\Statistics\Statistic
    {
        return $this->woocommerce_net_sales;
    }
    public function bounce_rate() : \IAWP\Statistics\Statistic
    {
        return $this->bounce_rate;
    }
    public function view_per_session() : \IAWP\Statistics\Statistic
    {
        return $this->views_per_session;
    }
    public function chart_interval() : Interval
    {
        return $this->chart_interval;
    }
    /**
     * I'm sure there's more we could do here. If you get a result back where there isn't a full
     * page of results or where you're not paginating, then you can just count up the rows...
     *
     * @return int|null
     */
    public function total_number_of_rows() : ?int
    {
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        $views_table = Query::get_table_name(Query::VIEWS);
        $column = $this->total_table_rows_column() ?? $this->required_column();
        $query = Illuminate_Builder::get_builder()->selectRaw("COUNT(DISTINCT {$column}) AS total_table_rows")->from("{$sessions_table} AS sessions")->join("{$views_table} AS views", function (JoinClause $join) {
            $join->on('sessions.session_id', '=', 'views.session_id');
        })->when(!\is_null($this->rows), function (Builder $query) {
            $this->rows->attach_filters($query);
        })->whereBetween('sessions.created_at', [$this->date_range->iso_start(), $this->date_range->iso_end()])->whereBetween('views.viewed_at', [$this->date_range->iso_start(), $this->date_range->iso_end()]);
        return $query->value('total_table_rows');
    }
    /**
     * Define which id column to use to count up the total table rows. This is only required
     * for classes that don't have a required column and don't override required_column
     *
     * @return string|null
     */
    protected function total_table_rows_column() : ?string
    {
        return null;
    }
    /**
     * Statistics can require that a column exists in order to be included. As an example, geos
     * requires visitors.country_code and campaigns requires sessions.campaign_id
     *
     * @return string|null
     */
    protected function required_column() : ?string
    {
        return null;
    }
    private function get_statistic(string $name) : \IAWP\Statistics\Statistic
    {
        return new \IAWP\Statistics\Statistic($this->statistics->{$name}, $this->previous_period_statistics->{$name}, $this->fill_in_partial_day_range($this->statistics_by_day, $name));
    }
    private function query(Date_Range $range, bool $as_daily_statistics)
    {
        $utc_offset = Timezone::utc_offset();
        $site_offset = Timezone::site_offset();
        $sessions_table = Query::get_table_name(Query::SESSIONS);
        $views_table = Query::get_table_name(Query::VIEWS);
        $wc_orders_table = Query::get_table_name(Query::WC_ORDERS);
        $session_statistics = Illuminate_Builder::get_builder();
        $session_statistics->select('sessions.session_id')->selectRaw('COUNT(DISTINCT views.id) AS views')->selectRaw('COUNT(DISTINCT wc_orders.order_id) AS orders')->selectRaw('IFNULL(CAST(SUM(wc_orders.total) AS DECIMAL(10, 2)), 0) AS gross_sales')->selectRaw('IFNULL(CAST(SUM(wc_orders.total_refunded) AS DECIMAL(10, 2)), 0) AS total_refunded')->selectRaw('IFNULL(CAST(SUM(wc_orders.total_refunds) AS UNSIGNED), 0) AS total_refunds')->selectRaw('IFNULL(CAST(SUM(wc_orders.total - wc_orders.total_refunded) AS DECIMAL(10, 2)), 0) AS net_sales')->from("{$sessions_table} AS sessions")->join("{$views_table} AS views", function (JoinClause $join) {
            $join->on('sessions.session_id', '=', 'views.session_id');
        })->leftJoin("{$wc_orders_table} AS wc_orders", function (JoinClause $join) {
            $join->on('views.id', '=', 'wc_orders.initial_view_id')->whereIn('wc_orders.status', ['wc-completed', 'completed', 'wc-processing', 'processing', 'wc-refunded', 'refunded']);
        })->when(!\is_null($this->rows), function (Builder $query) {
            $this->rows->attach_filters($query);
        })->whereBetween('sessions.created_at', [$range->iso_start(), $range->iso_end()])->whereBetween('views.viewed_at', [$range->iso_start(), $range->iso_end()])->groupBy('sessions.session_id')->when(!\is_null($this->required_column()), function (Builder $query) {
            $query->whereNotNull($this->required_column());
        });
        $statistics = Illuminate_Builder::get_builder();
        $statistics->selectRaw('IFNULL(CAST(SUM(sessions.total_views) AS UNSIGNED), 0) AS total_views')->selectRaw('IFNULL(CAST(SUM(session_statistics.views) AS UNSIGNED), 0) AS views')->selectRaw('COUNT(DISTINCT sessions.visitor_id) AS visitors')->selectRaw('COUNT(DISTINCT sessions.session_id) AS sessions')->selectRaw('IFNULL(CAST(AVG(TIMESTAMPDIFF(SECOND, sessions.created_at, sessions.ended_at)) AS UNSIGNED), 0) AS average_session_duration')->selectRaw('COUNT(DISTINCT IF(sessions.final_view_id IS NULL, sessions.session_id, NULL)) AS bounces')->selectRaw('IFNULL(CAST(SUM(session_statistics.orders) AS UNSIGNED), 0) AS wc_orders')->selectRaw('IFNULL(CAST(SUM(session_statistics.gross_sales) AS DECIMAL(10, 2)), 0) AS wc_gross_sales')->selectRaw('IFNULL(CAST(SUM(session_statistics.total_refunds) AS UNSIGNED), 0) AS wc_refunds')->selectRaw('IFNULL(CAST(SUM(session_statistics.total_refunded) AS DECIMAL(10, 2)), 0) AS wc_refunded_amount')->selectRaw('IFNULL(CAST(SUM(session_statistics.net_sales) AS DECIMAL(10, 2)), 0) AS wc_net_sales')->from("{$sessions_table} AS sessions")->joinSub($session_statistics, 'session_statistics', function (JoinClause $join) {
            $join->on('sessions.session_id', '=', 'session_statistics.session_id');
        })->whereBetween('sessions.created_at', [$range->iso_start(), $range->iso_end()])->when($as_daily_statistics, function (Builder $query) use($utc_offset, $site_offset) {
            if ($this->chart_interval->id() === 'daily') {
                $query->selectRaw("DATE(CONVERT_TZ(sessions.created_at, '{$utc_offset}', '{$site_offset}')) AS date");
            } elseif ($this->chart_interval->id() === 'monthly') {
                $query->selectRaw("DATE_FORMAT(CONVERT_TZ(sessions.created_at, '{$utc_offset}', '{$site_offset}'), '%Y-%m-01 00:00:00') AS date");
            } elseif ($this->chart_interval->id() === 'weekly') {
                $day_of_week = \IAWPSCOPED\iawp()->get_option('iawp_dow', 0) + 1;
                $query->selectRaw("\n                               IF (\n                                  DAYOFWEEK(CONVERT_TZ(sessions.created_at, '{$utc_offset}', '{$site_offset}')) - {$day_of_week} < 0,\n                                  DATE_FORMAT(SUBDATE(CONVERT_TZ(sessions.created_at, '{$utc_offset}', '{$site_offset}'), DAYOFWEEK(CONVERT_TZ(sessions.created_at, '{$utc_offset}', '{$site_offset}')) - {$day_of_week} + 7), '%Y-%m-%d 00:00:00'),\n                                  DATE_FORMAT(SUBDATE(CONVERT_TZ(sessions.created_at, '{$utc_offset}', '{$site_offset}'), DAYOFWEEK(CONVERT_TZ(sessions.created_at, '{$utc_offset}', '{$site_offset}')) - {$day_of_week}), '%Y-%m-%d 00:00:00')\n                               ) AS date\n                           ");
            } else {
                $query->selectRaw("DATE_FORMAT(CONVERT_TZ(sessions.created_at, '{$utc_offset}', '{$site_offset}'), '%Y-%m-%d %H:00:00') AS date");
            }
            $query->groupByRaw("date");
        });
        $results = \array_map(function (object $statistic) : object {
            return $this->parse_statistic($statistic);
        }, $statistics->get()->all());
        if (!$as_daily_statistics) {
            return $results[0];
        }
        return $results;
    }
    private function parse_statistic(object $statistic) : object
    {
        $statistic->wc_gross_sales = \floatval($statistic->wc_gross_sales);
        $statistic->wc_refunded_amount = \floatval($statistic->wc_refunded_amount);
        $statistic->wc_net_sales = \floatval($statistic->wc_net_sales);
        return $statistic;
    }
    private function calculate_percent(float $top, float $bottom) : float
    {
        if ($bottom === 0.0 && $top > 0) {
            return 100;
        } elseif ($bottom === 0.0) {
            return 0;
        }
        return \round($top / $bottom * 100, 0);
    }
    private function divide(float $top, float $bottom, int $precision = 0) : float
    {
        if ($bottom === 0.0 && $top > 0) {
            return 100;
        } elseif ($bottom === 0.0) {
            return 0;
        }
        return \round($top / $bottom, $precision);
    }
    /**
     * @param array $partial_day_range
     * @param string $field
     *
     * @return array
     */
    private function fill_in_partial_day_range(array $partial_day_range, string $field) : array
    {
        $original_start = (clone $this->date_range->start())->setTimezone(Timezone::site_timezone());
        $start = $this->chart_interval->calculate_start_of_interval_for($original_start);
        $original_end = (clone $this->date_range->end())->setTimezone(Timezone::site_timezone());
        $end = $this->chart_interval->calculate_start_of_interval_for($original_end);
        $end->add(new \DateInterval('PT1S'));
        $date_range = new DatePeriod($start, $this->chart_interval->date_interval(), $end);
        $filled_in_data = [];
        foreach ($date_range as $date) {
            $stat = $this->get_statistic_for_date($partial_day_range, $date, $field);
            $filled_in_data[] = [$date, $stat];
        }
        return $filled_in_data;
    }
    /**
     * @param array $partial_day_range
     * @param DateTime $datetime_to_match
     * @param string $field
     *
     * @return int Defaults to 0
     */
    private function get_statistic_for_date(array $partial_day_range, DateTime $datetime_to_match, string $field) : int
    {
        $user_timezone = Timezone::site_timezone();
        $default_value = 0;
        foreach ($partial_day_range as $day) {
            $date = $day->date;
            $stat = $day->{$field};
            try {
                $datetime = new DateTime($date, $user_timezone);
            } catch (\Throwable $e) {
                return $default_value;
            }
            // Intentionally using non-strict equality to see if two distinct DateTime objects represent the same time
            if ($datetime == $datetime_to_match) {
                return \intval($stat);
            }
        }
        return $default_value;
    }
}
