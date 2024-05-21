<?php

namespace IAWP\AJAX;

use DateTime;
use IAWP\Chart;
use IAWP\Chart_Geo;
use IAWP\Date_Range\Date_Range;
use IAWP\Date_Range\Exact_Date_Range;
use IAWP\Date_Range\Relative_Date_Range;
use IAWP\Quick_Stats;
use IAWP\Statistics\Intervals\Intervals;
use IAWP\Tables\Table;
use IAWPSCOPED\Proper\Timezone;
use Throwable;
/** @internal */
class Filter extends \IAWP\AJAX\AJAX
{
    protected function action_name() : string
    {
        return 'iawp_filter';
    }
    protected function action_required_fields() : array
    {
        return ['table_type', 'columns'];
    }
    protected function action_callback() : void
    {
        $date_range = $this->get_date_range();
        $is_new_date_range = $this->get_field('is_new_date_range') === 'true';
        $filters = $this->get_field('filters') ?? [];
        $visible_columns = $this->get_field('columns');
        $visible_datasets = $this->get_field('visible_datasets') ?? [];
        $sort_column = $this->get_field('sort_column') ?? null;
        $sort_direction = $this->get_field('sort_direction') ?? null;
        $group = $this->get_field('group') ?? null;
        $as_csv = $this->get_field('as_csv') ?? \false;
        $is_new_group = $this->get_field('is_new_group') === 'true';
        $chart_interval = $is_new_date_range ? Intervals::default_for($date_range->number_of_days()) : Intervals::find_by_id($this->get_field('chart_interval'));
        $page = \intval($this->get_field('page') ?? 1);
        $number_of_rows = $page * \IAWPSCOPED\iawp()->pagination_page_size();
        $table_type = $this->get_field('table_type');
        $is_geo_table = $table_type === 'geo';
        $table_class = Table::get_table_by_type($this->get_field('table_type'));
        if (\is_null($table_class)) {
            return;
        }
        $table = new $table_class($visible_columns, $group, $is_new_group);
        $filters = $table->sanitize_filters($filters);
        $sort_configuration = $table->sanitize_sort_parameters($sort_column, $sort_direction);
        $rows_class = $table->group()->rows_class();
        $statistics_class = $table->group()->statistics_class();
        if ($as_csv) {
            $rows_query = new $rows_class($date_range, null, $filters, $sort_configuration);
            $rows = $rows_query->rows();
            $csv = $table->csv($rows, \true);
            echo $csv->to_string();
            return;
        }
        if ($is_geo_table) {
            $rows_query = new $rows_class($date_range, null, $filters, $sort_configuration);
        } else {
            $rows_query = new $rows_class($date_range, $number_of_rows, $filters, $sort_configuration);
        }
        $rows = $rows_query->rows();
        if (empty($filters)) {
            $filtered_statistics = null;
            $unfiltered_statistics = new $statistics_class($date_range, null, $chart_interval);
            $total_number_of_rows = $unfiltered_statistics->total_number_of_rows();
        } else {
            $unfiltered_statistics = new $statistics_class($date_range, null, $chart_interval);
            $filtered_statistics = new $statistics_class($date_range, $rows_query, $chart_interval);
            $total_number_of_rows = $filtered_statistics->total_number_of_rows();
        }
        if ($is_geo_table) {
            $chart = new Chart_Geo($rows, $date_range->label());
            $rows = \array_slice($rows, 0, $number_of_rows);
        } else {
            $chart = new Chart($filtered_statistics ?? $unfiltered_statistics, $date_range->label());
        }
        $table->set_statistics($filtered_statistics ?? $unfiltered_statistics);
        $quick_stats = new Quick_Stats($filtered_statistics, $unfiltered_statistics);
        \wp_send_json_success(['rows' => $table->get_rendered_template($rows, \true, $sort_configuration->column(), $sort_configuration->direction()), 'table' => $table->get_rendered_template($rows, \false, $sort_configuration->column(), $sort_configuration->direction()), 'totalNumberOfRows' => $total_number_of_rows, 'chart' => $chart->get_html($visible_datasets), 'stats' => $quick_stats->get_html(), 'label' => $date_range->label(), 'isLastPage' => \count($rows) < \IAWPSCOPED\iawp()->pagination_page_size() * $page, 'columns' => $table->visible_column_ids(), 'columnsHTML' => $table->column_picker_html(), 'groupId' => $table->group()->id(), 'filters' => $filters, 'filtersTemplateHTML' => $table->filters_template_html(), 'filtersButtonsHTML' => $table->filters_condition_buttons_html($filters), 'chartInterval' => $chart_interval->id()]);
    }
    /**
     * Get the date range for the filter request
     *
     * The date info can be supplied in one of two ways.
     *
     * The first is to provide a relative_range_id which is converted into start, end, and label.
     *
     * The second is to provide explicit start and end fields which will be used as is.
     *
     * @return Date_Range
     */
    private function get_date_range() : Date_Range
    {
        $relative_range_id = $this->get_field('relative_range_id');
        $start_timestamp = $this->get_field('exact_start');
        $end_timestamp = $this->get_field('exact_end');
        if (!\is_null($start_timestamp) && !\is_null($end_timestamp)) {
            try {
                $start = new DateTime($start_timestamp, Timezone::site_timezone());
                $end = new DateTime($end_timestamp, Timezone::site_timezone());
                return new Exact_Date_Range($start, $end);
            } catch (Throwable $e) {
                // Do nothing and fall back to default relative date range
            }
        }
        return new Relative_Date_Range($relative_range_id);
    }
}
