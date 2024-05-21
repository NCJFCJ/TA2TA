<?php

namespace IAWP;

use IAWP\Date_Range\Relative_Date_Range;
use IAWP\Utils\Number_Formatter;
use IAWP\Utils\Security;
use IAWPSCOPED\Illuminate\Database\Query\Builder;
use IAWPSCOPED\Illuminate\Database\Query\JoinClause;
/** @internal */
class View_Counter
{
    public function __construct()
    {
        \add_action('the_content', [$this, 'output_counter']);
        \add_action('init', [$this, 'add_shortcode']);
        \add_action('add_meta_boxes', [$this, 'maybe_add_meta_box'], 10);
        \add_action('save_post', [$this, 'save_metabox_settings']);
    }
    public function output_counter($content)
    {
        if (!$this->passes_checks()) {
            return $content;
        }
        $position = \IAWPSCOPED\iawp()->get_option('iawp_view_counter_position', 'after');
        $counter = $this->get_counter_html();
        if ($position == 'before' || $position == 'both') {
            $content = $counter . $content;
        }
        if ($position == 'after' || $position == 'both') {
            $content .= $counter;
        }
        return $content;
    }
    public function get_counter_html($label = null, $icon = null)
    {
        $current_resource = \IAWP\Resource_Identifier::for_resource_being_viewed();
        // Get stats for individual posts in the loop if shortcode added to each post
        global $post;
        if ($post->ID != $current_resource->meta_value() && \is_main_query() && \in_the_loop()) {
            $current_resource = \IAWP\Resource_Identifier::for_post_id($post->ID);
        }
        if (\is_null($current_resource)) {
            return;
        }
        $view_count = $this->get_view_count($current_resource);
        if (\IAWPSCOPED\iawp()->get_option('iawp_view_counter_manual_adjustment', \false)) {
            $view_count += \intval(\get_post_meta($current_resource->meta_value(), 'iawp_view_counter_adjustment', \true));
        }
        $view_count = Number_Formatter::decimal($view_count);
        if (\is_null($label)) {
            $default = \function_exists('IAWPSCOPED\\pll__') ? pll__('Views:', 'independent-analytics') : \__('Views:', 'independent-analytics');
            $label = \IAWPSCOPED\iawp()->get_option('iawp_view_counter_label', $default);
        }
        if (\is_null($icon)) {
            $icon = \get_option('iawp_view_counter_icon', \true);
        }
        if ($icon) {
            $svg = '<svg height="20" viewBox="0 0 192 192" width="20" fill="currentColor" style="margin-right:6px; margin-top:-2px;"><path d="m16 176v-136h-16v144a8 8 0 0 0 8 8h184v-16z"/><path d="m72 112a8 8 0 0 0 -8-8h-24a8 8 0 0 0 -8 8v56h40z"/><path d="m128 80a8 8 0 0 0 -8-8h-24a8 8 0 0 0 -8 8v88h40z"/><path d="m184 48a8 8 0 0 0 -8-8h-24a8 8 0 0 0 -8 8v120h40z"/></svg>';
            $label = $svg . ' ' . $label;
        }
        return '<div class="iawp-view-counter" style="display: flex;"><span class="view-counter-text" style="display: flex; align-items: center;">' . Security::svg($label) . '</span> <span class="view-counter-value" style="margin-left: 3px;">' . \esc_html($view_count) . '</span></div>';
    }
    public function add_shortcode()
    {
        \add_shortcode('iawp_view_counter', [$this, 'shortcode']);
    }
    public function shortcode($atts)
    {
        $a = \shortcode_atts(['label' => \IAWPSCOPED\iawp()->get_option('iawp_view_counter_label', \esc_html__('Views:', 'independent-analytics')), 'icon' => \true], $atts);
        return $this->get_counter_html($a['label'], $a['icon']);
    }
    public function maybe_add_meta_box() : void
    {
        if (!\IAWPSCOPED\iawp()->get_option('iawp_view_counter_manual_adjustment', \false)) {
            return;
        }
        foreach (\IAWPSCOPED\iawp()->get_option('iawp_view_counter_post_types', []) as $screen) {
            \add_meta_box('iawp-view-counter-adjustment', \esc_html__('View Counter Adjustment', 'independent-analytics'), [$this, 'render_meta_box_content'], $screen, 'side');
        }
    }
    public function render_meta_box_content() : void
    {
        global $post;
        echo '<p>' . \esc_html__('Increase count by:', 'independent-analytics') . '<input type="number" name="iawp_view_counter_adjustment" id="iawp_view_counter_adjustment" 
            value="' . \esc_attr(\get_post_meta($post->ID, 'iawp_view_counter_adjustment', \true)) . '"
            placeholder="0" style="max-width: 80px; margin-left: 8px" />
            <a class="info-link" 
                href="https://independentwp.com/knowledgebase/dashboard/display-view-counter/" 
                target="_blank"
                style="text-decoration:none;float:right;margin-top:4px">
                    <span class="dashicons dashicons-editor-help"></span>
                </a>
            </p>';
    }
    public function save_metabox_settings(int $post_id)
    {
        if (\array_key_exists('iawp_view_counter_adjustment', $_POST)) {
            \update_post_meta($post_id, 'iawp_view_counter_adjustment', \absint($_POST['iawp_view_counter_adjustment']));
        }
    }
    private function passes_checks() : bool
    {
        if (!\is_singular() || !\is_main_query()) {
            return \false;
        }
        if (\IAWPSCOPED\iawp()->get_option('iawp_view_counter_enable', \false) == \false) {
            return \false;
        }
        if (!\in_array(\get_post_type(), \IAWPSCOPED\iawp()->get_option('iawp_view_counter_post_types', []))) {
            return \false;
        }
        if (\IAWPSCOPED\iawp()->get_option('iawp_view_counter_private', \false) && !\is_user_logged_in()) {
            return \false;
        }
        $exclude = \IAWPSCOPED\iawp()->get_option('iawp_view_counter_exclude', '');
        if ($exclude != '') {
            $exclude = \explode(',', $exclude);
            if (\in_array(\get_the_ID(), $exclude)) {
                return \false;
            }
        }
        return \true;
    }
    private function get_view_count(\IAWP\Resource_Identifier $resource) : int
    {
        $resources_table = \IAWP\Query::get_table_name(\IAWP\Query::RESOURCES);
        $views_table = \IAWP\Query::get_table_name(\IAWP\Query::VIEWS);
        $is_today = \IAWPSCOPED\iawp()->get_option('iawp_view_counter_views_to_count', 'total') === 'today';
        $is_last_thirty = \IAWPSCOPED\iawp()->get_option('iawp_view_counter_views_to_count', 'total') === 'last_thirty';
        $is_this_month = \IAWPSCOPED\iawp()->get_option('iawp_view_counter_views_to_count', 'total') === 'this_month';
        $is_last_month = \IAWPSCOPED\iawp()->get_option('iawp_view_counter_views_to_count', 'total') === 'last_month';
        $query = \IAWP\Illuminate_Builder::get_builder()->selectRaw('COUNT(views.id) AS views')->from($resources_table, 'resources')->leftJoin("{$views_table} AS views", function (JoinClause $join) {
            $join->on('resources.id', '=', 'views.resource_id');
        })->where('resource', '=', $resource->type())->when($resource->has_meta(), function (Builder $query) use($resource) {
            $query->where($resource->meta_key(), '=', $resource->meta_value());
        })->when($is_today, function (Builder $query) {
            $today = new Relative_Date_Range('TODAY');
            $query->whereBetween('viewed_at', [$today->iso_start(), $today->iso_end()]);
        })->when($is_last_thirty, function (Builder $query) {
            $today = new Relative_Date_Range('LAST_THIRTY');
            $query->whereBetween('viewed_at', [$today->iso_start(), $today->iso_end()]);
        })->when($is_this_month, function (Builder $query) {
            $today = new Relative_Date_Range('THIS_MONTH');
            $query->whereBetween('viewed_at', [$today->iso_start(), $today->iso_end()]);
        })->when($is_last_month, function (Builder $query) {
            $today = new Relative_Date_Range('LAST_MONTH');
            $query->whereBetween('viewed_at', [$today->iso_start(), $today->iso_end()]);
        })->groupBy('resources.id');
        $views = $query->value('views');
        return \is_null($views) ? 0 : $views;
    }
}
