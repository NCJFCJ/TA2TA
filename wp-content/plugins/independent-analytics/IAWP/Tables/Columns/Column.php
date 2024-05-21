<?php

namespace IAWP\Tables\Columns;

use IAWP\Tables\Groups\Group;
/** @internal */
class Column
{
    private $id;
    private $label;
    private $visible;
    private $type;
    private $requires_woocommerce;
    private $exportable;
    private $options;
    private $filter_placeholder;
    private $unavailable_for;
    private $database_column;
    private $is_nullable;
    public function __construct($options)
    {
        $this->id = $options['id'];
        $this->label = $options['label'];
        $this->visible = $options['visible'];
        $this->type = $options['type'];
        $this->requires_woocommerce = $options['requires_woocommerce'] ?? \false;
        $this->exportable = $options['exportable'] ?? \true;
        $this->options = $options['options'] ?? [];
        $this->filter_placeholder = $options['filter_placeholder'] ?? '';
        $this->unavailable_for = $options['unavailable_for'] ?? [];
        $this->database_column = $options['database_column'] ?? null;
        $this->is_nullable = $options['is_nullable'] ?? \false;
    }
    public function is_enabled_for_group(Group $group) : bool
    {
        return !\in_array($group->id(), $this->unavailable_for);
    }
    public function is_group_dependent() : bool
    {
        return \count($this->unavailable_for) > 0;
    }
    public function id() : string
    {
        return $this->id;
    }
    public function database_column() : string
    {
        return !\is_null($this->database_column) ? $this->database_column : $this->id;
    }
    public function label() : string
    {
        return $this->label;
    }
    public function visible() : bool
    {
        return $this->visible;
    }
    public function type() : string
    {
        return $this->type;
    }
    /**
     * @return string[]
     */
    public function filter_operators() : array
    {
        switch ($this->type) {
            case 'string':
                return ['contains', 'exact'];
            case 'date':
                return ['before', 'after', 'on'];
            case 'select':
                return ['is', 'isnt'];
            default:
                // int
                return ['greater', 'lesser', 'equal'];
        }
    }
    public function is_valid_filter_operator(string $operator) : bool
    {
        return \in_array($operator, $this->filter_operators());
    }
    public function sort_direction() : string
    {
        $descending_types = ['int', 'date'];
        return \in_array($this->type, $descending_types) ? 'desc' : 'asc';
    }
    public function set_visibility(bool $visible) : void
    {
        $this->visible = $visible;
    }
    public function requires_woocommerce() : bool
    {
        return $this->requires_woocommerce;
    }
    public function exportable() : bool
    {
        return $this->exportable;
    }
    /**
     * @return array List of possible options for this filter such as a list of authors or list of post categories
     */
    public function options() : array
    {
        return $this->options;
    }
    public function filter_placeholder() : string
    {
        return $this->filter_placeholder;
    }
    public function is_nullable() : bool
    {
        return $this->is_nullable;
    }
}
