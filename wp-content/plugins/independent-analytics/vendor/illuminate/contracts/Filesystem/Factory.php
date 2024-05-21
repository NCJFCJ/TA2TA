<?php

namespace IAWPSCOPED\Illuminate\Contracts\Filesystem;

/** @internal */
interface Factory
{
    /**
     * Get a filesystem implementation.
     *
     * @param  string|null  $name
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function disk($name = null);
}
