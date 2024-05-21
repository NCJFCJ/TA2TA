<?php

namespace IAWP\Models;

/** @internal */
class Page_Date_Archive extends \IAWP\Models\Page
{
    private $date_archive;
    public function __construct($row)
    {
        $this->date_archive = $row->date_archive;
        parent::__construct($row);
    }
    protected function resource_key() : string
    {
        return 'date_archive';
    }
    protected function resource_value() : string
    {
        return $this->date_archive;
    }
    protected function calculate_is_deleted() : bool
    {
        return \false;
    }
    protected function calculate_url()
    {
        list($type, $year, $month, $day) = $this->date_archive_type();
        if ($type == 'year') {
            return \get_year_link($year);
        } elseif ($type == 'month') {
            return \get_month_link($year, $month);
        } else {
            return \get_day_link($year, $month, $day);
        }
    }
    protected function calculate_title()
    {
        return $this->date_archive;
    }
    protected function calculate_type()
    {
        return 'date-archive';
    }
    protected function calculate_type_label()
    {
        list($type) = $this->date_archive_type();
        if ($type == 'year') {
            return \esc_html__('Date Archive (Year)', 'independent-analytics');
        } elseif ($type == 'month') {
            return \esc_html__('Date Archive (Month)', 'independent-analytics');
        } else {
            return \esc_html__('Date Archive (Day)', 'independent-analytics');
        }
    }
    protected function calculate_icon()
    {
        return '<span class="dashicons dashicons-calendar-alt"></span>';
    }
    protected function calculate_author_id()
    {
        return null;
    }
    protected function calculate_author()
    {
        return null;
    }
    protected function calculate_avatar()
    {
        return null;
    }
    protected function calculate_date()
    {
        return null;
    }
    protected function calculate_category()
    {
        return [];
    }
    private function date_archive_type()
    {
        list($year, $month, $day) = \array_pad(\explode('-', $this->date_archive), 3, null);
        if (\is_null($day) && \is_null($month)) {
            return ['year', $year, null, null];
        } elseif (\is_null($day)) {
            return ['month', $year, $month, null];
        } else {
            return ['day', $year, $month, $day];
        }
    }
}
