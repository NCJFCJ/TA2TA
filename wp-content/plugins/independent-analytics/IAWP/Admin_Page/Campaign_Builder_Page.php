<?php

namespace IAWP\Admin_Page;

use IAWP\Campaign_Builder;
/** @internal */
class Campaign_Builder_Page extends \IAWP\Admin_Page\Admin_Page
{
    protected function render_page()
    {
        (new Campaign_Builder())->render_campaign_builder();
    }
}
