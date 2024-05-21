<?php

namespace IAWP\Models;

/** @internal */
class Current_Traffic
{
    private $visitor_count;
    private $page_count;
    private $referrer_count;
    private $country_count;
    private $campaign_count;
    private $view_count;
    public function __construct($row)
    {
        $this->visitor_count = \intval($row->visitor_count);
        $this->page_count = \intval($row->page_count);
        $this->referrer_count = \intval($row->referrer_count);
        $this->country_count = \intval($row->country_count);
        $this->campaign_count = \intval($row->campaign_count);
        $this->view_count = \intval($row->view_count);
    }
    public function get_visitor_count()
    {
        return $this->visitor_count;
    }
    public function get_page_count()
    {
        return $this->page_count;
    }
    public function get_referrer_count()
    {
        return $this->referrer_count;
    }
    public function get_country_count()
    {
        return $this->country_count;
    }
    public function get_campaign_count()
    {
        return $this->campaign_count;
    }
    public function get_view_count()
    {
        return $this->view_count;
    }
}
