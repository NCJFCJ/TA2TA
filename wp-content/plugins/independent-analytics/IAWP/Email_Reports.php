<?php

namespace IAWP;

use IAWP\Date_Range\Exact_Date_Range;
use IAWP\Date_Range\Relative_Date_Range;
use IAWP\Rows\Campaigns;
use IAWP\Rows\Countries;
use IAWP\Rows\Device_Types;
use IAWP\Rows\Pages;
use IAWP\Rows\Referrers;
use IAWP\Statistics\Intervals\Hourly;
use IAWP\Statistics\Page_Statistics;
use IAWP\Utils\URL;
/** @internal */
class Email_Reports
{
    public function __construct()
    {
        \add_filter('cron_schedules', [$this, 'add_monthly_schedule_cron']);
        $monitored_options = ['iawp_email_report_interval', 'iawp_email_report_time', 'iawp_email_report_email_addresses'];
        foreach ($monitored_options as $option) {
            \add_action('update_option_' . $option, [$this, 'schedule_email_report'], 10, 0);
            \add_action('add_option_' . $option, [$this, 'schedule_email_report'], 10, 0);
        }
        // Maybe reschedule when starting day of the week is changed
        \add_action('update_option_iawp_dow', [$this, 'maybe_reschedule_email_report'], 10, 0);
        \add_action('add_option_iawp_dow', [$this, 'maybe_reschedule_email_report'], 10, 0);
        \add_action('iawp_send_email_report', [$this, 'send_email_report']);
    }
    public function schedule_email_report()
    {
        $this->unschedule_email_report();
        if (empty(\IAWPSCOPED\iawp()->get_option('iawp_email_report_email_addresses', []))) {
            return;
        }
        $interval = \IAWPSCOPED\iawp()->get_option('iawp_email_report_interval', 'monthly');
        $delivery_time = $this->get_delivery_time();
        \wp_schedule_event($delivery_time->getTimestamp(), $interval, 'iawp_send_email_report');
    }
    public function get_delivery_time()
    {
        $interval = \IAWPSCOPED\iawp()->get_option('iawp_email_report_interval', 'monthly');
        $date_string = 'first day of +1 month';
        if ($interval == 'weekly') {
            $first_dow = \IAWPSCOPED\iawp()->get_option('iawp_dow', 0);
            $date_string = 'next ' . $this->days_of_week()[$first_dow];
        } elseif ($interval == 'daily') {
            $date_string = 'tomorrow';
        }
        $delivery_time = new \DateTime($date_string, new \DateTimeZone(\wp_timezone_string()));
        $delivery_time->setTime(\IAWPSCOPED\iawp()->get_option('iawp_email_report_time', 9), 0);
        return $delivery_time;
    }
    public function unschedule_email_report()
    {
        $timestamp = \wp_next_scheduled('iawp_send_email_report');
        \wp_unschedule_event($timestamp, 'iawp_send_email_report');
    }
    /* $use_cron is normally true so we report on the date from the cron event.
     ** It is set to false for testing purposes. This allows the date to be reconstructed and compared to the cron event date */
    public function get_next_scheduled_email_date_formatted($use_cron = \true)
    {
        if (!\wp_next_scheduled('iawp_send_email_report')) {
            return \esc_html__('There is no email scheduled.', 'independent-analytics');
        }
        if ($use_cron) {
            $date = new \DateTime('now', new \DateTimeZone(\wp_timezone_string()));
            $date->setTimestamp(\wp_next_scheduled('iawp_send_email_report'));
            $date->setTime(\IAWPSCOPED\iawp()->get_option('iawp_email_report_time', 9), 0);
        } else {
            $date = $this->get_delivery_time();
        }
        $day = $date->format(\get_option('date_format'));
        $time = $date->format(\get_option('time_format'));
        return \sprintf(\__('Next email scheduled for %s at %s.', 'independent-analytics'), '<span>' . $day . '</span>', '<span>' . $time . '</span>');
    }
    public function add_monthly_schedule_cron($schedules)
    {
        $schedules['monthly'] = ['interval' => \MONTH_IN_SECONDS, 'display' => \esc_html__('Once a Month', 'independent-analytics')];
        return $schedules;
    }
    public function maybe_reschedule_email_report()
    {
        if (!\wp_next_scheduled('iawp_send_email_report')) {
            return;
        }
        if (\IAWPSCOPED\iawp()->get_option('iawp_email_report_interval', 'monthly') != 'weekly') {
            return;
        }
        $this->schedule_email_report();
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
    public function get_email_preview($colors)
    {
        return $this->get_email_body($colors);
    }
    private function days_of_week()
    {
        return ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    }
    private function get_email_body($colors = '')
    {
        $interval = \IAWPSCOPED\iawp()->get_option('iawp_email_report_interval', 'monthly');
        if ($interval == 'monthly') {
            $statistics = new Page_Statistics(new Relative_Date_Range('LAST_MONTH'));
        } elseif ($interval == 'weekly') {
            $statistics = new Page_Statistics(new Relative_Date_Range('LAST_WEEK'));
        } else {
            $statistics = new Page_Statistics(new Relative_Date_Range('YESTERDAY'), null, new Hourly());
        }
        $quick_stats = (new \IAWP\Quick_Stats(null, $statistics))->get_stats();
        $chart = new \IAWP\Email_Chart($statistics);
        $colors = $colors == '' ? \IAWPSCOPED\iawp()->get_option('iawp_email_report_colors', ['#5123a0', '#fafafa', '#3a1e6b', '#fafafa', '#5123a0', '#a985e6', '#ece9f2', '#f7f5fa', '#ece9f2', '#dedae6']) : \explode(',', $colors);
        return \IAWPSCOPED\iawp_blade()->run('email.email', ['site_title' => \get_bloginfo('name'), 'site_url' => (new URL(\get_site_url()))->get_domain(), 'date' => $this->get_email_date_subheading(), 'stats' => $quick_stats, 'top_ten' => $this->get_top_ten(), 'chart_views' => $chart->views, 'most_views' => $chart->most_views, 'y_labels' => $chart->y_labels, 'x_labels' => $chart->x_labels, 'colors' => $colors]);
    }
    private function get_email_start_end_dates()
    {
        $interval = \IAWPSCOPED\iawp()->get_option('iawp_email_report_interval', 'monthly');
        $start = new \DateTime('First day of last month', new \DateTimeZone(\wp_timezone_string()));
        $end = new \DateTime('Last day of last month', new \DateTimeZone(\wp_timezone_string()));
        if ($interval == 'weekly') {
            $date_string = 'last ' . $this->days_of_week()[\IAWPSCOPED\iawp()->get_option('iawp_dow', 0)];
            $start = new \DateTime($date_string, new \DateTimeZone(\wp_timezone_string()));
            // -1 week if today isn't the chosen day of the week
            if ((new \DateTime('now'))->format('w') != \IAWPSCOPED\iawp()->get_option('iawp_dow', 0)) {
                $start->modify('-7 days');
            }
            $end = clone $start;
            $end->modify('+6 days');
        } elseif ($interval == 'daily') {
            $start = new \DateTime('Yesterday', new \DateTimeZone(\wp_timezone_string()));
            $end = clone $start;
        }
        return [$start, $end];
    }
    private function get_email_date_subheading()
    {
        $dates = $this->get_email_start_end_dates();
        $interval = \IAWPSCOPED\iawp()->get_option('iawp_email_report_interval', 'monthly');
        $format = \get_option('date_format');
        $string = $dates[0]->format('F Y');
        if ($interval == 'weekly') {
            $string = $dates[0]->format($format) . ' - ' . $dates[1]->format($format);
        } elseif ($interval == 'daily') {
            $string = $dates[0]->format($format);
        }
        return $string;
    }
    private function get_top_ten() : array
    {
        $dates = $this->get_email_start_end_dates();
        $date_range = new Exact_Date_Range($dates[0], $dates[1]);
        $queries = ['pages' => 'title', 'referrers' => 'referrer', 'countries' => 'country', 'devices' => 'device_type', 'campaigns' => 'title', 'landing_pages' => 'title', 'exit_pages' => 'title'];
        $top_ten = [];
        $sort_configuration = new \IAWP\Sort_Configuration('views', 'desc');
        foreach ($queries as $type => $title) {
            if ($type === 'pages') {
                $query = new Pages($date_range, 10, null, $sort_configuration);
            } elseif ($type === 'referrers') {
                $query = new Referrers($date_range, 10, null, $sort_configuration);
            } elseif ($type === 'countries') {
                $query = new Countries($date_range, 10, null, $sort_configuration);
            } elseif ($type === 'devices') {
                $query = new Device_Types($date_range, 10, null, $sort_configuration);
            } elseif ($type === 'campaigns') {
                $query = new Campaigns($date_range, 10, null, $sort_configuration);
            } elseif ($type === 'landing_pages') {
                $query = new Pages($date_range, 10, null, new \IAWP\Sort_Configuration('entrances', 'desc'));
            } elseif ($type === 'exit_pages') {
                $query = new Pages($date_range, 10, null, new \IAWP\Sort_Configuration('exits', 'desc'));
            } else {
                continue;
            }
            $rows = \array_map(function ($row, $index) use($type, $title) {
                $edited_title = $row->{$title}();
                $edited_title = \mb_strlen($edited_title) > 30 ? \mb_substr($edited_title, 0, 30) . '...' : $edited_title;
                $metric = 'views';
                if ($type == 'landing_pages') {
                    $metric = 'entrances';
                } elseif ($type == 'exit_pages') {
                    $metric = 'exits';
                }
                return ['title' => $edited_title, 'views' => $row->{$metric}()];
            }, $query->rows(), \array_keys($query->rows()));
            if (\count($rows) == 0) {
                continue;
            }
            $top_ten[$type] = $rows;
        }
        return $top_ten;
    }
}
