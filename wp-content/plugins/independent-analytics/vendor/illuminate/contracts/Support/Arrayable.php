<?php

namespace IAWPSCOPED\Illuminate\Contracts\Support;

/** @internal */
interface Arrayable
{
    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray();
}
