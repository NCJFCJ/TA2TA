<?php

namespace IAWP;

use IAWP\Date_Range\Date_Range;
use IAWP\Interval\Interval;
/** @internal */
class Visitors_Over_Time_Finder
{
    /**
     * @var Date_Range
     */
    private $date_range;
    private $interval;
    /**
     * @param Date_Range $date_range Range to fetch referrers for
     */
    public function __construct(Date_Range $date_range, Interval $interval)
    {
        $this->date_range = $date_range;
        $this->interval = $interval;
    }
    public function fetch()
    {
        $rows = $this->interval->fetch($this->date_range);
        return $this->rows_to_class($rows);
    }
    private function rows_to_class(array $rows) : object
    {
        $date_interval = $this->interval->get_date_interval();
        $date_period = new \DatePeriod($this->date_range->start(), $date_interval, $this->date_range->end());
        $interval_data = [];
        $visitors_data = [];
        $views_data = [];
        foreach ($date_period as $index => $date) {
            $current_interval = $index;
            $current_visitors = 0;
            $current_views = 0;
            foreach ($rows as $row) {
                $row_interval = \intval($row->interval_ago);
                $row_visitors = \intval($row->visitors);
                $row_views = \intval($row->views);
                if ($row_interval === $index) {
                    $current_interval = $row_interval;
                    $current_visitors = $row_visitors;
                    $current_views = $row_views;
                    break;
                }
            }
            $interval_data[] = $current_interval;
            $visitors_data[] = $current_visitors;
            $views_data[] = $current_views;
        }
        return (object) ['visitors' => $visitors_data, 'views' => $views_data, 'interval_labels_short' => $this->interval->get_short_labels($interval_data), 'interval_labels_full' => $this->interval->get_full_labels($interval_data)];
    }
}
