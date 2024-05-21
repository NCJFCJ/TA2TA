<?php

namespace IAWP\Tables\Groups;

/** @internal */
class Group
{
    private $id;
    private $singular;
    private $rows_class;
    private $statistics_class;
    public function __construct(string $id, string $singular, string $rows_class, string $statistics_class)
    {
        $this->id = $id;
        $this->singular = $singular;
        $this->rows_class = $rows_class;
        $this->statistics_class = $statistics_class;
    }
    public function id() : string
    {
        return $this->id;
    }
    public function singular() : string
    {
        return $this->singular;
    }
    public function rows_class() : string
    {
        return $this->rows_class;
    }
    public function statistics_class() : string
    {
        return $this->statistics_class;
    }
}
