<?php

namespace IAWP;

/** @internal */
class WP_Option_Cache_Bust
{
    private $option_name;
    /**
     * @param string $option_name
     */
    private function __construct(string $option_name)
    {
        $this->option_name = $option_name;
    }
    /**
     * @param string|null $prefix
     * @return string
     */
    private function option_name(?string $prefix = null) : string
    {
        if (\is_string($prefix)) {
            return $prefix . $this->option_name;
        }
        return $this->option_name;
    }
    /**
     * @param $default_value
     * @return mixed|null
     */
    private function value($default_value)
    {
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT option_value FROM {$wpdb->options} WHERE option_name = %s LIMIT 1", $this->option_name()));
        if (\is_object($row)) {
            $value = $row->option_value;
        } else {
            $value = $default_value;
        }
        return \apply_filters($this->option_name('option_'), \maybe_unserialize($value), $this->option_name());
    }
    /**
     * @param string $option_name
     * @return void
     */
    public static function register(string $option_name)
    {
        $cache_bust = new self($option_name);
        \add_filter($cache_bust->option_name('pre_option_'), function ($default_value) use($cache_bust) {
            return $cache_bust->value($default_value);
        });
    }
}
