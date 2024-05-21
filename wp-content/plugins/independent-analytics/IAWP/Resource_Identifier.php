<?php

namespace IAWP;

use IAWP\Utils\Request;
/** @internal */
class Resource_Identifier
{
    private $type;
    private $meta_key;
    private $meta_value;
    /**
     * @param string $type
     * @param string|null $meta_key
     * @param int|string|null $meta_value
     */
    private function __construct(string $type, ?string $meta_key = null, $meta_value = null)
    {
        $this->type = $type;
        $this->meta_key = $meta_key;
        $this->meta_value = $meta_value;
    }
    /**
     * @return string
     */
    public function type() : string
    {
        return $this->type;
    }
    /**
     * @return string|null
     */
    public function meta_key() : ?string
    {
        return $this->meta_key;
    }
    /**
     * @return int|string|null
     */
    public function meta_value()
    {
        return $this->meta_value;
    }
    /**
     * @return bool
     */
    public function has_meta() : bool
    {
        return !\is_null($this->meta_key) && !\is_null($this->meta_value);
    }
    /**
     * @return self|null
     */
    public static function for_resource_being_viewed() : ?self
    {
        if (\is_string(self::get_virtual_page_id())) {
            $type = 'virtual_page';
            $meta_key = 'virtual_page_id';
            $meta_value = self::get_virtual_page_id();
        } elseif (\is_singular()) {
            $type = 'singular';
            $meta_key = 'singular_id';
            $meta_value = \get_queried_object_id();
        } elseif (\is_author()) {
            $type = 'author_archive';
            $meta_key = 'author_id';
            $meta_value = \get_queried_object_id();
        } elseif (\is_date()) {
            $type = 'date_archive';
            $meta_key = 'date_archive';
            $meta_value = self::get_date_archive_date();
        } elseif (\is_post_type_archive()) {
            $type = 'post_type_archive';
            $meta_key = 'post_type';
            $meta_value = \get_queried_object()->name;
        } elseif (\is_category() || \is_tag() || \is_tax()) {
            $type = 'term_archive';
            $meta_key = 'term_id';
            $meta_value = \get_queried_object_id();
        } elseif (\is_search()) {
            $type = 'search';
            $meta_key = 'search_query';
            $meta_value = \get_search_query();
        } elseif (\is_home()) {
            $type = 'home';
            $meta_key = null;
            $meta_value = null;
        } elseif (\is_404()) {
            $path = Request::path_relative_to_site_url();
            if (\is_null($path)) {
                return null;
            }
            $type = '404';
            $meta_key = 'not_found_url';
            $meta_value = $path;
        } else {
            return null;
        }
        return new self($type, $meta_key, $meta_value);
    }
    /**
     * @return self|null
     */
    public static function for_resource_being_edited() : ?self
    {
        if (!\is_admin()) {
            return null;
        }
        $screen = \get_current_screen();
        // Check if the current screen is post editing page
        if (!\is_null($screen) && $screen->base === 'post') {
            $singular_id = \get_the_ID();
            if (\is_int($singular_id)) {
                $singular_id = \strval($singular_id);
            } else {
                return null;
            }
            return new self('singular', 'singular_id', $singular_id);
        }
        return null;
    }
    public static function for_post_id(int $post_id) : ?self
    {
        return new self('singular', 'singular_id', $post_id);
    }
    private static function get_virtual_page_id() : ?string
    {
        if (\IAWPSCOPED\iawp_using_woocommerce() && is_checkout() && is_wc_endpoint_url('order-received')) {
            return 'wc_checkout_success';
        }
        return null;
    }
    /**
     * Build a date archive string with the year and possibly a month and date
     *
     * Examples would be "2023", "2023-01", or "2023-01-21"
     *
     * @return mixed|string
     */
    private static function get_date_archive_date()
    {
        $str = \get_query_var('year');
        if (\is_month() || \is_day()) {
            $month = \get_query_var('monthnum');
            $str = $str . '-' . \str_pad($month, 2, '0', \STR_PAD_LEFT);
        }
        if (\is_day()) {
            $day = \get_query_var('day');
            $str = $str . '-' . \str_pad($day, 2, '0', \STR_PAD_LEFT);
        }
        return $str;
    }
}
