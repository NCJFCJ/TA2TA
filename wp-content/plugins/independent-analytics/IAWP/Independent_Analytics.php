<?php

namespace IAWP;

use IAWP\Admin_Page\Analytics_Page;
use IAWP\Admin_Page\Campaign_Builder_Page;
use IAWP\Admin_Page\Settings_Page;
use IAWP\Admin_Page\Support_Page;
use IAWP\Admin_Page\Updates_Page;
use IAWP\AJAX\AJAX_Manager;
use IAWP\Menu_Bar_Stats\Menu_Bar_Stats;
use IAWP\Migrations\Migrations;
use IAWP\Utils\Singleton;
/** @internal */
class Independent_Analytics
{
    use Singleton;
    public $settings;
    public $email_reports;
    public $cron_manager;
    // This is where we attach functions to WP hooks
    private function __construct()
    {
        $this->settings = new \IAWP\Settings();
        new \IAWP\REST_API();
        new \IAWP\Dashboard_Widget();
        new \IAWP\View_Counter();
        AJAX_Manager::getInstance();
        if (!Migrations::is_migrating()) {
            new \IAWP\Track_Resource_Changes();
            Menu_Bar_Stats::register();
            \IAWP\WooCommerce_Order::initialize_order_tracker();
        }
        $this->cron_manager = new \IAWP\Cron_Manager();
        if (\IAWPSCOPED\iawp_is_pro()) {
            $this->email_reports = new \IAWP\Email_Reports();
            new \IAWP\Campaign_Builder();
            new \IAWP\WooCommerce_Referrer_Meta_Box();
        }
        \add_filter('admin_body_class', function ($classes) {
            if (\get_option('iawp_dark_mode')) {
                $classes .= ' iawp-dark-mode ';
            }
            return $classes;
        });
        \add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts_and_styles'], 20);
        // Called at 20 to dequeue other scripts
        \add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts_and_styles_front_end']);
        \add_action('admin_menu', [$this, 'add_admin_menu_pages']);
        \add_filter('plugin_action_links_independent-analytics/iawp.php', [$this, 'plugin_action_links']);
        \add_filter('admin_footer_text', [$this, 'ip_db_attribution'], 1, 1);
        \add_action('init', [$this, 'polylang_translations']);
        \add_action('init', [$this, 'load_textdomain']);
        \IAWP_FS()->add_filter('connect_message_on_update', [$this, 'filter_connect_message_on_update'], 10, 6);
        \IAWP_FS()->add_filter('connect_message', [$this, 'filter_connect_message_on_update'], 10, 6);
        \IAWP_FS()->add_filter('pricing_url', [$this, 'change_freemius_pricing_url'], 10);
        \IAWP_FS()->add_filter('show_deactivation_feedback_form', function () {
            return \false;
        });
        \add_action('admin_init', [$this, 'maybe_delete_mu_plugin']);
    }
    /**
     * At one point in time, there was a must-use plugin that was created. The plugin file and the
     * option need to get cleaned up.
     * @return void
     */
    public function maybe_delete_mu_plugin()
    {
        $already_attempted = \get_option('iawp_attempted_to_delete_mu_plugin', '0');
        if ($already_attempted === '1') {
            return;
        }
        if (\get_option('iawp_must_use_directory_not_writable', '0') === '1') {
            \delete_option('iawp_must_use_directory_not_writable');
        }
        $mu_plugin_file = \trailingslashit(\WPMU_PLUGIN_DIR) . 'iawp-performance-boost.php';
        if (\file_exists($mu_plugin_file)) {
            \unlink($mu_plugin_file);
        }
        \update_option('iawp_attempted_to_delete_mu_plugin', '1');
    }
    public function load_textdomain()
    {
        \load_plugin_textdomain('independent-analytics', \false, \IAWP_LANGUAGES_DIRECTORY);
    }
    public function polylang_translations()
    {
        if (\function_exists('IAWPSCOPED\\pll_register_string')) {
            pll_register_string('view_counter', 'Views:', 'Independent Analytics');
        }
    }
    // Changes the URL for the "Upgrade" tab in the Account menu
    public function change_freemius_pricing_url()
    {
        return 'https://independentwp.com/pricing/?utm_source=User+Dashboard&utm_medium=WP+Admin&utm_campaign=Upgrade+to+Pro&utm_content=Account';
    }
    public function add_admin_menu_pages()
    {
        $title = \IAWP\Capability_Manager::white_labeled() ? \esc_html__('Analytics', 'independent-analytics') : 'Independent Analytics';
        \add_menu_page($title, \esc_html__('Analytics', 'independent-analytics'), \IAWP\Capability_Manager::can_view_string(), 'independent-analytics', function () {
            $analytics_page = new Analytics_Page();
            $analytics_page->render();
        }, 'dashicons-analytics', 3);
        if (\IAWP\Capability_Manager::can_edit()) {
            \add_submenu_page('independent-analytics', \esc_html__('Settings', 'independent-analytics'), \esc_html__('Settings', 'independent-analytics'), \IAWP\Capability_Manager::can_view_string(), 'independent-analytics-settings', function () {
                $settings_page = new Settings_Page();
                $settings_page->render(\false);
            });
        }
        if (\IAWPSCOPED\iawp_is_pro()) {
            \add_submenu_page('independent-analytics', \esc_html__('Campaign Builder', 'independent-analytics'), \esc_html__('Campaign Builder', 'independent-analytics'), \IAWP\Capability_Manager::can_view_string(), 'independent-analytics-campaign-builder', function () {
                $campaign_builder_page = new Campaign_Builder_Page();
                $campaign_builder_page->render(\false);
            });
        }
        if (!\IAWP\Capability_Manager::white_labeled()) {
            \add_submenu_page('independent-analytics', \esc_html__('Help & Support', 'independent-analytics'), \esc_html__('Help & Support', 'independent-analytics'), \IAWP\Capability_Manager::can_view_string(), 'independent-analytics-support-center', function () {
                $support_page = new Support_Page();
                $support_page->render(\false);
            });
        }
        if (!\IAWP\Capability_Manager::white_labeled()) {
            $count = $this->get_update_notification_count();
            $menu_html = '<span class="menu-name">' . \esc_html__('Changelog', 'independent-analytics') . '</span>';
            $menu_html = $count > 0 ? $menu_html . ' <span class="menu-counter">' . \absint($count) . '</span>' : $menu_html;
            \add_submenu_page('independent-analytics', \esc_html__('Changelog', 'independent-analytics'), $menu_html, \IAWP\Capability_Manager::can_view_string(), 'independent-analytics-updates', function () {
                $updates_page = new Updates_Page();
                $updates_page->render(\false);
            });
        }
        if (\IAWPSCOPED\iawp_is_free() && !\IAWP\Capability_Manager::white_labeled()) {
            \add_submenu_page('independent-analytics', \esc_html__('Upgrade to Pro &rarr;', 'independent-analytics'), '<span style="color: #F69D0A;">' . \esc_html__('Upgrade to Pro &rarr;', 'independent-analytics') . '</span>', \IAWP\Capability_Manager::can_view_string(), \esc_url('https://independentwp.com/pricing/?utm_source=User+Dashboard&utm_medium=WP+Admin&utm_campaign=Upgrade+to+Pro&utm_content=Sidebar'));
        }
    }
    public function register_scripts_and_styles() : void
    {
        \wp_register_style('iawp-styles', \IAWPSCOPED\iawp_url_to('dist/styles/style.css'), [], \IAWP_VERSION);
        \wp_register_style('iawp-dashboard-widget-styles', \IAWPSCOPED\iawp_url_to('dist/styles/dashboard_widget.css'), [], \IAWP_VERSION);
        \wp_register_style('iawp-freemius-notice-styles', \IAWPSCOPED\iawp_url_to('dist/styles/freemius_notice_styles.css'), [], \IAWP_VERSION);
        \wp_register_script('iawp-javascript', \IAWPSCOPED\iawp_url_to('dist/js/index.js'), [], \IAWP_VERSION);
        \wp_register_script('iawp-dashboard-widget-javascript', \IAWPSCOPED\iawp_url_to('dist/js/dashboard_widget.js'), [], \IAWP_VERSION);
        \wp_register_script('iawp-layout-javascript', \IAWPSCOPED\iawp_url_to('dist/js/layout.js'), [], \IAWP_VERSION);
        \wp_register_script('iawp-settings-javascript', \IAWPSCOPED\iawp_url_to('dist/js/settings.js'), ['wp-color-picker'], \IAWP_VERSION);
        if (Menu_Bar_Stats::is_option_enabled()) {
            \wp_register_style('iawp-front-end-styles', \IAWPSCOPED\iawp_url_to('dist/styles/menu_bar_stats.css'), [], \IAWP_VERSION);
        }
        if (\is_rtl()) {
            \wp_register_style('iawp-styles-rtl', \IAWPSCOPED\iawp_url_to('dist/styles/rtl.css'), [], \IAWP_VERSION);
        }
    }
    public function register_scripts_and_styles_front_end() : void
    {
        if (Menu_Bar_Stats::is_option_enabled()) {
            \wp_register_style('iawp-front-end-styles', \IAWPSCOPED\iawp_url_to('dist/styles/menu_bar_stats.css'), [], \IAWP_VERSION);
        }
    }
    public function enqueue_scripts_and_styles($hook)
    {
        $this->register_scripts_and_styles();
        $page = \IAWP\Env::get_page();
        $this->enqueue_translations();
        $this->enqueue_nonces();
        \wp_enqueue_style('iawp-freemius-notice-styles');
        if (\is_string($page)) {
            \wp_enqueue_style('iawp-styles');
            \wp_enqueue_script('iawp-javascript');
            \wp_enqueue_script('iawp-layout-javascript');
            $this->dequeue_bad_actors();
            if (\is_rtl()) {
                \wp_enqueue_style('iawp-styles-rtl');
            }
        }
        if ($page === 'independent-analytics-settings') {
            \wp_enqueue_style('wp-color-picker');
            \wp_enqueue_script('iawp-settings-javascript');
        } elseif ($hook === 'index.php') {
            \wp_enqueue_script('iawp-dashboard-widget-javascript');
            \wp_enqueue_style('iawp-dashboard-widget-styles');
        }
        if (Menu_Bar_Stats::is_option_enabled()) {
            \wp_enqueue_style('iawp-front-end-styles');
        }
    }
    public function enqueue_scripts_and_styles_front_end()
    {
        if (Menu_Bar_Stats::is_option_enabled()) {
            $this->register_scripts_and_styles_front_end();
            \wp_enqueue_style('iawp-front-end-styles');
        }
    }
    public function enqueue_translations()
    {
        \wp_register_script('iawp-translations', '');
        \wp_enqueue_script('iawp-translations');
        \wp_add_inline_script('iawp-translations', 'const iawpText = ' . \json_encode(['visitors' => \__('Visitors', 'independent-analytics'), 'views' => \__('Views', 'independent-analytics'), 'sessions' => \__('Sessions', 'independent-analytics'), 'orders' => \__('Orders', 'independent-analytics'), 'netSales' => \__('Net Sales', 'independent-analytics'), 'country' => \__('country', 'independent-analytics'), 'exactDates' => \__('Apply Exact Dates', 'independent-analytics'), 'relativeDates' => \__('Apply Relative Dates', 'independent-analytics'), 'copied' => \__('Copied', 'independent-analytics'), 'exportingPages' => \__('Exporting Pages...', 'independent-analytics'), 'exportPages' => \__('Export Pages', 'independent-analytics'), 'exportingReferrers' => \__('Exporting Referrers...', 'independent-analytics'), 'exportReferrers' => \__('Export Referrers', 'independent-analytics'), 'exportingGeolocations' => \__('Exporting Geolocations...', 'independent-analytics'), 'exportGeolocations' => \__('Export Geolocations', 'independent-analytics'), 'exportingDevices' => \__('Exporting Devices...', 'independent-analytics'), 'exportDevices' => \__('Export Devices', 'independent-analytics'), 'exportingCampaigns' => \__('Exporting Campaigns...', 'independent-analytics'), 'exportCampaigns' => \__('Export Campaigns', 'independent-analytics'), 'invalidReportArchive' => \__('This report archive is invalid. Please export your reports and try again.', 'independent-analytics'), 'openMobileMenu' => \__('Open menu', 'independent-analytics'), 'closeMobileMenu' => \__('Close menu', 'independent-analytics')]), 'before');
    }
    public function enqueue_nonces()
    {
        \wp_register_script('iawp-nonces', '');
        \wp_enqueue_script('iawp-nonces');
        \wp_add_inline_script('iawp-nonces', 'const iawpActions = ' . \json_encode(AJAX_Manager::getInstance()->get_action_signatures()), 'before');
    }
    public function get_option($name, $default)
    {
        $option = \get_option($name, $default);
        return $option === '' ? $default : $option;
    }
    public function filter_connect_message_on_update($message, $user_first_name, $product_title, $user_login, $site_link, $freemius_link)
    {
        // Add the heading HTML.
        $plugin_name = 'Independent Analytics';
        $title = '<h3>' . \sprintf(\esc_html__('We hope you love %1$s', 'independent-analytics'), $plugin_name) . '</h3>';
        $html = '';
        // Add the introduction HTML.
        $html .= '<p>';
        $html .= \sprintf(\esc_html__('Hi, %1$s! This is an invitation to help the %2$s community.', 'independent-analytics'), $user_first_name, $plugin_name);
        $html .= '<strong>';
        $html .= \sprintf(\esc_html__('If you opt-in, some data about your usage of %2$s will be shared with us', 'independent-analytics'), $user_first_name, $plugin_name);
        $html .= '</strong>';
        $html .= \sprintf(\esc_html__(' so we can improve %2$s. We will also share some helpful info on using the plugin so you can get the most out of your sites analytics.', 'independent-analytics'), $user_first_name, $plugin_name);
        $html .= '</p>';
        $html .= '<p>';
        $html .= \sprintf(\esc_html__('And if you skip this, that\'s okay! %1$s will still work just fine.', 'independent-analytics'), $plugin_name);
        $html .= '</p>';
        return $title . $html;
    }
    public function plugin_action_links($links)
    {
        // Create the link
        $settings_link = '<a class="calendar-link" href="' . \esc_url(\IAWPSCOPED\iawp_dashboard_url()) . '">' . \esc_html__('Analytics Dashboard', 'independent-analytics') . '</a>';
        // Add the link to the start of the array
        \array_unshift($links, $settings_link);
        return $links;
    }
    public function ip_db_attribution($text)
    {
        if (\IAWP\Env::get_tab() === 'geo') {
            $text = $text . ' ' . \esc_html_x('Geolocation data powered by', 'Following text is a noun: DB-IP', 'independent-analytics') . ' ' . '<a href="https://db-ip.com" class="geo-message" target="_blank">DB-IP</a>.';
        }
        return $text;
    }
    public function pagination_page_size()
    {
        return 50;
    }
    public function dequeue_bad_actors()
    {
        // https://wordpress.org/plugins/comment-link-remove/
        \wp_dequeue_style('qc_clr_admin_style_css');
        // https://wordpress.org/plugins/webappick-pdf-invoice-for-woocommerce/
        \wp_dequeue_style('woo-invoice');
        // https://wordpress.org/plugins/wp-media-files-name-rename/
        \wp_dequeue_style('wpcmp_bootstrap_css');
    }
    public function get_update_notification_count()
    {
        // https://api.wordpress.org/plugins/info/1.0/independent-analytics.json
        $version_history = ['2.3.0', '2.2.0', '2.1.0'];
        $last_update_viewed = $this->get_option('iawp_last_update_viewed', '0');
        $notification_html = '';
        $unseen_versions = \array_filter($version_history, function ($version) use($last_update_viewed) {
            return \version_compare($last_update_viewed, $version, '<');
        });
        return \count($unseen_versions);
    }
}
