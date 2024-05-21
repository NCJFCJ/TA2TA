<?php

namespace IAWP;

use DateTime;
use IAWP\Date_Range\Date_Range;
use IAWP\Date_Range\Exact_Date_Range;
use IAWP\Date_Range\Relative_Date_Range;
use IAWP\Statistics\Intervals\Interval;
use IAWP\Statistics\Intervals\Intervals;
use IAWP\Tables\Table;
use IAWPSCOPED\Proper\Timezone;
use Throwable;
/**
 * Dashboards support various options via the search query string portion of the URL.
 *
 * The Dashboard_Options class give you an interface for fetching any set values or falling back
 * to a default value as needed.
 * @internal
 */
class Dashboard_Options
{
    private $report_id;
    private $report;
    public function __construct()
    {
        $this->report_id = \array_key_exists('report', $_GET) ? \sanitize_text_field($_GET['report']) : null;
        $this->report = $this->fetch_report_by_id($this->report_id);
    }
    public function report_name() : ?string
    {
        if (\is_null($this->report->name ?? null)) {
            return 'Report';
        }
        return $this->report->name;
    }
    public function columns() : ?array
    {
        if (\is_null($this->report) || \is_null($this->report->columns)) {
            return null;
        }
        return \json_decode($this->report->columns, \true);
    }
    public function visible_datasets() : array
    {
        if (\is_null($this->report) || \is_null($this->report->visible_datasets)) {
            return ['visitors', 'views'];
        }
        $visible_datasets = \json_decode($this->report->visible_datasets, \true);
        return \is_null($visible_datasets) ? ['visitors', 'views'] : $visible_datasets;
    }
    public function filters() : array
    {
        if (\is_null($this->report) || \is_null($this->report->filters)) {
            return [];
        }
        $table_class = Table::get_table_by_type($this->report->type);
        $table = new $table_class(null, $this->report->group_name ?? null);
        $filters = \json_decode($this->report->filters, \true);
        return \is_null($filters) ? [] : $table->sanitize_filters($filters);
    }
    public function sort_column() : string
    {
        return $this->report->sort_column ?? 'visitors';
    }
    public function sort_direction() : string
    {
        return $this->report->sort_direction ?? 'desc';
    }
    public function group() : ?string
    {
        return $this->report->group_name ?? null;
    }
    public function chart_interval() : ?Interval
    {
        if (\is_null($this->report->chart_interval ?? null)) {
            return Intervals::default_for($this->get_date_range()->number_of_days());
        }
        return Intervals::find_by_id($this->report->chart_interval);
    }
    /**
     * @return Date_Range
     */
    public function get_date_range() : Date_Range
    {
        if ($this->has_exact_range()) {
            try {
                $start = new DateTime($this->start(), Timezone::site_timezone());
                $end = new DateTime($this->end(), Timezone::site_timezone());
                return new Exact_Date_Range($start, $end);
            } catch (Throwable $e) {
                // Do nothing and fall back to default relative date range
            }
        }
        return new Relative_Date_Range($this->relative_range_id());
    }
    public function start() : ?string
    {
        if (!$this->has_exact_range()) {
            return null;
        }
        return $this->report->exact_start;
    }
    public function end() : ?string
    {
        if (!$this->has_exact_range()) {
            return null;
        }
        return $this->report->exact_end;
    }
    /**
     * Prefer exact range to relative range if both are provided
     */
    public function relative_range_id() : ?string
    {
        $relative_range_id = $this->report->relative_range_id ?? null;
        if (!$this->has_exact_range() && $relative_range_id === null) {
            return 'LAST_THIRTY';
        } elseif ($this->has_exact_range()) {
            return null;
        } elseif (Relative_Date_Range::is_valid_range($relative_range_id) === \false) {
            return 'LAST_THIRTY';
        }
        return $relative_range_id;
    }
    public function maybe_redirect() : void
    {
        if (\IAWP\Env::get_page() !== 'independent-analytics') {
            return;
        }
        if (empty($_GET['report']) && empty($_GET['tab'])) {
            $favorite_report = \IAWP\Report_Finder::get_favorite();
            if (\is_null($favorite_report)) {
                return;
            }
            \wp_safe_redirect($favorite_report->url());
            exit;
        }
        if (!\is_null($this->report_id) && \is_null($this->report)) {
            \wp_safe_redirect(\IAWPSCOPED\iawp_dashboard_url(['tab' => \IAWP\Env::get_tab()]));
            exit;
        }
        if (!\is_null($this->report) && \IAWP\Env::get_tab() !== $this->report->type) {
            \wp_safe_redirect(\IAWPSCOPED\iawp_dashboard_url(['tab' => $this->report->type, 'report' => $this->report->report_id]));
            exit;
        }
    }
    public function is_sidebar_collapsed() : bool
    {
        $is_sidebar_collapsed = \get_user_meta(\get_current_user_id(), 'iawp_is_sidebar_collapsed', \true) === '1';
        return $is_sidebar_collapsed;
    }
    private function fetch_report_by_id($report_id) : ?object
    {
        $reports_table = \IAWP\Query::get_table_name(\IAWP\Query::REPORTS);
        if (\is_null($report_id)) {
            return null;
        }
        return \IAWP\Illuminate_Builder::get_builder()->from($reports_table)->where('report_id', '=', $report_id)->first();
    }
    private function has_exact_range() : bool
    {
        return !\is_null($this->report->exact_start ?? null) && !\is_null($this->report->exact_end ?? null);
    }
}
