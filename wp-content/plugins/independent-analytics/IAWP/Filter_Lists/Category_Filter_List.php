<?php

namespace IAWP\Filter_Lists;

/** @internal */
class Category_Filter_List
{
    use \IAWP\Filter_Lists\Filter_List_Trait;
    protected static function fetch_options() : array
    {
        return \array_map(function ($category) {
            return [$category->term_id, $category->name];
        }, \get_categories());
    }
}
