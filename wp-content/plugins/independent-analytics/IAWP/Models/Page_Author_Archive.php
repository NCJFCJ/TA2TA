<?php

namespace IAWP\Models;

/** @internal */
class Page_Author_Archive extends \IAWP\Models\Page
{
    private $author_id;
    public function __construct($row)
    {
        $this->author_id = $row->author_id;
        parent::__construct($row);
    }
    protected function resource_key() : string
    {
        return 'author_id';
    }
    protected function resource_value() : string
    {
        return $this->author_id;
    }
    protected function calculate_is_deleted() : bool
    {
        return \get_userdata($this->author_id) == \false;
    }
    protected function calculate_url()
    {
        return \get_author_posts_url($this->author_id);
    }
    protected function calculate_title()
    {
        return \get_the_author_meta('display_name', $this->author_id) . ' ' . \esc_html__('Archive', 'independent-analytics');
    }
    protected function calculate_type()
    {
        return 'author-archive';
    }
    protected function calculate_type_label()
    {
        return \esc_html__('Author Archive', 'independent-analytics');
    }
    protected function calculate_icon()
    {
        return '<span class="dashicons dashicons-admin-users"></span>';
    }
    protected function calculate_author_id()
    {
        return $this->author_id;
    }
    protected function calculate_author()
    {
        return \get_the_author_meta('display_name', $this->author_id);
    }
    protected function calculate_avatar()
    {
        return \get_avatar($this->author_id, 20);
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
