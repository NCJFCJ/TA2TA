<?php

namespace IAWP\AJAX;

use IAWP\Capability_Manager;
use IAWP\Illuminate_Builder;
use IAWP\Query;
/** @internal */
class Sort_Reports extends \IAWP\AJAX\AJAX
{
    /**
     * @return array
     */
    protected function action_required_fields() : array
    {
        return ['ids', 'type'];
    }
    /**
     * @return string
     */
    protected function action_name() : string
    {
        return 'iawp_sort_reports';
    }
    /**
     * @return void
     */
    protected function action_callback() : void
    {
        $reports_table = Query::get_table_name(Query::REPORTS);
        if (!Capability_Manager::can_edit()) {
            \wp_send_json_error([], 400);
        }
        Illuminate_Builder::get_builder()->from($reports_table)->where('type', '=', $this->get_field('type'))->update(['position' => null]);
        foreach ($this->get_field('ids') as $index => $id) {
            Illuminate_Builder::get_builder()->from($reports_table)->where('type', '=', $this->get_field('type'))->where('report_id', '=', $id)->update(['position' => $index]);
        }
        \wp_send_json_success();
    }
}
