<?php

namespace IAWP;

use IAWP\Utils\Request;
use IAWP\Utils\String_Util;
/** @internal */
class Settings
{
    public function __construct()
    {
        \add_action('admin_init', [$this, 'register_settings']);
        \add_action('admin_init', [$this, 'register_view_counter_settings']);
        \add_action('admin_init', [$this, 'register_blocked_ip_settings']);
        \add_action('admin_init', [$this, 'register_block_by_role_settings']);
        if (\IAWPSCOPED\iawp_is_pro()) {
            \add_action('admin_init', [$this, 'register_email_report_settings']);
        }
    }
    public function render_settings()
    {
        echo \IAWPSCOPED\iawp_blade()->run('settings.index');
        if (\IAWPSCOPED\iawp_is_pro()) {
            $defaults = $this->email_report_colors();
            $saved = \IAWPSCOPED\iawp()->get_option('iawp_email_report_colors', $defaults);
            // There were 6 colors and now 9, so there is a saved value but 7-9 don't exist
            $input_defaults = $defaults;
            for ($i = 0; $i < \count($saved); $i++) {
                $input_defaults[$i] = $saved[$i];
            }
            echo \IAWPSCOPED\iawp_blade()->run('settings.email-reports', ['time' => \IAWPSCOPED\iawp()->get_option('iawp_email_report_time', 9), 'emails' => \IAWPSCOPED\iawp()->get_option('iawp_email_report_email_addresses', []), 'default_colors' => $defaults, 'input_default' => $input_defaults]);
        }
        $ip = Request::ip();
        $ips = \IAWPSCOPED\iawp()->get_option('iawp_blocked_ips', []);
        $ip_is_blocked = \in_array($ip, $ips);
        echo \IAWPSCOPED\iawp_blade()->run('settings.block-ips', ['current_ip' => $ip, 'ip_is_blocked' => $ip_is_blocked, 'ips' => $ips]);
        echo \IAWPSCOPED\iawp_blade()->run('settings.block-by-role', ['roles' => \wp_roles()->roles, 'blocked' => \IAWPSCOPED\iawp()->get_option('iawp_blocked_roles', [])]);
        echo \IAWPSCOPED\iawp_blade()->run('settings.capabilities', ['editable_roles' => $this->get_editable_roles(), 'capabilities' => \IAWP\Capability_Manager::all_capabilities()]);
        echo \IAWPSCOPED\iawp_blade()->run('settings.view-counter');
        echo \IAWPSCOPED\iawp_blade()->run('settings.export-reports', ['report_finder' => new \IAWP\Report_Finder()]);
        echo \IAWPSCOPED\iawp_blade()->run('settings.export-data');
        echo \IAWPSCOPED\iawp_blade()->run('settings.delete', ['site_name' => \get_bloginfo('name'), 'site_url' => \site_url()]);
    }
    public function register_settings()
    {
        \add_settings_section('iawp-settings-section', \esc_html__('Basic Settings', 'independent-analytics'), function () {
        }, 'independent-analytics-settings');
        $args = ['type' => 'boolean', 'default' => \false, 'sanitize_callback' => 'rest_sanitize_boolean'];
        \register_setting('iawp_settings', 'iawp_dark_mode', $args);
        \add_settings_field('iawp_dark_mode', \esc_html__('Dark mode', 'independent-analytics'), [$this, 'dark_mode_callback'], 'independent-analytics-settings', 'iawp-settings-section', ['class' => 'dark-mode']);
        \register_setting('iawp_settings', 'iawp_track_authenticated_users', $args);
        \add_settings_field('iawp_track_authenticated_users', \esc_html__('Track logged in users', 'independent-analytics'), [$this, 'track_authenticated_users_callback'], 'independent-analytics-settings', 'iawp-settings-section', ['class' => 'logged-in']);
        \register_setting('iawp_settings', 'iawp_disable_admin_toolbar_analytics', $args);
        \add_settings_field('iawp_disable_admin_toolbar_analytics', \esc_html__('Admin toolbar stats', 'independent-analytics'), [$this, 'disable_admin_toolbar_analytics_callback'], 'independent-analytics-settings', 'iawp-settings-section');
        \register_setting('iawp_settings', 'iawp_disable_widget', $args);
        \add_settings_field('iawp_disable_widget', \esc_html__('Dashboard widget', 'independent-analytics'), [$this, 'disable_widget_callback'], 'independent-analytics-settings', 'iawp-settings-section');
        $args = ['type' => 'integer', 'default' => 0, 'sanitize_callback' => 'absint'];
        \register_setting('iawp_settings', 'iawp_dow', $args);
        \add_settings_field('iawp_dow', \esc_html__('First day of week', 'independent-analytics'), [$this, 'starting_dow_callback'], 'independent-analytics-settings', 'iawp-settings-section', ['class' => 'dow']);
        $args = ['type' => 'boolean', 'default' => \false, 'sanitize_callback' => 'rest_sanitize_boolean'];
        \register_setting('iawp_settings', 'iawp_refresh_salt', $args);
        \add_settings_field('iawp_refresh_salt', \esc_html__('Salt refresh rate', 'independent-analytics'), [$this, 'refresh_salt_callback'], 'independent-analytics-settings', 'iawp-settings-section', ['class' => 'salt']);
    }
    public function dark_mode_callback()
    {
        echo \IAWPSCOPED\iawp_blade()->run('settings.dark-mode', ['dark_mode' => \IAWPSCOPED\iawp()->get_option('iawp_dark_mode', \false)]);
    }
    public function track_authenticated_users_callback()
    {
        echo \IAWPSCOPED\iawp_blade()->run('settings.track-authenticated-users', ['track_authenticated_users' => \IAWPSCOPED\iawp()->get_option('iawp_track_authenticated_users', \false)]);
    }
    public function disable_admin_toolbar_analytics_callback()
    {
        echo \IAWPSCOPED\iawp_blade()->run('settings.disable-admin-toolbar-analytics', ['value' => \IAWPSCOPED\iawp()->get_option('iawp_disable_admin_toolbar_analytics', \false)]);
    }
    public function disable_widget_callback()
    {
        echo \IAWPSCOPED\iawp_blade()->run('settings.disable-widget', ['value' => \IAWPSCOPED\iawp()->get_option('iawp_disable_widget', \false)]);
    }
    public function starting_dow_callback()
    {
        echo \IAWPSCOPED\iawp_blade()->run('settings.first-day-of-week', ['day_of_week' => \IAWPSCOPED\iawp()->get_option('iawp_dow', 0), 'days' => [0 => \esc_html__('Sunday', 'independent-analytics'), 1 => \esc_html__('Monday', 'independent-analytics'), 2 => \esc_html__('Tuesday', 'independent-analytics'), 3 => \esc_html__('Wednesday', 'independent-analytics'), 4 => \esc_html__('Thursday', 'independent-analytics'), 5 => \esc_html__('Friday', 'independent-analytics'), 6 => \esc_html__('Saturday', 'independent-analytics')]]);
    }
    public function refresh_salt_callback()
    {
        echo \IAWPSCOPED\iawp_blade()->run('settings.refresh-salt', ['refresh_salt' => \IAWPSCOPED\iawp()->get_option('iawp_refresh_salt', \false)]);
    }
    public function register_view_counter_settings()
    {
        \add_settings_section('iawp-view-counter-settings-section', \esc_html__('Public View Counter', 'independent-analytics'), function () {
        }, 'independent-analytics-view-counter-settings');
        $args = ['type' => 'boolean', 'default' => \false, 'sanitize_callback' => 'rest_sanitize_boolean'];
        \register_setting('iawp_view_counter_settings', 'iawp_view_counter_enable', $args);
        \add_settings_field('iawp_view_counter_enable', \esc_html__('Enable the view counter', 'independent-analytics'), [$this, 'view_counter_enable_callback'], 'independent-analytics-view-counter-settings', 'iawp-view-counter-settings-section', ['class' => 'enable']);
        $args = ['type' => 'array', 'default' => [], 'sanitize_callback' => [$this, 'sanitize_view_counter_post_types']];
        \register_setting('iawp_view_counter_settings', 'iawp_view_counter_post_types', $args);
        \add_settings_field('iawp_view_counter_post_types', \esc_html__('Display on these post types', 'independent-analytics'), [$this, 'view_counter_post_types_callback'], 'independent-analytics-view-counter-settings', 'iawp-view-counter-settings-section', ['class' => 'post-types']);
        // Position
        $args = ['type' => 'string', 'default' => 'after', 'sanitize_callback' => [$this, 'sanitize_view_counter_position']];
        \register_setting('iawp_view_counter_settings', 'iawp_view_counter_position', $args);
        \add_settings_field('iawp_view_counter_position', \esc_html__('Show it in this location', 'independent-analytics'), [$this, 'view_counter_position_callback'], 'independent-analytics-view-counter-settings', 'iawp-view-counter-settings-section', ['class' => 'position']);
        // Views to count
        \register_setting('iawp_view_counter_settings', 'iawp_view_counter_views_to_count', ['type' => 'string', 'default' => 'total', 'sanitize_callback' => [$this, 'sanitize_view_counter_views_to_count']]);
        \add_settings_field('iawp_view_counter_views_to_count', \esc_html__('Date range to count views', 'independent-analytics'), [$this, 'view_counter_views_to_count_callback'], 'independent-analytics-view-counter-settings', 'iawp-view-counter-settings-section', ['class' => 'views-to-count']);
        // Exclude
        $args = ['type' => 'string', 'default' => '', 'sanitize_callback' => [$this, 'sanitize_view_counter_exclude']];
        \register_setting('iawp_view_counter_settings', 'iawp_view_counter_exclude', $args);
        \add_settings_field('iawp_view_counter_exclude', \esc_html__('Exclude these pages', 'independent-analytics'), [$this, 'view_counter_exclude_callback'], 'independent-analytics-view-counter-settings', 'iawp-view-counter-settings-section', ['class' => 'exclude']);
        // Label
        $default = \function_exists('IAWPSCOPED\\pll__') ? pll__('Views:', 'independent-analytics') : \__('Views:', 'independent-analytics');
        $args = ['type' => 'string', 'default' => $default, 'sanitize_callback' => 'sanitize_text_field'];
        \register_setting('iawp_view_counter_settings', 'iawp_view_counter_label', $args);
        \add_settings_field('iawp_view_counter_label', \esc_html__('Edit the label', 'independent-analytics'), [$this, 'view_counter_label_callback'], 'independent-analytics-view-counter-settings', 'iawp-view-counter-settings-section', ['class' => 'counter-label']);
        // Icon
        $args = ['type' => 'boolean', 'default' => \true, 'sanitize_callback' => 'rest_sanitize_boolean'];
        \register_setting('iawp_view_counter_settings', 'iawp_view_counter_icon', $args);
        \add_settings_field('iawp_view_counter_icon', \esc_html__('Display the icon', 'independent-analytics'), [$this, 'view_counter_icon_callback'], 'independent-analytics-view-counter-settings', 'iawp-view-counter-settings-section', ['class' => 'icon']);
        // Private
        \register_setting('iawp_view_counter_settings', 'iawp_view_counter_private', ['type' => 'boolean', 'default' => \false, 'sanitize_callback' => 'rest_sanitize_boolean']);
        \add_settings_field('iawp_view_counter_private', \esc_html__('Make the view counter private?', 'independent-analytics'), [$this, 'view_counter_private_callback'], 'independent-analytics-view-counter-settings', 'iawp-view-counter-settings-section', ['class' => 'private']);
        // Allow manual adjustment
        \register_setting('iawp_view_counter_settings', 'iawp_view_counter_manual_adjustment', ['type' => 'boolean', 'default' => \false, 'sanitize_callback' => 'rest_sanitize_boolean']);
        \add_settings_field('iawp_view_counter_manual_adjustment', \esc_html__('Allow manual adjustment?', 'independent-analytics'), [$this, 'view_counter_manual_adjustment_callback'], 'independent-analytics-view-counter-settings', 'iawp-view-counter-settings-section', ['class' => 'manual-adjustment']);
    }
    public function view_counter_enable_callback()
    {
        echo \IAWPSCOPED\iawp_blade()->run('settings.view-counter.enable', ['enable' => \IAWPSCOPED\iawp()->get_option('iawp_view_counter_enable', \false)]);
    }
    public function view_counter_post_types_callback()
    {
        $site_post_types = \get_post_types(['public' => \true], 'objects');
        $counter = 0;
        foreach ($site_post_types as $post_type) {
            echo \IAWPSCOPED\iawp_blade()->run('settings.view-counter.post-types', ['counter' => $counter, 'post_type' => $post_type, 'saved' => \IAWPSCOPED\iawp()->get_option('iawp_view_counter_post_types', [])]);
            $counter++;
        }
        ?>
        <p class="description"><?php 
        \esc_html_e('Uncheck all boxes to only show view count manually. See shortcode documentation below for details.', 'independent-analytics');
        ?></p>
        <?php 
    }
    public function view_counter_position_callback()
    {
        echo \IAWPSCOPED\iawp_blade()->run('settings.view-counter.position', ['position' => \IAWPSCOPED\iawp()->get_option('iawp_view_counter_position', 'after')]);
    }
    public function view_counter_views_to_count_callback()
    {
        echo \IAWPSCOPED\iawp_blade()->run('settings.view-counter.views-to-count', ['value' => \IAWPSCOPED\iawp()->get_option('iawp_view_counter_views_to_count', 'total')]);
    }
    public function view_counter_exclude_callback()
    {
        echo \IAWPSCOPED\iawp_blade()->run('settings.view-counter.exclude', ['exclude' => \IAWPSCOPED\iawp()->get_option('iawp_view_counter_exclude', '')]);
    }
    public function view_counter_label_callback()
    {
        $default = \function_exists('IAWPSCOPED\\pll__') ? pll__('Views:', 'independent-analytics') : \__('Views:', 'independent-analytics');
        echo \IAWPSCOPED\iawp_blade()->run('settings.view-counter.label', ['label' => \IAWPSCOPED\iawp()->get_option('iawp_view_counter_label', $default)]);
    }
    public function view_counter_icon_callback()
    {
        echo \IAWPSCOPED\iawp_blade()->run('settings.view-counter.icon', ['icon' => \get_option('iawp_view_counter_icon', \true)]);
    }
    public function view_counter_private_callback()
    {
        echo \IAWPSCOPED\iawp_blade()->run('settings.view-counter.private', ['private' => \IAWPSCOPED\iawp()->get_option('iawp_view_counter_private', \false)]);
    }
    public function view_counter_manual_adjustment_callback()
    {
        echo \IAWPSCOPED\iawp_blade()->run('settings.view-counter.manual-adjustment', ['value' => \IAWPSCOPED\iawp()->get_option('iawp_view_counter_manual_adjustment', \false)]);
    }
    public function register_blocked_ip_settings()
    {
        \add_settings_section('iawp-blocked-ips-settings-section', \esc_html__('Block IP Addresses', 'independent-analytics'), function () {
        }, 'iawp-blocked-ips-settings');
        $args = ['type' => 'array', 'default' => [], 'sanitize_callback' => [$this, 'sanitize_blocked_ips']];
        \register_setting('iawp_blocked_ip_settings', 'iawp_blocked_ips', $args);
    }
    public function register_email_report_settings()
    {
        \add_settings_section('iawp-email-report-settings-section', \esc_html__('Scheduled Email Reports', 'independent-analytics'), function () {
        }, 'iawp-email-report-settings');
        $args = ['type' => 'number', 'default' => 9, 'sanitize_callback' => [$this, 'sanitize_email_report_time']];
        \register_setting('iawp_email_report_settings', 'iawp_email_report_time', $args);
        $args = ['type' => 'array', 'default' => [], 'sanitize_callback' => [$this, 'sanitize_email_addresses']];
        \register_setting('iawp_email_report_settings', 'iawp_email_report_email_addresses', $args);
        $args = ['type' => 'array', 'default' => $this->email_report_colors(), 'sanitize_callback' => [$this, 'sanitize_email_report_colors']];
        \register_setting('iawp_email_report_settings', 'iawp_email_report_colors', $args);
    }
    public function register_block_by_role_settings()
    {
        \add_settings_section('iawp-block-by-role-settings-section', \esc_html__('Block by User Role', 'independent-analytics'), function () {
        }, 'iawp-block-by-role-settings');
        $args = ['type' => 'array', 'default' => [], 'sanitize_callback' => [$this, 'sanitize_blocked_roles']];
        \register_setting('iawp_block_by_role_settings', 'iawp_blocked_roles', $args);
    }
    public function sanitize_view_counter_post_types($user_input)
    {
        $site_post_types = \get_post_types(['public' => \true]);
        $to_save = [];
        foreach ($user_input as $post_type) {
            if (\in_array($post_type, $site_post_types)) {
                $to_save[] = $post_type;
            }
        }
        return $to_save;
    }
    public function sanitize_view_counter_position($user_input)
    {
        if (\in_array($user_input, ['before', 'after', 'both'])) {
            return $user_input;
        } else {
            return 'after';
        }
    }
    public function sanitize_view_counter_views_to_count($user_input)
    {
        if (\in_array($user_input, ['total', 'today', 'last_thirty', 'this_month', 'last_month'])) {
            return $user_input;
        } else {
            return 'total';
        }
    }
    public function sanitize_view_counter_exclude($user_input)
    {
        $user_input = \explode(',', $user_input);
        $to_save = [];
        foreach ($user_input as $id) {
            $save = \absint($id);
            if ($save != 0) {
                $to_save[] = $save;
            }
        }
        $to_save = \implode(',', $to_save);
        return $to_save;
    }
    public function sanitize_blocked_ips($user_input)
    {
        $to_save = [];
        foreach ($user_input as $ip) {
            // Limit to two wildcards
            if (\substr_count($ip, '*') > 2) {
                continue;
            }
            // If it's safe with wildcard replacements, it's safe with them
            $safe_replacement = String_Util::str_contains($ip, '.') ? '172' : '2601';
            $wildcards_replaced = \str_replace('*', $safe_replacement, $ip);
            if (\filter_var($wildcards_replaced, \FILTER_VALIDATE_IP)) {
                $to_save[] = $ip;
            }
        }
        return $to_save;
    }
    public function sanitize_email_addresses($emails)
    {
        $to_save = [];
        foreach ($emails as $email) {
            $cleaned = \sanitize_email($email);
            if (\is_email($cleaned)) {
                $to_save[] = $cleaned;
            }
        }
        return $to_save;
    }
    public function sanitize_email_report_time($user_time)
    {
        $accepted_times = [];
        for ($i = 0; $i < 24; $i++) {
            $accepted_times[] = $i;
        }
        if (\in_array($user_time, $accepted_times)) {
            return $user_time;
        } else {
            return 9;
        }
    }
    public function sanitize_blocked_roles($blocked_roles)
    {
        $to_save = [];
        $user_roles = \array_keys(\wp_roles()->roles);
        foreach ($blocked_roles as $blocked) {
            if (\in_array($blocked, $user_roles)) {
                $to_save[] = $blocked;
            }
        }
        return $to_save;
    }
    public function sanitize_email_report_colors($colors)
    {
        $to_save = [];
        // No idea why WP sometimes returns an array, so this is for my sanity
        if (\is_string($colors)) {
            $colors = \explode(',', $colors);
        }
        foreach ($colors as $color) {
            $to_save[] = \sanitize_hex_color($color);
        }
        return $to_save;
    }
    /**
     * @return array
     */
    private function get_editable_roles() : array
    {
        $editable_roles = [];
        $wp_roles = \wp_roles()->roles;
        \array_walk($wp_roles, function ($role, $role_key) use(&$editable_roles) {
            if ($role_key === 'administrator') {
                return;
            }
            $read_only_access = $role['capabilities']['iawp_read_only_access'] ?? \false;
            $full_access = $role['capabilities']['iawp_full_access'] ?? \false;
            $editable_roles[] = ['key' => $role_key, 'name' => $role['name'], 'iawp_no_access' => !$read_only_access && !$full_access, 'iawp_read_only_access' => $read_only_access, 'iawp_full_access' => $full_access];
        });
        return $editable_roles;
    }
    private function email_report_colors()
    {
        return ['#5123a0', '#fafafa', '#3a1e6b', '#fafafa', '#5123a0', '#a985e6', '#ece9f2', '#f7f5fa', '#ece9f2', '#dedae6'];
    }
}
