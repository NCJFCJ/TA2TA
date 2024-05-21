<?php

namespace IAWP\Admin_Page;

use IAWP\Capability_Manager;
/** @internal */
class Support_Page extends \IAWP\Admin_Page\Admin_Page
{
    protected function render_page()
    {
        if (!Capability_Manager::white_labeled()) {
            echo \IAWPSCOPED\iawp_blade()->run('support');
        } else {
            echo '<p class="permission-blocked">' . \esc_html__('You do not have permission to view this page.', 'independent-analytics') . '</p>';
        }
    }
}
