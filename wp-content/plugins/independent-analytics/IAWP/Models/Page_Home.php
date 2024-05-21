<?php

namespace IAWP\Models;

/** @internal */
class Page_Home extends \IAWP\Models\Page
{
    public function __construct($row)
    {
        parent::__construct($row);
    }
    protected function resource_key()
    {
        return 'resource';
    }
    protected function resource_value()
    {
        return 'home';
    }
    protected function calculate_is_deleted() : bool
    {
        return \false;
    }
    protected function calculate_url()
    {
        $id = \get_option('page_for_posts');
        if ($id == 0) {
            return \get_home_url();
        } else {
            return \get_permalink($id);
        }
    }
    protected function calculate_title()
    {
        $id = \get_option('page_for_posts');
        if ($id == 0) {
            return \esc_html__('Blog', 'independent-analytics');
        } else {
            return \get_the_title($id);
        }
    }
    protected function calculate_type()
    {
        return 'blog-archive';
    }
    protected function calculate_type_label()
    {
        return \esc_html__('Blog', 'independent-analytics');
    }
    protected function calculate_icon()
    {
        if (\get_option('page_on_front') == 0) {
            return '<span class="dashicons dashicons-admin-home"></span>';
        } else {
            return '<span class="dashicons dashicons-edit-large"></span>';
        }
    }
    protected function calculate_author_id()
    {
        return null;
    }
    protected function calculate_author()
    {
        return null;
    }
    protected function calculate_avatar()
    {
        return null;
    }
    protected function calculate_date()
    {
        return null;
    }
    protected function calculate_category()
    {
        return [];
    }
}
