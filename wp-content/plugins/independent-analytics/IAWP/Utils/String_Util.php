<?php

namespace IAWP\Utils;

/** @internal */
class String_Util
{
    /** These functions are copied verbatim from the WordPress polyfills added in 5.9.
     *  They allow us to use these PHP 8 functions with PHP 7 and WP 5.5 */
    public static function str_contains($haystack, $needle)
    {
        return '' === $needle || \false !== \strpos($haystack, $needle);
    }
    public static function str_starts_with($haystack, $needle)
    {
        if ('' === $needle) {
            return \true;
        }
        return 0 === \strpos($haystack, $needle);
    }
    public static function str_ends_with($haystack, $needle)
    {
        if ('' === $haystack && '' !== $needle) {
            return \false;
        }
        $len = \strlen($needle);
        return 0 === \substr_compare($haystack, $needle, -$len, $len);
    }
}
