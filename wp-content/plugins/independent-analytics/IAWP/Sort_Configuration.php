<?php

namespace IAWP;

/** @internal */
class Sort_Configuration
{
    public const ASCENDING = 'asc';
    public const DESCENDING = 'desc';
    public const VALID_DIRECTIONS = [self::ASCENDING, self::DESCENDING];
    public const DEFAULT_DIRECTION = self::DESCENDING;
    private $column = 'visitors';
    private $direction = self::DEFAULT_DIRECTION;
    private $is_nullable;
    /**
     * @param string|null $column
     * @param string|null $direction
     * @param bool $is_nullable
     */
    public function __construct(?string $column = null, ?string $direction = null, bool $is_nullable = \false)
    {
        if (\is_string($column)) {
            $this->column = $column;
        }
        if (\in_array($direction, self::VALID_DIRECTIONS)) {
            $this->direction = $direction;
        }
        $this->is_nullable = $is_nullable;
    }
    public function column() : string
    {
        return $this->column;
    }
    public function direction() : string
    {
        return $this->direction;
    }
    public function is_nullable() : bool
    {
        return $this->direction === self::ASCENDING && $this->is_nullable;
    }
}
