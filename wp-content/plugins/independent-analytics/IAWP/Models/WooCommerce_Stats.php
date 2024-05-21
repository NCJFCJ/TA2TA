<?php

namespace IAWP\Models;

/** @internal */
trait WooCommerce_Stats
{
    protected $wc_orders;
    protected $wc_gross_sales;
    protected $wc_refunds;
    protected $wc_refunded_amount;
    protected $wc_net_sales;
    protected $wc_conversion_rate;
    protected $wc_earnings_per_visitor;
    protected $wc_average_order_volume;
    public final function wc_orders() : int
    {
        return $this->wc_orders;
    }
    public final function wc_gross_sales()
    {
        return $this->wc_gross_sales;
    }
    public final function wc_refunds()
    {
        return $this->wc_refunds;
    }
    public function wc_refunded_amount()
    {
        return $this->wc_refunded_amount;
    }
    public function wc_net_sales()
    {
        return $this->wc_net_sales;
    }
    public function wc_conversion_rate()
    {
        return $this->wc_conversion_rate;
    }
    public function wc_earnings_per_visitor()
    {
        return $this->wc_earnings_per_visitor;
    }
    public function wc_average_order_volume()
    {
        return $this->wc_average_order_volume;
    }
    protected final function set_wc_stats($row)
    {
        $this->wc_orders = isset($row->wc_orders) ? \floatval($row->wc_orders) : 0;
        $this->wc_gross_sales = isset($row->wc_gross_sales) ? \floatval($row->wc_gross_sales) : 0;
        $this->wc_refunds = isset($row->wc_refunds) ? \floatval($row->wc_refunds) : 0;
        $this->wc_refunded_amount = isset($row->wc_refunded_amount) ? \floatval($row->wc_refunded_amount) : 0;
        $this->wc_net_sales = isset($row->wc_net_sales) ? \floatval($row->wc_net_sales) : 0;
        $this->wc_conversion_rate = isset($row->wc_conversion_rate) ? \floatval($row->wc_conversion_rate) : 0;
        $this->wc_earnings_per_visitor = isset($row->wc_earnings_per_visitor) ? \floatval($row->wc_earnings_per_visitor) : 0;
        $this->wc_average_order_volume = isset($row->wc_average_order_volume) ? \floatval($row->wc_average_order_volume) : 0;
    }
}
