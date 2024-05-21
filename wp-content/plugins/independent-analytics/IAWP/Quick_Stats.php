<?php

namespace IAWP;

use IAWP\Statistics\Statistics;
use IAWP\Utils\Currency;
use IAWP\Utils\Number_Formatter;
use IAWPSCOPED\Proper\Number;
/** @internal */
class Quick_Stats
{
    private $statistics;
    private $filtered_statistics;
    private $preview;
    /**
     * @param Statistics|null $statistics
     * @param Statistics $unfiltered_statistics
     * @param bool $preview
     */
    public function __construct(?Statistics $statistics, Statistics $unfiltered_statistics, bool $preview = \false)
    {
        $this->preview = $preview;
        if (\is_null($statistics)) {
            $this->statistics = $unfiltered_statistics;
        } else {
            $this->filtered_statistics = $statistics;
            $this->statistics = $unfiltered_statistics;
        }
    }
    public function get_stats()
    {
        $is_filtered = !\is_null($this->filtered_statistics);
        $statistics = $is_filtered ? $this->filtered_statistics : $this->statistics;
        $stats = [['title' => \__('Visitors', 'independent-analytics'), 'class' => 'visitors', 'count' => $this->maybe_abbreviate($statistics->visitors()->value()), 'growth' => $statistics->visitors()->growth(), 'formatted_growth' => $this->format_growth($statistics->visitors()->growth()), 'unfiltered' => $this->maybe_abbreviate($this->statistics->visitors()->value())], ['title' => \__('Views', 'independent-analytics'), 'class' => 'views', 'count' => $this->maybe_abbreviate($statistics->views()->value()), 'growth' => $statistics->views()->growth(), 'formatted_growth' => $this->format_growth($statistics->views()->growth()), 'unfiltered' => $this->maybe_abbreviate($this->statistics->views()->value())]];
        if ($this->is_full_view()) {
            $stats[] = ['title' => \__('Sessions', 'independent-analytics'), 'class' => 'sessions', 'count' => $this->maybe_abbreviate($statistics->sessions()->value()), 'growth' => $statistics->sessions()->growth(), 'formatted_growth' => $this->format_growth($statistics->sessions()->growth()), 'unfiltered' => $this->maybe_abbreviate($this->statistics->sessions()->value())];
            $stats[] = ['title' => \__('Average Session Duration', 'independent-analytics'), 'class' => 'average-session-duration', 'count' => Number_Formatter::second_to_minute_timestamp($statistics->average_session_duration()->value()), 'growth' => $statistics->average_session_duration()->growth(), 'formatted_growth' => $this->format_growth($statistics->average_session_duration()->growth()), 'unfiltered' => Number_Formatter::second_to_minute_timestamp($this->statistics->average_session_duration()->value())];
            $stats[] = ['title' => \__('Bounce Rate', 'independent-analytics'), 'class' => 'bounce-rate', 'count' => Number_Formatter::percent($statistics->bounce_rate()->value()), 'growth' => $statistics->bounce_rate()->growth(), 'formatted_growth' => $this->format_growth($statistics->bounce_rate()->growth()), 'unfiltered' => Number_Formatter::percent($this->statistics->bounce_rate()->value())];
            $stats[] = ['title' => \__('Views Per Session', 'independent-analytics'), 'class' => 'views-per-session', 'count' => Number_Formatter::decimal($statistics->view_per_session()->value(), 2), 'growth' => $statistics->view_per_session()->growth(), 'formatted_growth' => $this->format_growth($statistics->view_per_session()->growth()), 'unfiltered' => Number_Formatter::decimal($this->statistics->view_per_session()->value(), 2)];
        }
        if ($this->is_full_view() && \IAWPSCOPED\iawp_using_woocommerce()) {
            $stats[] = ['title' => \__('Orders', 'independent-analytics'), 'class' => 'orders', 'count' => $this->maybe_abbreviate($statistics->woocommerce_orders()->value()), 'growth' => $statistics->woocommerce_orders()->growth(), 'formatted_growth' => $this->format_growth($statistics->woocommerce_orders()->growth()), 'unfiltered' => $this->maybe_abbreviate($this->statistics->woocommerce_orders()->value())];
            $stats[] = ['title' => \__('Net Sales', 'independent-analytics'), 'class' => 'net-sales', 'count' => Currency::format($statistics->woocommerce_net_sales()->value(), \true, \false), 'growth' => $statistics->woocommerce_net_sales()->growth(), 'formatted_growth' => $this->format_growth($statistics->woocommerce_net_sales()->growth()), 'unfiltered' => Currency::format($this->statistics->woocommerce_net_sales()->value(), \true, \false)];
        }
        return $stats;
    }
    public function get_html()
    {
        $stats = $this->get_stats();
        $is_filtered = !\is_null($this->filtered_statistics);
        return \IAWPSCOPED\iawp_blade()->run('quick-stats', ['is_filtered' => $is_filtered, 'stats' => $stats]);
    }
    private function format_growth($growth) : string
    {
        return Number_Formatter::percent(\absint($growth));
    }
    /**
     * @param int|float $number
     *
     * @return string
     */
    private function maybe_abbreviate($number) : string
    {
        if ($number < 100000) {
            return \number_format_i18n($number, 0);
        }
        return Number::abbreviate($number, \false);
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
