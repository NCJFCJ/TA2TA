<?php

namespace IAWP\AJAX;

use IAWP\Report_Finder;
use IAWP\Report_Options_Parser;
/** @internal */
class Import_Reports extends \IAWP\AJAX\AJAX
{
    /**
     * @inheritDoc
     */
    protected function action_required_fields() : array
    {
        return ['json'];
    }
    /**
     * @inheritDoc
     */
    protected function action_name() : string
    {
        return 'iawp_import_reports';
    }
    /**
     * @inheritDoc
     */
    protected function action_callback() : void
    {
        $report_archive = \json_decode(\stripslashes($_POST['json']), \true);
        foreach ($report_archive['reports'] as $report) {
            $report_options = new Report_Options_Parser($report);
            Report_Finder::create_report($report_options->get_options_for_creating());
        }
        \wp_send_json_success([]);
    }
}
