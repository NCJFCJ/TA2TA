<?php

namespace IAWP\Tables\Groups;

/** @internal */
class Groups
{
    private $groups;
    /**
     * @param Group[] $groups
     */
    public function __construct(array $groups = [])
    {
        $this->groups = $groups;
    }
    public function add(\IAWP\Tables\Groups\Group $group) : void
    {
        $this->groups[] = $group;
    }
    public function find_by_id(?string $id = null) : \IAWP\Tables\Groups\Group
    {
        if (\is_null($id)) {
            return $this->default_group();
        }
        $array = \array_filter($this->groups(), function ($group) use($id) {
            return $group->id() === $id;
        });
        $match = \reset($array);
        return $match === \false ? $this->default_group() : $match;
    }
    /**
     * Return an array of groups representing buttons one can select
     *
     * @return Group[]
     */
    public function buttons() : array
    {
        if (\count($this->groups()) === 1) {
            return [];
        } else {
            return $this->groups();
        }
    }
    /**
     * @return Group[]
     */
    public function groups() : array
    {
        return $this->groups;
    }
    private function default_group() : \IAWP\Tables\Groups\Group
    {
        return $this->groups()[0];
    }
}
