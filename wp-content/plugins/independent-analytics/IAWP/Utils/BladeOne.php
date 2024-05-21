<?php

namespace IAWP\Utils;

/** @internal */
class BladeOne
{
    public static function create()
    {
        return new \IAWPSCOPED\eftec\bladeone\BladeOne(\IAWPSCOPED\iawp_path_to('views'), \IAWPSCOPED\iawp_temp_path_to('template-cache'));
    }
}
