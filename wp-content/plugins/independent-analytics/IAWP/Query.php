<?php

namespace IAWP;

/** @internal */
class Query
{
    public const CAMPAIGN_URLS = 'campaign_urls';
    public const CAMPAIGNS = 'campaigns';
    public const REFERRER_GROUPS = 'referrer_groups';
    public const REFERRERS = 'referrers';
    public const RESOURCES = 'resources';
    public const VIEWS = 'views';
    public const VISITORS = 'visitors';
    public const VISITORS_TMP = 'visitors_tmp';
    // Used in DB v7 migration
    public const VISITORS_1_16_ARCHIVE = 'visitors_1_16_archive';
    // Used in DB v7 migration
    public const SESSIONS = 'sessions';
    public const WC_ORDERS = 'wc_orders';
    public const CITIES = 'cities';
    public const COUNTRIES = 'countries';
    public const DEVICES = 'devices';
    public const DEVICE_TYPES = 'device_types';
    public const DEVICE_OSS = 'device_oss';
    public const DEVICE_BROWSERS = 'device_browsers';
    public const REPORTS = 'reports';
    /**
     * Safe way to get the name of a table
     *
     * @param string $name
     *
     * @return string|null
     */
    public static function get_table_name(string $name) : ?string
    {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $reflection = new \ReflectionClass(static::class);
        $constants = $reflection->getConstants();
        if (\in_array($name, $constants)) {
            return $prefix . 'independent_analytics_' . $name;
        } else {
            return null;
        }
    }
}
