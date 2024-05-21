<?php

namespace IAWPSCOPED\Doctrine\Common\Cache;

/**
 * Interface for cache that can be flushed.
 *
 * @link   www.doctrine-project.org
 * @internal
 */
interface FlushableCache
{
    /**
     * Flushes all cache entries, globally.
     *
     * @return bool TRUE if the cache entries were successfully flushed, FALSE otherwise.
     */
    public function flushAll();
}
