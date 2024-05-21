<?php

namespace IAWPSCOPED\Illuminate\Database\Eloquent;

use IAWPSCOPED\Illuminate\Contracts\Queue\EntityNotFoundException;
use IAWPSCOPED\Illuminate\Contracts\Queue\EntityResolver as EntityResolverContract;
/** @internal */
class QueueEntityResolver implements EntityResolverContract
{
    /**
     * Resolve the entity for the given ID.
     *
     * @param  string  $type
     * @param  mixed  $id
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Queue\EntityNotFoundException
     */
    public function resolve($type, $id)
    {
        $instance = (new $type())->find($id);
        if ($instance) {
            return $instance;
        }
        throw new EntityNotFoundException($type, $id);
    }
}
