<?php

namespace IAWP\Rows;

use IAWP\Date_Range\Date_Range;
use IAWP\Sort_Configuration;
use IAWPSCOPED\Illuminate\Database\Query\Builder;
/** @internal */
abstract class Rows
{
    protected $date_range;
    protected $number_of_rows;
    /** @var Filter[] */
    protected $filters;
    protected $sort_configuration;
    private $rows = null;
    public function __construct(Date_Range $date_range, ?int $number_of_rows = null, ?array $filters = null, ?Sort_Configuration $sort_configuration = null)
    {
        $this->date_range = $date_range;
        $this->number_of_rows = $number_of_rows;
        $this->filters = $filters ?? [];
        $this->sort_configuration = $sort_configuration ?? new Sort_Configuration();
    }
    protected abstract function fetch_rows() : array;
    public abstract function attach_filters(Builder $query) : void;
    public function rows()
    {
        if (\is_array($this->rows)) {
            return $this->rows;
        }
        $this->rows = $this->fetch_rows();
        return $this->rows;
    }
}
