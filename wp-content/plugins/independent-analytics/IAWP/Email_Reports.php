<?php

namespace IAWP;

use IAWP\Date_Range\Exact_Date_Range;
use IAWP\Date_Range\Relative_Date_Range;
use IAWP\Rows\Campaigns;
use IAWP\Rows\Countries;
use IAWP\Rows\Pages;
use IAWP\Rows\Referrers;
use IAWP\Statistics\Page_Statistics;
use IAWP\Utils\URL;
/** @internal */
class Email_Reports
{
    public function __construct()
    {
        \add_filter('cron_schedules', [$this, 'add_monthly_schedule_cron']);
        $monitored_options = ['iawp_email_report_time', 'iawp_email_report_email_addresses'];
        foreach ($monitored_options as $option) {
            \add_action('update_option_' . $option, [$this, 'schedule_email_report'], 10, 0);
            \add_action('add_option_' . $option, [$this, 'schedule_email_report'], 10, 0);
        }
        \add_action('iawp_send_email_report', [$this, 'send_email_report']);
    }
    public function schedule_email_report()
    {
        $this->unschedule_email_report();
        if (empty(\IAWPSCOPED\iawp()->get_option('iawp_email_report_email_addresses', []))) {
            return;
        }
        $delivery_time = new \DateTime('first day of +1 month', new \DateTimeZone(\wp_timezone_string()));
        $delivery_time->setTime(\IAWPSCOPED\iawp()->get_option('iawp_email_report_time', 9), 0);
        \wp_schedule_event($delivery_time->getTimestamp(), 'monthly', 'iawp_send_email_report');
    }
    public function unschedule_email_report()
    {
        $timestamp = \wp_next_scheduled('iawp_send_email_report');
        \wp_unschedule_event($timestamp, 'iawp_send_email_report');
    }
    public function add_monthly_schedule_cron($schedules)
    {
        $schedules['monthly'] = ['interval' => \MONTH_IN_SECONDS, 'display' => \esc_html__('Once a Month', 'independent-analytics')];
        return $schedules;
    }
    public function send_email_report(bool $test = \false)
    {
        $to = \IAWPSCOPED\iawp()->get_option('iawp_email_report_email_addresses', []);
        if (empty($to)) {
            return;
        }
        $subject = \sprintf(\esc_html__('Analytics Report for %1$s [%2$s]', 'independent-analytics'), \get_bloginfo('name'), (new \DateTime('-1 month', new \DateTimeZone(\wp_timezone_string())))->format('F Y'));
        if ($test) {
            $subject = \esc_html__('[Test]', 'independent-analytics') . ' ' . $subject;
        }
        $body = $this->get_email_body();
        $headers[] = 'From: ' . \get_bloginfo('name') . ' <' . \get_bloginfo('admin_email') . '>';
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        return \wp_mail($to, $subject, $body, $headers);
    }
    private function get_email_body()
    {
        $statistics = new Page_Statistics(new Relative_Date_Range('LAST_MONTH'));
        $quick_stats = (new \IAWP\Quick_Stats(null, $statistics))->get_stats();
        $chart = new \IAWP\Email_Chart($statistics);
        return \IAWPSCOPED\iawp_blade()->run('email.email', ['site_title' => \get_bloginfo('name'), 'site_url' => (new URL(\get_site_url()))->get_domain(), 'date' => (new \DateTime('Last month', new \DateTimeZone(\wp_timezone_string())))->format('F Y'), 'stats' => $quick_stats, 'top_ten' => $this->get_top_ten(), 'chart_views' => $chart->daily_views, 'most_views' => $chart->most_views, 'y_labels' => $chart->y_labels, 'x_labels' => $chart->x_labels, 'colors' => \IAWPSCOPED\iawp()->get_option('iawp_email_report_colors', ['#5123a0', '#fafafa', '#3a1e6b', '#fafafa', '#5123a0', '#a985e6', '#ece9f2', '#f7f5fa', '#ece9f2', '#dedae6'])]);
    }
    private function get_top_ten() : array
    {
        $start = new \DateTime('First day of last month', new \DateTimeZone(\wp_timezone_string()));
        $end = new \DateTime('Last day of last month', new \DateTimeZone(\wp_timezone_string()));
        $date_range = new Exact_Date_Range($start, $end);
        $queries = ['pages' => 'title', 'referrers' => 'referrer', 'countries' => 'country', 'campaigns' => 'title'];
        $top_ten = [];
        $sort_configuration = new \IAWP\Sort_Configuration('views', 'desc');
        foreach ($queries as $type => $title) {
            if ($type === 'pages') {
                $query = new Pages($date_range, 10, null, $sort_configuration);
            } elseif ($type === 'referrers') {
                $query = new Referrers($date_range, 10, null, $sort_configuration);
            } elseif ($type === 'countries') {
                $query = new Countries($date_range, 10, null, $sort_configuration);
            } elseif ($type === 'campaigns') {
                $query = new Campaigns($date_range, 10, null, $sort_configuration);
            } else {
                continue;
            }
            $rows = \array_map(function ($row, $index) use($title) {
                $edited_title = $row->{$title}();
                $edited_title = \mb_strlen($edited_title) > 30 ? \mb_substr($edited_title, 0, 30) . '...' : $edited_title;
                return ['title' => $edited_title, 'views' => $row->views()];
            }, $query->rows(), \array_keys($query->rows()));
            $top_ten[$type] = $rows;
        }
        return $top_ten;
    }
}
