<?php

namespace IAWP\Statistics\Intervals;

/** @internal */
class Hourly extends \IAWP\Statistics\Intervals\Interval
{
    public function id() : string
    {
        return 'hourly';
    }
    public function label() : string
    {
        return \__('Hourly', 'independent-analytics');
    }
    public function date_interval() : \DateInterval
    {
        return new \DateInterval('PT1H');
    }
    public function calculate_start_of_interval_for(\DateTime $original_date_time) : \DateTime
    {
        $date_time = clone $original_date_time;
        $date_time->setTime(\intval($date_time->format('G')), 0, 0);
        return $date_time;
    }
    public function get_label_for(\DateTime $date_time) : array
    {
        $date_format = 'F jS';
        $time_format = \get_option('time_format', 'g:i a');
        $in_one_hour = (clone $date_time)->add(new \DateInterval('PT1H'));
        return ['tick' => $this->format($date_time, $time_format), 'tooltipLabel' => $this->format($date_time, $time_format) . " - " . $this->format($in_one_hour, $time_format) . $this->format($date_time, ' (' . $date_format . ')')];
    }
}
