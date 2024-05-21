<?php

namespace IAWP;

/** @internal */
class Env
{
    public function is_free() : bool
    {
        return \IAWPSCOPED\iawp_is_free();
    }
    public function is_pro() : bool
    {
        return \IAWPSCOPED\iawp_is_pro();
    }
    public function is_white_labeled() : bool
    {
        return \IAWP\Capability_Manager::white_labeled();
    }
    public function can_write() : bool
    {
        return \IAWP\Capability_Manager::can_edit();
    }
    public static function get_page() : ?string
    {
        if (!\is_admin()) {
            return null;
        }
        $page = $_GET['page'] ?? null;
        $valid_pages = ['independent-analytics', 'independent-analytics-settings', 'independent-analytics-campaign-builder', 'independent-analytics-support-center', 'independent-analytics-updates'];
        if (\in_array($page, $valid_pages)) {
            return $page;
        }
        return null;
    }
    public static function get_tab() : ?string
    {
        if (self::get_page() !== 'independent-analytics') {
            return null;
        }
        if (\IAWPSCOPED\iawp_is_pro()) {
            $valid_tabs = ['views', 'referrers', 'geo', 'devices', 'campaigns', 'real-time'];
        } else {
            $valid_tabs = ['views', 'referrers', 'geo', 'devices'];
        }
        $default_tab = $valid_tabs[0];
        $tab = \array_key_exists('tab', $_GET) ? \sanitize_text_field($_GET['tab']) : \false;
        $is_valid = \array_search($tab, $valid_tabs) != \false;
        if (!$tab || !$is_valid) {
            $tab = $default_tab;
        }
        return $tab;
    }
}
