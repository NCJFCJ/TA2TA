<?php

namespace IAWP\AJAX;

use IAWP\Campaign_Builder;
use IAWP\Utils\Security;
/** @internal */
class Create_Campaign extends \IAWP\AJAX\AJAX
{
    protected function action_name() : string
    {
        return 'iawp_create_campaign';
    }
    protected function requires_pro() : bool
    {
        return \true;
    }
    protected function action_callback() : void
    {
        $campaign_builder = new Campaign_Builder();
        $html = $campaign_builder->create_campaign(Security::string(\trim($this->get_field('path'))), Security::string(\trim($this->get_field('utm_source'))), Security::string(\trim($this->get_field('utm_medium'))), Security::string(\trim($this->get_field('utm_campaign'))), Security::string(\trim($this->get_field('utm_term'))), Security::string(\trim($this->get_field('utm_content'))));
        \wp_send_json_success(['html' => $html]);
    }
}
