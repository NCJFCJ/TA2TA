<?php

namespace IAWP\Filter_Lists;

/** @internal */
class Referrer_Type_Filter_List
{
    use \IAWP\Filter_Lists\Filter_List_Trait;
    protected static function fetch_options() : array
    {
        return [['Search', \esc_html__('Search', 'independent-analytics')], ['Social', \esc_html__('Social', 'independent-analytics')], ['Referrer', \esc_html__('Referrer', 'independent-analytics')], ['Ad', \esc_html__('Ad', 'independent-analytics')], ['Direct', \esc_html__('Direct', 'independent-analytics')]];
    }
}
