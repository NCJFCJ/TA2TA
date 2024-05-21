<?php

namespace IAWP\AJAX;

use IAWP\Illuminate_Builder;
use IAWP\Query;
/** @internal */
class Rename_Report extends \IAWP\AJAX\AJAX
{
    /**
     * @return array
     */
    protected function action_required_fields() : array
    {
        return ['id', 'name'];
    }
    /**
     * @return string
     */
    protected function action_name() : string
    {
        return 'iawp_rename_report';
    }
    /**
     * @return void
     */
    protected function action_callback() : void
    {
        $reports_table = Query::get_table_name(Query::REPORTS);
        $name = \trim($this->get_field('name'));
        if (\strlen($name) === 0) {
            \wp_send_json_error([], 400);
        }
        Illuminate_Builder::get_builder()->from($reports_table)->where('report_id', '=', $this->get_field('id'))->update(['name' => $name]);
        \wp_send_json_success(['name' => $name]);
    }
}
