<?php

namespace IAWP\AJAX;

use IAWP\Capability_Manager;
/** @internal */
class Confirm_Cache_Cleared extends \IAWP\AJAX\AJAX
{
    protected function action_name() : string
    {
        return 'iawp_confirm_cache_cleared';
    }
    protected function action_callback() : void
    {
        if (!Capability_Manager::can_edit()) {
            return;
        }
        \update_option('iawp_need_clear_cache', \false);
    }
}
