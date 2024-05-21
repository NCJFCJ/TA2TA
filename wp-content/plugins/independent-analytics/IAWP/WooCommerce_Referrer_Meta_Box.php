<?php

namespace IAWP;

use IAWP\Models\Campaign;
use IAWP\Models\Referrer;
use IAWP\Utils\Security;
use IAWPSCOPED\Illuminate\Database\Query\JoinClause;
/** @internal */
class WooCommerce_Referrer_Meta_Box
{
    public function __construct()
    {
        \add_action('add_meta_boxes_shop_order', [$this, 'maybe_add_meta_box'], 10, 1);
    }
    public function maybe_add_meta_box(\WP_Post $post) : void
    {
        if (\is_null($this->get_referrer_for($post))) {
            return;
        }
        \add_meta_box('iawp-wc-referrer-source', \esc_html__('Order Referrer', 'independent-analytics'), [$this, 'render_meta_box_content'], 'shop_order', 'side');
    }
    public function render_meta_box_content(\WP_Post $post) : void
    {
        $referrer_row = $this->get_referrer_for($post);
        $campaign_row = $this->get_campaign_for($post);
        $referrer_row->referrer_ids = !\is_null($referrer_row->referrer_id) ? [$referrer_row->referrer_id] : null;
        $referrer = new Referrer($referrer_row);
        $campaign = new Campaign($campaign_row);
        if ($referrer->has_link()) {
            $content = '<a target="_blank" href="' . \esc_url($referrer->referrer_url()) . '">' . Security::string($referrer->referrer()) . '</a>';
        } else {
            $content = Security::string($referrer->referrer());
        }
        echo '<p><strong>' . \esc_html__('Referrer:', 'independent-analytics') . '</strong> <span data-testid="referrer">' . $content . '</span> <a class="info-link" 
                href="https://independentwp.com/knowledgebase/woocommerce/order-referrers-box/" 
                target="_blank"
                style="text-decoration:none;float:right;">
                    <span class="dashicons dashicons-editor-help"></span>
                </a>
        </p>';
        if (\is_string($campaign->utm_source())) {
            echo '<p><strong>' . \__('Campaign Source', 'independent-analytics') . ':</strong> <span data-testid="source">' . Security::string($campaign->utm_source()) . '</span> </p>';
        }
        if (\is_string($campaign->utm_medium())) {
            echo '<p><strong>' . \__('Campaign Medium', 'independent-analytics') . ':</strong> <span data-testid="medium">' . Security::string($campaign->utm_medium()) . '</span> </p>';
        }
        if (\is_string($campaign->utm_campaign())) {
            echo '<p><strong>' . \__('Campaign Name', 'independent-analytics') . ':</strong> <span data-testid="campaign">' . Security::string($campaign->utm_campaign()) . '</span> </p>';
        }
        if (\is_string($campaign->utm_term())) {
            echo '<p><strong>' . \__('Campaign Term', 'independent-analytics') . ':</strong> <span data-testid="term">' . Security::string($campaign->utm_term()) . '</span> </p>';
        }
        if (\is_string($campaign->utm_content())) {
            echo '<p><strong>' . \__('Campaign Content', 'independent-analytics') . ':</strong> <span data-testid="content">' . Security::string($campaign->utm_content()) . '</span> </p>';
        }
    }
    private function get_campaign_for(\WP_Post $post) : ?object
    {
        $wc_orders_table = \IAWP\Query::get_table_name(\IAWP\Query::WC_ORDERS);
        $campaigns_table = \IAWP\Query::get_table_name(\IAWP\Query::CAMPAIGNS);
        $views_table = \IAWP\Query::get_table_name(\IAWP\Query::VIEWS);
        $sessions_table = \IAWP\Query::get_table_name(\IAWP\Query::SESSIONS);
        $referrer_query = \IAWP\Illuminate_Builder::get_builder();
        $referrer_query->select("landing_page_title AS title", "utm_source", "utm_medium", "utm_campaign", "utm_term", "utm_content")->from($wc_orders_table, 'orders')->leftJoin("{$views_table} AS views", function (JoinClause $join) {
            $join->on('views.id', '=', 'orders.view_id');
        })->leftJoin("{$sessions_table} AS sessions", function (JoinClause $join) {
            $join->on('sessions.session_id', '=', 'views.session_id');
        })->leftJoin("{$campaigns_table} AS campaigns", function (JoinClause $join) {
            $join->on('sessions.campaign_id', '=', 'campaigns.campaign_id');
        })->where('orders.order_id', '=', $post->ID);
        $record = $referrer_query->get()->first();
        return $record;
    }
    private function get_referrer_for(\WP_Post $post) : ?object
    {
        $wc_orders_table = \IAWP\Query::get_table_name(\IAWP\Query::WC_ORDERS);
        $views_table = \IAWP\Query::get_table_name(\IAWP\Query::VIEWS);
        $sessions_table = \IAWP\Query::get_table_name(\IAWP\Query::SESSIONS);
        $referrer_table = \IAWP\Query::get_table_name(\IAWP\Query::REFERRERS);
        $referrer_query = \IAWP\Illuminate_Builder::get_builder();
        $referrer_query->select('sessions.referrer_id', 'referrer', 'type AS referrer_type', 'domain')->from($wc_orders_table, 'orders')->leftJoin("{$views_table} AS views", function (JoinClause $join) {
            $join->on('views.id', '=', 'orders.view_id');
        })->leftJoin("{$sessions_table} AS sessions", function (JoinClause $join) {
            $join->on('sessions.session_id', '=', 'views.session_id');
        })->leftJoin("{$referrer_table} AS referrers", function (JoinClause $join) {
            $join->on('referrers.id', '=', 'sessions.referrer_id');
        })->where('orders.order_id', '=', $post->ID);
        $record = $referrer_query->get()->first();
        return $record;
    }
}
