<?php

namespace IAWP\AJAX;

use IAWP\Campaign_Builder;
/** @internal */
class Delete_Campaign extends \IAWP\AJAX\AJAX
{
    protected function action_name() : string
    {
        return 'iawp_delete_campaign';
    }
    protected function requires_pro() : bool
    {
        return \true;
    }
    protected function action_callback() : void
    {
        Campaign_Builder::delete_campaign($this->get_field('campaign_url_id'));
        \wp_send_json_success([]);
    }
}
