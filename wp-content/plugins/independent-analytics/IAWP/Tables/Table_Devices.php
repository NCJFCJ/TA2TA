<?php

namespace IAWP\Tables;

use IAWP\Filter_Lists\Device_Browser_Filter_List;
use IAWP\Filter_Lists\Device_OS_Filter_List;
use IAWP\Filter_Lists\Device_Type_Filter_List;
use IAWP\Rows\Device_Browsers;
use IAWP\Rows\Device_OSS;
use IAWP\Rows\Device_Types;
use IAWP\Statistics\Device_Browser_Statistics;
use IAWP\Statistics\Device_OS_Statistics;
use IAWP\Statistics\Device_Type_Statistics;
use IAWP\Tables\Columns\Column;
use IAWP\Tables\Groups\Group;
use IAWP\Tables\Groups\Groups;
/** @internal */
class Table_Devices extends \IAWP\Tables\Table
{
    protected function table_name() : string
    {
        return 'devices';
    }
    protected function groups() : Groups
    {
        $groups = [];
        $groups[] = new Group('device_type', \__('Device Type', 'independent-analytics'), Device_Types::class, Device_Type_Statistics::class);
        $groups[] = new Group('os', \__('OS', 'independent-analytics'), Device_OSS::class, Device_OS_Statistics::class);
        $groups[] = new Group('browser', \__('Browser', 'independent-analytics'), Device_Browsers::class, Device_Browser_Statistics::class);
        return new Groups($groups);
    }
    protected function local_columns() : array
    {
        return [new Column(['id' => 'device_type', 'label' => \__('Type', 'independent-analytics'), 'visible' => \true, 'type' => 'select', 'options' => Device_Type_Filter_List::options(), 'database_column' => 'device_types.device_type_id', 'unavailable_for' => ['browser', 'os']]), new Column(['id' => 'os', 'label' => \__('Operating System', 'independent-analytics'), 'visible' => \true, 'type' => 'select', 'options' => Device_OS_Filter_List::options(), 'database_column' => 'device_oss.device_os_id', 'unavailable_for' => ['device_type', 'browser']]), new Column(['id' => 'browser', 'label' => \__('Browser', 'independent-analytics'), 'visible' => \true, 'type' => 'select', 'options' => Device_Browser_Filter_List::options(), 'database_column' => 'device_browsers.device_browser_id', 'unavailable_for' => ['device_type', 'os']]), new Column(['id' => 'visitors', 'label' => \esc_html__('Visitors', 'independent-analytics'), 'visible' => \true, 'type' => 'int']), new Column(['id' => 'views', 'label' => \esc_html__('Views', 'independent-analytics'), 'visible' => \true, 'type' => 'int']), new Column(['id' => 'sessions', 'label' => \esc_html__('Sessions', 'independent-analytics'), 'visible' => \false, 'type' => 'int']), new Column(['id' => 'average_session_duration', 'label' => \esc_html__('Session Duration', 'independent-analytics'), 'visible' => \true, 'type' => 'int', 'filter_placeholder' => 'Seconds']), new Column(['id' => 'views_per_session', 'label' => \esc_html__('Views Per Session', 'independent-analytics'), 'visible' => \false, 'type' => 'int']), new Column(['id' => 'bounce_rate', 'label' => \esc_html__('Bounce Rate', 'independent-analytics'), 'visible' => \true, 'type' => 'int']), new Column(['id' => 'visitors_growth', 'label' => \esc_html__('Visitors Growth', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'exportable' => \false]), new Column(['id' => 'views_growth', 'label' => \esc_html__('Views Growth', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'exportable' => \false]), new Column(['id' => 'wc_orders', 'label' => \esc_html__('Orders', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'requires_woocommerce' => \true]), new Column(['id' => 'wc_gross_sales', 'label' => \esc_html__('Gross Sales', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'requires_woocommerce' => \true]), new Column(['id' => 'wc_refunds', 'label' => \esc_html__('Refunds', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'requires_woocommerce' => \true]), new Column(['id' => 'wc_refunded_amount', 'label' => \esc_html__('Refunded Amount', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'requires_woocommerce' => \true]), new Column(['id' => 'wc_net_sales', 'label' => \esc_html__('Net Sales', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'requires_woocommerce' => \true]), new Column(['id' => 'wc_conversion_rate', 'label' => \esc_html__('Conversion Rate', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'requires_woocommerce' => \true]), new Column(['id' => 'wc_earnings_per_visitor', 'label' => \esc_html__('Earnings Per Visitor', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'requires_woocommerce' => \true]), new Column(['id' => 'wc_average_order_volume', 'label' => \esc_html__('Average Order Volume', 'independent-analytics'), 'visible' => \false, 'type' => 'int', 'requires_woocommerce' => \true])];
    }
}
