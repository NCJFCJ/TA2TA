<?php

namespace IAWP;

use IAWP\Date_Range\Relative_Date_Range;
use IAWP\Statistics\Page_Statistics;
/** @internal */
class Dashboard_Widget
{
    public function __construct()
    {
        if ($this->is_enabled()) {
            \add_action('wp_dashboard_setup', [$this, 'add_dashboard_widget']);
        }
    }
    public function add_dashboard_widget()
    {
        if (\IAWP\Migrations\Migrations::is_migrating() || !\IAWP\Capability_Manager::can_view()) {
            return;
        }
        \ob_start();
        ?>
        <span><?php 
        \esc_html_e('Analytics', 'independent-analytics');
        ?></span>
        <span>
            <a href="<?php 
        echo \esc_url(\IAWPSCOPED\iawp_dashboard_url());
        ?>" class="iawp-button purple">
                <?php 
        \esc_html_e('Open Dashboard', 'independent-analytics');
        ?>
            </a>
        </span>
        <?php 
        $title = \ob_get_contents();
        \ob_end_clean();
        \wp_add_dashboard_widget('iawp', $title, [$this, 'dashboard_widget']);
    }
    public function dashboard_widget()
    {
        $date_range = new Relative_Date_Range('LAST_THIRTY');
        $statistics = new Page_Statistics($date_range);
        $chart = new \IAWP\Chart($statistics, null, \true);
        $stats = new \IAWP\Quick_Stats(null, $statistics, \true);
        echo $chart->get_html();
        echo $stats->get_html();
    }
    private function is_enabled() : bool
    {
        return \get_option('iawp_disable_widget') !== '1';
    }
}
