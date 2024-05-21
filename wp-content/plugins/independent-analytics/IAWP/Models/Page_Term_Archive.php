<?php

namespace IAWP\Models;

/** @internal */
class Page_Term_Archive extends \IAWP\Models\Page
{
    private $term_id;
    public function __construct($row)
    {
        $this->term_id = \intval($row->term_id);
        parent::__construct($row);
    }
    protected function resource_key() : string
    {
        return 'term_id';
    }
    protected function resource_value() : string
    {
        return $this->term_id;
    }
    protected function calculate_is_deleted() : bool
    {
        try {
            $term = \get_term($this->term_id);
            return \is_wp_error($term) || \is_null($term);
        } catch (\Throwable $e) {
            return \true;
        }
    }
    protected function calculate_url()
    {
        return \get_term_link($this->term_id);
    }
    protected function calculate_title()
    {
        return $this->term()->name;
    }
    protected function calculate_type()
    {
        return $this->term()->taxonomy;
    }
    protected function calculate_type_label()
    {
        return \get_taxonomy_labels(\get_taxonomy($this->term()->taxonomy))->singular_name;
    }
    protected function calculate_icon()
    {
        $icon = 'dashicons-category';
        if (!$this->calculate_is_deleted()) {
            if ($this->type() == 'Tag') {
                $icon = 'dashicons-tag';
            }
        }
        $html = '<div class="post-type-icon">';
        $html .= '<span class="dashicons ' . $icon . '"></span>';
        $html .= '</div>';
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
    private function term()
    {
        return \get_term($this->term_id);
    }
}
