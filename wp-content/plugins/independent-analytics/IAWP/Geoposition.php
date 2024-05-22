<?php

namespace IAWP;

use IAWPSCOPED\MaxMind\Db\Reader;
/**
 * Give an ip address, get the ip addresses' city, subdivision, country, continent, and country_code
 * @internal
 */
class Geoposition
{
    private $geoposition;
    public function __construct(string $ip)
    {
        $this->geoposition = $this->fetch_geoposition($ip);
    }
    public function valid_location() : bool
    {
        $required_values = [$this->continent(), $this->country_code(), $this->country(), $this->city()];
        $null_values = \array_filter($required_values, function ($item) {
            return \is_null($item);
        });
        return \count($null_values) === 0;
    }
    public function country_code() : ?string
    {
        return $this->geoposition['country']['iso_code'] ?? null;
    }
    /**
     * Return an English city name
     *
     * @return string|null
     */
    public function city() : ?string
    {
        $city = $this->geoposition['city']['names']['en'] ?? null;
        if (\is_null($city)) {
            return null;
        }
        return $this->strip_city_neighborhood_data($city);
    }
    /**
     * Return an English subdivision name
     *
     * @return string|null
     */
    public function subdivision() : ?string
    {
        return $this->geoposition['subdivisions'][0]['names']['en'] ?? null;
    }
    /**
     * @return string|null
     */
    public function country() : ?string
    {
        return $this->geoposition['country']['names']['en'] ?? null;
    }
    /**
     * @return string|null
     */
    public function continent() : ?string
    {
        return $this->geoposition['continent']['names']['en'] ?? null;
    }
    /**
     * City names can occasionally contain neighborhood data in parentheses after the city name such
     * as "Philadelphia (North Philadelphia)" in of "Philadelphia". This strips that extra
     * neighborhood data, returning just the city name.
     *
     * @param $city_name
     *
     * @return string
     */
    private function strip_city_neighborhood_data($city_name) : string
    {
        $position = \strpos($city_name, '(');
        if ($position !== \false) {
            $city_name = \substr($city_name, 0, $position);
        }
        return \trim($city_name);
    }
    private function fetch_geoposition(string $ip) : array
    {
        try {
            $reader = new Reader(\IAWP\Geo_Database_Manager::path_to_database());
            $geo = $reader->get($ip);
            $reader->close();
            return $geo;
        } catch (\Throwable $e) {
            return [];
        }
    }
}
