<?php

namespace IAWP\AJAX;

use IAWP\Capability_Manager;
use IAWP\Date_Range\Exact_Date_Range;
use IAWP\Rows\Campaigns;
use IAWP\Tables\Table_Campaigns;
/** @internal */
class Export_Campaigns extends \IAWP\AJAX\AJAX
{
    protected function action_name() : string
    {
        return 'iawp_export_campaigns';
    }
    protected function requires_pro() : bool
    {
        return \true;
    }
    protected function action_callback() : void
    {
        if (!Capability_Manager::can_edit()) {
            return;
        }
        $campaigns = new Campaigns(Exact_Date_Range::comprehensive_range());
        $table = new Table_Campaigns();
        $csv = $table->csv($campaigns->rows());
        echo $csv->to_string();
    }
}
