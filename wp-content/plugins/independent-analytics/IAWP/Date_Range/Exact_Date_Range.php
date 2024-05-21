<?php

namespace IAWP\Date_Range;

use DateTime;
use IAWP\Utils\WordPress_Site_Date_Format_Pattern;
use IAWPSCOPED\Proper\Timezone;
/** @internal */
class Exact_Date_Range extends \IAWP\Date_Range\Date_Range
{
    /**
     * Generate a range using exact start and end dates
     *
     * @param DateTime $start
     * @param DateTime $end
     * @param bool $convert_to_full_days
     */
    public function __construct(DateTime $start, DateTime $end, bool $convert_to_full_days = \true)
    {
        $this->set_range($start, $end, $convert_to_full_days);
    }
    /**
     * Get a formatted label for the range
     *
     * @return string
     */
    public function label() : string
    {
        $formatted_start = $this->start->setTimezone(Timezone::site_timezone())->format(WordPress_Site_Date_Format_Pattern::for_php());
        $formatted_end = $this->end->setTimezone(Timezone::site_timezone())->format(WordPress_Site_Date_Format_Pattern::for_php());
        return $formatted_start . ' - ' . $formatted_end;
    }
    /**
     * Get a range that covers the entirety of the plugins lifetime
     *
     * @return Exact_Date_Range
     */
    public static function comprehensive_range() : \IAWP\Date_Range\Exact_Date_Range
    {
        return new \IAWP\Date_Range\Exact_Date_Range(new DateTime('1991-01-06'), new DateTime());
    }
}
