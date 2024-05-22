<?php

namespace IAWP\Models;

/** @internal */
class Page_Post_Type_Archive extends \IAWP\Models\Page
{
    private $post_type;
    public function __construct($row)
    {
        $this->post_type = $row->post_type;
        parent::__construct($row);
    }
    protected function resource_key() : string
    {
        return 'post_type';
    }
    protected function resource_value() : string
    {
        return $this->post_type;
    }
    protected function calculate_is_deleted() : bool
    {
        return \is_null(\get_post_type_object($this->post_type));
    }
    protected function calculate_url()
    {
        return \get_post_type_archive_link($this->post_type);
    }
    protected function calculate_title()
    {
        if ($this->post_type === 'product') {
            return \esc_html__('Shop', 'independent-analytics');
        }
        $post_type_object = \get_post_type_object($this->post_type);
        if (\is_null($post_type_object)) {
            return null;
        }
        return \get_post_type_object($this->post_type)->labels->singular_name . ' ' . \esc_html__('Archive', 'independent-analytics');
    }
    protected function calculate_type()
    {
        return $this->post_type . '-archive';
    }
    protected function calculate_type_label()
    {
        return $this->title();
    }
    protected function calculate_icon()
    {
        $icon = null;
        if (!$this->calculate_is_deleted()) {
            $icon = \get_post_type_object($this->post_type)->menu_icon;
        }
        $has_icon = !\is_null($icon);
        $html = '';
        if ($has_icon) {
            if (\esc_url_raw($icon) === $icon) {
                $html .= '<span><img src="' . \esc_url($icon) . '" width="20px" height="20px" /></span>';
            } else {
                $html .= '<span class="dashicons ' . \esc_attr($icon) . '"></span>';
            }
        } else {
            $html .= '<span class="dashicons dashicons-archive"></span>';
        }
        return $html;
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
