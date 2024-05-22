<?php

namespace IAWP;

use IAWP\Statistics\Page_Statistics;
use IAWPSCOPED\Proper\Number;
/** @internal */
class Email_Chart
{
    public $views;
    public $most_views;
    public $y_labels;
    public $x_labels;
    private $statistics;
    public function __construct(Page_Statistics $statistics)
    {
        $this->statistics = $statistics;
        $this->views = self::views();
        $this->most_views = self::most_views();
        $this->y_labels = self::y_labels();
        $this->x_labels = self::x_labels();
    }
    public function views()
    {
        $interval = \IAWPSCOPED\iawp()->get_option('iawp_email_report_interval', 'monthly');
        return \array_map(function ($day) {
            return $day[1];
        }, $this->statistics->views()->daily_summary());
    }
    public function most_views()
    {
        return \round(\max($this->views) * 1.1);
    }
    public function y_labels()
    {
        return [Number::abbreviate($this->most_views), Number::abbreviate($this->most_views / 2), 0];
    }
    public function x_labels()
    {
        $interval = \IAWPSCOPED\iawp()->get_option('iawp_email_report_interval', 'monthly');
        $format = 'M j';
        if ($interval == 'weekly') {
            $format = 'D';
        } elseif ($interval == 'daily') {
            $format = \get_option('time_format', 'g:i a');
        }
        $all_x_labels = \array_map(function ($day) use($format) {
            return $day[0]->format($format);
        }, $this->statistics->views()->daily_summary());
        $x_labels = [];
        if ($interval == 'monthly') {
            for ($x = 0; $x < \count($all_x_labels); $x++) {
                if (($x + 5) % 5 == 0) {
                    $x_labels[] = $all_x_labels[$x];
                }
            }
        } elseif ($interval == 'weekly') {
            $x_labels = $all_x_labels;
        } elseif ($interval == 'daily') {
            for ($x = 0; $x < \count($all_x_labels); $x++) {
                if (($x + 6) % 6 == 0) {
                    $x_labels[] = $all_x_labels[$x];
                }
            }
        }
        return $x_labels;
    }
}
