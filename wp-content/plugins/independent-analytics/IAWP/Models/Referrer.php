<?php

namespace IAWP\Models;

/** @internal */
class Referrer
{
    use \IAWP\Models\View_Stats;
    use \IAWP\Models\WooCommerce_Stats;
    private $is_direct;
    private $referrer;
    private $domain;
    private $referrer_type;
    public function __construct($row)
    {
        $this->is_direct = $row->domain === '';
        $this->referrer = $row->referrer;
        $this->domain = $row->domain;
        $this->referrer_type = $row->referrer_type;
        $this->set_view_stats($row);
        $this->set_wc_stats($row);
    }
    /**
     * Return group name, referrer url, or direct.
     *
     * @return string Referrer
     */
    // Todo - This is the one the table is doing...
    public function referrer() : string
    {
        return $this->referrer;
    }
    public function referrer_url() : string
    {
        return $this->domain;
    }
    /**
     * Return group referrer type, referrer, or direct.
     *
     * @return string Referrer type
     */
    public function referrer_type() : string
    {
        return $this->referrer_type;
    }
    public function is_direct() : bool
    {
        return $this->is_direct;
    }
    /**
     * Should the referrer record link back to the referrering domain?
     *
     * @return bool
     */
    public function has_link() : bool
    {
        return !$this->is_direct() && $this->referrer_type !== 'Ad';
    }
}
