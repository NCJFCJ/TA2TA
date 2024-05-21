<?php

namespace IAWPSCOPED\Illuminate\Contracts\Support;

/** @internal */
interface Renderable
{
    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render();
}
