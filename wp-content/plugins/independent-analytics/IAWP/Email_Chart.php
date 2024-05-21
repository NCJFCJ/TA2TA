<?php

namespace IAWP;

use IAWP\Statistics\Page_Statistics;
use IAWPSCOPED\Proper\Number;
/** @internal */
class Email_Chart
{
    public $daily_views;
    public $most_views;
    public $y_labels;
    public $x_labels;
    private $statistics;
    public function __construct(Page_Statistics $statistics)
    {
        $this->statistics = $statistics;
        $this->daily_views = self::daily_views();
        $this->most_views = self::most_views();
        $this->y_labels = self::y_labels();
        $this->x_labels = self::x_labels();
    }
    public function daily_views()
    {
        return \array_map(function ($day) {
            return $day[1];
        }, $this->statistics->views()->daily_summary());
    }
    public function most_views()
    {
        return \round(\max($this->daily_views) * 1.1);
    }
    public function y_labels()
    {
        return [Number::abbreviate($this->most_views), Number::abbreviate($this->most_views / 2), 0];
    }
    public function x_labels()
    {
        $all_x_labels = \array_map(function ($day) {
            return $day[0]->format('M j');
        }, $this->statistics->views()->daily_summary());
        $x_labels = [];
        for ($x = 0; $x < \count($all_x_labels); $x++) {
            if (($x + 5) % 5 == 0) {
                $x_labels[] = $all_x_labels[$x];
            }
        }
        return $x_labels;
    }
}
