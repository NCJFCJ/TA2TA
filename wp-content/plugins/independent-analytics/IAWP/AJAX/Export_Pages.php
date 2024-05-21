<?php

namespace IAWP\AJAX;

use IAWP\Capability_Manager;
use IAWP\Date_Range\Exact_Date_Range;
use IAWP\Rows\Pages;
use IAWP\Tables\Table_Pages;
/** @internal */
class Export_Pages extends \IAWP\AJAX\AJAX
{
    protected function action_name() : string
    {
        return 'iawp_export_pages';
    }
    protected function action_callback() : void
    {
        if (!Capability_Manager::can_edit()) {
            return;
        }
        $resources = new Pages(Exact_Date_Range::comprehensive_range());
        $table = new Table_Pages();
        $csv = $table->csv($resources->rows());
        echo $csv->to_string();
    }
}
