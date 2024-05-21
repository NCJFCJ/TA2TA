<?php

namespace IAWP\Models;

/** @internal */
class Page_Virtual extends \IAWP\Models\Page
{
    private $virtual_page_id;
    public function __construct($row)
    {
        $this->virtual_page_id = $row->virtual_page_id;
        parent::__construct($row);
    }
    protected function resource_key() : string
    {
        return 'virtual_page_id';
    }
    protected function resource_value() : string
    {
        return $this->virtual_page_id;
    }
    protected function calculate_is_deleted() : bool
    {
        return \false;
    }
    protected function calculate_url()
    {
        return null;
    }
    protected function calculate_title()
    {
        switch ($this->virtual_page_id) {
            case "wc_checkout_success":
                return \__('Checkout Success', 'independent-analytics');
        }
    }
    protected function calculate_type()
    {
        return 'page';
    }
    protected function calculate_type_label()
    {
        return 'Page';
    }
    protected function calculate_icon()
    {
        return '<span class="dashicons dashicons-admin-page"></span>';
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
