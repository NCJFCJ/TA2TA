<?php

namespace IAWP\Menu_Bar_Stats;

use IAWP\Capability_Manager;
use IAWP\Date_Range\Date_Range;
use IAWP\Date_Range\Relative_Date_Range;
use IAWP\Illuminate_Builder;
use IAWP\Query;
use IAWP\Resource_Identifier;
use IAWP\Utils\Number_Formatter;
use IAWPSCOPED\Illuminate\Database\Query\Builder;
use IAWPSCOPED\Illuminate\Database\Query\JoinClause;
/** @internal */
class Menu_Bar_Stats
{
    /**
     * @var ?Resource_Identifier
     */
    private $current_resource_identifier;
    private $views_today = 0;
    private $views_yesterday = 0;
    private $views_last_thirty = 0;
    private $views_total = 0;
    public function __construct()
    {
        $today = new Relative_Date_Range('TODAY');
        $yesterday = new Relative_Date_Range('YESTERDAY');
        $last_thirty = new Relative_Date_Range('LAST_THIRTY');
        $all_time = new Relative_Date_Range('ALL_TIME');
        if (\is_admin()) {
            $this->current_resource_identifier = Resource_Identifier::for_resource_being_edited();
        } else {
            $this->current_resource_identifier = Resource_Identifier::for_resource_being_viewed();
        }
        if (!\is_null($this->current_resource_identifier)) {
            $this->views_today = self::get_views_in_date_range($today);
            $this->views_yesterday = self::get_views_in_date_range($yesterday);
            $this->views_last_thirty = self::get_views_in_date_range($last_thirty);
            $this->views_total = self::get_views_in_date_range($all_time);
        }
    }
    /**
     * @return bool
     */
    public function is_enabled() : bool
    {
        if (!self::is_option_enabled()) {
            return \false;
        }
        if (\is_null($this->current_resource_identifier)) {
            return \false;
        }
        return \true;
    }
    /**
     * @return array[]
     */
    private function get_menu_bar_items() : array
    {
        $today = Number_Formatter::decimal($this->views_today);
        $yesterday = Number_Formatter::decimal($this->views_yesterday);
        $last_thirty = Number_Formatter::decimal($this->views_last_thirty);
        $total = Number_Formatter::decimal($this->views_total);
        return [['id' => 'iawp_admin_bar', 'title' => '<span class="ab-icon dashicons-analytics"></span>' . \sprintf('%s Views', \esc_html_x($today, 'X Views', 'independent-analytics')), 'meta' => ['class' => 'iawp_admin_bar_button']], ['id' => 'iawp_admin_bar_group_title', 'title' => '<span>' . \esc_html__('Date', 'independent-analytics') . '</span> ' . '<span>' . \esc_html__('Views', 'independent-analytics') . '</span>', 'parent' => 'iawp_admin_bar'], ['id' => 'iawp_admin_bar_today', 'title' => '<span>' . \esc_html__('Today:', 'independent-analytics') . '</span> <span>' . \esc_html__($today) . '</span>', 'parent' => 'iawp_admin_bar'], ['id' => 'iawp_admin_bar_yesterday', 'title' => '<span>' . \esc_html__('Yesterday:', 'independent-analytics') . '</span> <span>' . \esc_html__($yesterday) . '</span>', 'parent' => 'iawp_admin_bar'], ['id' => 'iawp_admin_bar_last_thirty', 'title' => '<span>' . \esc_html__('Last 30 Days:', 'independent-analytics') . '</span> <span>' . \esc_html__($last_thirty) . '</span>', 'parent' => 'iawp_admin_bar'], ['id' => 'iawp_admin_bar_total', 'title' => '<span>' . \esc_html__('All Time:', 'independent-analytics') . '</span> <span>' . \esc_html__($total) . '</span>', 'parent' => 'iawp_admin_bar'], ['id' => 'iawp_admin_bar_dashboard_group', 'parent' => 'iawp_admin_bar', 'is_group' => \true], ['id' => 'iawp_admin_bar_dashboard_link', 'title' => \esc_html__('Analytics Dashboard', 'independent-analytics') . ' &rarr;', 'href' => \esc_url(\IAWPSCOPED\iawp_dashboard_url()), 'parent' => 'iawp_admin_bar_dashboard_group']];
    }
    private function get_views_in_date_range(Date_Range $date_range) : int
    {
        $resources_table = Query::get_table_name(Query::RESOURCES);
        $views_table = Query::get_table_name(Query::VIEWS);
        $resource_statistics_query = Illuminate_Builder::get_builder();
        $resource = $this->current_resource_identifier;
        $resource_statistics_query->selectRaw('COUNT(*) AS views')->from("{$resources_table} as resources")->join("{$views_table} AS views", function (JoinClause $join) {
            $join->on('resources.id', '=', 'views.resource_id');
        })->where('resource', '=', $resource->type())->when($resource->has_meta(), function (Builder $query) use($resource) {
            $query->where($resource->meta_key(), '=', $resource->meta_value());
        })->whereBetween('views.viewed_at', [$date_range->iso_start(), $date_range->iso_end()]);
        $resource_statistics = $resource_statistics_query->get()->first();
        return $resource_statistics->views ?? 0;
    }
    public static function is_option_enabled() : bool
    {
        return \IAWPSCOPED\iawp()->get_option('iawp_disable_admin_toolbar_analytics', \false) === \false && Capability_Manager::can_view();
    }
    public static function register()
    {
        \add_action('admin_bar_menu', function ($admin_bar) {
            $menu_bar_stats = new self();
            if (!$menu_bar_stats->is_enabled()) {
                return;
            }
            foreach ($menu_bar_stats->get_menu_bar_items() as $menu_bar_item) {
                $is_group = $menu_bar_item['is_group'] ?? \false;
                // Should the item be registered as a group or a node?
                if ($is_group) {
                    $admin_bar->add_group($menu_bar_item);
                } else {
                    $admin_bar->add_node($menu_bar_item);
                }
            }
        }, 100);
    }
}
