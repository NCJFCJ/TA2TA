<?php

namespace IAWP;

/**
 * Reads a list of icons from a directory allowing them to be searched. Supports defaults.
 * @internal
 */
class Icon_Directory_Factory
{
    private static $flags = null;
    private static $device_types = null;
    private static $operating_systems = null;
    private static $browser = null;
    private function __construct()
    {
        // Cannot be initialized
    }
    public static function flags() : \IAWP\Icon_Directory
    {
        if (\is_null(self::$flags)) {
            self::$flags = new \IAWP\Icon_Directory('img/flags/', 'Country flag');
        }
        return self::$flags;
    }
    public static function device_types() : \IAWP\Icon_Directory
    {
        if (\is_null(self::$device_types)) {
            self::$device_types = new \IAWP\Icon_Directory('img/device-types/', 'Device type icon');
        }
        return self::$device_types;
    }
    public static function operating_systems() : \IAWP\Icon_Directory
    {
        if (\is_null(self::$operating_systems)) {
            self::$operating_systems = new \IAWP\Icon_Directory('img/operating-systems/', 'Operating system icon');
        }
        return self::$operating_systems;
    }
    public static function browsers() : \IAWP\Icon_Directory
    {
        if (\is_null(self::$browser)) {
            self::$browser = new \IAWP\Icon_Directory('img/browsers/', 'Browser icon');
        }
        return self::$browser;
    }
}
