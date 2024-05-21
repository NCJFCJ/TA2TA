<?php

namespace IAWP\Statistics\Intervals;

/** @internal */
class Intervals
{
    /**
     * @return Interval[]
     */
    public static function all() : array
    {
        return [new \IAWP\Statistics\Intervals\Hourly(), new \IAWP\Statistics\Intervals\Daily(), new \IAWP\Statistics\Intervals\Weekly(), new \IAWP\Statistics\Intervals\Monthly()];
    }
    /**
     * Find an interval by its id. Will return the default interval if provided id is invalid.
     *
     * @param string|null $interval_id
     *
     * @return Interval
     */
    public static function find_by_id(?string $interval_id) : \IAWP\Statistics\Intervals\Interval
    {
        foreach (self::all() as $interval) {
            if ($interval->id() === $interval_id) {
                return $interval;
            }
        }
        return new \IAWP\Statistics\Intervals\Daily();
    }
    public static function default_for(int $days) : \IAWP\Statistics\Intervals\Interval
    {
        if ($days <= 3) {
            return new \IAWP\Statistics\Intervals\Hourly();
        } elseif ($days <= 84) {
            return new \IAWP\Statistics\Intervals\Daily();
        } elseif ($days <= 182) {
            return new \IAWP\Statistics\Intervals\Weekly();
        } else {
            return new \IAWP\Statistics\Intervals\Monthly();
        }
    }
    // public static function create_interval(?string $interval_id): Interval
    // {
    //     // Switch case...
    // }
}
