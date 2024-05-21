<?php

namespace IAWP\AJAX;

use IAWP\Report_Finder;
/** @internal */
class Export_Reports extends \IAWP\AJAX\AJAX
{
    /**
     * @inheritDoc
     */
    protected function action_required_fields() : array
    {
        return ['ids'];
    }
    /**
     * @inheritDoc
     */
    protected function action_name() : string
    {
        return 'iawp_export_reports';
    }
    /**
     * @inheritDoc
     */
    protected function action_callback() : void
    {
        $ids = $this->get_field('ids');
        if (\count($ids) === 0) {
            \wp_send_json_error([], 400);
        }
        $report_finder = new Report_Finder();
        $reports = $report_finder->by_ids($ids);
        $reports_array = \array_map(function ($report) {
            return $report->to_array();
        }, $reports);
        \wp_send_json_success(['json' => \json_encode(['plugin_version' => '2.3.2', 'database_version' => '27', 'export_version' => '1', 'reports' => $reports_array])]);
    }
}
