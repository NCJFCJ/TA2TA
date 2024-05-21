<?php

namespace IAWP\Utils;

/** @internal */
class Salt
{
    /*
     * Used for salting visitor hashes.
     */
    public static function visitor_token_salt() : string
    {
        return self::get_salt_option('iawp_salt');
    }
    public static function refresh_visitor_token_salt() : string
    {
        \delete_option('iawp_salt');
        return self::get_salt_option('iawp_salt');
    }
    /*
     * Primarily used for salting request payloads.
     */
    public static function request_payload_salt() : string
    {
        return self::get_salt_option('iawp_request_payload_salt');
    }
    private static function get_salt_option($name) : string
    {
        $salt = \get_option($name);
        if (!$salt) {
            $salt = self::generate_salt();
            \update_option($name, $salt);
        }
        return $salt;
    }
    private static function generate_salt() : string
    {
        $length = 32;
        $bytes = \random_bytes($length);
        return \substr(\strtr(\base64_encode($bytes), '+', '.'), 0, 44);
    }
}
