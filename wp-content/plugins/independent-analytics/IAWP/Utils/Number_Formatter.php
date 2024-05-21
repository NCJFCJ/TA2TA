<?php

namespace IAWP\Utils;

use DateTime;
/** @internal */
class Number_Formatter
{
    /**
     * Pass in 90 and get back 1:30. Pass in 121 and get back 2:01.
     *
     * @param int $seconds
     *
     * @return string
     */
    public static function second_to_minute_timestamp(int $seconds) : string
    {
        $unix_epoch = new DateTime("@0");
        $now = new DateTime("@{$seconds}");
        $interval = $unix_epoch->diff($now);
        return $interval->format('%i:%S');
    }
    /**
     * @param int|float $number
     * @param int $decimals
     *
     * @return string
     */
    public static function percent($number, int $decimals = 0) : string
    {
        if (\class_exists('\\NumberFormatter')) {
            $formatter = new \NumberFormatter(\get_locale(), \NumberFormatter::PERCENT);
            $formatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $decimals);
            return $formatter->format($number / 100);
        } else {
            return \number_format_i18n($number, $decimals) . '%';
        }
    }
    /**
     * @param int|float $number
     * @param int $decimals
     *
     * @return string
     */
    public static function decimal($number, int $decimals = 0) : string
    {
        return \number_format_i18n($number, $decimals);
    }
}
