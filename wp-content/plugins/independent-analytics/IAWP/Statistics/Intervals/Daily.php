<?php

namespace IAWP\Statistics\Intervals;

/** @internal */
class Daily extends \IAWP\Statistics\Intervals\Interval
{
    public function id() : string
    {
        return 'daily';
    }
    public function label() : string
    {
        return \__('Daily', 'independent-analytics');
    }
    public function date_interval() : \DateInterval
    {
        return new \DateInterval('P1D');
    }
    public function calculate_start_of_interval_for(\DateTime $original_date_time) : \DateTime
    {
        $date_time = clone $original_date_time;
        $date_time->setTime(0, 0, 0);
        return $date_time;
    }
    public function get_label_for(\DateTime $date_time) : array
    {
        return ['tick' => $this->format($date_time, 'M j'), 'tooltipLabel' => $this->format($date_time, 'F jS (l)')];
    }
}
