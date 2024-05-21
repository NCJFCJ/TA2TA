<?php

namespace IAWP;

use IAWP\Statistics\Intervals\Intervals;
use IAWP\Statistics\Statistics;
use IAWP\Utils\Security;
/** @internal */
class Chart
{
    private $statistics;
    private $title;
    private $preview;
    public function __construct(Statistics $statistics, ?string $title, bool $preview = \false)
    {
        $this->preview = $preview;
        $this->statistics = $statistics;
        $this->title = $title;
    }
    /**
     * @param string[] $visible_datasets
     *
     * @return false|string
     */
    public function get_html(array $visible_datasets = ['visitors', 'views'])
    {
        $labels = \array_map(function ($data_point) {
            return Security::json_encode($this->statistics->chart_interval()->get_label_for($data_point[0]));
        }, $this->statistics->views()->daily_summary());
        $views_data = \array_map(function ($data_point) {
            return $data_point[1];
        }, $this->statistics->views()->daily_summary());
        $visitors_data = \array_map(function ($data_point) {
            return $data_point[1];
        }, $this->statistics->visitors()->daily_summary());
        $sessions_data = \array_map(function ($data_point) {
            return $data_point[1];
        }, $this->statistics->sessions()->daily_summary());
        $woocommerce_orders_data = \array_map(function ($data_point) {
            return $data_point[1];
        }, $this->statistics->woocommerce_orders()->daily_summary());
        $woocommerce_net_sales_data = \array_map(function ($data_point) {
            return $data_point[1];
        }, $this->statistics->woocommerce_net_sales()->daily_summary());
        return $this->chart_html($labels, $views_data, $visitors_data, $sessions_data, $woocommerce_orders_data, $woocommerce_net_sales_data, $visible_datasets);
    }
    private function chart_html(array $labels, array $views, array $visitors, array $sessions, array $woocommerce_orders_data, array $woocommerce_net_sales_data, array $visible_datasets)
    {
        \ob_start();
        ?>
        <div class="chart-container">
        <div class="chart-inner">
            <div class="legend-container">
                <h2 class="legend-title"><?php 
        echo \esc_html($this->title);
        ?></h2>
                <div class="legend"></div>
                <?php 
        if ($this->is_full_view()) {
            ?>
                    <select id="chart-interval-select" class="chart-interval-select" data-controller="chart-interval" data-action="chart-interval#setChartInterval">
                        <?php 
            foreach (Intervals::all() as $interval) {
                ?>
                            <option
                                    value="<?php 
                echo \esc_attr($interval->id());
                ?>"
                                    <?php 
                \selected($interval->equals($this->statistics->chart_interval()));
                ?>
                            ><?php 
                echo \esc_html($interval->label());
                ?></option>
                        <?php 
            }
            ?>
                    </select>
                <?php 
        }
        ?>
            </div>
            <canvas id="myChart"
                    data-testid="chart"
                    width="800"
                    height="200"
                    data-controller="chart"
                    data-chart-locale-value="<?php 
        echo \get_bloginfo('language');
        ?>"
                    data-chart-preview-value='<?php 
        echo $this->is_preview() ? '1' : '0';
        ?>'
                    data-chart-using-woo-commerce-value='<?php 
        echo \IAWPSCOPED\iawp_using_woocommerce() ? '1' : '0';
        ?>'
                    data-chart-labels-value='<?php 
        echo Security::json_encode($labels);
        ?>'
                    data-chart-views-value='<?php 
        echo Security::json_encode($views);
        ?>'
                    data-chart-visitors-value='<?php 
        echo Security::json_encode($visitors);
        ?>'
                    data-chart-visible-datasets-value='<?php 
        echo Security::json_encode($visible_datasets);
        ?>'
                <?php 
        if ($this->is_full_view()) {
            ?>
                    data-chart-sessions-value='<?php 
            echo Security::json_encode($sessions);
            ?>'
                <?php 
        }
        ?>
                <?php 
        if ($this->is_full_view() && \IAWPSCOPED\iawp_using_woocommerce()) {
            ?>
                    data-chart-currency-value="<?php 
            echo get_woocommerce_currency();
            ?>"
                    data-chart-woocommerce-orders-value='<?php 
            echo Security::json_encode($woocommerce_orders_data);
            ?>'
                    data-chart-woocommerce-net-sales-value='<?php 
            echo Security::json_encode($woocommerce_net_sales_data);
            ?>'
                <?php 
        }
        ?>
            >
            </canvas>
        </div>
        </div><?php 
        $html = \ob_get_contents();
        \ob_end_clean();
        return $html;
    }
    /**
     * @return bool
     */
    private function is_preview() : bool
    {
        return $this->preview;
    }
    /**
     * @return bool
     */
    private function is_full_view() : bool
    {
        return !$this->is_preview();
    }
}
