<?php

namespace IAWP\Filter_Lists;

/** @internal */
class Page_Type_Filter_List
{
    use \IAWP\Filter_Lists\Filter_List_Trait;
    protected static function fetch_options() : array
    {
        $options = [];
        $options[] = ['post', \esc_html__('Post', 'independent-analytics')];
        $options[] = ['page', \esc_html__('Page', 'independent-analytics')];
        $options[] = ['attachment', \esc_html__('Attachment', 'independent-analytics')];
        foreach (\get_post_types(['public' => \true, '_builtin' => \false]) as $custom_type) {
            $options[] = [$custom_type, \get_post_type_object($custom_type)->labels->singular_name];
        }
        $options[] = ['category', \esc_html__('Category', 'independent-analytics')];
        $options[] = ['post_tag', \esc_html__('Tag', 'independent-analytics')];
        foreach (\get_taxonomies(['public' => \true, '_builtin' => \false]) as $taxonomy) {
            $label = \get_taxonomy_labels(\get_taxonomy($taxonomy))->singular_name;
            /**
             * WooCommerce category and tag taxonomies have the same singular name as WordPress
             * category and tag taxonomies, so use the name here instead
             */
            if (\in_array($taxonomy, ['product_cat', 'product_tag'])) {
                $label = \get_taxonomy_labels(\get_taxonomy($taxonomy))->name;
            }
            $options[] = [$taxonomy, \ucwords($label)];
        }
        $options[] = ['blog-archive', \esc_html__('Blog Home', 'independent-analytics')];
        $options[] = ['author-archive', \esc_html__('Author Archive', 'independent-analytics')];
        $options[] = ['date-archive', \esc_html__('Date Archive', 'independent-analytics')];
        $options[] = ['search-archive', \esc_html__('Search Results', 'independent-analytics')];
        $options[] = ['not-found', \esc_html__('404', 'independent-analytics')];
        return $options;
    }
}
