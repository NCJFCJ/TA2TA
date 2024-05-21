<?php

namespace IAWP\Statistics\Intervals;

/** @internal */
class Weekly extends \IAWP\Statistics\Intervals\Interval
{
    public function id() : string
    {
        return 'weekly';
    }
    public function label() : string
    {
        return \__('Weekly', 'independent-analytics');
    }
    public function date_interval() : \DateInterval
    {
        return new \DateInterval('P7D');
    }
    public function calculate_start_of_interval_for(\DateTime $original_date_time) : \DateTime
    {
        $date_time = clone $original_date_time;
        $date_time->setTime(0, 0, 0);
        $start_of_week = \intval(\get_option('iawp_dow', 0));
        $day_of_week = \intval($date_time->format('w'));
        $days_to_subtract = $day_of_week - $start_of_week;
        if ($days_to_subtract < 0) {
            $days_to_subtract = 7 - \abs($days_to_subtract);
        }
        $interval_to_subtract = new \DateInterval('P' . $days_to_subtract . "D");
        $date_time->sub($interval_to_subtract);
        return $date_time;
    }
    public function get_label_for(\DateTime $date_time) : array
    {
        $in_six_days = (clone $date_time)->add(new \DateInterval('P6D'));
        return ['tick' => $this->format($date_time, 'M j'), 'tooltipLabel' => $this->format($date_time, 'F jS') . ' - ' . $this->format($in_six_days, 'F jS')];
    }
}
