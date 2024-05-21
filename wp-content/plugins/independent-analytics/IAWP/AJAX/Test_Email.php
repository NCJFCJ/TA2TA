<?php

namespace IAWP\AJAX;

use IAWP\Capability_Manager;
/** @internal */
class Test_Email extends \IAWP\AJAX\AJAX
{
    protected function action_name() : string
    {
        return 'iawp_test_email';
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
        $sent = \IAWPSCOPED\iawp()->email_reports->send_email_report(\true);
        echo \rest_sanitize_boolean($sent);
    }
}
