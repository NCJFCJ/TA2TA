<?php

namespace IAWP\Models;

/** @internal */
class Page_Not_Found extends \IAWP\Models\Page
{
    private $not_found_url;
    public function __construct($row)
    {
        $this->not_found_url = $row->not_found_url;
        parent::__construct($row);
    }
    public function most_popular_subtitle() : string
    {
        return $this->url();
    }
    protected function resource_key() : string
    {
        return 'not_found_url';
    }
    protected function resource_value() : string
    {
        return $this->not_found_url;
    }
    protected function calculate_is_deleted() : bool
    {
        return \false;
    }
    protected function calculate_url()
    {
        return \site_url($this->not_found_url);
    }
    protected function calculate_title()
    {
        return '404';
    }
    protected function calculate_type()
    {
        return 'not-found';
    }
    protected function calculate_type_label()
    {
        return '404';
    }
    protected function calculate_icon()
    {
        return '<span class="dashicons dashicons-warning"></span>';
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
