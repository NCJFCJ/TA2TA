<?php

namespace IAWP\AJAX;

/** @internal */
class Update_User_Settings extends \IAWP\AJAX\AJAX
{
    protected function action_name() : string
    {
        return 'iawp_update_user_settings';
    }
    protected function action_callback() : void
    {
        $is_sidebar_collapsed = $this->get_boolean_field('is_sidebar_collapsed');
        if (\is_bool($is_sidebar_collapsed)) {
            \update_user_meta(\get_current_user_id(), 'iawp_is_sidebar_collapsed', $is_sidebar_collapsed);
        }
        \wp_send_json_success([]);
    }
}
