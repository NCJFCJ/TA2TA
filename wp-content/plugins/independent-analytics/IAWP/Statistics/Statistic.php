<?php

namespace IAWP\Statistics;

/** @internal */
class Statistic
{
    private $value;
    private $previous_value;
    private $daily_summary;
    public function __construct($value = 0, $previous_value = 0, $daily_summary = [])
    {
        $this->value = $value;
        $this->previous_value = $previous_value;
        $this->daily_summary = $daily_summary;
    }
    public function value()
    {
        return $this->value;
    }
    public function growth()
    {
        if ($this->value == 0 && $this->previous_value != 0) {
            return -100;
        } elseif ($this->value == 0 || $this->previous_value == 0) {
            return 0;
        }
        $percent_growth = ($this->value / $this->previous_value - 1) * 100;
        return \round($percent_growth, 0);
    }
    public function daily_summary()
    {
        return $this->daily_summary;
    }
}
