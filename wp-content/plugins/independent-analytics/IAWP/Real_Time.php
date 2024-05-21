<?php

namespace IAWP;

use DateTime;
use IAWP\Date_Range\Exact_Date_Range;
use IAWP\Interval\Minute_Interval;
use IAWP\Interval\Ten_Second_Interval;
use IAWP\Rows\Campaigns;
use IAWP\Rows\Countries;
use IAWP\Rows\Device_Types;
use IAWP\Rows\Pages;
use IAWP\Rows\Referrers;
use IAWP\Utils\Singleton;
/** @internal */
class Real_Time
{
    use Singleton;
    public function __construct()
    {
    }
    public function get_real_time_analytics()
    {
        $thirty_minutes_ago = new DateTime('-30 minutes');
        $thirty_minutes_ago = $this->round_up_by_seconds($thirty_minutes_ago, 60);
        $five_minutes_ago = new DateTime('-5 minutes');
        $five_minutes_ago = $this->round_up_by_seconds($five_minutes_ago, 10);
        $now = new DateTime();
        $end_minutes = $this->round_up_by_seconds($now, 60);
        $end_seconds = $this->round_up_by_seconds($now, 10);
        $visitors_by_minute_date_range = new Exact_Date_Range($thirty_minutes_ago, $end_minutes, \false);
        $visitors_by_minute_finder = new \IAWP\Visitors_Over_Time_Finder($visitors_by_minute_date_range, new Minute_Interval());
        $visitors_by_minute = $visitors_by_minute_finder->fetch();
        $visitors_by_second_date_range = new Exact_Date_Range($five_minutes_ago, $end_seconds, \false);
        $visitors_by_second_finder = new \IAWP\Visitors_Over_Time_Finder($visitors_by_second_date_range, new Ten_Second_Interval());
        $visitors_by_second = $visitors_by_second_finder->fetch();
        $five_minute_date_range = new Exact_Date_Range($five_minutes_ago, new DateTime(), \false);
        $current_traffic_finder = new \IAWP\Current_Traffic_Finder($five_minute_date_range);
        $current_traffic = $current_traffic_finder->fetch();
        $pages = new Pages($five_minute_date_range, 10);
        $page_rows = \array_map(function ($row, $index) {
            return ['id' => $row->id(), 'position' => $index + 1, 'title' => $row->title(), 'views' => $row->views(), 'subtitle' => $row->most_popular_subtitle()];
        }, $pages->rows(), \array_keys($pages->rows()));
        $referrers = new Referrers($five_minute_date_range, 10);
        $referrer_rows = \array_map(function ($row, $index) {
            return ['id' => $row->referrer(), 'position' => $index + 1, 'title' => $row->referrer(), 'views' => $row->views()];
        }, $referrers->rows(), \array_keys($referrers->rows()));
        $countries = new Countries($five_minute_date_range, 10);
        $country_rows = \array_map(function ($row, $index) {
            return ['id' => $row->country(), 'position' => $index + 1, 'title' => $row->country(), 'views' => $row->views(), 'flag' => \IAWP\Icon_Directory_Factory::flags()->find($row->country_code())];
        }, $countries->rows(), \array_keys($countries->rows()));
        $campaigns = new Campaigns($five_minute_date_range, 10);
        $campaign_rows = \array_map(function ($row, $index) {
            return ['id' => $row->params(), 'position' => $index + 1, 'title' => $row->utm_campaign(), 'views' => $row->views()];
        }, $campaigns->rows(), \array_keys($campaigns->rows()));
        $device_types = new Device_Types($five_minute_date_range, 10);
        $device_rows = \array_map(function ($row, $index) {
            return ['id' => $row->device_type(), 'position' => $index + 1, 'title' => $row->device_type(), 'views' => $row->views()];
        }, $device_types->rows(), \array_keys($device_types->rows()));
        $visitor_message = $this->get_visitor_count_message($current_traffic->get_visitor_count());
        $page_message = $this->get_count_message($current_traffic->get_page_count(), \__('Page', 'independent-analytics'), \__('Pages', 'independent-analytics'));
        $referrer_message = $this->get_count_message($current_traffic->get_referrer_count(), \__('Referrer', 'independent-analytics'), \__('Referrers', 'independent-analytics'));
        $country_message = $this->get_count_message($current_traffic->get_country_count(), \__('Country', 'independent-analytics'), \__('Countries', 'independent-analytics'));
        return ['visitor_message' => $visitor_message, 'page_message' => $page_message, 'referrer_message' => $referrer_message, 'country_message' => $country_message, 'chart_data' => ['minute_interval_visitors' => $visitors_by_minute->visitors, 'minute_interval_views' => $visitors_by_minute->views, 'minute_interval_labels_short' => $visitors_by_minute->interval_labels_short, 'minute_interval_labels_full' => $visitors_by_minute->interval_labels_full, 'second_interval_visitors' => $visitors_by_second->visitors, 'second_interval_views' => $visitors_by_second->views, 'second_interval_labels_short' => $visitors_by_second->interval_labels_short, 'second_interval_labels_full' => $visitors_by_second->interval_labels_full], 'lists' => ['pages' => ['title' => \__('Active Pages'), 'entries' => $page_rows], 'referrers' => ['title' => \__('Active Referrers'), 'entries' => $referrer_rows], 'countries' => ['title' => \__('Active Countries'), 'entries' => $country_rows], 'campaigns' => ['title' => \__('Active Campaigns'), 'entries' => $campaign_rows], 'device_types' => ['title' => \__('Device Types'), 'entries' => $device_rows]]];
    }
    public function render_real_time_analytics()
    {
        echo \IAWPSCOPED\iawp_blade()->run('real_time', $this->get_real_time_analytics());
    }
    private function get_count_message(int $count, string $singular, string $plural) : string
    {
        return \number_format_i18n($count) . ' ' . \_n($singular, $plural, $count);
    }
    private function get_visitor_count_message(int $count) : string
    {
        return $this->get_count_message($count, \__('Active Visitor', 'independent-analytics'), \__('Active Visitors', 'independent-analytics'));
    }
    private function round_up_by_seconds(DateTime $datetime, $precision_seconds) : DateTime
    {
        $datetime = clone $datetime;
        $datetime->setTimestamp($precision_seconds * (int) \ceil($datetime->getTimestamp() / $precision_seconds));
        return $datetime;
    }
}
