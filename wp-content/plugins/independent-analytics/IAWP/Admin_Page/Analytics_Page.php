<?php

namespace IAWP\Admin_Page;

use IAWP\Capability_Manager;
use IAWP\Chart;
use IAWP\Chart_Geo;
use IAWP\Dashboard_Options;
use IAWP\Database;
use IAWP\Env;
use IAWP\Plugin_Conflict_Detector;
use IAWP\Quick_Stats;
use IAWP\Real_Time;
use IAWP\Report_Finder;
use IAWP\Tables\Table;
use IAWP\Tables\Table_Campaigns;
use IAWP\Tables\Table_Devices;
use IAWP\Tables\Table_Geo;
use IAWP\Tables\Table_Pages;
use IAWP\Tables\Table_Referrers;
use IAWP\Utils\Security;
/** @internal */
class Analytics_Page extends \IAWP\Admin_Page\Admin_Page
{
    protected function render_page()
    {
        $options = new Dashboard_Options();
        $date_rage = $options->get_date_range();
        $date_label = $date_rage->label();
        $columns = $options->columns();
        $tab = (new Env())->get_tab();
        if ($tab === 'views') {
            $table = new Table_Pages($columns);
            $statistics_class = $table->group()->statistics_class();
            $statistics = new $statistics_class($date_rage, null, $options->chart_interval());
            $stats = new Quick_Stats(null, $statistics);
            $chart = new Chart($statistics, $date_label);
            $this->interface($table, $stats, $chart);
        } elseif ($tab === 'referrers') {
            $table = new Table_Referrers($columns);
            $statistics_class = $table->group()->statistics_class();
            $statistics = new $statistics_class($date_rage, null, $options->chart_interval());
            $stats = new Quick_Stats(null, $statistics);
            $chart = new Chart($statistics, $date_label);
            $this->interface($table, $stats, $chart);
        } elseif ($tab === 'geo') {
            $table = new Table_Geo($columns, $options->group());
            $statistics_class = $table->group()->statistics_class();
            $statistics = new $statistics_class($date_rage, null, $options->chart_interval());
            $stats = new Quick_Stats(null, $statistics);
            $table_data_class = $table->group()->rows_class();
            $geo_data = new $table_data_class($date_rage);
            $chart = new Chart_Geo($geo_data->rows(), $date_label);
            $this->interface($table, $stats, $chart);
        } elseif ($tab === 'campaigns') {
            $table = new Table_Campaigns($columns);
            $statistics_class = $table->group()->statistics_class();
            $statistics = new $statistics_class($date_rage, null, $options->chart_interval());
            $stats = new Quick_Stats(null, $statistics);
            $chart = new Chart($statistics, $date_label);
            $this->interface($table, $stats, $chart);
        } elseif ($tab === 'devices') {
            $table = new Table_Devices($columns, $options->group());
            $statistics_class = $table->group()->statistics_class();
            $statistics = new $statistics_class($date_rage, null, $options->chart_interval());
            $stats = new Quick_Stats(null, $statistics);
            $chart = new Chart($statistics, $date_label);
            $this->interface($table, $stats, $chart);
        } elseif ($tab === 'real-time') {
            (new Real_Time())->render_real_time_analytics();
        }
    }
    private function interface(Table $table, $stats, $chart)
    {
        $options = new Dashboard_Options();
        $sort_configuration = $table->sanitize_sort_parameters($options->sort_column(), $options->sort_direction());
        ?>
        <div data-controller="report"
             data-report-name-value="<?php 
        echo Security::string($options->report_name());
        ?>"
             data-report-relative-range-id-value="<?php 
        echo Security::attr($options->relative_range_id());
        ?>"
             data-report-exact-start-value="<?php 
        echo Security::attr($options->start());
        ?>"
             data-report-exact-end-value="<?php 
        echo Security::attr($options->end());
        ?>"
             data-report-group-value="<?php 
        echo Security::attr($options->group());
        ?>"
             data-report-filters-value="<?php 
        echo \esc_attr(Security::json_encode($options->filters()));
        ?>"
             data-report-chart-interval-value="<?php 
        echo Security::attr($options->chart_interval()->id());
        ?>"
             data-report-sort-column-value="<?php 
        echo Security::attr($options->sort_column());
        ?>"
             data-report-sort-direction-value="<?php 
        echo Security::attr($options->sort_direction());
        ?>"
             data-report-columns-value="<?php 
        echo \esc_attr(Security::json_encode($table->visible_column_ids()));
        ?>"
             data-report-visible-datasets-value="<?php 
        echo \esc_attr(Security::json_encode($options->visible_datasets()));
        ?>"
        >
            <div class="report-header-container">
                <?php 
        echo \IAWPSCOPED\iawp_blade()->run('partials.report-header', ['report' => (new Report_Finder())->current(), 'can_edit' => Capability_Manager::can_edit()]);
        ?>
                <?php 
        $table->output_toolbar();
        ?>
            </div>
            <?php 
        echo $stats->get_html();
        ?>
            <?php 
        echo $chart->get_html($options->visible_datasets());
        ?>
            <?php 
        echo $table->get_table_toolbar_markup();
        ?>
            <?php 
        echo $table->get_table_markup($sort_configuration->column(), $sort_configuration->direction());
        ?>
        </div>
        <div class="iawp-notices">
        <?php 
        $plugin_conflict_detector = new Plugin_Conflict_Detector();
        if (!$plugin_conflict_detector->has_conflict()) {
            echo \IAWPSCOPED\iawp_blade()->run('settings.notice', ['notice_text' => $plugin_conflict_detector->get_error(), 'button_text' => \false, 'notice' => 'iawp-error', 'url' => 'https://independentwp.com/knowledgebase/tracking/secure-rest-api/']);
        }
        if (\get_option('iawp_need_clear_cache')) {
            echo \IAWPSCOPED\iawp_blade()->run('settings.notice', ['notice_text' => \__('Please clear your cache to ensure tracking works properly.', 'independent-analytics'), 'button_text' => \__('I\'ve cleared the cache', 'independent-analytics'), 'notice' => 'iawp-warning', 'url' => 'https://independentwp.com/knowledgebase/common-questions/views-not-recording/']);
        }
        if (\IAWPSCOPED\iawp_db_version() > 0 && !Database::has_correct_database_privileges()) {
            echo \IAWPSCOPED\iawp_blade()->run('settings.notice', ['notice_text' => \__('Your site is missing the following critical database permissions:', 'independent-analytics') . ' ' . \implode(', ', Database::missing_database_privileges()) . '. ' . \__('There is no issue at this time, but you will need to enable the missing permissions before updating the plugin to a newer version to ensure an error is avoided. Please click this link to read our tutorial:', 'independent-analytics'), 'button_text' => \false, 'notice' => 'iawp-error', 'url' => 'https://independentwp.com/knowledgebase/common-questions/missing-database-permissions/']);
        }
        ?>
        </div><?php 
    }
}
