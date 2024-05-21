<?php

namespace IAWP\Models;

use IAWP\Utils\String_Util;
/** @internal */
class Page_Singular extends \IAWP\Models\Page
{
    private $singular_id;
    private $comments;
    public function __construct($row)
    {
        $this->singular_id = $row->singular_id;
        $this->comments = $row->comments ?? 0;
        parent::__construct($row);
    }
    /**
     * Override comments method to add comments support for singular pages.
     *
     * @return int|null
     */
    public function comments() : ?int
    {
        return $this->comments;
    }
    protected function resource_key() : string
    {
        return 'singular_id';
    }
    protected function resource_value() : string
    {
        return $this->singular_id;
    }
    protected function calculate_is_deleted() : bool
    {
        $post = \get_post($this->singular_id);
        return \is_null($post) || \is_null(\get_post_type_object($post->post_type));
    }
    protected function calculate_url()
    {
        return $this->convert_wpml_url(\get_permalink($this->singular_id));
    }
    protected function calculate_title()
    {
        return \get_the_title($this->singular_id);
    }
    protected function calculate_type()
    {
        $post_type_object = \get_post_type_object(\get_post_type($this->singular_id));
        if (\is_null($post_type_object)) {
            return null;
        }
        return $post_type_object->name;
    }
    protected function calculate_type_label()
    {
        $post_type_object = \get_post_type_object(\get_post_type($this->singular_id));
        if (\is_null($post_type_object)) {
            return null;
        }
        return $post_type_object->labels->singular_name;
    }
    protected function calculate_icon()
    {
        $icon = null;
        if (!$this->calculate_is_deleted()) {
            $icon = \get_post_type_object($this->type(\true))->menu_icon;
        }
        $has_icon = !\is_null($icon);
        $html = '<div class="post-type-icon">';
        if ($has_icon) {
            if (\esc_url_raw($icon) == $icon) {
                if (String_Util::str_contains($icon, 'svg')) {
                    $html .= '<span class="custom-icon" style="display: block;-webkit-mask: url(' . \esc_url($icon) . ') no-repeat center;mask: url(' . \esc_url($icon) . ') no-repeat center;"></span>';
                } else {
                    $html .= '<span><img src="' . \esc_url($icon) . '" width="20px" height="20px" /></span>';
                }
            } else {
                $html .= '<span class="dashicons ' . \esc_attr($icon) . '"></span>';
            }
        } else {
            $html .= '<span class="dashicons dashicons-admin-post"></span>';
        }
        $html .= '</div>';
        return $html;
    }
    protected function calculate_author_id()
    {
        return \get_post_field('post_author', $this->singular_id);
    }
    protected function calculate_author()
    {
        return \get_the_author_meta('display_name', $this->author_id());
    }
    protected function calculate_avatar()
    {
        return \get_avatar($this->author_id(), 20);
    }
    protected function calculate_date()
    {
        $date = \get_the_date('Y-m-d', $this->singular_id);
        if (!\is_string($date)) {
            return null;
        }
        return $date;
    }
    protected function calculate_category()
    {
        $post_type_still_registered = \in_array($this->calculate_type(), \get_post_types());
        $categories = [];
        if (!$post_type_still_registered) {
            return [];
        }
        foreach (\get_the_category($this->singular_id) as $category) {
            $categories[] = $category->term_id;
        }
        return $categories;
    }
    protected function convert_wpml_url($permalink)
    {
        $active_plugins = \get_option('active_plugins');
        if (\in_array('sitepress-multilingual-cms/sitepress.php', $active_plugins)) {
            $language = \apply_filters('wpml_post_language_details', null, $this->singular_id);
            $permalink = \apply_filters('wpml_permalink', $permalink, $language['language_code'], \true);
        }
        return $permalink;
    }
}
