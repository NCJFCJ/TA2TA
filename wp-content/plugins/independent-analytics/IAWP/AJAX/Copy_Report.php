<?php

namespace IAWP\AJAX;

use IAWP\Illuminate_Builder;
use IAWP\Query;
use IAWP\Report;
use IAWP\Report_Options_Parser;
/** @internal */
class Copy_Report extends \IAWP\AJAX\AJAX
{
    /**
     * @return array
     */
    protected function action_required_fields() : array
    {
        return ['name'];
    }
    /**
     * @return string
     */
    protected function action_name() : string
    {
        return 'iawp_copy_report';
    }
    /**
     * @return void
     */
    protected function action_callback() : void
    {
        $reports_table = Query::get_table_name(Query::REPORTS);
        $existing_report_options = $this->get_existing_report_options();
        if (\is_null($existing_report_options)) {
            \wp_send_json_error();
        }
        $new_report_id = Illuminate_Builder::get_builder()->from($reports_table)->insertGetId($existing_report_options);
        $report_options_parser = Report_Options_Parser::from_json($_POST['changes']);
        if (\count($report_options_parser->get_options_for_updating()) > 0) {
            Illuminate_Builder::get_builder()->from($reports_table)->where('report_id', '=', $new_report_id)->update($report_options_parser->get_options_for_updating());
        }
        $row = Illuminate_Builder::get_builder()->from($reports_table)->where('report_id', '=', $new_report_id)->first();
        $report = new Report($row);
        \wp_send_json_success(['url' => $report->url()]);
    }
    private function get_existing_report_options() : ?array
    {
        if (\strlen($this->get_field('id')) === 0) {
            return ['name' => $this->get_field('name'), 'type' => $this->get_field('type')];
        }
        $reports_table = Query::get_table_name(Query::REPORTS);
        $existing_report = Illuminate_Builder::get_builder()->from($reports_table)->where('report_id', '=', $this->get_field('id'))->first();
        if (\is_null($existing_report)) {
            return null;
        }
        $existing_report = (array) $existing_report;
        unset($existing_report['report_id']);
        $existing_report['name'] = $this->get_field('name');
        return $existing_report;
    }
}
