<?php

namespace IAWP;

use IAWP\Models\Geo;
/** @internal */
class Chart_Geo
{
    private $countries;
    private $title;
    /**
     * @param Geo[] $geos
     * @param $title
     */
    public function __construct(array $geos, $title = null)
    {
        $this->countries = $this->parse($geos);
        $this->title = $title;
    }
    public function get_html($unused_visible_datasets = null)
    {
        $chart_data = \array_map(function ($country) {
            return [$country['country_code'], $country['views'], $this->get_tooltip($country)];
        }, $this->countries);
        $dark_mode = \IAWPSCOPED\iawp()->get_option('iawp_dark_mode', '0');
        \ob_start();
        ?>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <div class="chart-container">
            <div class="chart-inner">
                <div class="legend-container">
                    <h2 class="legend-title"><?php 
        echo $this->title;
        ?></h2>
                </div>
                <div id="myChart"
                     data-controller="chart-geo"
                     data-chart-geo-data-value="<?php 
        echo \esc_attr(\json_encode($chart_data));
        ?>"
                     data-chart-geo-dark-mode-value="<?php 
        echo \esc_attr($dark_mode);
        ?>">
                    <div data-chart-geo-target="chart"></div>
                </div>
            </div>
        </div><?php 
        $html = \ob_get_contents();
        \ob_end_clean();
        return $html;
    }
    private function parse($geos) : array
    {
        $countries = [];
        foreach ($geos as $geo) {
            $existing_country_index = null;
            foreach ($countries as $index => $country) {
                if ($geo->country_code() === $country['country_code']) {
                    $existing_country_index = $index;
                }
            }
            if (\is_numeric($existing_country_index)) {
                $countries[$existing_country_index]['views'] += $geo->views();
                $countries[$existing_country_index]['visitors'] += $geo->visitors();
                $countries[$existing_country_index]['sessions'] += $geo->sessions();
            } else {
                $countries[] = ['country_code' => $geo->country_code(), 'country' => $geo->country(), 'views' => $geo->views(), 'visitors' => $geo->visitors(), 'sessions' => $geo->sessions()];
            }
        }
        return $countries;
    }
    private function get_tooltip($country) : string
    {
        \ob_start();
        ?>
        <div class="iawp-geo-chart-tooltip">
            <?php 
        echo \IAWP\Icon_Directory_Factory::flags()->find($country['country_code']);
        ?>
            <h1><?php 
        echo $country['country'];
        ?></h1>
            <p><span><?php 
        \_e('Views');
        ?>: </span> <?php 
        echo \number_format_i18n($country['views']);
        ?></p>
            <p><span><?php 
        \_e('Visitors');
        ?>: </span><?php 
        echo \number_format_i18n($country['visitors']);
        ?> </p>
            <p><span><?php 
        \_e('Sessions');
        ?>: </span><?php 
        echo \number_format_i18n($country['sessions']);
        ?> </p>
        </div>
        <?php 
        $html = \ob_get_contents();
        \ob_end_clean();
        return $html;
    }
}
