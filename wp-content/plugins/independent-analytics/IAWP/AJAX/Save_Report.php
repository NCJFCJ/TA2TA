<?php

namespace IAWP\AJAX;

use IAWP\Illuminate_Builder;
use IAWP\Query;
use IAWP\Report_Options_Parser;
/** @internal */
class Save_Report extends \IAWP\AJAX\AJAX
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
        return 'iawp_save_report';
    }
    /**
     * @return void
     */
    protected function action_callback() : void
    {
        $reports_table = Query::get_table_name(Query::REPORTS);
        $report_options_parser = Report_Options_Parser::from_json($_POST['changes']);
        if (\count($report_options_parser->get_options_for_updating()) > 0) {
            Illuminate_Builder::get_builder()->from($reports_table)->where('report_id', '=', $this->get_field('id'))->update($report_options_parser->get_options_for_updating());
        }
    }
}
