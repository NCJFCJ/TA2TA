<?php

namespace IAWP\Admin_Page;

use IAWP\Capability_Manager;
/** @internal */
class Settings_Page extends \IAWP\Admin_Page\Admin_Page
{
    protected function render_page()
    {
        if (Capability_Manager::can_edit()) {
            \IAWPSCOPED\iawp()->settings->render_settings();
        } else {
            echo '<p class="permission-blocked">' . \esc_html__('You do not have permission to edit the settings.', 'independent-analytics') . '</p>';
        }
    }
}
