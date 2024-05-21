<?php

namespace IAWP\Statistics\Intervals;

/** @internal */
class Monthly extends \IAWP\Statistics\Intervals\Interval
{
    public function id() : string
    {
        return 'monthly';
    }
    public function label() : string
    {
        return \__('Monthly', 'independent-analytics');
    }
    public function date_interval() : \DateInterval
    {
        return new \DateInterval('P01M');
    }
    public function calculate_start_of_interval_for(\DateTime $original_date_time) : \DateTime
    {
        $date_time = clone $original_date_time;
        $date_time->setDate(\intval($date_time->format('Y')), \intval($date_time->format('m')), 1);
        $date_time->setTime(0, 0, 0);
        return $date_time;
    }
    public function get_label_for(\DateTime $date_time) : array
    {
        return ['tick' => $this->format($date_time, 'F'), 'tooltipLabel' => $this->format($date_time, 'F')];
    }
}
