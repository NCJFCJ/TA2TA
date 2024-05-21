<?php

namespace IAWPSCOPED\Illuminate\Contracts\Queue;

/** @internal */
interface ClearableQueue
{
    /**
     * Delete all of the jobs from the queue.
     *
     * @param  string  $queue
     * @return int
     */
    public function clear($queue);
}
