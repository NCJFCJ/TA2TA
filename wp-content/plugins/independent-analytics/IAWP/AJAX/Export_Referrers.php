<?php

namespace IAWP\AJAX;

use IAWP\Capability_Manager;
use IAWP\Date_Range\Exact_Date_Range;
use IAWP\Rows\Referrers;
use IAWP\Tables\Table_Referrers;
/** @internal */
class Export_Referrers extends \IAWP\AJAX\AJAX
{
    protected function action_name() : string
    {
        return 'iawp_export_referrers';
    }
    protected function action_callback() : void
    {
        if (!Capability_Manager::can_edit()) {
            return;
        }
        $referrers = new Referrers(Exact_Date_Range::comprehensive_range());
        $table = new Table_Referrers();
        $csv = $table->csv($referrers->rows());
        echo $csv->to_string();
    }
}
