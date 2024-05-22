<?php

namespace IAWP\AJAX;

use IAWP\Capability_Manager;
use IAWP\Utils\Security;
/** @internal */
class Preview_Email extends \IAWP\AJAX\AJAX
{
    protected function action_name() : string
    {
        return 'iawp_preview_email';
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
        $colors = Security::string(\trim($this->get_field('colors')));
        $email = \IAWPSCOPED\iawp()->email_reports->get_email_preview($colors);
        \wp_send_json_success(['html' => $email]);
    }
}
