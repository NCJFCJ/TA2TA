<?php

namespace IAWP\Models;

/** @internal */
class Page_Search extends \IAWP\Models\Page
{
    private $search_query;
    public function __construct($row)
    {
        $this->search_query = $row->search_query;
        parent::__construct($row);
    }
    public function calculate_url()
    {
        return \get_search_link($this->search_query);
    }
    protected function resource_key() : string
    {
        return 'search_query';
    }
    protected function resource_value() : string
    {
        return $this->search_query;
    }
    protected function calculate_is_deleted() : bool
    {
        return \false;
    }
    protected function calculate_title()
    {
        return \esc_html__('Search:', 'independent-analytics') . ' "' . $this->search_query . '"';
    }
    protected function calculate_type()
    {
        return 'search-archive';
    }
    protected function calculate_type_label()
    {
        return \esc_html__('Search', 'independent-analytics');
    }
    protected function calculate_icon()
    {
        return '<span class="dashicons dashicons-search"></span>';
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
