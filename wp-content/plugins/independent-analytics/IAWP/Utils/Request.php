<?php

namespace IAWP\Utils;

/** @internal */
class Request
{
    public static function path_relative_to_site_url($url = null)
    {
        if (\is_null($url)) {
            $url = self::url();
        }
        $site_url = \site_url();
        if ($url == $site_url) {
            return '/';
        } elseif (\substr($url, 0, \strlen($site_url)) == $site_url) {
            return \substr($url, \strlen($site_url));
        } else {
            return $url;
        }
    }
    public static function ip()
    {
        $headers = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR', 'HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_INCAP_CLIENT_IP'];
        foreach ($headers as $header) {
            if (isset($_SERVER[$header])) {
                return \explode(',', $_SERVER[$header])[0];
            }
        }
        return null;
    }
    public static function user_agent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }
    private static function scheme()
    {
        if (!empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https' || !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' || !empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') {
            return 'https';
        } else {
            return 'http';
        }
    }
    private static function url()
    {
        if (!empty($_SERVER['HTTP_HOST']) && !empty($_SERVER['REQUEST_URI'])) {
            return \esc_url_raw(self::scheme() . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        } else {
            return null;
        }
    }
}
