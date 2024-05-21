<?php

namespace IAWP\Migrations;

use IAWP\Report_Finder;
/** @internal */
class Migration_17 extends \IAWP\Migrations\Migration
{
    /**
     * @inheritdoc
     */
    protected $database_version = '17';
    /**
     * @inheritDoc
     */
    protected function migrate() : void
    {
        $this->add_initial_saved_reports();
    }
    private function add_initial_saved_reports() : void
    {
        Report_Finder::create_report(['name' => 'Blog Posts', 'type' => 'views', 'user_created_report' => 0, 'filters' => [['inclusion' => 'include', 'column' => 'type', 'operator' => 'is', 'operand' => 'post']]]);
        Report_Finder::create_report(['name' => 'Top Landing Pages', 'type' => 'views', 'user_created_report' => 0, 'sort_column' => 'entrances', 'sort_direction' => 'desc', 'columns' => ['title', 'visitors', 'views', 'average_view_duration', 'bounce_rate', 'entrances', 'url', 'type']]);
        Report_Finder::create_report(['name' => 'Fastest-Growing Pages', 'type' => 'views', 'user_created_report' => 0, 'sort_column' => 'visitors_growth', 'sort_direction' => 'desc', 'columns' => ['title', 'visitors', 'views', 'average_view_duration', 'bounce_rate', 'visitors_growth', 'url', 'type'], 'filters' => [['inclusion' => 'exclude', 'column' => 'visitors', 'operator' => 'lesser', 'operand' => '5']]]);
        Report_Finder::create_report(['name' => 'Today', 'type' => 'views', 'user_created_report' => 0, 'relative_range_id' => 'TODAY']);
        Report_Finder::create_report(['name' => 'Search Engine Traffic', 'type' => 'referrers', 'user_created_report' => 0, 'filters' => [['inclusion' => 'include', 'column' => 'referrer_type', 'operator' => 'is', 'operand' => 'Search']]]);
        Report_Finder::create_report(['name' => 'Social Media Traffic', 'type' => 'referrers', 'user_created_report' => 0, 'filters' => [['inclusion' => 'include', 'column' => 'referrer_type', 'operator' => 'is', 'operand' => 'Social']]]);
        Report_Finder::create_report(['name' => 'Fastest-Growing Referrers', 'type' => 'referrers', 'user_created_report' => 0, 'sort_column' => 'visitors_growth', 'sort_direction' => 'desc', 'columns' => ['referrer', 'referrer_type', 'visitors', 'views', 'average_session_duration', 'bounce_rate', 'visitors_growth'], 'filters' => [['inclusion' => 'exclude', 'column' => 'visitors', 'operator' => 'lesser', 'operand' => '5']]]);
        Report_Finder::create_report(['name' => 'Last 7 Days', 'type' => 'referrers', 'user_created_report' => 0, 'relative_range_id' => 'LAST_SEVEN']);
        Report_Finder::create_report(['name' => 'Cities', 'type' => 'geo', 'group_name' => 'city', 'user_created_report' => 0]);
        Report_Finder::create_report(['name' => 'European Countries', 'type' => 'geo', 'user_created_report' => 0, 'filters' => [['inclusion' => 'include', 'column' => 'continent', 'operator' => 'exact', 'operand' => 'Europe']]]);
        Report_Finder::create_report(['name' => 'Browsers', 'type' => 'devices', 'group_name' => 'browser', 'user_created_report' => 0]);
        Report_Finder::create_report(['name' => 'OS', 'type' => 'devices', 'group_name' => 'os', 'user_created_report' => 0]);
    }
}
