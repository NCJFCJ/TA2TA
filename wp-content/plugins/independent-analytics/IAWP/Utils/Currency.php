<?php

namespace IAWP\Utils;

/** @internal */
class Currency
{
    /**
     * Format currency for sites using WooCommerce
     *
     * @param mixed $amount
     * @param bool $round_to_whole_dollars
     *
     * @return string
     */
    public static function format($amount, bool $round_to_whole_dollars = \true, bool $strip_tags = \true) : string
    {
        if (!\function_exists('wc_price')) {
            return $amount;
        }
        if ($round_to_whole_dollars) {
            $options = ['decimals' => 0];
        } else {
            $options = null;
        }
        return $strip_tags ? \strip_tags(\wc_price($amount, $options)) : \wc_price($amount, $options);
    }
}
