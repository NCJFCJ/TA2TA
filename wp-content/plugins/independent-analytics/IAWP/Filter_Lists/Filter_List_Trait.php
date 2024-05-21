<?php

namespace IAWP\Filter_Lists;

/** @internal */
trait Filter_List_Trait
{
    private static $options = null;
    protected static abstract function fetch_options() : array;
    public static function options() : array
    {
        if (\is_null(self::$options)) {
            self::$options = self::fetch_options();
        }
        return self::$options;
    }
    public static function option($option_id) : ?string
    {
        $matches = \array_filter(self::$options, function (array $option) use($option_id) {
            return $option[0] == $option_id;
        });
        $match = \reset($matches);
        return \is_array($match) ? $match[1] : null;
    }
}
