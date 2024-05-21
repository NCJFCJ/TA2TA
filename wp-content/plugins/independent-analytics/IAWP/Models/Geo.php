<?php

namespace IAWP\Models;

/** @internal */
class Geo
{
    use \IAWP\Models\View_Stats;
    use \IAWP\Models\WooCommerce_Stats;
    private $continent;
    private $country;
    private $country_code;
    private $subdivision;
    private $city;
    public function __construct($row)
    {
        $this->continent = $row->continent;
        $this->country = $row->country;
        $this->country_code = $row->country_code;
        $this->subdivision = $row->subdivision ?? '';
        $this->city = $row->city ?? '';
        $this->set_view_stats($row);
        $this->set_wc_stats($row);
    }
    public function continent()
    {
        return $this->continent;
    }
    public function country()
    {
        return $this->country;
    }
    public function country_code()
    {
        return $this->country_code;
    }
    public function subdivision()
    {
        return $this->subdivision;
    }
    public function city()
    {
        return $this->city;
    }
}
