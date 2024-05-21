<?php

namespace IAWP\Models;

/** @internal */
class Campaign
{
    use \IAWP\Models\View_Stats;
    use \IAWP\Models\WooCommerce_Stats;
    private $title;
    private $utm_source;
    private $utm_medium;
    private $utm_campaign;
    private $utm_term;
    private $utm_content;
    public function __construct($row)
    {
        $this->title = $row->title;
        $this->utm_source = $row->utm_source;
        $this->utm_medium = $row->utm_medium;
        $this->utm_campaign = $row->utm_campaign;
        $this->utm_term = $row->utm_term;
        $this->utm_content = $row->utm_content;
        $this->set_view_stats($row);
        $this->set_wc_stats($row);
    }
    /*
     * Column names have shared logic between tables. So "title" for resources has the same logic
     * as "title" for campaigns. Adding is_deleted ensures that the method can be called even though
     * campaigns can never be deleted. A better code base would allow this to be removed.
     */
    public function is_deleted()
    {
        return \false;
    }
    public function title()
    {
        return $this->title;
    }
    public function utm_source()
    {
        return $this->utm_source;
    }
    public function utm_medium()
    {
        return $this->utm_medium;
    }
    public function utm_campaign()
    {
        return $this->utm_campaign;
    }
    public function utm_term()
    {
        return $this->utm_term;
    }
    public function utm_content()
    {
        return $this->utm_content;
    }
    /**
     * This isn't building a URL param that's used in a URL. This is building a unique id that's
     * used for uniqueness in real-times most popular campaign list.
     *
     * @return string
     */
    public function params() : string
    {
        return \http_build_query(['title' => $this->title(), 'utm_source' => $this->utm_source(), 'utm_medium' => $this->utm_medium(), 'utm_campaign' => $this->utm_campaign(), 'utm_term' => $this->utm_term(), 'utm_content' => $this->utm_content()]);
    }
}
