<?php

namespace IAWP\AJAX;

use IAWP\Illuminate_Builder;
use IAWP\Query;
use IAWP\Report;
use IAWP\Report_Finder;
/** @internal */
class Delete_Report extends \IAWP\AJAX\AJAX
{
    /**
     * @return array
     */
    protected function action_required_fields() : array
    {
        return ['id'];
    }
    /**
     * @return string
     */
    protected function action_name() : string
    {
        return 'iawp_delete_report';
    }
    /**
     * @return void
     */
    protected function action_callback() : void
    {
        $reports_table = Query::get_table_name(Query::REPORTS);
        $existing_report = Illuminate_Builder::get_builder()->from($reports_table)->where('report_id', '=', $this->get_field('id'))->first();
        if (\is_null($existing_report)) {
            \wp_send_json_error();
        }
        $report = new Report((object) ['type' => $existing_report->type]);
        $report_finder = new Report_Finder();
        $reports = $report_finder->by_type($existing_report->type);
        $report_index = 0;
        foreach ($reports as $index => $report) {
            if ($report->id() === $existing_report->report_id) {
                $report_index = $index;
            }
        }
        if (\array_key_exists($report_index + 1, $reports)) {
            $report = $reports[$report_index + 1];
        } elseif ($report_index > 0 && \array_key_exists($report_index - 1, $reports)) {
            $report = $reports[$report_index - 1];
        }
        Illuminate_Builder::get_builder()->from($reports_table)->where('report_id', '=', $this->get_field('id'))->delete();
        \wp_send_json_success(['url' => $report->url()]);
    }
}
