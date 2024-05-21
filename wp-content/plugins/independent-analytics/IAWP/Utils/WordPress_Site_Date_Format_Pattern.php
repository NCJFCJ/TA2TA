<?php

namespace IAWP\Utils;

/** @internal */
class WordPress_Site_Date_Format_Pattern
{
    public static function for_php() : string
    {
        return \get_option('date_format');
    }
    public static function for_javascript() : string
    {
        $php_format = self::for_php();
        $replacements = [
            'd' => 'DD',
            'D' => 'ddd',
            'j' => 'D',
            'l' => 'dddd',
            'N' => 'E',
            'S' => 'o',
            'w' => 'e',
            'z' => 'DDD',
            'W' => 'W',
            'F' => 'MMMM',
            'm' => 'MM',
            'M' => 'MMM',
            'n' => 'M',
            't' => '',
            // no equivalent
            'L' => '',
            // no equivalent
            'o' => 'YYYY',
            'Y' => 'YYYY',
            'y' => 'YY',
            'a' => 'a',
            'A' => 'A',
            'B' => '',
            // no equivalent
            'g' => 'h',
            'G' => 'H',
            'h' => 'hh',
            'H' => 'HH',
            'i' => 'mm',
            's' => 'ss',
            'u' => 'SSS',
            'e' => 'zz',
            // deprecated since version 1.6.0 of moment.js
            'I' => '',
            // no equivalent
            'O' => '',
            // no equivalent
            'P' => '',
            // no equivalent
            'T' => '',
            // no equivalent
            'Z' => '',
            // no equivalent
            'c' => '',
            // no equivalent
            'r' => '',
            // no equivalent
            'U' => 'X',
        ];
        return \strtr($php_format, $replacements);
    }
}
