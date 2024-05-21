<?php

namespace IAWP\Date_Range;

use DateTime;
use IAWPSCOPED\Proper\Timezone;
/** @internal */
abstract class Date_Range
{
    /**
     * @var DateTime
     */
    protected $start;
    /**
     * @var DateTime
     */
    protected $end;
    public abstract function label() : string;
    /**
     * @return DateTime
     */
    public function start() : DateTime
    {
        return $this->start;
    }
    /**
     * @return string
     */
    public function iso_start() : string
    {
        return $this->start->format('Y-m-d\\TH:i:s');
    }
    /**
     * @return DateTime
     */
    public function end() : DateTime
    {
        return $this->end;
    }
    /**
     * @return string
     */
    public function iso_end() : string
    {
        return $this->end->format('Y-m-d\\TH:i:s');
    }
    public function previous_period() : \IAWP\Date_Range\Date_Range
    {
        $range_size = $this->range_size_in_days();
        $previous_start = (clone $this->start)->modify("-{$range_size} days");
        $previous_end = (clone $this->end)->modify("-{$range_size} days");
        return new \IAWP\Date_Range\Exact_Date_Range($previous_start, $previous_end, \false);
    }
    public function number_of_days() : int
    {
        return \intval($this->start->diff($this->end)->format('%a')) + 1;
    }
    protected function set_range(DateTime $start, DateTime $end, bool $convert_to_full_days)
    {
        $start = clone $start;
        $end = clone $end;
        if ($convert_to_full_days) {
            $start = $this->start_of_locale_day($start);
            $end = $this->end_of_locale_day($end);
        }
        $this->start = $start;
        $this->end = $end;
    }
    /**
     * Return the range size in days for previous period calculations
     *
     * @return int
     */
    private function range_size_in_days() : int
    {
        return $this->start->diff($this->end)->days + 1;
    }
    /**
     * Return a new DateTime representing the start of the day in the users timezone
     *
     * @param DateTime $datetime
     *
     * @return DateTime
     */
    private function start_of_locale_day(\DateTime $datetime) : \DateTime
    {
        $datetime = clone $datetime;
        return $datetime->setTimezone(Timezone::site_timezone())->setTime(0, 0, 0)->setTimezone(Timezone::utc_timezone());
    }
    /**
     * Return a new DateTime representing the end of the day in the users timezone
     *
     * @param DateTime $datetime
     *
     * @return DateTime
     */
    private function end_of_locale_day(\DateTime $datetime) : \DateTime
    {
        $datetime = clone $datetime;
        return $datetime->setTimezone(Timezone::site_timezone())->setTime(23, 59, 59)->setTimezone(Timezone::utc_timezone());
    }
}
