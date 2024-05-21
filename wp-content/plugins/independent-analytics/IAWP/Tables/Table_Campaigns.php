<?php

namespace IAWP\Tables;

use IAWP\Rows\Campaigns;
use IAWP\Statistics\Campaign_Statistics;
use IAWP\Tables\Columns\Column;
use IAWP\Tables\Groups\Group;
use IAWP\Tables\Groups\Groups;
/** @internal */
class Table_Campaigns extends \IAWP\Tables\Table
{
    protected function table_name() : string
    {
        return 'campaigns';
    }
    protected function groups() : Groups
    {
        $groups = [];
        $groups[] = new Group('campaign', \__('Campaign', 'independent-analytics'), Campaigns::class, Campaign_Statistics::class);
        return new Groups($groups);
    }
    protected function local_columns() : array
    {
        return [new Column(['id' => 'title', 'label' => \esc_html__('Landing Page', 'independent-analytics'), 'visible' => \true, 'type' => 'string']), new Column(['id' => 'utm_source', 'label' => \esc_html__('Source', 'independent-analytics'), 'visible' => \true, 'type' => 'string']), new Column(['id' => 'utm_medium', 'label' => \esc_html__('Medium', 'independent-analytics'), 'visible' => \true, 'type' => 'string']), new Column(['id' => 'utm_campaign', 'label' => \esc_html__('Campaign', 'independent-analytics'), 'visible' => \true, 'type' => 'string']), new Column(['id' => 'utm_term', 'label' => \esc_html__('Term', 'independent-analytics'), 'visible' => \false, 'type' => 'string', 'is_nullable' => \true]), new Column(['id' => 'utm_content', 'label' => \esc_html__('Content', 'independent-analytics'), 'visible' => \false, 'type' => 'string', 'is_nullable' => \true]), new Column(['id' => 'visitors', 'label' => \esc_html__('Visitors', 'independent-analytics'), 'visible' => \true, 'type' => 'int']), new Column(['id' => 'views', 'label' => \esc_html__('Views', 'independent-analytics'), 'visible' => \true, 'type' => 'int']), new Column(['id' => 'sessions', 'label' => \esc_html__('Sessions', 'independent-analytics'), 'visible' => \false, 'type' => 'int']), new Column(['id' => 'average_session_duration', 'label' => \esc_html__('Session Duration', 'independent-analytics'), 'visible' => \true, 'type' => 'int', 'filter_placeholder' => 'Seconds']), new Column(['id' => 'views_per_session', 'label' => \esc_html__('Views Per Session', 'independent-analytics'), 'visible' => \false, 'type' => 'int']), new Column(['id' => 'bounce_rate', 'label' => \esc_html__('Bounce Rate', 'independent-analytics'), 'visible' => \true, 'type' => 'int']), new Column(['id' => 'visitors_growth', 'label' => \esc_html__('Visitors Growth', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'exportable' => \false]), new Column(['id' => 'views_growth', 'label' => \esc_html__('Views Growth', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'exportable' => \false]), new Column(['id' => 'wc_orders', 'label' => \esc_html__('Orders', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'requires_woocommerce' => \true]), new Column(['id' => 'wc_gross_sales', 'label' => \esc_html__('Gross Sales', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'requires_woocommerce' => \true]), new Column(['id' => 'wc_refunds', 'label' => \esc_html__('Refunds', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'requires_woocommerce' => \true]), new Column(['id' => 'wc_refunded_amount', 'label' => \esc_html__('Refunded Amount', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'requires_woocommerce' => \true]), new Column(['id' => 'wc_net_sales', 'label' => \esc_html__('Net Sales', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'requires_woocommerce' => \true]), new Column(['id' => 'wc_conversion_rate', 'label' => \esc_html__('Conversion Rate', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'requires_woocommerce' => \true]), new Column(['id' => 'wc_earnings_per_visitor', 'label' => \esc_html__('Earnings Per Visitor', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'requires_woocommerce' => \true]), new Column(['id' => 'wc_average_order_volume', 'label' => \esc_html__('Average Order Volume', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'requires_woocommerce' => \true])];
    }
}
