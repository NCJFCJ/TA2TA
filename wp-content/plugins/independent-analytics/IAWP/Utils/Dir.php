<?php

namespace IAWP\Utils;

/** @internal */
class Dir
{
    public static function delete($directory)
    {
        if (\is_dir($directory)) {
            $objects = \scandir($directory);
            foreach ($objects as $object) {
                if ($object !== "." && $object !== "..") {
                    if (\filetype($directory . "/" . $object) === "dir") {
                        self::delete($directory . "/" . $object);
                    } else {
                        \unlink($directory . "/" . $object);
                    }
                }
            }
            \rmdir($directory);
        }
    }
}
