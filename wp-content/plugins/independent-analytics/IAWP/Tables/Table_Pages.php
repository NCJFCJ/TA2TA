<?php

namespace IAWP\Tables;

use IAWP\Filter_Lists\Author_Filter_List;
use IAWP\Filter_Lists\Category_Filter_List;
use IAWP\Filter_Lists\Page_Type_Filter_List;
use IAWP\Rows\Pages;
use IAWP\Statistics\Page_Statistics;
use IAWP\Tables\Columns\Column;
use IAWP\Tables\Groups\Group;
use IAWP\Tables\Groups\Groups;
/** @internal */
class Table_Pages extends \IAWP\Tables\Table
{
    protected function table_name() : string
    {
        return 'views';
    }
    protected function groups() : Groups
    {
        $groups = [];
        $groups[] = new Group('page', \__('Page', 'independent-analytics'), Pages::class, Page_Statistics::class);
        return new Groups($groups);
    }
    protected function local_columns() : array
    {
        return [new Column(['id' => 'title', 'label' => \esc_html__('Title', 'independent-analytics'), 'visible' => \true, 'type' => 'string', 'database_column' => 'cached_title']), new Column(['id' => 'visitors', 'label' => \esc_html__('Visitors', 'independent-analytics'), 'visible' => \true, 'type' => 'int']), new Column(['id' => 'views', 'label' => \esc_html__('Views', 'independent-analytics'), 'visible' => \true, 'type' => 'int']), new Column(['id' => 'sessions', 'label' => \esc_html__('Sessions', 'independent-analytics'), 'visible' => \false, 'type' => 'int']), new Column(['id' => 'average_view_duration', 'label' => \esc_html__('View Duration', 'independent-analytics'), 'visible' => \true, 'type' => 'int', 'filter_placeholder' => 'Seconds']), new Column(['id' => 'bounce_rate', 'label' => \esc_html__('Bounce Rate', 'independent-analytics'), 'visible' => \true, 'type' => 'int']), new Column(['id' => 'visitors_growth', 'label' => \esc_html__('Visitors Growth', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'exportable' => \false]), new Column(['id' => 'views_growth', 'label' => \esc_html__('Views Growth', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'exportable' => \false]), new Column(['id' => 'entrances', 'label' => \esc_html__('Entrances', 'independent-analytics'), 'visible' => \false, 'type' => 'int']), new Column(['id' => 'exits', 'label' => \esc_html__('Exits', 'independent-analytics'), 'visible' => \false, 'type' => 'int']), new Column(['id' => 'exit_percent', 'label' => \esc_html__('Exit Rate', 'independent-analytics'), 'visible' => \false, 'type' => 'int']), new Column(['id' => 'url', 'label' => \esc_html__('URL', 'independent-analytics'), 'visible' => \true, 'type' => 'string', 'database_column' => 'cached_url']), new Column(['id' => 'author', 'label' => \esc_html__('Author', 'independent-analytics'), 'visible' => \false, 'type' => 'select', 'options' => Author_Filter_List::options(), 'database_column' => 'cached_author_id', 'is_nullable' => \true]), new Column(['id' => 'type', 'label' => \esc_html__('Page Type', 'independent-analytics'), 'visible' => \true, 'type' => 'select', 'options' => Page_Type_Filter_List::options(), 'database_column' => 'cached_type', 'is_nullable' => \true]), new Column(['id' => 'date', 'label' => \esc_html__('Publish Date', 'independent-analytics'), 'visible' => \false, 'type' => 'date', 'database_column' => 'cached_date', 'is_nullable' => \true]), new Column(['id' => 'category', 'label' => \esc_html__('Post Category', 'independent-analytics'), 'visible' => \false, 'type' => 'select', 'options' => Category_Filter_List::options(), 'database_column' => 'cached_category', 'is_nullable' => \true]), new Column(['id' => 'comments', 'label' => \esc_html__('Comments', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'is_nullable' => \true]), new Column(['id' => 'wc_orders', 'label' => \esc_html__('Orders', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'requires_woocommerce' => \true]), new Column(['id' => 'wc_gross_sales', 'label' => \esc_html__('Gross Sales', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'requires_woocommerce' => \true]), new Column(['id' => 'wc_refunds', 'label' => \esc_html__('Refunds', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'requires_woocommerce' => \true]), new Column(['id' => 'wc_refunded_amount', 'label' => \esc_html__('Refunded Amount', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'requires_woocommerce' => \true]), new Column(['id' => 'wc_net_sales', 'label' => \esc_html__('Net Sales', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'requires_woocommerce' => \true]), new Column(['id' => 'wc_conversion_rate', 'label' => \esc_html__('Conversion Rate', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'requires_woocommerce' => \true]), new Column(['id' => 'wc_earnings_per_visitor', 'label' => \esc_html__('Earnings Per Visitor', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'requires_woocommerce' => \true]), new Column(['id' => 'wc_average_order_volume', 'label' => \esc_html__('Average Order Volume', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'requires_woocommerce' => \true])];
    }
}
